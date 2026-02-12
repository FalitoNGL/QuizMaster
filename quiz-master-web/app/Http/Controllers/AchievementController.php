<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;

class AchievementController extends Controller
{
    public function index()
    {
        $playerName = session('current_player');

        // Ambil semua lencana yang ada
        $allBadges = Achievement::all();

        // Ambil ID lencana yang SUDAH didapat pemain ini
        $myBadgeIds = [];
        if ($playerName) {
            $myBadgeIds = DB::table('player_achievements')
                            ->where('player_name', $playerName)
                            ->pluck('achievement_id')
                            ->toArray();
        }

        return view('achievements', compact('allBadges', 'myBadgeIds', 'playerName'));
    }
}