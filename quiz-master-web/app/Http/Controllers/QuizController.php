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
        // SECURITY: Validasi input parameter
        $validated = $request->validate([
            'limit' => 'nullable|integer|min:1|max:100',
            'timer' => 'nullable|integer|min:5|max:300',
        ]);

        $category = Category::where('slug', $slug)->firstOrFail();
        $limit = $validated['limit'] ?? 10; 
        $timer = $validated['timer'] ?? 30; 

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
        // SECURITY: Validasi ID adalah integer positif
        if (!is_numeric($id) || $id <= 0) {
            abort(404);
        }

        $result = Result::with(['category', 'answers.question.options', 'answers.option'])
                        ->findOrFail($id);
        return view('review', compact('result'));
    }

    /**
     * PROSES SIMPAN SKOR (SECURE VERSION)
     * Menghitung skor di server berdasarkan jawaban user.
     * 
     * SECURITY NOTES:
     * - Skor dihitung 100% di server, tidak menerima input skor dari client
     * - Semua input divalidasi ketat
     * - Menggunakan explicit column assignment untuk mencegah mass assignment
     */
    public function submit(Request $request)
    {
        // 1. SECURITY: Validasi Input Ketat
        $data = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'player_name' => 'required|string|max:50|regex:/^[a-zA-Z0-9\s\-\_]+$/',
            'answers' => 'required|array|min:1|max:100',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer' => 'present', // Bisa null, single value, atau array
            'answers.*.time_left' => 'nullable|integer|min:0|max:300',
        ]);

        // 2. Tentukan User (logged in atau guest)
        $userId = null;
        $playerName = $data['player_name'];
        
        if (Auth::check()) {
            $userId = Auth::id();
            $playerName = Auth::user()->name; // Override dengan nama akun
        }
        session(['current_player' => $playerName]);

        // 3. SECURITY: Validasi bahwa soal milik kategori yang diklaim
        $categoryId = $data['category_id'];
        $questionIds = collect($data['answers'])->pluck('question_id')->unique();
        
        $validQuestionCount = Question::whereIn('id', $questionIds)
            ->where('category_id', $categoryId)
            ->count();
        
        if ($validQuestionCount !== $questionIds->count()) {
            return response()->json([
                'message' => 'Error: Soal tidak valid untuk kategori ini.',
            ], 422);
        }

        // 4. LOGIKA HITUNG SKOR (100% SERVER-SIDE - ANTI CHEAT)
        $calculatedScore = 0;
        $correctCount = 0;
        $totalQuestions = count($data['answers']);
        $processedAnswers = [];

        foreach ($data['answers'] as $ans) {
            $question = Question::with('options')->find($ans['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            $userAnswerVal = $ans['answer'] ?? null; 
            $timeLeft = min(max((int)($ans['time_left'] ?? 0), 0), 300); // Clamp 0-300

            // A. Single Choice
            if ($question->type === 'single') {
                if (is_numeric($userAnswerVal)) {
                    $opt = $question->options->where('id', (int)$userAnswerVal)->first();
                    $isCorrect = $opt && $opt->is_correct;
                }
                $saveOptionId = is_numeric($userAnswerVal) ? (int)$userAnswerVal : null;
            } 
            // B. Multiple Choice
            elseif ($question->type === 'multiple' && is_array($userAnswerVal)) {
                $userIds = array_map('intval', $userAnswerVal);
                $correctIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
                sort($userIds); 
                sort($correctIds);
                $isCorrect = $userIds == $correctIds;
                $saveOptionId = null;
            }
            // C. Ordering
            elseif ($question->type === 'ordering' && is_array($userAnswerVal)) {
                $userOrder = array_map('intval', $userAnswerVal);
                $correctSequence = $question->options->sortBy('correct_order')->pluck('id')->toArray();
                $isCorrect = array_values($userOrder) === array_values($correctSequence);
                $saveOptionId = null;
            }
            // D. Matching
            elseif ($question->type === 'matching' && is_array($userAnswerVal)) {
                $allMatch = true;
                foreach ($userAnswerVal as $item) {
                    if (!is_array($item) || !isset($item['left_id']) || !isset($item['pair_text'])) {
                        $allMatch = false;
                        break;
                    }
                    $opt = $question->options->where('id', (int)$item['left_id'])->first();
                    if (!$opt || $opt->matching_pair !== $item['pair_text']) {
                        $allMatch = false;
                        break;
                    }
                }
                $isCorrect = $allMatch;
                $saveOptionId = null;
            } else {
                $isCorrect = false;
                $saveOptionId = null;
            }

            // Hitung Poin: 100 + Sisa Waktu (Hanya jika benar)
            if ($isCorrect) {
                $calculatedScore += 100 + $timeLeft;
                $correctCount++;
            }

            $processedAnswers[] = [
                'question_id' => $question->id,
                'option_id' => $saveOptionId,
                'is_correct' => $isCorrect
            ];
        }

        // 5. SECURITY: Simpan dengan explicit columns (Anti Mass Assignment)
        $result = new Result();
        $result->user_id = $userId;
        $result->player_name = $playerName;
        $result->category_id = $categoryId;
        $result->score = $calculatedScore;
        $result->correct_answers = $correctCount;
        $result->total_questions = $totalQuestions;
        $result->save();

        // 6. Simpan Detail Jawaban
        foreach ($processedAnswers as $pAns) {
            $optId = $pAns['option_id'];
            
            // Fix foreign key constraint jika null
            if ($optId === null) {
                $firstOpt = Option::where('question_id', $pAns['question_id'])->first();
                $optId = $firstOpt ? $firstOpt->id : null;
            }

            if ($optId) {
                $resultAnswer = new ResultAnswer();
                $resultAnswer->result_id = $result->id;
                $resultAnswer->question_id = $pAns['question_id'];
                $resultAnswer->option_id = $optId;
                $resultAnswer->is_correct = $pAns['is_correct'];
                $resultAnswer->save();
            }
        }

        // 7. Cek Achievements
        $achievData = [
            'score' => $calculatedScore,
            'correct' => $correctCount,
            'total' => $totalQuestions
        ];
        $newBadges = $this->checkAchievements($playerName, $userId, $achievData);

        return response()->json([
            'message' => 'Skor berhasil disimpan!',
            'result_id' => $result->id,
            'new_badges' => $newBadges,
            'real_score' => $calculatedScore,
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