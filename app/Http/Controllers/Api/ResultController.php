<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    // 1. ADMIN: Barcha natijalarni ko'rish
    public function index()
    {
        // Natijalarni Student, Test va Details (foizlar) bilan birga olib kelish
        return Result::with(['student', 'test', 'details.section'])
                     ->latest()
                     ->paginate(15);
    }

    // 2. STUDENT: Faqat o'zining natijalarini ko'rish
    public function myResults()
    {
        $studentId = Auth::id();
        
        return Result::with(['test', 'details'])
                     ->where('student_id', $studentId)
                     ->latest()
                     ->get();
    }

    // 3. GENERAL: Bitta aniq natijani to'liq ko'rish
    public function show($id)
    {
        $result = Result::with(['student', 'test', 'details.section', 'answers'])
                        ->findOrFail($id);

        // Agar user student bo'lsa, faqat o'zini natijasini ko'ra olsin
        $user = Auth::user();
        if ($user->role === 'student' && $result->student_id !== $user->id) {
            return response()->json(['message' => 'Ruxsat yo\'q'], 403);
        }

        return response()->json($result);
    }
}