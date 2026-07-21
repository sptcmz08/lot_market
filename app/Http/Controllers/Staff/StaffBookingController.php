<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryPhoto;
use App\Models\DeliveryTask;
use App\Services\PhotoUploadService;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffBookingController extends Controller
{
    public function __construct(private PhotoUploadService $photoUploadService)
    {
    }

    public function index(Request $request)
    {
        $query = Booking::with(['lots', 'deliveryTasks.photos']);

        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        } else {
            $query->where('status', '!=', 'completed');
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('shop_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('lots', fn ($lots) => $lots->where('lot_code', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('use_date', $request->date);
        }

        $bookings = $query->orderByDesc('use_date')->orderByDesc('id')->paginate(15)->withQueryString();

        // Calculate summary statistics for filtered results
        $allFilteredBookings = (clone $query)->get();
        
        $tentSummary = [
            'total' => 0,
            'sizes' => []
        ];
        
        $counterSummary = [
            'total' => 0,
            'sizes' => []
        ];

        foreach ($allFilteredBookings as $b) {
            $tentItems = $b->tentEquipmentItems() ?: [];
            foreach ($tentItems as $item) {
                $qty = (int)($item['quantity'] ?? 0);
                $size = $item['size'] ?? '';
                $color = $item['color'] ?? '';
                
                $tentSummary['total'] += $qty;
                if (!isset($tentSummary['sizes'][$size])) {
                    $tentSummary['sizes'][$size] = [
                        'total' => 0,
                        'colors' => []
                    ];
                }
                $tentSummary['sizes'][$size]['total'] += $qty;
                
                if ($color) {
                    if (!isset($tentSummary['sizes'][$size]['colors'][$color])) {
                        $tentSummary['sizes'][$size]['colors'][$color] = 0;
                    }
                    $tentSummary['sizes'][$size]['colors'][$color] += $qty;
                }
            }

            $counterItems = $b->counterEquipmentItems() ?: [];
            foreach ($counterItems as $item) {
                $qty = (int)($item['quantity'] ?? 0);
                $size = $item['size'] ?? '';
                
                $counterSummary['total'] += $qty;
                if (!isset($counterSummary['sizes'][$size])) {
                    $counterSummary['sizes'][$size] = 0;
                }
                $counterSummary['sizes'][$size] += $qty;
            }
        }

        return view('staff.bookings-index', compact('bookings', 'tentSummary', 'counterSummary'));
    }

    public function camera(Booking $booking)
    {
        $booking->load(['lots', 'deliveryTasks.photos']);
        $this->ensurePhotoAccess($booking);

        return view('staff.booking-camera', compact('booking'));
    }

    public function uploadPhotos(Request $request, Booking $booking, DeliveryTask $task = null)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        $validated = $request->validate([
            'photo_type' => 'required|in:lot_number,after',
            'camera_photo' => 'nullable|required_without:photos|image',
            'photos' => 'nullable|required_without:camera_photo|array|min:1',
            'photos.*' => 'required|image',
            'note' => 'nullable|string|max:250',
        ], [
            'camera_photo.required_without' => 'กรุณาถ่ายรูปหรือเลือกรูปอย่างน้อย 1 รูป',
            'photos.required_without' => 'กรุณาถ่ายรูปหรือเลือกรูปอย่างน้อย 1 รูป',
        ]);

        $lotPhotos = $booking->deliveryTasks->flatMap->photos->where('photo_type', 'lot_number');
        if ($validated['photo_type'] === 'after') {
            abort_unless($lotPhotos->contains('ocr_status', 'approved'), 403, 'ต้องรอแอดมินอนุมัติรูป LOT ก่อนแนบรูปงานติดตั้ง');
        }
        if ($validated['photo_type'] === 'lot_number') {
            abort_if($lotPhotos->contains('ocr_status', 'submitted'), 403, 'รูป LOT ถูกส่งแล้วและกำลังรอแอดมินอนุมัติ');
            abort_if($lotPhotos->contains('ocr_status', 'approved'), 403, 'รูป LOT ได้รับการอนุมัติแล้ว');
        }

        $files = collect($request->file('photos', []));
        if ($request->hasFile('camera_photo')) {
            $files->prepend($request->file('camera_photo'));
        }

        // Keep newly uploaded evidence on a task that is still awaiting review,
        // so it cannot appear on the customer page before admin approval.
        if (!$task) {
            $task = $booking->deliveryTasks
                ->where('status', '!=', 'completed')
                ->sortBy('id')
                ->firstOrFail();
        } else {
            abort_unless($task->booking_id === $booking->id, 404);
        }

        foreach ($files as $file) {
            DeliveryPhoto::create([
                'delivery_task_id' => $task->id,
                'photo_type' => $validated['photo_type'],
                'image_path' => $this->photoUploadService->upload($file),
                'taken_at' => now(),
                'uploaded_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'ocr_status' => $validated['photo_type'] === 'lot_number' ? 'draft' : null,
            ]);
        }

        return back()->with('success', 'เพิ่มรูปเรียบร้อยแล้ว '.$files->count().' รูป สามารถเพิ่มรูปต่อหรือกดส่งได้');
    }

    public function submitLot(Booking $booking)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        $lotPhotos = $booking->deliveryTasks->flatMap->photos->where('photo_type', 'lot_number');
        if ($lotPhotos->contains('ocr_status', 'approved')) {
            return back()->with('error', 'รูป LOT ได้รับการอนุมัติแล้ว');
        }
        if ($lotPhotos->contains('ocr_status', 'submitted')) {
            return back()->with('error', 'รูป LOT ถูกส่งแล้ว กรุณารอแอดมินอนุมัติ');
        }
        if ($lotPhotos->isEmpty()) {
            return back()->with('error', 'กรุณาถ่ายหรือแนบรูปเลข LOT อย่างน้อย 1 รูปก่อนส่ง');
        }

        DB::transaction(function () use ($booking) {
            DeliveryPhoto::whereIn('delivery_task_id', $booking->deliveryTasks->pluck('id'))
                ->where('photo_type', 'lot_number')
                ->where('ocr_status', '!=', 'approved')
                ->update(['ocr_status' => 'submitted']);

            foreach ($booking->deliveryTasks as $task) {
                if (str_starts_with((string) $task->problem_note, 'ตีกลับรูป LOT โดยแอดมิน:')) {
                    $task->update(['problem_note' => null]);
                }
            }

            StatusLogService::log(Booking::class, $booking->id, $booking->status, $booking->status, auth()->id(), 'Staff ส่งรูป LOT แล้ว รอแอดมินอนุมัติ');
        });

        return redirect()->route('staff.bookings.index')->with('success', 'ส่งรูป LOT เรียบร้อยแล้ว กรุณารอแอดมินอนุมัติก่อนแนบรูปงานติดตั้ง');
    }

    public function submitWork(Booking $booking, DeliveryTask $task = null)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        $photos = $booking->deliveryTasks->flatMap->photos;
        if (! $photos->where('photo_type', 'lot_number')->contains('ocr_status', 'approved')) {
            return back()->with('error', 'ต้องรอแอดมินอนุมัติรูป LOT ก่อนส่งรูปงานติดตั้ง');
        }

        if ($task) {
            abort_unless($task->booking_id === $booking->id, 404);
            $tasksToSubmit = collect([$task]);
        } else {
            $tasksToSubmit = $booking->deliveryTasks->where('status', '!=', 'completed');
        }

        // Check if there is at least one after photo for the task(s) we are submitting
        foreach ($tasksToSubmit as $t) {
            if (!$t->photos->contains('photo_type', 'after')) {
                return back()->with('error', 'กรุณาถ่ายหรือแนบรูปงานติดตั้งอย่างน้อย 1 รูปก่อนส่งสำหรับ' . $t->typeLabel());
            }
        }

        DB::transaction(function () use ($booking, $tasksToSubmit) {
            foreach ($tasksToSubmit as $task) {
                if ($task->status === 'completed') {
                    continue;
                }

                $oldStatus = $task->status;
                $task->update([
                    'status' => 'photo_uploaded',
                    'started_at' => $task->started_at ?: now(),
                    'problem_note' => null,
                ]);
                StatusLogService::log(DeliveryTask::class, $task->id, $oldStatus, 'photo_uploaded', auth()->id(), 'Staff ส่งรูปงานติดตั้งให้แอดมินตรวจสอบ');
            }

            $oldBookingStatus = $booking->status;
            $newBookingStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, $newBookingStatus, auth()->id(), 'ส่งรูปงานแล้ว รอแอดมินอนุมัติ');
        });

        return redirect()->route('staff.bookings.index')->with('success', 'ส่งรูปงานติดตั้งเรียบร้อยแล้ว กรุณารอแอดมินอนุมัติ');
    }

    private function ensurePhotoAccess(Booking $booking): void
    {
        abort_if(in_array($booking->status, ['pending_admin', 'cancelled'], true), 403, 'รายการนี้ยังไม่พร้อมส่งรูป');
        abort_if($booking->deliveryTasks->isEmpty(), 403, 'รายการนี้ยังไม่มีงานติดตั้ง');

        $nonCompletedTasks = $booking->deliveryTasks->where('status', '!=', 'completed');

        abort_if($nonCompletedTasks->isEmpty(), 403, 'รายการนี้อนุมัติเรียบร้อยแล้ว');
        abort_if($nonCompletedTasks->every(fn (DeliveryTask $task) => $task->status === 'photo_uploaded'), 403, 'รายการนี้ส่งรูปครบแล้วและกำลังรอแอดมินอนุมัติ');
    }
}
