<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    use HasFactory;

    // Disabling updated_at since the table migration only has created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'old_status',
        'new_status',
        'changed_by',
        'note'
    ];

    public function loggable()
    {
        return $this->morphTo();
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
