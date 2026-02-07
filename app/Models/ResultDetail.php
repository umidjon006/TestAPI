<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultDetail extends Model
{
    use HasFactory;

    protected $fillable = ['result_id', 'section_id', 'correct_answers', 'score_percentage'];
    
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}