@extends('layouts.admin')

@section('title', 'แผนผังแผงตลาดสด')
@section('page_title', 'แผนผังและสถานะแผงตลาด')

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
        min-width: 600px;
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

    /* Lot Status Colors */
    .lot-available { fill: #A2E8B9; }
    .lot-pending { fill: #FFE17D; }
    .lot-booked { fill: #FFA3A3; }
    .lot-installing { fill: #C7B5FF; }
    .lot-completed { fill: #8DE5DE; }
    .lot-blocked { fill: #E0E0E0; }
    .lot-problem { fill: #FFC078; }

    /* Selected highlight */
    .lot-selected {
        stroke: var(--primary) !important;
        stroke-width: 3.5px !important;
        filter: drop-shadow(0 0 8px rgba(255, 143, 177, 0.5));
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
                <div class="map-viewport">
                    <svg viewBox="0 0 600 340" width="100%" height="100%" class="market-svg">
                        @foreach($zones as $zone)
                            @php
                                $yLabel = 35;
                                if($zone->code === 'GC') $yLabel = 105;
                                if($zone->code === 'GD') $yLabel = 175;
                                if($zone->code === 'GY') $yLabel = 245;
                            @endphp
                            <g class="zone-group" data-zone-code="{{ $zone->code }}">
                                <text x="30" y="{{ $yLabel }}" class="zone-label">{{ $zone->name }}</text>
                                @foreach($zone->lots as $lot)
                                    <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}">
                                        <rect class="market-lot lot-available" 
                                              id="lot-{{ $lot->lot_code }}"
                                              data-lot-id="{{ $lot->id }}"
                                              data-lot-code="{{ $lot->lot_code }}"
                                              x="{{ $lot->position_x }}" 
                                              y="{{ $lot->position_y }}" 
                                              width="{{ $lot->width }}" 
                                              height="{{ $lot->height }}" 
                                              rx="12" />
                                        <text x="{{ $lot->position_x + ($lot->width / 2) }}" 
                                              y="{{ $lot->position_y + ($lot->height / 2) + 2 }}" 
                                              class="lot-text">{{ $lot->display_name ?? $lot->lot_code }}</text>
                                    </g>
                                @endforeach
                            </g>
                        @endforeach
                    </svg>
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
        
        let lotStatuses = {};
        let currentSelected = null;

        function loadStatuses() {
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
                });
        }

        loadStatuses();

        datePicker.addEventListener('change', function() {
            if (currentSelected) {
                const prevEl = document.getElementById(`lot-${currentSelected}`);
                if (prevEl) prevEl.classList.remove('lot-selected');
                currentSelected = null;
            }
            noSelect.style.display = 'block';
            detailPanel.style.display = 'none';
            loadStatuses();
        });

        // Lot click listener
        const lotGroups = document.querySelectorAll('.lot-group');
        lotGroups.forEach(g => {
            g.addEventListener('click', function() {
                const code = this.getAttribute('data-lot-code');
                
                if (currentSelected) {
                    const prevEl = document.getElementById(`lot-${currentSelected}`);
                    if (prevEl) prevEl.classList.remove('lot-selected');
                }

                currentSelected = code;
                const newEl = document.getElementById(`lot-${code}`);
                if (newEl) newEl.classList.add('lot-selected');

                showDetails(code);
            });
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
