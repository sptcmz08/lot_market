@extends('layouts.staff')

@section('title', 'รายละเอียดและดำเนินการติดตั้งเต็นท์')

@section('content')
    <div style="margin-bottom: 15px;">
        <a href="{{ route('staff.tasks.index') }}" style="color: var(--text-dark); text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-left"></i> กลับหน้ารวมงานวันนี้
        </a>
    </div>

    <!-- Task Meta Card -->
    <div class="staff-card">
        <h2 style="font-size: 20px; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-tents" style="color: var(--primary);"></i> รายละเอียดงานแผง: 
            <span style="color: var(--primary-hover);">{{ $task->booking ? $task->booking->lots->pluck('lot_code')->implode(', ') : '-' }}</span>
        </h2>

        <div style="margin-top: 5px; font-size: 13px; color: var(--text-muted);">
            วันที่นัดหมาย: {{ $task->task_date->format('d/m/Y') }}
        </div>

        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 10px; border-top: 1px dashed var(--border-cute); padding-top: 12px;">
            <div class="info-row">
                <span class="info-label">ร้านค้า:</span>
                <strong class="info-value">{{ $task->booking ? $task->booking->shop_name : '-' }}</strong>
            </div>
            <div class="info-row">
                <span class="info-label">เบอร์โทรศัพท์:</span>
                <strong class="info-value">
                    <a href="tel:{{ $task->booking ? $task->booking->customer_phone : '' }}" style="color: var(--secondary); text-decoration: none;">
                        <i class="fa-solid fa-phone"></i> {{ $task->booking ? $task->booking->customer_phone : '-' }}
                    </a>
                </strong>
            </div>
            <div class="info-row">
                <span class="info-label">ขนาดเต็นท์เช่า:</span>
                <strong class="info-value">{{ $task->booking ? $task->booking->tent_size : '-' }} เมตร</strong>
            </div>
            <div class="info-row">
                <span class="info-label">ขนาดเคาน์เตอร์:</span>
                <strong class="info-value">{{ $task->booking ? ($task->booking->counter_size ?: 'ไม่มี') : '-' }}</strong>
            </div>
        </div>

        @if ($task->booking && $task->booking->customer_note)
            <div style="background-color: var(--bg-page); border-radius: 12px; padding: 10px 12px; font-size: 13px; border: 1px dashed var(--border-cute);">
                <strong style="display:block;margin-bottom:2px;"><i class="fa-solid fa-comment-dots"></i> คำสั่งพิเศษจากลูกค้า:</strong>
                {{ $task->booking->customer_note }}
            </div>
        @endif

        @if ($task->booking && $task->booking->admin_note)
            <div style="background-color: #FFFDF0; border-radius: 12px; padding: 10px 12px; font-size: 13px; border: 1px dashed #FFEBA3; color: #856404;">
                <strong style="display:block;margin-bottom:2px;"><i class="fa-solid fa-bell"></i> คำสั่งด่วนจากผู้จัดการ:</strong>
                {{ $task->booking->admin_note }}
            </div>
        @endif
    </div>

    <!-- GPS Geolocation Detector status banner -->
    <div class="alert-cute alert-success" style="background-color: #E8F4FD; color: #004085; font-size:13px; margin-bottom: 15px;" id="gps-status-banner">
        <i class="fa-solid fa-spinner fa-spin" id="gps-icon"></i>
        <div id="gps-text">กำลังค้นหาและระบุตำแหน่งดาวเทียม (GPS)...</div>
    </div>

    <!-- Active workflow states -->
    @if ($task->status === 'waiting')
        <!-- 1. WAITING FOR START -->
        <div class="cute-card" style="text-align: center; padding: 30px 20px;">
            <i class="fa-solid fa-play" style="font-size: 40px; color: var(--secondary); margin-bottom: 15px; display: block;"></i>
            <h3 style="margin-top:0;">พร้อมเข้าดำเนินงานติดตั้งแล้ว</h3>
            <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 25px;">
                เมื่อเดินทางถึงจุดแผงตลาดแล้ว โปรดกดปุ่มเริ่มงานด้านล่างเพื่อบันทึกเวลาปฏิบัติงาน
            </p>
            <form action="{{ route('staff.tasks.start', $task) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-large btn-large-primary">
                    <i class="fa-solid fa-circle-play"></i> กดเริ่มงานติดตั้งเต็นท์
                </button>
            </form>
        </div>
    @elseif ($task->status === 'started' || $task->status === 'problem')
        <!-- 2. STARTED - UPLOAD AND SUBMIT FORMS -->
        <div class="cute-card">
            <h3 class="cute-card-title" style="font-size:16px; margin-bottom:15px;">
                <i class="fa-solid fa-camera"></i> ถ่ายรูปส่งงานติดตั้ง (ขนาดไม่เกิน 10MB)
            </h3>

            <!-- Show current photo upload counters -->
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
                @php
                    $photos = $task->photos;
                    $hasLotNo = $photos->contains('photo_type', 'lot_number');
                    $hasBefore = $photos->contains('photo_type', 'before');
                    $hasAfter = $photos->contains('photo_type', 'after');
                @endphp
                <span class="status-badge @if($hasLotNo) status-completed @else status-pending @endif">
                    <i class="fa-solid @if($hasLotNo) fa-check @else fa-circle-xmark @endif"></i> ป้ายเลขแผง
                </span>
                <span class="status-badge @if($hasBefore) status-completed @else status-blocked @endif">
                    <i class="fa-solid @if($hasBefore) fa-check @else fa-circle @endif"></i> ก่อนติดตั้ง (ถ้ามี)
                </span>
                <span class="status-badge @if($hasAfter) status-completed @else status-pending @endif">
                    <i class="fa-solid @if($hasAfter) fa-check @else fa-circle-xmark @endif"></i> หลังติดตั้งเสร็จ
                </span>
            </div>

            <!-- Upload forms -->
            <form action="{{ route('staff.tasks.upload_photo', $task) }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 25px;">
                @csrf
                <!-- GPS coords dynamically injected by JS -->
                <input type="hidden" name="latitude" class="gps-lat">
                <input type="hidden" name="longitude" class="gps-lng">

                <div class="cute-input-group">
                    <label class="cute-label" for="photo_type">ประเภทรูปภาพถ่าย *</label>
                    <select id="photo_type" name="photo_type" class="cute-select" required>
                        <option value="" disabled selected>เลือกชนิดภาพถ่าย</option>
                        <option value="lot_number">1. ป้ายเลขแผงตลาด (Lot ID)</option>
                        <option value="before">2. ภาพสภาพสถานที่ก่อนประกอบ (Before)</option>
                        <option value="after">3. ภาพเต็นท์เสร็จสมบูรณ์ (After)</option>
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="photo">เลือกรูปภาพ / ถ่ายภาพ *</label>
                    <input type="file" id="photo" name="photo" class="cute-input" accept="image/*" capture="environment" required>
                    <small style="color:var(--text-muted);font-size:11px;">* หากใช้งานบนมือถือ ระบบจะเปิดกล้องถ่ายภาพอัตโนมัติ</small>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="note">บันทึกเพิ่มเติมจากภาพ (ถ้ามี)</label>
                    <input type="text" id="note" name="note" class="cute-input" placeholder="เช่น กิ๊บล็อคชำรุดเล็กน้อย">
                </div>

                <button type="submit" class="btn-large btn-large-secondary" style="height: 48px;">
                    <i class="fa-solid fa-cloud-arrow-up"></i> อัปโหลดรูปภาพ
                </button>
            </form>
        </div>

        <!-- Problem Report Section -->
        <div class="cute-card" style="border: 2px solid #FFE17D; background-color: #FFFDF8;">
            <h3 class="cute-card-title" style="font-size: 16px; margin-bottom: 12px; color: #856404;">
                <i class="fa-solid fa-triangle-exclamation"></i> รายงานความผิดปกติ / ปัญหาหน้างาน
            </h3>
            
            <form action="{{ route('staff.tasks.problem', $task) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="latitude" class="gps-lat">
                <input type="hidden" name="longitude" class="gps-lng">

                <div class="cute-input-group">
                    <label class="cute-label" for="problem_note" style="color: #856404;">คำอธิบายปัญหา *</label>
                    <textarea id="problem_note" name="problem_note" class="cute-textarea" rows="2" placeholder="เช่น แผงตลาดมีของระเกะระกะประกอบไม่ได้, ขาดเต็นท์ขนาดนี้, ล็อคเหล็กชำรุด..." required>{{ old('problem_note') }}</textarea>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="problem_photo" style="color: #856404;">แนบรูปหลักฐานปัญหา (ถ้ามี)</label>
                    <input type="file" id="problem_photo" name="problem_photo" class="cute-input" accept="image/*" capture="environment">
                </div>

                <button type="submit" class="btn-large btn-large-danger" style="height: 48px;" onsubmit="return confirm('ยืนยันแจ้งปัญหา? ระบบจะระงับงานติดตั้งชั่วคราวและแจ้งแอดมิน');">
                    <i class="fa-solid fa-circle-exclamation"></i> บันทึกรายงานปัญหาหน้างาน
                </button>
            </form>
        </div>

    @endif

    <!-- Uploaded Photos Grid -->
    @if ($task->photos->isNotEmpty())
        <div class="cute-card">
            <h3 class="cute-card-title" style="font-size: 16px; margin-bottom: 15px;">
                <i class="fa-solid fa-circle-check"></i> รูปถ่ายที่อัปโหลดไว้แล้วในระบบ
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px;">
                @foreach ($task->photos as $ph)
                    <div style="border: 2px solid var(--border-cute); border-radius: 14px; overflow:hidden; background-color: var(--bg-page); text-align: center;">
                        <a href="{{ Storage::url($ph->image_path) }}" target="_blank">
                            <img src="{{ Storage::url($ph->image_path) }}" style="width:100%; height:90px; object-fit:cover; display:block;" alt="ภาพประวัติ">
                        </a>
                        <div style="font-size: 11px; padding: 4px; font-weight:700;">
                            @if($ph->photo_type === 'lot_number') 📝 ป้ายแผง
                            @elseif($ph->photo_type === 'before') 🛠️ ก่อนประกอบ
                            @elseif($ph->photo_type === 'after') ✅ เสร็จสิ้น
                            @elseif($ph->photo_type === 'problem') ⚠️ ปัญหา
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

