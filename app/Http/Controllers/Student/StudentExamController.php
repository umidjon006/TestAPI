<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Result;
use App\Models\ResultDetail;
use Illuminate\Support\Facades\Session;

class StudentExamController extends Controller
{
    // 1. Registratsiya (View nomi: Student.registerStudent)
    public function showRegister($unique_link)
    {
        $test = Test::where('unique_link', $unique_link)->firstOrFail();
        return view('Student.registerStudent', compact('test'));
    }

    // 2. Start (Sessiyaga yozish)
    public function startExam(Request $request, $unique_link)
    {
        $request->validate([
            'full_name' => 'required|string',
            'phone' => 'required|string',
        ]);
        Session::put('student_name', $request->full_name);
        Session::put('student_phone', $request->phone);

        return redirect()->route('student.test', $unique_link);
    }

    // 3. Test Jarayoni (View nomi: Student.StudentTest)
    public function showTest($unique_link)
    {
        // 1. Testni, bo'limlari va savollari bilan birga chaqirib olamiz
        $test = Test::with(['sections.questions'])
                    ->where('unique_link', $unique_link)
                    ->firstOrFail();

        // 2. MANTIQ: Har bir bo'lim ichiga kirib, savollarni random qilib, 10 tasini ajratib olamiz
        foreach ($test->sections as $section) {
            // shuffle() -> savollarni aralashtiradi
            // take(10) -> faqat 10 tasini oladi (agar 10 dan kam bo'lsa, borini oladi)
            $section->setRelation('questions', $section->questions->shuffle()->take(10));
        }

        // 3. O'quvchiga tayyor testni ko'rsatamiz
        return view('Student.StudentTest', compact('test'));
    }

    // 4. Testni yakunlash
    public function submitTest(Request $request, $unique_link)
    {
        $test = Test::with('sections.questions')->where('unique_link', $unique_link)->firstOrFail();
        $answers = $request->input('answers', []);

        $totalCorrect = 0;
        $totalQuestions = 0;

        $result = Result::create([
            'test_id' => $test->id,
            'student_name' => Session::get('student_name'),
            'phone' => Session::get('student_phone'),
            'correct_answers' => 0,
            'total_questions' => 0,
        ]);

        foreach ($test->sections as $section) {
            $sectionCorrect = 0;
            $sectionTotal = $section->questions->count();

            foreach ($section->questions as $question) {
                if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                    $sectionCorrect++;
                    $totalCorrect++;
                }
            }
            $totalQuestions += $sectionTotal;

            ResultDetail::create([
                'result_id' => $result->id,
                'section_id' => $section->id,
                'correct_answers' => $sectionCorrect,
                'score_percentage' => ($sectionTotal > 0) ? round(($sectionCorrect / $sectionTotal) * 100) : 0,
            ]);
        }

        $result->update([
            'correct_answers' => $totalCorrect,
            'total_questions' => $totalQuestions,
        ]);

        Session::forget(['student_name', 'student_phone']);
        return redirect()->route('student.result', $result->id);
    }

    // 5. Natija (View nomi: Student.result)
    public function showResult($result_id)
    {
        $result = Result::with(['test', 'details.section'])->findOrFail($result_id);
        return view('Student.result', compact('result')); // <-- O'ZGARTIRILDI
    }
}
