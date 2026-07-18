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
            $query->where('status', $request->status);
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

        return view('staff.bookings-index', compact('bookings'));
    }

    public function camera(Booking $booking)
    {
        $booking->load(['lots', 'deliveryTasks.photos']);
        $this->ensurePhotoAccess($booking);

        return view('staff.booking-camera', compact('booking'));
    }

    public function uploadPhotos(Request $request, Booking $booking)
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

        $files = collect($request->file('photos', []));
        if ($request->hasFile('camera_photo')) {
            $files->prepend($request->file('camera_photo'));
        }

        // Keep newly uploaded evidence on a task that is still awaiting review,
        // so it cannot appear on the customer page before admin approval.
        $task = $booking->deliveryTasks
            ->where('status', '!=', 'completed')
            ->sortBy('id')
            ->firstOrFail();
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

    public function submit(Booking $booking)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        $photos = $booking->deliveryTasks->flatMap->photos;
        if (! $photos->contains('photo_type', 'lot_number') || ! $photos->contains('photo_type', 'after')) {
            return back()->with('error', 'กรุณาเพิ่มรูปเลข LOT และรูปหลังติดตั้งอย่างน้อยประเภทละ 1 รูปก่อนส่ง');
        }

        DB::transaction(function () use ($booking) {
            DeliveryPhoto::whereIn('delivery_task_id', $booking->deliveryTasks->pluck('id'))
                ->where('photo_type', 'lot_number')
                ->where('ocr_status', '!=', 'approved')
                ->update(['ocr_status' => 'submitted']);

            foreach ($booking->deliveryTasks as $task) {
                if ($task->status === 'completed') {
                    continue;
                }

                $oldStatus = $task->status;
                $task->update([
                    'status' => 'photo_uploaded',
                    'started_at' => $task->started_at ?: now(),
                    'problem_note' => null,
                ]);
                StatusLogService::log(DeliveryTask::class, $task->id, $oldStatus, 'photo_uploaded', auth()->id(), 'พนักงานส่งรูปงานให้แอดมินตรวจสอบ');
            }

            $oldBookingStatus = $booking->status;
            $newBookingStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, $newBookingStatus, auth()->id(), 'ส่งรูปงานแล้ว รอแอดมินอนุมัติ');
        });

        return redirect()->route('staff.bookings.index')->with('success', 'ส่งรูปเรียบร้อยแล้ว กรุณารอแอดมินอนุมัติ');
    }

    private function ensurePhotoAccess(Booking $booking): void
    {
        abort_if(in_array($booking->status, ['pending_admin', 'cancelled'], true), 403, 'รายการนี้ยังไม่พร้อมส่งรูป');
        abort_if($booking->deliveryTasks->isEmpty(), 403, 'รายการนี้ยังไม่มีงานติดตั้ง');
        abort_if($booking->deliveryTasks->contains('status', 'photo_uploaded'), 403, 'รายการนี้ส่งแล้วและกำลังรอแอดมินอนุมัติ');
        abort_if($booking->deliveryTasks->every(fn (DeliveryTask $task) => $task->status === 'completed'), 403, 'รายการนี้อนุมัติเรียบร้อยแล้ว');
    }
}
