@extends('layouts.public')

@section('title', 'กรอกรายละเอียดการสั่งจองอุปกรณ์')

@section('content')
    <div class="cute-card">
        <h2 class="cute-card-title">
            <i class="fa-solid fa-file-invoice"></i> กรอกข้อมูลสั่งจองเต็นท์/เคาน์เตอร์
        </h2>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex:1;">
                    <strong style="display:block;margin-bottom:5px;">เกิดข้อผิดพลาดในการกรอกข้อมูล:</strong>
                    <ul style="margin:0;padding-left:20px;font-size:14px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('public.booking.store') }}" method="POST">
            @csrf
            
            <input type="hidden" name="use_date" value="{{ $date }}">
            
            <!-- Selected lots summary box -->
            <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 18px; padding: 15px 20px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); font-weight: 600; display: block;">ล็อตของลูกค้าที่เลือก:</span>
                        <strong style="font-size: 20px; color: var(--primary-hover); font-weight: 800;">
                            {{ implode(', ', $selectedCodes) }}
                        </strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 13px; color: var(--text-muted); font-weight: 600; display: block;">วันที่ใช้งาน:</span>
                        <strong style="font-size: 16px; font-weight: 700;">
                            @php
                                $parts = explode('-', $date);
                                $thaiYear = intval($parts[0]) + 543;
                                $months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                                $thaiMonth = $months[intval($parts[1]) - 1];
                                $thaiDay = intval($parts[2]);
                                echo "$thaiDay $thaiMonth $thaiYear";
                            @endphp
                        </strong>
                    </div>
                </div>
                @foreach($selectedCodes as $code)
                    <input type="hidden" name="lots[]" value="{{ $code }}">
                @endforeach
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="shop_name"><i class="fa-solid fa-store" style="color:var(--primary);"></i> ชื่อร้านค้า *</label>
                <input type="text" id="shop_name" name="shop_name" class="cute-input" value="{{ old('shop_name') }}" placeholder="ตัวอย่าง: หอยแครงลวกเจ๊แก้ว" required>
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="customer_phone"><i class="fa-solid fa-phone" style="color:var(--primary);"></i> เบอร์โทรศัพท์ลูกค้า *</label>
                <input type="tel" id="customer_phone" name="customer_phone" class="cute-input" value="{{ old('customer_phone') }}" placeholder="ตัวอย่าง: 0812345678" required>
                <small style="color: var(--text-muted); font-size: 12px;">กรอกเบอร์โทรศัพท์ 9-10 หลัก สำหรับตรวจสอบสถานะงาน</small>
            </div>

            <div class="cute-input-group">
                <label class="cute-label"><i class="fa-solid fa-cart-plus" style="color:var(--primary);"></i> รายการที่ต้องการจอง *</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
                    <div style="background: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 14px;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:800;margin-bottom:12px;">
                            <input type="checkbox" name="wants_tent" value="1" style="width:18px;height:18px;accent-color:var(--primary);" {{ old('wants_tent', old('wants_counter') ? null : true) ? 'checked' : '' }}>
                            <span>จองเต็นท์</span>
                        </label>
                        <div style="display:grid;gap:10px;">
                            <select name="tent_size" class="cute-select">
                                <option value="">เลือกขนาดเต็นท์</option>
                                @foreach($tentSizes as $size)
                                    <option value="{{ $size }}" {{ old('tent_size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <select name="tent_color" class="cute-select">
                                <option value="">เลือกสีเต็นท์</option>
                                @foreach($equipmentColors as $color)
                                    <option value="{{ $color }}" {{ old('tent_color') == $color ? 'selected' : '' }}>{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="background: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 14px;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:800;margin-bottom:12px;">
                            <input type="checkbox" name="wants_counter" value="1" style="width:18px;height:18px;accent-color:var(--primary);" {{ old('wants_counter') ? 'checked' : '' }}>
                            <span>จองเคาน์เตอร์</span>
                        </label>
                        <div style="display:grid;gap:10px;">
                            <select name="counter_size" class="cute-select">
                                <option value="">เลือกขนาดเคาน์เตอร์</option>
                                @foreach($counterSizes as $size)
                                    <option value="{{ $size }}" {{ old('counter_size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <select name="counter_color" class="cute-select">
                                <option value="">เลือกสีเคาน์เตอร์</option>
                                @foreach($equipmentColors as $color)
                                    <option value="{{ $color }}" {{ old('counter_color') == $color ? 'selected' : '' }}>{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="customer_note"><i class="fa-solid fa-comment-dots" style="color:var(--primary);"></i> หมายเหตุเพิ่มเติม (ระบุตำแหน่งเต็นท์ / สิ่งจำเป็น)</label>
                <textarea id="customer_note" name="customer_note" class="cute-textarea" rows="3" placeholder="ตัวอย่าง: ต้องการโต๊ะเสริม, ปลั๊กไฟใกล้ๆ หรือ ฝากโน้ตถึงพนักงานติดตั้ง">{{ old('customer_note') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('public.map', ['date' => $date]) }}" class="btn-secondary" style="flex: 1;">
                    <i class="fa-solid fa-arrow-left"></i> ย้อนกลับไปเลือกล็อต
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-paper-plane"></i> ส่งคำสั่งจองอุปกรณ์
                </button>
            </div>
        </form>
    </div>
@endsection
