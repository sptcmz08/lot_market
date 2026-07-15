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
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $credentials['login'];
        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $attempt = [
            $loginField => $login,
            'password' => $credentials['password'],
            'is_active' => true,
        ];

        if (Auth::attempt($attempt, $request->boolean('remember'))) {
            return $this->redirectAfterLogin($request);
        }

        foreach (['phone', 'name'] as $fallbackField) {
            if (Auth::attempt([
                $fallbackField => $login,
                'password' => $credentials['password'],
                'is_active' => true,
            ], $request->boolean('remember'))) {
                return $this->redirectAfterLogin($request);
            }
        }

        return back()->withErrors([
            'login' => 'ข้อมูลประจำตัวไม่ถูกต้อง หรือผู้ใช้ถูกระงับการใช้งาน',
        ])->onlyInput('login');
    }

    private function redirectAfterLogin(Request $request)
    {
        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('staff.tasks.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
