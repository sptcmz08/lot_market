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
use Illuminate\Support\Facades\Storage;

class StaffBookingController extends Controller
{
    public function __construct(private PhotoUploadService $photoUploadService)
    {
    }

    public function index(Request $request)
    {
        $todayDate = now('Asia/Bangkok')->format('Y-m-d');
        $isAllDates = ($request->query('date') === 'all');
        $summaryDate = ($request->filled('date') && !$isAllDates) ? $request->date : $todayDate;
        $query = Booking::with(['lots.zone', 'deliveryTasks.photos']);

        if ($request->filled('status')) {
            if ($request->status !== 'all') {
                $query->where('status', $request->status);
            }
        } else {
            // Default filter for current day without explicit status: show pending work (exclude completed)
            // If staff selects a specific date or 'all', do not exclude completed so historical data is displayed
            if (!$request->filled('date')) {
                $query->where('status', '!=', 'completed');
            }
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

        $equipmentType = $request->string('equipment_type')->toString();
        if (in_array($equipmentType, [DeliveryTask::TYPE_TENT, DeliveryTask::TYPE_COUNTER, DeliveryTask::TYPE_OTHER], true)) {
            $query->whereHas('deliveryTasks', fn ($tasks) => $tasks->where('task_type', $equipmentType));
        }

        if (!$isAllDates) {
            $query->whereDate('use_date', $summaryDate);
        }

        $bookings = $query->orderByDesc('use_date')->orderByDesc('created_at')->paginate(15)->withQueryString();

        foreach ($bookings as $booking) {
            if ($booking->deliveryTasks->isEmpty() && $booking->status !== 'cancelled') {
                $booking->ensureEquipmentTasks();
                $booking->load(['lots.zone', 'deliveryTasks.photos']);
            }
        }

        $isToday = ($summaryDate === $todayDate && !$isAllDates);

        // Calculate summary statistics specifically for the summary date (excluding cancelled bookings)
        $activeBookingsQuery = Booking::where('status', '!=', 'cancelled');
        if (!$isAllDates) {
            $activeBookingsQuery->whereDate('use_date', $summaryDate);
        }
        $summaryBookings = $activeBookingsQuery->get();

        $tentSummary = [
            'total' => 0,
            'sizes' => []
        ];

        $counterSummary = [
            'total' => 0,
            'sizes' => []
        ];

        foreach ($summaryBookings as $b) {
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
                $displaySize = preg_match('/^\d+\s*ล็อค/u', $size, $matches) ? $matches[0] : $size;
                
                $counterSummary['total'] += $qty;
                if (!isset($counterSummary['sizes'][$displaySize])) {
                    $counterSummary['sizes'][$displaySize] = 0;
                }
                $counterSummary['sizes'][$displaySize] += $qty;
            }
        }

        // Build a compact, daily work-sheet view for staff. It is intentionally
        // separate from the photo/action table so the operational overview stays
        // readable while the existing delivery workflow remains unchanged.
        $workRows = collect();
        foreach ($bookings as $booking) {
            $tasks = $booking->deliveryTasks;
            $lotCodes = $booking->lots->pluck('lot_code')->filter()->implode(', ') ?: '-';
            $zoneCodes = $booking->lots->map(fn ($lot) => $lot->zone?->code)->filter()->unique()->implode(', ') ?: '-';
            $types = $equipmentType ? [$equipmentType] : [
                DeliveryTask::TYPE_TENT,
                DeliveryTask::TYPE_COUNTER,
                DeliveryTask::TYPE_OTHER,
            ];

            foreach ($types as $type) {
                if ($type === DeliveryTask::TYPE_TENT) {
                    $items = $booking->tentEquipmentItems();
                    foreach ($items as $item) {
                        $workRows->push([
                            'date' => $booking->use_date,
                            'shop' => $booking->shop_name,
                            'color_or_number' => $item['color'] ?? '-',
                            'is_number' => false,
                            'equipment' => 'เต็นท์ '.($item['size'] ?? '-').' x'.($item['quantity'] ?? 1),
                            'zone' => $zoneCodes,
                            'lots' => $lotCodes,
                            'sequence' => $workRows->count() + 1,
                        ]);
                    }
                    continue;
                }

                if ($type === DeliveryTask::TYPE_COUNTER) {
                    $items = $booking->counterEquipmentItems();
                    foreach ($items as $item) {
                        $displaySize = preg_match('/^\d+\s*ล็อค/u', $item['size'] ?? '', $matches) ? $matches[0] : ($item['size'] ?? '-');
                        $workRows->push([
                            'date' => $booking->use_date,
                            'shop' => $booking->shop_name,
                            'color_or_number' => $item['number'] ?? '-',
                            'is_number' => true,
                            'equipment' => 'เคาน์เตอร์ '.$displaySize,
                            'zone' => $zoneCodes,
                            'lots' => $lotCodes,
                            'sequence' => $workRows->count() + 1,
                        ]);
                    }
                    continue;
                }

                foreach ($tasks->where('task_type', DeliveryTask::TYPE_OTHER) as $task) {
                    $workRows->push([
                        'date' => $booking->use_date,
                        'shop' => $booking->shop_name,
                        'color_or_number' => '-',
                        'is_number' => false,
                        'equipment' => $task->equipment_note ?: 'อุปกรณ์อื่น',
                        'zone' => $zoneCodes,
                        'lots' => $lotCodes,
                        'sequence' => $workRows->count() + 1,
                    ]);
                }
            }
        }

        return view('staff.bookings-index-v2', compact(
            'bookings',
            'tentSummary',
            'counterSummary',
            'summaryDate',
            'todayDate',
            'isToday',
            'isAllDates',
            'equipmentType',
            'workRows'
        ));
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
            'photo_type' => 'required|in:after',
            'camera_photo' => 'nullable|required_without:photos|image',
            'photos' => 'nullable|required_without:camera_photo|array|min:1',
            'photos.*' => 'required|image',
            'note' => 'nullable|string|max:250',
            'submit_after_upload' => 'nullable|boolean',
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
        if (!$task) {
            $task = $booking->deliveryTasks
                ->where('status', '!=', 'completed')
                ->sortBy('id')
                ->firstOrFail();
        } else {
            abort_unless($task->booking_id === $booking->id, 404);
        }

        if ($request->filled('on_site_payment_method')) {
            $paymentError = $this->processOnSitePayment($request, $booking);
            if ($paymentError) {
                return back()->with('error', $paymentError)->withInput();
            }
        }

        foreach ($files as $file) {
            DeliveryPhoto::create([
                'delivery_task_id' => $task->id,
                'photo_type' => $validated['photo_type'],
                'image_path' => $this->photoUploadService->upload($file),
                'taken_at' => now(),
                'uploaded_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'ocr_status' => null,
            ]);
        }

        if ($request->boolean('submit_after_upload')) {
            $oldStatus = $task->status;
            $task->update([
                'status' => 'photo_uploaded',
                'started_at' => $task->started_at ?: now(),
                'problem_note' => null,
            ]);
            StatusLogService::log(DeliveryTask::class, $task->id, $oldStatus, 'photo_uploaded', auth()->id(), 'Staff ส่งรูปงานติดตั้งให้แอดมินตรวจสอบ');

            $oldBookingStatus = $booking->status;
            $newBookingStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, $newBookingStatus, auth()->id(), 'ส่งรูปงานแล้ว รอแอดมินอนุมัติ');

            return redirect()
                ->route('staff.bookings.index')
                ->with('success', 'อัปโหลดและส่งรูปงานให้ Admin ตรวจสอบเรียบร้อยแล้ว');
        }

