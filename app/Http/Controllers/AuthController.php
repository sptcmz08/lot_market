<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('staff.tasks.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string', // Support email or username
            'password' => 'required|string',
        ]);

        // Attempt login using email or phone/name as key
        $loginField = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $attempt = [
            $loginField => $credentials['email'],
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (Auth::attempt($attempt, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('staff.tasks.index'));
        }

        // Fallback to name-based login just in case
        if ($loginField === 'phone') {
            $attemptName = [
                'name' => $credentials['email'],
                'password' => $credentials['password'],
                'is_active' => true,
            ];
            if (Auth::attempt($attemptName, $request->boolean('remember'))) {
                $request->session()->regenerate();
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return redirect()->intended(route('admin.dashboard'));
                }
                return redirect()->intended(route('staff.tasks.index'));
            }
        }

        return back()->withErrors([
            'email' => 'ข้อมูลประจำตัวไม่ถูกต้อง หรือผู้ใช้ถูกระงับการใช้งาน',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
