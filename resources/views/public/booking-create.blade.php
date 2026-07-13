@extends('layouts.public')

@section('title', 'กรอกรายละเอียดการจองแผงตลาด')

@section('content')
    <div class="cute-card">
        <h2 class="cute-card-title">
            <i class="fa-solid fa-file-invoice"></i> กรอกข้อมูลการจองแผงตลาด
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
                        <span style="font-size: 13px; color: var(--text-muted); font-weight: 600; display: block;">ล็อตที่เลือกจอง:</span>
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
                <label class="cute-label" for="tent_size"><i class="fa-solid fa-tents" style="color:var(--primary);"></i> ขนาดเต็นท์ที่เช่า *</label>
                <select id="tent_size" name="tent_size" class="cute-select" required>
                    <option value="" disabled selected>เลือกขนาดเต็นท์</option>
                    @foreach($tentSizes as $size)
                        <option value="{{ $size }}" {{ old('tent_size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="customer_note"><i class="fa-solid fa-comment-dots" style="color:var(--primary);"></i> หมายเหตุเพิ่มเติม (ระบุตำแหน่งเต็นท์ / สิ่งจำเป็น)</label>
                <textarea id="customer_note" name="customer_note" class="cute-textarea" rows="3" placeholder="ตัวอย่าง: ต้องการโต๊ะเสริม, ปลั๊กไฟใกล้ๆ หรือ ฝากโน้ตถึงพนักงานติดตั้ง">{{ old('customer_note') }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('public.map', ['date' => $date]) }}" class="btn-secondary" style="flex: 1;">
                    <i class="fa-solid fa-arrow-left"></i> ย้อนกลับไปเลือกแผง
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-paper-plane"></i> ส่งคำขอจองแผง
                </button>
            </div>
        </form>
    </div>
@endsection
