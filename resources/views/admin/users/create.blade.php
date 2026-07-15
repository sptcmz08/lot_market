@extends('layouts.admin')

@section('title', 'เพิ่มพนักงานหรือผู้ใช้ระบบใหม่')
@section('page_title', 'เพิ่มพนักงานใหม่')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-user-plus"></i> ฟอร์มลงทะเบียนพนักงานและเจ้าหน้าที่ระบบ
        </h3>

        @if ($errors->any())
            <div class="alert-cute alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div style="flex: 1;">
                    <strong>มีข้อผิดพลาด:</strong>
                    <ul style="margin:5px 0 0 0;padding-left:20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="name">ชื่อพนักงาน *</label>
                    <input type="text" id="name" name="name" class="cute-input" value="{{ old('name') }}" placeholder="ตัวอย่าง: สมชาย ขยันดี" required>
                </div>
                
                <div class="cute-input-group">
                    <label class="cute-label" for="username">Username สำหรับล็อกอิน *</label>
                    <input type="text" id="username" name="username" class="cute-input" value="{{ old('username') }}" placeholder="เช่น staff3" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="role">บทบาท / ตำแหน่ง *</label>
                    <select id="role" name="role" class="cute-select" required>
                        <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>พนักงานติดตั้งเต็นท์ (Staff)</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>ผู้จัดการแอดมินระบบ (Admin)</option>
                        <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>ผู้เข้าชมดูข้อมูลการจอง (Viewer)</option>
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="email">อีเมล (ถ้ามี)</label>
                    <input type="email" id="email" name="email" class="cute-input" value="{{ old('email') }}" placeholder="ตัวอย่าง: staff@example.com">
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="phone">เบอร์โทรศัพท์ (ถ้ามี)</label>
                    <input type="text" id="phone" name="phone" class="cute-input" value="{{ old('phone') }}" placeholder="ตัวอย่าง: 0899999999">
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="password">รหัสผ่านสำหรับเข้าสู่ระบบ *</label>
                    <input type="password" id="password" name="password" class="cute-input" placeholder="กำหนดอย่างน้อย 6 ตัวอักษร" required>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="password_confirmation">ยืนยันรหัสผ่านอีกครั้ง *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="cute-input" placeholder="พิมพ์ยืนยันรหัสผ่าน" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="is_active">สถานะการใช้งานบัญชี *</label>
                    <select id="is_active" name="is_active" class="cute-select" required>
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>ปกติ (ล็อกอินใช้งานได้ปกติ)</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>ระงับการใช้งาน (บล็อกบัญชีชั่วคราว)</option>
                    </select>
                </div>
                
                <div style="display:flex;align-items:center;padding-top:15px;">
                    <small style="color: var(--text-muted);">* จำเป็นต้องกรอกข้อมูลให้ครบถ้วนก่อนทำการกดบันทึก</small>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="flex: 1;">
                    ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกข้อมูลและลงทะเบียน
                </button>
            </div>
        </form>
    </div>
@endsection
