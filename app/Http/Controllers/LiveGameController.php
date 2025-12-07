<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameRoom;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;
use App\Models\Challenge;
use App\Events\GameUpdated;
use App\Events\NewChallengeReceived;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LiveGameController extends Controller
{
    // --- 1. MENU & ROOM MANAGEMENT ---
    
    public function index()
    {
        $categories = Category::all();
        return view('live.lobby', compact('categories'));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'total_questions' => 'required|integer|min:3|max:50',
            'duration' => 'required|integer|min:5|max:120',
        ]);

        $count = Question::where('category_id', $request->category_id)->count();
        if ($count < $request->total_questions) {
            return back()->with('error', "Kategori kurang soal (Ada: $count).");
        }

        $roomCode = strtoupper(Str::random(5));
        
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

    public function joinRoom(Request $request)
    {
        $request->validate(['room_code' => 'required']);
        $room = GameRoom::where('room_code', $request->room_code)->first();

        if (!$room) return back()->with('error', 'Room tidak ditemukan!');
        
        if ($room->host_id == Auth::id()) {
            return redirect()->route('live.play', $room->room_code);
        }

        if ($room->status !== 'waiting') {
            return back()->with('error', 'Game sudah berjalan atau selesai!');
        }

        $room->update(['challenger_id' => Auth::id(), 'status' => 'playing']);

        try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}

        return redirect()->route('live.play', $room->room_code);
    }

    public function play($roomCode)
    {
        $room = GameRoom::where('room_code', $roomCode)->with(['host', 'challenger', 'category'])->firstOrFail();
        
        // Ambil soal. Idealnya 'is_correct' disembunyikan (makeHidden) tapi
        // frontend saat ini butuh untuk visual feedback instan (merah/hijau).
        // Keamanan utama ada di endpoint updateScore.
        $questions = Question::with('options')
                        ->where('category_id', $room->category_id)
                        ->inRandomOrder($room->id) // Seed sama untuk kedua pemain
                        ->take($room->total_questions)
                        ->get();

        return view('live.play', compact('room', 'questions'));
    }

    // --- 2. CORE GAMEPLAY (SECURED) ---

    public function updateScore(Request $request)
    {
        // Validasi input: Kita terima JAWABAN, bukan SKOR.
        $request->validate([
            'room_code' => 'required',
            'question_id' => 'required|exists:questions,id',
            'time_left' => 'integer|min:0',
            'answer' => 'required' // Bisa single ID atau Array
        ]);

        $room = GameRoom::where('room_code', $request->room_code)->firstOrFail();
        
        // Cek status game
        if ($room->status !== 'playing') {
            return response()->json(['status' => 'error', 'message' => 'Game not playing']);
        }

        // VALIDASI JAWABAN DI SERVER (ANTI-CHEAT)
        $isCorrect = $this->validateAnswer($request);

        if ($isCorrect) {
            // Hitung Poin: Basis 100 + Bonus Waktu
            $points = 100 + ($request->time_left ?? 0);
            
            // Update skor di DB
            if ($room->host_id == Auth::id()) {
                $room->increment('host_score', $points);
            } elseif ($room->challenger_id == Auth::id()) {
                $room->increment('challenger_score', $points);
            }

            // Broadcast skor baru ke semua pemain
            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
            
            return response()->json(['status' => 'correct', 'points' => $points]);
        }

        return response()->json(['status' => 'wrong', 'points' => 0]);
    }

    /**
     * Logika Validasi Jawaban Kompleks
     */
    private function validateAnswer($request)
    {
        $question = Question::with('options')->find($request->question_id);
        if (!$question) return false;

        $type = $question->type;
        $input = $request->input('answer'); 

        // 1. Single Choice (Input: Option ID)
        if ($type === 'single') {
            $option = $question->options->where('id', $input)->first();
            return $option && $option->is_correct;
        }

        // 2. Multiple Choice (Input: Array of Option IDs)
        if ($type === 'multiple' && is_array($input)) {
            $correctIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
            sort($input); 
            sort($correctIds);
            return $input == $correctIds;
        }

        // 3. Ordering (Input: Array of Option IDs in user order)
        if ($type === 'ordering' && is_array($input)) {
            $correctSequence = $question->options->sortBy('correct_order')->pluck('id')->toArray();
            // Reset keys array agar bisa dibandingkan (0,1,2...)
            return array_values($input) === array_values($correctSequence);
        }

        // 4. Matching (Input: Array of objects {left_id, pair_text})
        if ($type === 'matching' && is_array($input)) {
            foreach ($input as $item) {
                // Pastikan item punya struktur yg benar
                if (!isset($item['left_id']) || !isset($item['pair_text'])) return false;

                $opt = $question->options->where('id', $item['left_id'])->first();
                // Jika opsi tidak ditemukan atau pasangannya salah
                if (!$opt || $opt->matching_pair !== $item['pair_text']) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    public function finishGame(Request $request)
    {
        $room = GameRoom::where('room_code', $request->room_code)->firstOrFail();

        // Hanya proses jika belum finish
        if ($room->status !== 'finished') {
            $room->update(['status' => 'finished']);
            
            // Tentukan Pemenang
            $winnerId = null;
            if ($room->host_score > $room->challenger_score) {
                $winnerId = $room->host_id;
            } elseif ($room->challenger_score > $room->host_score) {
                $winnerId = $room->challenger_id;
            }

            // Update Tabel Challenge jika ini berasal dari challenge
            Challenge::where('room_code', $request->room_code)
                     ->update([
                         'status' => 'completed',
                         'winner_id' => $winnerId
                     ]);

            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
        }

        return response()->json(['status' => 'finished']);
    }

    // --- 3. CHALLENGE SYSTEM (Direct Duel) ---

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