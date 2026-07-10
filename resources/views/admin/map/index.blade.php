@extends('layouts.admin')

@section('title', 'แผนผังแผงตลาดสด')
@section('page_title', 'แผนผังและสถานะแผงตลาด')

@section('styles')
<style>
    /* Floating Popover styling */
    .lot-popover {
        position: absolute;
        z-index: 2000;
        width: 360px;
        max-width: 90vw;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        padding: 18px;
        transform: translate(-50%, -100%) translateY(-25px);
        transition: opacity 0.2s ease, transform 0.2s ease;
        pointer-events: auto;
    }

    /* Popover Arrow */
    .lot-popover::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        border-width: 10px 10px 0;
        border-style: solid;
        border-color: rgba(255, 255, 255, 0.88) transparent;
        display: block;
        width: 0;
    }

    .popover-close {
        position: absolute;
        top: 10px;
        right: 14px;
        background: none;
        border: none;
        font-size: 20px;
        color: #999;
        cursor: pointer;
        transition: color 0.15s ease;
        line-height: 1;
    }

    .popover-close:hover {
        color: #333;
    }

    .popover-title {
        font-size: 18px;
        font-weight: 800;
        color: #111;
        margin: 0 0 4px 0;
        text-align: center;
        padding-right: 15px;
    }

    .popover-badge-container {
        display: flex;
        justify-content: center;
        margin-bottom: 12px;
    }

    .popover-badge {
        font-size: 13px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background-color: #E2F9EB;
        color: #10B981;
    }

    .popover-badge.pending {
        background-color: #FEF3C7;
        color: #D97706;
    }

    .popover-badge.booked {
        background-color: #FEE2E2;
        color: #EF4444;
    }

    .popover-badge.installing {
        background-color: #F3E8FF;
        color: #8B5CF6;
    }

    .popover-badge.completed {
        background-color: #E0F2FE;
        color: #0284C7;
    }

    .popover-badge.problem {
        background-color: #FFEDD5;
        color: #EA580C;
    }

    .popover-badge.blocked {
        background-color: #F3F4F6;
        color: #4B5563;
    }

    .popover-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }

    .popover-card {
        background: rgba(243, 244, 246, 0.85);
        border: 1px solid rgba(229, 231, 235, 0.7);
        border-radius: 12px;
        aspect-ratio: 1.2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .popover-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .popover-card-label {
        font-size: 11px;
        color: #888;
        margin-top: 4px;
        font-weight: 500;
    }

    .popover-card-text {
        font-size: 12px;
        font-weight: 700;
        color: #777;
    }

    .popover-btn-share {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: #4B5563;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px;
        width: 100%;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .popover-btn-share:hover {
        background-color: #374151;
    }

    .popover-btn-book {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background-color: #10B981;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 10px;
        width: 100%;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.15s ease;
        margin-top: 8px;
    }

    .popover-btn-book:hover {
        background-color: #059669;
    }

    .date-select-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .legend-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 15px 0 0 0;
        padding-top: 15px;
        border-top: 1px dashed var(--border-cute);
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 6px;
    }

    .map-card {
        padding: 10px;
        background-color: #fff;
        border-radius: 24px;
        overflow: hidden;
        border: 2px solid var(--border-cute);
    }

    .map-viewport {
        position: relative;
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .market-svg {
        display: block;
        min-width: 1120px;
        margin: 0 auto;
        user-select: none;
    }

    .market-lot {
        stroke: #ffffff;
        stroke-width: 1.5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .lot-available { fill: #A2E8B9; }
    .lot-pending { fill: #FFE17D; }
    .lot-booked { fill: #FFA3A3; }
    .lot-installing { fill: #C7B5FF; }
    .lot-completed { fill: #8DE5DE; }
    .lot-problem { fill: #FFC078; }
    .lot-blocked { fill: #E0E0E0; }

    .market-lot:hover {
        filter: brightness(1.15);
        transform: scale(1.02);
    }

    .lot-text {
        fill: #2F2F37;
        font-size: 8px;
        font-weight: 850;
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: middle;
    }

    .zone-label {
        font-size: 14px;
        font-weight: 900;
        fill: var(--text-dark);
        text-anchor: end;
        dominant-baseline: middle;
    }

    /* Selected highlight */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.6));
    }

    .map-layout-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    @media (max-width: 991px) {
        .map-layout-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <div class="map-layout-grid">
        
        <!-- Left: Date Selector & Map -->
        <div>
            <div class="cute-card">
                <div class="date-select-container">
                    <h2 class="cute-card-title" style="margin: 0; font-size: 18px;">
                        <i class="fa-solid fa-map-location-dot"></i> ตรวจสอบสถานะการจองตามวันใช้งาน
                    </h2>
                    <div class="cute-input-group" style="margin: 0; flex-direction: row; align-items: center; gap: 10px;">
                        <label class="cute-label" for="date-picker">วันที่ใช้งาน:</label>
                        <input type="date" id="date-picker" class="cute-input" value="{{ $date }}" style="width: auto; padding: 8px 12px; border-radius: 12px;">
                    </div>
                </div>

                <div class="legend-container">
                    <div class="legend-item"><div class="legend-color" style="background-color: #A2E8B9;"></div><span>🟢 ว่าง</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #FFE17D;"></div><span>🟡 รอแอดมินยืนยัน</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #FFA3A3;"></div><span>🔴 จองแล้ว</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #C7B5FF;"></div><span>🟣 กำลังติดตั้ง</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #8DE5DE;"></div><span>🔵 ติดตั้งเสร็จแล้ว</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #FFC078;"></div><span>🟠 พบปัญหา</span></div>
                    <div class="legend-item"><div class="legend-color" style="background-color: #E0E0E0;"></div><span>⚫ ปิดการใช้งาน</span></div>
                </div>
            </div>

            <!-- Map rendering -->
            <div class="map-card">
                <div class="map-viewport" id="map-viewport-wrapper">
                    @include('components.market-map-svg', ['zones' => $zones])

                    <!-- Floating Popover -->
                    <div id="lot-popover" class="lot-popover" style="display: none;">
                        <button class="popover-close" id="popover-close-btn">&times;</button>
                        <div id="popover-content-area">
                            <!-- filled dynamically by JS -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Detail panel -->
        <div>
            <div class="cute-card" style="position: sticky; top: 20px;">
                <h3 class="cute-card-title" style="font-size: 18px; border-bottom: 2px solid var(--border-cute); padding-bottom: 10px; margin-bottom: 15px;">
                    <i class="fa-solid fa-circle-info"></i> ข้อมูลรายละเอียดแผง
                </h3>
                
                <div id="no-selection-panel" style="text-align: center; padding: 40px 10px; color: var(--text-muted);">
                    <i class="fa-solid fa-hand-pointer" style="font-size: 40px; margin-bottom: 12px; display: block; color: var(--border-cute);"></i>
                    กรุณาคลิกเลือกแผงบนแผนที่ เพื่อดูข้อมูลการจอง
                </div>

                <div id="detail-panel" style="display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 22px; color: var(--primary-hover);" id="panel-lot-code">GB01</h4>
                        <span class="status-badge status-available" id="panel-badge">ว่าง</span>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;" id="panel-booking-info">
                        <!-- Filled by JS -->
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <a href="#" class="btn-primary" id="btn-view-booking" style="display: none; width: 100%;">
                            <i class="fa-solid fa-eye"></i> ดูใบจองนี้
                        </a>
                        <a href="#" class="btn-secondary" id="btn-edit-lot" style="width: 100%;">
                            <i class="fa-solid fa-gear"></i> ตั้งค่าแผง / พิกัด SVG
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const datePicker = document.getElementById('date-picker');
        const noSelect = document.getElementById('no-selection-panel');
        const detailPanel = document.getElementById('detail-panel');
        const popover = document.getElementById('lot-popover');
        const popoverContent = document.getElementById('popover-content-area');
        const popoverClose = document.getElementById('popover-close-btn');
        const mapViewport = document.getElementById('map-viewport-wrapper');
        const svgElement = document.getElementById('market-svg-element');
        
        let lotStatuses = {};
        let currentSelected = null;

        function loadStatuses(onComplete = null) {
            const date = datePicker.value;
            fetch(`{{ route('public.lots.status') }}?date=${date}`)
                .then(res => res.json())
                .then(data => {
                    lotStatuses = {};
                    data.lots.forEach(lot => {
                        lotStatuses[lot.lot_code] = lot;
                        const el = document.getElementById(`lot-${lot.lot_code}`);
                        if (el) {
                            el.className.baseVal = 'market-lot';
                            el.classList.add(`lot-${lot.status}`);
                            if (currentSelected === lot.lot_code) {
                                el.classList.add('lot-selected');
                            }
                        }
                    });
                    
                    if (currentSelected) {
                        showDetails(currentSelected);
                    }
                    
                    if (onComplete) onComplete();
                });
        }

        loadStatuses(function() {
            // Check for URL parameters to auto-select lot
            const urlParams = new URLSearchParams(window.location.search);
            const autoLot = urlParams.get('lot');
            if (autoLot) {
                setTimeout(() => {
                    const el = document.getElementById(`lot-${autoLot}`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.dispatchEvent(new Event('click'));
                    }
                }, 800);
            }
        });

        datePicker.addEventListener('change', function() {
            if (currentSelected) {
                const prevEl = document.getElementById(`lot-${currentSelected}`);
                if (prevEl) prevEl.classList.remove('lot-selected');
                currentSelected = null;
            }
            popover.style.display = 'none';
            noSelect.style.display = 'block';
            detailPanel.style.display = 'none';
            loadStatuses();
        });

        // Lot click listener
        const lotGroups = document.querySelectorAll('.market-lot');
        lotGroups.forEach(g => {
            g.addEventListener('click', function(e) {
                e.stopPropagation();
                const code = this.getAttribute('data-lot-code');
                
                if (currentSelected) {
                    const prevEl = document.getElementById(`lot-${currentSelected}`);
                    if (prevEl) prevEl.classList.remove('lot-selected');
                }

                currentSelected = code;
                const newEl = document.getElementById(`lot-${code}`);
                if (newEl) newEl.classList.add('lot-selected');

                showDetails(code);

                // Floating Popover positioning and content using bounding rect
                const lot = lotStatuses[code];
                if (!lot) return;

                const viewportRect = mapViewport.getBoundingClientRect();
                const targetRect = this.getBoundingClientRect();

                const left = (targetRect.left - viewportRect.left) + (targetRect.width / 2) + mapViewport.scrollLeft;
                const top = (targetRect.top - viewportRect.top) + (targetRect.height / 2) + mapViewport.scrollTop;

                popover.style.left = left + 'px';
                popover.style.top = top + 'px';
                popover.style.display = 'block';

                let html = '';
                const details = lot.booking_details;

                if (lot.status === 'available') {
                    html = `
                        <h3 class="popover-title">แผงยังไม่มีการจอง</h3>
                        <div class="popover-badge-container">
                            <span class="popover-badge">ล็อค ${code} (ว่าง)</span>
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปแผง</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพแผง</span>
                            </div>
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปเมนู</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพเมนู</span>
                            </div>
                        </div>
                        <button class="popover-btn-share" onclick="shareCoordinates('${code}', null, null)">
                            <i class="fa-solid fa-location-arrow"></i> แชร์พิกัด
                        </button>
                    `;
                } else if (lot.status === 'blocked') {
                    html = `
                        <h3 class="popover-title">ปิดใช้งานแผงชั่วคราว</h3>
                        <div class="popover-badge-container">
                            <span class="popover-badge blocked">ล็อค ${code} (ปิดแผง)</span>
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปแผง</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพแผง</span>
                            </div>
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปเมนู</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพเมนู</span>
                            </div>
                        </div>
                        <button class="popover-btn-share" style="opacity: 0.5; cursor: not-allowed;" disabled>
                            <i class="fa-solid fa-location-arrow"></i> แชร์พิกัด
                        </button>
                    `;
                } else {
                    const shopName = details ? details.shop_name : 'ไม่ระบุชื่อร้าน';
                    const listLots = details && details.lots ? details.lots.join(', ') : code;
                    
                    let imageHtml1 = `<span class="popover-card-text">ไม่มีรูปแผง</span>`;
                    if (details && details.photos) {
                        const afterPhoto = details.photos.after;
                        const beforePhoto = details.photos.before;
                        const numberPhoto = details.photos.lot_number;
                        const problemPhoto = details.photos.problem;
                        const photoUrl = afterPhoto || beforePhoto || numberPhoto || problemPhoto;
                        if (photoUrl) {
                            imageHtml1 = `<img src="${photoUrl}" alt="Shop image">`;
                        }
                    }

                    let badgeClass = 'popover-badge';
                    if (lot.status === 'pending') badgeClass += ' pending';
                    if (lot.status === 'booked') badgeClass += ' booked';
                    if (lot.status === 'installing') badgeClass += ' installing';
                    if (lot.status === 'completed') badgeClass += ' completed';
                    if (lot.status === 'problem') badgeClass += ' problem';

                    let statusText = '';
                    switch (lot.status) {
                        case 'pending': statusText = 'รอยืนยัน'; break;
                        case 'booked': statusText = 'จองแล้ว'; break;
                        case 'installing': statusText = 'กำลังติดตั้ง'; break;
                        case 'completed': statusText = 'ติดตั้งแล้ว'; break;
                        case 'problem': statusText = 'มีปัญหา'; break;
                    }

                    html = `
                        <h3 class="popover-title">${shopName}</h3>
                        <div class="popover-badge-container">
                            <span class="${badgeClass}">ล็อค ${listLots} (${statusText})</span>
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                ${imageHtml1}
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพแผง</span>
                            </div>
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปเมนู</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพเมนู</span>
                            </div>
                        </div>
                        <button class="popover-btn-share" onclick="shareCoordinates('${code}', null, null)">
                            <i class="fa-solid fa-location-arrow"></i> แชร์พิกัด
                        </button>
                    `;
                }

                popoverContent.innerHTML = html;
            });
        });

        // Close popover
        popoverClose.addEventListener('click', function(e) {
            e.stopPropagation();
            popover.style.display = 'none';
            if (currentSelected) {
                const prevEl = document.getElementById(`lot-${currentSelected}`);
                if (prevEl) prevEl.classList.remove('lot-selected');
                currentSelected = null;
            }
            noSelect.style.display = 'block';
            detailPanel.style.display = 'none';
        });

        // Hide popover when clicking outside
        document.addEventListener('click', function(e) {
            if (popover.style.display !== 'none' && !popover.contains(e.target) && !e.target.classList.contains('market-lot')) {
                popover.style.display = 'none';
                if (currentSelected) {
                    const prevEl = document.getElementById(`lot-${currentSelected}`);
                    if (prevEl) prevEl.classList.remove('lot-selected');
                    currentSelected = null;
                }
                noSelect.style.display = 'block';
                detailPanel.style.display = 'none';
            }
        });

        function showDetails(code) {
            const lot = lotStatuses[code];
            if (!lot) return;

            noSelect.style.display = 'none';
            detailPanel.style.display = 'block';

            document.getElementById('panel-lot-code').innerText = `แผง ${code}`;
            
            // badge setup
            const badge = document.getElementById('panel-badge');
            let badgeText = 'ว่าง';
            let badgeClass = 'status-badge status-available';
            
            switch (lot.status) {
                case 'available': badgeText = 'ว่าง'; badgeClass = 'status-badge status-available'; break;
                case 'pending': badgeText = 'รอยืนยัน'; badgeClass = 'status-badge status-pending'; break;
                case 'booked': badgeText = 'จองแล้ว'; badgeClass = 'status-badge status-booked'; break;
                case 'installing': badgeText = 'กำลังติดตั้ง'; badgeClass = 'status-badge status-installing'; break;
                case 'completed': badgeText = 'ติดตั้งเสร็จ'; badgeClass = 'status-badge status-completed'; break;
                case 'blocked': badgeText = 'ปิดใช้งาน'; badgeClass = 'status-badge status-blocked'; break;
                case 'problem': badgeText = 'พบปัญหา'; badgeClass = 'status-badge status-problem'; break;
            }

            badge.innerText = badgeText;
            badge.className = badgeClass;

            const info = document.getElementById('panel-booking-info');
            const btnBooking = document.getElementById('btn-view-booking');
            const btnEditLot = document.getElementById('btn-edit-lot');

            // edit lot URL dynamically
            btnEditLot.href = `/admin/lots/${lot.id}/edit`;

            if (lot.status !== 'available' && lot.status !== 'blocked') {
                // Fetch booking details
                btnBooking.style.display = 'block';
                btnBooking.href = `/admin/bookings?search=${lot.booking_code}`;

                info.innerHTML = `
                    <div style="border-bottom: 1px dashed var(--border-cute); padding-bottom: 8px;">
                        <span style="font-size: 12px; color: var(--text-muted); display:block;">รหัสจอง:</span>
                        <strong>${lot.booking_code}</strong>
                    </div>
                    <div style="border-bottom: 1px dashed var(--border-cute); padding-bottom: 8px;">
                        <span style="font-size: 12px; color: var(--text-muted); display:block;">ชื่อร้านค้า:</span>
                        <strong>${lot.shop_name}</strong>
                    </div>
                    <div>
                        <span style="font-size: 12px; color: var(--text-muted); display:block;">วันที่จองใช้งาน:</span>
                        <strong>${formatThaiDate(datePicker.value)}</strong>
                    </div>
                `;
            } else {
                btnBooking.style.display = 'none';
                info.innerHTML = `
                    <div>
                        <span style="font-size: 12px; color: var(--text-muted); display:block;">สถานะแผง:</span>
                        <strong>${lot.status === 'available' ? 'พร้อมให้บริการสำหรับผู้ต้องการจอง' : 'แอดมินปิดการใช้แผงนี้ชั่วคราว'}</strong>
                    </div>
                    <div style="margin-top: 8px;">
                        <span style="font-size: 12px; color: var(--text-muted); display:block;">วันที่ใช้งาน:</span>
                        <strong>${formatThaiDate(datePicker.value)}</strong>
                    </div>
                `;
            }
        }

        // Share functionality
        window.shareCoordinates = function(code, lat, lng) {
            if (lat && lng) {
                window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
            } else {
                const url = `${window.location.origin}/?date=${datePicker.value}&lot=${code}`;
                navigator.clipboard.writeText(url).then(() => {
                    alert(`คัดลอกลิงก์พิกัดสำหรับล็อค ${code} เรียบร้อยแล้ว!`);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            }
        };

        function formatThaiDate(dateStr) {
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const year = parseInt(parts[0]) + 543;
            const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            return `${parseInt(parts[2])} ${months[parseInt(parts[1]) - 1]} ${year}`;
        }
    });
</script>
@endsection
