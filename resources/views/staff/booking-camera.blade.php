@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(280px,440px) 1fr;gap:18px}.upload-stack{display:grid;gap:18px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:22px;padding:20px}.panel-lot{border-top:5px solid var(--primary)}.panel-after{border-top:5px solid #4ECDC4}.panel-task-tent{border-top-color:#e5b700;background:#fffdf3}.panel-task-counter{border-top-color:#e66bcf;background:#fff6fd}.panel-task-other{border-top-color:#39a9db;background:#f5fbff}.task-band{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:-20px -20px 16px;padding:11px 16px;border-radius:16px 16px 0 0;font-weight:900}.panel-task-tent .task-band{background:#ffe873;color:#604d00}.panel-task-counter .task-band{background:#f2a4e8;color:#6f1d63}.panel-task-other .task-band{background:#b9e7f8;color:#07546f}.task-band small{font-size:11px;font-weight:800;opacity:.82}.back-btn{width:40px;height:40px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}.pick{min-height:95px;border:2px dashed var(--border-cute);border-radius:18px;background:#fff;color:var(--text-dark);font:inherit;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:8px;font-weight:800;cursor:pointer;text-align:center}.pick:active{transform:scale(.98)}.pick i{font-size:28px;color:var(--primary-hover)}.browser-camera-pick{display:none}.file-input{display:none}.thumb-section+.thumb-section{margin-top:20px;padding-top:18px;border-top:1px dashed var(--border-cute)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}.thumb-card{position:relative;min-width:0}.thumb{position:relative;width:100%;border:0;background:none;padding:0}.thumb img{width:100%;height:125px;object-fit:cover;border-radius:14px;border:1px solid var(--border-cute);display:block}.thumb span{position:absolute;left:6px;bottom:6px;padding:4px 7px;border-radius:999px;background:rgba(255,255,255,.92);font-size:10px;font-weight:800}.thumb-delete{position:absolute;top:6px;right:6px;z-index:2;width:34px;height:34px;border:0;border-radius:50%;background:rgba(180,35,24,.94);color:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 3px 10px rgba(0,0,0,.2)}.thumb-delete:active{transform:scale(.94)}.camera-modal{position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(20,20,28,.86)}.camera-modal.is-open{display:flex}.camera-dialog{width:min(100%,680px);padding:16px;border-radius:22px;background:#11131a;color:#fff}.camera-video{display:block;width:100%;max-height:68vh;object-fit:contain;border-radius:16px;background:#000}.camera-actions{display:flex;gap:10px;margin-top:14px}.camera-actions button{flex:1}.camera-error{display:none;padding:18px;text-align:center;color:#ffd3d3}@media(hover:hover) and (pointer:fine){.native-camera-pick{display:none}.browser-camera-pick{display:flex}}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.upload-choice{grid-template-columns:1fr 1fr}.task-band{margin:-20px -20px 14px}}
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
                <form class="panel photo-upload-form panel-lot" data-camera-key="lot_number" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', $booking) }}">
                    @csrf
                    <input type="hidden" name="photo_type" value="lot_number">
                    <h2 style="font-size:18px;margin:0">รูปเลข LOT</h2>
                    <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายให้เห็นเลขแผงชัดเจน แนบหลายรูปและเพิ่มซ้ำได้</p>
                    <div class="upload-choice">
                        <label class="pick native-camera-pick" data-native-camera-trigger for="camera_lot_number"><i class="fa-solid fa-camera"></i>ถ่ายรูปด้วยกล้อง</label>
                        <button class="pick browser-camera-pick" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูป</button>
                        <input class="file-input camera-input" type="file" id="camera_lot_number" name="camera_photo" accept="image/*;capture=camera" capture="environment">
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
                    $taskPanelClass = match ($task->task_type) {
                        \App\Models\DeliveryTask::TYPE_TENT => 'panel-task-tent',
                        \App\Models\DeliveryTask::TYPE_COUNTER => 'panel-task-counter',
                        default => 'panel-task-other',
                    };
                    $taskTitle = match ($task->task_type) {
                        \App\Models\DeliveryTask::TYPE_TENT => 'Tent (เต็นท์)',
                        \App\Models\DeliveryTask::TYPE_COUNTER => 'Counter (เคาน์เตอร์)',
                        \App\Models\DeliveryTask::TYPE_OTHER => 'Other (อุปกรณ์อื่น)',
                        default => $task->typeLabel(),
                    };
                @endphp
                @if (!$lotApproved)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center;opacity:.72;">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>ขั้นตอนถัดไป</small></div>
                        <i class="fa-solid fa-lock" style="font-size:38px;color:var(--text-muted);"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">รูปงานติดตั้งยังถูกล็อก</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">ต้องให้ Admin อนุมัติรูปเลข LOT ก่อน</p>
                    </div>
                @elseif ($taskFinished)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center;">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>อนุมัติแล้ว</small></div>
                        <i class="fa-solid fa-circle-check" style="font-size:38px;color:#28a745;"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">งานเสร็จสมบูรณ์แล้ว</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">Admin อนุมัติงานติดตั้งนี้เรียบร้อยแล้ว</p>
                    </div>
                @elseif ($taskSubmitted)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center;">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>ส่งแล้ว</small></div>
                        <i class="fa-solid fa-hourglass-half" style="font-size:38px;color:#6f42c1;"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px;">รอ Admin อนุมัติ</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px;">ส่งงานติดตั้งนี้ให้ Admin ตรวจสอบแล้ว</p>
                    </div>
                @else
                    <form class="panel photo-upload-form panel-after {{ $taskPanelClass }}" data-camera-key="after_{{ $task->id }}" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', [$booking, $task]) }}">
                        @csrf
                        <input type="hidden" name="photo_type" value="after">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>รูปหลังติดตั้ง</small></div>
                        <h2 style="font-size:18px;margin:0">เพิ่มรูปส่งงาน</h2>
                        <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายภาพงานที่ติดตั้งเสร็จแล้ว แนบหลายรูปและเพิ่มซ้ำได้</p>
                        <div class="upload-choice">
                            <label class="pick native-camera-pick" data-native-camera-trigger for="camera_after_{{ $task->id }}"><i class="fa-solid fa-camera"></i>ถ่ายรูปด้วยกล้อง</label>
                            <button class="pick browser-camera-pick" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูป</button>
                            <input class="file-input camera-input" type="file" id="camera_after_{{ $task->id }}" name="camera_photo" accept="image/*;capture=camera" capture="environment">
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
                            <div class="thumb-card">
                                <button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}">
                                    <img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปเลข LOT">
                                    <span>รูปเลข LOT</span>
                                </button>
                                @if(in_array($photo->ocr_status, ['draft', 'rejected'], true))
                                    <form method="POST" action="{{ route('staff.bookings.photos.destroy', [$booking, $photo]) }}" onsubmit="return confirm('ลบรูปเลข LOT ใบนี้ใช่หรือไม่?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="thumb-delete" type="submit" title="ลบรูป" aria-label="ลบรูปเลข LOT"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                @endif
                            </div>
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
                                    <div class="thumb-card">
                                        <button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}">
                                            <img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปงาน{{ $task->typeLabel() }}">
                                            <span>{{ $task->typeLabel() }}</span>
                                        </button>
                                        @if(!in_array($task->status, ['photo_uploaded', 'completed'], true))
                                            <form method="POST" action="{{ route('staff.bookings.photos.destroy', [$booking, $photo]) }}" onsubmit="return confirm('ลบรูปงาน{{ $task->typeLabel() }}ใบนี้ใช่หรือไม่?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="thumb-delete" type="submit" title="ลบรูป" aria-label="ลบรูปงาน{{ $task->typeLabel() }}"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
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
            <div class="camera-error" id="camera-error">Browser นี้ไม่รองรับกล้องสด กรุณากดปุ่มถ่ายรูปด้วยกล้องมือถือ</div>
            <canvas id="camera-canvas" hidden></canvas>
            <div class="camera-actions">
                <button class="btn-large btn-large-secondary" type="button" id="camera-close"><i class="fa-solid fa-xmark"></i> ปิด</button>
                <button class="btn-large btn-large-primary" type="button" id="camera-native" style="display:none;"><i class="fa-solid fa-camera"></i> ถ่ายรูปด้วยกล้องมือถือ</button>
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
    const isAndroidInAppBrowser = /Android/i.test(navigator.userAgent)
        && (/Line\//i.test(navigator.userAgent)
            || /FBAN|FBAV|Instagram/i.test(navigator.userAgent)
            || /;\s*wv\)/i.test(navigator.userAgent));

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
        captureButton.style.display = 'flex';
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

    const openBrowserCamera = async (form) => {
        activeForm = form;
        const cameraInput = form.querySelector('.camera-input');

        if (!navigator.mediaDevices?.getUserMedia || typeof DataTransfer === 'undefined') {
            cameraInput.click();
            return;
        }

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        errorBox.style.display = 'none';
        video.style.display = 'block';
        nativeCameraButton.style.display = 'none';
        captureButton.style.display = 'flex';

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
            captureButton.style.display = 'none';
        }
    };

    document.querySelectorAll('[data-native-camera-trigger]').forEach(trigger => {
        trigger.addEventListener('click', event => {
            if (!isAndroidInAppBrowser) return;

            event.preventDefault();
            const cameraKey = trigger.closest('.photo-upload-form')?.dataset.cameraKey;
            const externalUrl = new URL(window.location.href);
            externalUrl.searchParams.set('direct_camera', cameraKey || 'lot_number');
            window.location.href = `intent://${externalUrl.host}${externalUrl.pathname}${externalUrl.search}#Intent;scheme=https;package=com.android.chrome;end`;
        });
    });

    document.querySelectorAll('.photo-upload-form').forEach(form => {
        const cameraInput = form.querySelector('.camera-input');
        const galleryInput = form.querySelector('.gallery-input');
        const submitButton = form.querySelector('button[type="submit"]');

        form.querySelector('[data-gallery-trigger]').addEventListener('click', () => galleryInput.click());
        form.querySelector('[data-camera-trigger]').addEventListener('click', () => openBrowserCamera(form));

        cameraInput.addEventListener('change', () => {
            updateSelection(form);
            if (!cameraInput.files?.length) return;

            submitButton.disabled = true;
            form.querySelector('.selection').textContent = 'กำลังอัปโหลดรูป...';
            form.requestSubmit();
        });
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
        const capturedForm = activeForm;
        const scale = Math.min(1, 1920 / video.videoWidth, 1920 / video.videoHeight);
        canvas.width = Math.max(1, Math.round(video.videoWidth * scale));
        canvas.height = Math.max(1, Math.round(video.videoHeight * scale));
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(blob => {
            if (!blob) return;
            const file = new File([blob], `camera-${Date.now()}.jpg`, { type: 'image/jpeg' });
            putFiles(capturedForm.querySelector('.camera-input'), [file]);
            updateSelection(capturedForm);
            stopCamera();
            capturedForm.requestSubmit();
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

    const directCameraKey = new URLSearchParams(window.location.search).get('direct_camera');
    const directCameraForm = directCameraKey
        ? Array.from(document.querySelectorAll('.photo-upload-form'))
            .find(form => form.dataset.cameraKey === directCameraKey)
        : null;
    if (directCameraForm) {
        window.setTimeout(() => openBrowserCamera(directCameraForm), 250);
    }
});
</script>
@endsection
