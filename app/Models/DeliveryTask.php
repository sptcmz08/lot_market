<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryTask extends Model
{
    use HasFactory;

    public const TYPE_TENT = 'tent';
    public const TYPE_COUNTER = 'counter';
    public const TYPE_OTHER = 'other';

    protected $fillable = [
        'booking_id',
        'staff_id',
        'task_type',
        'equipment_note',
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

    public function typeLabel(): string
    {
        return match ($this->task_type) {
            self::TYPE_TENT => 'งานเต็นท์',
            self::TYPE_COUNTER => 'งานเคาน์เตอร์',
            self::TYPE_OTHER => 'งานอุปกรณ์อื่น',
            default => 'งานจัดส่ง',
        };
    }

    public function equipmentSummary(): string
    {
        if (!$this->booking) {
            return $this->equipment_note ?: '-';
        }

        return match ($this->task_type) {
            self::TYPE_TENT => $this->booking->tent_size
                ? trim('เต็นท์ '.$this->booking->tent_size.($this->booking->tent_color ? ' สี'.$this->booking->tent_color : ''))
                : '-',
            self::TYPE_COUNTER => $this->booking->counter_size
                ? trim('เคาน์เตอร์ '.$this->booking->counter_size.($this->booking->counter_color ? ' สี'.$this->booking->counter_color : ''))
                : '-',
            self::TYPE_OTHER => $this->equipment_note ?: 'อุปกรณ์อื่น',
            default => $this->booking->equipmentSummary(),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'waiting' => 'รอเริ่มงาน',
            'started' => 'กำลังดำเนินการ',
            'photo_uploaded' => 'ส่งแล้ว / รออนุมัติ',
            'completed' => 'เสร็จแล้ว',
            'problem' => 'พบปัญหา',
            default => $this->status,
        };
    }
}
