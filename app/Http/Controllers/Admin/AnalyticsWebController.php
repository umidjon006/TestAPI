<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\ResultDetail;

class AnalyticsWebController extends Controller
{
    public function index()
    {
        // 1. Barcha bo'limlarni olamiz
        $sections = Section::all();

        // 2. Har bir bo'lim bo'yicha yig'ilgan umumiy ballarni hisoblaymiz
        // (ResultDetail jadvalida 'section_id' va 'score_percentage' bor deb hisoblaymiz)

        $analytics = [];
        $totalScoreSum = 0;

        foreach ($sections as $section) {
            // Shu bo'lim bo'yicha barcha o'quvchilarning foizlarini yig'indisi
            $sum = ResultDetail::where('section_id', $section->id)->sum('score_percentage');

            $analytics[] = [
                'name' => $section->name,
                'score' => $sum,
                'color' => $this->getColor($section->name), // Rangni nomiga qarab tanlaymiz
                'icon' => $this->getIcon($section->name),   // Ikonkani ham
            ];

            $totalScoreSum += $sum;
        }

        // 3. Ballar bo'yicha kamayish tartibida saralash (Eng zo'ri birinchi chiqadi)
        usort($analytics, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // 4. Foizlarni hisoblash (Progress bar uchun)
        foreach ($analytics as &$item) {
            $item['percent'] = ($totalScoreSum > 0) ? round(($item['score'] / $totalScoreSum) * 100) : 0;
        }

        return view('Admin.analitika', compact('analytics'));
    }

    // Yordamchi funksiya: Rang tanlash
    private function getColor($name)
    {
        $name = strtolower($name);
        if (str_contains($name, 'front')) return 'emerald';
        if (str_contains($name, 'back')) return 'indigo';
        if (str_contains($name, 'iq')) return 'orange';
        return 'slate'; // Default rang
    }

    // Yordamchi funksiya: Ikonka tanlash
    private function getIcon($name)
    {
        $name = strtolower($name);
        if (str_contains($name, 'front')) return 'fa-code';
        if (str_contains($name, 'back')) return 'fa-server';
        if (str_contains($name, 'iq')) return 'fa-brain';
        return 'fa-layer-group';
    }
}
