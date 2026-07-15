@extends('layouts.admin')

@section('title', 'แผงควบคุมระบบ')
@section('page_title', 'แผงควบคุมหลัก')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    @media (max-width: 991px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    
    @media (max-width: 575px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat-card {
        border-radius: 20px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100px;
        color: white;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .stat-card-yellow { background: linear-gradient(135deg, #FFD166, #F4B929); color: #664d03 !important; }
    .stat-card-blue { background: linear-gradient(135deg, #8BD3DD, #5FBDC9); color: #055160 !important; }
    .stat-card-purple { background: linear-gradient(135deg, #A78BFA, #8B5CF6); }
    .stat-card-green { background: linear-gradient(135deg, #4ECDC4, #2CBFB3); }
    .stat-card-orange { background: linear-gradient(135deg, #FF9F1C, #F38500); }

    .stat-value {
        font-size: 36px;
        font-weight: 800;
        line-height: 1;
        margin: 10px 0 0 0;
    }

    .stat-label {
        font-size: 14px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .daily-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .daily-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 22px;
    }

    .daily-summary-item {
        min-width: 0;
        padding: 16px;
        border: 1px solid var(--border-cute);
        border-left: 4px solid var(--primary);
        border-radius: 12px;
        background: var(--bg-card);
    }

    .daily-summary-item:nth-child(2) { border-left-color: #22c55e; }
    .daily-summary-item:nth-child(3) { border-left-color: #f59e0b; }
    .daily-summary-item:nth-child(4) { border-left-color: #38bdf8; }

    .daily-summary-label {
        display: block;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .daily-summary-value {
        display: block;
        margin-top: 5px;
        color: var(--text-dark);
        font-size: 25px;
        font-weight: 900;
    }

    .front-store-table-wrap {
        overflow-x: auto;
    }

    .front-store-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
    }

    .front-store-amount-form {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .front-store-amount-form .cute-input {
        width: 125px;
        min-height: 38px;
        padding: 7px 10px;
    }

    @media (max-width: 767px) {
        .daily-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .daily-toolbar form,
        .daily-toolbar .cute-input-group,
        .daily-toolbar .btn-primary {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')

    @if ($errors->any())
        <div class="alert-cute alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    <!-- Status Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card stat-card-yellow">
            <span class="stat-label"><i class="fa-solid fa-spinner"></i> จองใหม่</span>
            <span class="stat-value">{{ $stats['pending'] }}</span>
        </div>
        <div class="stat-card stat-card-blue">
            <span class="stat-label"><i class="fa-solid fa-calendar-check"></i> ยืนยัน / รอส่ง</span>
            <span class="stat-value">{{ $stats['confirmed'] + $stats['assigned'] }}</span>
        </div>
        <div class="stat-card stat-card-purple">
            <span class="stat-label"><i class="fa-solid fa-hammer"></i> กำลังติดตั้ง</span>
            <span class="stat-value">{{ $stats['installing'] }}</span>
        </div>
        <div class="stat-card stat-card-green">
            <span class="stat-label"><i class="fa-solid fa-circle-check"></i> ติดตั้งเสร็จแล้ว</span>
            <span class="stat-value">{{ $stats['completed'] }}</span>
        </div>
        <div class="stat-card stat-card-orange">
            <span class="stat-label"><i class="fa-solid fa-triangle-exclamation"></i> มีปัญหา</span>
            <span class="stat-value">{{ $stats['problem'] }}</span>
        </div>
    </div>

    <div class="cute-card">
        <div class="daily-toolbar">
            <div>
                <h3 class="cute-card-title" style="margin-bottom:4px;">
                    <i class="fa-solid fa-coins"></i> สรุปยอดรายวัน
                </h3>
                <span style="color:var(--text-muted);font-size:13px;">ดูยอดงานและรายการเก็บเงินหน้าร้านตามวันที่ใช้งาน</span>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;">
                <form action="{{ route('admin.dashboard') }}" method="GET" style="display:flex;gap:8px;align-items:flex-end;">
                    <div class="cute-input-group" style="margin:0;">
                        <label class="cute-label" for="summary_date">วันที่</label>
                        <input type="date" id="summary_date" name="date" class="cute-input" value="{{ $selectedDate }}">
                    </div>
                    <button type="submit" class="btn-primary" style="min-height:44px;padding:10px 16px;">
                        <i class="fa-solid fa-magnifying-glass"></i> แสดงยอด
                    </button>
                </form>
                <a href="{{ route('admin.dashboard.front_store_export', ['date' => $selectedDate]) }}" class="btn-secondary" style="min-height:44px;padding:10px 16px;">
                    <i class="fa-solid fa-file-excel"></i> ส่งออก Excel
                </a>
            </div>
        </div>

        <div class="daily-summary-grid">
            <div class="daily-summary-item">
                <span class="daily-summary-label">รายการจองวันนี้</span>
                <span class="daily-summary-value">{{ number_format($dailySummary['bookings']) }} งาน</span>
            </div>
            <div class="daily-summary-item">
                <span class="daily-summary-label">จำนวน LOT วันนี้</span>
                <span class="daily-summary-value">{{ number_format($dailySummary['lots']) }} LOT</span>
            </div>
            <div class="daily-summary-item">
                <span class="daily-summary-label">รอเก็บเงินหน้าร้าน</span>
                <span class="daily-summary-value">{{ number_format($dailySummary['front_store_pending']) }} รายการ</span>
            </div>
            <div class="daily-summary-item">
                <span class="daily-summary-label">ยอดเก็บหน้าร้านแล้ว</span>
                <span class="daily-summary-value">{{ number_format($dailySummary['front_store_collected_amount'], 2) }} บาท</span>
            </div>
        </div>
    </div>

    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-cash-register"></i> รายการเก็บเงินหน้าร้าน
        </h3>
        <p style="margin:-8px 0 16px;color:var(--text-muted);font-size:13px;">
            แสดงตามวันที่ใช้งาน {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }} และระบุเลข LOT ของแต่ละร้าน
        </p>

        @if ($frontStoreBookings->isEmpty())
            <div style="padding:28px;text-align:center;color:var(--text-muted);">
                <i class="fa-solid fa-receipt" style="display:block;margin-bottom:8px;font-size:30px;color:var(--border-cute);"></i>
                ไม่มีรายการที่เลือกเก็บเงินหน้าร้านในวันนี้
            </div>
        @else
            <div class="front-store-table-wrap">
                <table class="cute-table front-store-table">
                    <thead>
                        <tr>
                            <th>เลข LOT</th>
                            <th>ร้านค้า / เบอร์โทร</th>
                            <th>รายการอุปกรณ์</th>
                            <th>สถานะเก็บเงิน</th>
                            <th>ยอดเงินหน้าร้าน</th>
                            <th>รายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($frontStoreBookings as $booking)
                            <tr>
                                <td>
                                    <strong style="color:var(--primary-hover);">{{ $booking->lots->pluck('lot_code')->implode(', ') }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $booking->shop_name }}</strong>
                                    <small style="display:block;color:var(--text-muted);">{{ $booking->customer_phone }}</small>
                                </td>
                                <td>{{ $booking->equipmentSummary() }}</td>
                                <td>
                                    @if ($booking->front_store_collected_at)
                                        <span class="status-badge status-completed">เก็บแล้ว</span>
                                        <small style="display:block;margin-top:5px;color:var(--text-muted);">
                                            {{ $booking->front_store_collected_at->format('d/m/Y H:i') }} น.
                                            @if ($booking->frontStoreCollectedBy)
                                                โดย {{ $booking->frontStoreCollectedBy->name }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="status-badge status-pending_admin">รอเก็บ</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.dashboard.front_store_collection', $booking) }}" method="POST" class="front-store-amount-form">
                                        @csrf
                                        <input type="number" name="front_store_collected_amount" class="cute-input" min="0.01" max="99999999.99" step="0.01" value="{{ old('front_store_collected_amount', $booking->front_store_collected_amount) }}" placeholder="0.00" required aria-label="ยอดเก็บเงินหน้าร้าน {{ $booking->booking_code }}">
                                        <button type="submit" class="btn-primary" style="padding:8px 12px;font-size:12px;border-radius:9px;white-space:nowrap;">
                                            <i class="fa-solid fa-floppy-disk"></i>
                                            {{ $booking->front_store_collected_at ? 'แก้ไขยอด' : 'บันทึกเก็บเงิน' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary" style="padding:7px 10px;font-size:12px;border-radius:9px;">
                                        <i class="fa-solid fa-eye"></i> ดู
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Quick action links -->
    <div style="display: flex; gap: 15px; margin-bottom: 25px; flex-wrap: wrap;">
        <a href="{{ route('admin.bookings.index') }}" class="btn-primary">
            <i class="fa-solid fa-calendar-days"></i> จัดการรายการจองทั้งหมด
        </a>
        <a href="{{ route('admin.map.index') }}" class="btn-secondary">
            <i class="fa-solid fa-map-location-dot"></i> ดูแผนผังแผงตลาด
        </a>
        <a href="{{ route('admin.users.index') }}" class="btn-secondary">
            <i class="fa-solid fa-user-plus"></i> เพิ่ม/จัดการพนักงาน
        </a>
    </div>

    <!-- Today's Jobs -->
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-business-time"></i> งานติดตั้งตามวันที่เลือก ({{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }})
        </h3>
        
        @if ($todayBookings->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-calendar-minus" style="font-size: 40px; color: var(--border-cute); margin-bottom: 10px; display: block;"></i>
                ไม่มีงานติดตั้งเต็นท์ที่นัดหมายใช้แผงในวันนี้
            </div>
        @else
            <div class="cute-table-container">
                <table class="cute-table">
                    <thead>
                        <tr>
                            <th>รหัสจอง</th>
                            <th>ร้านค้า</th>
                            <th>ล็อตที่จอง</th>
                            <th>รายการอุปกรณ์</th>
                            <th>พนักงานผู้รับผิดชอบ</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($todayBookings as $booking)
                            <tr>
                                <td><strong>{{ $booking->booking_code }}</strong></td>
                                <td>
                                    <div><strong>{{ $booking->shop_name }}</strong></div>
                                    <small style="color: var(--text-muted);">โทร: {{ $booking->customer_phone }}</small>
                                </td>
                                <td><strong style="color: var(--primary-hover);">{{ $booking->lots->pluck('lot_code')->implode(', ') }}</strong></td>
                                <td>
                                    <div>{{ $booking->equipmentSummary() }}</div>
                                </td>
                                <td>
                                    @if ($booking->deliveryTask && $booking->deliveryTask->staff)
                                        <strong><i class="fa-solid fa-user-check" style="color: var(--secondary);"></i> {{ $booking->deliveryTask->staff->name }}</strong>
                                    @else
                                        <span style="color: var(--text-muted); font-style: italic;">ยังไม่ได้มอบหมาย</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = 'status-' . $booking->status;
                                        $statusName = 'รอยืนยัน';
                                        switch($booking->status) {
                                            case 'pending_admin': $statusName = 'รอยืนยัน'; break;
                                            case 'confirmed': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                            case 'assigned': $statusName = 'มอบหมายพนักงาน'; break;
                                            case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                            case 'completed': $statusName = 'ติดตั้งเสร็จแล้ว'; break;
                                            case 'cancelled': $statusName = 'ยกเลิก'; break;
                                            case 'problem': $statusName = 'มีปัญหา'; break;
                                        }
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;">
                                        <i class="fa-solid fa-eye"></i> ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
