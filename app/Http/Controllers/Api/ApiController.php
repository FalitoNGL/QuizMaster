<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use App\Models\Result;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * GET /api/categories
     * Mengambil semua kategori beserta jumlah soalnya.
     */
    public function categories()
    {
        $categories = Category::withCount('questions')->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * GET /api/quiz/{id}
     * Mengambil soal beserta opsinya berdasarkan kategori ID.
     * Menyembunyikan field 'is_correct' dan 'matching_pair' dari opsi untuk keamanan.
     */
    public function quiz($id, Request $request)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $limit = $request->input('limit', 10);

        $questions = Question::with(['options' => function ($query) {
            // Sembunyikan jawaban benar dari response API
            $query->select('id', 'question_id', 'option_text', 'correct_order');
        }])
            ->where('category_id', $id)
            ->inRandomOrder()
            ->take($limit)
            ->get()
            ->makeHidden(['created_at', 'updated_at']); // Bersihkan timestamp

        return response()->json([
            'success' => true,
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug
            ],
            'total_available' => Question::where('category_id', $id)->count(),
            'questions' => $questions
        ]);
    }

    /**
     * GET /api/leaderboard
     * Mengambil data skor tertinggi (20 teratas).
     */
    public function leaderboard(Request $request)
    {
        $limit = $request->input('limit', 20);

        $topScores = Result::with('category:id,name,slug')
            ->select('id', 'player_name', 'category_id', 'score', 'correct_answers', 'total_questions', 'created_at')
            ->orderBy('score', 'desc')
            ->take($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topScores
        ]);
    }

    /**
     * GET /api/achievements
     * Mengambil daftar achievement user berdasarkan user_id atau player_name.
     * Query param: user_id atau player_name
     */
    public function achievements(Request $request)
    {
        $userId = $request->input('user_id');
        $playerName = $request->input('player_name');

        // Ambil semua achievement yang tersedia
        $allAchievements = Achievement::all();

        // Jika tidak ada filter, return semua achievement (daftar master)
        if (!$userId && !$playerName) {
            return response()->json([
                'success' => true,
                'message' => 'Daftar semua achievement. Gunakan ?user_id atau ?player_name untuk melihat achievement user.',
                'data' => $allAchievements
            ]);
        }

        // Query achievement yang sudah di-unlock
        $query = DB::table('player_achievements')
            ->join('achievements', 'player_achievements.achievement_id', '=', 'achievements.id');

        if ($userId) {
            $query->where('player_achievements.user_id', $userId);
        } else {
            $query->where('player_achievements.player_name', $playerName);
        }

        $unlocked = $query->select(
            'achievements.id',
            'achievements.name',
            'achievements.slug',
            'achievements.description',
            'achievements.icon',
            'player_achievements.created_at as unlocked_at'
        )->get();

        return response()->json([
            'success' => true,
            'player' => $userId ? "user_id: $userId" : "player_name: $playerName",
            'unlocked_count' => $unlocked->count(),
            'total_achievements' => $allAchievements->count(),
            'data' => $unlocked
        ]);
    }

    /**
     * POST /api/quiz/submit
     * Menerima jawaban dari aplikasi Android dan menghitung skor di server.
     * 
     * Request Body:
     * {
     *   "player_name": "John",
     *   "category_id": 1,
     *   "answers": [
     *     {"question_id": 1, "answer": 3, "time_left": 15},
     *     {"question_id": 2, "answer": [1,3], "time_left": 10}
     *   ]
     * }
     */
    public function submit(Request $request)
    {
        // 1. Validasi Input
        $data = $request->validate([
            'player_name' => 'required|string|max:50|regex:/^[a-zA-Z0-9\s\-\_]+$/',
            'category_id' => 'required|integer|exists:categories,id',
            'answers' => 'required|array|min:1|max:100',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.answer' => 'present',
            'answers.*.time_left' => 'nullable|integer|min:0|max:300',
        ]);

        $categoryId = $data['category_id'];
        $playerName = $data['player_name'];

        // 2. Validasi soal milik kategori yang diklaim
        $questionIds = collect($data['answers'])->pluck('question_id')->unique();
        $validCount = Question::whereIn('id', $questionIds)
            ->where('category_id', $categoryId)
            ->count();

        if ($validCount !== $questionIds->count()) {
            return response()->json([
                'success' => false,
                'message' => 'Soal tidak valid untuk kategori ini.'
            ], 422);
        }

        // 3. Hitung Skor (Server-Side - Anti Cheat)
        $calculatedScore = 0;
        $correctCount = 0;
        $totalQuestions = count($data['answers']);

        foreach ($data['answers'] as $ans) {
            $question = Question::with('options')->find($ans['question_id']);
            if (!$question) continue;

            $isCorrect = false;
            $userAnswer = $ans['answer'] ?? null;
            $timeLeft = min(max((int)($ans['time_left'] ?? 0), 0), 300);

            // Single Choice
            if ($question->type === 'single' && is_numeric($userAnswer)) {
                $opt = $question->options->where('id', (int)$userAnswer)->first();
                $isCorrect = $opt && $opt->is_correct;
            }
            // Multiple Choice
            elseif ($question->type === 'multiple' && is_array($userAnswer)) {
                $userIds = array_map('intval', $userAnswer);
                $correctIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
                sort($userIds);
                sort($correctIds);
                $isCorrect = $userIds == $correctIds;
            }
            // Ordering
            elseif ($question->type === 'ordering' && is_array($userAnswer)) {
                $userOrder = array_map('intval', $userAnswer);
                $correctSeq = $question->options->sortBy('correct_order')->pluck('id')->toArray();
                $isCorrect = array_values($userOrder) === array_values($correctSeq);
            }

            if ($isCorrect) {
                $calculatedScore += 100 + $timeLeft;
                $correctCount++;
            }
        }

        // 4. Simpan Hasil
        $result = new Result();
        $result->user_id = null; // API tidak pakai auth untuk sementara
        $result->player_name = $playerName;
        $result->category_id = $categoryId;
        $result->score = $calculatedScore;
        $result->correct_answers = $correctCount;
        $result->total_questions = $totalQuestions;
        $result->save();

        return response()->json([
            'success' => true,
            'result_id' => $result->id,
            'player_name' => $playerName,
            'score' => $calculatedScore,
            'correct' => $correctCount,
            'total' => $totalQuestions,
            'accuracy' => $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 1) : 0
        ]);
    }
}
