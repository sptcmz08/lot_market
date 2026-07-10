@extends('layouts.public')

@section('title', 'แผนที่เลือกจองล็อคตลาด')

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
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin: 15px 0;
    }

    @media (max-width: 575px) {
        .legend-container {
            grid-template-columns: repeat(2, 1fr);
        }
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

    /* Map SVG styles */
    .map-card {
        padding: 20px;
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
        min-width: 1000px;
        margin: 0 auto;
        user-select: none;
    }

    .market-lot {
        stroke: #ffffff;
        stroke-width: 1.5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

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
        font-size: 11px;
        font-weight: 800;
        fill: var(--text-dark);
    }

    /* Selected state */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.6));
    }


</style>
@section('content')

    <div class="cute-card">
        <div class="date-select-container">
            <h2 class="cute-card-title" style="margin: 0;">
                <i class="fa-solid fa-map-location-dot"></i> เลือกวันจองแผงตลาด
            </h2>
            <div class="cute-input-group" style="margin: 0; flex-direction: row; align-items: center; gap: 10px;">
                <label class="cute-label" for="date-picker">วันที่ใช้แผง:</label>
                <input type="date" id="date-picker" class="cute-input" value="{{ $date }}" style="width: auto; padding: 8px 12px; border-radius: 12px;" min="{{ date('Y-m-d') }}">
            </div>
        </div>

        <!-- Legend -->
        <div class="legend-container">
            <div class="legend-item"><div class="legend-color" style="background-color: #A2E8B9;"></div><span>🟢 ว่าง</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFE17D;"></div><span>🟡 รอยืนยัน</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFA3A3;"></div><span>🔴 จองแล้ว</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #C7B5FF;"></div><span>🟣 กำลังติดตั้ง</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #8DE5DE;"></div><span>🔵 ติดตั้งแล้ว</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFC078;"></div><span>🟠 มีปัญหา</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #E0E0E0;"></div><span>⚫ ปิดแผง</span></div>
        </div>
    </div>

    <!-- Map View -->
    <div class="map-card">
        <div class="map-viewport" id="map-viewport-wrapper">
            <svg viewBox="0 0 1200 620" width="100%" height="100%" class="market-svg" id="market-svg-element">
                @php
                    // === LEFT SIDE: GB-GJ, 10 lots each, 2 rows of 5 ===
                    $leftGroups = [
                        ['GJ'],         // Row 1 (top)
                        ['GI', 'GH'],   // Row 2
                        ['GG', 'GF'],   // Row 3
                        ['GE', 'GD'],   // Row 4
                        ['GC', 'GB'],   // Row 5 (bottom)
                    ];
                    $ltW = 24; $ltH = 16; $ltGap = 3; // left tent dimensions
                    $lx0 = 50; // left start X
                    $ly = 15;  // current Y cursor
                    $groupGap = 18; // gap between row groups
                @endphp

                @foreach($leftGroups as $gi => $groupCodes)
                    @foreach($groupCodes as $zi => $zCode)
                        @php $zone = $zones->firstWhere('code', $zCode); @endphp
                        @if($zone)
                        {{-- Zone label --}}
                        <text x="{{ $lx0 - 5 }}" y="{{ $ly + $ltH }}" text-anchor="end" font-size="10" fill="#4B5563" font-weight="bold">{{ $zCode }}</text>
                        @foreach($zone->lots as $lot)
                            @php
                                $r = (int)substr($lot->lot_code, 2, 2);
                                $col = ($r - 1) % 5;
                                $row = intval(($r - 1) / 5);
                                $tx = $lx0 + $col * ($ltW + $ltGap);
                                $ty = $ly + $row * ($ltH + $ltGap);
                                $cx = $tx + $ltW / 2;
                                $cy = $ty + $ltH / 2;
                            @endphp
                            <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                <rect class="market-lot lot-available"
                                      id="lot-{{ $lot->lot_code }}"
                                      data-lot-id="{{ $lot->id }}"
                                      data-lot-code="{{ $lot->lot_code }}"
                                      data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                      data-cx="{{ $cx }}" data-cy="{{ $cy }}"
                                      x="{{ $tx }}" y="{{ $ty }}"
                                      width="{{ $ltW }}" height="{{ $ltH }}" rx="3" />
                                <text x="{{ $cx }}" y="{{ $cy + 1 }}" class="lot-text" font-size="7.5">{{ substr($lot->lot_code, 2, 2) }}</text>
                            </g>
                        @endforeach
                        @php $ly += 2 * ($ltH + $ltGap); @endphp {{-- 2 rows per zone --}}
                        @endif
                    @endforeach
                    @php $ly += $groupGap; @endphp {{-- gap between groups --}}
                @endforeach

                {{-- === CENTER: Chang Beer Yard === --}}
                <g>
                    <rect x="255" y="100" width="210" height="420" fill="none" stroke="#2E7D32" stroke-width="2.5" rx="12" stroke-dasharray="6 4" opacity="0.5" />
                    <rect x="260" y="105" width="200" height="410" fill="#E8F5E9" stroke="#81C784" stroke-width="1.5" rx="10" />
                    <text x="360" y="310" text-anchor="middle" fill="#1B5E20" font-size="15" font-weight="900" letter-spacing="2">ลานเบียร์ช้าง</text>
                    {{-- tables --}}
                    <circle cx="310" cy="200" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="310" cy="200" r="3" fill="#2E7D32" />
                    <circle cx="410" cy="200" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="410" cy="200" r="3" fill="#2E7D32" />
                    <circle cx="310" cy="400" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="310" cy="400" r="3" fill="#2E7D32" />
                    <circle cx="410" cy="400" r="8" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="410" cy="400" r="3" fill="#2E7D32" />
                    <circle cx="360" cy="200" r="10" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="360" cy="200" r="4" fill="#2E7D32" />
                    <circle cx="360" cy="400" r="10" fill="#FFF" stroke="#2E7D32" stroke-width="1.2" /><circle cx="360" cy="400" r="4" fill="#2E7D32" />
                </g>

                @php
                    // === RIGHT SIDE: GL-GT ===
                    $rightGroups = [
                        ['GT'],         // Row 1 (top) - 9 tents (4+5)
                        ['GS', 'GR'],   // Row 2 - 14 each (4+5+5)
                        ['GQ', 'GP'],   // Row 3
                        ['GO', 'GN'],   // Row 4
                        ['GM', 'GL'],   // Row 5 (bottom)
                    ];
                    $rtW = 20; $rtH = 16; $rtGap = 3; // right tent dimensions
                    $rx0 = 520; // right start X
                    $secGap = 8; // gap between sections (4|5|5)
                    $ry = 15; // Y cursor
                @endphp

                @foreach($rightGroups as $gi => $groupCodes)
                    @foreach($groupCodes as $zi => $zCode)
                        @php $zone = $zones->firstWhere('code', $zCode); @endphp
                        @if($zone)
                        <text x="{{ $rx0 - 5 }}" y="{{ $ry + $rtH }}" text-anchor="end" font-size="10" fill="#4B5563" font-weight="bold">{{ $zCode }}</text>
                        @foreach($zone->lots as $lot)
                            @php
                                $r = (int)substr($lot->lot_code, 2, 2);
                                // Section layout: 1-4 = section1, 5-9 = section2, 10-14 = section3
                                if ($r <= 4) {
                                    $col = $r - 1;
                                    $secOffset = 0;
                                } elseif ($r <= 9) {
                                    $col = $r - 5;
                                    $secOffset = 4 * ($rtW + $rtGap) + $secGap;
                                } else {
                                    $col = $r - 10;
                                    $secOffset = 4 * ($rtW + $rtGap) + $secGap + 5 * ($rtW + $rtGap) + $secGap;
                                }
                                $tx = $rx0 + $secOffset + $col * ($rtW + $rtGap);
                                $ty = $ry;
                                $cx = $tx + $rtW / 2;
                                $cy = $ty + $rtH / 2;
                            @endphp
                            <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                <rect class="market-lot lot-available"
                                      id="lot-{{ $lot->lot_code }}"
                                      data-lot-id="{{ $lot->id }}"
                                      data-lot-code="{{ $lot->lot_code }}"
                                      data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                      data-cx="{{ $cx }}" data-cy="{{ $cy }}"
                                      x="{{ $tx }}" y="{{ $ty }}"
                                      width="{{ $rtW }}" height="{{ $rtH }}" rx="3" />
                                <text x="{{ $cx }}" y="{{ $cy + 1 }}" class="lot-text" font-size="7">{{ substr($lot->lot_code, 2, 2) }}</text>
                            </g>
                        @endforeach
                        @php $ry += $rtH + $rtGap; @endphp {{-- next row --}}
                        @endif
                    @endforeach
                    @php $ry += $groupGap; @endphp
                @endforeach
            </svg>

            <!-- Floating Popover -->
            <div id="lot-popover" class="lot-popover" style="display: none;">
                <button class="popover-close" id="popover-close-btn">&times;</button>
                <div id="popover-content-area">
                    <!-- filled dynamically by JS -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const datePicker = document.getElementById('date-picker');
        const popover = document.getElementById('lot-popover');
        const popoverContent = document.getElementById('popover-content-area');
        const popoverClose = document.getElementById('popover-close-btn');
        const mapViewport = document.getElementById('map-viewport-wrapper');
        const svgElement = document.getElementById('market-svg-element');
        
        let lotStatuses = {};
        let selectedLot = null;

        // Fetch Lot Statuses
        function loadLotStatuses(onComplete = null) {
            const date = datePicker.value;
            fetch(`{{ route('public.lots.status') }}?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    lotStatuses = {};
                    data.lots.forEach(lot => {
                        lotStatuses[lot.lot_code] = lot;
                        const el = document.getElementById(`lot-${lot.lot_code}`);
                        if (el) {
                            // reset classes
                            el.className.baseVal = 'market-lot';
                            el.classList.add(`lot-${lot.status}`);
                        }
                    });
                    
                    if (onComplete) onComplete();
                });
        }

        loadLotStatuses(function() {
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

        // Listen for date change
        datePicker.addEventListener('change', function() {
            popover.style.display = 'none';
            selectedLot = null;
            loadLotStatuses();
        });

        // Click Lot interaction
        const lotGroups = document.querySelectorAll('.market-lot');
        lotGroups.forEach(group => {
            group.addEventListener('click', function(e) {
                e.stopPropagation();
                const code = this.getAttribute('data-lot-code');
                const lot = lotStatuses[code];
                if (!lot) return;

                selectedLot = code;

                // Highlight selected lot
                const prevSelected = document.querySelector('.market-lot.lot-selected');
                if (prevSelected) prevSelected.classList.remove('lot-selected');
                this.classList.add('lot-selected');

                // Position calculation relative to map-viewport using bounding rect
                const viewportRect = mapViewport.getBoundingClientRect();
                const targetRect = this.getBoundingClientRect();

                const left = (targetRect.left - viewportRect.left) + (targetRect.width / 2) + mapViewport.scrollLeft;
                const top = (targetRect.top - viewportRect.top) + (targetRect.height / 2) + mapViewport.scrollTop;

                popover.style.left = left + 'px';
                popover.style.top = top + 'px';
                popover.style.display = 'block';

                // Content rendering based on status
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
                        <button class="popover-btn-book" onclick="redirectToBooking('${code}')">
                            <i class="fa-solid fa-file-signature"></i> จองแผงนี้เลย
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
                    // Booked / Pending / Installing / Completed / Problem
                    const shopName = details ? details.shop_name : 'ไม่ระบุชื่อร้าน';
                    const listLots = details && details.lots ? details.lots.join(', ') : code;
                    
                    // Check for photos
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
                        case 'pending': statusText = 'รอแอดมินยืนยัน'; break;
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
            selectedLot = null;
            const prevSelected = document.querySelector('.market-lot.lot-selected');
            if (prevSelected) prevSelected.classList.remove('lot-selected');
        });

        // Hide popover when clicking outside
        document.addEventListener('click', function(e) {
            if (popover.style.display !== 'none' && !popover.contains(e.target) && !e.target.classList.contains('market-lot')) {
                popover.style.display = 'none';
                selectedLot = null;
                const prevSelected = document.querySelector('.market-lot.lot-selected');
                if (prevSelected) prevSelected.classList.remove('lot-selected');
            }
        });

        // Share functionality
        window.shareCoordinates = function(code, lat, lng) {
            if (lat && lng) {
                window.open(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`, '_blank');
            } else {
                const url = `${window.location.origin}${window.location.pathname}?date=${datePicker.value}&lot=${code}`;
                navigator.clipboard.writeText(url).then(() => {
                    alert(`คัดลอกลิงก์พิกัดสำหรับล็อค ${code} เรียบร้อยแล้ว!`);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            }
        };

        // Redirect to booking
        window.redirectToBooking = function(code) {
            window.location.href = `{{ route('public.booking.create') }}?date=${datePicker.value}&lots=${code}`;
        };
    });
</script>
@endsection
