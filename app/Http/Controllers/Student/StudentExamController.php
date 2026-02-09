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
    // 1. Registratsiya
    public function showRegister($unique_link)
    {
        $test = Test::where('unique_link', $unique_link)->firstOrFail();
        return view('Student.registerStudent', compact('test'));
    }

    // 2. Start
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

    // 3. Test Jarayoni (O'zgarishsiz qoldi, bu qism to'g'ri edi)
    public function showTest($unique_link)
    {
        $test = Test::with(['sections.questions'])
                    ->where('unique_link', $unique_link)
                    ->firstOrFail();

        foreach ($test->sections as $section) {
            // Har bir bo'limdan 10 tadan savol olamiz
            $section->setRelation('questions', $section->questions->shuffle()->take(10));
        }

        return view('Student.StudentTest', compact('test'));
    }

    // 4. Testni yakunlash (ASOSIY O'ZGARISH SHU YERDA)
    public function submitTest(Request $request, $unique_link)
    {
        $test = Test::with('sections.questions')->where('unique_link', $unique_link)->firstOrFail();
        $answers = $request->input('answers', []);

        // Umumiy statistika uchun o'zgaruvchilar
        $totalCorrectGlobal = 0;
        $totalQuestionsGlobal = 0;

        // 1. Resultni yaratib olamiz
        $result = Result::create([
            'test_id' => $test->id,
            'student_name' => Session::get('student_name'),
            'phone' => Session::get('student_phone'),
            'correct_answers' => 0,
            'total_questions' => 0,
        ]);

        // 2. Har bir bo'limni aylanib chiqamiz
        foreach ($test->sections as $section) {
            $sectionCorrect = 0;

            // Faqat shu bo'limga tegishli savollarni tekshiramiz
            foreach ($section->questions as $question) {
                // Agar o'quvchi javob belgilagan bo'lsa VA u to'g'ri bo'lsa
                // DIQQAT: Bazada to'g'ri javob ustuni nomi 'correct_answer' yoki 'correct_option' ekanligini tekshiring.
                // Sizning kodingizda 'correct_answer' deb yozilgan edi, shuni qoldirdim.
                if (isset($answers[$question->id]) && $answers[$question->id] == $question->correct_answer) {
                    $sectionCorrect++;
                }
            }

            // --- MANTIQ SHU YERDA ---
            // 1 ta to'g'ri javob = 10 ball.
            // Masalan: 7 ta topsa -> 7 * 10 = 70 ball.
            $sectionScore = $sectionCorrect * 10;

            // Umumiy hisobga qo'shamiz
            $totalCorrectGlobal += $sectionCorrect;
            $totalQuestionsGlobal += 10; // Biz har doim 10 ta savol ko'rsatganmiz

            // ResultDetail (Batafsil natija) ga yozamiz
            ResultDetail::create([
                'result_id' => $result->id,
                'section_id' => $section->id,
                'correct_answers' => $sectionCorrect,
                'score_percentage' => $sectionScore, // <-- MANA SHU YERDA BALL YOZILADI
            ]);
        }

        // 3. Umumiy natijani yangilaymiz
        $result->update([
            'correct_answers' => $totalCorrectGlobal,
            'total_questions' => $totalQuestionsGlobal,
        ]);

        Session::forget(['student_name', 'student_phone']);
        return redirect()->route('student.result', $result->id);
    }

    // 5. Natija
    public function showResult($result_id)
    {
        $result = Result::with(['test', 'details.section'])->findOrFail($result_id);
        return view('Student.result', compact('result'));
    }
}
