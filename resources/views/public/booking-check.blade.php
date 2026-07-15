@extends('layouts.public')

@section('title', 'ตรวจสอบสถานะการจองติดตั้งเต็นท์')

@section('styles')
<style>
    .customer-evidence {
        border-top: 1px dashed var(--border-cute);
        padding-top: 16px;
        margin-top: 4px;
    }

    .customer-evidence-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .customer-evidence-group {
        border: 1px solid var(--border-cute);
        border-radius: 14px;
        padding: 12px;
        background: var(--bg-card-soft);
        min-width: 0;
    }

    .customer-evidence-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        color: var(--text-dark);
        font-size: 13px;
        font-weight: 800;
    }

    .customer-evidence-title i {
        color: var(--primary);
    }

    .customer-evidence-photos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 8px;
    }

    .customer-evidence-photo {
        overflow: hidden;
        border: 1px solid var(--border-cute);
        border-radius: 10px;
        background: var(--bg-card);
    }

    .customer-evidence-photo button {
        display: block;
        width: 100%;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: zoom-in;
    }

    .customer-evidence-photo img {
        display: block;
        width: 100%;
        height: 112px;
        object-fit: cover;
    }

    .customer-evidence-photo span {
        display: block;
        padding: 6px;
        color: var(--text-dark);
        font-size: 11px;
        font-weight: 700;
        text-align: center;
    }

    .customer-evidence-empty {
        display: flex;
        min-height: 112px;
        padding: 14px;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
        line-height: 1.5;
        text-align: center;
    }

    @media (max-width: 640px) {
        .booking-search-form {
            flex-direction: column;
        }

        .booking-result-grid {
            grid-template-columns: 1fr !important;
        }

        .customer-evidence-grid {
            grid-template-columns: 1fr;
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
                    $taskPhotos = $booking->deliveryTasks->flatMap->photos;
                    $approvedLotPhotos = $taskPhotos
                        ->where('photo_type', 'lot_number')
                        ->where('ocr_status', 'approved');
                    $pendingLotPhotos = $taskPhotos
                        ->where('photo_type', 'lot_number')
                        ->where('ocr_status', 'pending_review');
                    $afterPhotos = $booking->status === 'completed'
                        ? $taskPhotos->where('photo_type', 'after')
                        : collect();
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
                    </div>

                    @if ($booking->customer_note)
                        <div style="background-color: var(--bg-page); padding: 10px 15px; border-radius: 12px; font-size: 13px; margin-bottom: 15px; border: 1px solid var(--border-cute);">
                            <span style="font-weight:600;display:block;margin-bottom:2px;">บันทึกจากลูกค้า:</span>
                            {{ $booking->customer_note }}
                        </div>
                    @endif

                    @if ($booking->deliveryTasks->isNotEmpty())
                        <div class="customer-evidence">
                            <div class="customer-evidence-grid">
                                <section class="customer-evidence-group">
                                    <div class="customer-evidence-title">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>รูปยืนยันเลข LOT ({{ $approvedLotPhotos->count() }} รูป)</span>
                                    </div>
                                    @if ($approvedLotPhotos->isNotEmpty())
                                        <div class="customer-evidence-photos">
                                            @foreach ($approvedLotPhotos as $photo)
                                                <div class="customer-evidence-photo">
                                                    <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="รูปยืนยันเลข LOT">
                                                        <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" alt="รูปยืนยันเลข LOT">
                                                        <span><i class="fa-solid fa-circle-check"></i> แอดมินยืนยันแล้ว</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($pendingLotPhotos->isNotEmpty())
                                        <div class="customer-evidence-empty">
                                            พนักงานส่งรูปเลข LOT แล้ว กำลังรอแอดมินตรวจสอบ
                                        </div>
                                    @else
                                        <div class="customer-evidence-empty">ยังไม่มีรูปยืนยันเลข LOT</div>
                                    @endif
                                </section>

                                <section class="customer-evidence-group">
                                    <div class="customer-evidence-title">
                                        <i class="fa-solid fa-images"></i>
                                        <span>รูปส่งงานหลังติดตั้งทั้งหมด ({{ $afterPhotos->count() }} รูป)</span>
                                    </div>
                                    @if ($afterPhotos->isNotEmpty())
                                        <div class="customer-evidence-photos">
                                            @foreach ($afterPhotos as $photo)
                                                <div class="customer-evidence-photo">
                                                    <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="รูปส่งงานหลังติดตั้ง">
                                                        <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" alt="รูปส่งงานหลังติดตั้ง">
                                                        <span><i class="fa-solid fa-circle-check"></i> ส่งงานแล้ว</span>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($booking->status === 'completed')
                                        <div class="customer-evidence-empty">ยังไม่มีรูปส่งงานหลังติดตั้ง</div>
                                    @else
                                        <div class="customer-evidence-empty">รูปส่งงานจะแสดงเมื่อติดตั้งเสร็จแล้ว</div>
                                    @endif
                                </section>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    @endif

    @include('components.image-lightbox')
@endsection
