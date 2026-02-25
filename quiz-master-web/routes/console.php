<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\GameRoom;

// Command bawaan Laravel (biarkan saja)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

/**
 * --------------------------------------------------------------------------
 * CLEANUP SCHEDULE (ZOMBIE ROOMS)
 * --------------------------------------------------------------------------
 * Tugas ini akan berjalan otomatis setiap jam untuk membersihkan
 * room yang ditinggalkan atau 'nyangkut'.
 */
Schedule::call(function () {
    
    // 1. Hapus Room 'Waiting' yang Basi (> 24 Jam)
    // Kasus: Host buat room, lalu ditinggal tidur/lupa, tidak pernah di-start.
    $deletedWaiting = GameRoom::where('status', 'waiting')
            ->where('created_at', '<', now()->subDay())
            ->delete();

    // 2. Paksa Selesai Room 'Playing' yang Nyangkut (> 2 Jam)
    // Kasus: Server restart saat main, atau kedua pemain close tab tanpa menyelesaikannya.
    // Kita ubah statusnya jadi 'finished' agar tidak dianggap sedang main selamanya.
    $updatedPlaying = GameRoom::where('status', 'playing')
            ->where('updated_at', '<', now()->subHours(2))
            ->update(['status' => 'finished']);

    // Log aktivitas ke storage/logs/laravel.log (Opsional, untuk monitoring)
    if ($deletedWaiting > 0 || $updatedPlaying > 0) {
        Log::info("Scheduler Cleanup: Menghapus $deletedWaiting room 'waiting' tua & Menyelesaikan $updatedPlaying room 'zombie'.");
    }

})->hourly(); // Jalankan pengecekan setiap jam