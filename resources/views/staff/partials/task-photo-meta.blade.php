<div class="task-meta">
    <div>
        <small>ร้านค้า</small>
        <strong>{{ $booking->shop_name }}</strong>
    </div>
    <div>
        <small>เลข LOT</small>
        <strong class="lot-value">{{ $lotCodes }}</strong>
    </div>
    @if($task->task_type === \App\Models\DeliveryTask::TYPE_TENT)
        <div>
            <small>สีเต็นท์</small>
            <strong>{{ $tentColor }}</strong>
        </div>
    @else
        <div>
            <small>ประเภทงาน</small>
            <strong>{{ $task->typeLabel() }}</strong>
        </div>
    @endif
</div>
