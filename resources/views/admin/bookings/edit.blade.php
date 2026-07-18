@extends('layouts.admin')

@section('title', 'แก้ไขข้อมูลการจอง #' . $booking->booking_code)
@section('page_title', 'แก้ไขข้อมูลการจอง')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-pen-to-square"></i> ฟอร์มแก้ไขข้อมูลการจอง
        </h3>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex:1;">
                    <strong>มีข้อผิดพลาด:</strong>
                    <ul style="margin:5px 0 0 0;padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="use_date">วันที่ใช้งานตลาด</label>
                    <input type="date" id="use_date" name="use_date" class="cute-input" value="{{ old('use_date', $booking->use_date->format('Y-m-d')) }}" required>
                </div>
                
                <div class="cute-input-group">
                    <label class="cute-label" for="booking_code_disabled">รหัสอ้างอิง (แก้ไขไม่ได้)</label>
                    <input type="text" id="booking_code_disabled" class="cute-input" value="{{ $booking->booking_code }}" disabled style="background-color: var(--bg-page);">
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="shop_name">ชื่อร้านค้า</label>
                    <input type="text" id="shop_name" name="shop_name" class="cute-input" value="{{ old('shop_name', $booking->shop_name) }}" required>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="customer_phone">เบอร์โทรศัพท์ลูกค้า</label>
                    <input type="text" id="customer_phone" name="customer_phone" class="cute-input" value="{{ old('customer_phone', $booking->customer_phone) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label">รายการเต็นท์</label>
                    <div style="background: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 14px;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:800;margin-bottom:12px;">
                            <input type="checkbox" name="wants_tent" value="1" style="width:18px;height:18px;accent-color:var(--primary);" {{ old('wants_tent', $booking->tent_size ? 1 : 0) ? 'checked' : '' }}>
                            <span>จองเต็นท์</span>
                        </label>
                        <div style="display:grid;gap:10px;" id="admin-tent-items">
                            @foreach(old('tent_items', $booking->tentEquipmentItems() ?: [['size'=>'','color'=>'','quantity'=>1]]) as $index => $item)
                                <div class="admin-equipment-item" style="display:grid;grid-template-columns:1fr 1fr 90px 42px;gap:8px;">
                                    <select name="tent_items[{{ $index }}][size]" class="cute-select"><option value="">ขนาดเต็นท์</option>@foreach ($tentSizes as $size)<option value="{{ $size }}" @selected(($item['size'] ?? '') === $size)>{{ $size }}</option>@endforeach</select>
                                    <select name="tent_items[{{ $index }}][color]" class="cute-select"><option value="">สีเต็นท์</option>@foreach ($equipmentColors as $color)<option value="{{ $color }}" @selected(($item['color'] ?? '') === $color)>{{ $color }}</option>@endforeach</select>
                                    <input type="number" name="tent_items[{{ $index }}][quantity]" class="cute-input" value="{{ $item['quantity'] ?? 1 }}" min="1" max="99" title="จำนวนหลัง">
                                    <button type="button" class="admin-equipment-remove btn-secondary" style="padding:8px;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                            <button type="button" class="btn-secondary" data-add-admin-equipment="tent"><i class="fa-solid fa-plus"></i> เพิ่มเต็นท์ต่างขนาด/สี</button>
                            <small style="display:flex;gap:6px;align-items:flex-start;color:var(--text-muted);line-height:1.5;">
                                <i class="fa-solid fa-circle-info" style="color:#f59e0b;margin-top:3px;"></i>
                                <span><strong>หมายเหตุ:</strong> หากสีที่เลือกหมด ทางร้านจะเลือกสีอื่นทดแทน</span>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label">รายการเคาน์เตอร์</label>
                    <div style="background: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 14px;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:800;margin-bottom:12px;">
                            <input type="checkbox" name="wants_counter" value="1" style="width:18px;height:18px;accent-color:var(--primary);" {{ old('wants_counter', $booking->counter_size ? 1 : 0) ? 'checked' : '' }}>
                            <span>จองเคาน์เตอร์</span>
                        </label>
                        <div style="display:grid;gap:10px;" id="admin-counter-items">
                            @foreach(old('counter_items', $booking->counterEquipmentItems() ?: [['size'=>'','quantity'=>1]]) as $index => $item)
                                <div class="admin-equipment-item" style="display:grid;grid-template-columns:1fr 90px 42px;gap:8px;">
                                    <select name="counter_items[{{ $index }}][size]" class="cute-select"><option value="">ขนาดเคาน์เตอร์</option>@foreach ($counterSizes as $size)<option value="{{ $size }}" @selected(($item['size'] ?? '') === $size)>{{ $size }}</option>@endforeach</select>
                                    <input type="number" name="counter_items[{{ $index }}][quantity]" class="cute-input" value="{{ $item['quantity'] ?? 1 }}" min="1" max="99" title="จำนวนชุด">
                                    <button type="button" class="admin-equipment-remove btn-secondary" style="padding:8px;"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                            @endforeach
                            <button type="button" class="btn-secondary" data-add-admin-equipment="counter"><i class="fa-solid fa-plus"></i> เพิ่มเคาน์เตอร์ต่างขนาด</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cute-input-group">
                <label class="cute-label">ล็อตของลูกค้า (เลือกได้หลายล็อต)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); gap: 10px; background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 16px; padding: 15px;">
                    @foreach ($allLots as $lot)
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                            <input type="checkbox" name="lots[]" value="{{ $lot->id }}" style="accent-color: var(--primary); width:16px; height:16px;"
                                {{ in_array($lot->id, old('lots', $booking->lots->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <span>{{ $lot->lot_code }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="cute-input-group">
                <label class="cute-label" for="admin_note">หมายเหตุ/คำสั่งเพิ่มเติมจากผู้จัดการ</label>
                <textarea id="admin_note" name="admin_note" class="cute-textarea" rows="3">{{ old('admin_note', $booking->admin_note) }}</textarea>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-secondary" style="flex: 1;">
                    ยกเลิก / ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกการแก้ไขข้อมูล
                </button>
            </div>
        </form>
    </div>

    <template id="admin-tent-template"><div class="admin-equipment-item" style="display:grid;grid-template-columns:1fr 1fr 90px 42px;gap:8px;"><select name="__SIZE__" class="cute-select"><option value="">ขนาดเต็นท์</option>@foreach($tentSizes as $size)<option value="{{ $size }}">{{ $size }}</option>@endforeach</select><select name="__COLOR__" class="cute-select"><option value="">สีเต็นท์</option>@foreach($equipmentColors as $color)<option value="{{ $color }}">{{ $color }}</option>@endforeach</select><input type="number" name="__QUANTITY__" class="cute-input" value="1" min="1" max="99"><button type="button" class="admin-equipment-remove btn-secondary" style="padding:8px;"><i class="fa-solid fa-xmark"></i></button></div></template>
    <template id="admin-counter-template"><div class="admin-equipment-item" style="display:grid;grid-template-columns:1fr 90px 42px;gap:8px;"><select name="__SIZE__" class="cute-select"><option value="">ขนาดเคาน์เตอร์</option>@foreach($counterSizes as $size)<option value="{{ $size }}">{{ $size }}</option>@endforeach</select><input type="number" name="__QUANTITY__" class="cute-input" value="1" min="1" max="99"><button type="button" class="admin-equipment-remove btn-secondary" style="padding:8px;"><i class="fa-solid fa-xmark"></i></button></div></template>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lists = { tent: document.getElementById('admin-tent-items'), counter: document.getElementById('admin-counter-items') };
    const refresh = list => list.querySelectorAll('.admin-equipment-remove').forEach(button => button.disabled = list.querySelectorAll('.admin-equipment-item').length <= 1);
    Object.entries(lists).forEach(([type, list]) => {
        list.addEventListener('click', event => {
            const remove = event.target.closest('.admin-equipment-remove');
            if (remove && !remove.disabled) { remove.closest('.admin-equipment-item').remove(); refresh(list); }
        });
        list.querySelector('[data-add-admin-equipment]').addEventListener('click', () => {
            const index = Date.now().toString();
            const template = document.getElementById(`admin-${type}-template`);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML.replace('__SIZE__', `${type}_items[${index}][size]`).replace('__QUANTITY__', `${type}_items[${index}][quantity]`).replace('__COLOR__', `${type}_items[${index}][color]`);
            list.querySelector('[data-add-admin-equipment]').before(wrapper.firstElementChild);
            refresh(list);
        });
        refresh(list);
    });
});
</script>
@endsection
