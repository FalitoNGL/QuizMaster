<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultAnswer extends Model
{
    use HasFactory;

    // Izinkan mass assignment
    protected $guarded = [];

    // Relasi ke Soal
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    // Relasi ke Opsi Jawaban
    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}