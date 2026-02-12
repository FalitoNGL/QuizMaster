<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Daftar Lencana
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // ID unik (misal: 'first-win')
            $table->string('name');
            $table->string('description');
            $table->string('icon_class'); // Nama class icon FontAwesome
            $table->string('color_class'); // Warna (text-yellow-400, dll)
            $table->timestamps();
        });

        // 2. Tabel Siapa Dapat Apa
        Schema::create('player_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('player_name'); // Kita pakai nama karena belum ada sistem login user
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_achievements');
        Schema::dropIfExists('achievements');
    }
};