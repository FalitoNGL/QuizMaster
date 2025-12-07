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
    public function index()
    {
        $categories = Category::withCount('questions')->get();
        if(Auth::check()) {
            session(['current_player' => Auth::user()->name]);
        }
        return view('menu', ['categories' => $categories]);
    }

    public function show($slug, Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $limit = $request->input('limit', 10); 
        $timer = $request->input('timer', 30); 

        // Ambil soal (sembunyikan field is_correct jika ingin sangat ketat, 
        // tapi untuk single page app sederhana kita biarkan agar frontend mudah menghandle visual)
        $questions = Question::with('options')
                        ->where('category_id', $category->id)
                        ->inRandomOrder()
                        ->take($limit)
                        ->get();

        return view('play', compact('category', 'questions', 'timer'));
    }

    public function leaderboard()
    {
        $topScores = Result::with('category')
                        ->orderBy('score', 'desc')
                        ->take(20)
                        ->get();
        return view('leaderboard', compact('topScores'));
    }

    public function review($id)
    {
        $result = Result::with(['category', 'answers.question.options', 'answers.option'])
                        ->findOrFail($id);
        return view('review', compact('result'));
    }

    /**
     * PROSES SIMPAN SKOR (SECURE VERSION)
     * Menghitung skor di server berdasarkan jawaban user.
     */
    public function submit(Request $request)
    {
        // 1. Validasi Input (Hanya data esensial)
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'player_name' => 'required|string|max:50',
            'answers' => 'required|array', // Array jawaban dari JS
        ]);

        // 2. Tentukan User
        $userId = null;
        if (Auth::check()) {
            $userId = Auth::id();
            $data['player_name'] = Auth::user()->name; 
        }
        session(['current_player' => $data['player_name']]);

        // 3. LOGIKA HITUNG SKOR (SERVER-SIDE)
        $calculatedScore = 0;
        $correctCount = 0;
        $totalQuestions = count($data['answers']);
        $processedAnswers = []; // Penampung untuk disimpan ke DB

        foreach ($data['answers'] as $ans) {
            $question = Question::with('options')->find($ans['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            $userAnswerVal = $ans['answer'] ?? null; 
            $timeLeft = $ans['time_left'] ?? 0; // Bonus waktu

            // A. Single Choice
            if ($question->type === 'single') {
                $opt = $question->options->where('id', $userAnswerVal)->first();
                $isCorrect = $opt && $opt->is_correct;
                // Simpan ID opsi untuk referensi
                $saveOptionId = $userAnswerVal;
            } 
            // B. Multiple Choice
            elseif ($question->type === 'multiple' && is_array($userAnswerVal)) {
                $correctIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
                sort($userAnswerVal); 
                sort($correctIds);
                $isCorrect = $userAnswerVal == $correctIds;
                $saveOptionId = 0; // 0 menandakan jawaban kompleks
            }
            // C. Ordering
            elseif ($question->type === 'ordering' && is_array($userAnswerVal)) {
                $correctSequence = $question->options->sortBy('correct_order')->pluck('id')->toArray();
                $isCorrect = array_values($userAnswerVal) === array_values($correctSequence);
                $saveOptionId = 0;
            }
            // D. Matching
            elseif ($question->type === 'matching' && is_array($userAnswerVal)) {
                $allMatch = true;
                foreach ($userAnswerVal as $item) {
                    // $item structure: {left_id: 1, pair_text: 'Jawab A'}
                    $opt = $question->options->where('id', $item['left_id'])->first();
                    if (!$opt || $opt->matching_pair !== $item['pair_text']) {
                        $allMatch = false;
                        break;
                    }
                }
                $isCorrect = $allMatch;
                $saveOptionId = 0;
            } else {
                // Tipe tidak dikenal atau format salah
                $isCorrect = false;
                $saveOptionId = null;
            }

            // Hitung Poin: 100 + Sisa Waktu (Hanya jika benar)
            if ($isCorrect) {
                $calculatedScore += 100 + $timeLeft;
                $correctCount++;
            }

            // Siapkan data untuk result_answers
            $processedAnswers[] = [
                'question_id' => $question->id,
                'option_id' => $saveOptionId,
                'is_correct' => $isCorrect
            ];
        }

        // 4. Simpan Header Hasil
        $result = Result::create([
            'user_id' => $userId,
            'player_name' => $data['player_name'],
            'category_id' => $data['category_id'],
            'score' => $calculatedScore, // Skor hasil hitungan server
            'correct_answers' => $correctCount,
            'total_questions' => $totalQuestions,
        ]);

        // 5. Simpan Detail Jawaban
        foreach ($processedAnswers as $pAns) {
            $optId = $pAns['option_id'];
            
            // Fix foreign key constraint jika 0
            if ($optId === 0 || $optId === null) {
                $firstOpt = Option::where('question_id', $pAns['question_id'])->first();
                $optId = $firstOpt ? $firstOpt->id : null;
            }

            if ($optId) {
                ResultAnswer::create([
                    'result_id' => $result->id,
                    'question_id' => $pAns['question_id'],
                    'option_id' => $optId,
                    'is_correct' => $pAns['is_correct'],
                ]);
            }
        }

        // 6. Cek Achievements
        // Kita bungkus data agar sesuai parameter checkAchievements
        $achievData = [
            'score' => $calculatedScore,
            'correct' => $correctCount,
            'total' => $totalQuestions
        ];
        $newBadges = $this->checkAchievements($data['player_name'], $userId, $achievData);

        return response()->json([
            'message' => 'Skor berhasil disimpan!',
            'result_id' => $result->id,
            'new_badges' => $newBadges,
            'real_score' => $calculatedScore, // Kembalikan skor asli ke frontend
            'correct_count' => $correctCount
        ]);
    }

    private function checkAchievements($player, $userId, $data)
    {
        $newBadges = [];
        
        $query = Result::query();
        if ($userId) $query->where('user_id', $userId);
        else $query->where('player_name', $player);
        
        $totalGames = $query->count();

        if ($totalGames == 1) $this->unlockBadge($player, $userId, 'newbie', $newBadges);
        if ($totalGames > 5) $this->unlockBadge($player, $userId, 'veteran', $newBadges);
        if ($data['correct'] == $data['total'] && $data['total'] > 0) $this->unlockBadge($player, $userId, 'sharpshooter', $newBadges);
        if ($data['score'] > 1000) $this->unlockBadge($player, $userId, 'speedster', $newBadges);

        return $newBadges;
    }

    private function unlockBadge($player, $userId, $slug, &$newBadges)
    {
        $badge = Achievement::where('slug', $slug)->first();
        if (!$badge) return;

        $query = DB::table('player_achievements')->where('achievement_id', $badge->id);
        if ($userId) $query->where('user_id', $userId);
        else $query->where('player_name', $player);

        if (!$query->exists()) {
            DB::table('player_achievements')->insert([
                'user_id' => $userId,
                'player_name' => $player,
                'achievement_id' => $badge->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $newBadges[] = $badge->name;
        }
    }
}