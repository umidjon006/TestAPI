<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id', 
        'title', 
        'description', 
        'unique_link', 
        'is_active'
    ];

    // Testning bo'limlari
    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}