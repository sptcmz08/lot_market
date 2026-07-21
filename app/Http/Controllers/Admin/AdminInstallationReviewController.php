<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use App\Models\DeliveryPhoto;
use App\Services\StatusLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminInstallationReviewController extends Controller
{
    public function approveLot(Booking $booking)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensureLotPending($booking);

        DB::transaction(function () use ($booking) {
            DeliveryPhoto::whereIn('delivery_task_id', $booking->deliveryTasks->pluck('id'))
                ->where('photo_type', 'lot_number')
                ->where('ocr_status', 'submitted')
                ->update([
                    'ocr_status' => 'approved',
                    'ocr_text' => 'ยืนยันโดยแอดมิน: '.auth()->user()->name,
                    'ocr_confidence' => 100,
                ]);

            foreach ($booking->deliveryTasks as $task) {
                if (str_starts_with((string) $task->problem_note, 'ตีกลับรูป LOT โดยแอดมิน:')) {
                    $task->update(['problem_note' => null]);
                }
            }

            StatusLogService::log(Booking::class, $booking->id, $booking->status, $booking->status, auth()->id(), 'แอดมินอนุมัติรูป LOT แล้ว Staff สามารถแนบรูปงานติดตั้งได้');
        });

        return back()->with('success', 'อนุมัติรูป LOT เรียบร้อยแล้ว Staff สามารถแนบรูปงานติดตั้งได้');
    }

    public function rejectLot(Request $request, Booking $booking)
    {
        $validated = $request->validate(['reason' => 'required|string|max:250']);
        $booking->load('deliveryTasks.photos');
        $this->ensureLotPending($booking);

        DB::transaction(function () use ($booking, $validated) {
            DeliveryPhoto::whereIn('delivery_task_id', $booking->deliveryTasks->pluck('id'))
                ->where('photo_type', 'lot_number')
                ->where('ocr_status', 'submitted')
                ->update([
                    'ocr_status' => 'rejected',
                    'ocr_text' => 'ตีกลับโดยแอดมิน: '.$validated['reason'],
                    'ocr_confidence' => 0,
                ]);

            foreach ($booking->deliveryTasks as $task) {
                $task->update(['problem_note' => 'ตีกลับรูป LOT โดยแอดมิน: '.$validated['reason']]);
            }
            StatusLogService::log(Booking::class, $booking->id, $booking->status, $booking->status, auth()->id(), 'แอดมินตีกลับรูป LOT: '.$validated['reason']);
        });

        return back()->with('success', 'ตีกลับรูป LOT ให้ Staff ส่งใหม่แล้ว');
    }

    public function approveWork(Booking $booking)
    {
        $booking->load('deliveryTasks.photos');
        $this->ensureWorkPending($booking);
        abort_unless(
            $booking->deliveryTasks->flatMap->photos->contains('photo_type', 'after'),
            422,
            'ไม่พบรูปงานติดตั้ง'
        );

        DB::transaction(function () use ($booking) {
            foreach ($booking->deliveryTasks->where('status', 'photo_uploaded') as $task) {
                $task->update(['status' => 'completed', 'completed_at' => now(), 'problem_note' => null]);
                StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'completed', auth()->id(), 'แอดมินอนุมัติรูปงานติดตั้ง');
            }

            $oldStatus = $booking->status;
            $newStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldStatus, $newStatus, auth()->id(), 'แอดมินอนุมัติรูปงานติดตั้ง รูปแสดงในหน้าลูกค้าแล้ว');
        });

        return back()->with('success', 'อนุมัติรูปงานติดตั้งเรียบร้อยแล้ว');
    }

    public function rejectWork(Request $request, Booking $booking)
    {
        $validated = $request->validate(['reason' => 'required|string|max:250']);
        $booking->load('deliveryTasks');
        $this->ensureWorkPending($booking);

        DB::transaction(function () use ($booking, $validated) {
            foreach ($booking->deliveryTasks->where('status', 'photo_uploaded') as $task) {
                $task->update(['status' => 'started', 'problem_note' => 'ตีกลับรูปงานโดยแอดมิน: '.$validated['reason']]);
                StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'started', auth()->id(), 'แอดมินตีกลับรูปงาน: '.$validated['reason']);
            }
            $booking->refresh()->refreshDeliveryStatus();
        });

        return back()->with('success', 'ตีกลับรูปงานให้ Staff เพิ่มรูปและส่งใหม่แล้ว');
    }

    public function approveWorkTask(DeliveryTask $task)
    {
        $task->load(['booking.deliveryTasks.photos', 'photos']);
        $booking = $task->booking;

        abort_unless(
            $booking->deliveryTasks->flatMap->photos
                ->where('photo_type', 'lot_number')
                ->contains('ocr_status', 'approved'),
            403,
            'ต้องอนุมัติรูป LOT ก่อนตรวจรูปงานติดตั้ง'
        );

        abort_unless($task->status === 'photo_uploaded', 404);
        abort_unless($task->photos->contains('photo_type', 'after'), 422, 'ไม่พบรูปงานติดตั้ง');

        DB::transaction(function () use ($task, $booking) {
            $task->update(['status' => 'completed', 'completed_at' => now(), 'problem_note' => null]);
            StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'completed', auth()->id(), 'แอดมินอนุมัติรูปงานติดตั้ง ' . $task->typeLabel());

            $oldStatus = $booking->status;
            $newStatus = $booking->refresh()->refreshDeliveryStatus();
            StatusLogService::log(Booking::class, $booking->id, $oldStatus, $newStatus, auth()->id(), 'แอดมินอนุมัติรูปงานติดตั้ง ' . $task->typeLabel() . ' รูปแสดงในหน้าลูกค้าแล้ว');
        });

        return back()->with('success', 'อนุมัติรูปงานติดตั้ง ' . $task->typeLabel() . ' เรียบร้อยแล้ว');
    }

    public function rejectWorkTask(Request $request, DeliveryTask $task)
    {
        $validated = $request->validate(['reason' => 'required|string|max:250']);
        $task->load('booking');
        $booking = $task->booking;

        abort_unless(
            $booking->deliveryTasks->flatMap->photos
                ->where('photo_type', 'lot_number')
                ->contains('ocr_status', 'approved'),
            403,
            'ต้องอนุมัติรูป LOT ก่อนตรวจรูปงานติดตั้ง'
        );

        abort_unless($task->status === 'photo_uploaded', 404);

        DB::transaction(function () use ($task, $booking, $validated) {
            $task->update(['status' => 'started', 'problem_note' => 'ตีกลับรูปงานโดยแอดมิน: '.$validated['reason']]);
            StatusLogService::log(DeliveryTask::class, $task->id, 'photo_uploaded', 'started', auth()->id(), 'แอดมินตีกลับรูปงาน ' . $task->typeLabel() . ': '.$validated['reason']);
            
            $booking->refresh()->refreshDeliveryStatus();
        });

        return back()->with('success', 'ตีกลับรูปงาน ' . $task->typeLabel() . ' ให้ Staff เพิ่มรูปและส่งใหม่แล้ว');
    }

    private function ensureLotPending(Booking $booking): void
    {
        abort_unless(
            $booking->deliveryTasks->flatMap->photos
                ->where('photo_type', 'lot_number')
                ->contains('ocr_status', 'submitted'),
            404
        );
    }

    private function ensureWorkPending(Booking $booking): void
    {
        abort_unless(
            $booking->deliveryTasks->flatMap->photos
                ->where('photo_type', 'lot_number')
                ->contains('ocr_status', 'approved'),
            403,
            'ต้องอนุมัติรูป LOT ก่อนตรวจรูปงานติดตั้ง'
        );
        abort_unless($booking->deliveryTasks->contains('status', 'photo_uploaded'), 404);
    }
}
