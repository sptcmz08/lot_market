@extends('layouts.admin')

@section('title', 'จัดการคำสั่งจองอุปกรณ์')
@section('page_title', 'รายการจองทั้งหมด')

@section('styles')
<style>
    .payment-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 20px;
    }

    .payment-summary-item {
        padding: 15px 16px;
        border: 1px solid var(--border-cute);
        border-left: 4px solid #f59e0b;
        border-radius: 10px;
        background: var(--bg-card);
    }

    .payment-summary-item:nth-child(2) { border-left-color: #22c55e; }
    .payment-summary-item:nth-child(3) { border-left-color: #ef4444; }

    .payment-summary-item span {
        display: block;
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .payment-summary-item strong {
        display: block;
        margin-top: 4px;
        color: var(--text-dark);
        font-size: 23px;
    }

    .payment-method-detail {
        min-width: 145px;
    }

    @media (max-width: 640px) {
        .payment-summary-grid {
            grid-template-columns: 1fr;
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

    <div class="payment-summary-grid">
        <div class="payment-summary-item">
            <span><i class="fa-solid fa-cash-register"></i> เก็บเงินหน้าร้าน</span>
            <strong>{{ number_format($paymentSummary['front_store']) }} รายการ</strong>
        </div>
        <div class="payment-summary-item">
            <span><i class="fa-solid fa-receipt"></i> แนบสลิปแล้ว</span>
            <strong>{{ number_format($paymentSummary['slip_attached']) }} รายการ</strong>
        </div>
        <div class="payment-summary-item">
            <span><i class="fa-solid fa-clock"></i> รอแนบสลิป</span>
            <strong>{{ number_format($paymentSummary['slip_pending']) }} รายการ</strong>
        </div>
    </div>

    <div class="cute-card">
        @php
            $todayStr = \Carbon\Carbon::now('Asia/Bangkok')->format('Y-m-d');
            $yesterdayStr = \Carbon\Carbon::now('Asia/Bangkok')->subDay()->format('Y-m-d');
        @endphp
        <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 13px; font-weight: 700; color: var(--text-muted);"><i class="fa-solid fa-clock-rotate-left"></i> ทางลัดเลือกวันที่:</span>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['date' => $todayStr])) }}"
               class="btn-secondary" style="padding: 6px 12px; font-size: 12px; text-decoration: none; {{ request('date') === $todayStr ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : '' }}">
                <i class="fa-solid fa-calendar-day"></i> วันนี้ ({{ \Carbon\Carbon::parse($todayStr)->format('d/m/Y') }})
            </a>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['date' => $yesterdayStr])) }}"
               class="btn-secondary" style="padding: 6px 12px; font-size: 12px; text-decoration: none; {{ request('date') === $yesterdayStr ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : '' }}">
                <i class="fa-solid fa-calendar-minus"></i> เมื่อวาน ({{ \Carbon\Carbon::parse($yesterdayStr)->format('d/m/Y') }})
            </a>
            <a href="{{ route('admin.bookings.index', request()->except(['page', 'date'])) }}"
               class="btn-secondary" style="padding: 6px 12px; font-size: 12px; text-decoration: none; {{ empty(request('date')) ? 'background: #0284c7; color: #fff; border-color: #0284c7;' : '' }}">
                <i class="fa-solid fa-database"></i> ดูย้อนหลังทั้งหมด (ไม่จำกัดวันที่)
            </a>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('admin.bookings.index') }}" method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: flex-end;">
            <div class="cute-input-group" style="margin-bottom: 0; flex: 2; min-width: 200px;">
                <label class="cute-label" for="search">ค้นหา</label>
                <input type="text" id="search" name="search" class="cute-input" value="{{ request('search') }}" placeholder="ค้นหารหัสจอง, ร้านค้า, เบอร์โทร, เลขล็อต...">
            </div>
            
            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 130px;">
                <label class="cute-label" for="status">สถานะ</label>
                <select id="status" name="status" class="cute-select">
                    <option value="">ทั้งหมด</option>
                    <option value="pending_admin" {{ request('status') == 'pending_admin' ? 'selected' : '' }}>รอยืนยัน</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว/รอส่ง</option>
                    <option value="photo_review" {{ request('status') == 'photo_review' ? 'selected' : '' }}>ส่งรูปแล้ว/รอตรวจ</option>
                    <option value="installing" {{ request('status') == 'installing' ? 'selected' : '' }}>กำลังติดตั้ง</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>ติดตั้งสำเร็จ</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                    <option value="problem" {{ request('status') == 'problem' ? 'selected' : '' }}>มีปัญหา</option>
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
                <label class="cute-label" for="payment_method">วิธีชำระเงิน</label>
                <select id="payment_method" name="payment_method" class="cute-select">
                    <option value="">ทั้งหมด</option>
                    <option value="front_store" {{ request('payment_method') === 'front_store' ? 'selected' : '' }}>เก็บเงินหน้าร้าน</option>
                    <option value="slip_attached" {{ request('payment_method') === 'slip_attached' ? 'selected' : '' }}>แนบสลิปแล้ว</option>
                    <option value="slip_pending" {{ request('payment_method') === 'slip_pending' ? 'selected' : '' }}>รอแนบสลิป</option>
                </select>
            </div>

            <div class="cute-input-group" style="margin-bottom: 0; flex: 1; min-width: 140px;">
                <label class="cute-label" for="date">วันที่ใช้งาน (ดูย้อนหลัง)</label>
                <input type="date" id="date" name="date" class="cute-input" value="{{ request('date') }}" onchange="this.form.submit()">
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn-primary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-filter"></i> กรองข้อมูล
                </button>
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary" style="padding: 10px 18px;">
                    <i class="fa-solid fa-arrow-rotate-left"></i> ล้าง
                </a>
            </div>
        </form>
    </div>

    <div class="cute-table-container">
        <table class="cute-table">
            <thead>
                <tr>
                    <th>วันที่ใช้งาน</th>
                    <th>รหัสจอง</th>
                    <th>ชื่อร้านค้า / เบอร์โทร</th>
                    <th>ล็อตแผงที่จอง</th>
                    <th>รายการอุปกรณ์</th>
                    <th>การชำระเงิน</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $taskPhotos = $booking->deliveryTasks->flatMap->photos;
                        $isLotReviewPending = $taskPhotos->where('photo_type', 'lot_number')->contains('ocr_status', 'submitted');
                        $isWorkReviewPending = $booking->deliveryTasks->contains('status', 'photo_uploaded');
                        $isPhotoReviewPending = $isLotReviewPending || $isWorkReviewPending;
                        $isPhotoRejected = $booking->deliveryTasks->pluck('problem_note')
                            ->filter(fn ($note) => str_starts_with((string) $note, 'ตีกลับรูป'))
                            ->isNotEmpty();
                        $isLotApproved = $taskPhotos->where('photo_type', 'lot_number')->contains('ocr_status', 'approved');
                        $hasDraftPhotos = !$isPhotoReviewPending
                            && $booking->status !== 'completed'
                            && $taskPhotos->where('photo_type', 'after')->isNotEmpty();
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $booking->use_date->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <strong>{{ $booking->booking_code }}</strong>
                        </td>
                        <td>
                            <div><strong>{{ $booking->shop_name }}</strong></div>
                            <small style="color: var(--text-muted);">โทร: {{ $booking->customer_phone }}</small>
                        </td>
                        <td>
                            <strong style="color: var(--primary-hover);">{{ $booking->lots->pluck('lot_code')->implode(', ') }}</strong>
                        </td>
                        <td>
                            <div>{{ $booking->equipmentSummary() }}</div>
                        </td>
                        <td class="payment-method-detail">
                            @if ($booking->collect_front_store)
                                <span class="status-badge status-pending_admin"><i class="fa-solid fa-cash-register"></i> เก็บหน้าร้าน</span>
                                @if ($booking->front_store_collected_at)
                                    <small style="display:block;margin-top:5px;color:#15803d;font-weight:700;">
                                        เก็บแล้ว {{ number_format((float) $booking->front_store_collected_amount, 2) }} บาท
                                    </small>
                                @else
                                    <small style="display:block;margin-top:5px;color:var(--text-muted);">ยังไม่ได้เก็บเงิน</small>
                                @endif
                            @elseif ($booking->payment_slip_path)
                                <span class="status-badge status-completed"><i class="fa-solid fa-receipt"></i> แนบสลิปแล้ว</span>
                                <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $booking->payment_slip_path]) }}" data-lightbox-alt="สลิป {{ $booking->booking_code }}" style="display:block;margin-top:6px;color:var(--primary-hover);font-weight:700;font-size:12px;">
                                    <i class="fa-solid fa-magnifying-glass"></i> ดูสลิป
                                </button>
                            @else
                                <span class="status-badge status-problem"><i class="fa-solid fa-clock"></i> รอแนบสลิป</span>
                            @endif
                        </td>
                        <td>
                            @if ($isWorkReviewPending)
                                <span class="status-badge status-pending_admin"><i class="fa-solid fa-camera"></i> รูปงานรอตรวจ</span>
                            @elseif ($isLotReviewPending)
                                <span class="status-badge status-pending_admin"><i class="fa-solid fa-map-pin"></i> รูป LOT รอตรวจ</span>
                            @elseif ($isPhotoRejected)
                                <span class="status-badge status-problem"><i class="fa-solid fa-rotate-left"></i> ตีกลับ / รอส่งใหม่</span>
                            @elseif ($hasDraftPhotos)
                                <span class="status-badge status-installing"><i class="fa-solid fa-images"></i> LOT ผ่าน / รูปงานยังไม่ส่ง</span>
                            @elseif ($isLotApproved)
                                <span class="status-badge status-confirmed"><i class="fa-solid fa-circle-check"></i> LOT ผ่าน / รอรูปงาน</span>
                            @else
                                @php
                                    $statusClass = 'status-' . $booking->status;
                                    $statusName = 'รอยืนยัน';
                                    switch($booking->status) {
                                        case 'pending_admin': $statusName = 'รอยืนยัน'; break;
                                        case 'confirmed': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                        case 'assigned': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
                                        case 'installing': $statusName = 'กำลังติดตั้ง'; break;
                                        case 'completed': $statusName = 'ติดตั้งสำเร็จ'; break;
                                        case 'cancelled': $statusName = 'ยกเลิก'; break;
                                        case 'problem': $statusName = 'มีปัญหา'; break;
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusName }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <a href="{{ route('admin.bookings.show', $booking) }}{{ $isPhotoReviewPending ? '#installation-review' : '' }}" class="{{ $isPhotoReviewPending ? 'btn-primary' : 'btn-secondary' }}" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="{{ $isPhotoReviewPending ? 'เปิดตรวจและอนุมัติรูป' : 'เปิดดูรายละเอียด' }}">
                                    <i class="fa-solid {{ $isPhotoReviewPending ? 'fa-camera-retro' : 'fa-eye' }}"></i> {{ $isWorkReviewPending ? 'ตรวจรูปงาน' : ($isLotReviewPending ? 'ตรวจรูป LOT' : 'ดูรายละเอียด') }}
                                </a>
                                @if (!$booking->collect_front_store && !$booking->payment_slip_path)
                                    <form action="{{ route('admin.bookings.payment_slip', $booking) }}" method="POST" enctype="multipart/form-data" style="margin:0;">
                                        @csrf
                                        <label class="btn-secondary" style="padding:6px 12px;font-size:13px;border-radius:10px;cursor:pointer;margin:0;" title="แนบรูปสลิปการชำระเงิน">
                                            <i class="fa-solid fa-receipt"></i> แนบสลิป
                                            <input type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" required hidden onchange="this.form.submit()">
                                        </label>
                                    </form>
                                @endif
                                @if($booking->status === 'pending_admin')
                                    @if ($booking->payment_slip_path || $booking->collect_front_store)
                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" style="margin:0;">
                                            @csrf
                                            <button type="submit" class="btn-primary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="ยืนยันการจอง">
                                                <i class="fa-solid fa-check"></i> ยืนยัน
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="btn-secondary" style="padding:6px 12px;font-size:13px;border-radius:10px;opacity:.6;cursor:not-allowed;" disabled title="กรุณาแนบสลิปก่อนยืนยัน">
                                            <i class="fa-solid fa-lock"></i> แนบสลิปก่อน
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-box-open" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                            ไม่พบข้อมูลการจองในระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-cute">
        {{ $bookings->links() }}
    </div>

    @include('components.image-lightbox')
@endsection
