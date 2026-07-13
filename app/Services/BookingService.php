<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Lot;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function generateBookingCode(): string
    {
        return 'BK' . now()->format('YmdHis') . random_int(100, 999);
    }

    public function createBooking(array $data, array $lotIds): Booking
    {
        return DB::transaction(function () use ($data, $lotIds) {
            Lot::whereIn('id', $lotIds)->lockForUpdate()->get();

            $alreadyBooked = Booking::where('use_date', $data['use_date'])
                ->whereIn('status', ['pending_admin', 'confirmed', 'assigned', 'installing', 'completed'])
                ->whereHas('lots', function ($query) use ($lotIds) {
                    $query->whereIn('lots.id', $lotIds);
                })
                ->exists();

            if ($alreadyBooked) {
                throw ValidationException::withMessages([
                    'lots' => 'ล็อคที่เลือกมีคำสั่งจองอุปกรณ์อยู่แล้วในวันดังกล่าว กรุณาตรวจสอบรายการเดิมก่อน',
                ]);
            }

            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'use_date' => $data['use_date'],
                'shop_name' => $data['shop_name'],
                'customer_phone' => $data['customer_phone'],
                'tent_size' => $data['wants_tent'] ? ($data['tent_size'] ?? null) : null,
                'tent_color' => $data['wants_tent'] ? ($data['tent_color'] ?? null) : null,
                'counter_size' => $data['wants_counter'] ? ($data['counter_size'] ?? null) : null,
                'counter_color' => $data['wants_counter'] ? ($data['counter_color'] ?? null) : null,
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
