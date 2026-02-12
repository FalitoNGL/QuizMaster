<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_code')->unique(); // Kode unik (misal: A1B2)
            $table->foreignId('category_id')->constrained();
            
            // Status Pemain
            $table->foreignId('host_id')->constrained('users'); // Pemain 1
            $table->integer('host_score')->default(0);
            
            $table->foreignId('challenger_id')->nullable()->constrained('users'); // Pemain 2
            $table->integer('challenger_score')->default(0);
            
            // Status Game
            $table->string('status')->default('waiting'); // waiting, playing, finished
            $table->integer('current_question_index')->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_rooms');
    }
};