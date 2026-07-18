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
    table { width:100%;border-collapse:collapse;min-width:1380px; }
    th,td { text-align:left;padding:15px 14px;border-bottom:1px solid var(--border-cute);vertical-align:middle;font-size:14px; }
    th { background:#fff9fb;font-size:13px;white-space:nowrap; }
    .badge { display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;font-size:12px;font-weight:800;white-space:nowrap; }
    .badge-waiting { background:#fff1c9;color:#8a6500; }.badge-sent { background:#e9ddff;color:#6d28d9; }
    .badge-approved { background:#dff8e8;color:#14833b; }.badge-rejected { background:#ffe1e1;color:#b42318; }
    .actions { display:flex;gap:7px;align-items:center;white-space:nowrap; }
    .action-btn { min-height:39px;padding:0 13px;border-radius:12px;border:2px solid var(--border-cute);background:#fff;color:var(--text-dark);font:inherit;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:7px;cursor:pointer; }
    .action-btn.send { border:0;background:linear-gradient(135deg,var(--primary),var(--primary-hover));color:#fff; }
    .action-btn[disabled] { opacity:.55;cursor:not-allowed; }
    .equipment-detail { display:grid;gap:8px;min-width:165px; }
    .equipment-item { display:grid;gap:6px;padding:9px 10px;border:1px solid #dceff8;border-radius:13px;background:#f7fcff; }
    .equipment-total { display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:8px 10px;border-radius:11px;background:#0874a6;color:#fff;font-size:12px;font-weight:900; }
    .equipment-total b { font-size:21px;line-height:1; }
    .equipment-item strong { line-height:1.45; }
    .equipment-quantity { display:flex;align-items:baseline;justify-content:space-between;gap:8px;padding:7px 10px;border-radius:10px;background:#dff4ff;color:#075d87;font-size:12px;font-weight:900; }
    .equipment-quantity b { font-size:20px;line-height:1;color:#064f73; }
    .equipment-empty { color:var(--text-muted);font-weight:700; }
    .pagination { margin-top:18px; }
    @media(max-width:900px){
        .filter-grid{grid-template-columns:1fr}.page-heading{font-size:21px}.filter-card{padding:14px}.filter-card .actions{display:grid;grid-template-columns:1fr 1fr}.filter-card .action-btn{justify-content:center}
        .table-wrap{overflow:visible;background:transparent;border:0;border-radius:0}table{display:block;min-width:0}thead{display:none}tbody{display:grid;gap:14px}tr{display:block;background:#fff;border:1px solid var(--border-cute);border-radius:18px;padding:8px 14px;box-shadow:0 5px 16px rgba(47,47,55,.04)}td{display:grid;grid-template-columns:105px minmax(0,1fr);gap:10px;align-items:start;padding:10px 0;border-bottom:1px dashed var(--border-cute);font-size:13px;overflow-wrap:anywhere}td::before{content:attr(data-label);font-weight:900;color:var(--text-muted);font-size:12px}td:last-child{border-bottom:0}.equipment-detail{min-width:0}.actions{white-space:normal;flex-wrap:wrap}.action-btn{flex:1;justify-content:center;min-width:100px}.badge{white-space:normal}.empty-row{display:block;text-align:center;padding:30px 10px}.empty-row::before{display:none}
    }
    @media(max-width:390px){td{grid-template-columns:88px minmax(0,1fr)}.action-btn{min-width:0;padding:0 9px;font-size:12px}}
</style>
@endsection

@section('content')
    <h1 class="page-heading">รายการจองทั้งหมด</h1>

    <form class="filter-card" method="GET" action="{{ route('staff.bookings.index') }}">
        <div class="filter-grid">
            <div class="field"><label for="search">ค้นหา</label><input id="search" name="search" value="{{ request('search') }}" placeholder="รหัสจอง, ร้านค้า, เบอร์โทร, เลขล็อต..."></div>
            <div class="field"><label for="status">สถานะการจอง</label><select id="status" name="status"><option value="">ทั้งหมด</option>@foreach(['pending_admin'=>'รอยืนยัน','confirmed'=>'ยืนยันแล้ว','assigned'=>'มอบหมายแล้ว','installing'=>'กำลังติดตั้ง','completed'=>'เสร็จแล้ว','cancelled'=>'ยกเลิก','problem'=>'มีปัญหา'] as $value=>$label)<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>@endforeach</select></div>
            <div class="field"><label for="date">วันที่ใช้งาน</label><input type="date" id="date" name="date" value="{{ request('date') }}"></div>
            <div class="actions"><button class="action-btn send" type="submit"><i class="fa-solid fa-filter"></i> กรอง</button><a class="action-btn" href="{{ route('staff.bookings.index') }}">ล้าง</a></div>
        </div>
    </form>

    <div class="table-wrap">
        <table>
            <thead><tr><th>วันที่ใช้งาน</th><th>รหัสจอง</th><th>ชื่อร้านค้า / เบอร์โทร</th><th>ล็อตแผงที่จอง</th><th>เต็นท์</th><th>เคาน์เตอร์</th><th>อื่น ๆ</th><th>สถานะรูป</th><th>กล้อง / ส่งรูป</th></tr></thead>
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
                    $canUseCamera = !$isSent && !$isApproved && $tasks->isNotEmpty() && !in_array($booking->status, ['pending_admin','cancelled'], true);
                    $otherEquipment = $tasks->where('task_type', 'other')->pluck('equipment_note')->filter()->implode(' / ');
                    $tentItems = $booking->tentEquipmentItems();
                    $counterItems = $booking->counterEquipmentItems();
                @endphp
                <tr>
                    <td data-label="วันที่ใช้งาน"><strong>{{ $booking->use_date->format('d/m/Y') }}</strong></td>
                    <td data-label="รหัสจอง"><strong>{{ $booking->booking_code }}</strong></td>
                    <td data-label="ร้านค้า"><div><strong>{{ $booking->shop_name }}</strong><small style="display:block;color:var(--text-muted)">โทร: {{ $booking->customer_phone }}</small></div></td>
                    <td data-label="ล็อตแผง"><strong style="color:var(--primary-hover)">{{ $booking->lots->pluck('lot_code')->implode(', ') ?: '-' }}</strong></td>
                    <td data-label="เต็นท์">
                        @if($tentItems)
                            <div class="equipment-detail"><div class="equipment-total"><span>รวมที่ต้องเตรียม</span><b>{{ collect($tentItems)->sum('quantity') }} หลัง</b></div>@foreach($tentItems as $item)<div class="equipment-item"><strong>ขนาด {{ $item['size'] }}{{ !empty($item['color']) ? ' · สี'.$item['color'] : '' }}</strong><span class="equipment-quantity"><span>จำนวนขนาดนี้</span><b>{{ $item['quantity'] }} หลัง</b></span></div>@endforeach</div>
                        @else<span class="equipment-empty">ไม่จอง</span>@endif
                    </td>
                    <td data-label="เคาน์เตอร์">
                        @if($counterItems)
                            <div class="equipment-detail"><div class="equipment-total"><span>รวมที่ต้องเตรียม</span><b>{{ collect($counterItems)->sum('quantity') }} ชุด</b></div>@foreach($counterItems as $item)<div class="equipment-item"><strong>ขนาด {{ $item['size'] }}{{ !empty($item['color']) ? ' · สี'.$item['color'] : '' }}</strong><span class="equipment-quantity"><span>จำนวนขนาดนี้</span><b>{{ $item['quantity'] }} ชุด</b></span></div>@endforeach</div>
                        @else<span class="equipment-empty">ไม่จอง</span>@endif
                    </td>
                    <td data-label="อื่น ๆ">@if($otherEquipment)<div class="equipment-detail"><strong>{{ $otherEquipment }}</strong></div>@else<span class="equipment-empty">ไม่มี</span>@endif</td>
                    <td data-label="สถานะรูป">
                        @if($isApproved)<span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> อนุมัติแล้ว</span>
                        @elseif($isSent)<span class="badge badge-sent"><i class="fa-solid fa-paper-plane"></i> ส่งรูปงาน / รออนุมัติ</span>
                        @elseif($rejectNote)<span class="badge badge-rejected"><i class="fa-solid fa-rotate-left"></i> ตีกลับ / รอส่งใหม่</span><small style="display:block;margin-top:5px;color:#b42318">{{ str($rejectNote)->after(':')->trim() }}</small>
                        @elseif($lotSubmitted)<span class="badge badge-sent"><i class="fa-solid fa-paper-plane"></i> ส่งรูป LOT / รออนุมัติ</span>
                        @elseif($lotApproved && $afterPhotoCount)<span class="badge badge-waiting"><i class="fa-solid fa-images"></i> LOT ผ่าน / เพิ่มรูปงานแล้ว</span>
                        @elseif($lotApproved)<span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> LOT ผ่าน / รอรูปงาน</span>
                        @elseif($lotPhotoCount)<span class="badge badge-waiting"><i class="fa-solid fa-images"></i> เพิ่มรูป LOT แล้ว / ยังไม่ส่ง</span>
                        @else<span class="badge badge-waiting"><i class="fa-solid fa-clock"></i> รอรูป LOT</span>@endif
                    </td>
                    <td data-label="กล้อง / ส่งรูป"><div class="actions">
                        @if($canUseCamera)
                            <a class="action-btn" href="{{ route('staff.bookings.camera',$booking) }}"><i class="fa-solid fa-camera"></i> กล้อง</a>
                            @if(!$lotApproved)
                                <form method="POST" action="{{ route('staff.bookings.submit_lot',$booking) }}" style="margin:0">@csrf<button class="action-btn send" type="submit" @disabled($lotPhotoCount===0 || $lotSubmitted) onclick="return confirm('ยืนยันส่งรูป LOT ให้ Admin ตรวจสอบ?')"><i class="fa-solid fa-paper-plane"></i> ส่ง LOT</button></form>
                            @else
                                <form method="POST" action="{{ route('staff.bookings.submit_work',$booking) }}" style="margin:0">@csrf<button class="action-btn send" type="submit" @disabled($afterPhotoCount===0) onclick="return confirm('ยืนยันส่งรูปงานติดตั้งให้ Admin ตรวจสอบ?')"><i class="fa-solid fa-paper-plane"></i> ส่งรูปงาน</button></form>
                            @endif
                        @else
                            <button class="action-btn" disabled><i class="fa-solid fa-camera"></i> กล้อง</button><button class="action-btn send" disabled><i class="fa-solid fa-paper-plane"></i> ส่ง</button>
                        @endif
                    </div></td>
                </tr>
            @empty<tr><td colspan="9" class="empty-row" style="text-align:center;padding:40px;color:var(--text-muted)">ไม่พบรายการจอง</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $bookings->links() }}</div>
@endsection
