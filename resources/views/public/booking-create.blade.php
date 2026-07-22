@extends('layouts.public')

@section('title', 'จองเต็นท์/จองเคาน์เตอร์')

@section('styles')
<style>
    /* Mobile phone viewport optimization - Fits completely without scrolling */
    @media (max-width: 767px) {
        .header-bar, footer {
            display: none !important;
        }
        .content-container {
            padding: 4px !important;
            margin: 0 !important;
            max-width: 100% !important;
            height: 100dvh !important;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        html, body {
            height: 100dvh;
            overflow: hidden !important;
            background-color: #8ec63f !important;
        }
    }

    .paper-container {
        width: 100%;
        max-width: 460px;
        margin: 0 auto;
        background-color: #8ec63f;
        border: 3px solid #365507;
        border-radius: 8px;
        padding: 6px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-family: 'Prompt', sans-serif;
        color: #000;
        max-height: calc(100dvh - 8px);
        overflow-y: auto;
    }

    /* Scrollbar styling */
    .paper-container::-webkit-scrollbar {
        width: 4px;
    }
    .paper-container::-webkit-scrollbar-thumb {
        background: #365507;
        border-radius: 4px;
    }

    /* Title Header */
    .paper-title {
        background: #8ec63f;
        border: 2px solid #365507;
        border-radius: 6px;
        font-weight: 800;
        font-size: 18px;
        text-align: center;
        padding: 4px;
        color: #122400;
        margin: 0;
        letter-spacing: 0.5px;
    }

    /* Grid Rows */
    .paper-grid-row {
        display: grid;
        border: 2px solid #365507;
        border-radius: 6px;
        overflow: hidden;
        background: #ffffff;
        min-height: 28px;
    }

    .paper-grid-2col {
        grid-template-columns: 75px 1fr;
    }

    .paper-grid-4col {
        grid-template-columns: 60px 1fr 65px 1fr;
    }

    .paper-grid-split {
        grid-template-columns: 1fr 1fr;
        background: #8ec63f;
    }

    .paper-label {
        background: #8ec63f;
        font-weight: 700;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 2px solid #365507;
        padding: 1px 4px;
        color: #122400;
        white-space: nowrap;
    }

    .paper-cell {
        background: #ffffff;
        display: flex;
        align-items: center;
        padding: 1px 4px;
        min-width: 0;
    }

    .paper-cell + .paper-label {
        border-left: 2px solid #365507;
    }

    /* Inputs & Selects */
    .p-input, .p-select {
        width: 100%;
        height: 26px;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 12px;
        font-weight: 600;
        color: #000;
        padding: 0 2px;
        box-sizing: border-box;
    }

    /* Checkbox & Radio Labels */
    .paper-check-label {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-weight: 800;
        font-size: 13px;
        padding: 3px;
        cursor: pointer;
        color: #122400;
        user-select: none;
    }

    .paper-check-label input[type="checkbox"],
    .paper-check-label input[type="radio"] {
        width: 16px;
        height: 16px;
        accent-color: #365507;
        cursor: pointer;
    }

    /* Dynamic Equipment Rows */
    .equip-box {
        border: 2px solid #365507;
        border-radius: 6px;
        background: #8ec63f;
        padding: 3px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .equip-item-row {
        display: grid;
        grid-template-columns: 32px 1fr 24px 1fr 32px 1fr 40px 24px;
        align-items: center;
        gap: 2px;
        background: #ffffff;
        border: 1px solid #365507;
        border-radius: 4px;
        padding: 2px;
    }

    .equip-item-row.counter-row {
        grid-template-columns: 32px 1fr 70px 1fr 40px 24px;
    }

    .btn-add-inline {
        background: #8ec63f;
        border: 1px solid #365507;
        border-radius: 4px;
        font-weight: 800;
        font-size: 11px;
        color: #122400;
        cursor: pointer;
        padding: 2px 4px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .btn-remove-inline {
        background: #ef4444;
        color: #fff;
        border: 1px solid #991b1b;
        border-radius: 4px;
        font-weight: 800;
        font-size: 11px;
        cursor: pointer;
        padding: 0 4px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Lock Grid Row */
    .paper-grid-lock {
        grid-template-columns: 52px 1fr 52px 1fr 52px 1fr;
    }

    .lot-group-list {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .lot-group-item {
        display: grid;
        grid-template-columns: 52px 1fr 52px 1fr 52px 1fr 24px;
        gap: 2px;
        align-items: center;
        background: #ffffff;
        border: 1px solid #365507;
        border-radius: 4px;
        padding: 2px;
    }

    /* Action Buttons */
    .btn-submit-cyan {
        background: #00c2ff;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        border: 2px solid #0088b3;
        border-radius: 6px;
        width: 100%;
        height: 36px;
        font-size: 17px;
        font-weight: 800;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 1px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.12);
        transition: transform 0.1s;
    }

    .btn-submit-cyan:active {
        transform: scale(0.99);
    }

    .action-btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
    }

    .btn-orange-map {
        background: #f97316;
        color: #ffffff;
        border: 2px solid #c2410c;
        border-radius: 6px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        height: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .btn-white-status {
        background: #ffffff;
        color: #000000;
        border: 2px solid #365507;
        border-radius: 6px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 13px;
        height: 32px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .contact-footer {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 14px;
        color: #122400;
        padding: 2px 0 0;
    }

    .contact-footer a {
        color: #122400;
        text-decoration: none;
    }

    .error-alert-box {
        background: #fef2f2;
        border: 2px solid #ef4444;
        border-radius: 6px;
        padding: 4px 8px;
        color: #991b1b;
        font-size: 11px;
    }
</style>
@endsection

@section('content')
<div class="paper-container">
    <!-- Header Title -->
    <h1 class="paper-title">จองเต็นท์/จองเคาน์เตอร์</h1>

    @if ($errors->any())
        <div class="error-alert-box">
            <strong>เกิดข้อผิดพลาด:</strong> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('public.booking.store') }}" method="POST" enctype="multipart/form-data" id="bookingForm">
        @csrf

        <!-- Hidden input for lot mode -->
        @php
            $defaultMode = count($selectedGroups ?? []) > 1 ? 'multiple' : 'single';
            $oldMode = old('lot_mode', old('lot_groups') ? 'multiple' : $defaultMode);
        @endphp
        <input type="hidden" name="lot_mode" id="lot_mode" value="{{ $oldMode }}">

        <div style="display: flex; flex-direction: column; gap: 4px;">

            <!-- Row 1: วันที่ใช้ -->
            <div class="paper-grid-row paper-grid-2col">
                <div class="paper-label">วันที่ใช้</div>
                <div class="paper-cell">
                    <input type="date" id="use_date" name="use_date" class="p-input" value="{{ old('use_date', $date) }}" min="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <!-- Row 2: ชื่อร้าน & เบอร์โทร -->
            <div class="paper-grid-row paper-grid-4col">
                <div class="paper-label">ชื่อร้าน</div>
                <div class="paper-cell">
                    <input type="text" id="shop_name" name="shop_name" class="p-input" value="{{ old('shop_name') }}" placeholder="กรอกชื่อร้าน" required>
                </div>
                <div class="paper-label">เบอร์โทร</div>
                <div class="paper-cell">
                    <input type="tel" id="customer_phone" name="customer_phone" class="p-input" value="{{ old('customer_phone') }}" placeholder="08XXXXXXXX" required>
                </div>
            </div>

            <!-- Row 3: Checkboxes เต็นท์ / เคาน์เตอร์ -->
            <div class="paper-grid-row paper-grid-split">
                <label class="paper-check-label">
                    <input type="checkbox" name="wants_tent" value="1" id="chk_tent" data-equip-toggle="tent" {{ old('wants_tent', old('wants_counter') ? null : true) ? 'checked' : '' }}>
                    <span>เต็นท์</span>
                </label>
                <label class="paper-check-label">
                    <input type="checkbox" name="wants_counter" value="1" id="chk_counter" data-equip-toggle="counter" {{ old('wants_counter') ? 'checked' : '' }}>
                    <span>เคาน์เตอร์</span>
                </label>
            </div>

            <!-- Row 4A: Tent Items Section -->
            @php
                $tentItemRows = old('tent_items', [['size' => '', 'color' => '', 'quantity' => 1]]);
            @endphp
            <div class="equip-box" id="box_tent" data-equipment-row style="{{ old('wants_tent', old('wants_counter') ? null : true) ? '' : 'display:none;' }}">
                <div class="equipment-list" id="tent-item-list">
                    @foreach($tentItemRows as $index => $item)
                        <div class="equip-item-row">
                            <span class="paper-label" style="border:none;background:transparent;">ขนาด</span>
                            <select name="tent_items[{{ $index }}][size]" class="p-select tent-size">
                                <option value="">เลือกขนาด</option>
                                @foreach($tentSizes as $size)
                                    <option value="{{ $size }}" @selected(($item['size'] ?? '') === $size)>{{ $size }}</option>
                                @endforeach
                            </select>

                            <span class="paper-label" style="border:none;background:transparent;">สี</span>
                            <select name="tent_items[{{ $index }}][color]" class="p-select tent-color">
                                <option value="">เลือกสี</option>
                                @foreach($equipmentColors as $color)
                                    <option value="{{ $color }}" @selected(($item['color'] ?? '') === $color)>{{ $color }}</option>
                                @endforeach
                            </select>

                            <span class="paper-label" style="border:none;background:transparent;">จำนวน</span>
                            <input type="number" name="tent_items[{{ $index }}][quantity]" class="p-input tent-qty" value="{{ $item['quantity'] ?? 1 }}" min="1" max="99" style="text-align:center;">

                            <button type="button" class="btn-add-inline" id="add-tent-item" title="เพิ่มเต็นท์ต่างขนาดหรือสี" aria-label="เพิ่มเต็นท์ต่างขนาดหรือสี">+เพิ่ม</button>
                            <button type="button" class="btn-remove-inline remove-equip-btn" style="{{ count($tentItemRows) > 1 ? '' : 'opacity:0.3;' }}">&times;</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Row 4B: Counter Items Section -->
            @php
                $counterItemRows = old('counter_items', [['size' => '', 'quantity' => 1]]);
            @endphp
            <div class="equip-box" id="box_counter" data-equipment-row style="{{ old('wants_counter') ? '' : 'display:none;' }}">
                <div class="equipment-list" id="counter-item-list">
                    @foreach($counterItemRows as $index => $item)
                        <div class="equip-item-row counter-row">
                            <span class="paper-label" style="border:none;background:transparent;">ขนาด</span>
                            <select name="counter_items[{{ $index }}][size]" class="p-select counter-size">
                                <option value="">เลือกขนาดเคาน์เตอร์</option>
                                @foreach($counterSizes as $size)
                                    <option value="{{ $size }}" @selected(($item['size'] ?? '') === $size)>{{ $size }}</option>
                                @endforeach
                            </select>

                            <span class="paper-label" style="border:none;background:transparent;">เลขเคาน์เตอร์</span>
                            <input type="number" name="counter_items[{{ $index }}][quantity]" class="p-input counter-qty" value="{{ $item['quantity'] ?? 1 }}" min="1" max="99" style="text-align:center;">

                            <button type="button" class="btn-add-inline" id="add-counter-item" title="เพิ่มเคาน์เตอร์ต่างขนาด" aria-label="เพิ่มเคาน์เตอร์ต่างขนาด">+เพิ่ม</button>
                            <button type="button" class="btn-remove-inline remove-equip-btn" style="{{ count($counterItemRows) > 1 ? '' : 'opacity:0.3;' }}">&times;</button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Row 5: หมายเหตุ -->
            <div class="paper-grid-row paper-grid-2col">
                <div class="paper-label">หมายเหตุ</div>
                <div class="paper-cell">
                    <input type="text" id="customer_note" name="customer_note" class="p-input" value="{{ old('customer_note') }}" placeholder="ระบุเพิ่มเติม (ถ้ามี)">
                </div>
            </div>

            <!-- Row 6: Mode 선택 1 ล็อค / มากกว่า 1 ล็อค -->
            <div class="paper-grid-row paper-grid-split">
                <label class="paper-check-label">
                    <input type="radio" name="lot_mode_radio" value="single" id="mode_single" {{ $oldMode === 'single' ? 'checked' : '' }}>
                    <span>1 ล็อค</span>
                </label>
                <label class="paper-check-label">
                    <input type="radio" name="lot_mode_radio" value="multiple" id="mode_multiple" {{ $oldMode === 'multiple' ? 'checked' : '' }}>
                    <span>มากกว่า 1 ล็อค</span>
                </label>
            </div>

            <!-- Row 7: Lock Input Details -->
            <div id="single-lot-section" class="paper-grid-row paper-grid-lock" style="{{ $oldMode === 'multiple' ? 'display:none;' : '' }}">
                <div class="paper-label">ตัวอักษร</div>
                <div class="paper-cell">
                    <select name="lot_prefix" class="p-select" {{ $oldMode === 'multiple' ? '' : 'required' }}>
                        <option value="">โซน</option>
                        @foreach($lotPrefixes as $prefix)
                            <option value="{{ $prefix }}" {{ old('lot_prefix', $selectedPrefix) == $prefix ? 'selected' : '' }}>{{ $prefix }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="paper-label">เลขล็อค</div>
                <div class="paper-cell">
                    <input type="number" name="lot_number_from" class="p-input" value="{{ old('lot_number_from', $selectedFrom) }}" min="1" max="999" placeholder="เริ่ม" {{ $oldMode === 'multiple' ? '' : 'required' }}>
                </div>
                <div class="paper-label">เลขล็อค</div>
                <div class="paper-cell">
                    <input type="number" name="lot_number_to" class="p-input" value="{{ old('lot_number_to', $selectedTo) }}" min="1" max="999" placeholder="ถึง">
                </div>
            </div>

            <!-- Multiple Lock Section -->
            @php
                $oldGroups = old('lot_groups', $selectedGroups ?: [
                    ['prefix' => $selectedPrefix, 'from' => $selectedFrom, 'to' => $selectedTo],
                ]);
            @endphp
            <div id="multiple-lot-section" style="{{ $oldMode === 'multiple' ? '' : 'display:none;' }}">
                <div class="lot-group-list" id="lot-group-list">
                    @foreach($oldGroups as $index => $group)
                        <div class="lot-group-item">
                            <span class="paper-label" style="border:none;background:transparent;">ตัวอักษร</span>
                            <select name="lot_groups[{{ $index }}][prefix]" class="p-select">
                                <option value="">โซน</option>
                                @foreach($lotPrefixes as $prefix)
                                    <option value="{{ $prefix }}" {{ ($group['prefix'] ?? null) == $prefix ? 'selected' : '' }}>{{ $prefix }}</option>
                                @endforeach
                            </select>
                            <span class="paper-label" style="border:none;background:transparent;">เลขล็อค</span>
                            <input type="number" name="lot_groups[{{ $index }}][from]" class="p-input" value="{{ $group['from'] ?? '' }}" min="1" max="999" placeholder="เริ่ม">
                            <span class="paper-label" style="border:none;background:transparent;">เลขล็อค</span>
                            <input type="number" name="lot_groups[{{ $index }}][to]" class="p-input" value="{{ $group['to'] ?? '' }}" min="1" max="999" placeholder="ถึง">
                            <button type="button" class="btn-remove-inline remove-lot-btn">&times;</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="btn-add-inline" id="add-lot-group" style="width:100%;margin-top:3px;height:26px;">+เพิ่มล็อค/โซน</button>
            </div>

            <!-- Templates for Dynamic Rows -->
            <template id="tent-item-template">
                <div class="equip-item-row">
                    <span class="paper-label" style="border:none;background:transparent;">ขนาด</span>
                    <select name="__SIZE__" class="p-select tent-size">
                        <option value="">เลือกขนาด</option>
                        @foreach($tentSizes as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="paper-label" style="border:none;background:transparent;">สี</span>
                    <select name="__COLOR__" class="p-select tent-color">
                        <option value="">เลือกสี</option>
                        @foreach($equipmentColors as $color)
                            <option value="{{ $color }}">{{ $color }}</option>
                        @endforeach
                    </select>
                    <span class="paper-label" style="border:none;background:transparent;">จำนวน</span>
                    <input type="number" name="__QUANTITY__" class="p-input tent-qty" value="1" min="1" max="99" style="text-align:center;">
                    <button type="button" class="btn-add-inline add-tent-trigger">+เพิ่ม</button>
                    <button type="button" class="btn-remove-inline remove-equip-btn">&times;</button>
                </div>
            </template>

            <template id="counter-item-template">
                <div class="equip-item-row counter-row">
                    <span class="paper-label" style="border:none;background:transparent;">ขนาด</span>
                    <select name="__SIZE__" class="p-select counter-size">
                        <option value="">เลือกขนาดเคาน์เตอร์</option>
                        @foreach($counterSizes as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="paper-label" style="border:none;background:transparent;">เลขเคาน์เตอร์</span>
                    <input type="number" name="__QUANTITY__" class="p-input counter-qty" value="1" min="1" max="99" style="text-align:center;">
                    <button type="button" class="btn-add-inline add-counter-trigger">+เพิ่ม</button>
                    <button type="button" class="btn-remove-inline remove-equip-btn">&times;</button>
                </div>
            </template>

            <template id="lot-group-template">
                <div class="lot-group-item">
                    <span class="paper-label" style="border:none;background:transparent;">ตัวอักษร</span>
                    <select name="__PREFIX_NAME__" class="p-select">
                        <option value="">โซน</option>
                        @foreach($lotPrefixes as $prefix)
                            <option value="{{ $prefix }}">{{ $prefix }}</option>
                        @endforeach
                    </select>
                    <span class="paper-label" style="border:none;background:transparent;">เลขล็อค</span>
                    <input type="number" name="__FROM_NAME__" class="p-input" min="1" max="999" placeholder="เริ่ม">
                    <span class="paper-label" style="border:none;background:transparent;">เลขล็อค</span>
                    <input type="number" name="__TO_NAME__" class="p-input" min="1" max="999" placeholder="ถึง">
                    <button type="button" class="btn-remove-inline remove-lot-btn">&times;</button>
                </div>
            </template>

            <!-- Row 8: วิธีชำระเงิน -->
            @php
                $selectedPaymentMethod = old('payment_method', 'slip');
            @endphp
            <div class="paper-grid-row paper-grid-split">
                <label class="paper-check-label">
                    <input type="radio" name="payment_method" value="slip" id="pay_slip" {{ $selectedPaymentMethod === 'slip' ? 'checked' : '' }}>
                    <span>ชำระแล้ว แนบสลิป</span>
                </label>
                <label class="paper-check-label">
                    <input type="radio" name="payment_method" value="front_store" id="pay_front" {{ $selectedPaymentMethod === 'front_store' ? 'checked' : '' }}>
                    <span>ชำระหน้าร้าน</span>
                </label>
            </div>

            <!-- File Upload Row -->
            <div class="paper-grid-row paper-grid-2col" id="slip-row" style="{{ $selectedPaymentMethod === 'front_store' ? 'display:none;' : '' }}">
                <div class="paper-label">แนบสลิป</div>
                <div class="paper-cell">
                    <input type="file" name="payment_slip" id="payment_slip" class="p-input" accept="image/jpeg,image/png,image/webp" style="padding-top:3px;">
                </div>
            </div>

            <!-- Submit Button (Full Width Bright Cyan) -->
            <button type="submit" class="btn-submit-cyan">
                ส่งจอง
            </button>

            <!-- Navigation Buttons (Orange Map & White Check Status) -->
            <div class="action-btn-group">
                <a href="{{ route('public.map', ['date' => $date]) }}" class="btn-orange-map">
                    ดูผังตลาด
                </a>
                <a href="{{ route('public.booking.check') }}" class="btn-white-status">
                    ตรวจสอบสถานะ
                </a>
            </div>

            <!-- Footer Contact Info -->
            <div class="contact-footer">
                <span>ติดต่อแอดมิน</span>
                <a href="tel:086403174">086403174</a>
            </div>

        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chkTent = document.getElementById('chk_tent');
        const chkCounter = document.getElementById('chk_counter');
        const boxTent = document.getElementById('box_tent');
        const boxCounter = document.getElementById('box_counter');
        const tentItemList = document.getElementById('tent-item-list');
        const counterItemList = document.getElementById('counter-item-list');
        const tentItemTemplate = document.getElementById('tent-item-template');
        const counterItemTemplate = document.getElementById('counter-item-template');

        const modeInput = document.getElementById('lot_mode');
        const modeSingle = document.getElementById('mode_single');
        const modeMultiple = document.getElementById('mode_multiple');
        const singleSection = document.getElementById('single-lot-section');
        const multipleSection = document.getElementById('multiple-lot-section');
        const singleRequiredFields = singleSection.querySelectorAll('[name="lot_prefix"], [name="lot_number_from"]');
        const groupList = document.getElementById('lot-group-list');
        const addLotBtn = document.getElementById('add-lot-group');
        const lotGroupTemplate = document.getElementById('lot-group-template');

        const paySlipRadio = document.getElementById('pay_slip');
        const payFrontRadio = document.getElementById('pay_front');
        const slipRow = document.getElementById('slip-row');
        const paymentSlipInput = document.getElementById('payment_slip');

        // Toggle Equipment Visibility
        function syncEquipmentBoxes() {
            boxTent.style.display = chkTent.checked ? '' : 'none';
            boxCounter.style.display = chkCounter.checked ? '' : 'none';

            boxTent.querySelectorAll('select, input').forEach(el => el.disabled = !chkTent.checked);
            boxCounter.querySelectorAll('select, input').forEach(el => el.disabled = !chkCounter.checked);
        }

        chkTent.addEventListener('change', syncEquipmentBoxes);
        chkCounter.addEventListener('change', syncEquipmentBoxes);
        syncEquipmentBoxes();

        // Dynamic Tent Items
        document.addEventListener('click', function(e) {
            if (e.target.id === 'add-tent-item' || e.target.classList.contains('add-tent-trigger')) {
                addTentRow();
            }
            if (e.target.id === 'add-counter-item' || e.target.classList.contains('add-counter-trigger')) {
                addCounterRow();
            }
        });

        function addTentRow() {
            const index = Date.now().toString();
            const html = tentItemTemplate.innerHTML
                .replace('__SIZE__', `tent_items[${index}][size]`)
                .replace('__COLOR__', `tent_items[${index}][color]`)
                .replace('__QUANTITY__', `tent_items[${index}][quantity]`);
            const div = document.createElement('div');
            div.innerHTML = html;
            tentItemList.appendChild(div.firstElementChild);
            refreshRemoveButtons(tentItemList, '.remove-equip-btn');
        }

        function addCounterRow() {
            const index = Date.now().toString();
            const html = counterItemTemplate.innerHTML
                .replace('__SIZE__', `counter_items[${index}][size]`)
                .replace('__QUANTITY__', `counter_items[${index}][quantity]`);
            const div = document.createElement('div');
            div.innerHTML = html;
            counterItemList.appendChild(div.firstElementChild);
            refreshRemoveButtons(counterItemList, '.remove-equip-btn');
        }

        function refreshRemoveButtons(container, selector) {
            const items = container.querySelectorAll(selector);
            items.forEach(btn => {
                btn.style.opacity = items.length <= 1 ? '0.3' : '1';
                btn.disabled = items.length <= 1;
            });
        }

        tentItemList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-equip-btn') && !e.target.disabled) {
                e.target.closest('.equip-item-row').remove();
                refreshRemoveButtons(tentItemList, '.remove-equip-btn');
            }
        });

        counterItemList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-equip-btn') && !e.target.disabled) {
                e.target.closest('.equip-item-row').remove();
                refreshRemoveButtons(counterItemList, '.remove-equip-btn');
            }
        });

        // Lot Mode Switch
        function setLotMode(mode) {
            modeInput.value = mode;
            singleSection.style.display = mode === 'single' ? '' : 'none';
            multipleSection.style.display = mode === 'multiple' ? '' : 'none';
            singleRequiredFields.forEach(f => f.required = (mode === 'single'));
            if (mode === 'single') {
                modeSingle.checked = true;
            } else {
                modeMultiple.checked = true;
                if (groupList.children.length === 0) {
                    addLotGroup();
                }
            }
        }

        modeSingle.addEventListener('change', () => setLotMode('single'));
        modeMultiple.addEventListener('change', () => setLotMode('multiple'));

        function addLotGroup() {
            const index = Date.now().toString();
            const html = lotGroupTemplate.innerHTML
                .replace('__PREFIX_NAME__', `lot_groups[${index}][prefix]`)
                .replace('__FROM_NAME__', `lot_groups[${index}][from]`)
                .replace('__TO_NAME__', `lot_groups[${index}][to]`);
            const div = document.createElement('div');
            div.innerHTML = html;
            groupList.appendChild(div.firstElementChild);
            refreshRemoveButtons(groupList, '.remove-lot-btn');
        }

        addLotBtn.addEventListener('click', addLotGroup);
        groupList.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-lot-btn') && !e.target.disabled) {
                e.target.closest('.lot-group-item').remove();
                refreshRemoveButtons(groupList, '.remove-lot-btn');
            }
        });

        // Payment Method Switch
        function setPaymentMethod() {
            const isSlip = paySlipRadio.checked;
            slipRow.style.display = isSlip ? '' : 'none';
            paymentSlipInput.required = isSlip;
        }

        paySlipRadio.addEventListener('change', setPaymentMethod);
        payFrontRadio.addEventListener('change', setPaymentMethod);
        setPaymentMethod();

        // Initial setup
        setLotMode(modeInput.value || 'single');
        refreshRemoveButtons(tentItemList, '.remove-equip-btn');
        refreshRemoveButtons(counterItemList, '.remove-equip-btn');
        refreshRemoveButtons(groupList, '.remove-lot-btn');
    });
</script>
@endsection
