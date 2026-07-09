@extends('layouts.admin')

@section('title', 'เพิ่มล็อตแผงตลาดใหม่')
@section('page_title', 'เพิ่มล็อตแผงใหม่')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-store"></i> ฟอร์มเพิ่มข้อมูลล็อตแผงตลาด
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

        <form action="{{ route('admin.lots.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="zone_id">เลือกโซน *</label>
                    <select id="zone_id" name="zone_id" class="cute-select" required>
                        <option value="" disabled selected>กรุณาเลือกโซน</option>
                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                {{ $zone->name }} ({{ $zone->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="lot_code">รหัสแผง (Code) *</label>
                    <input type="text" id="lot_code" name="lot_code" class="cute-input" value="{{ old('lot_code') }}" placeholder="ตัวอย่าง: GB01" required>
                    <small style="color: var(--text-muted);">ต้องไม่ซ้ำในระบบ</small>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="display_name">ชื่อที่ใช้แสดงผล</label>
                    <input type="text" id="display_name" name="display_name" class="cute-input" value="{{ old('display_name') }}" placeholder="ว่างไว้จะใช้รหัสแผงอัตโนมัติ">
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="svg_element_id">SVG Element ID</label>
                    <input type="text" id="svg_element_id" name="svg_element_id" class="cute-input" value="{{ old('svg_element_id') }}" placeholder="ตัวอย่าง: lot-GB01">
                </div>
            </div>

            <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 20px; padding: 20px; margin-bottom: 20px;">
                <h4 style="margin-top:0;margin-bottom:15px;color:var(--text-dark);"><i class="fa-solid fa-compass"></i> กำหนดตำแหน่งบนแผนที่ SVG Interactive Map</h4>
                
                <div class="form-grid">
                    <div class="cute-input-group">
                        <label class="cute-label" for="position_x">พิกัด X (Position X)</label>
                        <input type="number" step="any" id="position_x" name="position_x" class="cute-input" value="{{ old('position_x') }}" placeholder="ระบุตัวเลข เช่น 40">
                    </div>

                    <div class="cute-input-group">
                        <label class="cute-label" for="position_y">พิกัด Y (Position Y)</label>
                        <input type="number" step="any" id="position_y" name="position_y" class="cute-input" value="{{ old('position_y') }}" placeholder="ระบุตัวเลข เช่น 50">
                    </div>
                </div>

                <div class="form-grid" style="margin-bottom:0;">
                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="width">ความกว้างแผง (Width)</label>
                        <input type="number" step="any" id="width" name="width" class="cute-input" value="{{ old('width', 45) }}">
                    </div>

                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="height">ความสูงแผง (Height)</label>
                        <input type="number" step="any" id="height" name="height" class="cute-input" value="{{ old('height', 45) }}">
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="is_active">เปิดให้จองใช้งานหรือไม่ *</label>
                    <select id="is_active" name="is_active" class="cute-select" required>
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>เปิดใช้งาน (จองได้ปกติ)</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>ปิดใช้งาน (แอดมินปิดการจองชั่วคราว)</option>
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="note">บันทึกเพิ่มเติม</label>
                    <input type="text" id="note" name="note" class="cute-input" value="{{ old('note') }}" placeholder="เช่น ใกล้ทางออก, ร้อนบ่าย">
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('admin.lots.index') }}" class="btn-secondary" style="flex: 1;">
                    ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลแผงใหม่
                </button>
            </div>
        </form>
    </div>
@endsection
