<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    // Tambahkan 'explanation' dan 'reference' ke dalam fillable
    protected $fillable = [
        'category_id', 
        'type', 
        'question_text', 
        'image_path', 
        'audio_path',
        'explanation', // <-- BARU
        'reference'    // <-- BARU
    ];

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}