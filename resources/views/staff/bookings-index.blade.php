@extends('layouts.staff')

@section('title', 'รายการจองทั้งหมด')

@section('styles')
<style>
    .page-heading { margin: 4px 0 18px; font-size: 24px; }
    .filter-card { background:#fff;border:1px solid var(--border-cute);border-radius:20px;padding:18px;margin-bottom:18px; }
    .filter-grid { display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end; }
    .field label { display:block;font-size:13px;font-weight:700;margin-bottom:6px; }
    .field input,.field select { width:100%;height:44px;border:2px solid var(--border-cute);border-radius:13px;padding:0 12px;font:inherit;box-sizing:border-box;background:#fff; }
    .table-wrap { overflow:auto;background:#fff;border:1px solid var(--border-cute);border-radius:20px; }
    table { width:100%;border-collapse:collapse;min-width:1260px; }
    th,td { text-align:left;padding:14px 10px;border-bottom:1px solid var(--border-cute);vertical-align:middle;font-size:14px; }
    th { background:#fff9fb;font-size:13px;white-space:nowrap; }
    .badge { display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;font-size:12px;font-weight:800;white-space:nowrap; }
    .badge-waiting { background:#fff1c9;color:#8a6500; }.badge-sent { background:#e9ddff;color:#6d28d9; }
    .badge-approved { background:#dff8e8;color:#14833b; }.badge-rejected { background:#ffe1e1;color:#b42318; }
    .actions { display:flex;gap:7px;align-items:center;white-space:nowrap; }
    .action-btn { min-height:39px;padding:0 13px;border-radius:12px;border:2px solid var(--border-cute);background:#fff;color:var(--text-dark);font:inherit;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:7px;cursor:pointer; }
    .action-btn.send { border:0;background:linear-gradient(135deg,var(--primary),var(--primary-hover));color:#fff; }
    .action-btn[disabled] { opacity:.55;cursor:not-allowed; }
    .equipment-empty { color:var(--text-muted);font-weight:700; }
    .photo-preview-grid { display:grid;grid-template-columns:repeat(2,42px);gap:5px;justify-content:center;margin-top:3px; }
    .photo-preview { position:relative;width:42px;height:42px;border:0;border-radius:8px;overflow:hidden;background:#eef2f7;padding:0;cursor:zoom-in; }
    .photo-preview img { width:100%;height:100%;object-fit:cover;display:block; }
    .photo-preview span { position:absolute;right:2px;bottom:2px;padding:1px 3px;border-radius:4px;background:rgba(17,19,26,.78);color:#fff;font-size:7px;font-weight:800; }
    .pagination { margin-top:18px; }
    @media(max-width:900px){
        .page-heading{font-size:18px;margin:3px 0 10px}.summary-card{padding:11px !important;margin-bottom:12px !important;border-radius:14px !important}.summary-header{margin-bottom:9px !important;padding-bottom:7px !important}.summary-header>div{font-size:12px !important}.summary-header strong{font-size:13px !important}.summary-card>div:not(.summary-header){font-size:11px !important;line-height:1.55 !important}.summary-card>div:not(.summary-header)>span:first-child{min-width:78px !important;padding:3px 7px !important;margin-right:7px !important}.status-tabs{gap:5px !important;margin-bottom:12px !important}.status-tab{padding:6px 10px !important;font-size:11px !important}.filter-grid{grid-template-columns:1fr 1fr auto;gap:7px}.filter-card{padding:9px;margin-bottom:12px;border-radius:13px}.filter-card .field:first-child{grid-column:1/-1}.filter-card .field label{font-size:11px;margin-bottom:3px}.filter-card .field input,.filter-card .field select{height:36px;border-width:1px;border-radius:9px;padding:0 8px;font-size:12px}.filter-card .actions{display:flex;gap:5px}.filter-card .action-btn{justify-content:center;min-height:36px;padding:0 9px;border-width:1px;border-radius:9px;font-size:11px}
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 20px; border: 1px solid var(--border-cute); background: #fff; }
        table{min-width:980px}th,td{padding:8px 6px;font-size:11px}th{font-size:10px}.badge{padding:4px 7px;font-size:10px}.action-btn{min-height:34px;padding:0 8px;font-size:11px;border-radius:9px}
    }
    @media(max-width:480px){
        table{min-width:920px}th,td{padding:7px 5px;font-size:10px}.filter-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr)}.filter-card .actions{grid-column:1/-1}.filter-card .actions>*{flex:1}.summary-header{align-items:flex-start !important}.summary-header>a{font-size:10px !important}
    }
</style>
@endsection

@section('content')
    <h1 class="page-heading">รายการจองทั้งหมด</h1>

    <!-- แผงสรุปจำนวนอุปกรณ์ (รูปแบบ Excel) -->
    <div class="summary-card" style="background:#fff; border:1px solid var(--border-cute); border-radius:20px; padding:18px; margin-bottom:20px; font-family: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <!-- หัวข้อระบุวันที่สรุป -->
        <div class="summary-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; flex-wrap: wrap; gap: 8px;">
            <div style="font-weight: 800; font-size: 15px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-day" style="color: var(--primary); font-size: 18px;"></i>
                <span>สรุปจำนวนอุปกรณ์ ประจำวันที่: <strong style="color: #0874a6; font-size: 16px;">{{ \Carbon\Carbon::parse($summaryDate)->format('d/m/Y') }}</strong></span>
                @if(!empty($isToday))
                    <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 12px; padding: 2px 9px; border-radius: 999px; font-weight: 800;">(วันปัจจุบัน)</span>
                @endif
            </div>
            @if(empty($isToday))
                <a href="{{ route('staff.bookings.index', request()->except('date')) }}" style="font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 700; background: #e0f2fe; padding: 4px 10px; border-radius: 8px;">
                    <i class="fa-solid fa-rotate-left"></i> กลับมาวันปัจจุบัน
                </a>
            @endif
        </div>

        <!-- สรุป เต็นท์ -->
        <div style="margin-bottom: 12px; font-size:14px; line-height: 1.8;">
            <span style="background: #ffd966; color: #7f6000; padding: 4px 10px; border-radius: 6px; font-weight: bold; margin-right: 15px; display: inline-block; min-width: 100px; text-align: center;">สรุป เต็นท์ = {{ $tentSummary['total'] }}</span>
            @php $firstTentSize = true; @endphp
            @if(!empty($tentSummary['sizes']))
                @foreach($tentSummary['sizes'] as $size => $data)
                    @if(!$firstTentSize)
                        <div style="padding-left: 120px; margin-top: 4px;">
                    @endif
                    <strong style="color: #333; font-size: 14px; margin-right: 15px;">{{ $size }} = {{ $data['total'] }}</strong>
                    @if(!empty($data['colors']))
                        @foreach($data['colors'] as $color => $qty)
                            <span style="margin-right: 15px; color: #b4235a; font-weight: bold;">สี{{ $color }} = {{ $qty }}</span>
                        @endforeach
                    @endif
                    @if(!$firstTentSize)
                        </div>
                    @endif
                    @php $firstTentSize = false; @endphp
                @endforeach
            @else
                <span style="color: #94a3b8; font-weight: 600;">ไม่มีรายการเต็นท์ในวันที่เลือก</span>
            @endif
        </div>

        <!-- สรุป เคาเตอร์ -->
        <div style="font-size:14px; line-height: 1.8; border-top: 1px dashed var(--border-cute); padding-top: 10px;">
            <span style="background: #9bc2e6; color: #1f4e78; padding: 4px 10px; border-radius: 6px; font-weight: bold; margin-right: 15px; display: inline-block; min-width: 100px; text-align: center;">สรุปเคาเตอร์</span>
            @if(!empty($counterSummary['sizes']))
                @foreach($counterSummary['sizes'] as $size => $qty)
                    <span style="margin-right: 25px; font-weight: bold; color: #333;">{{ $size }} = {{ $qty }}</span>
                @endforeach
            @else
                <span style="color: #94a3b8; font-weight: 600;">ไม่มีรายการเคาน์เตอร์ในวันที่เลือก</span>
            @endif
        </div>
    </div>

    <!-- ปุ่มเลือกสถานะ (Status Tabs) -->
    <div class="status-tabs" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
        @php
            $currentStatus = request('status');
            $statusTabs = [
                '' => 'งานค้างส่ง (ไม่รวมเสร็จแล้ว)',
                'all' => 'รวมทั้งหมด',
                'confirmed' => 'ยืนยันแล้ว',
                'assigned' => 'มอบหมายแล้ว',
                'installing' => 'กำลังติดตั้ง',
                'problem' => 'มีปัญหา',
                'completed' => 'เสร็จแล้ว',
                'cancelled' => 'ยกเลิก',
            ];
        @endphp
        @foreach($statusTabs as $val => $lbl)
            @php
                $isActive = ($currentStatus === $val || ($val === '' && $currentStatus === null));
                $bg = '#f3f4f6';
                $color = '#374151';
                $border = '1px solid #e5e7eb';
                if ($isActive) {
                    $bg = 'var(--primary)';
                    $color = '#fff';
                    $border = '1px solid var(--primary)';
                    if ($val === 'completed') {
                        $bg = '#14833b';
                        $border = '1px solid #14833b';
                    } elseif ($val === 'problem') {
                        $bg = '#b42318';
                        $border = '1px solid #b42318';
                    } elseif ($val === '') {
                        $bg = '#0874a6';
                        $border = '1px solid #0874a6';
                    }
                }
            @endphp
            <a class="status-tab" href="{{ route('staff.bookings.index', array_merge(request()->except(['page', 'status']), $val !== '' ? ['status' => $val] : [])) }}"
               style="text-decoration: none; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: bold; background: {{ $bg }}; color: {{ $color }}; border: {{ $border }}; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                @if($val === '')
                    <i class="fa-solid fa-clock-rotate-left"></i>
                @elseif($val === 'all')
                    <i class="fa-solid fa-list"></i>
                @elseif($val === 'confirmed')
                    <i class="fa-solid fa-circle-check"></i>
                @elseif($val === 'assigned')
                    <i class="fa-solid fa-user-check"></i>
                @elseif($val === 'installing')
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                @elseif($val === 'problem')
                    <i class="fa-solid fa-circle-exclamation"></i>
                @elseif($val === 'completed')
                    <i class="fa-solid fa-circle-check"></i>
                @elseif($val === 'cancelled')
                    <i class="fa-solid fa-circle-xmark"></i>
                @endif
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    <form class="filter-card" method="GET" action="{{ route('staff.bookings.index') }}">
        @if(request()->filled('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="filter-grid">
            <div class="field"><label for="search">ค้นหา</label><input id="search" name="search" value="{{ request('search') }}" placeholder="รหัสจอง, ร้านค้า, เบอร์โทร, เลขล็อต..."></div>
            <div class="field"><label for="date">วันที่ใช้งาน</label><input type="date" id="date" name="date" value="{{ $summaryDate }}"></div>
            <div class="field">
                <label for="equipment_type">แยกประเภทงาน</label>
                <select id="equipment_type" name="equipment_type">
                    <option value="">รวมทุกประเภท</option>
                    <option value="tent" @selected(request('equipment_type') === 'tent')>งานเต็นท์</option>
                    <option value="counter" @selected(request('equipment_type') === 'counter')>งานเคาน์เตอร์</option>
                    <option value="other" @selected(request('equipment_type') === 'other')>อุปกรณ์อื่น</option>
                </select>
            </div>
            <div class="actions"><button class="action-btn send" type="submit"><i class="fa-solid fa-filter"></i> กรอง</button><a class="action-btn" href="{{ route('staff.bookings.index', request()->only('status')) }}">ล้าง</a></div>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>วันที่ใช้งาน</th>
                    <th>เวลาที่จอง</th>
                    <th>ชื่อร้านค้า / เบอร์โทร</th>
                    <th>เลขล็อค</th>
                    <th>เต็นท์ (ขนาด)</th>
                    <th>สี</th>
                    <th>เคาน์เตอร์</th>
                    <th>อื่น ๆ</th>
                    <th>รูปภาพ (กล้อง)</th>
                    <th>สถานะรูป</th>
                    <th>แอดมินยืนยัน</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $tasks = $booking->deliveryTasks;
                    $allPhotos = $tasks->flatMap->photos;
                    $lotPhotoCount = $allPhotos->where('photo_type', 'lot_number')->count();
                    $afterPhotoCount = $allPhotos->where('photo_type', 'after')->count();
                    $photoCount = $lotPhotoCount + $afterPhotoCount;
                    $lotApproved = $allPhotos->where('photo_type', 'lot_number')->contains('ocr_status', 'approved');
                    $lotSubmitted = $allPhotos->where('photo_type', 'lot_number')->contains('ocr_status', 'submitted');
                    $isSent = $tasks->contains('status', 'photo_uploaded');
                    $isApproved = $tasks->isNotEmpty() && $tasks->every(fn($task) => $task->status === 'completed');
                    $rejectNote = $tasks->pluck('problem_note')->filter()->first();
                    $hasTasks = $tasks->isNotEmpty() || $booking->tent_size || !empty($booking->tent_items) || $booking->counter_size || !empty($booking->counter_items);
                    $canUseCamera = !$isSent && !$isApproved && $hasTasks && $booking->status !== 'cancelled';
                    $otherEquipment = $tasks->where('task_type', 'other')->pluck('equipment_note')->filter()->implode(' / ');
                    $tentItems = $booking->tentEquipmentItems();
                    $counterItems = $booking->counterEquipmentItems();
                    $previewPhotos = collect();
                    $latestLotPhoto = $allPhotos->where('photo_type', 'lot_number')->sortByDesc('id')->first();
                    if ($latestLotPhoto) {
                        $previewPhotos->push(['photo' => $latestLotPhoto, 'label' => 'LOT', 'alt' => 'รูปเลข LOT']);
                    }
                    foreach ($tasks as $task) {
                        $latestTaskPhoto = $task->photos->where('photo_type', 'after')->sortByDesc('id')->first();
                        if (!$latestTaskPhoto) {
                            continue;
                        }
                        $taskPhotoLabel = match ($task->task_type) {
                            \App\Models\DeliveryTask::TYPE_TENT => 'เต็นท์',
                            \App\Models\DeliveryTask::TYPE_COUNTER => 'เคาน์เตอร์',
                            \App\Models\DeliveryTask::TYPE_OTHER => 'อื่น',
                            default => 'งาน',
                        };
                        $previewPhotos->push([
                            'photo' => $latestTaskPhoto,
                            'label' => $taskPhotoLabel,
                            'alt' => 'รูปงาน'.$taskPhotoLabel,
                        ]);
                    }
                @endphp
                <tr>
                    <td><strong>{{ $booking->use_date->format('d/m/Y') }}</strong></td>
                    <td><strong>{{ $booking->created_at->format('H:i') }} น.</strong></td>
                    <td>
                        <div>
                            <strong>{{ $booking->shop_name }}</strong>
                            <small style="display:block;color:var(--text-muted)">โทร: {{ $booking->customer_phone }}</small>
                        </div>
                    </td>
                    <td><strong style="color:var(--primary-hover)">{{ $booking->lots->pluck('lot_code')->implode(', ') ?: '-' }}</strong></td>
                    
                    <!-- เต็นท์ (ขนาด) -->
                    <td>
                        @if($tentItems)
                            @foreach($tentItems as $item)
                                <div style="font-size:13px; font-weight: bold;">{{ $item['size'] }} <span style="color:#0874a6; font-size:12px;">x{{ $item['quantity'] }}</span></div>
                            @endforeach
                        @else
                            <span class="equipment-empty">-</span>
                        @endif
                    </td>

                    <!-- สี -->
                    <td>
                        @if($tentItems)
                            @foreach($tentItems as $item)
                                <div style="font-size:13px;">{{ $item['color'] ?: '-' }}</div>
                            @endforeach
                        @else
                            <span class="equipment-empty">-</span>
                        @endif
                    </td>

                    <!-- เคาน์เตอร์ -->
                    <td>
                        @if($counterItems)
                            @foreach($counterItems as $item)
                                @php
                                    $displaySize = preg_match('/^\d+\s*ล็อค/u', $item['size'], $matches) ? $matches[0] : $item['size'];
                                @endphp
                                <div style="font-size:13px; font-weight: bold;">{{ $displaySize }} <span style="color:#0874a6; font-size:12px;">x{{ $item['quantity'] }}</span></div>
                            @endforeach
                        @else
                            <span class="equipment-empty">-</span>
                        @endif
                    </td>

                    <td>@if($otherEquipment)<div style="font-size:13px;">{{ $otherEquipment }}</div>@else<span class="equipment-empty">-</span>@endif</td>
                    
                    <!-- รูปภาพ (กล้อง) -->
                    <td>
                        <div style="display:flex; flex-direction:column; gap:5px; align-items: stretch; width: 100%;">
                            @if($previewPhotos->isNotEmpty())
                                <div class="photo-preview-grid" aria-label="รูปที่แนบแล้ว">
                                    @foreach($previewPhotos as $preview)
                                        @php $photo = $preview['photo']; @endphp
                                        <button type="button"
                                                class="photo-preview image-lightbox-trigger"
                                                data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}"
                                                data-lightbox-alt="{{ $preview['alt'] }}">
                                            <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" alt="{{ $preview['alt'] }}">
                                            <span>{{ $preview['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @if($canUseCamera)
                                <a class="action-btn" href="{{ route('staff.bookings.camera',$booking) }}" style="padding:4px 8px; min-height:30px; font-size:12px; justify-content:center;"><i class="fa-solid fa-camera"></i> กล้อง</a>
                                @if(!$lotApproved && !$lotSubmitted)
                                    <form method="POST" action="{{ route('staff.bookings.submit_lot',$booking) }}" style="margin:0; width: 100%;">
                                        @csrf
                                        <button class="action-btn send" type="submit" @disabled($lotPhotoCount===0) onclick="return confirm('ยืนยันส่งรูป LOT ให้ Admin ตรวจสอบ?')" style="padding:4px 8px; min-height:30px; font-size:12px; width: 100%; justify-content:center;">
                                            <i class="fa-solid fa-paper-plane"></i> ส่ง LOT
                                        </button>
                                    </form>
                                @endif
                                @foreach($tasks as $task)
                                    @if($task->status !== 'completed' && $task->status !== 'photo_uploaded')
                                        @php
                                            $taskPhotoCount = $task->photos->where('photo_type', 'after')->count();
                                        @endphp
                                        <form method="POST" action="{{ route('staff.bookings.submit_work',[$booking, $task]) }}" style="margin:0; width: 100%;">
                                            @csrf
                                            <button class="action-btn send" type="submit" @disabled($taskPhotoCount===0) onclick="return confirm('ยืนยันส่งรูปงานติดตั้งสำหรับ {{ $task->typeLabel() }} ให้ Admin ตรวจสอบ?')" style="padding:4px 8px; min-height:30px; font-size:12px; width: 100%; justify-content:center; background: linear-gradient(135deg, #0284c7, #0369a1);">
                                                <i class="fa-solid fa-paper-plane"></i> ส่งงาน {{ $task->typeLabel() }}
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                            @else
                                <button class="action-btn" disabled style="padding:4px 8px; min-height:30px; font-size:12px; justify-content:center;"><i class="fa-solid fa-camera"></i> กล้อง</button>
                            @endif
                        </div>
                    </td>

                    <!-- สถานะรูป -->
                    <td>
                        @if (!$lotApproved)
                            @if ($lotSubmitted)
                                <span class="badge badge-sent" style="padding:3px 7px;font-size:10px;margin-bottom:4px;"><i class="fa-solid fa-paper-plane"></i> LOT: ส่งแล้ว</span>
                            @elseif ($lotPhotoCount)
                                <span class="badge badge-waiting" style="padding:3px 7px;font-size:10px;margin-bottom:4px;"><i class="fa-solid fa-images"></i> LOT: มีรูป</span>
                            @else
                                <span class="badge badge-waiting" style="padding:3px 7px;font-size:10px;margin-bottom:4px;background:#e5e7eb;color:#6b7280;"><i class="fa-solid fa-clock"></i> LOT: รอรูป</span>
                            @endif
                        @else
                            <span class="badge badge-approved" style="padding:3px 7px;font-size:10px;margin-bottom:4px;"><i class="fa-solid fa-circle-check"></i> LOT: อนุมัติ</span>
                        @endif

                        @foreach($tasks as $task)
                            <div style="margin-top:6px;font-size:11px;">
                                <strong>{{ $task->typeLabel() }}:</strong>
                                @if ($task->status === 'completed')
                                    <span class="badge badge-approved" style="padding:2px 6px;font-size:9px;">เสร็จสิ้น</span>
                                @elseif ($task->status === 'photo_uploaded')
                                    <span class="badge badge-sent" style="padding:2px 6px;font-size:9px;">ส่งแล้ว</span>
                                @elseif ($task->problem_note)
                                    <span class="badge badge-rejected" style="padding:2px 6px;font-size:9px;">ตีกลับ</span>
                                @elseif ($task->photos->where('photo_type', 'after')->count() > 0)
                                    <span class="badge badge-waiting" style="padding:2px 6px;font-size:9px;">มีรูป</span>
                                @else
                                    <span class="badge badge-waiting" style="padding:2px 6px;font-size:9px;background:#e5e7eb;color:#6b7280;">รอรูป</span>
                                @endif
                            </div>
                        @endforeach
                    </td>

                    <!-- แอดมินยืนยัน -->
                    <td>
                        @if(!$lotApproved)
                            <div style="font-size:11px; margin-bottom: 4px;">
                                <strong>LOT:</strong>
                                @if($lotSubmitted)
                                    <span style="color:#8a6500;font-weight:bold;">รอตรวจ</span>
                                @else
                                    <span style="color:var(--text-muted);">-</span>
                                @endif
                            </div>
                        @else
                            <div style="font-size:11px; margin-bottom: 4px;">
                                <strong>LOT:</strong> <span style="color:#14833b;font-weight:bold;">Pass</span>
                            </div>
                        @endif
                        @foreach($tasks as $task)
                            <div style="font-size:11px; margin-bottom:4px;">
                                <strong>{{ $task->typeLabel() }}:</strong>
                                @if ($task->status === 'completed')
                                    <span style="color:#14833b;font-weight:bold;">Pass</span>
                                @elseif ($task->problem_note)
                                    <span style="color:#b42318;font-weight:bold;" title="{{ $task->problem_note }}">Reject</span>
                                @elseif ($task->status === 'photo_uploaded')
                                    <span style="color:#8a6500;font-weight:bold;">รอตรวจ</span>
                                @else
                                    <span style="color:var(--text-muted);">-</span>
                                @endif
                            </div>
                        @endforeach
                    </td>
                </tr>
            @empty
                <tr><td colspan="11" class="empty-row" style="text-align:center;padding:40px;color:var(--text-muted)">ไม่พบรายการจอง</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $bookings->links() }}</div>
    @include('components.image-lightbox')
@endsection
