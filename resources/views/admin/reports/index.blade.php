@extends('layouts.admin')

@section('title', 'รายงานและสถิติการใช้งานระบบ')
@section('page_title', 'รายงานและสถิติแผงตลาด')

@section('styles')
<style>
    .report-card-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }

    @media (max-width: 767px) {
        .report-card-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .report-mini-card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-cute);
        border-radius: 18px;
        padding: 15px 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }

    .mini-card-val {
        font-size: 28px;
        font-weight: 800;
        margin-top: 5px;
        display: block;
    }

    .progress-bar-container {
        width: 100%;
        background-color: #FAF5F7;
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
        border: 1px solid var(--border-cute);
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
    <!-- Report Filters -->
    <div class="cute-card">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end; margin: 0;">
            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="cute-label" for="year">ปี พ.ศ. (ค.ศ.)</label>
                <select id="year" name="year" class="cute-select">
                    @for ($y = date('Y') - 1; $y <= date('Y') + 2; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                            {{ $y + 543 }} ({{ $y }})
                        </option>
                    @endfor
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="cute-label" for="month">เลือกเดือน</label>
                <select id="month" name="month" class="cute-select">
                    @php
                        $monthsNames = [
                            '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                            '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                            '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                        ];
                    @endphp
                    @foreach ($monthsNames as $key => $name)
                        <option value="{{ $key }}" {{ $key == $month ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary" style="padding: 10px 20px;">
                <i class="fa-solid fa-chart-pie"></i> แสดงรายงาน
            </button>
        </form>
    </div>

    <!-- selected month stats aggregate cards -->
    <div class="report-card-grid">
        <div class="report-mini-card">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">ยอดจองล็อคเดือนนี้</span>
            <span class="mini-card-val" style="color: var(--text-dark);">{{ $monthStats['total'] }} ล็อค</span>
        </div>
        <div class="report-mini-card">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">ติดตั้งสำเร็จแล้ว</span>
            <span class="mini-card-val" style="color: #1E7E34;">{{ $monthStats['completed'] }} ล็อค</span>
            <div class="progress-bar-container">
                @php
                    $pctCompleted = $monthStats['total'] > 0 ? ($monthStats['completed'] / $monthStats['total']) * 100 : 0;
                @endphp
                <div class="progress-bar-fill" style="width: {{ $pctCompleted }}%; background-color: var(--completed);"></div>
            </div>
        </div>
        <div class="report-mini-card">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">แจ้งปัญหาหน้างาน</span>
            <span class="mini-card-val" style="color: #D35400;">{{ $monthStats['problem'] }} ล็อค</span>
            <div class="progress-bar-container">
                @php
                    $pctProblem = $monthStats['total'] > 0 ? ($monthStats['problem'] / $monthStats['total']) * 100 : 0;
                @endphp
                <div class="progress-bar-fill" style="width: {{ $pctProblem }}%; background-color: var(--problem);"></div>
            </div>
        </div>
        <div class="report-mini-card">
            <span style="font-size: 13px; color: var(--text-muted); font-weight: 600;">จำนวนงานยกเลิก</span>
            <span class="mini-card-val" style="color: #383D41;">{{ $monthStats['cancelled'] }} ล็อค</span>
        </div>
    </div>

    <!-- Day by day report table -->
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-chart-bar"></i> รายงานสถิติแยกรายวัน (ประจำเดือน {{ $monthsNames[$month] }} {{ $year + 543 }})
        </h3>
        
        @if ($monthlyReport->isEmpty())
            <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                <i class="fa-solid fa-chart-line" style="font-size: 40px; color: var(--border-cute); margin-bottom: 10px; display: block;"></i>
                ยังไม่มีข้อมูลบันทึกสถิติการใช้งานสำหรับเดือนนี้
            </div>
        @else
            <div class="cute-table-container">
                <table class="cute-table">
                    <thead>
                        <tr>
                            <th>วันที่ใช้แผง</th>
                            <th>จำนวนแผงที่จอง (ยอดจอง)</th>
                            <th>ติดตั้งสำเร็จแล้ว (แผง)</th>
                            <th>พบปัญหาระหว่างติดตั้ง</th>
                            <th>ยกเลิก</th>
                            <th>อัตราส่วนงานติดตั้งสำเร็จ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($monthlyReport as $day)
                            <tr>
                                <td>
                                    <strong>
                                        @php
                                            $parts = explode('-', $day->date);
                                            echo intval($parts[2]) . ' ' . $monthsNames[$parts[1]] . ' ' . (intval($parts[0]) + 543);
                                        @endphp
                                    </strong>
                                </td>
                                <td><strong style="font-size: 15px;">{{ $day->total_bookings }} แผง</strong></td>
                                <td><span style="color: #1E7E34; font-weight: 700;">{{ $day->completed_bookings }}</span></td>
                                <td><span style="color: #D35400; font-weight: 700;">{{ $day->problem_bookings }}</span></td>
                                <td><span style="color: #721C24; font-weight: 700;">{{ $day->cancelled_bookings }}</span></td>
                                <td>
                                    @php
                                        $ratio = $day->total_bookings > 0 ? ($day->completed_bookings / $day->total_bookings) * 100 : 0;
                                    @endphp
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="progress-bar-container" style="margin-top:0; width:80px; height:6px;">
                                            <div class="progress-bar-fill" style="width: {{ $ratio }}%; background-color: var(--completed);"></div>
                                        </div>
                                        <strong>{{ round($ratio, 1) }}%</strong>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
