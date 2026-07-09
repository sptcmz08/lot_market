<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'use_date',
        'shop_name',
        'customer_phone',
        'tent_size',
        'counter_size',
        'status',
        'admin_note',
        'customer_note',
        'confirmed_by',
        'confirmed_at'
    ];

    protected $casts = [
        'use_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function lots()
    {
        return $this->belongsToMany(Lot::class, 'booking_lots');
    }

    public function deliveryTask()
    {
        return $this->hasOne(DeliveryTask::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
