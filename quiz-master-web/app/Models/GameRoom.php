<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRoom extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'room_code', 
        'category_id', 
        'host_id', 
        'challenger_id', 
        'host_score', 
        'challenger_score', 
        'status', 
        'total_questions', 
        'duration'
    ];

    public function host() { return $this->belongsTo(User::class, 'host_id'); }
    public function challenger() { return $this->belongsTo(User::class, 'challenger_id'); }
    public function category() { return $this->belongsTo(Category::class); }
}