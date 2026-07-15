@extends('layouts.public')

@section('title', 'กรอกรายละเอียดการสั่งจองอุปกรณ์')

@section('styles')
<style>
    .booking-paper {
        max-width: 760px;
        margin: 0 auto;
        padding: 26px;
    }

    .booking-paper-title {
        font-size: 24px;
        margin-bottom: 22px;
    }

    .booking-form-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 16px;
    }

    .booking-field {
        grid-column: span 12;
    }

    .booking-field.half {
        grid-column: span 6;
    }

    .booking-field.third {
        grid-column: span 4;
    }

    .booking-field.quarter {
        grid-column: span 3;
    }

    .booking-line {
        display: grid;
        grid-template-columns: 150px minmax(0, 1fr);
        gap: 12px;
        align-items: center;
    }

    .booking-line .cute-label {
        margin: 0;
        font-size: 18px;
        justify-content: flex-start;
    }

    .equipment-row {
        border: 2px solid var(--border-cute);
        background: var(--bg-page);
        border-radius: 18px;
        padding: 14px;
    }

    .equipment-head {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 900;
        font-size: 18px;
        margin-bottom: 12px;
    }

    .equipment-inputs {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(120px, 160px);
        gap: 12px;
    }

    .checkbox-line {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        cursor: pointer;
    }

    .checkbox-line input {
        width: 20px;
        height: 20px;
        accent-color: var(--primary);
    }

    .lot-range-grid {
        display: grid;
        grid-template-columns: minmax(120px, 180px) minmax(90px, 1fr) auto minmax(90px, 1fr);
        gap: 10px;
        align-items: center;
    }

    .lot-mode-tabs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }

    .lot-mode-tab {
        border: 1px solid var(--border-cute);
        border-radius: 14px;
        padding: 10px 12px;
        background: var(--bg-card-soft);
        color: var(--text-muted);
        font-weight: 800;
        cursor: pointer;
        text-align: center;
    }

    .lot-mode-tab.is-active {
        border-color: var(--primary);
        color: var(--text-dark);
        background: rgba(56, 189, 248, 0.14);
    }

    .lot-group-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .lot-group-row {
        display: grid;
        grid-template-columns: minmax(110px, 160px) minmax(80px, 1fr) auto minmax(80px, 1fr) 42px;
        gap: 8px;
        align-items: center;
    }

    .lot-remove-btn,
    .lot-add-btn {
        border: 1px solid var(--border-cute);
        border-radius: 12px;
        background: var(--bg-card-soft);
        color: var(--text-dark);
        min-height: 42px;
        font-weight: 900;
        cursor: pointer;
    }

    .lot-add-btn {
        width: 100%;
        margin-top: 10px;
        color: var(--primary);
    }

    @media (max-width: 720px) {
        .booking-paper {
            padding: 16px;
            border-radius: 18px;
        }

        .booking-paper-title {
            font-size: 19px;
        }

        .booking-field.half,
        .booking-field.third,
        .booking-field.quarter {
            grid-column: span 12;
        }

        .booking-line,
        .equipment-inputs,
        .lot-range-grid,
        .lot-group-row {
            grid-template-columns: 1fr;
        }

        .booking-line .cute-label,
        .equipment-head {
            font-size: 16px;
        }

        .equipment-row {
            padding: 12px;
            border-radius: 16px;
        }

        .checkbox-line {
            min-height: 44px;
        }

        .booking-form-actions {
            flex-direction: column-reverse;
            margin-top: 22px !important;
        }

        .lot-mode-tabs {
            grid-template-columns: 1fr;
        }

        .lot-remove-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
    <div class="cute-card booking-paper">
        <h2 class="cute-card-title booking-paper-title">
            <i class="fa-solid fa-file-invoice"></i> จองเต็นท์/เคาน์เตอร์
        </h2>
        <p style="margin:-8px 0 20px;color:var(--text-muted);font-size:14px;">
            กรอกเลขล็อคของร้านได้เลย ไม่จำเป็นต้องเลือกผ่านแผนผัง
        </p>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex:1;">
                    <strong style="display:block;margin-bottom:5px;">เกิดข้อผิดพลาดในการกรอกข้อมูล:</strong>
                    <ul style="margin:0;padding-left:20px;font-size:14px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('public.booking.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="booking-form-grid">
                <div class="booking-field">
                    <div class="booking-line">
                        <label class="cute-label" for="use_date">วันที่ใช้ *</label>
                        <input type="date" id="use_date" name="use_date" class="cute-input" value="{{ old('use_date', $date) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="booking-field">
                    <div class="equipment-row">
                        <label class="equipment-head">
                            <input type="checkbox" name="wants_tent" value="1" style="width:20px;height:20px;accent-color:var(--primary);" {{ old('wants_tent', old('wants_counter') ? null : true) ? 'checked' : '' }}>
                            <span>จองเต็นท์</span>
                        </label>
                        <div class="equipment-inputs">
                            <select name="tent_size" class="cute-select">
                                <option value="">เลือกขนาดเต็นท์</option>
                                @foreach($tentSizes as $size)
                                    <option value="{{ $size }}" {{ old('tent_size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                            <select name="tent_color" class="cute-select">
                                <option value="">เลือกสีเต็นท์</option>
                                @foreach($equipmentColors as $color)
                                    <option value="{{ $color }}" {{ old('tent_color') == $color ? 'selected' : '' }}>{{ $color }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="booking-field">
                    <div class="equipment-row">
                        <label class="equipment-head">
                            <input type="checkbox" name="wants_counter" value="1" style="width:20px;height:20px;accent-color:var(--primary);" {{ old('wants_counter') ? 'checked' : '' }}>
                            <span>จองเคาน์เตอร์</span>
                        </label>
                        <div>
                            <select name="counter_size" class="cute-select">
                                <option value="">เลือกขนาดเคาน์เตอร์</option>
                                @foreach($counterSizes as $size)
                                    <option value="{{ $size }}" {{ old('counter_size') == $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="booking-field half">
                    <label class="cute-label" for="shop_name"><i class="fa-solid fa-store" style="color:var(--primary);"></i> ชื่อร้าน *</label>
                    <input type="text" id="shop_name" name="shop_name" class="cute-input" value="{{ old('shop_name') }}" placeholder="ชื่อร้านค้า" required>
                </div>

                <div class="booking-field half">
                    <label class="cute-label" for="customer_phone"><i class="fa-solid fa-phone" style="color:var(--primary);"></i> โทร *</label>
                    <input type="tel" id="customer_phone" name="customer_phone" class="cute-input" value="{{ old('customer_phone') }}" placeholder="0812345678" required>
                </div>

                <div class="booking-field">
                    <label class="cute-label"><i class="fa-solid fa-location-dot" style="color:var(--primary);"></i> เลขล็อค *</label>
                    @php
                        $defaultMode = count($selectedGroups ?? []) > 1 ? 'multiple' : 'single';
                        $oldMode = old('lot_mode', old('lot_groups') ? 'multiple' : $defaultMode);
                        $oldGroups = old('lot_groups', $selectedGroups ?: [
                            ['prefix' => $selectedPrefix, 'from' => $selectedFrom, 'to' => $selectedTo],
                        ]);
                    @endphp

                    <input type="hidden" name="lot_mode" id="lot_mode" value="{{ $oldMode }}">

                    <div class="lot-mode-tabs" role="tablist" aria-label="เลือกประเภทเลขล็อค">
                        <button type="button" class="lot-mode-tab {{ $oldMode === 'single' ? 'is-active' : '' }}" data-lot-mode="single">
                            ล็อคเดียว / โซนเดียว
                        </button>
                        <button type="button" class="lot-mode-tab {{ $oldMode === 'multiple' ? 'is-active' : '' }}" data-lot-mode="multiple">
                            หลายล็อค / หลายโซน
                        </button>
                    </div>

                    <div id="single-lot-section" style="{{ $oldMode === 'multiple' ? 'display:none;' : '' }}">
                        <div class="lot-range-grid">
                            <select name="lot_prefix" class="cute-select" {{ $oldMode === 'multiple' ? '' : 'required' }}>
                                <option value="">เลือกอักษรล็อค</option>
                                @foreach($lotPrefixes as $prefix)
                                    <option value="{{ $prefix }}" {{ old('lot_prefix', $selectedPrefix) == $prefix ? 'selected' : '' }}>{{ $prefix }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="lot_number_from" class="cute-input" value="{{ old('lot_number_from', $selectedFrom) }}" min="1" max="999" placeholder="เลขล็อค" {{ $oldMode === 'multiple' ? '' : 'required' }}>
                            <strong style="text-align:center;color:var(--text-dark);">ถึง</strong>
                            <input type="number" name="lot_number_to" class="cute-input" value="{{ old('lot_number_to', $selectedTo) }}" min="1" max="999" placeholder="ถ้าล็อคเดียวไม่ต้องกรอก">
                        </div>
                    </div>

                    <div id="multiple-lot-section" style="{{ $oldMode === 'multiple' ? '' : 'display:none;' }}">
                        <div class="lot-group-list" id="lot-group-list">
                            @foreach($oldGroups as $index => $group)
                                <div class="lot-group-row">
                                    <select name="lot_groups[{{ $index }}][prefix]" class="cute-select">
                                        <option value="">อักษรล็อค</option>
                                        @foreach($lotPrefixes as $prefix)
                                            <option value="{{ $prefix }}" {{ ($group['prefix'] ?? null) == $prefix ? 'selected' : '' }}>{{ $prefix }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="lot_groups[{{ $index }}][from]" class="cute-input" value="{{ $group['from'] ?? '' }}" min="1" max="999" placeholder="เลขเริ่ม">
                                    <strong style="text-align:center;color:var(--text-dark);">ถึง</strong>
                                    <input type="number" name="lot_groups[{{ $index }}][to]" class="cute-input" value="{{ $group['to'] ?? '' }}" min="1" max="999" placeholder="เลขสิ้นสุด">
                                    <button type="button" class="lot-remove-btn" aria-label="ลบแถว"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="lot-add-btn" id="add-lot-group">
                            <i class="fa-solid fa-plus"></i> เพิ่มล็อค/โซน
                        </button>
                        <small style="display:block;color: var(--text-muted); font-size: 12px; margin-top:8px;">
                            ตัวอย่าง: แถวแรก GB 10 ถึง 12, แถวต่อไป GQ 40 ถึง 41
                        </small>
                    </div>

                    <template id="lot-group-template">
                        <div class="lot-group-row">
                            <select name="__PREFIX_NAME__" class="cute-select">
                            <option value="">เลือกอักษรล็อค</option>
                            @foreach($lotPrefixes as $prefix)
                                <option value="{{ $prefix }}">{{ $prefix }}</option>
                            @endforeach
                        </select>
                            <input type="number" name="__FROM_NAME__" class="cute-input" min="1" max="999" placeholder="เลขเริ่ม">
                        <strong style="text-align:center;color:var(--text-dark);">ถึง</strong>
                            <input type="number" name="__TO_NAME__" class="cute-input" min="1" max="999" placeholder="เลขสิ้นสุด">
                            <button type="button" class="lot-remove-btn" aria-label="ลบแถว"><i class="fa-solid fa-xmark"></i></button>
                        </div>
                    </template>

                    @if(!empty($selectedCodes))
                        <small style="color: var(--text-muted); font-size: 12px;">เติมจากล็อตที่เลือกบนแผนที่: {{ implode(', ', $selectedCodes) }}</small>
                    @endif
                </div>

                <div class="booking-field">
                    <label class="cute-label" for="payment_slip"><i class="fa-solid fa-image" style="color:var(--primary);"></i> รูปภาพสลิป กรณีลูกค้าชำระแล้ว</label>
                    <input type="file" id="payment_slip" name="payment_slip" class="cute-input" accept="image/*">
                    <small style="color: var(--text-muted); font-size: 12px;">รองรับ JPG, PNG, WEBP ขนาดไม่เกิน 5MB</small>
                </div>

                <div class="booking-field">
                    <label class="checkbox-line">
                        <input type="checkbox" name="collect_front_store" value="1" {{ old('collect_front_store') ? 'checked' : '' }}>
                        <span>ให้เก็บหน้าร้าน</span>
                    </label>
                </div>

                <div class="booking-field">
                    <label class="cute-label" for="customer_note"><i class="fa-solid fa-comment-dots" style="color:var(--primary);"></i> หมายเหตุเพิ่มเติม</label>
                    <textarea id="customer_note" name="customer_note" class="cute-textarea" rows="3" placeholder="ระบุตำแหน่งเต็นท์ / รายละเอียดเพิ่มเติม">{{ old('customer_note') }}</textarea>
                </div>
            </div>

            <div class="booking-form-actions" style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('public.map', ['date' => $date]) }}" class="btn-secondary" style="flex: 1;">
                    <i class="fa-solid fa-map-location-dot"></i> ดูแผนผัง
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-paper-plane"></i> ส่งคำสั่งจองอุปกรณ์
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modeInput = document.getElementById('lot_mode');
        const tabs = document.querySelectorAll('[data-lot-mode]');
        const singleSection = document.getElementById('single-lot-section');
        const multipleSection = document.getElementById('multiple-lot-section');
        const singleRequiredFields = singleSection.querySelectorAll('[name="lot_prefix"], [name="lot_number_from"]');
        const groupList = document.getElementById('lot-group-list');
        const addButton = document.getElementById('add-lot-group');
        const template = document.getElementById('lot-group-template');

        function setMode(mode) {
            modeInput.value = mode;
            singleSection.style.display = mode === 'single' ? '' : 'none';
            multipleSection.style.display = mode === 'multiple' ? '' : 'none';
            singleRequiredFields.forEach(field => field.required = mode === 'single');
            tabs.forEach(tab => tab.classList.toggle('is-active', tab.dataset.lotMode === mode));

            if (mode === 'multiple' && groupList.children.length === 0) {
                addLotGroup();
            }
        }

        function refreshRemoveButtons() {
            groupList.querySelectorAll('.lot-remove-btn').forEach(button => {
                button.disabled = groupList.children.length <= 1;
                button.style.opacity = button.disabled ? '0.45' : '1';
            });
        }

        function addLotGroup() {
            const index = Date.now().toString();
            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML
                .replace('__PREFIX_NAME__', `lot_groups[${index}][prefix]`)
                .replace('__FROM_NAME__', `lot_groups[${index}][from]`)
                .replace('__TO_NAME__', `lot_groups[${index}][to]`);
            groupList.appendChild(wrapper.firstElementChild);
            refreshRemoveButtons();
        }

        tabs.forEach(tab => tab.addEventListener('click', () => setMode(tab.dataset.lotMode)));
        addButton.addEventListener('click', addLotGroup);
        groupList.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.lot-remove-btn');
            if (!removeButton || removeButton.disabled) {
                return;
            }
            removeButton.closest('.lot-group-row').remove();
            refreshRemoveButtons();
        });

        setMode(modeInput.value || 'single');
        refreshRemoveButtons();
    });
</script>
@endsection
