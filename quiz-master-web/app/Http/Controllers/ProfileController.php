<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $request->input('search');
        
        // 1. Pencarian
        $searchResults = [];
        if ($query) {
            $searchResults = User::where('id', '!=', $user->id)
                                ->where(function($q) use ($query) {
                                    $q->where('name', 'LIKE', "%{$query}%")
                                      ->orWhere('email', 'LIKE', "%{$query}%");
                                })
                                ->take(10)->get();
        }

        // 2. Teman & Follower
        $following = $user->following()->get();
        $followers = $user->followers()->get();

        // 3. Tantangan Masuk (Inbox)
        $challenges = \App\Models\Challenge::with(['sender', 'room.category'])
                        ->where('target_id', $user->id)
                        ->where('status', 'pending')
                        ->latest()
                        ->get();

        // 4. RIWAYAT DUEL (BARU!)
        // Ambil duel di mana user sebagai Sender ATAU Target, dan statusnya 'completed'
        $history = \App\Models\Challenge::with(['sender', 'target', 'room.category'])
                        ->where('status', 'completed')
                        ->where(function($q) use ($user) {
                            $q->where('sender_id', $user->id)
                              ->orWhere('target_id', $user->id);
                        })
                        ->latest()
                        ->take(20) // Ambil 20 terakhir
                        ->get();

        return view('profile.index', compact('user', 'following', 'followers', 'searchResults', 'query', 'challenges', 'history'));
    }

    /**
     * 2. LIHAT PROFIL (Publik/Detail)
     */
    public function show($id)
    {
        $user = User::withCount(['followers', 'following'])->findOrFail($id);
        
        $totalScore = Result::where('user_id', $id)->sum('score');
        $totalGames = Result::where('user_id', $id)->count();
        
        $achievements = DB::table('player_achievements')
                            ->join('achievements', 'player_achievements.achievement_id', '=', 'achievements.id')
                            ->where('player_achievements.user_id', $id)
                            ->select('achievements.*', 'player_achievements.created_at as unlocked_at')
                            ->get();

        $isFollowing = false;
        if (Auth::check()) {
            $isFollowing = Auth::user()->isFollowing($id);
        }

        return view('profile.show', compact('user', 'totalScore', 'totalGames', 'achievements', 'isFollowing'));
    }

    /**
     * 3. HALAMAN EDIT PROFIL
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * 4. PROSES UPDATE PROFIL
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:50',
            'title' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:150',
        ]);

        $user->update([
            'name' => $request->name,
            'title' => $request->title,
            'bio' => $request->bio,
        ]);

        return redirect()->route('profile.show', $user->id)->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * 5. LOGIKA FOLLOW / UNFOLLOW
     */
    public function follow($id)
    {
        $me = Auth::user();
        $target = User::findOrFail($id);

        if ($me->id == $target->id) return back()->with('error', 'Tidak bisa follow diri sendiri');

        if ($me->isFollowing($id)) {
            $me->following()->detach($id);
            return back()->with('success', 'Berhenti mengikuti ' . $target->name);
        } else {
            $me->following()->attach($id);
            return back()->with('success', 'Mulai mengikuti ' . $target->name);
        }
    }
}