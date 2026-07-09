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
        $today = now()->format('Y-m-d');
        $staffId = auth()->id();

        $tasks = DeliveryTask::where('staff_id', $staffId)
            ->whereDate('task_date', $today)
            ->with(['booking.lots'])
            ->get();

        return view('staff.tasks-index', compact('tasks', 'today'));
    }

    public function show(DeliveryTask $task)
    {
        // Enforce ownership
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access to this task.');
        }

        $task->load(['booking.lots', 'photos']);

        return view('staff.task-show', compact('task'));
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
            $booking->update(['status' => 'installing']);

            StatusLogService::log(DeliveryTask::class, $task->id, $oldTaskStatus, 'started', auth()->id(), 'เริ่มการติดตั้งเต็นท์');
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, 'installing', auth()->id(), 'เริ่มการติดตั้งเต็นท์');
        });

        return back()->with('success', 'เริ่มงานติดตั้งเรียบร้อยแล้ว!');
    }

    public function uploadPhoto(Request $request, DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'photo_type' => 'required|in:lot_number,before,after,problem',
            'photo' => 'required|image|max:10240', // Limit to 10MB before compression
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'note' => 'nullable|string|max:250',
        ]);

        $path = $this->photoUploadService->upload($request->file('photo'));

        $photo = DeliveryPhoto::create([
            'delivery_task_id' => $task->id,
            'photo_type' => $request->photo_type,
            'image_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'taken_at' => now(),
            'uploaded_by' => auth()->id(),
            'note' => $request->note,
        ]);

        return back()->with('success', 'อัปโหลดรูปภาพสำเร็จแล้ว');
    }

    public function complete(DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $photos = $task->photos;
        
        $hasLotNumber = $photos->contains('photo_type', 'lot_number');
        $hasAfter = $photos->contains('photo_type', 'after');

        if (!$hasLotNumber || !$hasAfter) {
            return back()->with('error', 'ไม่สามารถส่งงานได้: ต้องถ่ายรูปเลขล็อตอย่างน้อย 1 รูป และรูปหลังติดตั้งอย่างน้อย 1 รูป');
        }

        DB::transaction(function () use ($task) {
            $oldTaskStatus = $task->status;
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $booking = $task->booking;
            $oldBookingStatus = $booking->status;
            $booking->update(['status' => 'completed']);

            StatusLogService::log(DeliveryTask::class, $task->id, $oldTaskStatus, 'completed', auth()->id(), 'ส่งงานติดตั้งเสร็จสิ้น');
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, 'completed', auth()->id(), 'ติดตั้งเต็นท์เรียบร้อยแล้ว');
        });

        return redirect()->route('staff.tasks.index')->with('success', 'ส่งงานติดตั้งเต็นท์เสร็จเรียบร้อย! เก่งมาก!');
    }

    public function reportProblem(Request $request, DeliveryTask $task)
    {
        if ($task->staff_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'problem_note' => 'required|string|min:5',
            'problem_photo' => 'nullable|image|max:10240',
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
            $booking->update(['status' => 'problem']);

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
            StatusLogService::log(Booking::class, $booking->id, $oldBookingStatus, 'problem', auth()->id(), 'มีปัญหาการติดตั้ง: ' . $request->problem_note);
        });

        return redirect()->route('staff.tasks.index')->with('success', 'รายงานปัญหาไปยังผู้ดูแลระบบแล้ว');
    }
}
