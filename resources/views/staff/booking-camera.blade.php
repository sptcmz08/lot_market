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
    @endphp
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px"><a class="back-btn" href="{{ route('staff.bookings.index') }}"><i class="fa-solid fa-arrow-left"></i></a><div><h1 style="font-size:22px;margin:0">เพิ่มรูปส่งงาน</h1><small style="color:var(--text-muted)">{{ $booking->shop_name }} · {{ $booking->lots->pluck('lot_code')->implode(', ') }}</small></div></div>
    @if($errors->any())<div class="alert-cute alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>@endif
    <div class="camera-grid">
        <div class="upload-stack">
            @foreach(['lot_number' => ['รูปเลข LOT', 'ถ่ายให้เห็นเลขแผงชัดเจน', 'panel-lot'], 'after' => ['รูปหลังติดตั้ง', 'ถ่ายภาพงานที่ติดตั้งเสร็จแล้ว', 'panel-after']] as $type => [$title, $description, $class])
                <form class="panel photo-upload-form {{ $class }}" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos',$booking) }}">@csrf
                    <input type="hidden" name="photo_type" value="{{ $type }}">
                    <h2 style="font-size:18px;margin:0">{{ $title }}</h2><p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">{{ $description }} แนบหลายรูปและเพิ่มซ้ำได้</p>
                    <div class="upload-choice">
                        <button class="pick" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูป</button>
                        <input class="file-input camera-input" type="file" id="camera_{{ $type }}" name="camera_photo" accept="image/*" capture="environment">
                        <button class="pick" type="button" data-gallery-trigger><i class="fa-solid fa-images"></i>แนบรูป</button>
                        <input class="file-input gallery-input" type="file" id="gallery_{{ $type }}" name="photos[]" accept="image/*" multiple>
                    </div>
                    <div class="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:12px">ยังไม่ได้เลือกรูป</div>
                    <button class="btn-large {{ $type === 'lot_number' ? 'btn-large-primary' : 'btn-large-success' }}" type="submit"><i class="fa-solid fa-plus"></i> เพิ่ม{{ $title }}</button>
                </form>
            @endforeach
        </div>
        <div class="panel">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:14px"><h2 style="font-size:18px;margin:0">รูปที่เพิ่มแล้ว</h2><strong>{{ $photos->whereIn('photo_type',['lot_number','after'])->count() }} รูป</strong></div>
            @foreach([['รูปเลข LOT',$lotPhotos],['รูปหลังติดตั้ง',$afterPhotos]] as [$title,$group])
                <div class="thumb-section"><div style="display:flex;justify-content:space-between;margin-bottom:10px"><strong>{{ $title }}</strong><small>{{ $group->count() }} รูป</small></div>
                    @if($group->isEmpty())<div style="padding:22px 10px;text-align:center;color:var(--text-muted);background:var(--bg-page);border-radius:14px">ยังไม่มีรูป</div>@else<div class="thumbs">@foreach($group as $photo)<button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}"><img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="{{ $title }}"><span>{{ $title }}</span></button>@endforeach</div>@endif
                </div>
            @endforeach
            <form method="POST" action="{{ route('staff.bookings.submit',$booking) }}" style="margin-top:20px">@csrf<button class="btn-large btn-large-success" type="submit" @disabled($lotPhotos->isEmpty() || $afterPhotos->isEmpty()) onclick="return confirm('ยืนยันส่งรูป LOT และรูปหลังติดตั้งทั้งหมดให้แอดมินตรวจสอบ?')"><i class="fa-solid fa-paper-plane"></i> ส่งรูปทั้งหมดให้แอดมิน</button></form>
            @if($lotPhotos->isEmpty() || $afterPhotos->isEmpty())<small style="display:block;text-align:center;color:var(--text-muted);margin-top:8px">ต้องมีรูปเลข LOT และรูปหลังติดตั้งอย่างน้อยประเภทละ 1 รูป</small>@endif
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
