<?php

namespace App\Services;

use App\Models\StatusLog;
use Illuminate\Support\Facades\Auth;

class StatusLogService
{
    public static function log(string $type, int $id, ?string $oldStatus, string $newStatus, ?int $userId = null, ?string $note = null): StatusLog
    {
        if (is_null($userId) && Auth::check()) {
            $userId = Auth::id();
        }

        return StatusLog::create([
            'loggable_type' => $type,
            'loggable_id' => $id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $userId,
            'note' => $note
        ]);
    }
}
