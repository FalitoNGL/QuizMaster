<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // HUBUNGAN BARU: Result punya banyak jawaban detail
    public function answers()
    {
        return $this->hasMany(ResultAnswer::class);
    }
}