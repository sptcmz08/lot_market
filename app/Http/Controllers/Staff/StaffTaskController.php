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

class StaffTaskController extends Controller
{
    protected $photoUploadService;

    public function __construct(PhotoUploadService $photoUploadService)
    {
        $this->photoUploadService = $photoUploadService;
    }

    public function index()
    {
        return redirect()->route('staff.bookings.index');
    }

    public function show(DeliveryTask $task)
    {
        return redirect()->route('staff.bookings.camera', $task->booking_id);
    }

    public function start(DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        if ($task->status !== 'waiting') {
            return back()->with('error', 'งานนี้เริ่มไปแล้ว หรือ เสร็จสิ้นแล้ว');
        }

        DB::transaction(function () use ($task) {
            $oldTaskStatus = $task->status;
            $task->update([
                'status' => 'started',
                'started_at' => now(),
            ]);

            $booking = $task->booking;
            $oldBookingStatus = $booking->status;
            $newBookingStatus = $booking->refreshDeliveryStatus();

            StatusLogService::log(DeliveryTask::class, $task->id, $oldTaskStatus, 'started', auth()->id(), 'เริ่ม'.$task->typeLabel());
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, $newBookingStatus, auth()->id(), 'เริ่ม'.$task->typeLabel());
        });

        return back()->with('success', 'เริ่มงานติดตั้งเรียบร้อยแล้ว!');
    }

    public function uploadPhoto(Request $request, DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'photo_type' => 'required|in:after',
            'photo' => 'nullable|required_without:photos|image',
            'photos' => 'nullable|required_without:photo|array|min:1',
            'photos.*' => 'required|image',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'note' => 'nullable|string|max:250',
        ], [
            'photo.required_without' => 'กรุณาเลือกรูปภาพ',
            'photos.required_without' => 'กรุณาเลือกรูปภาพ',
        ]);

        $task->load('booking.deliveryTasks.photos');
        $files = $request->hasFile('photos')
            ? $request->file('photos')
            : [$request->file('photo')];

        foreach ($files as $file) {
            $path = $this->photoUploadService->upload($file);

            DeliveryPhoto::create([
                'delivery_task_id' => $task->id,
                'photo_type' => $validated['photo_type'],
                'image_path' => $path,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'taken_at' => now(),
                'uploaded_by' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'ocr_status' => null,
            ]);
        }

        return back()->with('success', 'อัปโหลดรูปหลังติดตั้งสำเร็จ ' . count($files) . ' รูป สามารถเพิ่มรูปหรือกดส่งงานได้');
    }

    public function reportProblem(Request $request, DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'problem_note' => 'required|string|min:5',
            'problem_photo' => 'nullable|image',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::transaction(function () use ($request, $task) {
            $oldTaskStatus = $task->status;
            
            $task->update([
                'status' => 'problem',
                'problem_note' => $request->problem_note,
            ]);

            $booking = $task->booking;
            $oldBookingStatus = $booking->status;
            $newBookingStatus = $booking->refreshDeliveryStatus();

            if ($request->hasFile('problem_photo')) {
                $path = $this->photoUploadService->upload($request->file('problem_photo'));
                DeliveryPhoto::create([
                    'delivery_task_id' => $task->id,
                    'photo_type' => 'problem',
                    'image_path' => $path,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'taken_at' => now(),
                    'uploaded_by' => auth()->id(),
                    'note' => $request->problem_note,
                ]);
            }

            StatusLogService::log(DeliveryTask::class, $task->id, $oldTaskStatus, 'problem', auth()->id(), 'รายงานปัญหา: ' . $request->problem_note);
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, $newBookingStatus, auth()->id(), 'มีปัญหา'.$task->typeLabel().': ' . $request->problem_note);
        });

        return redirect()->route('staff.tasks.index')->with('success', 'รายงานปัญหาไปยังผู้ดูแลระบบแล้ว');
    }
}
