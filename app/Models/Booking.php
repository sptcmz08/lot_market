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
        'tent_color',
        'counter_size',
        'counter_color',
        'payment_slip_path',
        'collect_front_store',
        'front_store_collected_amount',
        'front_store_collected_at',
        'front_store_collected_by',
        'status',
        'admin_note',
        'customer_note',
        'confirmed_by',
        'confirmed_at'
    ];

    protected $casts = [
        'use_date' => 'date',
        'confirmed_at' => 'datetime',
        'collect_front_store' => 'boolean',
        'front_store_collected_amount' => 'decimal:2',
        'front_store_collected_at' => 'datetime',
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

    public function frontStoreCollectedBy()
    {
        return $this->belongsTo(User::class, 'front_store_collected_by');
    }

    public function equipmentSummary(): string
    {
        $items = [];

        if ($this->tent_size) {
            $items[] = trim('เต็นท์ ' . $this->tent_size . ($this->tent_color ? ' สี' . $this->tent_color : ''));
        }

        if ($this->counter_size) {
            $items[] = trim('เคาน์เตอร์ ' . $this->counter_size . ($this->counter_color ? ' สี' . $this->counter_color : ''));
        }

        return implode(' / ', $items) ?: '-';
    }
}
