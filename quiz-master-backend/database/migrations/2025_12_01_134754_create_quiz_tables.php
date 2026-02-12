<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Kategori (Menggantikan quizCategories di quizData.js)
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // contoh: 'biologi-dasar'
            $table->string('name');           // contoh: 'Biologi Dasar'
            $table->text('description');      // Deskripsi kategori
            $table->string('icon_class')->nullable(); // Untuk menyimpan nama icon
            $table->timestamps();
        });

        // 2. Tabel Soal (Menggantikan file json soal)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->text('explanation')->nullable(); // Penjelasan (opsional)
            $table->timestamps();
        });

        // 3. Tabel Jawaban/Opsi (A, B, C, D)
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('option_text');
            $table->boolean('is_correct')->default(false); // 1 = Benar, 0 = Salah
            $table->timestamps();
        });
    }                                                           

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_tables');
    }
};
