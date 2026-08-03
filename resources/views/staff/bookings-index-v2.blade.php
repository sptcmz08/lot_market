@extends('layouts.staff')

@section('title', 'รายการจองทั้งหมด')

@section('styles')
<style>
    .page-heading { margin: 4px 0 18px; font-size: 24px; }
    .filter-card { background:#fff;border:1px solid var(--border-cute);border-radius:20px;padding:18px;margin-bottom:18px; }
    .filter-grid { display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:end; }
    .field label { display:block;font-size:13px;font-weight:700;margin-bottom:6px; }
    .field input,.field select { width:100%;height:44px;border:2px solid var(--border-cute);border-radius:13px;padding:0 12px;font:inherit;box-sizing:border-box;background:#fff; }
    .table-wrap { overflow:auto;background:#fff;border:1px solid var(--border-cute);border-radius:20px;overscroll-behavior-x:contain;scrollbar-gutter:stable;touch-action:pan-x pan-y; }
    table { width:100%;border-collapse:separate;border-spacing:0;min-width:1260px; }
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
    .photo-action-row { display:flex;align-items:center;justify-content:center;gap:7px;flex-wrap:wrap;min-width:132px; }
    .photo-open-action { min-height:36px;padding:0 10px;border:1px solid var(--border-cute);border-radius:10px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;cursor:pointer;font-size:12px;font-weight:800;white-space:nowrap; }
    .photo-open-action:hover { border-color:var(--primary);color:var(--primary-hover); }
    .pagination { margin-top:18px; }
    .status-filter-form { margin:0;flex:0 0 auto; }
    .status-tab { cursor:pointer;touch-action:manipulation; }
    .work-sheet-card { background:#fff;border:1px solid var(--border-cute);border-radius:20px;margin-bottom:18px;overflow:hidden; }
    .work-sheet-heading { display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 18px;border-bottom:1px solid var(--border-cute);background:#fff9fb; }
    .work-sheet-heading strong { font-size:16px;color:#1e293b; }
    .work-sheet-scroll { overflow-x:auto;-webkit-overflow-scrolling:touch; }
    .work-sheet-table { min-width:840px;width:100%;border-collapse:collapse; }
    .work-sheet-table th,.work-sheet-table td { padding:11px 12px;border-bottom:1px solid #dbe2ea;text-align:left;white-space:nowrap;font-size:13px; }
    .work-sheet-table th { background:#dbeafe;color:#173b6b;font-size:12px;font-weight:900; }
    .work-sheet-table tr:nth-child(even) td { background:#fcfdff; }
    .work-sheet-table td:first-child { font-weight:800; }
    .work-sheet-table .work-shop { font-weight:800;min-width:150px; }
    .work-sheet-table .work-color { color:#b42318;font-weight:900; }
    .work-sheet-table .work-number { color:#dc2626;font-weight:900; }
    .work-sheet-table .work-lots { color:var(--primary-hover);font-weight:900; }
    .work-sheet-empty { padding:25px;text-align:center;color:var(--text-muted);font-weight:700; }
    @media(max-width:900px){
        .page-heading{font-size:18px;margin:3px 0 10px}.summary-card{padding:11px !important;margin-bottom:12px !important;border-radius:14px !important}.summary-header{margin-bottom:9px !important;padding-bottom:7px !important}.summary-header>div{font-size:12px !important}.summary-header strong{font-size:13px !important}.summary-card>div:not(.summary-header){font-size:11px !important;line-height:1.55 !important}.summary-card>div:not(.summary-header)>span:first-child{min-width:78px !important;padding:3px 7px !important;margin-right:7px !important}.status-tabs{gap:5px !important;margin-bottom:12px !important;flex-wrap:nowrap !important;overflow-x:auto;padding:3px 2px 8px;overscroll-behavior-x:contain;-webkit-overflow-scrolling:touch}.status-tab{padding:9px 12px !important;min-height:40px;font-size:11px !important;white-space:nowrap}.filter-grid{grid-template-columns:1fr 1fr auto;gap:7px}.filter-card{padding:9px;margin-bottom:12px;border-radius:13px}.filter-card .field:first-child{grid-column:1/-1}.filter-card .field label{font-size:11px;margin-bottom:3px}.filter-card .field input,.filter-card .field select{height:36px;border-width:1px;border-radius:9px;padding:0 8px;font-size:12px}.filter-card .actions{display:flex;gap:5px}.filter-card .action-btn{justify-content:center;min-height:36px;padding:0 9px;border-width:1px;border-radius:9px;font-size:11px}
        .table-wrap { position: relative; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table{min-width:980px}th,td{padding:8px 6px;font-size:11px}th{font-size:10px}.badge{padding:4px 7px;font-size:10px}.action-btn{min-height:34px;padding:0 8px;font-size:11px;border-radius:9px}
        th.col-action { position: sticky; right: 0; background: #fff9fb; z-index: 5; }
        td.col-action { position: sticky; right: 0; background: #ffffff; z-index: 4; box-shadow: -4px 0 10px rgba(0, 0, 0, 0.08); border-left: 2px solid var(--border-cute) !important; min-width: 145px; }
        tr:nth-child(even) td.col-action { background: #f8fafc; }
        .work-sheet-heading { padding:11px 12px;align-items:flex-start;flex-direction:column;gap:4px; }
        .work-sheet-heading strong { font-size:13px; }.work-sheet-heading span { font-size:11px;color:var(--text-muted); }
        .work-sheet-table { min-width:760px; }.work-sheet-table th,.work-sheet-table td { padding:8px 7px;font-size:11px; }
    }
    @media(max-width:480px){
        table{min-width:920px}th,td{padding:7px 5px;font-size:10px}.filter-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr)}.filter-card .actions{grid-column:1/-1}.filter-card .actions>*{flex:1}.summary-header{align-items:flex-start !important}.summary-header>a{font-size:10px !important}
    }
</style>
@endsection

@section('content')
    <h1 class="page-heading" data-ui-version="staff-photo-actions-v2">รายการจองทั้งหมด</h1>

    <!-- แผงสรุปจำนวนอุปกรณ์ (รูปแบบ Excel) -->
    <div class="summary-card" style="background:#fff; border:1px solid var(--border-cute); border-radius:20px; padding:18px; margin-bottom:20px; font-family: inherit; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
        <!-- หัวข้อระบุวันที่สรุป -->
        <div class="summary-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; flex-wrap: wrap; gap: 8px;">
            <div style="font-weight: 800; font-size: 15px; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-day" style="color: var(--primary); font-size: 18px;"></i>
                @if(!empty($isAllDates))
                    <span>สรุปจำนวนอุปกรณ์: <strong style="color: #0874a6; font-size: 16px;">ย้อนหลังทุกวัน (ไม่จำกัดวันที่)</strong></span>
                @else
                    <span>สรุปจำนวนอุปกรณ์ ประจำวันที่: <strong style="color: #0874a6; font-size: 16px;">{{ \Carbon\Carbon::parse($summaryDate)->format('d/m/Y') }}</strong></span>
                    @if(!empty($isToday))
                        <span style="background: #dcfce7; color: #15803d; border: 1px solid #86efac; font-size: 12px; padding: 2px 9px; border-radius: 999px; font-weight: 800;">(วันปัจจุบัน)</span>
                    @endif
                @endif
            </div>
            @if(empty($isToday))
                <a href="{{ route('staff.bookings.index', request()->except('date')) }}" style="font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 700; background: #e0f2fe; padding: 4px 10px; border-radius: 8px;">
                    <i class="fa-solid fa-rotate-left"></i> กลับมาวันปัจจุบัน
                </a>
            @endif
        </div>

        @if($tentSummary['total'] > 0)
            <div style="font-size: 14px; color: #334155; margin-bottom: 8px; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                <span style="background: #fef08a; color: #854d0e; font-weight: 800; padding: 4px 10px; border-radius: 6px; border: 1px solid #fde047; font-size: 13px;">สรุป เต็นท์ = {{ $tentSummary['total'] }}</span>
                @foreach($tentSummary['sizes'] as $size => $info)
                    <span style="font-weight: 700;">{{ $size }} = {{ $info['total'] }}</span>
                    @if(!empty($info['colors']))
                        @foreach($info['colors'] as $color => $count)
                            <span style="color: #dc2626; font-weight: 700; margin-right: 6px;">สี{{ $color }} = {{ $count }}</span>
                        @endforeach
                    @endif
                @endforeach
            </div>
        @else
            <div style="font-size: 13px; color: #94a3b8; margin-bottom: 8px;">
                <span style="background: #f8fafc; color: #64748b; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 13px;">สรุปเต็นท์</span>
                <span style="margin-left: 8px;">ไม่มีรายการเต็นท์ในวันที่เลือก</span>
            </div>
        @endif

        @if($counterSummary['total'] > 0)
            <div style="font-size: 14px; color: #334155; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                <span style="background: #e0f2fe; color: #075985; font-weight: 800; padding: 4px 10px; border-radius: 6px; border: 1px solid #bae6fd; font-size: 13px;">สรุปเคาน์เตอร์ = {{ $counterSummary['total'] }}</span>
                @foreach($counterSummary['sizes'] as $size => $count)
                    <span style="font-weight: 700;">{{ $size }} = {{ $count }}</span>
                @endforeach
            </div>
        @else
            <div style="font-size: 13px; color: #94a3b8;">
                <span style="background: #f8fafc; color: #64748b; font-weight: 700; padding: 4px 10px; border-radius: 6px; border: 1px solid #e2e8f0; font-size: 13px;">สรุปเคาน์เตอร์</span>
                <span style="margin-left: 8px;">ไม่มีรายการเคาน์เตอร์ในวันที่เลือก</span>
            </div>
        @endif
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
            <form class="status-filter-form" method="GET" action="{{ route('staff.bookings.index') }}">
                @foreach(request()->except(['page', 'status']) as $key => $value)
                    @if(is_scalar($value))<input type="hidden" name="{{ $key }}" value="{{ $value }}">@endif
                @endforeach
                <button class="status-tab" type="submit" name="status" value="{{ $val }}" aria-current="{{ $isActive ? 'page' : 'false' }}"
                        style="padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: bold; background: {{ $bg }}; color: {{ $color }}; border: {{ $border }}; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); font-family:inherit;">
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
                </button>
            </form>
        @endforeach
    </div>

    <!-- ตัวกรองค้นหาและเลือกวันที่ใช้งาน (Custom Date Picker Filter) -->
    <form class="filter-card" method="GET" action="{{ route('staff.bookings.index') }}" id="staffFilterForm">
        @if(request()->filled('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="filter-grid">
            <div class="field">
                <label for="search"><i class="fa-solid fa-magnifying-glass"></i> ค้นหา</label>
                <input id="search" name="search" value="{{ request('search') }}" placeholder="รหัสจอง, ร้านค้า, เบอร์โทร, เลขล็อต...">
            </div>

            <div class="field">
                <label for="date"><i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> เลือกวันที่ต้องการดูข้อมูล</label>
                <input type="date" id="date" name="date" value="{{ !empty($isAllDates) ? '' : $summaryDate }}" onchange="this.form.submit()">
            </div>

            <div class="field">
                <label for="equipment_type"><i class="fa-solid fa-boxes-stacked"></i> แยกประเภทงาน</label>
                <select id="equipment_type" name="equipment_type" onchange="this.form.submit()">
                    <option value="">รวมทุกประเภท</option>
                    <option value="tent" @selected(request('equipment_type') === 'tent')>งานเต็นท์</option>
                    <option value="counter" @selected(request('equipment_type') === 'counter')>งานเคาน์เตอร์</option>
                    <option value="other" @selected(request('equipment_type') === 'other')>อุปกรณ์อื่น</option>
                </select>
            </div>

            <div class="actions">
                <button class="action-btn send" type="submit"><i class="fa-solid fa-filter"></i> กรอง</button>
                <a class="action-btn" href="{{ route('staff.bookings.index', request()->only('status')) }}"><i class="fa-solid fa-rotate-left"></i> ล้าง</a>
            </div>
        </div>

        <!-- ทางลัดเลือกวันที่ด่วน -->
        <div style="display: flex; gap: 6px; margin-top: 10px; flex-wrap: wrap; align-items: center; border-top: 1px dashed var(--border-cute); padding-top: 8px;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-bolt"></i> เลือกด่วน:</span>
            <a href="{{ route('staff.bookings.index', array_merge(request()->except(['page', 'date']), ['date' => $todayDate])) }}"
               style="text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: {{ (!empty($isToday) && empty($isAllDates)) ? '#0874a6' : '#f1f5f9' }}; color: {{ (!empty($isToday) && empty($isAllDates)) ? '#fff' : '#334155' }}; border: 1px solid #cbd5e1;">
                วันนี้ ({{ \Carbon\Carbon::parse($todayDate)->format('d/m/Y') }})
            </a>
            <a href="{{ route('staff.bookings.index', array_merge(request()->except(['page', 'date']), ['date' => \Carbon\Carbon::parse($todayDate)->subDay()->format('Y-m-d')])) }}"
               style="text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: {{ (request('date') === \Carbon\Carbon::parse($todayDate)->subDay()->format('Y-m-d')) ? '#0874a6' : '#f1f5f9' }}; color: {{ (request('date') === \Carbon\Carbon::parse($todayDate)->subDay()->format('Y-m-d')) ? '#fff' : '#334155' }}; border: 1px solid #cbd5e1;">
                เมื่อวาน ({{ \Carbon\Carbon::parse($todayDate)->subDay()->format('d/m/Y') }})
            </a>
            <a href="{{ route('staff.bookings.index', array_merge(request()->except(['page', 'date']), ['date' => 'all', 'status' => 'all'])) }}"
               style="text-decoration: none; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; background: {{ !empty($isAllDates) ? '#0284c7' : '#f1f5f9' }}; color: {{ !empty($isAllDates) ? '#fff' : '#334155' }}; border: 1px solid #cbd5e1;">
                ดูย้อนหลังทั้งหมด (ไม่จำกัดวันที่)
            </a>
        </div>
    </form>

    <!-- คำแนะนำการใช้งานตารางบนมือถือ -->
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 8px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; margin-bottom: 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
        <span><i class="fa-solid fa-hand-pointer"></i> บนมือถือ: เลื่อนตารางไปทางซ้าย-ขวาเพื่อดูข้อมูลเพิ่มเติม</span>
        <i class="fa-solid fa-arrows-left-right"></i>
    </div>

    <!-- ใบงานภาพรวมประจำวัน รูปแบบเดียวกับใบงาน Excel -->
    @php
        $workSheetScope = $equipmentType
            ? 'เฉพาะ'.(match ($equipmentType) {
                'tent' => 'งานเต็นท์',
                'counter' => 'งานเคาน์เตอร์',
                default => 'อุปกรณ์อื่น',
            })
            : 'รวมทุกประเภทงาน';
    @endphp
    <section class="work-sheet-card" aria-labelledby="work-sheet-title">
        <div class="work-sheet-heading">
            <strong id="work-sheet-title"><i class="fa-solid fa-clipboard-list" style="color:var(--primary);"></i> ใบงานภาพรวมประจำวัน</strong>
            <span>{{ !empty($isAllDates) ? 'ย้อนหลังทุกวัน' : 'ประจำวันที่ '.\Carbon\Carbon::parse($summaryDate)->format('d/m/Y') }} · {{ $workSheetScope }}</span>
        </div>
        <div class="work-sheet-scroll">
            <table class="work-sheet-table">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>ชื่อร้าน</th>
                        <th>สี / No.</th>
                        <th>อุปกรณ์</th>
                        <th>โซน</th>
                        <th>เลขล็อค</th>
                        <th>ลำดับ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workRows as $row)
                        <tr>
                            <td>{{ $row['date']?->format('d/m/Y') }}</td>
                            <td class="work-shop">{{ $row['shop'] }}</td>
                            <td class="{{ $row['is_number'] ? 'work-number' : 'work-color' }}">
                                {{ $row['is_number'] ? 'No.'.($row['color_or_number'] ?: '-') : ($row['color_or_number'] ?: '-') }}
                            </td>
                            <td>{{ $row['equipment'] }}</td>
                            <td>{{ $row['zone'] }}</td>
                            <td class="work-lots">{{ $row['lots'] }}</td>
                            <td>{{ $row['sequence'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="work-sheet-empty">ไม่พบงานในวันที่เลือก</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>วันที่ใช้งาน</th>
                    <th>เวลาที่จอง</th>
                    <th>ชื่อร้านค้า / เบอร์โทร</th>
                    <th>เลขล็อค</th>
                    @if(!$equipmentType || $equipmentType === 'tent')<th>เต็นท์ (ขนาด)</th><th>สี</th>@endif
                    @if(!$equipmentType || $equipmentType === 'counter')<th>เคาน์เตอร์</th>@endif
                    @if(!$equipmentType || $equipmentType === 'other')<th>อื่น ๆ</th>@endif
                    <th class="col-action">เพิ่มรูปงาน</th>
                    <th>สถานะรูป</th>
                    <th>แอดมินยืนยัน</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $tasks = $booking->deliveryTasks;
                    $visibleTasks = $equipmentType ? $tasks->where('task_type', $equipmentType) : $tasks;
                    $hasTasks = $tasks->isNotEmpty() || $booking->tent_size || !empty($booking->tent_items) || $booking->counter_size || !empty($booking->counter_items);
                    $canUseCamera = $hasTasks && $booking->status !== 'cancelled' && $tasks->contains(fn($task) => !in_array($task->status, ['photo_uploaded', 'completed'], true));
                    $otherEquipment = $visibleTasks->where('task_type', 'other')->pluck('equipment_note')->filter()->implode(' / ');
                    $tentItems = $equipmentType === 'counter' || $equipmentType === 'other' ? [] : $booking->tentEquipmentItems();
                    $counterItems = $equipmentType === 'tent' || $equipmentType === 'other' ? [] : $booking->counterEquipmentItems();
                    $previewPhotos = collect();
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
                    
                    @if(!$equipmentType || $equipmentType === 'tent')
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
                    @endif

                    @if(!$equipmentType || $equipmentType === 'counter')
                    <td>
                        @if($counterItems)
                            @foreach($counterItems as $item)
                                @php
                                    $displaySize = preg_match('/^\d+\s*ล็อค/u', $item['size'], $matches) ? $matches[0] : $item['size'];
                                @endphp
                                <div style="font-size:13px; font-weight: bold;">{{ $displaySize }} <span style="color:#dc2626; font-size:12px;">No.{{ $item['number'] ?? '-' }}</span></div>
                            @endforeach
                        @else
                            <span class="equipment-empty">-</span>
                        @endif
                    </td>
                    @endif

                    @if(!$equipmentType || $equipmentType === 'other')
                    <td>@if($otherEquipment)<div style="font-size:13px;">{{ $otherEquipment }}</div>@else<span class="equipment-empty">-</span>@endif</td>
                    @endif
                    
                    <!-- รูปภาพ (กล้อง) -->
                    <td class="col-action">
                        <div class="photo-action-row">
                            @if($previewPhotos->isNotEmpty())
                                @foreach($previewPhotos as $preview)
                                    @php $photo = $preview['photo']; @endphp
                                    <button type="button" class="photo-preview image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="{{ $preview['alt'] }}" title="{{ $preview['alt'] }}" aria-label="{{ $preview['alt'] }}">
                                        <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" alt="{{ $preview['alt'] }}"><span>{{ $preview['label'] }}</span>
                                    </button>
                                @endforeach
                            @endif
                            @if($canUseCamera)
                                <a class="photo-open-action" href="{{ route('staff.bookings.camera',$booking) }}" title="ถ่ายหรือแนบรูปงาน"><span>ถ่าย/แนบรูป</span></a>
                            @elseif($previewPhotos->isNotEmpty())
                                <span style="font-size:11px;color:var(--text-muted);font-weight:700">ดูรูปด้านซ้าย</span>
                            @endif
                        </div>
                    </td>

                    <!-- สถานะรูป -->
                    <td>
                        @foreach($visibleTasks as $task)
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
                        @foreach($visibleTasks as $task)
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
                <tr><td colspan="{{ 4 + ($equipmentType ? 1 : 4) + 3 }}" class="empty-row" style="text-align:center;padding:40px;color:var(--text-muted)">ไม่พบรายการจอง</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $bookings->links() }}</div>
    @include('components.image-lightbox')
@endsection
