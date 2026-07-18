@extends('layouts.admin')

@section('title', 'อนุมัติรูปส่งงาน')
@section('page_title', 'อนุมัติรูปส่งงานจากพนักงาน')

@section('content')
    <div class="cute-card">
        <h2 class="cute-card-title" style="margin:0"><i class="fa-solid fa-images"></i> รายการรออนุมัติ</h2>
        <p style="margin:7px 0 0;color:var(--text-muted)">เมื่อตรวจและอนุมัติแล้ว รูปทั้งหมดจะปรากฏในหน้าตรวจสอบการจองของลูกค้า</p>
    </div>

    @forelse($bookings as $booking)
        @php($photos=$booking->deliveryTasks->flatMap->photos->where('photo_type','after')->sortBy('id'))
        <div class="cute-card">
            <div style="display:flex;justify-content:space-between;align-items:start;gap:14px;flex-wrap:wrap;margin-bottom:16px">
                <div>
                    <strong style="font-size:18px;color:var(--primary-hover)">{{ $booking->booking_code }}</strong>
                    <div style="font-weight:800;margin-top:3px">{{ $booking->shop_name }}</div>
                    <small style="color:var(--text-muted)">วันที่ {{ $booking->use_date->format('d/m/Y') }} · ล็อต {{ $booking->lots->pluck('lot_code')->implode(', ') }}</small>
                </div>
                <span class="status-badge status-pending"><i class="fa-solid fa-paper-plane"></i> ส่งแล้ว / รออนุมัติ</span>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:18px">
                @foreach($photos as $photo)
                    <button type="button" class="image-lightbox-trigger" data-lightbox-src="{{ route('media.show',['path'=>$photo->image_path]) }}" data-lightbox-alt="รูปส่งงาน {{ $booking->booking_code }}" style="border:1px solid var(--border-cute);border-radius:14px;overflow:hidden;padding:0;background:var(--bg-page)">
                        <img src="{{ route('media.show',['path'=>$photo->image_path]) }}" alt="รูปส่งงาน" style="display:block;width:100%;height:155px;object-fit:cover">
                        <small style="display:block;padding:7px;color:var(--text-muted)">{{ $photo->uploadedBy?->name ?? 'พนักงาน' }} · {{ $photo->created_at->format('H:i น.') }}</small>
                    </button>
                @endforeach
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
                <form method="POST" action="{{ route('admin.installation_reviews.approve',$booking) }}" style="margin:0">@csrf<button class="btn-primary" type="submit" onclick="return confirm('อนุมัติรูปและแสดงให้ลูกค้าเห็น?')"><i class="fa-solid fa-circle-check"></i> อนุมัติ</button></form>
                <form method="POST" action="{{ route('admin.installation_reviews.reject',$booking) }}" style="margin:0;display:flex;gap:8px;flex-wrap:wrap">@csrf<input class="cute-input" name="reason" required maxlength="250" placeholder="เหตุผลที่ตีกลับ" style="width:280px"><button class="btn-secondary" type="submit" style="border-color:#fca5a5;color:#b42318" onclick="return confirm('ตีกลับให้พนักงานเพิ่มรูปใหม่?')"><i class="fa-solid fa-rotate-left"></i> ตีกลับ</button></form>
            </div>
        </div>
    @empty
        <div class="cute-card" style="text-align:center;padding:50px;color:var(--text-muted)"><i class="fa-solid fa-circle-check" style="font-size:48px;color:#4ECDC4;display:block;margin-bottom:12px"></i>ไม่มีรูปส่งงานที่รออนุมัติ</div>
    @endforelse

    <div class="pagination-cute">{{ $bookings->links() }}</div>
    @include('components.image-lightbox')
@endsection
