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
    table { width:100%;border-collapse:collapse;min-width:1050px; }
    th,td { text-align:left;padding:15px 14px;border-bottom:1px solid var(--border-cute);vertical-align:middle;font-size:14px; }
    th { background:#fff9fb;font-size:13px;white-space:nowrap; }
    .badge { display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;font-size:12px;font-weight:800;white-space:nowrap; }
    .badge-waiting { background:#fff1c9;color:#8a6500; }.badge-sent { background:#e9ddff;color:#6d28d9; }
    .badge-approved { background:#dff8e8;color:#14833b; }.badge-rejected { background:#ffe1e1;color:#b42318; }
    .actions { display:flex;gap:7px;align-items:center;white-space:nowrap; }
    .action-btn { min-height:39px;padding:0 13px;border-radius:12px;border:2px solid var(--border-cute);background:#fff;color:var(--text-dark);font:inherit;font-size:13px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:7px;cursor:pointer; }
    .action-btn.send { border:0;background:linear-gradient(135deg,var(--primary),var(--primary-hover));color:#fff; }
    .action-btn[disabled] { opacity:.55;cursor:not-allowed; }
    .pagination { margin-top:18px; }
    @media(max-width:800px){.filter-grid{grid-template-columns:1fr}.page-heading{font-size:21px}}
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
            <thead><tr><th>วันที่ใช้งาน</th><th>รหัสจอง</th><th>ชื่อร้านค้า / เบอร์โทร</th><th>ล็อตแผงที่จอง</th><th>รายการอุปกรณ์</th><th>สถานะรูป</th><th>กล้อง / ส่งรูป</th></tr></thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $tasks = $booking->deliveryTasks;
                    $photoCount = $tasks->flatMap->photos->where('photo_type', 'after')->count();
                    $isSent = $tasks->contains('status', 'photo_uploaded');
                    $isApproved = $tasks->isNotEmpty() && $tasks->every(fn($task) => $task->status === 'completed');
                    $rejectNote = $tasks->pluck('problem_note')->filter(fn($note) => str_starts_with((string)$note, 'ตีกลับโดยแอดมิน:'))->first();
                    $canUseCamera = !$isSent && !$isApproved && $tasks->isNotEmpty() && !in_array($booking->status, ['pending_admin','cancelled'], true);
                @endphp
                <tr>
                    <td><strong>{{ $booking->use_date->format('d/m/Y') }}</strong></td>
                    <td><strong>{{ $booking->booking_code }}</strong></td>
                    <td><strong>{{ $booking->shop_name }}</strong><small style="display:block;color:var(--text-muted)">โทร: {{ $booking->customer_phone }}</small></td>
                    <td><strong style="color:var(--primary-hover)">{{ $booking->lots->pluck('lot_code')->implode(', ') ?: '-' }}</strong></td>
                    <td>{{ $booking->equipmentSummary() }}</td>
                    <td>
                        @if($isApproved)<span class="badge badge-approved"><i class="fa-solid fa-circle-check"></i> อนุมัติแล้ว</span>
                        @elseif($isSent)<span class="badge badge-sent"><i class="fa-solid fa-paper-plane"></i> ส่งแล้ว / รออนุมัติ</span>
                        @elseif($rejectNote)<span class="badge badge-rejected"><i class="fa-solid fa-rotate-left"></i> ตีกลับ</span><small style="display:block;margin-top:5px;color:#b42318">{{ str($rejectNote)->after('ตีกลับโดยแอดมิน:')->trim() }}</small>
                        @elseif($photoCount)<span class="badge badge-waiting"><i class="fa-solid fa-images"></i> เพิ่มแล้ว {{ $photoCount }} รูป</span>
                        @else<span class="badge badge-waiting"><i class="fa-solid fa-clock"></i> ยังไม่ได้ส่ง</span>@endif
                    </td>
                    <td><div class="actions">
                        @if($canUseCamera)
                            <a class="action-btn" href="{{ route('staff.bookings.camera',$booking) }}"><i class="fa-solid fa-camera"></i> กล้อง</a>
                            <form method="POST" action="{{ route('staff.bookings.submit',$booking) }}" style="margin:0">@csrf<button class="action-btn send" type="submit" @disabled($photoCount===0) onclick="return confirm('ยืนยันส่งรูปให้แอดมินตรวจสอบ?')"><i class="fa-solid fa-paper-plane"></i> ส่ง</button></form>
                        @else
                            <button class="action-btn" disabled><i class="fa-solid fa-camera"></i> กล้อง</button><button class="action-btn send" disabled><i class="fa-solid fa-paper-plane"></i> ส่ง</button>
                        @endif
                    </div></td>
                </tr>
            @empty<tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">ไม่พบรายการจอง</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $bookings->links() }}</div>
@endsection
