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
}
