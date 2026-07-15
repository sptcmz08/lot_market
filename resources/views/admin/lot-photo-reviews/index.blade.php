@extends('layouts.admin')

@section('title', 'ตรวจรูปเลขล็อต')
@section('page_title', 'ตรวจรูปเลขล็อตจากคนส่ง')

@section('content')
    <div class="cute-card" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 class="cute-card-title" style="margin:0;font-size:20px;">
                <i class="fa-solid fa-camera-retro"></i> รายการรอตรวจสอบ
            </h2>
            <p style="margin:6px 0 0;color:var(--text-muted);font-size:14px;">
                ตรวจว่าเลขล็อตในภาพตรงกับล็อตในใบจองหรือไม่ เมื่อยืนยันแล้วรายการจะหายจากหน้านี้
            </p>
        </div>
        <span class="status-badge status-pending" id="pending-review-count">{{ $photos->total() }} รอตรวจ</span>
    </div>

    <div id="review-list">
        @forelse ($photos as $photo)
            @php
                $task = $photo->deliveryTask;
                $booking = $task?->booking;
                $expectedLots = $booking ? $booking->lots->pluck('lot_code')->implode(', ') : '-';
            @endphp
            <div class="cute-card review-card" style="display:grid;grid-template-columns:minmax(220px,340px) minmax(0,1fr);gap:20px;align-items:start;">
                <a href="{{ route('media.show', ['path' => $photo->image_path]) }}" target="_blank" style="display:block;border-radius:18px;overflow:hidden;border:2px solid var(--border-cute);background:var(--bg-page);">
                    <img src="{{ route('media.show', ['path' => $photo->image_path]) }}" alt="รูปเลขล็อต" style="width:100%;height:260px;object-fit:cover;display:block;">
                </a>

                <div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px;margin-bottom:16px;">
                        <div>
                            <span style="display:block;font-size:12px;color:var(--text-muted);">รหัสจอง:</span>
                            <strong style="color:var(--primary-hover);">{{ $booking?->booking_code ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="display:block;font-size:12px;color:var(--text-muted);">ร้านค้า:</span>
                            <strong>{{ $booking?->shop_name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="display:block;font-size:12px;color:var(--text-muted);">ล็อตที่ต้องตรง:</span>
                            <strong style="color:var(--primary-hover);">{{ $expectedLots }}</strong>
                        </div>
                        <div>
                            <span style="display:block;font-size:12px;color:var(--text-muted);">คนส่ง:</span>
                            <strong>{{ $task?->staff?->name ?? $photo->uploadedBy?->name ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="display:block;font-size:12px;color:var(--text-muted);">เวลาส่งรูป:</span>
                            <strong>{{ $photo->created_at->format('d/m/Y H:i น.') }}</strong>
                        </div>
                    </div>

                    @if ($photo->note)
                        <div style="background:var(--bg-page);border:1px dashed var(--border-cute);border-radius:12px;padding:10px 12px;margin-bottom:14px;font-size:13px;">
                            <strong>หมายเหตุจากคนส่ง:</strong> {{ $photo->note }}
                        </div>
                    @endif

                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        <form action="{{ route('admin.lot_photo_reviews.approve', $photo) }}" method="POST" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,#4ECDC4,#3BBAAF);">
                                <i class="fa-solid fa-circle-check"></i> ยืนยันว่าตรง
                            </button>
                        </form>

                        <form action="{{ route('admin.lot_photo_reviews.reject', $photo) }}" method="POST" style="margin:0;display:flex;gap:8px;flex-wrap:wrap;">
                            @csrf
                            <input type="text" name="reason" class="cute-input" placeholder="เหตุผลตีกลับ เช่น รูปไม่ชัด/เลขไม่ตรง" style="width:280px;max-width:100%;">
                            <button type="submit" class="btn-secondary" style="border-color:#FFA3A3;color:#D83A3A;" onclick="return confirm('ยืนยันตีกลับรูปนี้?');">
                                <i class="fa-solid fa-circle-xmark"></i> ไม่ผ่าน
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="cute-card" style="text-align:center;padding:50px 20px;color:var(--text-muted);">
                <i class="fa-solid fa-inbox" style="font-size:48px;color:var(--border-cute);display:block;margin-bottom:12px;"></i>
                ไม่มีรูปเลขล็อตที่รอตรวจสอบตอนนี้
            </div>
        @endforelse
    </div>

    <div class="pagination-cute">
        {{ $photos->links() }}
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let currentCount = {{ $photos->total() }};

        setInterval(function () {
            fetch('{{ route('admin.lot_photo_reviews.status') }}', { headers: { 'Accept': 'application/json' } })
                .then(response => response.json())
                .then(data => {
                    const nextCount = Number(data.pending_count || 0);
                    const badge = document.getElementById('pending-review-count');
                    if (badge) {
                        badge.textContent = `${nextCount} รอตรวจ`;
                    }
                    if (nextCount !== currentCount) {
                        window.location.reload();
                    }
                })
                .catch(() => {});
        }, 1000);
    });
</script>
@endsection
