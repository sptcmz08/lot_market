<?php

namespace App\Services;

use App\Models\Booking;

class LotAvailabilityService
{
    public function isAvailable(array $lotIds, string $useDate, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('use_date', $useDate)
            ->whereIn('status', ['pending_admin', 'confirmed', 'assigned', 'installing', 'completed'])
            ->whereHas('lots', function ($q) use ($lotIds) {
                $q->whereIn('lots.id', $lotIds);
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return !$query->exists();
    }
}
