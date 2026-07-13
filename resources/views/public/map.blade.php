@extends('layouts.public')

@section('title', 'แผนที่เลือกล็อตสั่งจองอุปกรณ์')

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

    .lot-popover.below {
        transform: translate(-50%, 0) translateY(18px);
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

    .lot-popover.below::after {
        top: -10px;
        bottom: auto;
        border-width: 0 10px 10px;
        border-color: rgba(255, 255, 255, 0.88) transparent;
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
    .content-container {
        max-width: none;
        padding-left: 16px;
        padding-right: 16px;
    }

    .cute-card {
        max-width: 1020px;
        margin-left: auto;
        margin-right: auto;
    }

    .map-card {
        width: min(100%, calc(100vw - 32px));
        max-width: none;
        margin-left: auto;
        margin-right: auto;
        padding: 8px;
        background-color: #F7F7F7;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #D9D9D9;
        box-sizing: border-box;
    }

    .map-toolbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        margin-bottom: 6px;
        flex-wrap: wrap;
    }

    .map-tool-btn {
        border: 1px solid #D0D7DE;
        background: #FFFFFF;
        color: #374151;
        width: 32px;
        height: 30px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-weight: 800;
        box-shadow: none;
    }

    .map-tool-btn:hover {
        border-color: var(--primary);
        color: var(--primary-hover);
    }

    .map-zoom-readout {
        min-width: 54px;
        text-align: center;
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
    }

    .map-viewport {
        position: relative;
        width: 100%;
        overflow: auto;
        padding: 0;
        max-height: 72vh;
        background: #FFFFFF;
        border: 1px solid #D9D9D9;
        border-radius: 2px;
        -webkit-overflow-scrolling: touch;
    }

    .market-svg {
        display: block;
        width: auto;
        height: auto;
        max-width: none;
        min-width: 0;
        margin: 0;
        user-select: none;
    }

    .market-lot {
        fill: transparent;
        stroke: transparent;
        stroke-width: 1.5px;
        pointer-events: none;
        transition: all 0.2s ease;
        vector-effect: non-scaling-stroke;
    }

    .lot-group,
    .lot-hit-area {
        cursor: pointer;
    }

    .lot-cell-border {
        fill: none;
        stroke: #000000;
        stroke-width: 0.75px;
        pointer-events: none;
        vector-effect: non-scaling-stroke;
    }

    .lot-status-marker {
        display: none;
    }

    .lot-status-badge {
        fill: #EF4444;
        stroke: #FFFFFF;
        stroke-width: 1.3px;
        vector-effect: non-scaling-stroke;
    }

    .lot-status-x {
        display: none;
        fill: none;
        stroke: #FFFFFF;
        stroke-width: 1.7px;
        stroke-linecap: round;
        vector-effect: non-scaling-stroke;
    }

    .lot-status-dot {
        fill: #FFFFFF;
    }

    .lot-group.status-pending .lot-status-marker,
    .lot-group.status-booked .lot-status-marker,
    .lot-group.status-installing .lot-status-marker,
    .lot-group.status-completed .lot-status-marker,
    .lot-group.status-problem .lot-status-marker,
    .lot-group.status-blocked .lot-status-marker {
        display: block;
    }

    .lot-group.status-pending .lot-status-badge { fill: #D97706; }
    .lot-group.status-booked .lot-status-badge { fill: #EF4444; }
    .lot-group.status-installing .lot-status-badge { fill: #8B5CF6; }
    .lot-group.status-completed .lot-status-badge { fill: #0284C7; }
    .lot-group.status-problem .lot-status-badge { fill: #EA580C; }
    .lot-group.status-blocked .lot-status-badge { fill: #4B5563; }

    .lot-group.status-booked .lot-status-x,
    .lot-group.status-problem .lot-status-x,
    .lot-group.status-blocked .lot-status-x {
        display: block;
    }

    .lot-group.status-booked .lot-status-dot,
    .lot-group.status-problem .lot-status-dot,
    .lot-group.status-blocked .lot-status-dot {
        display: none;
    }

    .lot-available { fill: transparent; stroke: transparent; }
    .lot-pending { fill: rgba(255, 225, 125, 0.28); stroke: #D97706; }
    .lot-booked { fill: rgba(255, 163, 163, 0.3); stroke: #EF4444; }
    .lot-installing { fill: rgba(199, 181, 255, 0.3); stroke: #8B5CF6; }
    .lot-completed { fill: rgba(141, 229, 222, 0.28); stroke: #0284C7; }
    .lot-problem { fill: rgba(255, 192, 120, 0.32); stroke: #EA580C; }
    .lot-blocked { fill: rgba(156, 163, 175, 0.35); stroke: #4B5563; }

    .lot-group:hover .market-lot {
        stroke: #FF3B70;
        stroke-width: 2.5px;
    }

    .lot-text {
        fill: #2F2F37;
        font-size: 8px;
        font-weight: 850;
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: middle;
    }

    .lot-cell-text {
        display: none;
    }

    .zone-cell-text {
        display: none;
    }

    .map-area-label {
        display: none;
    }

    .excel-table-label,
    .excel-zone-label,
    .excel-road-label,
    .excel-place-label {
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: middle;
        font-family: 'Prompt', 'Outfit', sans-serif;
    }

    .excel-table-label {
        fill: #111827;
        font-size: 7.5px;
        font-weight: 700;
    }

    .excel-zone-label {
        fill: #111827;
        font-size: 12px;
        font-weight: 900;
    }

    .excel-road-label,
    .excel-place-label {
        fill: #FF0000;
        font-size: 10px;
        font-weight: 800;
    }

    .excel-label-box {
        fill: #FFFFFF;
        stroke: #BFBFBF;
        stroke-width: 1;
        vector-effect: non-scaling-stroke;
    }

    .excel-label-booth {
        stroke: #FF0000;
        stroke-width: 1.25;
    }

    .excel-label-shrine {
        stroke: #7030A0;
        stroke-width: 1.25;
    }

    .excel-label-ice {
        stroke: #00B0F0;
        stroke-width: 1.25;
    }

    .zone-label {
        font-size: 13px;
        font-weight: 900;
        fill: var(--text-dark);
        text-anchor: middle;
        dominant-baseline: middle;
    }

    .zone-label-bg {
        fill: rgba(255, 255, 255, 0.78);
        stroke: rgba(241, 221, 229, 0.9);
        stroke-width: 1;
    }

    /* Selected state */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.6));
    }


</style>
@endsection

@section('content')

    <div class="cute-card">
        <div class="date-select-container">
            <h2 class="cute-card-title" style="margin: 0;">
                <i class="fa-solid fa-map-location-dot"></i> เลือกวันที่ใช้งานอุปกรณ์
            </h2>
            <div class="cute-input-group" style="margin: 0; flex-direction: row; align-items: center; gap: 10px;">
                <label class="cute-label" for="date-picker">วันที่ใช้งาน:</label>
                <input type="date" id="date-picker" class="cute-input" value="{{ $date }}" style="width: auto; padding: 8px 12px; border-radius: 12px;" min="{{ date('Y-m-d') }}">
            </div>
        </div>

        <!-- Legend -->
        <div class="legend-container">
            <div class="legend-item"><div class="legend-color" style="background-color: #A2E8B9;"></div><span>🟢 ว่าง</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFE17D;"></div><span>🟡 รอยืนยัน</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFA3A3;"></div><span>🔴 มีคำสั่งจอง</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #C7B5FF;"></div><span>🟣 กำลังติดตั้ง</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #8DE5DE;"></div><span>🔵 ติดตั้งแล้ว</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #FFC078;"></div><span>🟠 มีปัญหา</span></div>
            <div class="legend-item"><div class="legend-color" style="background-color: #E0E0E0;"></div><span>⚫ ปิดใช้งาน</span></div>
        </div>
    </div>

    <!-- Map View -->
    <div class="map-card">
        <div class="map-toolbar" aria-label="เครื่องมือแผนที่">
            <button type="button" class="map-tool-btn" id="map-zoom-out" title="ย่อแผนที่"><i class="fa-solid fa-minus"></i></button>
            <span class="map-zoom-readout" id="map-zoom-readout">100%</span>
            <button type="button" class="map-tool-btn" id="map-zoom-in" title="ขยายแผนที่"><i class="fa-solid fa-plus"></i></button>
            <button type="button" class="map-tool-btn" id="map-zoom-fit" title="พอดีหน้าจอ"><i class="fa-solid fa-up-right-and-down-left-from-center"></i></button>
        </div>
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
        const zoomInBtn = document.getElementById('map-zoom-in');
        const zoomOutBtn = document.getElementById('map-zoom-out');
        const zoomFitBtn = document.getElementById('map-zoom-fit');
        const zoomReadout = document.getElementById('map-zoom-readout');
        
        let lotStatuses = {};
        let selectedLot = null;
        let mapZoom = 1;
        const naturalWidth = Number(svgElement.getAttribute('width'));
        const naturalHeight = Number(svgElement.getAttribute('height'));

        function applyMapZoom(nextZoom, keepCenter = true) {
            const oldZoom = mapZoom;
            const centerX = mapViewport.scrollLeft + (mapViewport.clientWidth / 2);
            const centerY = mapViewport.scrollTop + (mapViewport.clientHeight / 2);

            mapZoom = Math.min(1.4, Math.max(0.35, nextZoom));
            svgElement.style.width = `${naturalWidth * mapZoom}px`;
            svgElement.style.height = `${naturalHeight * mapZoom}px`;
            zoomReadout.textContent = `${Math.round(mapZoom * 100)}%`;

            if (keepCenter && oldZoom > 0) {
                const ratio = mapZoom / oldZoom;
                mapViewport.scrollLeft = (centerX * ratio) - (mapViewport.clientWidth / 2);
                mapViewport.scrollTop = (centerY * ratio) - (mapViewport.clientHeight / 2);
            }

            if (selectedLot) {
                const selectedGroup = document.getElementById(`lot-group-${selectedLot}`);
                if (selectedGroup && popover.style.display !== 'none') {
                    positionPopover(selectedGroup);
                }
            }
        }

        function fitMapToViewport() {
            const fitZoom = (mapViewport.clientWidth - 20) / naturalWidth;
            applyMapZoom(fitZoom, false);
            mapViewport.scrollLeft = 0;
            mapViewport.scrollTop = 0;
        }

        applyMapZoom(1, false);
        zoomInBtn.addEventListener('click', () => applyMapZoom(mapZoom + 0.12));
        zoomOutBtn.addEventListener('click', () => applyMapZoom(mapZoom - 0.12));
        zoomFitBtn.addEventListener('click', fitMapToViewport);

        function getLotAnchor(targetEl) {
            const viewportRect = mapViewport.getBoundingClientRect();
            const group = targetEl.closest('.lot-group') || targetEl;
            const cx = Number(group.dataset.cx);
            const cy = Number(group.dataset.cy);

            if (svgElement && Number.isFinite(cx) && Number.isFinite(cy) && svgElement.createSVGPoint) {
                const matrix = svgElement.getScreenCTM();
                if (matrix) {
                    const point = svgElement.createSVGPoint();
                    point.x = cx;
                    point.y = cy;
                    const screenPoint = point.matrixTransform(matrix);

                    return {
                        x: (screenPoint.x - viewportRect.left) + mapViewport.scrollLeft,
                        y: (screenPoint.y - viewportRect.top) + mapViewport.scrollTop,
                        viewportY: screenPoint.y - viewportRect.top,
                    };
                }
            }

            const targetRect = group.getBoundingClientRect();
            return {
                x: (targetRect.left - viewportRect.left) + (targetRect.width / 2) + mapViewport.scrollLeft,
                y: (targetRect.top - viewportRect.top) + (targetRect.height / 2) + mapViewport.scrollTop,
                viewportY: targetRect.top - viewportRect.top,
            };
        }

        function positionPopover(targetEl) {
            const popoverWidth = popover.offsetWidth || 360;
            const popoverHeight = popover.offsetHeight || 260;
            const minLeft = mapViewport.scrollLeft + (popoverWidth / 2) + 12;
            const maxLeft = mapViewport.scrollLeft + mapViewport.clientWidth - (popoverWidth / 2) - 12;
            const anchor = getLotAnchor(targetEl);
            const left = Math.min(Math.max(anchor.x, minLeft), Math.max(minLeft, maxLeft));

            popover.classList.toggle('below', anchor.viewportY < popoverHeight + 34);
            popover.style.left = left + 'px';
            popover.style.top = anchor.y + 'px';
        }

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

                        const group = document.getElementById(`lot-group-${lot.lot_code}`);
                        if (group) {
                            group.classList.remove(
                                'status-pending',
                                'status-booked',
                                'status-installing',
                                'status-completed',
                                'status-problem',
                                'status-blocked'
                            );
                            if (lot.status !== 'available') {
                                group.classList.add(`status-${lot.status}`);
                            }
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
                    const el = document.getElementById(`lot-group-${autoLot}`) || document.getElementById(`lot-${autoLot}`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        el.dispatchEvent(new MouseEvent('click', { bubbles: true }));
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
        const lotGroups = document.querySelectorAll('.lot-group');
        lotGroups.forEach(group => {
            group.addEventListener('click', function(e) {
                e.stopPropagation();
                const code = this.dataset.lotCode;
                const lotRect = this.querySelector('.market-lot');
                const lot = lotStatuses[code];
                if (!lot) return;

                selectedLot = code;

                // Highlight selected lot
                const prevSelected = document.querySelector('.market-lot.lot-selected');
                if (prevSelected) prevSelected.classList.remove('lot-selected');
                if (lotRect) lotRect.classList.add('lot-selected');

                popover.style.visibility = 'hidden';
                popover.style.display = 'block';

                // Content rendering based on status
                let html = '';
                const details = lot.booking_details;

                if (lot.status === 'available') {
                    html = `
                        <h3 class="popover-title">ล็อตนี้ยังไม่มีคำสั่งจองอุปกรณ์</h3>
                        <div class="popover-badge-container">
                            <span class="popover-badge">ล็อต ${code} (ว่าง)</span>
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปแผง</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพหน้าร้าน</span>
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
                            <i class="fa-solid fa-file-signature"></i> สั่งจองอุปกรณ์สำหรับล็อตนี้
                        </button>
                    `;
                } else if (lot.status === 'blocked') {
                    html = `
                        <h3 class="popover-title">ปิดใช้งานล็อตชั่วคราว</h3>
                        <div class="popover-badge-container">
                            <span class="popover-badge blocked">ล็อต ${code} (ปิดใช้งาน)</span>
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                <span class="popover-card-text">ไม่มีรูปแผง</span>
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพหน้าร้าน</span>
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
                    const equipmentSummary = details && details.equipment_summary ? details.equipment_summary : 'ยังไม่ระบุรายการอุปกรณ์';
                    
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
                        case 'booked': statusText = 'ยืนยันคำสั่งจอง'; break;
                        case 'installing': statusText = 'กำลังติดตั้ง'; break;
                        case 'completed': statusText = 'ติดตั้งแล้ว'; break;
                        case 'problem': statusText = 'มีปัญหา'; break;
                    }

                    html = `
                        <h3 class="popover-title">${shopName}</h3>
                        <div class="popover-badge-container">
                            <span class="${badgeClass}">ล็อต ${listLots} (${statusText})</span>
                        </div>
                        <div style="font-weight: 850; color: #2F2F37; text-align: center; margin: -4px 0 10px;">
                            ${equipmentSummary}
                        </div>
                        <div class="popover-grid">
                            <div class="popover-card">
                                ${imageHtml1}
                                <span class="popover-card-label" style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; background: rgba(0,0,0,0.4); color: white; padding: 2px 0; font-size: 9px;">รูปภาพหน้าร้าน</span>
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
                positionPopover(this);
                popover.style.visibility = 'visible';
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
            if (popover.style.display !== 'none' && !popover.contains(e.target) && !e.target.closest('.lot-group')) {
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
                    alert(`คัดลอกลิงก์พิกัดสำหรับล็อต ${code} เรียบร้อยแล้ว!`);
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
