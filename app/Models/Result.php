<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    // MANA SHU YERNI O'ZGARTIRING:
    protected $fillable = [
        'test_id',
        'student_name', // <-- student_id o'rniga
        'phone',        // <-- yangi qo'shildi
        'correct_answers',
        'total_questions'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    public function details()
    {
        return $this->hasMany(ResultDetail::class);
    }
}
