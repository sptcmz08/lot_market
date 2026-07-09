@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้และพนักงาน')
@section('page_title', 'พนักงานและเจ้าหน้าที่ระบบ')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3 style="margin:0; font-weight:700; color: var(--text-dark);">รายชื่อผู้ใช้ระบบทั้งหมด</h3>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <i class="fa-solid fa-user-plus"></i> เพิ่มพนักงานใหม่
        </a>
    </div>

    <!-- Users list table -->
    <div class="cute-table-container">
        <table class="cute-table">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้ / พนักงาน</th>
                    <th>อีเมลที่ใช้ล็อกอิน</th>
                    <th>เบอร์โทรศัพท์</th>
                    <th>ตำแหน่ง / บทบาท</th>
                    <th>สถานะบัญชี</th>
                    <th>วันที่สร้างบัญชี</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            <strong style="font-size: 15px; color: var(--text-dark);">{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email ?: '-' }}</td>
                        <td>{{ $user->phone ?: '-' }}</td>
                        <td>
                            @if ($user->role === 'admin')
                                <span class="status-badge" style="background-color: #FAF5F7; color: var(--primary-hover); font-weight: 800; border: 1px solid var(--border-cute);">
                                    <i class="fa-solid fa-shield-halved"></i> ผู้ดูแลระบบ (Admin)
                                </span>
                            @elseif ($user->role === 'staff')
                                <span class="status-badge" style="background-color: #E2F9E9; color: #1E7E34; font-weight: 800;">
                                    <i class="fa-solid fa-helmet-safety"></i> พนักงาน (Staff)
                                </span>
                            @elseif ($user->role === 'viewer')
                                <span class="status-badge" style="background-color: #E8F4FD; color: #004085; font-weight: 800;">
                                    <i class="fa-solid fa-eye"></i> ผู้เข้าชม (Viewer)
                                </span>
                            @endif
                        </td>
                        <td>
                            @if ($user->is_active)
                                <span class="status-badge status-available" style="padding: 4px 10px; font-size:12px;">
                                    <i class="fa-solid fa-circle-check"></i> ปกติ
                                </span>
                            @else
                                <span class="status-badge status-blocked" style="padding: 4px 10px; font-size:12px;">
                                    <i class="fa-solid fa-circle-minus"></i> ระงับการใช้งาน
                                </span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px;" title="แก้ไขผู้ใช้">
                                    <i class="fa-solid fa-user-pen"></i> แก้ไข
                                </a>
                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="margin: 0;" onsubmit="return confirm('คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-secondary" style="padding: 6px 12px; font-size: 13px; border-radius: 10px; border-color: #FFA3A3; color: #D83A3A;" title="ลบผู้ใช้">
                                            <i class="fa-solid fa-user-minus"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-users-slash" style="font-size: 40px; margin-bottom: 10px; display: block; color: var(--border-cute);"></i>
                            ไม่มีประวัติข้อมูลผู้ใช้ระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-cute">
        {{ $users->links() }}
    </div>
@endsection
