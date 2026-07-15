@extends('layouts.admin')

@section('title', 'แก้ไขข้อมูลผู้ใช้ #' . $user->name)
@section('page_title', 'แก้ไขข้อมูลผู้ใช้')

@section('content')
    <div class="cute-card">
        <h3 class="cute-card-title">
            <i class="fa-solid fa-user-pen"></i> ฟอร์มแก้ไขข้อมูลพนักงานและเจ้าหน้าที่ระบบ
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

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="name">ชื่อพนักงาน *</label>
                    <input type="text" id="name" name="name" class="cute-input" value="{{ old('name', $user->name) }}" required>
                </div>
                
                <div class="cute-input-group">
                    <label class="cute-label" for="username">Username สำหรับล็อกอิน *</label>
                    <input type="text" id="username" name="username" class="cute-input" value="{{ old('username', $user->username) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="role">บทบาท / ตำแหน่ง *</label>
                    <select id="role" name="role" class="cute-select" required>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>พนักงานติดตั้งเต็นท์ (Staff)</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>ผู้จัดการแอดมินระบบ (Admin)</option>
                        <option value="viewer" {{ old('role', $user->role) == 'viewer' ? 'selected' : '' }}>ผู้เข้าชมดูข้อมูลการจอง (Viewer)</option>
                    </select>
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="email">อีเมล</label>
                    <input type="email" id="email" name="email" class="cute-input" value="{{ old('email', $user->email) }}">
                </div>

                <div class="cute-input-group">
                    <label class="cute-label" for="phone">เบอร์โทรศัพท์</label>
                    <input type="text" id="phone" name="phone" class="cute-input" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <div style="background-color: var(--bg-page); border: 2px solid var(--border-cute); border-radius: 20px; padding: 20px; margin-bottom: 20px;">
                <h4 style="margin-top:0;margin-bottom:10px;color:var(--text-dark);"><i class="fa-solid fa-key"></i> เปลี่ยนรหัสผ่านความปลอดภัย (เว้นว่างไว้หากต้องการใช้รหัสเดิม)</h4>
                
                <div class="form-grid" style="margin-bottom:0;">
                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="password">รหัสผ่านใหม่ (ไม่ต่ำกว่า 6 ตัวอักษร)</label>
                        <input type="password" id="password" name="password" class="cute-input" placeholder="ป้อนเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่านใหม่">
                    </div>

                    <div class="cute-input-group" style="margin-bottom:0;">
                        <label class="cute-label" for="password_confirmation">ยืนยันรหัสผ่านใหม่อีกครั้ง</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="cute-input" placeholder="พิมพ์ยืนยันรหัสผ่านใหม่อีกครั้ง">
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="cute-input-group">
                    <label class="cute-label" for="is_active">สถานะการใช้งานบัญชี *</label>
                    <select id="is_active" name="is_active" class="cute-select" required>
                        <option value="1" {{ old('is_active', $user->is_active) == 1 ? 'selected' : '' }}>ปกติ (ล็อกอินใช้งานได้ปกติ)</option>
                        <option value="0" {{ old('is_active', $user->is_active) == 0 ? 'selected' : '' }}>ระงับการใช้งาน (บล็อกบัญชีชั่วคราว)</option>
                    </select>
                </div>
                
                @if ($user->id === auth()->id())
                    <div style="display:flex;align-items:center;padding-top:15px;color: #D83A3A;">
                        <strong><i class="fa-solid fa-triangle-exclamation"></i> บัญชีนี้คือบัญชีของตัวคุณเอง โปรดระมัดระวังในการแก้หรือปิดใช้งาน</strong>
                    </div>
                @endif
            </div>

            <div style="display: flex; gap: 12px; margin-top: 30px;">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="flex: 1;">
                    ย้อนกลับ
                </a>
                <button type="submit" class="btn-primary" style="flex: 2;">
                    <i class="fa-solid fa-floppy-disk"></i> บันทึกการแก้ไขข้อมูลผู้ใช้
                </button>
            </div>
        </form>
    </div>
@endsection
