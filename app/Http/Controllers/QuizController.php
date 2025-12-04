<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\Result;
use App\Models\ResultAnswer;
use App\Models\Achievement;
use App\Models\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * HALAMAN MENU UTAMA
     * Mengirim data kategori beserta jumlah soalnya untuk keperluan modal konfigurasi.
     */
    public function index()
    {
        // Ambil kategori dengan jumlah soal (untuk batas max slider di frontend)
        $categories = Category::withCount('questions')->get();
        
        // Sinkronisasi nama session dengan Auth jika user login
        if(Auth::check()) {
            session(['current_player' => Auth::user()->name]);
        }
        
        return view('menu', ['categories' => $categories]);
    }

    /**
     * HALAMAN MAIN KUIS (SOLO)
     * Menangani konfigurasi custom (jumlah soal & waktu) dari modal menu.
     */
    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // KONFIGURASI DARI MODAL (URL Parameters)
        // Ambil parameter 'limit' (jumlah soal), default 10
        $limit = $request->input('limit', 10); 
        
        // Ambil parameter 'timer' (waktu per soal), default 30 detik. 
        // Jika 0 berarti mode santai (tanpa waktu).
        $timer = $request->input('timer', 30); 

        // Ambil Soal secara acak sejumlah limit yang diminta
        $questions = Question::with('options')
                        ->where('category_id', $category->id)
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();

        // Kirim data ke view 'play'
        return view('play', compact('category', 'questions', 'timer'));
    }

    /**
     * HALAMAN LEADERBOARD
     */
    public function leaderboard()
    {
        $topScores = Result::with('category')
                        ->orderBy('score', 'desc')
                        ->take(20)
                        ->get();

        return view('leaderboard', compact('topScores'));
    }

    /**
     * HALAMAN PEMBAHASAN (REVIEW)
     */
    public function review($id)
    {
        // Ambil data hasil kuis beserta detail jawaban user dan opsi aslinya
        $result = Result::with(['category', 'answers.question.options', 'answers.option'])
                        ->findOrFail($id);

        return view('review', compact('result'));
    }

    /**
     * PROSES SIMPAN SKOR (SUBMIT)
     * Menangani validasi, penyimpanan skor, detail jawaban, dan pengecekan achievement.
     */
    public function submit(Request $request)
    {
        // 1. Validasi Input
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'player_name' => 'required|string|max:50',
            'score' => 'required|integer',
            'correct' => 'required|integer',
            'total' => 'required|integer',
            'answers' => 'required|array', // Array jawaban detail dari JS
        ]);

        // 2. Tentukan User (Login vs Tamu)
        $userId = null;
        if (Auth::check()) {
            $userId = Auth::id();
            // Jika login, paksa pakai nama akun asli agar konsisten
            $data['player_name'] = Auth::user()->name; 
        }
        
        // Simpan nama di session untuk keperluan lain (misal: sertifikat nanti)
        session(['current_player' => $data['player_name']]);

        // 3. Simpan Header Hasil (Skor Total) ke tabel 'results'
        $result = Result::create([
            'user_id' => $userId,
            'player_name' => $data['player_name'],
            'category_id' => $data['category_id'],
            'score' => $data['score'],
            'correct_answers' => $data['correct'],
            'total_questions' => $data['total'],
        ]);

        // 4. Simpan Detail Jawaban ke tabel 'result_answers'
        foreach ($data['answers'] as $ans) {
            // HANDLING KHUSUS: Soal Kompleks (Ordering/Multiple/Matching)
            // Jika option_id dikirim 0 oleh frontend (karena jawaban kompleks tidak merujuk 1 opsi spesifik),
            // kita cari opsi pertama dari soal tersebut sebagai placeholder agar tidak error Foreign Key.
            if ($ans['option_id'] == 0) {
                $firstOpt = Option::where('question_id', $ans['question_id'])->first();
                $ans['option_id'] = $firstOpt ? $firstOpt->id : null;
            }

            // Simpan hanya jika valid (option_id ditemukan)
            if ($ans['option_id']) {
                ResultAnswer::create([
                    'result_id' => $result->id,
                    'question_id' => $ans['question_id'],
                    'option_id' => $ans['option_id'],
                    'is_correct' => $ans['is_correct'],
                ]);
            }
        }

        // 5. Cek & Unlock Achievements (Piala)
        $newBadges = $this->checkAchievements($data['player_name'], $userId, $data);

        // Kembalikan response JSON agar frontend bisa redirect ke halaman review/menu
        return response()->json([
            'message' => 'Skor berhasil disimpan!',
            'result_id' => $result->id,
            'new_badges' => $newBadges
        ]);
    }

    /**
     * LOGIKA ACHIEVEMENTS INTERNAL
     */
    private function checkAchievements($player, $userId, $data)
    {
        $newBadges = [];
        
        // Hitung total main (berdasarkan User ID jika login, atau Nama jika tamu)
        $query = Result::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('player_name', $player);
        }
        $totalGames = $query->count();

        // Cek Badge: Newbie (Main 1x)
        if ($totalGames == 1) {
            $this->unlockBadge($player, $userId, 'newbie', $newBadges);
        }

        // Cek Badge: Veteran (Main > 5x)
        if ($totalGames > 5) {
            $this->unlockBadge($player, $userId, 'veteran', $newBadges);
        }

        // Cek Badge: Sharpshooter (Benar Semua)
        if ($data['correct'] == $data['total'] && $data['total'] > 0) {
            $this->unlockBadge($player, $userId, 'sharpshooter', $newBadges);
        }

        // Cek Badge: Speedster (Skor > 1000)
        if ($data['score'] > 1000) {
            $this->unlockBadge($player, $userId, 'speedster', $newBadges);
        }

        return $newBadges;
    }

    /**
     * FUNGSI HELPER UNTUK MEMBERIKAN BADGE KE DB
     */
    private function unlockBadge($player, $userId, $slug, &$newBadges)
    {
        // Cari badge berdasarkan slug
        $badge = Achievement::where('slug', $slug)->first();
        if (!$badge) return;

        // Cek apakah user SUDAH punya badge ini sebelumnya?
        $query = DB::table('player_achievements')->where('achievement_id', $badge->id);
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('player_name', $player);
        }

        // Jika belum punya, berikan!
        if (!$query->exists()) {
            DB::table('player_achievements')->insert([
                'user_id' => $userId,
                'player_name' => $player,
                'achievement_id' => $badge->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Masukkan nama badge ke array notifikasi
            $newBadges[] = $badge->name;
        }
    }
}