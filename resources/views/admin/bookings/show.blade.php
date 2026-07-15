@extends('layouts.admin')

@section('title', 'รายละเอียดการจอง #' . $booking->booking_code)
@section('page_title', 'รายละเอียดการจอง')

@section('styles')
<style>
    @media (max-width: 991px) {
        .booking-detail-layout {
            grid-template-columns: 1fr !important;
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

    <!-- Action buttons row -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> กลับไปรายการจอง
        </a>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <form action="{{ route('admin.bookings.payment_slip', $booking) }}" method="POST" enctype="multipart/form-data" style="margin:0;">
                @csrf
                <label class="btn-secondary" style="cursor:pointer;margin:0;" title="{{ $booking->payment_slip_path ? 'เปลี่ยนรูปสลิปการชำระเงิน' : 'แนบรูปสลิปการชำระเงิน' }}">
                    <i class="fa-solid fa-receipt"></i>
                    {{ $booking->payment_slip_path ? 'เปลี่ยนสลิป' : 'แนบสลิป' }}
                    <input type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" required hidden onchange="this.form.submit()">
                </label>
            </form>

            <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn-secondary" style="border-color: var(--accent); color: var(--text-dark);">
                <i class="fa-solid fa-pen-to-square"></i> แก้ไขข้อมูลการจอง
            </a>

            @if ($booking->status === 'pending_admin')
                @if ($booking->payment_slip_path || $booking->collect_front_store)
                    <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #4ECDC4, #3BBAAF); box-shadow: 0 4px 15px rgba(78,205,196,0.3);">
                            <i class="fa-solid fa-circle-check"></i> ยืนยันการจอง
                        </button>
                    </form>
                @else
                    <button type="button" class="btn-secondary" style="opacity:.6;cursor:not-allowed;" disabled title="กรุณาแนบสลิปก่อนยืนยัน">
                        <i class="fa-solid fa-lock"></i> แนบสลิปก่อนยืนยัน
                    </button>
                @endif
            @endif

            @if ($booking->status !== 'completed' && $booking->status !== 'cancelled')
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" style="margin:0;" onsubmit="return confirm('คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?');">
                    @csrf
                    <button type="submit" class="btn-secondary" style="border-color: #FFA3A3; color: #D83A3A;">
                        <i class="fa-solid fa-trash-can"></i> ยกเลิกการจอง
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="booking-detail-layout" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Left Side: Details & Photos -->
        <div>
            <!-- Detail Card -->
            <div class="cute-card">
                <h3 class="cute-card-title">
                    <i class="fa-solid fa-circle-info"></i> ข้อมูลคำสั่งจองอุปกรณ์
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px 30px; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">รหัสอ้างอิงการจอง:</span>
                        <strong style="font-size: 18px; color: var(--primary-hover);">{{ $booking->booking_code }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">สถานะคำสั่งจอง:</span>
                        @php
                            $statusClass = 'status-' . $booking->status;
                            $statusName = 'รอยืนยัน';
                            switch($booking->status) {
                                case 'pending_admin': $statusName = 'รอยืนยัน'; break;
                                case 'confirmed': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                case 'assigned': $statusName = 'มอบหมายงาน'; break;
                                case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                case 'completed': $statusName = 'ติดตั้งสำเร็จ'; break;
                                case 'cancelled': $statusName = 'ยกเลิกการจอง'; break;
                                case 'problem': $statusName = 'พบปัญหา'; break;
                            }
                        @endphp
                        <span class="status-badge {{ $statusClass }}" style="margin-top: 2px;">{{ $statusName }}</span>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">วันที่เข้าใช้งานตลาด:</span>
                        <strong>{{ $booking->use_date->format('d/m/Y') }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">ล็อตของลูกค้า:</span>
                        <strong style="color: var(--primary-hover); font-size: 16px;">{{ $booking->lots->pluck('lot_code')->implode(', ') }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">ชื่อร้านค้า:</span>
                        <strong>{{ $booking->shop_name }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">เบอร์โทรศัพท์ลูกค้า:</span>
                        <strong>{{ $booking->customer_phone }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">รายการอุปกรณ์ที่จอง:</span>
                        <strong>{{ $booking->equipmentSummary() }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 13px; color: var(--text-muted); display: block;">การชำระเงิน:</span>
                        <strong>{{ $booking->payment_slip_path ? 'แนบสลิปแล้ว' : ($booking->collect_front_store ? 'เก็บหน้าร้าน' : 'ยังไม่ระบุ') }}</strong>
                        @if ($booking->collect_front_store)
                            @if ($booking->front_store_collected_at)
                                <small style="display:block;margin-top:4px;color:#1E7E34;font-weight:700;">
                                    เก็บแล้ว {{ number_format((float) $booking->front_store_collected_amount, 2) }} บาท
                                    เมื่อ {{ $booking->front_store_collected_at->format('d/m/Y H:i') }} น.
                                </small>
                            @else
                                <small style="display:block;margin-top:4px;color:#856404;font-weight:700;">ยังไม่ได้บันทึกเก็บเงินหน้าร้าน</small>
                            @endif
                        @endif
                    </div>
                </div>

                @if ($booking->payment_slip_path)
                    <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 15px; margin-top: 15px;">
                        <strong style="font-size: 14px; display: block; margin-bottom: 10px;"><i class="fa-solid fa-receipt"></i> รูปภาพสลิปชำระเงิน:</strong>
                        <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $booking->payment_slip_path]) }}" data-lightbox-alt="สลิปชำระเงิน" style="display:inline-block;">
                            <img src="{{ route('media.show', ['path' => $booking->payment_slip_path]) }}" alt="สลิปชำระเงิน" style="width: 180px; max-width: 100%; border-radius: 12px; border: 1px solid var(--border-cute); display:block;">
                        </button>
                    </div>
                @endif

                @if ($booking->customer_note)
                    <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 15px; margin-top: 15px;">
                        <strong style="font-size: 14px; display: block; margin-bottom: 5px;"><i class="fa-solid fa-comment"></i> โน้ตความต้องการของลูกค้า:</strong>
                        <span style="font-size: 14px; color: var(--text-dark);">{{ $booking->customer_note }}</span>
                    </div>
                @endif

                @if ($booking->admin_note)
                    <div style="background-color: #FFFDF0; border: 2px solid #FFEBA3; border-radius: 16px; padding: 15px; margin-top: 15px;">
                        <strong style="font-size: 14px; display: block; margin-bottom: 5px; color: #856404;"><i class="fa-solid fa-clipboard-question"></i> บันทึกช่วยจำแอดมิน:</strong>
                        <span style="font-size: 14px; color: #856404;">{{ $booking->admin_note }}</span>
                    </div>
                @endif
            </div>

            <!-- Installation Photos -->
            <div class="cute-card">
                <h3 class="cute-card-title">
                    <i class="fa-solid fa-camera-retro"></i> ภาพถ่ายตรวจสอบหน้างาน
                </h3>
                
                @if ($booking->deliveryTasks->flatMap->photos->isNotEmpty())
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px;">
                        @foreach ($booking->deliveryTasks->flatMap->photos as $photo)
                            <div style="border: 2px solid var(--border-cute); border-radius: 18px; overflow: hidden; background-color: var(--bg-page); text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                                <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="ภาพถ่ายยืนยัน" style="display:block;width:100%;">
                                    <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" style="width: 100%; height: 140px; object-fit: cover; display: block;" alt="ภาพถ่ายยืนยัน">
                                </button>
                                <div style="padding: 10px; font-size: 13px;">
                                    <strong style="display:block;margin-bottom:4px;color:var(--text-dark);">
                                        @if($photo->photo_type === 'lot_number') 📋 ป้ายเลขแผง
                                        @elseif($photo->photo_type === 'before') 🛠️ ก่อนติดตั้ง
                                        @elseif($photo->photo_type === 'after') ✅ หลังติดตั้งเสร็จ
                                        @elseif($photo->photo_type === 'problem') ⚠️ รูปปัญหาหน้างาน
                                        @endif
                                    </strong>
                                    @if($photo->photo_type === 'lot_number')
                                        <small style="display:block;font-weight:800;color:@if($photo->ocr_status === 'approved') #1E7E34 @elseif($photo->ocr_status === 'pending_review') #856404 @else #D35400 @endif;">
                                            ตรวจเลขล็อต:
                                            @if($photo->ocr_status === 'approved') ผ่าน
                                            @elseif($photo->ocr_status === 'pending_review') รอตรวจ
                                            @elseif($photo->ocr_status === 'rejected') ไม่ผ่าน
                                            @else ยังไม่ตรวจ
                                            @endif
                                        </small>
                                        @if($photo->ocr_text)
                                            <small style="display:block;color:var(--text-muted);word-break:break-word;">{{ $photo->ocr_text }}</small>
                                        @endif
                                    @endif
                                    @if($photo->taken_at)
                                        <small style="color: var(--text-muted); font-size: 11px;">เวลา: {{ $photo->taken_at->format('H:i น.') }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fa-solid fa-image" style="font-size: 40px; color: var(--border-cute); margin-bottom: 10px; display: block;"></i>
                        พนักงานติดตั้งยังไม่ได้อัปโหลดภาพถ่ายการส่งงาน
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Assignment & History Logs -->
        <div>
            <!-- Worker Assignment Card -->
            @if ($booking->status !== 'pending_admin' && $booking->status !== 'cancelled')
                <div class="cute-card">
                    <h3 class="cute-card-title">
                        <i class="fa-solid fa-truck-pickup"></i> มอบหมายพนักงาน
                    </h3>
                    @php
                        $tasksByType = $booking->deliveryTasks->keyBy('task_type');
                    @endphp
                    <form action="{{ route('admin.bookings.assign', $booking) }}" method="POST">
                        @csrf
                        @if ($booking->tent_size)
                        <div class="cute-input-group">
                            <label class="cute-label" for="tent_staff_id"><i class="fa-solid fa-tents"></i> งานเต็นท์: {{ $booking->tent_size }} สี{{ $booking->tent_color }}</label>
                            <select id="tent_staff_id" name="tent_staff_id" class="cute-select">
                                <option value="">ยังไม่มอบหมาย</option>
                                @foreach ($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('tent_staff_id', $tasksByType->get('tent')?->staff_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if ($booking->counter_size)
                        <div class="cute-input-group">
                            <label class="cute-label" for="counter_staff_id"><i class="fa-solid fa-shop"></i> งานเคาน์เตอร์: {{ $booking->counter_size }}</label>
                            <select id="counter_staff_id" name="counter_staff_id" class="cute-select">
                                <option value="">ยังไม่มอบหมาย</option>
                                @foreach ($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('counter_staff_id', $tasksByType->get('counter')?->staff_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="cute-input-group">
                            <label class="cute-label" for="other_equipment_note"><i class="fa-solid fa-boxes-stacked"></i> รายละเอียดอุปกรณ์อื่น</label>
                            <input id="other_equipment_note" name="other_equipment_note" class="cute-input" maxlength="255" value="{{ old('other_equipment_note', $tasksByType->get('other')?->equipment_note) }}" placeholder="เช่น ถุงทราย เชือก ไฟ หรืออุปกรณ์เสริม">
                        </div>
                        <div class="cute-input-group">
                            <label class="cute-label" for="other_staff_id">พนักงานส่งอุปกรณ์อื่น</label>
                            <select id="other_staff_id" name="other_staff_id" class="cute-select">
                                <option value="">ยังไม่มีงานอุปกรณ์อื่น</option>
                                @foreach ($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" {{ old('other_staff_id', $tasksByType->get('other')?->staff_id) == $staff->id ? 'selected' : '' }}>
                                        {{ $staff->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-users-gear"></i> บันทึกการแบ่งงาน
                        </button>
                    </form>
                </div>
            @endif

            <!-- Status logs history -->
            <div class="cute-card" style="padding: 20px;">
                <h3 class="cute-card-title" style="font-size: 18px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> ประวัติสถานะ
                </h3>
                
                @if ($logs->isEmpty())
                    <span style="color: var(--text-muted); font-size: 13px; font-style: italic;">ไม่มีประวัติการบันทึกสถานะ</span>
                @else
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                        @foreach ($logs as $log)
                            <div style="border-left: 3px solid var(--primary); padding-left: 12px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <strong>สถานะ: 
                                        @php
                                            $stName = $log->new_status;
                                            switch($log->new_status) {
                                                case 'pending_admin': $stName = 'รอยืนยัน'; break;
                                                case 'confirmed': $stName = 'ยืนยันแล้ว'; break;
                                                case 'assigned': $stName = 'มอบหมายงาน'; break;
                                                case 'installing': $stName = 'กำลังติดตั้ง'; break;
                                                case 'completed': $stName = 'ติดตั้งสำเร็จ'; break;
                                                case 'cancelled': $stName = 'ยกเลิก'; break;
                                                case 'problem': $stName = 'พบปัญหา'; break;
                                            }
                                            echo $stName;
                                        @endphp
                                    </strong>
                                    <small style="color: var(--text-muted);">{{ $log->created_at->format('H:i น.') }}</small>
                                </div>
                                <div style="color: var(--text-muted); font-size: 12px; margin-top: 2px;">
                                    {{ $log->note }} &bull; โดย: {{ $log->changedBy ? $log->changedBy->name : 'ระบบ' }}
                                </div>
                                <div style="color: var(--text-muted); font-size: 11px; margin-top: 2px;">
                                    วันที่: {{ $log->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('components.image-lightbox')
@endsection
