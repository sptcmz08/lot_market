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
        min-width: 1000px;
        margin: 0 auto;
        user-select: none;
    }

    .market-lot {
        fill: rgba(255, 255, 255, 0) !important;
        stroke: transparent !important;
        stroke-width: 0 !important;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .market-lot:hover {
        fill: rgba(255, 255, 255, 0.25) !important;
        stroke: rgba(255, 255, 255, 0.8) !important;
        stroke-width: 1.5px !important;
    }

    .lot-text {
        display: none !important;
    }

    .zone-label {
        display: none !important;
    }

    /* Selected state */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 2.5px !important;
        fill: rgba(255, 59, 112, 0.25) !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.5));
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

    @php
        if (!function_exists('getLotMapData')) {
            function getLotMapData($lotCode) {
                $zone = substr($lotCode, 0, 2);
                $num = (int)substr($lotCode, 2, 2);

                $leftRedCodes = ['GB', 'GC', 'GD'];
                $leftGreenCodes = ['GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];
                $bottomRedCodes = ['GL', 'GM', 'GN'];
                $bottomGreenCodes = ['GO', 'GP', 'GQ', 'GR', 'GS', 'GT'];
                $rightCodes = ['GW', 'GX', 'GY', 'GZ'];

                if (in_array($zone, $leftRedCodes)) {
                    $corners = [
                        [152, 226],
                        [198, 202],
                        [370, 442],
                        [420, 418]
                    ];
                    $cols = 3;
                    $rows = 10;
                    $c = array_search($zone, $leftRedCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $leftGreenCodes)) {
                    $corners = [
                        [232, 188],
                        [310, 150],
                        [458, 396],
                        [535, 360]
                    ];
                    $cols = 6;
                    $rows = 10;
                    $c = array_search($zone, $leftGreenCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $bottomRedCodes)) {
                    $corners = [
                        [512, 380],
                        [562, 355],
                        [726, 521],
                        [788, 482]
                    ];
                    $cols = 3;
                    $rows = 10;
                    $c = array_search($zone, $bottomRedCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $bottomGreenCodes)) {
                    $corners = [
                        [596, 332],
                        [688, 280],
                        [824, 460],
                        [920, 404]
                    ];
                    $cols = 6;
                    $rows = 10;
                    $c = array_search($zone, $bottomGreenCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $rightCodes)) {
                    $cols = 2;
                    $rows = 5;
                    
                    if ($zone === 'GW') {
                        $corners = [[718, 175], [748, 160], [816, 235], [846, 220]];
                    } elseif ($zone === 'GX') {
                        $corners = [[766, 150], [796, 135], [864, 210], [894, 195]];
                    } elseif ($zone === 'GY') {
                        $corners = [[814, 125], [844, 110], [912, 185], [942, 170]];
                    } elseif ($zone === 'GZ') {
                        $corners = [[862, 100], [892, 85], [960, 160], [990, 145]];
                    } else {
                        return null;
                    }
                    
                    $c = ($num % 2 !== 0) ? 0 : 1;
                    $r = intval(($num - 1) / 2);
                } else {
                    return null;
                }

                $pts = [];
                $sumX = 0;
                $sumY = 0;

                // TL, TR, BR, BL order for SVG polygon points
                $directions = [[0, 0], [1, 0], [1, 1], [0, 1]];
                foreach ($directions as $d) {
                    $dc = $d[0];
                    $dr = $d[1];

                    $u = ($c + $dc) / $cols;
                    $v = ($r + $dr) / $rows;

                    $x = (1 - $u) * (1 - $v) * $corners[0][0] + $u * (1 - $v) * $corners[1][0] + (1 - $u) * $v * $corners[2][0] + $u * $v * $corners[3][0];
                    $y = (1 - $u) * (1 - $v) * $corners[0][1] + $u * (1 - $v) * $corners[1][1] + (1 - $u) * $v * $corners[2][1] + $u * $v * $corners[3][1];
                    
                    $pts[] = round($x) . ',' . round($y);
                    $sumX += $x;
                    $sumY += $y;
                }

                return [
                    'points' => implode(' ', $pts),
                    'cx' => round($sumX / 4),
                    'cy' => round($sumY / 4)
                ];
            }
        }
    @endphp

    <!-- Map View -->
    <div class="map-card">
        <div class="map-viewport" id="map-viewport-wrapper">
            <svg viewBox="0 0 1024 513" width="100%" height="100%" class="market-svg" id="market-svg-element">
                <!-- Background cartoon image -->
                <image href="/images/market_map.png" x="0" y="0" width="1024" height="513" />

                <!-- Dynamic Lots rendering with pixel-perfect overlays -->
                @foreach($zones as $zone)
                    <g class="zone-group" data-zone-code="{{ $zone->code }}">
                        @foreach($zone->lots as $lot)
                            @php
                                $mapData = getLotMapData($lot->lot_code);
                            @endphp
                            @if($mapData)
                                <polygon class="market-lot lot-available" 
                                         id="lot-{{ $lot->lot_code }}"
                                         data-lot-id="{{ $lot->id }}"
                                         data-lot-code="{{ $lot->lot_code }}"
                                         data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                         data-cx="{{ $mapData['cx'] }}"
                                         data-cy="{{ $mapData['cy'] }}"
                                         points="{{ $mapData['points'] }}" />
                            @endif
                        @endforeach
                    </g>
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

                // Position calculation relative to map-viewport
                const cx = parseFloat(this.getAttribute('data-cx'));
                const cy = parseFloat(this.getAttribute('data-cy'));

                const svgRect = svgElement.getBoundingClientRect();
                const viewportRect = mapViewport.getBoundingClientRect();

                const px = (cx / 1024) * svgRect.width;
                const py = (cy / 513) * svgRect.height;

                const left = px + (svgRect.left - viewportRect.left) + mapViewport.scrollLeft;
                const top = py + (svgRect.top - viewportRect.top) + mapViewport.scrollTop;

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
        });

        // Hide popover when clicking outside
        document.addEventListener('click', function(e) {
            if (popover.style.display !== 'none' && !popover.contains(e.target) && !e.target.classList.contains('market-lot')) {
                popover.style.display = 'none';
                selectedLot = null;
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
