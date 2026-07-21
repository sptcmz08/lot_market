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
    .pagination { margin-top:18px; }
    @media(max-width:900px){
        .filter-grid{grid-template-columns:1fr}.page-heading{font-size:21px}.filter-card{padding:14px}.filter-card .actions{display:grid;grid-template-columns:1fr 1fr}.filter-card .action-btn{justify-content:center}
        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 20px; border: 1px solid var(--border-cute); background: #fff; }
    }
</style>
@endsection

@section('content')
    <h1 class="page-heading">รายการจองทั้งหมด</h1>

    <!-- แผงสรุปจำนวนอุปกรณ์ -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:16px; margin-bottom:20px;">
        <!-- สรุปเต็นท์ -->
        <div style="background:#fff; border:1px solid var(--border-cute); border-radius:20px; padding:18px; box-shadow:0 4px 12px rgba(0,0,0,0.02);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px; border-bottom: 2px solid #fff3f6; padding-bottom:8px;">
                <span style="font-size:24px;">⛺</span>
                <div>
                    <div style="font-size:13px; color:var(--text-muted); font-weight:700;">ยอดจองเต็นท์รวม</div>
                    <div style="font-size:22px; font-weight:900; color:#b4235a;">{{ $tentSummary['total'] }} หลัง</div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse($tentSummary['sizes'] as $size => $data)
                    <div style="font-size:13px; background:#fafafa; border-radius:12px; padding:8px 12px; border:1px solid var(--border-cute);">
                        <div style="display:flex; justify-content:space-between; font-weight:bold; color:var(--text-dark);">
                            <span>ขนาด {{ $size }}</span>
                            <span style="color:#0874a6;">{{ $data['total'] }} หลัง</span>
                        </div>
                        @if(!empty($data['colors']))
                            <div style="margin-top:6px; display:flex; gap:8px; flex-wrap:wrap; font-size:11px; color:var(--text-muted);">
                                @foreach($data['colors'] as $color => $qty)
                                    <span style="background:#fff; border:1px solid var(--border-cute); padding:2px 6px; border-radius:6px;">สี{{ $color }}: <b>{{ $qty }}</b></span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div style="color:var(--text-muted); font-size:13px; text-align:center; padding:10px 0;">ไม่มีข้อมูลจองเต็นท์</div>
                @endforelse
            </div>
        </div>

        <!-- สรุปเคาน์เตอร์ -->
        <div style="background:#fff; border:1px solid var(--border-cute); border-radius:20px; padding:18px; box-shadow:0 4px 12px rgba(0,0,0,0.02);">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px; border-bottom: 2px solid #f0f9ff; padding-bottom:8px;">
                <span style="font-size:24px;">🪟</span>
                <div>
                    <div style="font-size:13px; color:var(--text-muted); font-weight:700;">ยอดจองเคาน์เตอร์รวม</div>
                    <div style="font-size:22px; font-weight:900; color:#0874a6;">{{ $counterSummary['total'] }} ชุด</div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @forelse($counterSummary['sizes'] as $size => $qty)
                    <div style="font-size:13px; background:#fafafa; border-radius:12px; padding:8px 12px; border:1px solid var(--border-cute); display:flex; justify-content:space-between; font-weight:bold; color:var(--text-dark);">
                        <span>ขนาด {{ $size }}</span>
                        <span style="color:#0874a6;">{{ $qty }} ชุด</span>
                    </div>
                @empty
                    <div style="color:var(--text-muted); font-size:13px; text-align:center; padding:10px 0;">ไม่มีข้อมูลจองเคาน์เตอร์</div>
                @endforelse
            </div>
        </div>
    </div>

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
            <thead>
                <tr>
                    <th>วันที่ใช้งาน</th>
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
                    $canUseCamera = !$isSent && !$isApproved && $tasks->isNotEmpty() && !in_array($booking->status, ['pending_admin','cancelled'], true);
                    $otherEquipment = $tasks->where('task_type', 'other')->pluck('equipment_note')->filter()->implode(' / ');
                    $tentItems = $booking->tentEquipmentItems();
                    $counterItems = $booking->counterEquipmentItems();
                @endphp
                <tr>
                    <td><strong>{{ $booking->use_date->format('d/m/Y') }}</strong></td>
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
                                <div style="font-size:13px; font-weight: bold;">{{ $item['size'] }} <span style="color:#0874a6; font-size:12px;">x{{ $item['quantity'] }}</span></div>
                            @endforeach
                        @else
                            <span class="equipment-empty">-</span>
                        @endif
                    </td>

                    <td>@if($otherEquipment)<div style="font-size:13px;">{{ $otherEquipment }}</div>@else<span class="equipment-empty">-</span>@endif</td>
                    
                    <!-- รูปภาพ (กล้อง) -->
                    <td>
                        <div style="display:flex; flex-direction:column; gap:5px; align-items: stretch; width: 100%;">
                            @if($canUseCamera)
                                <a class="action-btn" href="{{ route('staff.bookings.camera',$booking) }}" style="padding:4px 8px; min-height:30px; font-size:12px; justify-content:center;"><i class="fa-solid fa-camera"></i> กล้อง</a>
                                @if(!$lotApproved)
                                    <form method="POST" action="{{ route('staff.bookings.submit_lot',$booking) }}" style="margin:0; width: 100%;">
                                        @csrf
                                        <button class="action-btn send" type="submit" @disabled($lotPhotoCount===0 || $lotSubmitted) onclick="return confirm('ยืนยันส่งรูป LOT ให้ Admin ตรวจสอบ?')" style="padding:4px 8px; min-height:30px; font-size:12px; width: 100%; justify-content:center;">
                                            <i class="fa-solid fa-paper-plane"></i> ส่ง LOT
                                        </button>
                                    </form>
                                @else
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
                                @endif
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
                <tr><td colspan="10" class="empty-row" style="text-align:center;padding:40px;color:var(--text-muted)">ไม่พบรายการจอง</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $bookings->links() }}</div>
@endsection
