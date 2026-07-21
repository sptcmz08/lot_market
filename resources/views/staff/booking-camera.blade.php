@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(280px,440px) 1fr;gap:18px}.upload-stack{display:grid;gap:18px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:22px;padding:20px}.panel-lot{border-top:5px solid var(--primary)}.panel-after{border-top:5px solid #4ECDC4}.back-btn{width:40px;height:40px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}.pick{min-height:95px;border:2px dashed var(--border-cute);border-radius:18px;background:#fff;color:var(--text-dark);font:inherit;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:8px;font-weight:800;cursor:pointer;text-align:center}.pick:active{transform:scale(.98)}.pick i{font-size:28px;color:var(--primary-hover)}.file-input{position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;pointer-events:none}.thumb-section+.thumb-section{margin-top:20px;padding-top:18px;border-top:1px dashed var(--border-cute)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}.thumb{position:relative;border:0;background:none;padding:0}.thumb img{width:100%;height:125px;object-fit:cover;border-radius:14px;border:1px solid var(--border-cute);display:block}.thumb span{position:absolute;left:6px;bottom:6px;padding:4px 7px;border-radius:999px;background:rgba(255,255,255,.92);font-size:10px;font-weight:800}.camera-modal{position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(20,20,28,.86)}.camera-modal.is-open{display:flex}.camera-dialog{width:min(100%,680px);padding:16px;border-radius:22px;background:#11131a;color:#fff}.camera-video{display:block;width:100%;max-height:68vh;object-fit:contain;border-radius:16px;background:#000}.camera-actions{display:flex;gap:10px;margin-top:14px}.camera-actions button{flex:1}.camera-error{display:none;padding:18px;text-align:center;color:#ffd3d3}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.upload-choice{grid-template-columns:1fr 1fr}}
</style>
@endsection

@section('content')
    @php
        $photos = $booking->deliveryTasks->flatMap->photos->sortByDesc('id');
        $lotPhotos = $photos->where('photo_type', 'lot_number');
        $afterPhotos = $photos->where('photo_type', 'after');
        $lotApproved = $lotPhotos->contains('ocr_status', 'approved');
        $lotSubmitted = $lotPhotos->contains('ocr_status', 'submitted');
        $lotRejection = $booking->deliveryTasks->pluck('problem_note')
            ->filter(fn ($note) => str_starts_with((string) $note, 'ตีกลับรูป LOT โดยแอดมิน:'))
            ->first();
    @endphp
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px"><a class="back-btn" href="{{ route('staff.bookings.index') }}"><i class="fa-solid fa-arrow-left"></i></a><div><h1 style="font-size:22px;margin:0">เพิ่มรูปส่งงาน</h1><small style="color:var(--text-muted)">{{ $booking->shop_name }} · {{ $booking->lots->pluck('lot_code')->implode(', ') }}</small></div></div>
    @if($errors->any())<div class="alert-cute alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>@endif
    <div class="camera-grid">
        <div class="upload-stack">
            <!-- Lot Number Upload Section -->
            @if ($lotApproved)
                <div class="panel panel-lot" style="text-align:center;">
                    <i class="fa-solid fa-circle-check" style="font-size:38px;color:#14833b;"></i>
                    <h2 style="font-size:18px;margin:10px 0 4px;">รูปเลข LOT อนุมัติแล้ว</h2>
                    <p style="margin:0;color:var(--text-muted);font-size:13px;">สามารถถ่ายและแนบรูปงานติดตั้งได้แล้ว</p>
                </div>
            @elseif ($lotSubmitted)
                <div class="panel panel-lot" style="text-align:center;">
                    <i class="fa-solid fa-hourglass-half" style="font-size:38px;color:#8a6500;"></i>
                    <h2 style="font-size:18px;margin:10px 0 4px;">ส่งรูป LOT แล้ว</h2>
                    <p style="margin:0;color:var(--text-muted);font-size:13px;">กำลังรอ Admin อนุมัติ จึงยังแนบรูปงานติดตั้งไม่ได้</p>
                </div>
            @else
                <form class="panel photo-upload-form panel-lot" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', $booking) }}">
                    @csrf
                    <input type="hidden" name="photo_type" value="lot_number">
                    <h2 style="font-size:18px;margin:0">รูปเลข LOT</h2>
                    <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายให้เห็นเลขแผงชัดเจน แนบหลายรูปและเพิ่มซ้ำได้</p>
                    <div class="upload-choice">
                        <button class="pick" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูป</button>
                        <input class="file-input camera-input" type="file" id="camera_lot_number" name="camera_photo" accept="image/*">
                        <button class="pick" type="button" data-gallery-trigger><i class="fa-solid fa-images"></i>แนบรูป</button>
                        <input class="file-input gallery-input" type="file" id="gallery_lot_number" name="photos[]" accept="image/*" multiple>
                    </div>
                    <div class="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:12px">ยังไม่ได้เลือกรูป</div>
                    <button class="btn-large btn-large-primary" type="submit"><i class="fa-solid fa-plus"></i> เพิ่มรูปเลข LOT</button>
                </form>
            @endif

            <!-- Work/After Upload Sections for each task -->
            @foreach($booking->deliveryTasks as $task)
                @php
                    $taskFinished = $task->status === 'completed';
                    $taskSubmitted = $task->status === 'photo_uploaded';
                @endphp
                @if (!$lotApproved)
                    <div class="panel panel-after" style="text-align:center;opacity:.72;border-top-color:#bbb;">
                        <i class="fa-solid fa-lock" style="font-size:38px;color:var(--text-muted);"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">รูปงานติดตั้ง ({{ $task->typeLabel() }}) ยังถูกล็อก</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">ต้องให้ Admin อนุมัติรูปเลข LOT ก่อน</p>
                    </div>
                @elseif ($taskFinished)
                    <div class="panel panel-after" style="text-align:center;border-top-color:#28a745;">
                        <i class="fa-solid fa-circle-check" style="font-size:38px;color:#28a745;"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">{{ $task->typeLabel() }} เสร็จสมบูรณ์แล้ว</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">Admin อนุมัติงานติดตั้งนี้เรียบร้อยแล้ว</p>
                    </div>
                @elseif ($taskSubmitted)
                    <div class="panel panel-after" style="text-align:center;border-top-color:#6f42c1;">
                        <i class="fa-solid fa-hourglass-half" style="font-size:38px;color:#6f42c1;"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">{{ $task->typeLabel() }} รออนุมัติ</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">ส่งงานติดตั้งนี้ให้ Admin ตรวจสอบแล้ว</p>
                    </div>
                @else
                    <form class="panel photo-upload-form panel-after" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', [$booking, $task]) }}">
                        @csrf
                        <input type="hidden" name="photo_type" value="after">
                        <h2 style="font-size:18px;margin:0">รูปหลังติดตั้ง ({{ $task->typeLabel() }})</h2>
                        <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายภาพงานที่ติดตั้งเสร็จแล้ว แนบหลายรูปและเพิ่มซ้ำได้</p>
                        <div class="upload-choice">
                            <button class="pick" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูป</button>
                            <input class="file-input camera-input" type="file" id="camera_after_{{ $task->id }}" name="camera_photo" accept="image/*">
                            <button class="pick" type="button" data-gallery-trigger><i class="fa-solid fa-images"></i>แนบรูป</button>
                            <input class="file-input gallery-input" type="file" id="gallery_after_{{ $task->id }}" name="photos[]" accept="image/*" multiple>
                        </div>
                        <div class="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:12px">ยังไม่ได้เลือกรูป</div>
                        <button class="btn-large btn-large-success" type="submit"><i class="fa-solid fa-plus"></i> เพิ่มรูปงาน{{ $task->typeLabel() }}</button>
                    </form>
                @endif
            @endforeach
        </div>
        <div class="panel">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:14px">
                <h2 style="font-size:18px;margin:0">รูปที่เพิ่มแล้ว</h2>
                <strong>{{ $photos->whereIn('photo_type',['lot_number','after'])->count() }} รูป</strong>
            </div>

            <!-- Lot Photos Group -->
            <div class="thumb-section">
                <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                    <strong>รูปเลข LOT (ป้ายเลขแผง)</strong>
                    <small>{{ $lotPhotos->count() }} รูป</small>
                </div>
                @if($lotPhotos->isEmpty())
                    <div style="padding:22px 10px;text-align:center;color:var(--text-muted);background:var(--bg-page);border-radius:14px">ยังไม่มีรูป</div>
                @else
                    <div class="thumbs">
                        @foreach($lotPhotos as $photo)
                            <button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}">
                                <img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปเลข LOT">
                                <span>รูปเลข LOT</span>
                            </button>
                        @endforeach
                    </div>
                @endif
                
                @if (!$lotApproved)
                    @if ($lotRejection)
                        <div style="margin-top:10px;padding:11px;border-radius:12px;background:#ffe1e1;color:#b42318;font-weight:700;font-size:13px;">
                            เหตุผลที่ตีกลับ: {{ str($lotRejection)->after('ตีกลับรูป LOT โดยแอดมิน:')->trim() }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('staff.bookings.submit_lot',$booking) }}" style="margin-top:12px">
                        @csrf
                        <button class="btn-large btn-large-primary" type="submit" @disabled($lotPhotos->isEmpty() || $lotSubmitted) onclick="return confirm('ยืนยันส่งรูป LOT ให้ Admin ตรวจสอบ?')">
                            <i class="fa-solid fa-paper-plane"></i> {{ $lotSubmitted ? 'ส่งรูป LOT แล้ว / รออนุมัติ' : 'ส่งรูป LOT ให้ Admin' }}
                        </button>
                    </form>
                    @if($lotPhotos->isEmpty())
                        <small style="display:block;text-align:center;color:var(--text-muted);margin-top:6px;font-size:12px;">ต้องเพิ่มรูปเลข LOT อย่างน้อย 1 รูปก่อนส่ง</small>
                    @endif
                @endif
            </div>

            <!-- After/Work Photos for each task -->
            @if ($lotApproved)
                @foreach($booking->deliveryTasks as $task)
                    @php
                        $taskAfterPhotos = $task->photos->where('photo_type', 'after');
                        $taskFinished = $task->status === 'completed';
                        $taskSubmitted = $task->status === 'photo_uploaded';
                        $taskRejection = $task->problem_note && str_starts_with((string)$task->problem_note, 'ตีกลับรูปงานโดยแอดมิน: ') ? $task->problem_note : null;
                    @endphp
                    <div class="thumb-section">
                        <div style="display:flex;justify-content:space-between;margin-bottom:10px">
                            <strong>รูปผลงานติดตั้ง: {{ $task->typeLabel() }}</strong>
                            <small>{{ $taskAfterPhotos->count() }} รูป</small>
                        </div>
                        @if($taskAfterPhotos->isEmpty())
                            <div style="padding:22px 10px;text-align:center;color:var(--text-muted);background:var(--bg-page);border-radius:14px">ยังไม่มีรูป</div>
                        @else
                            <div class="thumbs">
                                @foreach($taskAfterPhotos as $photo)
                                    <button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}">
                                        <img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปงาน{{ $task->typeLabel() }}">
                                        <span>{{ $task->typeLabel() }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if ($taskRejection)
                            <div style="margin-top:10px;padding:11px;border-radius:12px;background:#ffe1e1;color:#b42318;font-weight:700;font-size:13px;">
                                เหตุผลที่ตีกลับ: {{ str($taskRejection)->after('ตีกลับรูปงานโดยแอดมิน:')->trim() }}
                            </div>
                        @endif

                        @if (!$taskFinished)
                            <form method="POST" action="{{ route('staff.bookings.submit_work', [$booking, $task]) }}" style="margin-top:12px">
                                @csrf
                                <button class="btn-large btn-large-success" type="submit" @disabled($taskAfterPhotos->isEmpty() || $taskSubmitted) onclick="return confirm('ยืนยันส่งรูปงานติดตั้ง{{ $task->typeLabel() }} ให้ Admin ตรวจสอบ?')">
                                    <i class="fa-solid fa-paper-plane"></i> {{ $taskSubmitted ? 'ส่งรูปงานแล้ว / รออนุมัติ' : 'ส่งรูปงาน'.$task->typeLabel() }}
                                </button>
                            </form>
                            @if($taskAfterPhotos->isEmpty())
                                <small style="display:block;text-align:center;color:var(--text-muted);margin-top:6px;font-size:12px;">ต้องถ่ายหรือแนบรูปงานติดตั้ง{{ $task->typeLabel() }} อย่างน้อย 1 รูปก่อนส่ง</small>
                            @endif
                        @else
                            <div style="margin-top:12px;text-align:center;color:#28a745;font-weight:bold;font-size:14px;">
                                <i class="fa-solid fa-circle-check"></i> อนุมัติสำเร็จแล้ว
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="camera-modal" id="browser-camera" aria-hidden="true">
        <div class="camera-dialog" role="dialog" aria-modal="true" aria-label="ถ่ายรูป">
            <video class="camera-video" id="camera-video" autoplay muted playsinline></video>
            <div class="camera-error" id="camera-error">ไม่สามารถเปิดกล้องผ่าน Browser ได้ กรุณาอนุญาตการใช้กล้องหรือเลือกแนบรูปแทน</div>
            <canvas id="camera-canvas" hidden></canvas>
            <div class="camera-actions">
                <button class="btn-large btn-large-secondary" type="button" id="camera-close"><i class="fa-solid fa-xmark"></i> ปิด</button>
                <button class="btn-large btn-large-secondary" type="button" id="camera-native" style="display:none;"><i class="fa-solid fa-mobile-screen"></i> เปิดกล้องของเครื่อง</button>
                <button class="btn-large btn-large-primary" type="button" id="camera-capture" disabled><i class="fa-solid fa-camera"></i> ถ่ายภาพนี้</button>
            </div>
        </div>
    </div>
    @include('components.image-lightbox')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('browser-camera');
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    const captureButton = document.getElementById('camera-capture');
    const nativeCameraButton = document.getElementById('camera-native');
    const closeButton = document.getElementById('camera-close');
    const errorBox = document.getElementById('camera-error');
    let activeForm = null;
    let cameraStream = null;

    const updateSelection = (form) => {
        const camera = form.querySelector('.camera-input');
        const gallery = form.querySelector('.gallery-input');
        const count = (camera.files?.length || 0) + (gallery.files?.length || 0);
        form.querySelector('.selection').textContent = count ? `เลือกแล้ว ${count} รูป` : 'ยังไม่ได้เลือกรูป';
    };

    const stopCamera = () => {
        cameraStream?.getTracks().forEach(track => track.stop());
        cameraStream = null;
        video.srcObject = null;
        captureButton.disabled = true;
        nativeCameraButton.style.display = 'none';
        errorBox.style.display = 'none';
        video.style.display = 'block';
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    const putFiles = (input, files) => {
        if (typeof DataTransfer === 'undefined') return false;
        const transfer = new DataTransfer();
        files.forEach(file => transfer.items.add(file));
        input.files = transfer.files;
        return true;
    };

    const compressImage = async (file) => {
        if (!file.type.startsWith('image/') || typeof createImageBitmap !== 'function') return file;
        try {
            const bitmap = await createImageBitmap(file);
            const scale = Math.min(1, 1920 / bitmap.width, 1920 / bitmap.height);
            const output = document.createElement('canvas');
            output.width = Math.max(1, Math.round(bitmap.width * scale));
            output.height = Math.max(1, Math.round(bitmap.height * scale));
            output.getContext('2d').drawImage(bitmap, 0, 0, output.width, output.height);
            bitmap.close();
            const blob = await new Promise(resolve => output.toBlob(resolve, 'image/jpeg', 0.86));
            if (!blob) return file;
            const name = file.name.replace(/\.[^.]+$/, '') + '.jpg';
            return new File([blob], name, { type: 'image/jpeg', lastModified: file.lastModified });
        } catch (_) {
            return file;
        }
    };

    document.querySelectorAll('.photo-upload-form').forEach(form => {
        const cameraInput = form.querySelector('.camera-input');
        const galleryInput = form.querySelector('.gallery-input');
        const submitButton = form.querySelector('button[type="submit"]');

        form.querySelector('[data-gallery-trigger]').addEventListener('click', () => galleryInput.click());
        form.querySelector('[data-camera-trigger]').addEventListener('click', async () => {
            activeForm = form;
            if (!navigator.mediaDevices?.getUserMedia || typeof DataTransfer === 'undefined') {
                cameraInput.click();
                return;
            }

            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            errorBox.style.display = 'none';
            video.style.display = 'block';
            nativeCameraButton.style.display = 'none';
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } },
                    audio: false
                });
                video.srcObject = cameraStream;
                await video.play();
                captureButton.disabled = false;
            } catch (_) {
                cameraStream?.getTracks().forEach(track => track.stop());
                cameraStream = null;
                video.srcObject = null;
                video.style.display = 'none';
                errorBox.style.display = 'block';
                nativeCameraButton.style.display = 'flex';
            }
        });

        cameraInput.addEventListener('change', () => updateSelection(form));
        galleryInput.addEventListener('change', async () => {
            const selected = Array.from(galleryInput.files || []);
            if (!selected.length) return updateSelection(form);
            form.dataset.processing = '1';
            submitButton.disabled = true;
            form.querySelector('.selection').textContent = `กำลังเตรียม ${selected.length} รูป...`;
            const compressed = await Promise.all(selected.map(compressImage));
            putFiles(galleryInput, compressed);
            delete form.dataset.processing;
            submitButton.disabled = false;
            updateSelection(form);
        });

        form.addEventListener('submit', event => {
            if (form.dataset.processing === '1') {
                event.preventDefault();
                alert('กรุณารอระบบเตรียมรูปให้เสร็จก่อน');
            }
        });
    });

    captureButton.addEventListener('click', () => {
        if (!activeForm || !video.videoWidth) return;
        const scale = Math.min(1, 1920 / video.videoWidth, 1920 / video.videoHeight);
        canvas.width = Math.max(1, Math.round(video.videoWidth * scale));
        canvas.height = Math.max(1, Math.round(video.videoHeight * scale));
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(blob => {
            if (!blob) return;
            const file = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
            putFiles(activeForm.querySelector('.camera-input'), [file]);
            updateSelection(activeForm);
            stopCamera();
        }, 'image/jpeg', 0.88);
    });

    nativeCameraButton.addEventListener('click', () => {
        const input = activeForm?.querySelector('.camera-input');
        stopCamera();
        input?.click();
    });

    closeButton.addEventListener('click', stopCamera);
    modal.addEventListener('click', event => { if (event.target === modal) stopCamera(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') stopCamera(); });
});
</script>
@endsection
