@extends('layouts.admin')

@section('title', 'แก้ไขข้อมูลการจอง #' . $booking->booking_code)
@section('page_title', 'แก้ไขข้อมูลการจอง')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-pen-to-square"></i> ฟอร์มแก้ไขข้อมูลการจอง
        </h3>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex:1;">
                    <strong>มีข้อผิดพลาด:</strong>
                    <ul style="margin:5px 0 0 0;padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="use_date">วันที่ใช้งานตลาด</label>
                    <input type="date" id="use_date" name="use_date" class="cute-input" value="{{ old('use_date', $booking->use_date->format('Y-m-d')) }}" required>
                </div>
                
                <div class="cute-input-group">
                    <label class="cute-label" for="booking_code_disabled">รหัสอ้างอิง (แก้ไขไม่ได้)</label>
                    <input type="text" id="booking_code_disabled" class="cute-input" value="{{ $booking->booking_code }}" disabled style="background-color: var(--bg-page);">
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="shop_name">ชื่อร้านค้า</label>
                    <input type="text" id="shop_name" name="shop_name" class="cute-input" value="{{ old('shop_name', $booking->shop_name) }}" required>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="customer_phone">เบอร์โทรศัพท์ลูกค้า</label>
                    <input type="text" id="customer_phone" name="customer_phone" class="cute-input" value="{{ old('customer_phone', $booking->customer_phone) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="tent_size">ขนาดเต็นท์</label>
                    <select id="tent_size" name="tent_size" class="cute-select" required>
                        @foreach ($tentSizes as $size)
                            <option value="{{ $size }}" {{ old('tent_size', $booking->tent_size) == $size ? 'selected' : '' }}>{{ $size }} เมตร</option>
                        @endforeach
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="counter_size">ขนาดเคาน์เตอร์</label>
                    <select id="counter_size" name="counter_size" class="cute-select">
                        @foreach ($counterSizes as $size)
                            <option value="{{ $size }}" {{ old('counter_size', $booking->counter_size) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="cute-input-group">
                <label class="cute-label">ล็อตแผงตลาดที่จอง (เลือกได้หลายล็อต)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 15px;">
                    @foreach ($allLots as $lot)
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                            <input type="checkbox" name="lots[]" value="{{ $lot->id }}" style="accent-color: var(--primary); width:16px; height:16px;"
                                {{ in_array($lot->id, old('lots', $booking->lots->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <span>{{ $lot->lot_code }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="admin_note">หมายเหตุ/คำสั่งเพิ่มเติมจากผู้จัดการ</label>
                <textarea id="admin_note" name="admin_note" class="cute-textarea" rows="3">{{ old('admin_note', $booking->admin_note) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary" style="flex: 1;">
                    ยกเลิก / ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกการแก้ไขข้อมูล
                </button>
            </div>
        </form>
    </div>
@endsection
