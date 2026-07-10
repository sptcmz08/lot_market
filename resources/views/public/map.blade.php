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



    /* Status background overlays matching the legend */
    .lot-bg.lot-pending {
        fill: #FFE17D !important;
        opacity: 0.9 !important;
    }
    .lot-bg.lot-booked {
        fill: #FFA3A3 !important;
        opacity: 0.9 !important;
    }
    .lot-bg.lot-installing {
        fill: #C7B5FF !important;
        opacity: 0.9 !important;
    }
    .lot-bg.lot-completed {
        fill: #8DE5DE !important;
        opacity: 0.9 !important;
    }
    .lot-bg.lot-problem {
        fill: #FFC078 !important;
        opacity: 0.9 !important;
    }
    .lot-bg.lot-blocked {
        fill: #E0E0E0 !important;
        opacity: 0.9 !important;
    }

    /* Selected glowing outline */
    .lot-bg.lot-selected {
        stroke: #FF3B70 !important;
        stroke-width: 3.5px !important;
        stroke-opacity: 1 !important;
        filter: drop-shadow(0 0 8px rgba(255, 60, 112, 0.9)) !important;
    }

    /* Dim the tent icon if not available */
    .market-lot {
        transition: all 0.2s ease;
    }
    .market-lot.lot-booked, 
    .market-lot.lot-installing, 
    .market-lot.lot-completed, 
    .market-lot.lot-blocked,
    .market-lot.lot-problem {
        opacity: 0.4;
    }
    
    .market-lot:hover {
        filter: brightness(1.25);
        transform: scale(1.05);
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
            <svg viewBox="0 0 1000 1200" width="100%" height="100%" class="market-svg" id="market-svg-element">
                <!-- Background Decoration Panel -->
                <rect x="0" y="0" width="1000" height="1200" fill="#FFFDF9" rx="20" stroke="#FEF3C7" stroke-width="3" />
                
                <!-- Top Banner -->
                <g transform="translate(100, 25)">
                    <rect x="0" y="0" width="800" height="95" rx="16" fill="#F0FDF4" stroke="#BBF7D0" stroke-width="2" />
                    <text x="400" y="42" font-size="28" font-weight="900" fill="#15803D" text-anchor="middle">ช้อป ชิม ชิล GREEN MARKET</text>
                    <text x="400" y="75" font-size="15" font-weight="700" fill="#4B5563" text-anchor="middle">ศูนย์ราชการแจ้งวัฒนะ อาคารบี ชั้น 2  |  วันที่ 15-19 มิถุนายน 2569</text>
                </g>
                
                <!-- Link to Building A -->
                <g transform="translate(350, 135)">
                    <rect x="0" y="0" width="300" height="35" rx="8" fill="#ECFDF5" stroke="#A7F3D0" stroke-width="2" />
                    <text x="150" y="22" font-size="12" font-weight="900" fill="#047857" text-anchor="middle">🔗 ทางเชื่อม อาคาร A</text>
                </g>
                
                <!-- Escalator left -->
                <g transform="translate(80, 480)">
                    <rect x="0" y="0" width="85" height="45" rx="6" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1.5" />
                    <text x="42.5" y="27" font-size="11" font-weight="900" fill="#475569" text-anchor="middle">บันไดเลื่อน</text>
                </g>
                
                <!-- Escalator right -->
                <g transform="translate(835, 480)">
                    <rect x="0" y="0" width="85" height="45" rx="6" fill="#F1F5F9" stroke="#E2E8F0" stroke-width="1.5" />
                    <text x="42.5" y="27" font-size="11" font-weight="900" fill="#475569" text-anchor="middle">บันไดเลื่อน</text>
                </g>
                
                <!-- Entrance Gate 3 -->
                <g transform="translate(820, 195)">
                    <rect x="0" y="0" width="100" height="40" rx="8" fill="#F0F9FF" stroke="#BAE6FD" stroke-width="2" />
                    <text x="50" y="25" font-size="12" font-weight="900" fill="#0369A1" text-anchor="middle">🚪 ประตู 3</text>
                </g>
                
                <!-- Main Entrance Gate 2 (Bottom) -->
                <g transform="translate(350, 1145)">
                    <rect x="0" y="0" width="300" height="40" rx="8" fill="#F0F9FF" stroke="#BAE6FD" stroke-width="2" />
                    <text x="150" y="25" font-size="14" font-weight="900" fill="#0369A1" text-anchor="middle">🚪 ประตู 2 (ทางเข้าหลัก)</text>
                </g>
                
                <!-- Walkway text labels -->
                <text x="500" y="520" font-size="14" font-weight="900" fill="#CBD5E1" text-anchor="middle" letter-spacing="4">ทางเดิน WALKWAY</text>
                <text x="500" y="805" font-size="14" font-weight="900" fill="#CBD5E1" text-anchor="middle" letter-spacing="4">ทางเดิน WALKWAY</text>
                
                <!-- Dynamic Map Lots from DB Seeder -->
                @foreach($zones as $zone)
                    @foreach($zone->lots as $lot)
                        @php
                            // Map each database lot to its pre-calculated positions
                            $lotMap = [
                                "261" => ["x" => 390, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "260" => ["x" => 330, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "259" => ["x" => 270, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "258" => ["x" => 210, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "262" => ["x" => 565, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "263" => ["x" => 625, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "264" => ["x" => 685, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "265" => ["x" => 745, "y" => 150, "w" => 45, "h" => 45, "color" => "#F39C12"],
                                "252" => ["x" => 130, "y" => 260, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "251" => ["x" => 130, "y" => 286, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "254" => ["x" => 160, "y" => 260, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "253" => ["x" => 160, "y" => 286, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "224" => ["x" => 105, "y" => 340, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "225" => ["x" => 105, "y" => 366, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "226" => ["x" => 135, "y" => 340, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "227" => ["x" => 135, "y" => 366, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "176" => ["x" => 90, "y" => 420, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "175" => ["x" => 95, "y" => 470, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "125" => ["x" => 105, "y" => 520, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "124" => ["x" => 115, "y" => 570, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "74" => ["x" => 130, "y" => 630, "w" => 26, "h" => 22, "color" => "#F1C40F"],
                                "73" => ["x" => 145, "y" => 680, "w" => 26, "h" => 22, "color" => "#F1C40F"],
                                "45" => ["x" => 170, "y" => 735, "w" => 26, "h" => 22, "color" => "#E67E22"],
                                "44" => ["x" => 190, "y" => 785, "w" => 26, "h" => 22, "color" => "#E67E22"],
                                "32" => ["x" => 215, "y" => 840, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "31" => ["x" => 235, "y" => 890, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "22" => ["x" => 260, "y" => 940, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "21" => ["x" => 280, "y" => 990, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "256" => ["x" => 844, "y" => 260, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "255" => ["x" => 844, "y" => 286, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "258W" => ["x" => 814, "y" => 260, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "257" => ["x" => 814, "y" => 286, "w" => 26, "h" => 22, "color" => "#FDEDEC"],
                                "216" => ["x" => 869, "y" => 340, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "217" => ["x" => 869, "y" => 366, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "218" => ["x" => 839, "y" => 340, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "219" => ["x" => 839, "y" => 366, "w" => 26, "h" => 22, "color" => "#9B59B6"],
                                "174" => ["x" => 884, "y" => 420, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "173" => ["x" => 879, "y" => 470, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "121" => ["x" => 869, "y" => 520, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "120" => ["x" => 859, "y" => 570, "w" => 26, "h" => 22, "color" => "#5DADE2"],
                                "72" => ["x" => 844, "y" => 630, "w" => 26, "h" => 22, "color" => "#F1C40F"],
                                "71" => ["x" => 829, "y" => 680, "w" => 26, "h" => 22, "color" => "#F1C40F"],
                                "43" => ["x" => 804, "y" => 735, "w" => 26, "h" => 22, "color" => "#E67E22"],
                                "42" => ["x" => 784, "y" => 785, "w" => 26, "h" => 22, "color" => "#E67E22"],
                                "30" => ["x" => 759, "y" => 840, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "29" => ["x" => 739, "y" => 890, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "24" => ["x" => 714, "y" => 940, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "23" => ["x" => 694, "y" => 990, "w" => 26, "h" => 22, "color" => "#D35400"],
                                "218W" => ["x" => 180, "y" => 240, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "219W" => ["x" => 180, "y" => 266, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "226W" => ["x" => 212, "y" => 240, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "227W" => ["x" => 212, "y" => 266, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "220" => ["x" => 250, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "221" => ["x" => 250, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "228" => ["x" => 282, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "229" => ["x" => 282, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "222" => ["x" => 320, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "223" => ["x" => 320, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "230" => ["x" => 352, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "231" => ["x" => 352, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "224W" => ["x" => 390, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "225W" => ["x" => 390, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "232" => ["x" => 422, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "233" => ["x" => 422, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "234" => ["x" => 550, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "235" => ["x" => 550, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "242" => ["x" => 582, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "243" => ["x" => 582, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "236" => ["x" => 620, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "237" => ["x" => 620, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "244" => ["x" => 652, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "245" => ["x" => 652, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "238" => ["x" => 690, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "239" => ["x" => 690, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "246" => ["x" => 722, "y" => 240, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "247" => ["x" => 722, "y" => 266, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "240" => ["x" => 760, "y" => 240, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "241" => ["x" => 760, "y" => 266, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "248" => ["x" => 792, "y" => 240, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "249" => ["x" => 792, "y" => 266, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "178" => ["x" => 180, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "177" => ["x" => 180, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "186" => ["x" => 212, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "187" => ["x" => 212, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "180" => ["x" => 250, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "179" => ["x" => 250, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "188" => ["x" => 282, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "189" => ["x" => 282, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "182" => ["x" => 320, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "181" => ["x" => 320, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "190" => ["x" => 352, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "191" => ["x" => 352, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "184" => ["x" => 390, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "183" => ["x" => 390, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "192" => ["x" => 422, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "193" => ["x" => 422, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "194" => ["x" => 550, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "195" => ["x" => 550, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "202" => ["x" => 582, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "203" => ["x" => 582, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "196" => ["x" => 620, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "197" => ["x" => 620, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "204" => ["x" => 652, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "205" => ["x" => 652, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "198" => ["x" => 690, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "199" => ["x" => 690, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "206" => ["x" => 722, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "207" => ["x" => 722, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "200" => ["x" => 760, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "201" => ["x" => 760, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "208" => ["x" => 792, "y" => 320, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "209" => ["x" => 792, "y" => 346, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "138" => ["x" => 180, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "137" => ["x" => 180, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "146" => ["x" => 212, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "147" => ["x" => 212, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "140" => ["x" => 250, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "139" => ["x" => 250, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "148" => ["x" => 282, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "149" => ["x" => 282, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "142" => ["x" => 320, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "141" => ["x" => 320, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "150" => ["x" => 352, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "151" => ["x" => 352, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "144" => ["x" => 390, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "143" => ["x" => 390, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "152" => ["x" => 422, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "153" => ["x" => 422, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "154" => ["x" => 550, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "155" => ["x" => 550, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "162" => ["x" => 582, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "163" => ["x" => 582, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "156" => ["x" => 620, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "157" => ["x" => 620, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "164" => ["x" => 652, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "165" => ["x" => 652, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "158" => ["x" => 690, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "159" => ["x" => 690, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "166" => ["x" => 722, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "167" => ["x" => 722, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "160" => ["x" => 760, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "161" => ["x" => 760, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "168" => ["x" => 792, "y" => 400, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "169" => ["x" => 792, "y" => 426, "w" => 28, "h" => 22, "color" => "#EC87C0"],
                                "108" => ["x" => 180, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "107" => ["x" => 180, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "116" => ["x" => 212, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "117" => ["x" => 212, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "110" => ["x" => 250, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "109" => ["x" => 250, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "118" => ["x" => 282, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "119" => ["x" => 282, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "112" => ["x" => 320, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "111" => ["x" => 320, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "120W" => ["x" => 352, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "121W" => ["x" => 352, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "114" => ["x" => 390, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "113" => ["x" => 390, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "122" => ["x" => 422, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "123" => ["x" => 422, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "124W" => ["x" => 550, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "123W" => ["x" => 550, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "132" => ["x" => 582, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "131" => ["x" => 582, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "126" => ["x" => 620, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "125W" => ["x" => 620, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "134" => ["x" => 652, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "133" => ["x" => 652, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "128" => ["x" => 690, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "127" => ["x" => 690, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "136" => ["x" => 722, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "135" => ["x" => 722, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "130" => ["x" => 760, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "129" => ["x" => 760, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "138W" => ["x" => 792, "y" => 580, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "137W" => ["x" => 792, "y" => 606, "w" => 28, "h" => 22, "color" => "#5DADE2"],
                                "76" => ["x" => 180, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "75" => ["x" => 180, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "84" => ["x" => 212, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "85" => ["x" => 212, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "78" => ["x" => 250, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "77" => ["x" => 250, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "86" => ["x" => 282, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "87" => ["x" => 282, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "80" => ["x" => 320, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "79" => ["x" => 320, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "88" => ["x" => 352, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "89" => ["x" => 352, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "82" => ["x" => 390, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "81" => ["x" => 390, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "90" => ["x" => 422, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "91" => ["x" => 422, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "92" => ["x" => 550, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "93" => ["x" => 550, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "100" => ["x" => 582, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "101" => ["x" => 582, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "94" => ["x" => 620, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "95" => ["x" => 620, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "102" => ["x" => 652, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "103" => ["x" => 652, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "96" => ["x" => 690, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "97" => ["x" => 690, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "104" => ["x" => 722, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "105" => ["x" => 722, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "98" => ["x" => 760, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "99" => ["x" => 760, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "106" => ["x" => 792, "y" => 660, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "107W" => ["x" => 792, "y" => 686, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "48" => ["x" => 250, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "47" => ["x" => 250, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "54" => ["x" => 282, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "55" => ["x" => 282, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "50" => ["x" => 320, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "49" => ["x" => 320, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "56" => ["x" => 352, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "57" => ["x" => 352, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "52" => ["x" => 390, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "51" => ["x" => 390, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "58" => ["x" => 422, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "59" => ["x" => 422, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "60" => ["x" => 550, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "59W" => ["x" => 550, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "66" => ["x" => 582, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "67" => ["x" => 582, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "62" => ["x" => 620, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "61" => ["x" => 620, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "68" => ["x" => 652, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "69" => ["x" => 652, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "64" => ["x" => 690, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "63" => ["x" => 690, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "70" => ["x" => 722, "y" => 740, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "71W" => ["x" => 722, "y" => 766, "w" => 28, "h" => 22, "color" => "#F1C40F"],
                                "26" => ["x" => 320, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "25" => ["x" => 320, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "32W" => ["x" => 352, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "33" => ["x" => 352, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "28" => ["x" => 390, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "27" => ["x" => 390, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "34" => ["x" => 422, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "35" => ["x" => 422, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "34W" => ["x" => 550, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "33W" => ["x" => 550, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "40" => ["x" => 582, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "39" => ["x" => 582, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "36" => ["x" => 620, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "35W" => ["x" => 620, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "42W" => ["x" => 652, "y" => 820, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "41" => ["x" => 652, "y" => 846, "w" => 28, "h" => 22, "color" => "#E67E22"],
                                "2" => ["x" => 285, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "1" => ["x" => 285, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "4" => ["x" => 317, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "3" => ["x" => 317, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "6" => ["x" => 355, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "5" => ["x" => 355, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "8" => ["x" => 387, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "7" => ["x" => 387, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "10" => ["x" => 425, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "9" => ["x" => 425, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "12" => ["x" => 457, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "11" => ["x" => 457, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "14" => ["x" => 535, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "13" => ["x" => 535, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "16" => ["x" => 567, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "15" => ["x" => 567, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "18" => ["x" => 605, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "17" => ["x" => 605, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "20" => ["x" => 637, "y" => 900, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "19" => ["x" => 637, "y" => 926, "w" => 28, "h" => 22, "color" => "#D35400"],
                                "C" => ["x" => 425, "y" => 1070, "w" => 32, "h" => 24, "color" => "#D35400"],
                                "D" => ["x" => 465, "y" => 1070, "w" => 32, "h" => 24, "color" => "#D35400"],
                                "B" => ["x" => 425, "y" => 1105, "w" => 32, "h" => 24, "color" => "#D35400"],
                                "A" => ["x" => 465, "y" => 1105, "w" => 32, "h" => 24, "color" => "#D35400"],
                            ];
                            $pos = $lotMap[$lot->lot_code] ?? null;
                        @endphp
                        @if($pos)
                            <g class="lot-group" data-lot-code="{{ $lot->lot_code }}" id="lot-group-{{ $lot->lot_code }}" style="cursor: pointer;">
                                <!-- Background coloring matching the pricing zone -->
                                <rect class="lot-bg lot-available"
                                      id="lot-bg-{{ $lot->lot_code }}"
                                      x="{{ $pos["x"] - 3 }}" y="{{ $pos["y"] - 3 }}"
                                      width="{{ $pos["w"] + 6 }}" height="{{ $pos["h"] + 6 }}"
                                      rx="6"
                                      fill="{{ $pos["color"] }}"
                                      opacity="0.5"
                                      style="transition: all 0.2s ease;" />
                                
                                <!-- Tent Icon Image -->
                                <image href="{{ asset("images/tent.png") }}"
                                       x="{{ $pos["x"] }}" y="{{ $pos["y"] }}"
                                       width="{{ $pos["w"] }}" height="{{ $pos["h"] }}"
                                       class="market-lot lot-available"
                                       id="lot-{{ $lot->lot_code }}"
                                       data-lot-id="{{ $lot->id }}"
                                       data-lot-code="{{ $lot->lot_code }}"
                                       data-display-name="{{ $lot->display_name ?? $lot->lot_code }}"
                                       data-cx="{{ $pos["x"] + $pos["w"]/2 }}"
                                       data-cy="{{ $pos["y"] + $pos["h"]/2 }}"
                                       style="transition: all 0.2s ease;" />
                                
                                <!-- Number Pill Badge -->
                                <rect x="{{ $pos["x"] + $pos["w"]/2 - 13 }}" y="{{ $pos["y"] + $pos["h"] - 5 }}" width="26" height="11" rx="3" fill="#ffffff" stroke="#cbd5e1" stroke-width="0.75" opacity="0.95" style="pointer-events: none;" />
                                <text x="{{ $pos["x"] + $pos["w"]/2 }}" y="{{ $pos["y"] + $pos["h"] + 3 }}" class="lot-text" font-size="7.5" font-weight="900" text-anchor="middle" fill="#0f172a" style="pointer-events: none;">{{ $lot->display_name ?? $lot->lot_code }}</text>
                            </g>
                        @endif
                    @endforeach
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
                            el.className.baseVal = 'market-lot';
                            el.classList.add(`lot-${lot.status}`);
                            if (selectedLot === lot.lot_code) {
                                el.classList.add('lot-selected');
                            }
                        }
                        const elBg = document.getElementById(`lot-bg-${lot.lot_code}`);
                        if (elBg) {
                            elBg.className.baseVal = 'lot-bg';
                            elBg.classList.add(`lot-${lot.status}`);
                            if (selectedLot === lot.lot_code) {
                                elBg.classList.add('lot-selected');
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
            // Clear current selections
            document.querySelectorAll('.lot-selected').forEach(el => el.classList.remove('lot-selected'));
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

                // Highlight selected lot (both tent and bg)
                document.querySelectorAll('.lot-selected').forEach(el => el.classList.remove('lot-selected'));
                this.classList.add('lot-selected');
                const bgEl = document.getElementById(`lot-bg-${code}`);
                if (bgEl) bgEl.classList.add('lot-selected');

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
