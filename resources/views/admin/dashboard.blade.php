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
</style>
@endsection

@section('content')

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
            <i class="fa-solid fa-business-time"></i> งานติดตั้งเต็นท์ประจำวันนี้ ({{ now()->format('d/m/Y') }})
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
                            <th>เต็นท์/เคาน์เตอร์</th>
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
                                    <div>เต็นท์: {{ $booking->tent_size }}ม.</div>
                                    @if ($booking->counter_size)
                                        <small style="color: var(--text-muted);">เคาน์เตอร์: {{ $booking->counter_size }}</small>
                                    @endif
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
