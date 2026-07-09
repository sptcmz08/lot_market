@extends('layouts.admin')

@section('title', 'จัดการงานติดตั้งและจัดส่งเต็นท์')
@section('page_title', 'งานติดตั้งพนักงานทั้งหมด')

@section('content')
    <div class="cute-card">
        <!-- Filters Form -->
        <form action="{{ route('admin.tasks.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                <label class="cute-label" for="staff_id">พนักงานผู้รับผิดชอบ</label>
                <select id="staff_id" name="staff_id" class="cute-select">
                    <option value="">ทั้งหมด</option>
                    @foreach ($staffMembers as $staff)
                        <option value="{{ $staff->id }}" {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 130px;">
                <label class="cute-label" for="status">สถานะการติดตั้ง</label>
                <select id="status" name="status" class="cute-select">
                    <option value="">ทั้งหมด</option>
                    <option value="waiting" {{ request('status') == 'waiting' ? 'selected' : '' }}>รอพนักงานเริ่มงาน</option>
                    <option value="started" {{ request('status') == 'started' ? 'selected' : '' }}>กำลังติดตั้ง</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>ติดตั้งเสร็จสิ้น</option>
                    <option value="problem" {{ request('status') == 'problem' ? 'selected' : '' }}>มีปัญหาหน้างาน</option>
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
                <label class="cute-label" for="date">วันที่ส่งงาน</label>
                <input type="date" id="date" name="date" class="cute-input" value="{{ request('date') }}">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-filter"></i> กรองงาน
                </button>
                <a href="{{ route('admin.tasks.index') }}" class="btn-secondary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-arrow-rotate-left"></i> ล้าง
                </a>
            </div>
        </form>
    </div>

    <!-- Tasks list table -->
    <div class="cute-table-container">
        <table class="cute-table">
            <thead>
                <tr>
                    <th>วันที่รับงาน</th>
                    <th>รหัสใบจอง</th>
                    <th>ชื่อร้านค้า / ล็อค</th>
                    <th>พนักงานติดตั้ง</th>
                    <th>เวลาเริ่ม/จบงาน</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr>
                        <td>
                            <strong>{{ $task->task_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <strong>{{ $task->booking ? $task->booking->booking_code : '-' }}</strong>
                        </td>
                        <td>
                            @if ($task->booking)
                                <div><strong>{{ $task->booking->shop_name }}</strong></div>
                                <small style="color: var(--primary-hover); font-weight:700;">
                                    แผง: {{ $task->booking->lots->pluck('lot_code')->implode(', ') }}
                                </small>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ไม่มีข้อมูลจอง</span>
                            @endif
                        </td>
                        <td>
                            @if ($task->staff)
                                <strong><i class="fa-solid fa-user-gear" style="color: var(--secondary);"></i> {{ $task->staff->name }}</strong>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ยังไม่มอบหมาย</span>
                            @endif
                        </td>
                        <td>
                            @if ($task->started_at)
                                <div>เริ่ม: {{ $task->started_at->format('H:i น.') }}</div>
                            @endif
                            @if ($task->completed_at)
                                <small style="color: var(--text-muted);">เสร็จ: {{ $task->completed_at->format('H:i น.') }}</small>
                            @endif
                            @if (!$task->started_at && !$task->completed_at)
                                <span style="color: var(--text-muted); font-style: italic;">ยังไม่ดำเนินการ</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . $task->status;
                                $statusName = 'รอเริ่มงาน';
                                switch($task->status) {
                                    case 'waiting': $statusName = 'รอเริ่มงาน'; break;
                                    case 'started': $statusName = 'กำลังติดตั้ง'; break;
                                    case 'completed': $statusName = 'ติดตั้งสำเร็จ'; break;
                                    case 'problem': $statusName = 'พบปัญหา'; break;
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.tasks.show', $task) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;">
                                <i class="fa-solid fa-folder-open"></i> รายละเอียดงาน
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-folder-minus" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                            ไม่มีประวัติงานติดตั้งเต็นท์ในระบบ หรือไม่ตรงเงื่อนไขค้นหา
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-cute">
        {{ $tasks->links() }}
    </div>
@endsection
