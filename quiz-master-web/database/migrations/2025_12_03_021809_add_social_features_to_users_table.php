<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah Kolom Bio & Title di Users
        Schema::table('users', function (Blueprint $table) {
            // Cek dulu biar tidak error kalau kolom sudah ada
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title')->nullable()->after('email'); // Contoh: "Raja Kuis"
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('title');     // Deskripsi diri
            }
        });

        // 2. Tabel Follows (Siapa ikut Siapa)
        if (!Schema::hasTable('follows')) {
            Schema::create('follows', function (Blueprint $table) {
                $table->id();
                $table->foreignId('follower_id')->constrained('users')->onDelete('cascade'); // Yang mengikuti
                $table->foreignId('following_id')->constrained('users')->onDelete('cascade'); // Yang diikuti
                $table->timestamps();
                
                // Mencegah duplikat (A follow B dua kali)
                $table->unique(['follower_id', 'following_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['title', 'bio']);
        });
    }
};