<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function generateBookingCode(): string
    {
        return 'BK' . now()->format('YmdHis') . random_int(100, 999);
    }

    public function createBooking(array $data, array $lotIds): Booking
    {
        return DB::transaction(function () use ($data, $lotIds) {
            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'use_date' => $data['use_date'],
                'shop_name' => $data['shop_name'],
                'customer_phone' => $data['customer_phone'],
                'tent_size' => $data['tent_size'],
                'counter_size' => $data['counter_size'] ?? null,
                'customer_note' => $data['customer_note'] ?? null,
                'status' => 'pending_admin',
            ]);

            $booking->lots()->sync($lotIds);

            // Log status change
            StatusLogService::log(Booking::class, $booking->id, null, 'pending_admin', null, 'จองโดยลูกค้า');

            return $booking;
        });
    }
}
