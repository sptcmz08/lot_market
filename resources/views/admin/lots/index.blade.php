@extends('layouts.admin')

@section('title', 'จัดการโซนและล็อตแผงตลาด')
@section('page_title', 'จัดการล็อตแผงตลาด')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <!-- Filters -->
        <form action="{{ route('admin.lots.index') }}" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 0;">
            <div class="cute-input-group" style="margin-bottom: 0; flex-direction: row; align-items: center; gap: 8px;">
                <label class="cute-label" for="zone_id" style="white-space: nowrap;">กรองตามโซน:</label>
                <select id="zone_id" name="zone_id" class="cute-select" style="width: auto; padding: 8px 12px; border-radius: 12px;" onchange="this.form.submit()">
                    <option value="">ทั้งหมด</option>
                    @foreach ($zones as $zone)
                        <option value="{{ $zone->id }}" {{ request('zone_id') == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }} ({{ $zone->lots_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex-direction: row; align-items: center; gap: 8px;">
                <input type="text" name="search" class="cute-input" value="{{ request('search') }}" placeholder="ค้นหาเลขล็อค..." style="padding: 8px 12px; border-radius: 12px; width: 150px;">
            </div>
            
            <button type="submit" class="btn-secondary" style="padding: 8px 15px; border-radius: 12px;">
                <i class="fa-solid fa-magnifying-glass"></i> ค้นหา
            </button>
        </form>

        <a href="{{ route('admin.lots.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i> เพิ่มล็อตแผงใหม่
        </a>
    </div>

    <!-- Lots Table -->
    <div class="cute-table-container">
        <table class="cute-table">
            <thead>
                <tr>
                    <th>เลขแผง (Code)</th>
                    <th>ชื่อแสดงผล</th>
                    <th>โซน</th>
                    <th>SVG Element ID</th>
                    <th>พิกัด (X, Y)</th>
                    <th>ขนาด (กว้าง x สูง)</th>
                    <th>สถานะการเปิดแผง</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lots as $lot)
                    <tr>
                        <td><strong style="color: var(--primary-hover); font-size: 15px;">{{ $lot->lot_code }}</strong></td>
                        <td>{{ $lot->display_name ?: '-' }}</td>
                        <td>
                            @if ($lot->zone)
                                <span style="font-weight: 700;">{{ $lot->zone->name }}</span>
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ไม่มีโซน</span>
                            @endif
                        </td>
                        <td><code>{{ $lot->svg_element_id ?: '-' }}</code></td>
                        <td>
                            @if($lot->position_x !== null)
                                X: {{ $lot->position_x }}, Y: {{ $lot->position_y }}
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ไม่ได้ระบุ</span>
                            @endif
                        </td>
                        <td>
                            @if($lot->width !== null)
                                {{ $lot->width }} x {{ $lot->height }}
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">ไม่ได้ระบุ</span>
                            @endif
                        </td>
                        <td>
                            @if ($lot->is_active)
                                <span class="status-badge status-available" style="padding: 4px 10px; font-size:12px;">
                                    <i class="fa-solid fa-circle-check"></i> เปิดใช้งาน
                                </span>
                            @else
                                <span class="status-badge status-blocked" style="padding: 4px 10px; font-size:12px;">
                                    <i class="fa-solid fa-circle-minus"></i> ปิดแผงชั่วคราว
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.lots.edit', $lot) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="แก้ไขแผง">
                                    <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                                </a>
                                <form action="{{ route('admin.lots.destroy', $lot) }}" method="POST" style="margin: 0;" onsubmit="return confirm('คุณต้องการลบล็อคนี้ใช่หรือไม่? ข้อมูลการจองที่เกี่ยวข้องจะยังคงอยู่แต่จะอ้างอิงล็อตนี้ไม่ได้');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px; border-color: #FFA3A3; color: #D83A3A;" title="ลบแผง">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-store-slash" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                            ยังไม่มีล็อตแผงในระบบ หรือ ไม่ตรงกับเงื่อนไขการค้นหา
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-cute">
        {{ $lots->links() }}
    </div>
@endsection
