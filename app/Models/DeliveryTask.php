<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'staff_id',
        'task_date',
        'status',
        'started_at',
        'completed_at',
        'problem_note'
    ];

    protected $casts = [
        'task_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function photos()
    {
        return $this->hasMany(DeliveryPhoto::class);
    }
}
