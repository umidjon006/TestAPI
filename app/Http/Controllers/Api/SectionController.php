<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index()
    {
        return Section::all();
    }

    // Konkret testning bo'limlarini olish uchun yordamchi metod
    public function getByTestId($test_id)
    {
        return Section::where('test_id', $test_id)->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_id' => 'required|exists:tests,id',
            'name' => 'required',
            'questions_to_ask' => 'integer|min:1'
        ]);

        $section = Section::create($request->all());
        return response()->json($section, 201);
    }

    public function show(Section $section)
    {
        return $section->load('questions');
    }

    public function update(Request $request, Section $section)
    {
        $section->update($request->all());
        return response()->json($section);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return response()->json(['message' => 'Section deleted successfully']);
    }
}