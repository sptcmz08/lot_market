@extends('layouts.public')

@section('title', 'ตรวจสอบสถานะการจองติดตั้งเต็นท์')

@section('content')
    <div class="cute-card">
        <h2 class="cute-card-title">
            <i class="fa-solid fa-clipboard-check"></i> ตรวจสอบสถานะการจอง
        </h2>
        <p style="color: var(--text-muted); font-size: 14px; margin-top: -10px; margin-bottom: 25px;">
            กรอกเบอร์โทรศัพท์ที่ใช้จอง หรือรหัสการจอง (เช่น BK...) เพื่อค้นหาสถานะปัจจุบันของท่าน
        </p>

        <form action="{{ route('public.booking.check.submit') }}" method="POST">
            @csrf
            <div style="display: flex; gap: 10px; align-items: flex-start;">
                <div class="cute-input-group" style="margin-bottom: 0; flex: 1;">
                    <input type="text" name="search_query" class="cute-input" value="{{ $query ?? old('search_query') }}" placeholder="กรอกเบอร์โทร 10 หลัก หรือ รหัสการจอง..." required>
                </div>
                <button type="submit" class="btn-primary" style="padding: 12px 25px;">
                    <i class="fa-solid fa-magnifying-glass"></i> ค้นหา
                </button>
            </div>
        </form>
    </div>

    @if (isset($bookings))
        @if ($bookings->isEmpty())
            <div class="cute-card" style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-folder-open" style="font-size: 50px; color: var(--border-cute); margin-bottom: 15px; display: block;"></i>
                <h3 style="margin: 0; color: var(--text-dark);">ไม่พบข้อมูลการจอง</h3>
                <p style="color: var(--text-muted); margin-top: 5px;">โปรดตรวจสอบความถูกต้องของรหัสจองหรือเบอร์โทรศัพท์ และลองใหม่อีกครั้ง</p>
            </div>
        @else
            <h3 style="font-weight: 700; margin-bottom: 15px; padding-left: 5px;">
                ผลการค้นหาข้อมูล (พบ {{ $bookings->count() }} รายการ)
            </h3>

            @foreach ($bookings as $booking)
                <div class="cute-card" style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 10px; border-bottom: 2px solid var(--border-cute); padding-bottom: 12px; margin-bottom: 15px;">
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block;">รหัสจอง:</span>
                            <strong style="font-size: 16px; color: var(--primary-hover);">{{ $booking->booking_code }}</strong>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600; display: block;">สถานะ:</span>
                            @php
                                $statusClass = 'status-' . $booking->status;
                                $statusName = 'รอยืนยัน';
                                switch($booking->status) {
                                    case 'pending_admin': $statusName = 'รอยืนยัน'; break;
                                    case 'confirmed': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                    case 'assigned': $statusName = 'มอบหมายงานพนักงานแล้ว'; break;
                                    case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                    case 'completed': $statusName = 'ติดตั้งเสร็จแล้ว'; break;
                                    case 'cancelled': $statusName = 'ยกเลิกการจอง'; break;
                                    case 'problem': $statusName = 'พบปัญหา'; break;
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">ร้านค้า:</span>
                            <strong style="font-size: 15px;">{{ $booking->shop_name }}</strong>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">เบอร์โทร:</span>
                            <strong style="font-size: 15px;">{{ $booking->customer_phone }}</strong>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">วันที่ใช้งาน:</span>
                            <strong style="font-size: 15px;">
                                @php
                                    $parts = explode('-', $booking->use_date->format('Y-m-d'));
                                    $thaiYear = intval($parts[0]) + 543;
                                    $months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                                    $thaiMonth = $months[intval($parts[1]) - 1];
                                    $thaiDay = intval($parts[2]);
                                    echo "$thaiDay $thaiMonth $thaiYear";
                                @endphp
                            </strong>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">เลขล็อค:</span>
                            <strong style="font-size: 15px; color: var(--primary-hover);">
                                {{ $booking->lots->pluck('lot_code')->implode(', ') }}
                            </strong>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">ขนาดเต็นท์:</span>
                            <strong style="font-size: 15px;">{{ $booking->tent_size }}</strong>
                        </div>
                    </div>

                    @if ($booking->customer_note)
                        <div style="background-color: var(--bg-page); padding: 10px 15px; border-radius: 12px; font-size: 13px; margin-bottom: 15px; border: 1px solid var(--border-cute);">
                            <span style="font-weight:600;display:block;margin-bottom:2px;">บันทึกจากลูกค้า:</span>
                            {{ $booking->customer_note }}
                        </div>
                    @endif

                    <!-- Task details and photos -->
                    @if ($booking->deliveryTask)
                        @if ($booking->deliveryTask->photos->isNotEmpty())
                            <div style="border-top: 1px dashed var(--border-cute); padding-top: 15px;">
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 10px;">
                                    <i class="fa-solid fa-images" style="color: var(--primary);"></i> ภาพถ่ายการติดตั้งโดยพนักงาน:
                                </span>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;">
                                    @foreach ($booking->deliveryTask->photos as $photo)
                                        <div style="border: 2px solid var(--border-cute); border-radius: 14px; overflow: hidden; background-color: var(--bg-page); text-align: center;">
                                            <a href="{{ Storage::url($photo->image_path) }}" target="_blank">
                                                <img src="{{ Storage::url($photo->image_path) }}" style="width: 100%; height: 100px; object-fit: cover; display: block;" alt="ภาพถ่ายติดตั้ง">
                                            </a>
                                            <span style="font-size: 11px; font-weight: 700; padding: 4px; display: block; color: var(--text-dark);">
                                                @if($photo->photo_type === 'lot_number') 📝 เลขล็อค
                                                @elseif($photo->photo_type === 'before') 🛠️ ก่อนติดตั้ง
                                                @elseif($photo->photo_type === 'after') ✅ หลังติดตั้ง
                                                @elseif($photo->photo_type === 'problem') ⚠️ แจ้งปัญหา
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            @if ($booking->status === 'assigned' || $booking->status === 'installing')
                                <div style="border-top: 1px dashed var(--border-cute); padding-top: 12px; color: var(--text-muted); font-size: 13px;">
                                    <i class="fa-solid fa-clock"></i> พนักงานกำลังเตรียมเข้าดำเนินงานติดตั้ง หรือยังไม่ได้ทำการบันทึกภาพ
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
            @endforeach
        @endif
    @endif
@endsection
