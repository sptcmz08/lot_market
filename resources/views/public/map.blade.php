@extends('layouts.public')

@section('title', 'แผนที่เลือกจองล็อคตลาด')

@section('styles')
<style>
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
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .market-lot:hover {
        fill-opacity: 0.8 !important;
        stroke-width: 2px !important;
    }

    .lot-text {
        fill: #1d1d1f;
        font-size: 7px;
        font-weight: 800;
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: middle;
        opacity: 0.55;
        transition: all 0.15s ease;
        text-shadow: 0 0 2px #ffffff, 0 0 2px #ffffff;
    }

    .lot-group:hover .lot-text,
    .lot-selected + .lot-text {
        opacity: 1;
        font-size: 8px;
        font-weight: 900;
        fill: #000000;
    }

    .zone-label {
        font-size: 13px;
        font-weight: 800;
        fill: var(--text-dark);
    }

    /* Lot Status Fills with semi-transparency for isometric overlay */
    .lot-available { fill: rgba(162, 232, 185, 0.35); stroke: #52c41a; stroke-width: 1px; } 
    .lot-pending { fill: rgba(255, 225, 125, 0.5); stroke: #faad14; stroke-width: 1px; } 
    .lot-booked { fill: rgba(255, 163, 163, 0.6); stroke: #ff4d4f; stroke-width: 1px; } 
    .lot-installing { fill: rgba(199, 181, 255, 0.5); stroke: #722ed1; stroke-width: 1px; } 
    .lot-completed { fill: rgba(141, 229, 222, 0.5); stroke: #13c2c2; stroke-width: 1px; } 
    .lot-blocked { fill: rgba(150, 150, 150, 0.4); stroke: #888888; stroke-dasharray: 2,2; stroke-width: 1px; } 
    .lot-problem { fill: rgba(255, 192, 120, 0.5); stroke: #fa8c16; stroke-width: 1px; } 

    /* Selected state */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3px !important;
        fill: rgba(255, 59, 112, 0.4) !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.5));
    }

    /* Bottom Sheet Details */
    .bottom-sheet {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: var(--bg-card);
        border-top: 3px solid var(--border-cute);
        border-top-left-radius: 28px;
        border-top-right-radius: 28px;
        box-shadow: 0 -10px 40px rgba(47, 47, 55, 0.12);
        z-index: 1000;
        padding: 20px 20px calc(20px + env(safe-area-inset-bottom, 0px)) 20px;
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        max-width: 600px;
        margin: 0 auto;
    }

    .bottom-sheet.open {
        transform: translateY(0);
    }

    .sheet-handle {
        width: 40px;
        height: 6px;
        background-color: var(--border-cute);
        border-radius: 3px;
        margin: 0 auto 15px auto;
        cursor: pointer;
    }

    .sheet-content {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .sheet-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sheet-title {
        font-size: 22px;
        font-weight: 800;
        margin: 0;
    }

    .sheet-status {
        font-size: 14px;
        font-weight: 700;
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

                $leftCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];
                $bottomCodes = ['GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT'];
                $rightCodes = ['GW', 'GX', 'GY', 'GZ'];

                if (in_array($zone, $leftCodes)) {
                    $corners = [
                        [120, 252],
                        [278, 142],
                        [330, 470],
                        [520, 318]
                    ];
                    $cols = 9;
                    $rows = 10;
                    $c = array_search($zone, $leftCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $bottomCodes)) {
                    $corners = [
                        [515, 385],
                        [780, 240],
                        [730, 520],
                        [980, 345]
                    ];
                    $cols = 9;
                    $rows = 10;
                    $c = array_search($zone, $bottomCodes);
                    $r = $num - 1;
                } elseif (in_array($zone, $rightCodes)) {
                    $corners = [
                        [722, 175],
                        [965, 55],
                        [900, 292],
                        [1030, 175]
                    ];
                    $cols = 8;
                    $rows = 5;
                    
                    $baseCol = array_search($zone, $rightCodes) * 2;
                    if ($num % 2 !== 0) {
                        $c = $baseCol;
                        $r = intval(($num - 1) / 2);
                    } else {
                        $c = $baseCol + 1;
                        $r = intval(($num / 2) - 1);
                    }
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
        <div class="map-viewport">
            <svg viewBox="0 0 1024 549" width="100%" height="100%" class="market-svg">
                <!-- Background cartoon image -->
                <image href="/images/market_map.png" x="0" y="0" width="1024" height="549" />

                <!-- Dynamic Lots rendering with pixel-perfect overlays -->
                @foreach($zones as $zone)
                    <g class="zone-group" data-zone-code="{{ $zone->code }}">
                        @foreach($zone->lots as $lot)
                            @php
                                $mapData = getLotMapData($lot->lot_code);
                            @endphp
                            @if($mapData)
                                <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                    <polygon class="market-lot lot-available" 
                                             id="lot-{{ $lot->lot_code }}"
                                             data-lot-id="{{ $lot->id }}"
                                             data-lot-code="{{ $lot->lot_code }}"
                                             data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                             points="{{ $mapData['points'] }}" />
                                    <text x="{{ $mapData['cx'] }}" 
                                          y="{{ $mapData['cy'] + 1 }}" 
                                          class="lot-text">{{ $lot->lot_code }}</text>
                                </g>
                            @endif
                        @endforeach
                    </g>
                @endforeach
            </svg>
        </div>
    </div>

    <!-- Bottom Sheet Details -->
    <div class="bottom-sheet" id="detail-sheet">
        <div class="sheet-handle" id="sheet-closer"></div>
        <div class="sheet-content">
            <div class="sheet-row">
                <h3 class="sheet-title" id="sheet-lot-code">GB50-52</h3>
                <span class="status-badge" id="sheet-badge">ว่าง</span>
            </div>
            
            <div id="sheet-details-area">
                <!-- filled dynamically by JS -->
            </div>

            <div style="margin-top: 10px;">
                <button class="btn-primary" id="btn-book-action" style="width: 100%;">
                    <i class="fa-solid fa-file-signature"></i> จองแผงนี้เลย
                </button>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const datePicker = document.getElementById('date-picker');
        const sheet = document.getElementById('detail-sheet');
        const sheetCloser = document.getElementById('sheet-closer');
        
        let lotStatuses = {};
        let selectedLots = [];

        // Fetch Lot Statuses
        function loadLotStatuses() {
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
                            
                            // re-apply selection if still selected
                            if (selectedLots.includes(lot.lot_code)) {
                                if (lot.status === 'available') {
                                    el.classList.add('lot-selected');
                                } else {
                                    // remove from selection if no longer available on this date
                                    selectedLots = selectedLots.filter(c => c !== lot.lot_code);
                                }
                            }
                        }
                    });
                    updateBottomSheet();
                });
        }

        loadLotStatuses();

        // Listen for date change
        datePicker.addEventListener('change', function() {
            selectedLots = []; // clear selection on date change
            const rects = document.querySelectorAll('.market-lot');
            rects.forEach(r => r.classList.remove('lot-selected'));
            loadLotStatuses();
        });

        // Click Lot interaction
        const lotGroups = document.querySelectorAll('.lot-group');
        lotGroups.forEach(group => {
            group.addEventListener('click', function() {
                const code = this.getAttribute('data-lot-code');
                const lot = lotStatuses[code];
                if (!lot) return;

                if (lot.status === 'available') {
                    // Toggle selection
                    const el = document.getElementById(`lot-${code}`);
                    if (selectedLots.includes(code)) {
                        selectedLots = selectedLots.filter(c => c !== code);
                        el.classList.remove('lot-selected');
                    } else {
                        selectedLots.push(code);
                        el.classList.add('lot-selected');
                    }
                } else {
                    // Clicking occupied lot opens sheet but clears current selection
                    selectedLots = [];
                    const rects = document.querySelectorAll('.market-lot');
                    rects.forEach(r => r.classList.remove('lot-selected'));
                    // set this one as focus
                    selectedLots = [code];
                }

                updateBottomSheet();
            });
        });

        // Close sheet
        sheetCloser.addEventListener('click', function() {
            sheet.classList.remove('open');
        });

        // Update bottom sheet view
        function updateBottomSheet() {
            if (selectedLots.length === 0) {
                sheet.classList.remove('open');
                return;
            }

            sheet.classList.add('open');

            const btnBook = document.getElementById('btn-book-action');
            const detailsArea = document.getElementById('sheet-details-area');
            const sheetLotCode = document.getElementById('sheet-lot-code');
            const sheetBadge = document.getElementById('sheet-badge');

            if (selectedLots.length > 1) {
                // Multi selection
                sheetLotCode.innerText = selectedLots.sort().join(', ');
                sheetBadge.innerText = 'เลือกไว้';
                sheetBadge.className = 'status-badge status-available';
                
                detailsArea.innerHTML = `
                    <div class="sheet-row">
                        <span class="info-label">จำนวนล็อตที่เลือก:</span>
                        <strong class="info-value">${selectedLots.length} ล็อค</strong>
                    </div>
                    <div class="sheet-row" style="margin-top:5px;">
                        <span class="info-label">วันที่จอง:</span>
                        <strong class="info-value">${formatThaiDate(datePicker.value)}</strong>
                    </div>
                `;

                btnBook.style.display = 'block';
                btnBook.innerHTML = `<i class="fa-solid fa-file-signature"></i> จองกลุ่มล็อตนี้ (${selectedLots.length} แผง)`;
                btnBook.onclick = function() {
                    window.location.href = `{{ route('public.booking.create') }}?date=${datePicker.value}&lots=${selectedLots.join(',')}`;
                };
            } else {
                // Single lot
                const code = selectedLots[0];
                const lot = lotStatuses[code];
                sheetLotCode.innerText = `แผง ${code}`;
                
                // Set badge status text
                let badgeText = 'ว่าง';
                let badgeClass = 'status-badge status-available';
                
                switch(lot.status) {
                    case 'available':
                        badgeText = 'ว่าง';
                        badgeClass = 'status-badge status-available';
                        break;
                    case 'pending':
                        badgeText = 'รอแอดมินยืนยัน';
                        badgeClass = 'status-badge status-pending';
                        break;
                    case 'booked':
                        badgeText = 'จองแล้ว';
                        badgeClass = 'status-badge status-booked';
                        break;
                    case 'installing':
                        badgeText = 'กำลังติดตั้ง';
                        badgeClass = 'status-badge status-installing';
                        break;
                    case 'completed':
                        badgeText = 'ติดตั้งแล้ว';
                        badgeClass = 'status-badge status-completed';
                        break;
                    case 'blocked':
                        badgeText = 'ปิดใช้งาน';
                        badgeClass = 'status-badge status-blocked';
                        break;
                    case 'problem':
                        badgeText = 'มีปัญหา';
                        badgeClass = 'status-badge status-problem';
                        break;
                }

                sheetBadge.innerText = badgeText;
                sheetBadge.className = badgeClass;

                // Shop name display logic
                let shopDetail = '';
                if (lot.status !== 'available' && lot.status !== 'blocked') {
                    shopDetail = `
                        <div class="sheet-row" style="margin-top:5px;">
                            <span class="info-label">ผู้จอง:</span>
                            <strong class="info-value" style="color:var(--primary-hover);">${lot.shop_name || 'ไม่เปิดเผย'}</strong>
                        </div>
                    `;
                }

                detailsArea.innerHTML = `
                    <div class="sheet-row">
                        <span class="info-label">วันที่ตรวจสอบ:</span>
                        <strong class="info-value">${formatThaiDate(datePicker.value)}</strong>
                    </div>
                    ${shopDetail}
                `;

                if (lot.status === 'available') {
                    btnBook.style.display = 'block';
                    btnBook.innerHTML = `<i class="fa-solid fa-file-signature"></i> จองแผง ${code}`;
                    btnBook.onclick = function() {
                        window.location.href = `{{ route('public.booking.create') }}?date=${datePicker.value}&lots=${code}`;
                    };
                } else {
                    btnBook.style.display = 'none';
                }
            }
        }

        function formatThaiDate(dateStr) {
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const year = parseInt(parts[0]) + 543;
            const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            const month = months[parseInt(parts[1]) - 1];
            const day = parseInt(parts[2]);
            return `${day} ${month} ${year}`;
        }
    });
</script>
@endsection
