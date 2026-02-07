<?php

namespace App\Http\Controllers\Admin; // Namespace to'g'irlandi

use App\Http\Controllers\Controller; // Asosiy Controllerni chaqirish kerak
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWebController extends Controller
{
    // 1. Login sahifasini ko'rsatish
    public function showLoginForm()
    {
        return view('Admin.login');
    }

    // 2. Login qilish jarayoni
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Login yoki parol noto‘g‘ri!',
        ])->withInput(); // Emailni qayta yozib o'tirmasligi uchun
    }

    // 3. Dashboard
    public function dashboard()
    {
        return view('Admin.index');
    }

    // 4. Logout
    public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
}
}
