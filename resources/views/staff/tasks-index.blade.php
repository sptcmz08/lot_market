@extends('layouts.staff')

@section('title', 'งานติดตั้งเต็นท์วันนี้ของฉัน')

@section('content')
    <div style="text-align: center; margin-bottom: 20px;">
        <span style="font-size: 14px; color: var(--text-muted); font-weight: 600;">งานติดตั้งประจำวันที่:</span>
        <strong style="font-size: 18px; color: var(--text-dark); display: block;">
            @php
                $parts = explode('-', $today);
                $year = intval($parts[0]) + 543;
                $months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                $month = $months[intval($parts[1]) - 1];
                $day = intval($parts[2]);
                echo "$day $month $year";
            @endphp
        </strong>
    </div>

    @if ($tasks->isEmpty())
        <div class="staff-card" style="text-align: center; padding: 50px 20px;">
            <i class="fa-solid fa-face-laugh-beam" style="font-size: 60px; color: var(--primary); margin-bottom: 15px; display: block;"></i>
            <h3 style="margin: 0; color: var(--text-dark);">วันนี้ไม่มีงานติดตั้งของคุณ!</h3>
            <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">เพลิดเพลินกับวันหยุด หรือติดต่อนายจ้างผู้จัดการแผงเมื่อมีข้อสงสัย</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 15px;">
            @foreach ($tasks as $task)
                @php
                    $isFinished = $task->status === 'completed';
                    $isProblem = $task->status === 'problem';
                @endphp
                <div class="staff-card" style="border-left: 6px solid @if($isFinished) var(--completed) @elseif($isProblem) var(--problem) @elseif($task->status === 'started') var(--started) @else var(--waiting) @endif;">
                    <div class="lot-display-big">
                        {{ $task->booking ? $task->booking->lots->pluck('lot_code')->implode(', ') : '-' }}
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">ชื่อร้านค้า:</span>
                        <strong class="info-value">{{ $task->booking ? $task->booking->shop_name : '-' }}</strong>
                    </div>

                    <div class="info-row">
                        <span class="info-label">ขนาดเต็นท์:</span>
                        <strong class="info-value">{{ $task->booking ? $task->booking->tent_size : '-' }} เมตร</strong>
                    </div>

                    @if ($task->booking && $task->booking->counter_size)
                        <div class="info-row">
                            <span class="info-label">ขนาดเคาน์เตอร์:</span>
                            <strong class="info-value">{{ $task->booking->counter_size }}</strong>
                        </div>
                    @endif

                    <div class="info-row">
                        <span class="info-label">สถานะงาน:</span>
                        @php
                            $statusName = 'รอเริ่มติดตั้ง';
                            if ($task->status === 'started') $statusName = 'กำลังดำเนินการ';
                            if ($task->status === 'completed') $statusName = 'ติดตั้งเรียบร้อย';
                            if ($task->status === 'problem') $statusName = 'มีปัญหาขัดข้อง';
                        @endphp
                        <strong class="info-value" style="color: @if($isFinished) #1E7E34 @elseif($isProblem) #D35400 @else var(--text-dark) @endif;">
                            {{ $statusName }}
                        </strong>
                    </div>

                    <div style="margin-top: 10px;">
                        <a href="{{ route('staff.tasks.show', $task) }}" class="btn-large btn-large-primary" style="height: 48px; font-size:15px;">
                            <i class="fa-solid fa-person-digging"></i> เข้าทำหรือดูข้อมูลงาน
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
