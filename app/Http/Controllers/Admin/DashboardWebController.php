<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str; // <-- BU MUHIM: Link yaratish uchun kutubxona

class DashboardWebController extends Controller
{
    // 1. Dashboard (Imtihonlar ro'yxati)
    public function index()
    {
        $tests = Test::orderBy('created_at', 'desc')->get();
        return view('Admin.index', compact('tests'));
    }

    // 2. Yangi Imtihon saqlash
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Test::create([
            'title' => $request->title,
            'description' => $request->description,
            'admin_id' => Auth::id(), // Admin ID

            // --- QO'SHILGAN QISMLAR ---
            'unique_link' => Str::uuid(), // Har bir testga takrorlanmas kod beradi
            'is_active' => true, // Testni avtomatik "faol" qilamiz (agar bazada shunday ustun bo'lsa)
        ]);

        return redirect()->back()->with('success', 'Imtihon muvaffaqiyatli yaratildi!');
    }

    // 3. O'chirish
    public function destroy($id)
    {
        Test::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Imtihon o\'chirildi!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $test = Test::findOrFail($id);
        $test->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Imtihon muvaffaqiyatli yangilandi!');
    }
}
