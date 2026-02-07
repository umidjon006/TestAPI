<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\ResultDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Umumiy statistika
        $totalStudents = User::where('role', 'student')->count();
        $completedTests = Result::where('status', 'completed')->count();

        // 2. Bo'limlar bo'yicha o'rtacha ball (IQ, Backend, Frontend)
        $sectionAverages = DB::table('result_details')
            ->join('sections', 'result_details.section_id', '=', 'sections.id')
            ->select('sections.name', DB::raw('ROUND(AVG(score_percentage), 2) as avg_score'))
            ->groupBy('sections.name')
            ->get();

        // 3. Qaysi yo'nalishda nechta student "LIDER" (eng yuqori ball olgan)
        // Bu so'rov har bir studentning eng kuchli tomonini aniqlaydi
        $studentPotentials = DB::table('result_details')
            ->join('sections', 'result_details.section_id', '=', 'sections.id')
            ->where('sections.name', 'NOT LIKE', '%IQ%') // IQ ni hisobga olmaymiz
            ->select('result_id', 'sections.name as direction', 'score_percentage')
            // Har bir result_id uchun eng yuqori score_percentage ni topish
            ->whereIn(DB::raw('(result_id, score_percentage)'), function ($query) {
                $query->select('result_id', DB::raw('MAX(score_percentage)'))
                    ->from('result_details')
                    ->groupBy('result_id');
            })
            ->get();

        // Yo'nalishlar bo'yicha talabalar sonini guruhlash
        $directionCounts = $studentPotentials->groupBy('direction')->map(function ($item) {
            return $item->count();
        });

        // 4. Eng ko'p ball yig'ilayotgan (eng oson yoki eng o'zlashtirilgan) yo'nalish
        $topPerformingSection = $sectionAverages->where('name', '!=', 'IQ')->sortByDesc('avg_score')->first();

        return response()->json([
            'summary' => [
                'total_students' => $totalStudents,
                'tests_completed' => $completedTests,
            ],
            'performance_by_direction' => $sectionAverages, // Har bir bo'limning o'rtacha ko'rsatkichi
            'student_distribution' => $directionCounts,    // Nechta student qaysi yo'nalishda kuchli (Lider)
            'insights' => [
                'most_popular_direction' => $directionCounts->sortDesc()->keys()->first() ?? 'N/A',
                'highest_scoring_direction' => $topPerformingSection->name ?? 'N/A',
                'average_score_all' => round($sectionAverages->avg('avg_score'), 2)
            ]
        ]);
    }
}
