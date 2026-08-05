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

        return response()->json([
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
                'type_label' => $latestPhotoReviewTask->equipment_type === 'counter' ? 'เคาน์เตอร์' : 'เต็นท์',
                'url' => route('admin.bookings.show', $latestPhotoReviewTask->booking_id) . '#installation-review',
                'updated_at' => $latestPhotoReviewTask->updated_at->toIso8601String(),
            ] : null,
        ]);
    }
}