        return redirect()
            ->route('staff.bookings.camera', $booking)
            ->with('clear_photo_draft_keys', [$booking->id.'_'.$task->id])
            ->with('success', 'แนบรูปเรียบร้อยแล้ว '.$files->count().' รูป สามารถเพิ่มรูปต่อหรือกดส่งได้');
    }

    public function destroyPhoto(Booking $booking, DeliveryPhoto $photo)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        $task = $booking->deliveryTasks->firstWhere('id', $photo->delivery_task_id);
        abort_unless($task, 404);

        abort_unless($photo->photo_type === 'after', 404);
        abort_if(in_array($task->status, ['photo_uploaded', 'completed'], true), 403, 'รูปงานที่ส่งตรวจหรืออนุมัติแล้วไม่สามารถลบได้');

        DB::transaction(function () use ($photo) {
            Storage::disk('public')->delete($photo->image_path);
            $photo->delete();
        });

        return redirect()
            ->route('staff.bookings.camera', $booking)
            ->with('success', 'ลบรูปเรียบร้อยแล้ว');
    }

    public function submitWork(Request $request, Booking $booking, DeliveryTask $task = null)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePhotoAccess($booking);

        if ($request->filled('on_site_payment_method')) {
            $paymentError = $this->processOnSitePayment($request, $booking);
            if ($paymentError) {
                return back()->with('error', $paymentError)->withInput();
            }
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

    private function processOnSitePayment(Request $request, Booking $booking): ?string
    {
        $method = $request->input('on_site_payment_method');
        if (!$method) return null;

        if ($method === 'cash') {
            $wasCollected = $booking->front_store_collected_at !== null;

            $booking->update([
                'collect_front_store' => true,
                'front_store_collected_at' => now(),
                'front_store_collected_by' => auth()->id(),
            ]);

            StatusLogService::log(
                Booking::class,
                $booking->id,
                $booking->status,
                $booking->status,
                auth()->id(),
                'Staff บันทึกรับชำระเงินสดหน้าร้าน'
            );
        } elseif ($method === 'transfer') {
            if ($request->hasFile('on_site_payment_slip')) {
                $oldPath = $booking->payment_slip_path;
                $newPath = $this->photoUploadService->upload($request->file('on_site_payment_slip'), 'payment-slips');

                $booking->update([
                    'payment_slip_path' => $newPath,
                ]);

                if ($oldPath && $oldPath !== $newPath) {
                    Storage::disk('public')->delete($oldPath);
                }

                StatusLogService::log(
                    Booking::class,
                    $booking->id,
                    $booking->status,
                    $booking->status,
                    auth()->id(),
                    'Staff แนบรูปสลิปชำระเงินโอนหน้าร้าน'
                );
            } elseif (!$booking->payment_slip_path) {
                return 'กรุณาแนบรูปสลิปการโอนเงินหน้าร้าน';
            }
        }

        return null;
    }

    private function ensurePhotoAccess(Booking $booking): void
    {
        abort_if($booking->status === 'cancelled', 403, 'รายการนี้ถูกยกเลิกแล้ว');
        
        $booking->ensureEquipmentTasks();
        $booking->load('deliveryTasks');

        $nonCompletedTasks = $booking->deliveryTasks->where('status', '!=', 'completed');

        abort_if($nonCompletedTasks->isEmpty(), 403, 'รายการนี้อนุมัติเรียบร้อยแล้ว');
        abort_if($nonCompletedTasks->every(fn (DeliveryTask $task) => $task->status === 'photo_uploaded'), 403, 'รายการนี้ส่งรูปครบแล้วและกำลังรอแอดมินอนุมัติ');
    }
}
