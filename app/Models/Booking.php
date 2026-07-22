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
        'tent_quantity',
        'tent_items',
        'counter_size',
        'counter_color',
        'counter_quantity',
        'counter_items',
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
        'tent_quantity' => 'integer',
        'counter_quantity' => 'integer',
        'tent_items' => 'array',
        'counter_items' => 'array',
    ];

    public function lots()
    {
        return $this->belongsToMany(Lot::class, 'booking_lots');
    }

    public function deliveryTask()
    {
        return $this->hasOne(DeliveryTask::class);
    }

    public function deliveryTasks()
    {
        return $this->hasMany(DeliveryTask::class)->orderByRaw("CASE task_type WHEN 'tent' THEN 1 WHEN 'counter' THEN 2 ELSE 3 END");
    }

    public function refreshDeliveryStatus(): string
    {
        if (in_array($this->status, ['pending_admin', 'cancelled'], true)) {
            return $this->status;
        }

        $tasks = $this->deliveryTasks()->get();
        $status = 'confirmed';

        if ($tasks->contains('status', 'problem')) {
            $status = 'problem';
        } elseif ($tasks->isNotEmpty() && $tasks->every(fn (DeliveryTask $task) => $task->status === 'completed')) {
            $status = 'completed';
        } elseif ($tasks->contains(fn (DeliveryTask $task) => in_array($task->status, ['started', 'photo_uploaded', 'completed'], true))) {
            $status = 'installing';
        }

        if ($this->status !== $status) {
            $this->update(['status' => $status]);
        }

        return $status;
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
        $items = collect($this->tentEquipmentItems())
            ->map(fn (array $item) => 'เต็นท์ '.$item['size'].(!empty($item['color']) ? ' สี'.$item['color'] : '').' จำนวน '.$item['quantity'].' หลัง')
            ->merge(collect($this->counterEquipmentItems())
                ->map(fn (array $item) => 'เคาน์เตอร์ '.$item['size'].(!empty($item['color']) ? ' สี'.$item['color'] : '').' จำนวน '.$item['quantity'].' ชุด'));

        return $items->implode(' / ') ?: '-';
    }

    public function tentEquipmentItems(): array
    {
        if (!empty($this->tent_items)) {
            return $this->normalizeEquipmentItems($this->tent_items);
        }

        return $this->tent_size ? [[
            'size' => $this->tent_size,
            'color' => $this->tent_color,
            'quantity' => (int) ($this->tent_quantity ?: 1),
        ]] : [];
    }

    public function counterEquipmentItems(): array
    {
        if (!empty($this->counter_items)) {
            return $this->normalizeEquipmentItems($this->counter_items);
        }

        return $this->counter_size ? [[
            'size' => $this->counter_size,
            'color' => $this->counter_color,
            'quantity' => (int) ($this->counter_quantity ?: 1),
        ]] : [];
    }

    public function ensureEquipmentTasks(): void
    {
        $types = [];
        if ($this->tent_size || !empty($this->tent_items)) {
            $types[] = DeliveryTask::TYPE_TENT;
        }
        if ($this->counter_size || !empty($this->counter_items)) {
            $types[] = DeliveryTask::TYPE_COUNTER;
        }

        foreach ($types as $type) {
            DeliveryTask::firstOrCreate(
                ['booking_id' => $this->id, 'task_type' => $type],
                ['task_date' => $this->use_date, 'status' => 'waiting']
            );
        }
    }

    private function normalizeEquipmentItems(array $items): array
    {
        return collect($items)->filter(fn ($item) => !empty($item['size']))->map(fn ($item) => [
            'size' => (string) $item['size'],
            'color' => $item['color'] ?? null,
            'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
        ])->values()->all();
    }
}

