@extends('layouts.admin')

@section('title', 'แก้ไขล็อตแผงตลาด #' . $lot->lot_code)
@section('page_title', 'แก้ไขล็อตแผงตลาด')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-pen-to-square"></i> ฟอร์มแก้ไขข้อมูลล็อตแผงตลาด
        </h3>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex: 1;">
                    <strong>มีข้อผิดพลาด:</strong>
                    <ul style="margin:5px 0 0 0;padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.lots.update', $lot) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="zone_id">เลือกโซน *</label>
                    <select id="zone_id" name="zone_id" class="cute-select" required>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id', $lot->zone_id) == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }} ({{ $zone->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="lot_code">รหัสแผง (Code) *</label>
                    <input type="text" id="lot_code" name="lot_code" class="cute-input" value="{{ old('lot_code', $lot->lot_code) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="display_name">ชื่อที่ใช้แสดงผล</label>
                    <input type="text" id="display_name" name="display_name" class="cute-input" value="{{ old('display_name', $lot->display_name) }}">
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="svg_element_id">SVG Element ID</label>
                    <input type="text" id="svg_element_id" name="svg_element_id" class="cute-input" value="{{ old('svg_element_id', $lot->svg_element_id) }}">
                </div>
            </div>

            <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 20px; padding: 20px; margin-bottom: 20px;">
                <h4 style="margin-top:0;margin-bottom:15px;color:var(--text-dark);"><i class="fa-solid fa-compass"></i> กำหนดตำแหน่งบนแผนที่ SVG Interactive Map</h4>
                
                <div class="form-grid">
                    <div class="cute-input-group">
                        <label class="cute-label" for="position_x">พิกัด X (Position X)</label>
                        <input type="number" step="any" id="position_x" name="position_x" class="cute-input" value="{{ old('position_x', $lot->position_x) }}">
                    </div>

                    <div class="cute-input-group">
                        <label class="cute-label" for="position_y">พิกัด Y (Position Y)</label>
                        <input type="number" step="any" id="position_y" name="position_y" class="cute-input" value="{{ old('position_y', $lot->position_y) }}">
                    </div>
                </div>

                <div class="form-grid" style="margin-bottom:0;">
                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="width">ความกว้างแผง (Width)</label>
                        <input type="number" step="any" id="width" name="width" class="cute-input" value="{{ old('width', $lot->width) }}">
                    </div>

                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="height">ความสูงแผง (Height)</label>
                        <input type="number" step="any" id="height" name="height" class="cute-input" value="{{ old('height', $lot->height) }}">
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="is_active">เปิดให้จองใช้งานหรือไม่ *</label>
                    <select id="is_active" name="is_active" class="cute-select" required>
                        <option value="1" {{ old('is_active', $lot->is_active) == 1 ? 'selected' : '' }}>เปิดใช้งาน (จองได้ปกติ)</option>
                        <option value="0" {{ old('is_active', $lot->is_active) == 0 ? 'selected' : '' }}>ปิดใช้งาน (แอดมินปิดการจองชั่วคราว)</option>
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="note">บันทึกเพิ่มเติม</label>
                    <input type="text" id="note" name="note" class="cute-input" value="{{ old('note', $lot->note) }}">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('admin.lots.index') }}" class="btn-secondary" style="flex: 1;">
                    ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกการแก้ไขข้อมูล
                </button>
            </div>
        </form>
    </div>
@endsection
