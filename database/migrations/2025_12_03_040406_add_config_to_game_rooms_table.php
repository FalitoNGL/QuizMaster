<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_rooms', function (Blueprint $table) {
            $table->integer('total_questions')->default(10)->after('category_id'); // Jumlah soal
            $table->integer('duration')->default(30)->after('total_questions');    // Detik per soal
        });
    }

    public function down(): void
    {
        Schema::table('game_rooms', function (Blueprint $table) {
            $table->dropColumn(['total_questions', 'duration']);
        });
    }
};