<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Result;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\ResultDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubmissionController extends Controller
{
    /**
     * Testni boshlash - started_at vaqtini belgilab bo'sh natija yaratadi.
     */
    public function start(Request $request)
    {
        $request->validate(['test_id' => 'required|exists:tests,id']);
        $user = $request->user();

        // 1. Bu talaba ushbu testni allaqachon topshirib bo'lganmi?
        $alreadyDone = Result::where('test_id', $request->test_id)
                            ->where('student_id', $user->id)
                            ->where('status', 'completed')
                            ->exists();

        if ($alreadyDone) {
            return response()->json([
                'message' => 'Siz bu testni topshirib bo\'lgansiz. Qayta kirish taqiqlanadi.'
            ], 403);
        }

        // 2. Agar test jarayoni ketayotgan bo'lsa, o'shani davom ettiradi
        // yoki yangi "processing" holatidagi natija ochadi
        $result = Result::firstOrCreate(
            [
                'test_id' => $request->test_id,
                'student_id' => $user->id,
                'status' => 'processing'
            ],
            ['started_at' => now()]
        );

        return response()->json([
            'result_id' => $result->id,
            'started_at' => $result->started_at
        ]);
    }

    /**
     * Testni yakunlash - vaqtni tekshiradi va natijani hisoblaydi.
     */
    public function store(Request $request)
    {
        // 1. VALIDATSIYA
        $request->validate([
            'result_id' => 'required|exists:results,id',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.selected' => 'required'
        ]);

        $user = $request->user();

        // Resultni barcha kerakli bog'liqliklar bilan yuklaymiz
        $result = Result::with('test')->where('id', $request->result_id)
                        ->where('student_id', $user->id)
                        ->firstOrFail();

        // 2. TIMER TEKSHIRUVI
        $startTime = Carbon::parse($result->started_at);
        $duration = $result->test->duration; // minutda
        $endTime = $startTime->copy()->addMinutes($duration);
        $now = now();

        // Agar talaba belgilangan vaqtdan 2 minutdan ko'p o'tkazib yuborgan bo'lsa (buffer time)
        if ($now->gt($endTime->addMinutes(2))) {
            $result->update(['status' => 'failed', 'total_score' => 0]);
            return response()->json([
                'message' => 'Vaqt tugagan! Test natijasi qabul qilinmadi.',
                'started_at' => $result->started_at,
                'submitted_at' => $now
            ], 403);
        }

        return DB::transaction(function () use ($result, $request) {
            $answersInput = $request->answers;

            // --- OPTIMIZATSIYA ---
            $questionIds = collect($answersInput)->pluck('question_id');
            $questions = Question::whereIn('id', $questionIds)->get()->keyBy('id');

            $sectionScores = [];

            foreach ($answersInput as $ans) {
                $question = $questions[$ans['question_id']];
                $isCorrect = ($question->correct_answer === $ans['selected']);

                StudentAnswer::create([
                    'result_id' => $result->id,
                    'question_id' => $question->id,
                    'section_id' => $question->section_id,
                    'selected_option' => $ans['selected'],
                    'is_correct' => $isCorrect
                ]);

                if ($isCorrect) {
                    $sectionScores[$question->section_id] = ($sectionScores[$question->section_id] ?? 0) + 1;
                }
            }

            // Sectionlar bo'yicha hisobot
            $test = Test::with('sections')->find($result->test_id);
            $finalTotalPercentage = 0;
            $sectionsCount = $test->sections->count();

            foreach ($test->sections as $section) {
                $correctCount = $sectionScores[$section->id] ?? 0;
                $percentage = ($section->questions_to_ask > 0)
                    ? ($correctCount / $section->questions_to_ask) * 100
                    : 0;

                ResultDetail::create([
                    'result_id' => $result->id,
                    'section_id' => $section->id,
                    'correct_answers' => $correctCount,
                    'score_percentage' => $percentage
                ]);

                $finalTotalPercentage += $percentage;
            }

            $averageScore = $sectionsCount > 0 ? $finalTotalPercentage / $sectionsCount : 0;

            $result->update([
                'total_score' => $averageScore,
                'status' => 'completed'
            ]);

            return response()->json([
                'message' => 'Test muvaffaqiyatli topshirildi!',
                'score' => $averageScore
            ]);

            // ... (Avvalgi hisob-kitoblar tugagan joyidan davom etadi)

            // 1. Natijalarni bazadan qayta yuklaymiz (bo'lim nomlari bilan)
            $details = ResultDetail::where('result_id', $result->id)
                ->with('section')
                ->get();

            // 2. FAQAT Frontend va Backend bo'limlarini filtrlab olamiz
            $techDetails = $details->filter(function ($detail) {
                $name = strtolower($detail->section->name);
                return str_contains($name, 'frontend') || str_contains($name, 'backend');
            });

            // 3. Filtrlangandan keyin eng yuqori ballisini topamiz
            $bestTech = $techDetails->sortByDesc('score_percentage')->first();

            $strength = "Noma'lum";
            $summary = "Sizda texnik bo'limlar bo'yicha natija aniqlanmadi.";

            if ($bestTech) {
                $strength = $bestTech->section->name;

                // Tavsiya berish qoidalari
                if ($bestTech->score_percentage >= 80) {
                    $summary = "Sizda {$strength} yo'nalishi bo'yicha kuchli bilim bor. Professional loyihalarga tayyorsiz.";
                } elseif ($bestTech->score_percentage >= 50) {
                    $summary = "Sizda {$strength} bo'yicha yaxshi poydevor bor, lekin amaliyotni ko'paytirish kerak.";
                } else {
                    $summary = "Sizga {$strength} yo'nalishini noldan chuqurroq o'rganishni tavsiya qilamiz.";
                }
            }

            // 4. Bazani yangilaymiz
            $result->update([
                'total_score' => $averageScore,
                'status' => 'completed',
                'feedback_strength' => $strength,
                'feedback_summary' => $summary
            ]);

            return response()->json([
                'message' => 'Test muvaffaqiyatli topshirildi!',
                'overall_score' => $averageScore,
                'career_path' => $strength, // Faqat Frontend yoki Backend chiqadi
                'recommendation' => $summary,
                'all_details' => $details // Bu yerda IQ ham, qolganlar ham ko'rinadi
            ]);
        });
    }
}
