<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            // Kita simpan nama pemain manual dulu (karena belum ada Login sistem)
            $table->string('player_name')->default('Tamu'); 
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->integer('correct_answers');
            $table->integer('total_questions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};