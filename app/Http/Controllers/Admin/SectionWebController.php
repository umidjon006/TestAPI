<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Section;

class SectionWebController extends Controller
{
    // 1. Bo'limlar ro'yxati
    public function index($test_id)
    {
        $test = Test::with('sections')->findOrFail($test_id);
        return view('Admin.section', compact('test'));
    }

    // 2. Yangi bo'lim yaratish
    public function store(Request $request, $test_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'questions_to_ask' => 'required|integer|min:1',
        ]);

        Section::create([
            'test_id' => $test_id,
            'name' => $request->name,
            'total_questions' => 0, // Hozircha 0, savol qo'shilganda ko'payadi
            'questions_to_ask' => $request->questions_to_ask,
        ]);

        return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli qo\'shildi!');
    }

    // 3. O'chirish
    public function destroy($id)
    {
        Section::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Bo\'lim o\'chirildi!');
    }

    // 4. Tahrirlash
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'questions_to_ask' => 'required|integer|min:1',
        ]);

        Section::findOrFail($id)->update([
            'name' => $request->name,
            'questions_to_ask' => $request->questions_to_ask,
        ]);

        return redirect()->back()->with('success', 'Bo\'lim yangilandi!');
    }
}
