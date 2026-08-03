@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(0,560px) minmax(0,1fr);gap:18px}.upload-stack{display:grid;gap:16px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:20px;padding:18px}.panel-after{border-top:5px solid #4ecdc4}.panel-task-tent{border-top-color:#e5b700;background:#fffdf3}.panel-task-counter{border-top-color:#e66bcf;background:#fff6fd}.panel-task-other{border-top-color:#39a9db;background:#f5fbff}.task-band{display:flex;align-items:center;justify-content:space-between;gap:10px;margin:-18px -18px 15px;padding:11px 15px;border-radius:15px 15px 0 0;font-weight:900}.panel-task-tent .task-band{background:#ffe873;color:#604d00}.panel-task-counter .task-band{background:#f2a4e8;color:#6f1d63}.panel-task-other .task-band{background:#b9e7f8;color:#07546f}.task-band small{font-size:11px;font-weight:800;opacity:.82}.back-btn{width:42px;height:42px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}.pick{min-height:88px;border:2px dashed var(--border-cute);border-radius:16px;background:#fff;color:var(--text-dark);font:inherit;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:7px;font-weight:800;cursor:pointer;text-align:center}.pick:active{transform:scale(.98)}.pick i{font-size:26px;color:var(--primary-hover)}.file-input{display:none}.selection{min-height:20px}.thumb-section+.thumb-section{margin-top:20px;padding-top:18px;border-top:1px dashed var(--border-cute)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(125px,1fr));gap:10px}.thumb-card{position:relative;min-width:0}.thumb{position:relative;width:100%;border:0;background:none;padding:0;cursor:zoom-in}.thumb img{width:100%;height:125px;object-fit:cover;border-radius:13px;border:1px solid var(--border-cute);display:block}.thumb span{position:absolute;left:6px;bottom:6px;padding:4px 7px;border-radius:999px;background:rgba(255,255,255,.92);font-size:10px;font-weight:800}.thumb-delete{position:absolute;top:6px;right:6px;z-index:2;width:34px;height:34px;border:0;border-radius:50%;background:rgba(180,35,24,.94);color:#fff;display:inline-flex;align-items:center;justify-content:center;cursor:pointer}.camera-modal{position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(20,20,28,.86)}.camera-modal.is-open{display:flex}.camera-dialog{width:min(100%,680px);padding:16px;border-radius:22px;background:#11131a;color:#fff}.camera-video{display:block;width:100%;max-height:68vh;object-fit:contain;border-radius:16px;background:#000}.camera-actions{display:flex;gap:10px;margin-top:14px}.camera-actions button{flex:1}.camera-error{display:none;padding:18px;text-align:center;color:#ffd3d3}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.panel{padding:15px;border-radius:16px}.task-band{margin:-15px -15px 14px}.upload-choice{gap:8px}.pick{min-height:82px;font-size:14px}}
</style>
@endsection

@section('content')
    @php
        $afterPhotos = $booking->deliveryTasks->flatMap->photos->where('photo_type', 'after')->sortByDesc('id');
    @endphp
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
        <a class="back-btn" href="{{ route('staff.bookings.index') }}" aria-label="กลับรายการจอง"><i class="fa-solid fa-arrow-left"></i></a>
        <div><h1 style="font-size:22px;margin:0">เพิ่มรูปส่งงาน</h1><small style="color:var(--text-muted)">{{ $booking->shop_name }} · {{ $booking->lots->pluck('lot_code')->implode(', ') }}</small></div>
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
                @endphp
                @if ($taskFinished)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>อนุมัติแล้ว</small></div>
                        <i class="fa-solid fa-circle-check" style="font-size:38px;color:#28a745"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px">งานเสร็จสมบูรณ์แล้ว</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px">Admin อนุมัติงานติดตั้งนี้เรียบร้อยแล้ว</p>
                    </div>
                @elseif ($taskSubmitted)
                    <div class="panel panel-after {{ $taskPanelClass }}" style="text-align:center">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>ส่งแล้ว</small></div>
                        <i class="fa-solid fa-hourglass-half" style="font-size:38px;color:#6f42c1"></i>
                        <h2 style="font-size:18px;margin:10px 0 4px">รอ Admin อนุมัติ</h2>
                        <p style="margin:0;color:var(--text-muted);font-size:13px">ส่งงานติดตั้งนี้ให้ Admin ตรวจสอบแล้ว</p>
                    </div>
                @else
                    <form class="panel photo-upload-form panel-after {{ $taskPanelClass }}" data-camera-key="after_{{ $task->id }}" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos', [$booking, $task]) }}">
                        @csrf
                        <input type="hidden" name="photo_type" value="after">
                        <div class="task-band"><span>{{ $taskTitle }}</span><small>รูปหลังติดตั้ง</small></div>
                        <h2 style="font-size:18px;margin:0">ถ่ายรูปงานติดตั้ง</h2>
                        <p style="color:var(--text-muted);font-size:13px;margin:5px 0 0">ถ่ายหรือแนบได้หลายรูป และเพิ่มรูปซ้ำได้</p>
                        <div class="upload-choice">
                            <label class="pick" for="camera_after_{{ $task->id }}"><i class="fa-solid fa-camera"></i>ถ่ายรูปด้วยกล้อง</label>
                            <button class="pick" type="button" data-gallery-trigger><i class="fa-solid fa-images"></i>แนบรูป</button>
                            <input class="file-input camera-input" type="file" id="camera_after_{{ $task->id }}" name="camera_photo" accept="image/*" capture="environment">
                            <input class="file-input gallery-input" type="file" name="photos[]" accept="image/*" multiple>
                        </div>
                        <div class="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:12px">ยังไม่ได้เลือกรูป</div>
                        <button class="btn-large btn-large-success" type="submit"><i class="fa-solid fa-plus"></i> เพิ่มรูปงาน{{ $task->typeLabel() }}</button>
                    </form>
                    @if($taskAfterPhotos->isNotEmpty())
                        <form method="POST" action="{{ route('staff.bookings.submit_work', [$booking, $task]) }}" style="margin-top:10px" onsubmit="return confirm('ยืนยันส่งรูปงาน{{ $task->typeLabel() }} ให้ Admin ตรวจสอบ?')">
                            @csrf
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
    @include('components.image-lightbox')
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const update=form=>{const count=(form.querySelector('.camera-input').files?.length||0)+(form.querySelector('.gallery-input').files?.length||0);form.querySelector('.selection').textContent=count?`เลือกแล้ว ${count} รูป`:'ยังไม่ได้เลือกรูป';};
    document.querySelectorAll('.photo-upload-form').forEach(form=>{
        const camera=form.querySelector('.camera-input'), gallery=form.querySelector('.gallery-input');
        form.querySelector('[data-gallery-trigger]').addEventListener('click',()=>gallery.click());
        camera.addEventListener('change',()=>update(form)); gallery.addEventListener('change',()=>update(form));
        form.addEventListener('submit',e=>{if(!camera.files?.length&&!gallery.files?.length){e.preventDefault();alert('กรุณาถ่ายหรือแนบรูปอย่างน้อย 1 รูป');}});
    });
});
</script>
@endsection
