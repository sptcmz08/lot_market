@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(280px,440px) 1fr;gap:18px}.upload-stack{display:grid;gap:18px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:22px;padding:20px}.panel-lot{border-top:5px solid var(--primary)}.panel-after{border-top:5px solid #4ECDC4}.back-btn{width:40px;height:40px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:14px 0}.pick{min-height:95px;border:2px dashed var(--border-cute);border-radius:18px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:8px;font-weight:800;cursor:pointer;text-align:center}.pick i{font-size:28px;color:var(--primary-hover)}.thumb-section+.thumb-section{margin-top:20px;padding-top:18px;border-top:1px dashed var(--border-cute)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}.thumb{position:relative;border:0;background:none;padding:0}.thumb img{width:100%;height:125px;object-fit:cover;border-radius:14px;border:1px solid var(--border-cute);display:block}.thumb span{position:absolute;left:6px;bottom:6px;padding:4px 7px;border-radius:999px;background:rgba(255,255,255,.92);font-size:10px;font-weight:800}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.upload-choice{grid-template-columns:1fr 1fr}}
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
                        <label class="pick" for="camera_{{ $type }}"><i class="fa-solid fa-camera"></i>ถ่ายรูป<input hidden type="file" id="camera_{{ $type }}" name="camera_photo" accept="image/*" capture="environment"></label>
                        <label class="pick" for="gallery_{{ $type }}"><i class="fa-solid fa-images"></i>แนบรูป<input hidden type="file" id="gallery_{{ $type }}" name="photos[]" accept="image/*" multiple></label>
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
    @include('components.image-lightbox')
@endsection

@section('scripts')<script>document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.photo-upload-form').forEach(form=>{const camera=form.querySelector('[name="camera_photo"]'),gallery=form.querySelector('[name="photos[]"]'),text=form.querySelector('.selection');const update=()=>{const count=(camera.files?.length||0)+(gallery.files?.length||0);text.textContent=count?`เลือกแล้ว ${count} รูป`:'ยังไม่ได้เลือกรูป'};camera.addEventListener('change',update);gallery.addEventListener('change',update)})});</script>@endsection
