<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameRoom;
use App\Models\Category;
use App\Models\Question;
use App\Events\GameUpdated;
use App\Models\Challenge;
use App\Events\NewChallengeReceived;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveGameController extends Controller
{
    // 1. MENU LIVE
    public function index()
    {
        $categories = Category::all();
        return view('live.lobby', compact('categories'));
    }

    // 2. BUAT ROOM MANUAL
    public function createRoom(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'total_questions' => 'required|integer|min:3|max:50',
            'duration' => 'required|integer|min:5|max:120',
        ]);

        $roomCode = strtoupper(Str::random(5));
        
        $count = Question::where('category_id', $request->category_id)->count();
        if ($count < $request->total_questions) {
            return back()->with('error', "Kategori kurang soal (Ada: $count).");
        }

        GameRoom::create([
            'room_code' => $roomCode,
            'category_id' => $request->category_id,
            'host_id' => Auth::id(),
            'status' => 'waiting',
            'total_questions' => $request->total_questions,
            'duration' => $request->duration
        ]);

        return redirect()->route('live.play', $roomCode);
    }

    // 3. JOIN ROOM
    public function joinRoom(Request $request)
    {
        $request->validate(['room_code' => 'required']);
        $room = GameRoom::where('room_code', $request->room_code)->first();

        if (!$room) return back()->with('error', 'Room tidak ditemukan!');
        
        if ($room->host_id == Auth::id()) {
            return redirect()->route('live.play', $room->room_code);
        }

        if ($room->status !== 'waiting') return back()->with('error', 'Game sudah selesai!');

        $room->update(['challenger_id' => Auth::id(), 'status' => 'playing']);

        try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}

        return redirect()->route('live.play', $room->room_code);
    }

    // 4. HALAMAN GAMEPLAY
    public function play($roomCode)
    {
        $room = GameRoom::where('room_code', $roomCode)->with(['host', 'challenger', 'category'])->firstOrFail();
        
        $questions = Question::with('options')
                        ->where('category_id', $room->category_id)
                        ->inRandomOrder($room->id)
                        ->take($room->total_questions)
                        ->get();

        return view('live.play', compact('room', 'questions'));
    }

    // 5. UPDATE SKOR
    public function updateScore(Request $request)
    {
        $room = GameRoom::where('room_code', $request->room_code)->firstOrFail();
        
        // Hanya update jika game masih berjalan
        if ($room->status === 'playing') {
            if ($room->host_id == Auth::id()) {
                $room->increment('host_score', $request->points);
            } else {
                $room->increment('challenger_score', $request->points);
            }
            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
        }

        return response()->json(['status' => 'ok']);
    }

    // 6. FINISH GAME (UPDATED: CATAT PEMENANG)
    public function finishGame(Request $request)
    {
        $room = GameRoom::where('room_code', $request->room_code)->firstOrFail();

        if ($room->status !== 'finished') {
            $room->update(['status' => 'finished']);
            
            // Hitung Pemenang
            $winnerId = null;
            if ($room->host_score > $room->challenger_score) {
                $winnerId = $room->host_id;
            } elseif ($room->challenger_score > $room->host_score) {
                $winnerId = $room->challenger_id;
            }
            // Jika seri, winnerId tetap null

            // Update Tabel Challenge
            Challenge::where('room_code', $request->room_code)
                     ->update([
                         'status' => 'completed',
                         'winner_id' => $winnerId
                     ]);

            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
        }

        return response()->json(['status' => 'finished']);
    }

    // --- FITUR CHALLENGE ---
    public function sendChallenge(Request $request)
    {
        $request->validate([
            'target_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'total_questions' => 'required',
            'duration' => 'required'
        ]);

        $roomCode = strtoupper(Str::random(5));
        
        GameRoom::create([
            'room_code' => $roomCode,
            'category_id' => $request->category_id,
            'host_id' => Auth::id(),
            'status' => 'waiting',
            'total_questions' => $request->total_questions,
            'duration' => $request->duration
        ]);

        $challenge = Challenge::create([
            'sender_id' => Auth::id(),
            'target_id' => $request->target_id,
            'room_code' => $roomCode,
            'status' => 'pending'
        ]);

        try { broadcast(new NewChallengeReceived($challenge)); } catch (\Exception $e) {}

        return redirect()->route('live.play', $roomCode);
    }

    public function acceptChallenge($id)
    {
        $challenge = Challenge::where('id', $id)->where('target_id', Auth::id())->firstOrFail();
        $challenge->update(['status' => 'accepted']);

        $room = GameRoom::where('room_code', $challenge->room_code)->first();
        if ($room && $room->status == 'waiting') {
            $room->update(['challenger_id' => Auth::id(), 'status' => 'playing']);
            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
            return redirect()->route('live.play', $room->room_code);
        }
        return back()->with('error', 'Room kadaluarsa.');
    }

    public function rejectChallenge($id)
    {
        Challenge::where('id', $id)->where('target_id', Auth::id())->delete();
        return back()->with('success', 'Tantangan ditolak.');
    }
}