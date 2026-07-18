@extends('layouts.staff')

@section('title', 'เพิ่มรูปส่งงาน')

@section('styles')
<style>
    .camera-grid{display:grid;grid-template-columns:minmax(260px,420px) 1fr;gap:18px}.panel{background:#fff;border:1px solid var(--border-cute);border-radius:22px;padding:20px}.back-btn{width:40px;height:40px;border:2px solid var(--border-cute);border-radius:12px;background:#fff;color:var(--text-dark);display:inline-flex;align-items:center;justify-content:center;text-decoration:none}.upload-choice{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:16px 0}.pick{min-height:110px;border:2px dashed var(--border-cute);border-radius:18px;display:flex;flex-direction:column;justify-content:center;align-items:center;gap:8px;font-weight:800;cursor:pointer;text-align:center}.pick i{font-size:30px;color:var(--primary-hover)}.thumbs{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:10px}.thumbs img{width:100%;height:125px;object-fit:cover;border-radius:14px;border:1px solid var(--border-cute)}@media(max-width:800px){.camera-grid{grid-template-columns:1fr}.upload-choice{grid-template-columns:1fr}}
</style>
@endsection

@section('content')
    @php($photos=$booking->deliveryTasks->flatMap->photos->where('photo_type','after')->sortByDesc('id'))
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px"><a class="back-btn" href="{{ route('staff.bookings.index') }}"><i class="fa-solid fa-arrow-left"></i></a><div><h1 style="font-size:22px;margin:0">เพิ่มรูปส่งงาน</h1><small style="color:var(--text-muted)">{{ $booking->shop_name }} · {{ $booking->lots->pluck('lot_code')->implode(', ') }}</small></div></div>
    @if($errors->any())<div class="alert-cute alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>@endif
    <div class="camera-grid">
        <form class="panel" method="POST" enctype="multipart/form-data" action="{{ route('staff.bookings.photos',$booking) }}">@csrf
            <h2 style="font-size:18px;margin:0">ถ่ายรูปหรือแนบรูป</h2><p style="color:var(--text-muted);font-size:13px">แนบได้หลายรูปและเพิ่มรูปได้หลายครั้ง ระบบไม่กำหนดขนาดไฟล์สูงสุด</p>
            <div class="upload-choice">
                <label class="pick" for="camera_photo"><i class="fa-solid fa-camera"></i>ถ่ายรูป<input hidden type="file" id="camera_photo" name="camera_photo" accept="image/*" capture="environment"></label>
                <label class="pick" for="photos"><i class="fa-solid fa-images"></i>เลือกรูปจากเครื่อง<input hidden type="file" id="photos" name="photos[]" accept="image/*" multiple></label>
            </div>
            <div id="selection" style="font-size:13px;color:var(--text-muted);margin-bottom:12px">ยังไม่ได้เลือกรูป</div>
            <label style="display:block;font-size:13px;font-weight:700;margin-bottom:6px" for="note">หมายเหตุ (ถ้ามี)</label><textarea id="note" name="note" rows="3" style="width:100%;box-sizing:border-box;border:2px solid var(--border-cute);border-radius:14px;padding:10px;font:inherit"></textarea>
            <button class="btn-large btn-large-primary" type="submit" style="margin-top:14px"><i class="fa-solid fa-plus"></i> เพิ่มรูป</button>
        </form>
        <div class="panel">
            <div style="display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:14px"><h2 style="font-size:18px;margin:0">รูปที่เพิ่มแล้ว</h2><strong>{{ $photos->count() }} รูป</strong></div>
            @if($photos->isEmpty())<div style="padding:40px 10px;text-align:center;color:var(--text-muted)"><i class="fa-regular fa-images" style="font-size:42px;display:block;margin-bottom:8px"></i>ยังไม่มีรูป</div>@else<div class="thumbs">@foreach($photos as $photo)<button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}" style="border:0;background:none;padding:0"><img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปส่งงาน"></button>@endforeach</div>@endif
            <form method="POST" action="{{ route('staff.bookings.submit',$booking) }}" style="margin-top:18px">@csrf<button class="btn-large btn-large-success" type="submit" @disabled($photos->isEmpty()) onclick="return confirm('ยืนยันส่งรูปทั้งหมดให้แอดมินตรวจสอบ?')"><i class="fa-solid fa-paper-plane"></i> ส่งรูปให้แอดมิน</button></form>
        </div>
    </div>
    @include('components.image-lightbox')
@endsection

@section('scripts')<script>document.addEventListener('DOMContentLoaded',()=>{const camera=document.getElementById('camera_photo'),gallery=document.getElementById('photos'),text=document.getElementById('selection');const update=()=>{const count=(camera.files?.length||0)+(gallery.files?.length||0);text.textContent=count?`เลือกแล้ว ${count} รูป`:'ยังไม่ได้เลือกรูป'};camera.addEventListener('change',update);gallery.addEventListener('change',update)});</script>@endsection