@endsection

@section('sticky_footer')
    <!-- Sticky bottom button at the bottom of viewport for thumbs-access -->
    @if ($task->status === 'started' || $task->status === 'problem')
        <div class="sticky-bottom-bar">
            <form action="{{ route('staff.tasks.complete', $task) }}" method="POST" style="margin: 0; width: 100%;">
                @csrf
                <button type="submit" class="btn-large btn-large-success" style="width: 100%;">
                    <i class="fa-solid fa-circle-check"></i> ส่งงานติดตั้งเสร็จสมบูรณ์
                </button>
            </form>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gpsStatus = document.getElementById('gps-status-banner');
        const gpsIcon = document.getElementById('gps-icon');
        const gpsText = document.getElementById('gps-text');
        
        const latFields = document.querySelectorAll('.gps-lat');
        const lngFields = document.querySelectorAll('.gps-lng');

        // Check if browser supports geolocation
        if ("geolocation" in navigator) {
            // Request geolocation
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                // Inject GPS coords into all hidden fields
                latFields.forEach(f => f.value = lat);
                lngFields.forEach(f => f.value = lng);
                
                // Update banner status
                gpsStatus.style.backgroundColor = '#E2F9E9';
                gpsStatus.style.color = '#1E7E34';
                gpsIcon.className = 'fa-solid fa-circle-check';
                gpsText.innerText = `ระบุพิกัดดาวเทียมสำเร็จ (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
            }, function(error) {
                // If location permission denied or error
                gpsStatus.style.backgroundColor = '#FFF3CD';
                gpsStatus.style.color = '#856404';
                gpsIcon.className = 'fa-solid fa-circle-info';
                gpsText.innerText = 'ระบบไม่สามารถระบุพิกัดดาวเทียมได้ (รูปภาพของคุณจะถูกเซฟโดยไม่มีจีพีเอส)';
            }, {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            });
        } else {
            gpsStatus.style.backgroundColor = '#F8D7DA';
            gpsStatus.style.color = '#721C24';
            gpsIcon.className = 'fa-solid fa-circle-xmark';
            gpsText.innerText = 'อุปกรณ์ของคุณไม่รองรับการจับพิกัดจีพีเอส';
        }
    });
</script>
@endsection

@section('body_class')
    @if ($task->status === 'started' || $task->status === 'problem')
        has-sticky-bottom
    @endif
@endsection
