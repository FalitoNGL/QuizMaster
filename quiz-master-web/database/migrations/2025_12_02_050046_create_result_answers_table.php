<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_answers', function (Blueprint $table) {
            $table->id();
            // Terhubung ke tabel skor utama (results)
            $table->foreignId('result_id')->constrained()->onDelete('cascade');
            
            // Soal yang dijawab
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            
            // Jawaban yang dipilih user
            $table->foreignId('option_id')->constrained()->onDelete('cascade');
            
            // Status benar/salah
            $table->boolean('is_correct');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_answers');
    }
};