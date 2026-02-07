<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Question;

class QuestionWebController extends Controller
{
    // 1. Bo'limga tegishli savollarni ko'rsatish
    public function index($section_id)
    {
        // Bo'limni va uning savollarini olib kelamiz
        $section = Section::with('questions')->findOrFail($section_id);

        // 'Admin.questions' fayliga yuboramiz
        return view('Admin.questions', compact('section'));
    }

    // 2. Yangi savol qo'shish
 public function store(Request $request, $section_id)
    {
        // 1. Validatsiya (HTML dagi name="" lar bilan bir xil bo'lishi kerak)
        $request->validate([
            'question_text' => 'required|string',
            'option_a' => 'required|string', // <-- option_a
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required',
        ]);

        // 2. Bazaga yozish
        Question::create([
            'section_id' => $section_id,
            'question_text' => $request->question_text,
            'option_a' => $request->option_a, // <-- option_a
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        // Bo'limdagi savollar sonini oshirish
        $section = Section::find($section_id);
        $section->increment('total_questions');

        return redirect()->back()->with('success', 'Savol muvaffaqiyatli qo\'shildi!');
    }

    public function update(Request $request, $id)
    {
        // 1. Validatsiya (Tekshirish)
        $request->validate([
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:a,b,c,d',
        ]);

        // 2. Savolni topish
        $question = Question::findOrFail($id);

        // 3. Yangilash
        $question->update([
            'question_text' => $request->question_text,
            'option_a' => $request->option_a,
            'option_b' => $request->option_b,
            'option_c' => $request->option_c,
            'option_d' => $request->option_d,
            'correct_answer' => $request->correct_answer,
        ]);

        // 4. Ortga qaytarish
        return back()->with('success', 'Savol muvaffaqiyatli yangilandi!');
    }

    // 3. Savolni o'chirish
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $section_id = $question->section_id; // Qaysi bo'limdaligini eslab qolamiz

        $question->delete();

        // Savollar sonini kamaytirish
        Section::find($section_id)->decrement('total_questions');

        return redirect()->back()->with('success', 'Savol o\'chirildi!');
    }
}
