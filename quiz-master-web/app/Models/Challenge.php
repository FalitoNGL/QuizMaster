<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function target() { return $this->belongsTo(User::class, 'target_id'); }
    public function room() { return $this->belongsTo(GameRoom::class, 'room_code', 'room_code'); }
}