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
        stroke: #ffffff;
        stroke-width: 2px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .market-lot:hover {
        filter: brightness(1.1);
        transform: scale(1.02);
    }

    .lot-text {
        fill: #2F2F37;
        font-size: 10px;
        font-weight: 800;
        pointer-events: none;
        text-anchor: middle;
        dominant-baseline: middle;
    }

    .zone-label {
        font-size: 13px;
        font-weight: 800;
        fill: var(--text-dark);
    }

    /* Lot Status Fills */
    .lot-available { fill: #A2E8B9; } /* soft green */
    .lot-pending { fill: #FFE17D; } /* soft yellow */
    .lot-booked { fill: #FFA3A3; } /* soft red */
    .lot-installing { fill: #C7B5FF; } /* soft purple */
    .lot-completed { fill: #8DE5DE; } /* soft green-blue */
    .lot-blocked { fill: #E0E0E0; } /* light gray */
    .lot-problem { fill: #FFC078; } /* soft orange */

    /* Selected state */
    .lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3px !important;
        filter: drop-shadow(0 0 6px rgba(255, 60, 112, 0.4));
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
        $leftBlockCodes = ['GB', 'GC', 'GD', 'GE', 'GF', 'GG', 'GH', 'GI', 'GJ'];
        $bottomBlockCodes = ['GL', 'GM', 'GN', 'GO', 'GP', 'GQ', 'GR', 'GS', 'GT'];
        $rightBlockCodes = ['GW', 'GX', 'GY', 'GZ'];
    @endphp

    <!-- Map View -->
    <div class="map-card">
        <div class="map-viewport">
            <svg viewBox="0 0 1100 700" width="100%" height="100%" class="market-svg">
                <defs>
                    <!-- Gradients for different zones/stalls -->
                    <linearGradient id="orange-tent-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#FF5A36" />
                        <stop offset="100%" stop-color="#FFA857" />
                    </linearGradient>
                    <linearGradient id="grass-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#8ED699" />
                        <stop offset="100%" stop-color="#5FB46D" />
                    </linearGradient>
                    <linearGradient id="road-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#E8E8EC" />
                        <stop offset="100%" stop-color="#DCDCE2" />
                    </linearGradient>
                    <linearGradient id="building-grad" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#8B5E4F" />
                        <stop offset="100%" stop-color="#5D3A2E" />
                    </linearGradient>
                </defs>

                <!-- Background Ground color -->
                <rect width="1100" height="700" fill="#F4EFEA" rx="20" />

                <!-- Roads -->
                <!-- Lamlukka Road -->
                <polygon points="-50,220 700,-30 780,-30 -50,280" fill="url(#road-grad)" />
                <text x="240" y="100" transform="rotate(-20 240 100)" fill="#9A9AA4" font-size="16" font-weight="bold">ถนนลำลูกกา</text>

                <!-- Phahonyothin Road -->
                <polygon points="-50,600 900,240 980,240 -50,660" fill="url(#road-grad)" />
                <text x="320" y="470" transform="rotate(-20 320 470)" fill="#9A9AA4" font-size="16" font-weight="bold">ถนนพหลโยธิน</text>

                <!-- Buildings -->
                <!-- Kanmanee Building -->
                <g class="building" transform="translate(680, 50)">
                    <polygon points="0,40 100,0 200,40 200,80 100,120 0,80" fill="#E6DFD9" stroke="#C5BDB6" stroke-width="2" />
                    <polygon points="0,40 100,0 200,40 100,60" fill="url(#building-grad)" />
                    <rect x="80" y="80" width="40" height="40" fill="#5D3A2E" rx="4" />
                    <text x="100" y="55" text-anchor="middle" fill="#FFFFFF" font-size="13" font-weight="bold">อาคารกานต์มณี</text>
                </g>

                <!-- Beer Garden -->
                <g class="beer-garden" transform="translate(480, 270)">
                    <polygon points="0,60 140,0 280,60 140,120" fill="url(#grass-grad)" stroke="#4A9C59" stroke-width="3" />
                    <!-- Table circles -->
                    <circle cx="60" cy="45" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <circle cx="140" cy="35" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <circle cx="220" cy="45" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <circle cx="90" cy="70" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <circle cx="190" cy="70" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <circle cx="140" cy="95" r="8" fill="#FFFFFF" stroke="#CCCCCC" />
                    <!-- Signboard -->
                    <g transform="translate(110, -5) rotate(-20)">
                        <rect x="0" y="0" width="90" height="26" fill="#6FD08C" rx="6" stroke="#4A9C59" stroke-width="1.5" />
                        <text x="45" y="17" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="bold">ลานเบียร์ช้าง</text>
                    </g>
                </g>

                <!-- Trees -->
                <g transform="translate(100, 320)">
                    <line x1="0" y1="0" x2="0" y2="20" stroke="#8B5E4F" stroke-width="3" />
                    <circle cx="0" cy="-5" r="14" fill="#95D5A4" stroke="#77C58A" stroke-width="2" />
                </g>
                <g transform="translate(140, 340)">
                    <line x1="0" y1="0" x2="0" y2="20" stroke="#8B5E4F" stroke-width="3" />
                    <circle cx="0" cy="-5" r="14" fill="#95D5A4" stroke="#77C58A" stroke-width="2" />
                </g>
                <g transform="translate(180, 360)">
                    <line x1="0" y1="0" x2="0" y2="20" stroke="#8B5E4F" stroke-width="3" />
                    <circle cx="0" cy="-5" r="14" fill="#95D5A4" stroke="#77C58A" stroke-width="2" />
                </g>

                <g transform="translate(80, 150)">
                    <line x1="0" y1="0" x2="0" y2="20" stroke="#8B5E4F" stroke-width="3" />
                    <circle cx="0" cy="-5" r="14" fill="#95D5A4" stroke="#77C58A" stroke-width="2" />
                </g>
                <g transform="translate(110, 130)">
                    <line x1="0" y1="0" x2="0" y2="20" stroke="#8B5E4F" stroke-width="3" />
                    <circle cx="0" cy="-5" r="14" fill="#95D5A4" stroke="#77C58A" stroke-width="2" />
                </g>

                <!-- Block 1: Left Block (GB to GJ) -->
                <g class="block-group" transform="translate(140, 220) rotate(-20) skewX(20)">
                    @foreach($zones->whereIn('code', $leftBlockCodes) as $zone)
                        <g class="zone-group" data-zone-code="{{ $zone->code }}">
                            <!-- Render Zone Label -->
                            <text x="{{ ($loop->index * 34) + 12 }}" y="-10" class="zone-label" text-anchor="middle" font-size="9" fill="#7F7F8F" font-weight="bold">{{ $zone->code }}</text>
                            @foreach($zone->lots as $lot)
                                <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                    <rect class="market-lot lot-available" 
                                          id="lot-{{ $lot->lot_code }}"
                                          data-lot-id="{{ $lot->id }}"
                                          data-lot-code="{{ $lot->lot_code }}"
                                          data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                          x="{{ $lot->position_x }}" 
                                          y="{{ $lot->position_y }}" 
                                          width="{{ $lot->width }}" 
                                          height="{{ $lot->height }}" 
                                          rx="3" />
                                    <text x="{{ $lot->position_x + ($lot->width / 2) }}" 
                                          y="{{ $lot->position_y + ($lot->height / 2) + 1 }}" 
                                          class="lot-text" font-size="7">{{ $lot->lot_code }}</text>
                                </g>
                            @endforeach
                        </g>
                    @endforeach
                </g>

                <!-- Block 2: Bottom Block (GL to GT) -->
                <g class="block-group" transform="translate(560, 420) rotate(-20) skewX(20)">
                    @foreach($zones->whereIn('code', $bottomBlockCodes) as $zone)
                        <g class="zone-group" data-zone-code="{{ $zone->code }}">
                            <!-- Render Zone Label -->
                            <text x="{{ ($loop->index * 34) + 12 }}" y="-10" class="zone-label" text-anchor="middle" font-size="9" fill="#7F7F8F" font-weight="bold">{{ $zone->code }}</text>
                            @foreach($zone->lots as $lot)
                                <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                    <rect class="market-lot lot-available" 
                                          id="lot-{{ $lot->lot_code }}"
                                          data-lot-id="{{ $lot->id }}"
                                          data-lot-code="{{ $lot->lot_code }}"
                                          data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                          x="{{ $lot->position_x }}" 
                                          y="{{ $lot->position_y }}" 
                                          width="{{ $lot->width }}" 
                                          height="{{ $lot->height }}" 
                                          rx="3" />
                                    <text x="{{ $lot->position_x + ($lot->width / 2) }}" 
                                          y="{{ $lot->position_y + ($lot->height / 2) + 1 }}" 
                                          class="lot-text" font-size="7">{{ $lot->lot_code }}</text>
                                </g>
                            @endforeach
                        </g>
                    @endforeach
                </g>

                <!-- Block 3: Right Block (GW to GZ) - Orange Tents -->
                <g class="block-group" transform="translate(850, 220) rotate(-20) skewX(20)">
                    @foreach($zones->whereIn('code', $rightBlockCodes) as $zone)
                        <g class="zone-group" data-zone-code="{{ $zone->code }}">
                            <!-- Render Zone Label with 'คี่'/'คู่' labels -->
                            <text x="{{ ($loop->index * 54) + 19 }}" y="-15" class="zone-label" text-anchor="middle" font-size="9" fill="#E65100" font-weight="bold">{{ $zone->code }} คี่</text>
                            <text x="{{ ($loop->index * 54) + 19 }}" y="-5" class="zone-label" text-anchor="middle" font-size="9" fill="#E65100" font-weight="bold">{{ $zone->code }} คู่</text>
                            @foreach($zone->lots as $lot)
                                <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                    <!-- Orange Tents -->
                                    <rect class="market-lot lot-available" 
                                          id="lot-{{ $lot->lot_code }}"
                                          data-lot-id="{{ $lot->id }}"
                                          data-lot-code="{{ $lot->lot_code }}"
                                          data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                          x="{{ $lot->position_x }}" 
                                          y="{{ $lot->position_y }}" 
                                          width="{{ $lot->width }}" 
                                          height="{{ $lot->height }}" 
                                          rx="4"
                                          style="fill-opacity: 0.85; stroke: #FF7043; stroke-width: 1.5px;" />
                                    <text x="{{ $lot->position_x + ($lot->width / 2) }}" 
                                          y="{{ $lot->position_y + ($lot->height / 2) + 1 }}" 
                                          class="lot-text" font-size="8" font-weight="900">{{ $lot->lot_code }}</text>
                                </g>
                            @endforeach
                        </g>
                    @endforeach
                </g>
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
