<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_task_id',
        'photo_type',
        'image_path',
        'latitude',
        'longitude',
        'taken_at',
        'uploaded_by',
        'note',
        'ocr_text',
        'ocr_status',
        'ocr_confidence'
    ];

    protected $casts = [
        'taken_at' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'ocr_confidence' => 'float',
    ];

    public function deliveryTask()
    {
        return $this->belongsTo(DeliveryTask::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
