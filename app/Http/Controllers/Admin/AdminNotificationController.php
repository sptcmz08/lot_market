<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DeliveryTask;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function check(Request $request)
    {
        $user = auth()->user();

        // 1. Admin Notifications Data
        $pendingBookingsCount = Booking::where('status', 'pending_admin')->count();
        $photoReviewCount = DeliveryTask::where('status', 'photo_uploaded')->count();

        $latestPendingBooking = Booking::where('status', 'pending_admin')
            ->with('lots')
            ->orderBy('id', 'desc')
            ->first();

        $latestPhotoReviewTask = DeliveryTask::where('status', 'photo_uploaded')
            ->with(['booking.lots'])
            ->orderBy('updated_at', 'desc')
            ->first();

        // 2. Staff Notifications Data
        $confirmedBookingsCount = Booking::whereIn('status', ['confirmed', 'assigned'])
            ->whereDate('use_date', now()->format('Y-m-d'))
            ->count();

        $latestConfirmedBooking = Booking::whereIn('status', ['confirmed', 'assigned'])
            ->whereDate('use_date', now()->format('Y-m-d'))
            ->with('lots')
            ->orderBy('updated_at', 'desc')
            ->first();

        $latestRejectedTask = DeliveryTask::whereNotNull('problem_note')
            ->where('status', 'started')
            ->with('booking.lots')
            ->orderBy('updated_at', 'desc')
            ->first();

        $latestApprovedTask = DeliveryTask::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->with('booking.lots')
            ->orderBy('completed_at', 'desc')
            ->first();

        return response()->json([
            'role' => $user?->role,

            // Admin Payload
            'pending_bookings_count' => $pendingBookingsCount,
            'photo_review_count' => $photoReviewCount,
            'latest_pending_booking' => $latestPendingBooking ? [
                'id' => $latestPendingBooking->id,
                'code' => $latestPendingBooking->booking_code,
                'shop' => $latestPendingBooking->shop_name,
                'lots' => $latestPendingBooking->lots->pluck('lot_code')->implode(', '),
                'url' => route('admin.bookings.show', $latestPendingBooking),
                'created_at' => $latestPendingBooking->created_at->toIso8601String(),
            ] : null,
            'latest_photo_review' => $latestPhotoReviewTask ? [
                'task_id' => $latestPhotoReviewTask->id,
                'booking_id' => $latestPhotoReviewTask->booking_id,
                'code' => $latestPhotoReviewTask->booking?->booking_code,
                'shop' => $latestPhotoReviewTask->booking?->shop_name,
                'lots' => $latestPhotoReviewTask->booking?->lots->pluck('lot_code')->implode(', '),
                'type_label' => $latestPhotoReviewTask->typeLabel(),
                'url' => route('admin.bookings.show', $latestPhotoReviewTask->booking_id) . '#installation-review',
                'updated_at' => $latestPhotoReviewTask->updated_at->toIso8601String(),
            ] : null,

            // Staff Payload
            'confirmed_bookings_count' => $confirmedBookingsCount,
            'latest_confirmed_booking' => $latestConfirmedBooking ? [
                'id' => $latestConfirmedBooking->id,
                'code' => $latestConfirmedBooking->booking_code,
                'shop' => $latestConfirmedBooking->shop_name,
                'lots' => $latestConfirmedBooking->lots->pluck('lot_code')->implode(', '),
                'url' => route('staff.bookings.camera', $latestConfirmedBooking),
                'updated_at' => $latestConfirmedBooking->updated_at->toIso8601String(),
            ] : null,
            'latest_rejected_task' => $latestRejectedTask ? [
                'task_id' => $latestRejectedTask->id,
                'booking_id' => $latestRejectedTask->booking_id,
                'code' => $latestRejectedTask->booking?->booking_code,
                'shop' => $latestRejectedTask->booking?->shop_name,
                'lots' => $latestRejectedTask->booking?->lots->pluck('lot_code')->implode(', '),
                'reason' => $latestRejectedTask->problem_note,
                'url' => route('staff.bookings.camera', $latestRejectedTask->booking_id),
                'updated_at' => $latestRejectedTask->updated_at->toIso8601String(),
            ] : null,
            'latest_approved_task' => $latestApprovedTask ? [
                'task_id' => $latestApprovedTask->id,
                'booking_id' => $latestApprovedTask->booking_id,
                'code' => $latestApprovedTask->booking?->booking_code,
                'shop' => $latestApprovedTask->booking?->shop_name,
                'lots' => $latestApprovedTask->booking?->lots->pluck('lot_code')->implode(', '),
                'type_label' => $latestApprovedTask->typeLabel(),
                'url' => route('staff.bookings.camera', $latestApprovedTask->booking_id),
                'completed_at' => $latestApprovedTask->completed_at?->toIso8601String(),
            ] : null,
        ]);
    }
}
