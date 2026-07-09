<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'lot_code',
        'display_name',
        'svg_element_id',
        'position_x',
        'position_y',
        'width',
        'height',
        'is_active',
        'note'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position_x' => 'float',
        'position_y' => 'float',
        'width' => 'float',
        'height' => 'float',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_lots');
    }
}
