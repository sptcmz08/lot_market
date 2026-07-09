<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,staff,viewer',
            'is_active' => 'required|boolean',
        ], [
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'phone.unique' => 'เบอร์โทรนี้ถูกใช้งานแล้ว',
            'password.min' => 'รหัสผ่านต้องมีความยาวไม่ต่ำกว่า 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านสองช่องไม่ตรงกัน',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:admin,staff,viewer',
            'is_active' => 'required|boolean',
        ], [
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            'phone.unique' => 'เบอร์โทรนี้ถูกใช้งานแล้ว',
            'password.min' => 'รหัสผ่านต้องมีความยาวไม่ต่ำกว่า 6 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านสองช่องไม่ตรงกัน',
        ]);

        if (filled($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'ไม่สามารถลบบัญชีตัวเองได้');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    }
}
