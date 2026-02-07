<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function index()
    {
        // Admin yaratgan testlarini ko'radi
        return Test::with('sections')->latest()->paginate(10);
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required']);

        $test = Test::create([
            'admin_id' => $request->user()->id, // Hozirgi admin
            'title' => $request->title,
            'description' => $request->description,
            'unique_link' => Str::random(10), // Link avtomatik generatsiya
            'is_active' => true
        ]);

        return response()->json($test, 201);
    }

    public function show(Test $test)
    {
        // Testni ichidagi bo'limlari bilan qaytarish
        return $test->load('sections');
    }

    // LINK ORQALI OLISH (Student uchun - avval yozgan edik)
    public function getByLink($unique_link)
    {
        $test = Test::where('unique_link', $unique_link)
                    ->where('is_active', true)
                    ->with(['sections.questions' => function($q) {
                         // Bu yerda savollarni yashirish yoki random qilish logikasi bo'lishi mumkin
                    }])
                    ->firstOrFail();
        
        // Savollarni random qilish logikasi (TestController avvalgi versiyasidagidek)
        // ... (qisqartirildi, avvalgi kodni ishlating)

        return response()->json($test);
    }

    public function update(Request $request, Test $test)
    {
        $test->update($request->all());
        return response()->json($test);
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return response()->json(['message' => 'Test deleted successfully']);
    }
}