<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Cek dulu: Kalau kolom belum ada, baru buat
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('question_text');
            }
            
            if (!Schema::hasColumn('questions', 'reference')) {
                $table->string('reference')->nullable()->after('explanation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'explanation')) {
                $table->dropColumn('explanation');
            }
            if (Schema::hasColumn('questions', 'reference')) {
                $table->dropColumn('reference');
            }
        });
    }
};