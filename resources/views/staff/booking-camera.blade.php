@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(0,560px) minmax(0,1fr);gap:18px}.upload-stack{display:grid;gap:16px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:20px;padding:18px}.panel-after{border-top:5px solid #4ecdc4}.panel-task-tent{border-top-color:#e5b700;background:#fffdf3}.panel-task-counter{border-top-color:#e66bcf;background:#fff6fd}.panel-task-other{border-top-color:#39a9db;background:#f5fbff}.task-band{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:-18px -18px 15px;padding:11px 15px;border-radius:15px 15px 0 0;font-weight:900}.panel-task-tent .task-band{background:#ffe873;color:#604d00}.panel-task-counter .task-band{background:#f2a4e8;color:#6f1d63}.panel-task-other .task-band{background:#b9e7f8;color:#07546f}.task-band small{font-size:11px;font-weight:800;opacity:.82}.task-meta{display:grid;grid-template-columns:1.2fr 1fr 1fr;gap:8px;margin:-3px 0 15px;padding:10px 11px;border:1px solid var(--border-cute);border-radius:12px;background:rgba(255,255,255,.72)}.task-meta small{display:block;color:var(--text-muted);font-size:11px;font-weight:700}.task-meta strong{display:block;margin-top:2px;font-size:13px;line-height:1.35;word-break:break-word}.task-meta .lot-value{color:var(--primary-hover)}.back-btn{width:42px;height:42px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}.pick{min-height:88px;border:2px dashed var(--border-cute);border-radius:16px;background:#fff;color:var(--text-dark);font:inherit;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:7px;font-weight:800;cursor:pointer;text-align:center}.pick:active{transform:scale(.98)}.pick i{font-size:26px;color:var(--primary-hover)}.file-input{display:none}.selection{min-height:20px}.selection-preview{display:grid;grid-template-columns:repeat(auto-fill,minmax(74px,1fr));gap:7px;margin:4px 0 12px}.selection-preview:empty{display:none}.selection-preview-item{position:relative;min-width:0}.selection-preview-item img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:9px;border:1px solid var(--border-cute);display:block}.selection-preview-zoom{display:block;width:100%;padding:0;border:0;background:none;cursor:zoom-in}.selection-delete{position:absolute;top:4px;right:4px;z-index:2;width:27px;height:27px;border:0;border-radius:50%;background:rgba(180,35,24,.94);color:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer}.thumb-section+.thumb-section{margin-top:20px;padding-top:18px;border-top:1px dashed var(--border-cute)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(125px,1fr));gap:10px}.thumb-card{position:relative;min-width:0}.thumb{position:relative;width:100%;border:0;background:none;padding:0;cursor:zoom-in}.thumb img{width:100%;height:125px;object-fit:cover;border-radius:13px;border:1px solid var(--border-cute);display:block}.thumb span{position:absolute;left:6px;bottom:6px;padding:4px 7px;border-radius:999px;background:rgba(255,255,255,.92);font-size:10px;font-weight:800}.thumb-delete{position:absolute;top:6px;right:6px;z-index:2;width:34px;height:34px;border:0;border-radius:50%;background:rgba(180,35,24,.94);color:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer}.camera-modal{position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(20,20,28,.86)}.camera-modal.is-open{display:flex}.camera-dialog{width:min(100%,680px);padding:16px;border-radius:22px;background:#11131a;color:#fff}.camera-video{display:block;width:100%;max-height:68vh;object-fit:contain;border-radius:16px;background:#000}.camera-actions{display:flex;gap:10px;margin-top:14px}.camera-actions button{flex:1}.camera-error{display:none;padding:18px;text-align:center;color:#ffd3d3;white-space:pre-line;line-height:1.6}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.panel{padding:15px;border-radius:16px}.task-band{margin:-15px -15px 14px}.task-meta{grid-template-columns:1fr 1fr}.task-meta>div:first-child{grid-column:1/-1}.upload-choice{gap:8px}.pick{min-height:82px;font-size:14px}}
</style>
@endsection

@section('content')
    @php
        $afterPhotos = $booking->deliveryTasks->flatMap->photos->where('photo_type', 'after')->sortByDesc('id');
    @endphp
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <a class="back-btn" href="{{ route('staff.bookings.index') }}" aria-label="กลับรายการจอง"><i class="fa-solid fa-arrow-left"></i></a>
        <div><h1 style="font-size:22px;margin:0">เพิ่มรูปส่งงาน</h1><small style="color:var(--text-muted)">เลือกงานที่ต้องการถ่ายรูปด้านล่าง</small></div>
    </div>
    @if($errors->any())<div class="alert-cute alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>@endif
    <div class="camera-grid">
        <div class="upload-stack">
            @foreach($booking->deliveryTasks as $task)
                @php
                    $taskAfterPhotos = $task->photos->where('photo_type', 'after')->sortByDesc('id');
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
                    $lotCodes = $booking->lots->pluck('lot_code')->implode(', ') ?: '-';
                    $tentColor = $booking->tent_color ?: '-';
                @endphp
                @if ($taskFinished)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>อนุมัติแล้ว</small></div>
                        @include('staff.partials.task-photo-meta', ['booking' => $booking, 'task' => $task, 'lotCodes' => $lotCodes, 'tentColor' => $tentColor])
                        <i class="fa-solid fa-circle-check" style="font-size:38px;color:#28a745"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px">งานเสร็จสมบูรณ์แล้ว</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px">Admin อนุมัติงานติดตั้งนี้เรียบร้อยแล้ว</p>
                    </div>
                @elseif ($taskSubmitted)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>ส่งแล้ว</small></div>
                        @include('staff.partials.task-photo-meta', ['booking' => $booking, 'task' => $task, 'lotCodes' => $lotCodes, 'tentColor' => $tentColor])
                        <i class="fa-solid fa-hourglass-half" style="font-size:38px;color:#6f42c1"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px">รอ Admin อนุมัติ</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px">ส่งงานติดตั้งนี้ให้ Admin ตรวจสอบแล้ว</p>
                    </div>
                @else
                    <form class="panel photo-upload-form panel-after {{ $taskPanelClass }}" data-camera-key="after_{{ $task->id }}" data-draft-key="{{ $booking->id }}_{{ $task->id }}" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', [$booking, $task]) }}">
                        @csrf
                        <input type="hidden" name="photo_type" value="after">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>รูปหลังติดตั้ง</small></div>
                        @include('staff.partials.task-photo-meta', ['booking' => $booking, 'task' => $task, 'lotCodes' => $lotCodes, 'tentColor' => $tentColor])

                        @if ($booking->payment_slip_path || $booking->front_store_collected_at)
                            <div style="background:#ecfdf5;border:1.5px solid #a7f3d0;color:#047857;padding:10px 14px;border-radius:14px;font-weight:700;font-size:13px;display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                                <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
                                <div>
                                    <div>ชำระเงินเรียบร้อยแล้ว</div>
                                    <small style="font-weight:600;font-size:11px;opacity:0.9;">
                                        {{ $booking->collect_front_store && $booking->front_store_collected_at ? 'บันทึกเก็บเงินสดหน้าร้านแล้ว ' . number_format((float)$booking->front_store_collected_amount, 2) . ' บาท' : 'แนบสลิปโอนเงินเรียบร้อยแล้ว' }}
                                    </small>
                                </div>
                            </div>
                        @else
                            <div class="on-site-payment-card" style="background:#fffdf0;border:2px dashed #fcd34d;border-radius:16px;padding:14px;margin-bottom:14px;">
                                <strong style="font-size:14px;color:#92400e;display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> เช็คการชำระเงินหน้าร้าน
                                </strong>
                                <div style="margin-bottom:10px;">
                                    <label style="font-size:13px;font-weight:700;color:var(--text-dark);display:block;margin-bottom:4px;">วิธีชำระเงิน:</label>
                                    <select name="on_site_payment_method" class="cute-input staff-payment-method-select" style="width:100%;font-weight:700;padding:8px 12px;border-radius:10px;font-size:14px;">
                                        <option value="cash" selected>💵 เงินสด (ไม่ต้องแนบรูปสลิป)</option>
                                        <option value="transfer">📲 โอนจ่าย / สแกน QR (ถ่ายรูปสลิปแนบ)</option>
                                    </select>
                                </div>

                                <div class="staff-cash-group" style="margin-bottom:8px;">
                                    <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:3px;">จำนวนเงินสดที่รับมา (บาท):</label>
                                    <input type="number" step="0.01" min="0.01" name="on_site_cash_amount" class="cute-input" value="{{ $booking->front_store_collected_amount ?: ($booking->total_price ?: 0) }}" style="width:100%;">
                                </div>

                                <div class="staff-slip-group" style="display:none;margin-bottom:8px;">
                                    <label style="font-size:12px;color:#b42318;font-weight:700;display:block;margin-bottom:3px;">ถ่าย/เลือกรูปสลิปโอนเงิน (จำเป็น):</label>
                                    <input type="file" name="on_site_payment_slip" accept="image/*" class="cute-input" style="padding:6px;width:100%;">
                                </div>
                            </div>
                        @endif

                        <h2 style="font-size:18px;margin:0">ถ่ายรูปงานติดตั้ง</h2>
                        <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายหรือแนบได้หลายรูป และเพิ่มรูปซ้ำได้</p>
                        <div class="upload-choice">
                            <button class="pick camera-trigger" type="button" data-camera-trigger><i class="fa-solid fa-camera"></i>ถ่ายรูปด้วยกล้อง</button>
                            <button class="pick" type="button" data-gallery-trigger><i class="fa-solid fa-images"></i>แนบรูป</button>
                            <input class="file-input camera-input" type="file" id="camera_after_{{ $task->id }}" name="camera_photo" accept="image/*" capture="environment">
                            <input class="file-input gallery-input" type="file" name="photos[]" accept="image/*" multiple>
                        </div>
                        <div class="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:4px">ยังไม่ได้เลือกรูป</div>
                        <div class="selection-preview" aria-live="polite"></div>
                        <button class="btn-large btn-large-success" type="submit"><i class="fa-solid fa-plus"></i> เพิ่มรูปงาน{{ $task->typeLabel() }}</button>
                        <button class="btn-large btn-large-primary submit-after-upload" type="submit" name="submit_after_upload" value="1" disabled style="margin-top:10px"><i class="fa-solid fa-paper-plane"></i> ส่งงานให้ Admin ทันที</button>
                    </form>
                    @if($taskAfterPhotos->isNotEmpty())
                        <form method="POST" action="{{ route('staff.bookings.submit_work', [$booking, $task]) }}" enctype="multipart/form-data" style="margin-top:10px" onsubmit="return confirm('ยืนยันส่งรูปงาน{{ $task->typeLabel() }} ให้ Admin ตรวจสอบ?')">
                            @csrf
                            @if (!$booking->payment_slip_path && !$booking->front_store_collected_at)
                                <div class="on-site-payment-card" style="background:#fffdf0;border:2px dashed #fcd34d;border-radius:16px;padding:14px;margin-bottom:10px;">
                                    <strong style="font-size:14px;color:#92400e;display:flex;align-items:center;gap:6px;margin-bottom:8px;">
                                        <i class="fa-solid fa-hand-holding-dollar"></i> เช็คการชำระเงินหน้าร้าน
                                    </strong>
                                    <div style="margin-bottom:10px;">
                                        <label style="font-size:13px;font-weight:700;color:var(--text-dark);display:block;margin-bottom:4px;">วิธีชำระเงิน:</label>
                                        <select name="on_site_payment_method" class="cute-input staff-payment-method-select" style="width:100%;font-weight:700;padding:8px 12px;border-radius:10px;font-size:14px;">
                                            <option value="cash" selected>💵 เงินสด (ไม่ต้องแนบรูปสลิป)</option>
                                            <option value="transfer">📲 โอนจ่าย / สแกน QR (ถ่ายรูปสลิปแนบ)</option>
                                        </select>
                                    </div>

                                    <div class="staff-cash-group" style="margin-bottom:8px;">
                                        <label style="font-size:12px;color:var(--text-muted);display:block;margin-bottom:3px;">จำนวนเงินสดที่รับมา (บาท):</label>
                                        <input type="number" step="0.01" min="0.01" name="on_site_cash_amount" class="cute-input" value="{{ $booking->front_store_collected_amount ?: ($booking->total_price ?: 0) }}" style="width:100%;">
                                    </div>

                                    <div class="staff-slip-group" style="display:none;margin-bottom:8px;">
                                        <label style="font-size:12px;color:#b42318;font-weight:700;display:block;margin-bottom:3px;">ถ่าย/เลือกรูปสลิปโอนเงิน (จำเป็น):</label>
                                        <input type="file" name="on_site_payment_slip" accept="image/*" class="cute-input" style="padding:6px;width:100%;">
                                    </div>
                                </div>
                            @endif
                            <button class="btn-large btn-large-primary" type="submit"><i class="fa-solid fa-paper-plane"></i> ส่งรูปงานให้ Admin ตรวจสอบ</button>
                        </form>
                    @endif
                @endif
            @endforeach
        </div>
        <div class="panel">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:14px"><h2 style="font-size:18px;margin:0">รูปที่เพิ่มแล้ว</h2><strong>{{ $afterPhotos->count() }} รูป</strong></div>
            @forelse($booking->deliveryTasks as $task)
                @php $taskAfterPhotos = $task->photos->where('photo_type', 'after')->sortByDesc('id'); @endphp
                <div class="thumb-section">
                    <div style="display:flex;justify-content:space-between;margin-bottom:10px"><strong>รูปผลงานติดตั้ง: {{ $task->typeLabel() }}</strong><small>{{ $taskAfterPhotos->count() }} รูป</small></div>
                    @if($taskAfterPhotos->isEmpty())
                        <div style="padding:22px 10px;text-align:center;color:var(--text-muted);background:var(--bg-page);border-radius:14px">ยังไม่มีรูป</div>
                    @else
                        <div class="thumbs">
                            @foreach($taskAfterPhotos as $photo)
                                <div class="thumb-card">
                                    <button type="button" class="thumb image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}"><img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปงาน{{ $task->typeLabel() }}"><span>{{ $task->typeLabel() }}</span></button>
                                    @if(!in_array($task->status, ['photo_uploaded', 'completed'], true))
                                        <form method="POST" action="{{ route('staff.bookings.photos.destroy', [$booking, $photo]) }}" onsubmit="return confirm('ลบรูปงาน{{ $task->typeLabel() }}ใบนี้ใช่หรือไม่?')">@csrf @method('DELETE')<button class="thumb-delete" type="submit" title="ลบรูป" aria-label="ลบรูป"><i class="fa-solid fa-trash"></i></button></form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($task->problem_note)
                        <div style="margin-top:10px;padding:11px;border-radius:12px;background:#ffe1e1;color:#b42318;font-weight:700;font-size:13px">เหตุผลที่ตีกลับ: {{ str($task->problem_note)->after('ตีกลับรูปงานโดยแอดมิน:')->trim() }}</div>
                    @endif
                </div>
            @empty
                <div style="padding:30px;text-align:center;color:var(--text-muted)">ยังไม่มีงานอุปกรณ์</div>
            @endforelse
        </div>
    </div>
    <div class="camera-modal" id="browser-camera" aria-hidden="true">
        <div class="camera-dialog" role="dialog" aria-modal="true" aria-label="ถ่ายรูป">
            <video class="camera-video" id="camera-video" autoplay muted playsinline></video>
            <div class="camera-error" id="camera-error">เปิดกล้องไม่ได้
กรุณาเปิดสิทธิ์กล้องของเว็บไซต์ก่อน แล้วโหลดหน้านี้ใหม่
Android: แตะไอคอนแม่กุญแจ/ตั้งค่าเว็บไซต์ข้างที่อยู่เว็บ → กล้อง → อนุญาต
iPhone/iPad: แตะปุ่มตั้งค่าเว็บไซต์หรือ aA ข้างที่อยู่เว็บ → กล้อง → อนุญาต</div>
            <canvas id="camera-canvas" hidden></canvas>
            <div class="camera-actions"><button class="btn-large btn-large-secondary" type="button" id="camera-close">ปิด</button><button class="btn-large btn-large-primary" type="button" id="camera-capture" disabled><i class="fa-solid fa-camera"></i> ถ่ายภาพนี้</button></div>
        </div>
    </div>
    @include('components.image-lightbox')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const draftDbName='staff-photo-drafts-v1',draftStoreName='photos',clearDraftKeys=@json(session('clear_photo_draft_keys', []));
    const openDraftDb=()=>new Promise((resolve,reject)=>{if(!window.indexedDB){reject(new Error('IndexedDB unavailable'));return;}const request=indexedDB.open(draftDbName,1);request.onupgradeneeded=()=>{if(!request.result.objectStoreNames.contains(draftStoreName))request.result.createObjectStore(draftStoreName,{keyPath:'id'});};request.onsuccess=()=>resolve(request.result);request.onerror=()=>reject(request.error||new Error('Cannot open draft database'));});
    const readDrafts=async draftKey=>{const db=await openDraftDb();return new Promise((resolve,reject)=>{const request=db.transaction(draftStoreName,'readonly').objectStore(draftStoreName).getAll();request.onsuccess=()=>{db.close();resolve(request.result.filter(item=>item.draftKey===draftKey).sort((a,b)=>a.position-b.position));};request.onerror=()=>{db.close();reject(request.error);};});};
    const replaceDrafts=async(form)=>{const draftKey=form.dataset.draftKey;if(!draftKey)return;const files=[...form.querySelector('.camera-input').files].map((file,position)=>({file,source:'camera',position})).concat([...form.querySelector('.gallery-input').files].map((file,position)=>({file,source:'gallery',position:position+10000})));const db=await openDraftDb();return new Promise((resolve,reject)=>{const transaction=db.transaction(draftStoreName,'readwrite'),store=transaction.objectStore(draftStoreName),read=store.getAll();read.onsuccess=()=>{read.result.filter(item=>item.draftKey===draftKey).forEach(item=>store.delete(item.id));files.forEach(({file,source,position})=>store.put({id:`${draftKey}:${source}:${position}`,draftKey,source,position,name:file.name,type:file.type,lastModified:file.lastModified,blob:file}));};read.onerror=()=>{db.close();reject(read.error);};transaction.oncomplete=()=>{db.close();resolve();};transaction.onerror=()=>{db.close();reject(transaction.error);};});};
    const saveDraft=form=>{form._draftSave=(form._draftSave||Promise.resolve()).then(()=>replaceDrafts(form)).catch(()=>{});return form._draftSave;};
    const clearDrafts=async draftKeys=>{if(!draftKeys.length)return;try{const db=await openDraftDb();await new Promise((resolve,reject)=>{const transaction=db.transaction(draftStoreName,'readwrite'),store=transaction.objectStore(draftStoreName),read=store.getAll();read.onsuccess=()=>{read.result.filter(item=>draftKeys.includes(item.draftKey)).forEach(item=>store.delete(item.id));};read.onerror=()=>reject(read.error);transaction.oncomplete=resolve;transaction.onerror=()=>reject(transaction.error);});db.close();}catch(_){}}
    const removeSelectedFile=(form, input, index)=>{
        if(typeof DataTransfer==='undefined')return;
        const dt=new DataTransfer();[...(input.files||[])].forEach((file,fileIndex)=>{if(fileIndex!==index)dt.items.add(file);});input.files=dt.files;update(form);saveDraft(form);
    };
    const update=form=>{
        const files=[...(form.querySelector('.camera-input').files||[]),...(form.querySelector('.gallery-input').files||[])];
        form.querySelector('.selection').textContent=files.length?`เลือกแล้ว ${files.length} รูป`:'ยังไม่ได้เลือกรูป';
        const submitButton=form.querySelector('.submit-after-upload');
        if(submitButton)submitButton.disabled=files.length===0;
        const preview=form.querySelector('.selection-preview');
        (form._previewUrls||[]).forEach(url=>URL.revokeObjectURL(url));
        form._previewUrls=[];
        preview.replaceChildren();
        const inputs=[form.querySelector('.camera-input'),form.querySelector('.gallery-input')];
        inputs.forEach((input,inputIndex)=>[...(input.files||[])].forEach((file,fileIndex)=>{
            const url=URL.createObjectURL(file);form._previewUrls.push(url);
            const item=document.createElement('div');item.className='selection-preview-item';
            const zoom=document.createElement('button');zoom.type='button';zoom.className='selection-preview-zoom image-lightbox-trigger';zoom.dataset.lightboxSrc=url;zoom.dataset.lightboxAlt='รูปที่เลือก';
            const image=document.createElement('img');image.alt='รูปที่เลือก';image.src=url;zoom.appendChild(image);
            const remove=document.createElement('button');remove.type='button';remove.className='selection-delete';remove.title='ลบรูปนี้';remove.setAttribute('aria-label','ลบรูปนี้');remove.innerHTML='<i class="fa-solid fa-xmark"></i>';remove.addEventListener('click',()=>removeSelectedFile(form,input,fileIndex));
            item.append(zoom,remove);preview.appendChild(item);
        }));
    };
    const modal=document.getElementById('browser-camera'), video=document.getElementById('camera-video'), canvas=document.getElementById('camera-canvas'), capture=document.getElementById('camera-capture'), close=document.getElementById('camera-close'), error=document.getElementById('camera-error');
    let activeForm=null, stream=null;
    const stopCamera=()=>{stream?.getTracks().forEach(track=>track.stop());stream=null;video.srcObject=null;capture.disabled=true;modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');};
    const useNativeCamera=form=>form.querySelector('.camera-input').click();
    document.querySelectorAll('.photo-upload-form').forEach(form=>{
        const camera=form.querySelector('.camera-input'), gallery=form.querySelector('.gallery-input');
        form.querySelector('[data-gallery-trigger]').addEventListener('click',()=>gallery.click());
        camera.addEventListener('change',()=>{update(form);saveDraft(form);}); gallery.addEventListener('change',()=>{update(form);saveDraft(form);});
        form.addEventListener('submit',e=>{if(!camera.files?.length&&!gallery.files?.length){e.preventDefault();alert('กรุณาถ่ายหรือแนบรูปอย่างน้อย 1 รูป');}});
        form.querySelector('[data-camera-trigger]').addEventListener('click',async()=>{
            activeForm=form;
            if(!navigator.mediaDevices?.getUserMedia||typeof DataTransfer==='undefined'){useNativeCamera(form);return;}
            modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');error.style.display='none';
            try{stream=await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});video.srcObject=stream;await video.play();capture.disabled=false;}catch(_){error.style.display='block';capture.disabled=true;}
        });
    });
    close.addEventListener('click',stopCamera);modal.addEventListener('click',event=>{if(event.target===modal)stopCamera();});
    capture.addEventListener('click',()=>{if(!activeForm||!video.videoWidth)return;canvas.width=video.videoWidth;canvas.height=video.videoHeight;canvas.getContext('2d').drawImage(video,0,0);canvas.toBlob(blob=>{if(!blob)return;const input=activeForm.querySelector('.camera-input'),dt=new DataTransfer();dt.items.add(new File([blob],`camera-${Date.now()}.jpg`,{type:'image/jpeg'}));input.files=dt.files;input.dispatchEvent(new Event('change',{bubbles:true}));stopCamera();},'image/jpeg',.88);});
    const restoreDraft=async form=>{try{const records=await readDrafts(form.dataset.draftKey);if(!records.length)return;if(typeof DataTransfer==='undefined'||typeof File==='undefined')return;const inputs={camera:form.querySelector('.camera-input'),gallery:form.querySelector('.gallery-input')};Object.entries(inputs).forEach(([source,input])=>{const dt=new DataTransfer();records.filter(record=>record.source===source).sort((a,b)=>a.position-b.position).forEach(record=>dt.items.add(new File([record.blob],record.name||`draft-${record.position}.jpg`,{type:record.type||record.blob.type||'image/jpeg',lastModified:record.lastModified||Date.now()})));input.files=dt.files;});update(form);}catch(_){}}
    document.querySelectorAll('.staff-payment-method-select').forEach(function(select) {
        select.addEventListener('change', function() {
            const form = select.closest('form');
            if (!form) return;
            const cashGroup = form.querySelector('.staff-cash-group');
            const slipGroup = form.querySelector('.staff-slip-group');
            if (select.value === 'transfer') {
                if (cashGroup) cashGroup.style.display = 'none';
                if (slipGroup) slipGroup.style.display = 'block';
            } else {
                if (cashGroup) cashGroup.style.display = 'block';
                if (slipGroup) slipGroup.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
