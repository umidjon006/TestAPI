<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;

class ResultWebController extends Controller
{
   public function index()
    {

        $results = Result::with(['test', 'details.section'])
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('Admin.natijalar', compact('results'));
    }

    public function destroy($id)
    {
        $result = Result::findOrFail($id);
        $result->delete(); // Bog'langan details avtomatik o'chadi (cascade bo'lsa)

        return redirect()->route('admin.results.index')->with('success', 'Natija o\'chirildi!');
    }
}
