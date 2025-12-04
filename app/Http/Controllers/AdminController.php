<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // ==========================================
    // 1. OTENTIKASI ADMIN
    // ==========================================

    public function showLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
        // Password sederhana (hardcoded) sesuai request
        if ($request->password === 'admin123') {
            session(['is_admin' => true]);
            return redirect()->route('admin.dashboard');
        }
        return back()->with('error', 'Password salah!');
    }

    public function logout() {
        session()->forget('is_admin');
        return redirect()->route('menu');
    }

    // ==========================================
    // 2. DASHBOARD & STATISTIK
    // ==========================================

    public function index() {
        if (!session('is_admin')) return redirect()->route('admin.login');

        // Statistik Kartu
        $totalQuestions = Question::count();
        $totalGames = Result::count();
        $totalPlayers = Result::distinct('player_name')->count('player_name');
        $avgScore = round(Result::avg('score') ?? 0);

        // Data Grafik (Kategori Terpopuler)
        $chartData = Result::select('category_id', DB::raw('count(*) as total'))
                        ->groupBy('category_id')
                        ->with('category')
                        ->get();
        
        $labels = $chartData->pluck('category.name');
        $data = $chartData->pluck('total');

        // Data Tabel (Soal & Aktivitas Terbaru)
        $questions = Question::with('category')->latest()->paginate(5);
        $recentActivities = Result::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'questions', 'totalQuestions', 'totalGames', 'totalPlayers', 'avgScore',
            'labels', 'data', 'recentActivities'
        ));
    }

    // ==========================================
    // 3. MANAJEMEN KATEGORI
    // ==========================================

    public function categories() {
        if (!session('is_admin')) return redirect()->route('admin.login');
        
        $categories = Category::withCount('questions')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug|alpha_dash',
            'description' => 'required|string',
            'icon_class' => 'required|string'
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories')->with('success', 'Kategori baru berhasil dibuat!');
    }

    public function deleteCategory($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');

        Category::destroy($id);
        return redirect()->route('admin.categories')->with('success', 'Kategori dan semua soal di dalamnya telah dihapus.');
    }

    // ==========================================
    // 4. MANAJEMEN SOAL (CRUD)
    // ==========================================

    public function create() {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $categories = Category::all();
        return view('admin.create', compact('categories'));
    }

    public function store(Request $request) {
        // Validasi Input Utama
        $request->validate([
            'category_id' => 'required',
            'type' => 'required|in:single,multiple,ordering,matching',
            'question_text' => 'required',
            'image' => 'nullable|image|max:2048',
            'audio' => 'nullable|mimes:mp3,wav|max:5120',
            'explanation' => 'nullable|string',
            'reference' => 'nullable|string|max:255'
        ]);

        // Validasi Opsi Berdasarkan Tipe
        if ($request->type == 'single') {
            $request->validate(['options_single' => 'required|array|min:2', 'correct_single' => 'required']);
        } elseif ($request->type == 'multiple') {
            $request->validate(['options_multiple' => 'required|array|min:2', 'correct_multiple' => 'required|array']);
        } elseif ($request->type == 'ordering') {
            $request->validate(['options_ordering' => 'required|array|min:2']);
        } elseif ($request->type == 'matching') {
            $request->validate(['options_matching_left' => 'required|array|min:2', 'options_matching_right' => 'required|array|min:2']);
        }

        // Upload Media
        $imagePath = $request->file('image') ? $request->file('image')->store('question_images', 'public') : null;
        $audioPath = $request->file('audio') ? $request->file('audio')->store('question_audio', 'public') : null;

        DB::beginTransaction();
        try {
            // Simpan Pertanyaan
            $q = Question::create([
                'category_id' => $request->category_id,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'audio_path' => $audioPath,
                'explanation' => $request->explanation,
                'reference' => $request->reference
            ]);

            // Simpan Opsi Jawaban
            if ($request->type == 'single') {
                foreach ($request->options_single as $idx => $val) {
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $val,
                        'is_correct' => ($idx == $request->correct_single)
                    ]);
                }
            } 
            elseif ($request->type == 'multiple') {
                foreach ($request->options_multiple as $idx => $val) {
                    $isCorrect = isset($request->correct_multiple) && in_array($idx, $request->correct_multiple);
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $val,
                        'is_correct' => $isCorrect
                    ]);
                }
            }
            elseif ($request->type == 'ordering') {
                foreach ($request->options_ordering as $idx => $val) {
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $val,
                        'correct_order' => $idx + 1
                    ]);
                }
            }
            elseif ($request->type == 'matching') {
                $count = count($request->options_matching_left);
                for ($i = 0; $i < $count; $i++) {
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $request->options_matching_left[$i],
                        'matching_pair' => $request->options_matching_right[$i]
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success', 'Soal berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Bersihkan file jika gagal
            if ($imagePath) Storage::disk('public')->delete($imagePath);
            if ($audioPath) Storage::disk('public')->delete($audioPath);
            
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $question = Question::with('options')->findOrFail($id);
        $categories = Category::all();
        return view('admin.edit', compact('question', 'categories'));
    }

    public function update(Request $request, $id) {
        $q = Question::findOrFail($id);
        
        $dataToUpdate = [
            'category_id' => $request->category_id,
            'question_text' => $request->question_text,
            'explanation' => $request->explanation,
            'reference' => $request->reference
        ];

        if ($request->hasFile('image')) {
            if ($q->image_path) Storage::disk('public')->delete($q->image_path);
            $dataToUpdate['image_path'] = $request->file('image')->store('question_images', 'public');
        }

        if ($request->hasFile('audio')) {
            if ($q->audio_path) Storage::disk('public')->delete($q->audio_path);
            $dataToUpdate['audio_path'] = $request->file('audio')->store('question_audio', 'public');
        }

        $q->update($dataToUpdate);

        // Update Opsi (Hanya support Single Choice sederhana untuk saat ini)
        if ($q->type == 'single' && $request->has('options')) {
            $i = 0;
            foreach ($q->options as $option) {
                if (isset($request->options[$i])) {
                    $option->update([
                        'option_text' => $request->options[$i],
                        'is_correct' => ($i == $request->correct_index)
                    ]);
                }
                $i++;
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'Soal berhasil diupdate!');
    }

    public function destroy($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        
        $q = Question::findOrFail($id);
        if ($q->image_path) Storage::disk('public')->delete($q->image_path);
        if ($q->audio_path) Storage::disk('public')->delete($q->audio_path);
        
        $q->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Soal berhasil dihapus!');
    }

    // ==========================================
    // 5. IMPORT JSON (FITUR BARU!)
    // ==========================================

    public function import() {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $categories = Category::all();
        return view('admin.import', compact('categories'));
    }

    public function processImport(Request $request) {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'json_file' => 'required|file|mimes:json,txt',
        ]);

        try {
            // 1. Baca & Decode File
            $file = $request->file('json_file');
            $content = file_get_contents($file->getRealPath());
            $data = json_decode($content, true);

            if (!$data || !is_array($data)) {
                return back()->with('error', 'Format JSON tidak valid. Pastikan file berisi array soal.');
            }

            DB::beginTransaction();
            $count = 0;

            // 2. Loop Data
            foreach ($data as $item) {
                // Validasi field wajib dalam JSON
                if (empty($item['question']) || empty($item['options'])) continue;

                // Default values
                $type = $item['type'] ?? 'single';
                
                // Buat Soal
                $q = Question::create([
                    'category_id' => $request->category_id,
                    'type' => $type,
                    'question_text' => $item['question'],
                    'explanation' => $item['explanation'] ?? null,
                    'reference' => $item['reference'] ?? null,
                    // Gambar/Audio bisa ditambahkan logic download jika perlu, 
                    // tapi untuk import dasar kita skip dulu.
                ]);

                // Buat Opsi
                if ($type == 'single' || $type == 'multiple') {
                    foreach ($item['options'] as $idx => $optText) {
                        $isCorrect = false;
                        if (isset($item['correct'])) {
                            if (is_array($item['correct'])) {
                                $isCorrect = in_array($idx, $item['correct']);
                            } else {
                                $isCorrect = ($idx == $item['correct']);
                            }
                        }

                        Option::create([
                            'question_id' => $q->id,
                            'option_text' => $optText,
                            'is_correct' => $isCorrect
                        ]);
                    }
                }
                // Tambahkan logic untuk ordering/matching jika format JSON mendukungnya nanti
                
                $count++;
            }

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success', "Berhasil mengimport $count soal!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }
}