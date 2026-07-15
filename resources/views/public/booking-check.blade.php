@extends('layouts.public')

@section('title', 'ตรวจสอบสถานะการจองติดตั้งเต็นท์')

@section('styles')
<style>
    .status-photo-card {
        border: 1px solid var(--border-cute);
        border-radius: 14px;
        overflow: hidden;
        background: var(--bg-card-soft);
        min-height: 148px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .status-photo-card img {
        width: 100%;
        height: 148px;
        object-fit: cover;
        display: block;
    }

    .status-photo-empty {
        color: var(--text-muted);
        font-size: 13px;
        font-weight: 700;
        padding: 16px;
    }

    .status-photo-caption {
        display: block;
        padding: 7px 8px;
        font-size: 12px;
        font-weight: 800;
        color: var(--text-dark);
        border-top: 1px solid var(--border-cute);
        background: var(--bg-card);
    }

    @media (max-width: 640px) {
        .booking-search-form {
            flex-direction: column;
        }

        .booking-result-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection

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
            <div class="booking-search-form" style="display: flex; gap: 10px; align-items: flex-start;">
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
                @php
                    $afterPhotos = $booking->deliveryTask
                        ? $booking->deliveryTask->photos->where('photo_type', 'after')
                        : collect();
                    $mainAfterPhoto = $afterPhotos->first();
                @endphp
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
                                $statusName = 'รอส่ง';
                                switch($booking->status) {
                                    case 'pending_admin': $statusName = 'รอส่ง'; break;
                                    case 'confirmed': $statusName = 'รอส่ง'; break;
                                    case 'assigned': $statusName = 'รอส่ง'; break;
                                    case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                    case 'completed': $statusName = 'ส่งเสร็จแล้ว'; break;
                                    case 'cancelled': $statusName = 'ยกเลิกการจอง'; break;
                                    case 'problem': $statusName = 'พบปัญหา'; break;
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                        </div>
                    </div>

                    <div class="booking-result-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
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
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">รายการอุปกรณ์:</span>
                            <strong style="font-size: 15px;">{{ $booking->equipmentSummary() }}</strong>
                        </div>
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); display: block;">การชำระเงิน:</span>
                            <strong style="font-size: 15px;">{{ $booking->payment_slip_path ? 'แนบสลิปแล้ว' : ($booking->collect_front_store ? 'เก็บหน้าร้าน' : 'ยังไม่ระบุ') }}</strong>
                        </div>
                        <div style="grid-column: span 2;">
                            <span style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 8px;">
                                รูปหลังติดตั้ง:
                            </span>
                            @if ($mainAfterPhoto && $booking->status === 'completed')
                                <div class="status-photo-card">
                                    <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $mainAfterPhoto->image_path]) }}" data-lightbox-alt="รูปหลังติดตั้ง" style="display:block;width:100%;border:0;padding:0;background:transparent;cursor:zoom-in;">
                                        <img src="{{ route('media.show', ['path' => $mainAfterPhoto->image_path]) }}" alt="รูปหลังติดตั้ง">
                                        <span class="status-photo-caption">
                                            <i class="fa-solid fa-magnifying-glass-plus"></i> กดเพื่อดูรูปหลังติดตั้ง
                                            @if($afterPhotos->count() > 1)
                                                ({{ $afterPhotos->count() }} รูป)
                                            @endif
                                        </span>
                                    </button>
                                </div>
                            @else
                                <div class="status-photo-card">
                                    <div class="status-photo-empty">
                                        <i class="fa-solid fa-image" style="font-size:26px;display:block;margin-bottom:8px;color:var(--border-cute);"></i>
                                        ยังไม่มีรูปหลังติดตั้ง
                                    </div>
                                </div>
                            @endif
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
                        @if ($booking->deliveryTask->photos->where('photo_type', 'after')->isNotEmpty() && $booking->status === 'completed')
                            <div style="border-top: 1px dashed var(--border-cute); padding-top: 15px;">
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-dark); display: block; margin-bottom: 10px;">
                                    <i class="fa-solid fa-images" style="color: var(--primary);"></i> รูปหลังติดตั้งทั้งหมด:
                                </span>
                                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;">
                                    @foreach ($booking->deliveryTask->photos->where('photo_type', 'after') as $photo)
                                        <div style="border: 2px solid var(--border-cute); border-radius: 14px; overflow: hidden; background-color: var(--bg-page); text-align: center;">
                                            <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="ภาพถ่ายติดตั้ง" style="display:block;width:100%;">
                                                <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" style="width: 100%; height: 100px; object-fit: cover; display: block;" alt="ภาพถ่ายติดตั้ง">
                                            </button>
                                            <span style="font-size: 11px; font-weight: 700; padding: 4px; display: block; color: var(--text-dark);">
                                                ✅ หลังติดตั้ง
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

    @include('components.image-lightbox')
@endsection
