<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Tipe Soal di Tabel Questions
        Schema::table('questions', function (Blueprint $table) {
            // types: 'single', 'multiple', 'ordering', 'matching'
            $table->string('type')->default('single')->after('category_id'); 
        });

        // 2. Tambah Kolom Logika di Tabel Options
        Schema::table('options', function (Blueprint $table) {
            // Untuk Matching: Pasangan teksnya (misal: "Jakarta" untuk soal "Indonesia")
            $table->string('matching_pair')->nullable()->after('option_text');
            
            // Untuk Ordering: Urutan benarnya (1, 2, 3, 4)
            $table->integer('correct_order')->nullable()->after('matching_pair');
        });
    }

    public function down(): void
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn(['matching_pair', 'correct_order']);
        });
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};