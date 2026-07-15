@extends('layouts.admin')

@section('title', 'จัดการคำสั่งจองอุปกรณ์')
@section('page_title', 'รายการจองทั้งหมด')

@section('content')
    @if ($errors->any())
        <div class="alert-cute alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <div class="cute-card">
        <!-- Filter Form -->
        <form action="{{ route('admin.bookings.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div class="cute-input-group" style="margin-bottom: 0; flex: 2; min-width: 200px;">
                <label class="cute-label" for="search">ค้นหา</label>
                <input type="text" id="search" name="search" class="cute-input" value="{{ request('search') }}" placeholder="ค้นหารหัสจอง, ร้านค้า, เบอร์โทร, เลขล็อต...">
            </div>
            
            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 130px;">
                <label class="cute-label" for="status">สถานะ</label>
                <select id="status" name="status" class="cute-select">
                    <option value="">ทั้งหมด</option>
                    <option value="pending_admin" {{ request('status') == 'pending_admin' ? 'selected' : '' }}>รอยืนยัน</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว/รอส่ง</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>มอบหมายพนักงาน</option>
                    <option value="installing" {{ request('status') == 'installing' ? 'selected' : '' }}>กำลังติดตั้ง</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>ติดตั้งสำเร็จ</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    <option value="problem" {{ request('status') == 'problem' ? 'selected' : '' }}>มีปัญหา</option>
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
                <label class="cute-label" for="date">วันที่ใช้งาน</label>
                <input type="date" id="date" name="date" class="cute-input" value="{{ request('date') }}">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-filter"></i> กรองข้อมูล
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-arrow-rotate-left"></i> ล้าง
                </a>
            </div>
        </form>
    </div>

    <div class="cute-table-container">
        <table class="cute-table">
            <thead>
                <tr>
                    <th>วันที่ใช้งาน</th>
                    <th>รหัสจอง</th>
                    <th>ชื่อร้านค้า / เบอร์โทร</th>
                    <th>ล็อตแผงที่จอง</th>
                    <th>รายการอุปกรณ์</th>
                    <th>ผู้รับผิดชอบ</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr>
                        <td>
                            <strong>{{ $booking->use_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <strong>{{ $booking->booking_code }}</strong>
                        </td>
                        <td>
                            <div><strong>{{ $booking->shop_name }}</strong></div>
                            <small style="color: var(--text-muted);">โทร: {{ $booking->customer_phone }}</small>
                        </td>
                        <td>
                            <strong style="color: var(--primary-hover);">{{ $booking->lots->pluck('lot_code')->implode(', ') }}</strong>
                        </td>
                        <td>
                            <div>{{ $booking->equipmentSummary() }}</div>
                        </td>
                        <td>
                            @if ($booking->deliveryTasks->whereNotNull('staff_id')->isNotEmpty())
                                @foreach ($booking->deliveryTasks->whereNotNull('staff_id') as $task)
                                    <small style="display:block;"><strong>{{ $task->typeLabel() }}:</strong> {{ $task->staff?->name }} ({{ $task->statusLabel() }})</small>
                                @endforeach
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ยังไม่ระบุ</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . $booking->status;
                                $statusName = 'รอยืนยัน';
                                switch($booking->status) {
                                    case 'pending_admin': $statusName = 'รอยืนยัน'; break;
                                    case 'confirmed': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                    case 'assigned': $statusName = 'มอบหมายงาน'; break;
                                    case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                    case 'completed': $statusName = 'ติดตั้งสำเร็จ'; break;
                                    case 'cancelled': $statusName = 'ยกเลิก'; break;
                                    case 'problem': $statusName = 'มีปัญหา'; break;
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="เปิดดูรายละเอียด">
                                    <i class="fa-solid fa-eye"></i> ดู
                                </a>
                                @if (!$booking->payment_slip_path)
                                    <form action="{{ route('admin.bookings.payment_slip', $booking) }}" method="POST" enctype="multipart/form-data" style="margin:0;">
                                        @csrf
                                        <label class="btn-secondary" style="padding:6px 12px;font-size:13px;border-radius:10px;cursor:pointer;margin:0;" title="แนบรูปสลิปการชำระเงิน">
                                            <i class="fa-solid fa-receipt"></i> แนบสลิป
                                            <input type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" required hidden onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif
                                @if($booking->status === 'pending_admin')
                                    @if ($booking->payment_slip_path || $booking->collect_front_store)
                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="ยืนยันการจอง">
                                                <i class="fa-solid fa-check"></i> ยืนยัน
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:13px;border-radius:10px;opacity:.6;cursor:not-allowed;" disabled title="กรุณาแนบสลิปก่อนยืนยัน">
                                            <i class="fa-solid fa-lock"></i> แนบสลิปก่อน
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-box-open" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                            ไม่พบข้อมูลการจองในระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-cute">
        {{ $bookings->links() }}
    </div>
@endsection
