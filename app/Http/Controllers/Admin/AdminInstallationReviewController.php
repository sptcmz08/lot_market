<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInstallationReviewController extends Controller
{
    public function index()
    {
        $bookings = Booking::whereHas('deliveryTasks', fn ($tasks) => $tasks->where('status', 'photo_uploaded'))
            ->with(['lots', 'deliveryTasks.photos.uploadedBy'])
            ->orderByDesc('use_date')
            ->paginate(15);

        return view('admin.installation-reviews.index', compact('bookings'));
    }

    public function approve(Booking $booking)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensurePending($booking);
        abort_unless($booking->deliveryTasks->flatMap->photos->contains('photo_type', 'after'), 422, 'ไม่พบรูปส่งงาน');

        DB::transaction(function () use ($booking) {
            foreach ($booking->deliveryTasks->where('status', 'photo_uploaded') as $task) {
                $task->update(['status' => 'completed', 'completed_at' => now(), 'problem_note' => null]);
                StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'completed', auth()->id(), 'แอดมินอนุมัติรูปส่งงาน');
            }

            $oldStatus = $booking->status;
            $newStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldStatus, $newStatus, auth()->id(), 'แอดมินอนุมัติรูปส่งงาน รูปแสดงในหน้าลูกค้าแล้ว');
        });

        return back()->with('success', 'อนุมัติรูปส่งงานเรียบร้อยแล้ว');
    }

    public function reject(Request $request, Booking $booking)
    {
        $validated = $request->validate(['reason' => 'required|string|max:250']);
        $booking->load('deliveryTasks');
        $this->ensurePending($booking);

        DB::transaction(function () use ($booking, $validated) {
            foreach ($booking->deliveryTasks->where('status', 'photo_uploaded') as $task) {
                $task->update(['status' => 'started', 'problem_note' => 'ตีกลับโดยแอดมิน: '.$validated['reason']]);
                StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'started', auth()->id(), 'แอดมินตีกลับรูป: '.$validated['reason']);
            }
            $booking->refresh()->refreshDeliveryStatus();
        });

        return back()->with('success', 'ตีกลับรายการให้พนักงานเพิ่มรูปและส่งใหม่แล้ว');
    }

    private function ensurePending(Booking $booking): void
    {
        abort_unless($booking->deliveryTasks->contains('status', 'photo_uploaded'), 404);
    }
}
