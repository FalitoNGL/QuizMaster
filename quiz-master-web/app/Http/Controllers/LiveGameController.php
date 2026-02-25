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
        // SECURITY: Validasi input ketat
        $validated = $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'total_questions' => 'required|integer|min:3|max:50',
            'duration' => 'required|integer|min:5|max:120',
        ]);

        $count = Question::where('category_id', $validated['category_id'])->count();
        if ($count < $validated['total_questions']) {
            return back()->with('error', "Kategori kurang soal (Ada: $count).");
        }

        $roomCode = strtoupper(Str::random(5));
        
        // SECURITY: Explicit column assignment (tidak ada mass assignment)
        $room = new GameRoom();
        $room->room_code = $roomCode;
        $room->category_id = $validated['category_id'];
        $room->host_id = Auth::id();
        $room->status = 'waiting';
        $room->total_questions = $validated['total_questions'];
        $room->duration = $validated['duration'];
        $room->host_score = 0;
        $room->challenger_score = 0;
        $room->save();

        return redirect()->route('live.play', $roomCode);
    }

    public function joinRoom(Request $request)
    {
        // SECURITY: Validasi room_code
        $validated = $request->validate([
            'room_code' => 'required|string|alpha_num|size:5'
        ]);

        $room = GameRoom::where('room_code', strtoupper($validated['room_code']))->first();

        if (!$room) return back()->with('error', 'Room tidak ditemukan!');
        
        if ($room->host_id == Auth::id()) {
            return redirect()->route('live.play', $room->room_code);
        }

        if ($room->status !== 'waiting') {
            return back()->with('error', 'Game sudah berjalan atau selesai!');
        }

        // SECURITY: Update dengan spesifik kolom saja
        $room->challenger_id = Auth::id();
        $room->status = 'playing';
        $room->save();

        try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}

        return redirect()->route('live.play', $room->room_code);
    }

    public function play($roomCode)
    {
        // SECURITY: Sanitize room code
        $roomCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $roomCode));

        $room = GameRoom::where('room_code', $roomCode)->with(['host', 'challenger', 'category'])->firstOrFail();
        
        // SECURITY: Verifikasi user adalah peserta game
        if (Auth::id() !== $room->host_id && Auth::id() !== $room->challenger_id && $room->status !== 'waiting') {
            abort(403, 'Anda bukan peserta game ini.');
        }

        $questions = Question::with('options')
                        ->where('category_id', $room->category_id)
                        ->inRandomOrder($room->id)
                        ->take($room->total_questions)
                        ->get();

        return view('live.play', compact('room', 'questions'));
    }

    // --- 2. CORE GAMEPLAY (SECURED) ---

    public function updateScore(Request $request)
    {
        // SECURITY: Validasi input ketat
        $validated = $request->validate([
            'room_code' => 'required|string|alpha_num|size:5',
            'question_id' => 'required|integer|exists:questions,id',
            'time_left' => 'nullable|integer|min:0|max:300',
            'answer' => 'required' // Bisa single ID atau Array
        ]);

        $room = GameRoom::where('room_code', strtoupper($validated['room_code']))->firstOrFail();
        
        // SECURITY: Verifikasi user adalah peserta game
        if (Auth::id() !== $room->host_id && Auth::id() !== $room->challenger_id) {
            return response()->json(['status' => 'error', 'message' => 'Not a participant'], 403);
        }

        if ($room->status !== 'playing') {
            return response()->json(['status' => 'error', 'message' => 'Game not playing']);
        }

        // SECURITY: Verifikasi soal benar-benar milik kategori room ini
        $question = Question::where('id', $validated['question_id'])
                            ->where('category_id', $room->category_id)
                            ->first();
        
        if (!$question) {
            return response()->json(['status' => 'error', 'message' => 'Invalid question']);
        }

        // VALIDASI JAWABAN DI SERVER (ANTI-CHEAT)
        $isCorrect = $this->validateAnswer($question, $validated['answer']);

        if ($isCorrect) {
            // Hitung Poin: Basis 100 + Bonus Waktu (max 300)
            $timeLeft = min(max((int)($validated['time_left'] ?? 0), 0), 300);
            $points = 100 + $timeLeft;
            
            // Update skor di DB dengan increment (aman dari race condition)
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
     * Logika Validasi Jawaban Kompleks (Server-Side Anti-Cheat)
     */
    private function validateAnswer($question, $input)
    {
        $question->load('options');
        $type = $question->type;

        // 1. Single Choice (Input: Option ID)
        if ($type === 'single') {
            if (!is_numeric($input)) return false;
            $option = $question->options->where('id', (int)$input)->first();
            return $option && $option->is_correct;
        }

        // 2. Multiple Choice (Input: Array of Option IDs)
        if ($type === 'multiple' && is_array($input)) {
            $userIds = array_map('intval', $input);
            $correctIds = $question->options->where('is_correct', 1)->pluck('id')->toArray();
            sort($userIds); 
            sort($correctIds);
            return $userIds == $correctIds;
        }

        // 3. Ordering (Input: Array of Option IDs in user order)
        if ($type === 'ordering' && is_array($input)) {
            $userOrder = array_map('intval', $input);
            $correctSequence = $question->options->sortBy('correct_order')->pluck('id')->toArray();
            return array_values($userOrder) === array_values($correctSequence);
        }

        // 4. Matching (Input: Array of objects {left_id, pair_text})
        if ($type === 'matching' && is_array($input)) {
            foreach ($input as $item) {
                if (!is_array($item) || !isset($item['left_id']) || !isset($item['pair_text'])) {
                    return false;
                }
                $opt = $question->options->where('id', (int)$item['left_id'])->first();
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
        // SECURITY: Validasi room_code
        $validated = $request->validate([
            'room_code' => 'required|string|alpha_num|size:5'
        ]);

        $room = GameRoom::where('room_code', strtoupper($validated['room_code']))->firstOrFail();

        // SECURITY: Hanya peserta yang boleh finish game
        if (Auth::id() !== $room->host_id && Auth::id() !== $room->challenger_id) {
            return response()->json(['status' => 'error', 'message' => 'Not a participant'], 403);
        }

        // Hanya proses jika belum finish
        if ($room->status !== 'finished') {
            $room->status = 'finished';
            $room->save();
            
            // Tentukan Pemenang
            $winnerId = null;
            if ($room->host_score > $room->challenger_score) {
                $winnerId = $room->host_id;
            } elseif ($room->challenger_score > $room->host_score) {
                $winnerId = $room->challenger_id;
            }

            // Update Tabel Challenge jika ini berasal dari challenge
            Challenge::where('room_code', $room->room_code)
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
        // SECURITY: Validasi input ketat dengan tipe data yang benar
        $validated = $request->validate([
            'target_id' => 'required|integer|exists:users,id|different:' . Auth::id(),
            'category_id' => 'required|integer|exists:categories,id',
            'total_questions' => 'required|integer|min:3|max:50',
            'duration' => 'required|integer|min:5|max:120'
        ]);

        // SECURITY: Verifikasi ada cukup soal
        $count = Question::where('category_id', $validated['category_id'])->count();
        if ($count < $validated['total_questions']) {
            return back()->with('error', "Kategori kurang soal (Ada: $count).");
        }

        $roomCode = strtoupper(Str::random(5));
        
        // SECURITY: Explicit column assignment
        $room = new GameRoom();
        $room->room_code = $roomCode;
        $room->category_id = $validated['category_id'];
        $room->host_id = Auth::id();
        $room->status = 'waiting';
        $room->total_questions = $validated['total_questions'];
        $room->duration = $validated['duration'];
        $room->host_score = 0;
        $room->challenger_score = 0;
        $room->save();

        // SECURITY: Explicit column assignment untuk Challenge
        $challenge = new Challenge();
        $challenge->sender_id = Auth::id();
        $challenge->target_id = $validated['target_id'];
        $challenge->room_code = $roomCode;
        $challenge->status = 'pending';
        $challenge->save();

        try { broadcast(new NewChallengeReceived($challenge)); } catch (\Exception $e) {}

        return redirect()->route('live.play', $roomCode);
    }

    public function acceptChallenge($id)
    {
        // SECURITY: Validasi ID adalah integer positif
        if (!is_numeric($id) || $id <= 0) {
            abort(404);
        }

        $challenge = Challenge::where('id', (int)$id)
                              ->where('target_id', Auth::id())
                              ->where('status', 'pending')
                              ->firstOrFail();
        
        $challenge->status = 'accepted';
        $challenge->save();

        $room = GameRoom::where('room_code', $challenge->room_code)->first();
        if ($room && $room->status == 'waiting') {
            $room->challenger_id = Auth::id();
            $room->status = 'playing';
            $room->save();
            
            try { broadcast(new GameUpdated($room)); } catch (\Exception $e) {}
            return redirect()->route('live.play', $room->room_code);
        }
        return back()->with('error', 'Room kadaluarsa.');
    }

    public function rejectChallenge($id)
    {
        // SECURITY: Validasi ID adalah integer positif
        if (!is_numeric($id) || $id <= 0) {
            abort(404);
        }

        Challenge::where('id', (int)$id)
                 ->where('target_id', Auth::id())
                 ->where('status', 'pending')
                 ->delete();
                 
        return back()->with('success', 'Tantangan ditolak.');
    }
}