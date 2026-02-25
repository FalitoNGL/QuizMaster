<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\Category;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        // 1. Cek apakah ada nama pemain di session?
        // Jika tidak ada, ambil dari parameter URL ?player=Nama (opsional)
        $playerName = session('current_player', $request->query('player'));

        if (!$playerName) {
            return view('stats_login'); // Tampilkan halaman minta nama jika belum ada
        }

        // 2. Ambil Semua Data Hasil Permainan Pemain Ini
        $results = Result::with('category')
                    ->where('player_name', $playerName)
                    ->orderBy('created_at', 'desc')
                    ->get();

        if ($results->isEmpty()) {
            return view('stats_empty', compact('playerName'));
        }

        // 3. Hitung Statistik
        $totalScore = $results->sum('score');
        $totalGames = $results->count();
        $totalCorrect = $results->sum('correct_answers');
        $totalQuestions = $results->sum('total_questions');
        
        // Menghitung Level (Misal: 1000 poin = 1 Level)
        $level = floor($totalScore / 1000) + 1;
        $currentLevelScore = $totalScore % 1000;
        $progressToNextLevel = ($currentLevelScore / 1000) * 100;

        // Akurasi Rata-rata
        $accuracy = $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100) : 0;

        // Statistik per Kategori (Kekuatan & Kelemahan)
        $categoryStats = $results->groupBy('category.name')->map(function ($row) {
            return [
                'played' => $row->count(),
                'total_score' => $row->sum('score'),
                'accuracy' => $row->sum('total_questions') > 0 
                    ? round(($row->sum('correct_answers') / $row->sum('total_questions')) * 100) 
                    : 0
            ];
        });

        // Kategori Terbaik (Berdasarkan Total Skor Tertinggi)
        $bestCategory = $categoryStats->sortByDesc('total_score')->keys()->first();
        
        return view('stats', compact(
            'playerName', 'level', 'totalScore', 'totalGames', 
            'accuracy', 'results', 'categoryStats', 'bestCategory', 
            'progressToNextLevel'
        ));
    }

    // Fungsi untuk Login Nama Manual
    public function login(Request $request)
    {
        $request->validate(['name' => 'required|string']);
        session(['current_player' => $request->name]);
        return redirect()->route('stats');
    }
}