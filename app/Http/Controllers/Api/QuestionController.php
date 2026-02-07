<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Section;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        return Question::paginate(15);
    }

    // Konkret bo'lim savollari
    public function getBySectionId($section_id)
    {
        return Question::where('section_id', $section_id)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D'
        ]);

        // Savol qo'shilganda Sectiondagi 'total_questions' sonini oshirish
        $question = Question::create($request->all());
        
        // Avtomatik hisoblash (ixtiyoriy)
        $section = Section::find($request->section_id);
        $section->increment('total_questions');

        return response()->json($question, 201);
    }

    public function show(Question $question)
    {
        return $question;
    }

    public function update(Request $request, Question $question)
    {
        $question->update($request->all());
        return response()->json($question);
    }

    public function destroy(Question $question)
    {
        $section = Section::find($question->section_id);
        $question->delete();
        
        // Savol o'chganda countni kamaytirish
        if($section) {
            $section->decrement('total_questions');
        }

        return response()->json(['message' => 'Question deleted successfully']);
    }
}