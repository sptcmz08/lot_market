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

    @media (max-width: 767px) {
        .show-action-bar {
            flex-direction: column;
            align-items: stretch !important;
            gap: 8px !important;
        }
        .show-action-buttons {
            width: 100%;
            flex-direction: column;
        }
        .show-action-buttons form,
        .show-action-buttons a,
        .show-action-buttons button,
        .show-action-buttons label {
            width: 100%;
            justify-content: center;
            box-sizing: border-box;
        }
        .reject-form-group {
            width: 100%;
            flex-direction: column;
            align-items: stretch !important;
        }
        .reject-form-group input {
            width: 100% !important;
        }
        .step-review-box {
            padding: 12px !important;
            border-radius: 12px !important;
        }
        .step-review-header {
            font-size: 13.5px !important;
        }
        .booking-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }
        .booking-status-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .booking-summary-grid { grid-template-columns: 1fr !important; }
    }

    .booking-status-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin: 18px 0 4px; }
    .booking-status-panel { border: 1px solid var(--border-cute); border-radius: 14px; padding: 13px; background: var(--bg-page); min-width: 0; }
    .booking-status-panel h4 { margin: 0 0 9px; font-size: 14px; }
    .booking-status-panel p { margin: 4px 0 0; color: var(--text-muted); font-size: 12px; line-height: 1.5; }
    .booking-equipment-status { display: grid; gap: 7px; }
    .booking-equipment-status-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; min-height: 30px; }
    .booking-equipment-status-row strong { font-size: 13px; }
    .booking-equipment-status-row .status-badge { padding: 4px 8px; font-size: 11px; white-space: nowrap; }
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
    <div class="show-action-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> กลับไปรายการจอง
        </a>
        <div class="show-action-buttons" style="display: flex; gap: 8px; flex-wrap: wrap;">
            @if (!$booking->collect_front_store)
                <form action="{{ route('admin.bookings.payment_slip', $booking) }}" method="POST" enctype="multipart/form-data" style="margin:0;">
                    @csrf
                    <label class="btn-secondary" style="cursor:pointer;margin:0;" title="{{ $booking->payment_slip_path ? 'เปลี่ยนรูปสลิปการชำระเงิน' : 'แนบรูปสลิปการชำระเงิน' }}">
                        <i class="fa-solid fa-receipt"></i>
                        {{ $booking->payment_slip_path ? 'เปลี่ยนสลิป' : 'แนบสลิป' }}
                        <input type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" required hidden onchange="this.form.submit()">
                    </label>
                </form>
            @endif

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

                <div class="booking-summary-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 15px 30px; margin-bottom: 20px;">
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
                                case 'assigned': $statusName = 'ยืนยันแล้ว/รอส่ง'; break;
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
                        <strong>{{ $booking->collect_front_store ? 'เก็บหน้าร้าน' : ($booking->payment_slip_path ? 'แนบสลิปแล้ว' : 'รอแนบสลิป') }}</strong>
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

                @php
                    $tasksByType = $booking->deliveryTasks->keyBy('task_type');
                    $equipmentRows = [
                        ['type' => 'tent', 'label' => 'เต็นท์', 'enabled' => $booking->tentEquipmentItems() !== []],
                        ['type' => 'counter', 'label' => 'เคาน์เตอร์', 'enabled' => $booking->counterEquipmentItems() !== []],
                    ];
                    $paymentIsPaid = $booking->collect_front_store ? (bool) $booking->front_store_collected_at : (bool) $booking->payment_slip_path;
                @endphp
                <div class="booking-status-grid">
                    <div class="booking-status-panel">
                        <h4><i class="fa-solid fa-wallet"></i> สถานะการชำระเงิน</h4>
                        <span class="status-badge {{ $paymentIsPaid ? 'status-completed' : 'status-problem' }}">
                            <i class="fa-solid {{ $paymentIsPaid ? 'fa-circle-check' : 'fa-clock' }}"></i>
                            {{ $paymentIsPaid ? 'ชำระเงินแล้ว' : 'ยังไม่ชำระเงิน' }}
                        </span>
                        <p>
                            @if ($booking->collect_front_store)
                                เก็บเงินหน้าร้าน{{ $booking->front_store_collected_at ? 'แล้ว' : ' (รอเก็บ)' }}
                            @elseif ($booking->payment_slip_path)
                                แนบสลิปแล้ว
                            @else
                                รอแนบสลิป
                            @endif
                        </p>
                    </div>
                    <div class="booking-status-panel">
                        <h4><i class="fa-solid fa-list-check"></i> สถานะงานอุปกรณ์</h4>
                        <div class="booking-equipment-status">
                            @foreach ($equipmentRows as $equipment)
                                @if ($equipment['enabled'])
                                    @php
                                        $task = $tasksByType->get($equipment['type']);
                                        $taskStatusClass = match ($task?->status) {
                                            'completed' => 'status-completed',
                                            'photo_uploaded' => 'status-pending_admin',
                                            'started' => 'status-installing',
                                            'problem' => 'status-problem',
                                            default => 'status-confirmed',
                                        };
                                        $taskStatusLabel = match ($task?->status) {
                                            'completed' => 'เสร็จแล้ว',
                                            'photo_uploaded' => 'ส่งรูป/รอตรวจ',
                                            'started' => 'กำลังทำงาน',
                                            'problem' => 'มีปัญหา',
                                            'waiting' => 'รอเริ่มงาน',
                                            default => 'รอสร้างงาน',
                                        };
                                    @endphp
                                    <div class="booking-equipment-status-row">
                                        <strong>{{ $equipment['label'] }}</strong>
                                        <span class="status-badge {{ $taskStatusClass }}">{{ $taskStatusLabel }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Interactive Payment Management Block -->
                <div style="background-color: var(--bg-card); border: 2px solid var(--border-cute); border-radius: 16px; padding: 18px; margin-top: 18px;">
                    <h4 style="font-size: 15px; font-weight: 700; margin: 0 0 12px; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-money-bill-transfer" style="color: var(--primary-hover);"></i> จัดการและบันทึกการชำระเงิน
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        <!-- Option 1: Record Cash / Store Collection -->
                        <div style="background: var(--bg-page); border: 1.5px solid var(--border-cute); border-radius: 14px; padding: 14px;">
                            <strong style="display: block; font-size: 14px; margin-bottom: 8px; color: var(--text-dark);">
                                <i class="fa-solid fa-store" style="color: #059669;"></i> 1. บันทึกเก็บเงินสด / หน้าร้าน
                            </strong>
                            <form method="POST" action="{{ route('admin.dashboard.front_store.record', $booking) }}" style="display: flex; flex-direction: column; gap: 8px; margin: 0;">
                                @csrf
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <input type="number" step="0.01" min="0.01" name="front_store_collected_amount" 
                                           class="cute-input" 
                                           value="{{ old('front_store_collected_amount', $booking->front_store_collected_amount ?: ($booking->total_price ?: 0)) }}" 
                                           placeholder="ยอดเงิน (บาท)" required style="flex: 1;">
                                    <span style="font-size: 13px; font-weight: 700; color: var(--text-dark);">บาท</span>
                                </div>
                                <button type="submit" class="btn-primary" style="background: #059669; border-color: #059669; justify-content: center;">
                                    <i class="fa-solid fa-cash-register"></i> {{ $booking->front_store_collected_at ? 'แก้ไขยอดเงินสดที่บันทึก' : 'บันทึกรับเงินสดหน้าร้าน' }}
                                </button>
                            </form>
                        </div>

                        <!-- Option 2: Upload / Change Payment Slip -->
                        <div style="background: var(--bg-page); border: 1.5px solid var(--border-cute); border-radius: 14px; padding: 14px;">
                            <strong style="display: block; font-size: 14px; margin-bottom: 8px; color: var(--text-dark);">
                                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary-hover);"></i> 2. อัปโหลด / แนบสลิปโอนเงิน
                            </strong>
                            <form method="POST" action="{{ route('admin.bookings.upload_slip', $booking) }}" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 8px; margin: 0;">
                                @csrf
                                <input type="file" name="payment_slip" accept="image/jpeg,image/png,image/webp" required class="cute-input" style="padding: 6px 10px; font-size: 13px;">
                                <button type="submit" class="btn-primary" style="justify-content: center;">
                                    <i class="fa-solid fa-upload"></i> {{ $booking->payment_slip_path ? 'อัปโหลดสลิปใบใหม่แทนที่' : 'อัปโหลด / แนบสลิปชำระเงิน' }}
                                </button>
                            </form>
                        </div>
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

            <!-- Installation Photos and review actions -->
            @php
                $allTaskPhotos = $booking->deliveryTasks->flatMap->photos;
                $workPhotos = $allTaskPhotos->where('photo_type', 'after');
                $isWorkReviewPending = $booking->deliveryTasks->contains('status', 'photo_uploaded');
                $isWorkApproved = $booking->deliveryTasks->isNotEmpty()
                    && $booking->deliveryTasks->every(fn ($task) => $task->status === 'completed');
                $workRejection = $booking->deliveryTasks->pluck('problem_note')
                    ->filter(fn ($note) => str_starts_with((string) $note, 'ตีกลับรูปงานโดยแอดมิน:'))
                    ->first();
            @endphp
            <div class="cute-card" id="installation-review">
                <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
                    <h3 class="cute-card-title" style="margin:0;">
                        <i class="fa-solid fa-camera-retro"></i> ภาพถ่ายและอนุมัติงานติดตั้ง
                    </h3>
                    <span style="color:var(--text-muted);font-size:13px;">ตรวจสอบรูปงานที่พนักงานส่ง แล้วอนุมัติหรือส่งกลับแก้ไขได้</span>
                </div>
                
                <!-- Installation Photos Grouped by Task Section -->
                @foreach ($booking->deliveryTasks as $task)
                    @php
                        $taskAfterPhotos = $task->photos->where('photo_type', 'after');
                    @endphp
                    <div style="margin-bottom: 24px;">
                        <h4 style="font-size: 15px; margin: 0 0 10px; color: var(--text-dark); display: flex; align-items: center; gap: 8px;">
                            <span>🛠️ รูปงานติดตั้ง: {{ $task->typeLabel() }}</span>
                            <span style="font-size: 12px; font-weight: normal; color: var(--text-muted);">
                                สถานะ: 
                                @if($task->status === 'completed')
                                    <span style="color:#1E7E34;font-weight:700;">เสร็จสมบูรณ์</span>
                                @elseif($task->status === 'photo_uploaded')
                                    <span style="color:#6d28d9;font-weight:700;">รออนุมัติ</span>
                                @elseif($task->problem_note)
                                    <span style="color:#D35400;font-weight:700;">ตีกลับ</span>
                                @else
                                    <span style="color:var(--text-muted);">รอรูปงาน</span>
                                @endif
                                ({{ $taskAfterPhotos->count() }} รูป)
                            </span>
                        </h4>
                        @if ($taskAfterPhotos->isNotEmpty())
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px;">
                                @foreach ($taskAfterPhotos as $photo)
                                    <div style="border: 2px solid var(--border-cute); border-radius: 18px; overflow: hidden; background-color: var(--bg-page); text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                                        <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show', ['path' => $photo->image_path]) }}" data-lightbox-alt="ภาพถ่ายงานติดตั้ง" style="display:block;width:100%;">
                                            <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" style="width: 100%; height: 140px; object-fit: cover; display: block;" alt="ภาพถ่ายงานติดตั้ง">
                                        </button>
                                        <div style="padding: 10px; font-size: 13px;">
                                            @if($photo->taken_at)
                                                <small style="color: var(--text-muted); font-size: 11px;">เวลา: {{ $photo->taken_at->format('H:i น.') }}</small>
                                            @endif
                                            @if($photo->uploadedBy)
                                                <small style="display:block;color:var(--text-muted);font-size:11px;">ส่งโดย: {{ $photo->uploadedBy->name }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div style="text-align: center; padding: 20px; color: var(--text-muted); background: var(--bg-page); border-radius: 14px; font-size: 13px;">
                                ยังไม่มีรูปงานติดตั้ง{{ $task->typeLabel() }}
                            </div>
                        @endif
                    </div>
                @endforeach

                <div style="margin-top:20px;padding-top:18px;border-top:2px dashed var(--border-cute);">
                    <!-- Task-level Work Review -->
                    @foreach($booking->deliveryTasks as $task)
                        @php
                            $taskSubmitted = $task->status === 'photo_uploaded';
                            $taskFinished = $task->status === 'completed';
                            $taskRejection = $task->problem_note && str_starts_with((string)$task->problem_note, 'ตีกลับรูปงานโดยแอดมิน: ') ? $task->problem_note : null;
                        @endphp
                        <div class="step-review-box" style="padding:16px;border:2px solid var(--border-cute);border-radius:16px;margin-bottom:14px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                <strong class="step-review-header"><i class="fa-solid fa-screwdriver-wrench"></i> ตรวจและอนุมัติรูปงาน: {{ $task->typeLabel() }}</strong>
                                @if ($taskFinished)
                                    <span class="status-badge status-completed">อนุมัติแล้ว</span>
                                @elseif ($taskSubmitted)
                                    <span class="status-badge status-pending_admin">รอตรวจสอบ</span>
                                @elseif ($taskRejection)
                                    <span class="status-badge status-problem">ตีกลับแล้ว</span>
                                @else
                                    <span class="status-badge status-pending">รอสตาฟส่งรูป</span>
                                @endif
                            </div>
                            @if ($taskSubmitted)
                                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                                    <form method="POST" action="{{ route('admin.tasks.work_review.approve', $task) }}" style="margin:0;">
                                        @csrf
                                        <button class="btn-primary" type="submit" onclick="return confirm('อนุมัติรูปงานติดตั้ง{{ $task->typeLabel() }} และแสดงให้ลูกค้าเห็น?')">
                                            <i class="fa-solid fa-circle-check"></i> อนุมัติรูปงาน{{ $task->typeLabel() }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.tasks.work_review.reject', $task) }}" class="reject-form-group" style="margin:0;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                        @csrf
                                        <input class="cute-input" name="reason" required maxlength="250" placeholder="เหตุผลที่ตีกลับรูปงาน{{ $task->typeLabel() }}" style="width:240px;max-width:100%;">
                                        <button class="btn-secondary" type="submit" style="border-color:#fca5a5;color:#b42318;" onclick="return confirm('ตีกลับรูปงาน{{ $task->typeLabel() }} ให้สตาฟส่งใหม่?')">
                                            <i class="fa-solid fa-rotate-left"></i> ตีกลับ
                                        </button>
                                    </form>
                                </div>
                            @elseif ($taskRejection)
                                <div style="padding:12px 14px;border-radius:14px;background:#fff1f1;color:#b42318;font-weight:700;">
                                    เหตุผลที่ตีกลับ: {{ str($taskRejection)->after('ตีกลับรูปงานโดยแอดมิน:')->trim() }}
                                </div>
                            @elseif ($taskFinished)
                                <div style="color:#1E7E34;font-weight:700;">อนุมัติรูปงานติดตั้ง{{ $task->typeLabel() }}แล้ว และแสดงรูปให้ลูกค้าแล้ว</div>
                            @else
                                <div style="color:var(--text-muted);font-weight:700;">รอสตาฟอัปโหลดและส่งรูปงาน{{ $task->typeLabel() }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: History Logs -->
        <div>
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
                                                case 'assigned': $stName = 'ยืนยันแล้ว/รอส่ง'; break;
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
