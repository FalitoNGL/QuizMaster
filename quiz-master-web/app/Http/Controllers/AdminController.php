<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;
use App\Models\Result;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Imports\QuestionsImport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    // ... (Fungsi Login, Logout, Index, Categories, Import SAMA SEPERTI SEBELUMNYA) ...
    // Salin bagian atas file asli Anda sampai sebelum method create()
    
    // ==========================================
    // 1. OTENTIKASI ADMIN
    // ==========================================

    public function showLogin() {
        return view('admin.login');
    }

    public function login(Request $request) {
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

        $totalQuestions = Question::count();
        $totalGames = Result::count();
        $totalPlayers = Result::distinct('player_name')->count('player_name');
        $avgScore = round(Result::avg('score') ?? 0);

        $chartData = Result::select('category_id', DB::raw('count(*) as total'))
                        ->groupBy('category_id')->with('category')->get();
        $labels = $chartData->pluck('category.name');
        $data = $chartData->pluck('total');

        $questions = Question::with('category')->latest()->paginate(10); 
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
        return redirect()->route('admin.categories')->with('success', 'Kategori berhasil dibuat!');
    }

    public function editCategory($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function updateCategory(Request $request, $id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug,'.$id.'|alpha_dash',
            'description' => 'required|string',
            'icon_class' => 'required|string'
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());
        
        return redirect()->route('admin.categories')->with('success', 'Kategori diperbarui!');
    }

    public function deleteCategory($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        Category::destroy($id);
        return redirect()->route('admin.categories')->with('success', 'Kategori dihapus.');
    }

    // ==========================================
    // 4. IMPORT JSON & EXCEL
    // ==========================================

    public function import() {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $categories = Category::all();
        return view('admin.import', compact('categories'));
    }

    public function processImport(Request $request) {
        set_time_limit(300); 

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'file_import' => 'required|file|mimes:json,txt,xlsx,xls,csv',
        ]);

        try {
            $file = $request->file('file_import');
            $ext = $file->getClientOriginalExtension();

            if (in_array($ext, ['xlsx', 'xls', 'csv'])) {
                Excel::import(new QuestionsImport($request->category_id), $file);
                return redirect()->route('admin.dashboard')->with('success', 'Import Excel Berhasil!');
            } else {
                $jsonContent = file_get_contents($file->getRealPath());
                $data = json_decode($jsonContent, true);

                if (!$data || !is_array($data)) {
                    return back()->with('error', 'Format JSON tidak valid.');
                }

                $existingQuestions = Question::where('category_id', $request->category_id)
                                            ->pluck('question_text')
                                            ->toArray();

                DB::beginTransaction();
                $count = 0;
                $skipped = 0;

                foreach ($data as $item) {
                    if (!isset($item['question']) || !isset($item['options'])) {
                        continue;
                    }

                    if (in_array($item['question'], $existingQuestions)) {
                        $skipped++;
                        continue; 
                    }

                    $q = Question::create([
                        'category_id' => $request->category_id,
                        'type' => $item['type'] ?? 'single',
                        'question_text' => $item['question'],
                        'explanation' => $item['explanation'] ?? null,
                        'reference' => $item['reference'] ?? null,
                    ]);

                    if ($q->type == 'matching') {
                        if (isset($item['pairs'])) {
                            foreach ($item['pairs'] as $pair) {
                                Option::create([
                                    'question_id' => $q->id,
                                    'option_text' => $pair['left'],
                                    'matching_pair' => $pair['right'],
                                    'is_correct' => true
                                ]);
                            }
                        }
                    } elseif ($q->type == 'ordering') {
                        $items = $item['correctOrder'] ?? ($item['items'] ?? []);
                        foreach ($items as $index => $val) {
                            Option::create([
                                'question_id' => $q->id,
                                'option_text' => $val,
                                'correct_order' => $index + 1,
                                'is_correct' => true
                            ]);
                        }
                    } else {
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
                    $count++;
                }

                DB::commit();
                
                $msg = "Berhasil mengimport $count soal JSON!";
                if ($skipped > 0) $msg .= " ($skipped duplikat dilewati)";

                return redirect()->route('admin.dashboard')->with('success', $msg);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    // ==========================================
    // 5. MANAJEMEN SOAL MANUAL (CRUD)
    // ==========================================

    public function create() {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $categories = Category::all();
        return view('admin.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'category_id' => 'required',
            'type' => 'required',
            'question_text' => 'required',
            'image' => 'nullable|image|max:2048',
            'audio' => 'nullable|mimes:mp3,wav|max:5120',
            'explanation' => 'nullable|string',
            'reference' => 'nullable|string'
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('question_images', 'public') : null;
        $audioPath = $request->file('audio') ? $request->file('audio')->store('question_audio', 'public') : null;

        DB::beginTransaction();
        try {
            $q = Question::create([
                'category_id' => $request->category_id,
                'type' => $request->type,
                'question_text' => $request->question_text,
                'image_path' => $imagePath,
                'audio_path' => $audioPath,
                'explanation' => $request->explanation,
                'reference' => $request->reference
            ]);

            $this->saveOptions($q, $request); // Refactored into helper method

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success', 'Soal berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage())->withInput();
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
        
        $request->validate([
            'category_id' => 'required',
            'question_text' => 'required',
            'image' => 'nullable|image|max:2048',
            'audio' => 'nullable|mimes:mp3,wav|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $dataToUpdate = [
                'category_id' => $request->category_id,
                'question_text' => $request->question_text,
                'explanation' => $request->explanation,
                'reference' => $request->reference,
                // Note: Kita tidak update 'type' agar struktur data tidak rusak, 
                // kecuali Anda ingin handle logic perubahan tipe secara ekstrem.
            ];

            if ($request->hasFile('image')) {
                if ($q->image_path) Storage::disk('public')->delete($q->image_path);
                $dataToUpdate['image_path'] = $request->file('image')->store('question_images', 'public');
            }
            // Logic Hapus Gambar
            if ($request->has('remove_image') && $request->remove_image == '1') {
                if ($q->image_path) Storage::disk('public')->delete($q->image_path);
                $dataToUpdate['image_path'] = null;
            }

            if ($request->hasFile('audio')) {
                if ($q->audio_path) Storage::disk('public')->delete($q->audio_path);
                $dataToUpdate['audio_path'] = $request->file('audio')->store('question_audio', 'public');
            }
            // Logic Hapus Audio
            if ($request->has('remove_audio') && $request->remove_audio == '1') {
                if ($q->audio_path) Storage::disk('public')->delete($q->audio_path);
                $dataToUpdate['audio_path'] = null;
            }

            $q->update($dataToUpdate);

            // STRATEGI: Hapus Semua Opsi Lama -> Buat Opsi Baru (Paling Aman untuk Dynamic Forms)
            $q->options()->delete();
            
            // Gunakan helper yang sama dengan store()
            $this->saveOptions($q, $request);

            DB::commit();
            return redirect()->route('admin.dashboard')->with('success', 'Soal berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal Update: ' . $e->getMessage())->withInput();
        }
    }

    // Helper untuk menyimpan opsi berdasarkan tipe (Reusability)
    private function saveOptions($q, $request) {
        if ($q->type == 'single' && $request->options_single) {
            foreach ($request->options_single as $idx => $val) {
                if(trim($val) === '') continue; // Skip kosong
                Option::create([
                    'question_id' => $q->id, 
                    'option_text' => $val, 
                    'is_correct' => ($idx == $request->correct_single)
                ]);
            }
        } elseif ($q->type == 'multiple' && $request->options_multiple) {
            foreach ($request->options_multiple as $idx => $val) {
                if(trim($val) === '') continue;
                $isCorrect = isset($request->correct_multiple) && in_array($idx, $request->correct_multiple);
                Option::create([
                    'question_id' => $q->id, 
                    'option_text' => $val, 
                    'is_correct' => $isCorrect
                ]);
            }
        } elseif ($q->type == 'ordering' && $request->options_ordering) {
            foreach ($request->options_ordering as $idx => $val) {
                if(trim($val) === '') continue;
                Option::create([
                    'question_id' => $q->id, 
                    'option_text' => $val, 
                    'correct_order' => $idx + 1, 
                    'is_correct' => true
                ]);
            }
        } elseif ($q->type == 'matching' && $request->options_matching_left) {
            $count = count($request->options_matching_left);
            for ($i = 0; $i < $count; $i++) {
                if(trim($request->options_matching_left[$i]) === '') continue;
                Option::create([
                    'question_id' => $q->id, 
                    'option_text' => $request->options_matching_left[$i], 
                    'matching_pair' => $request->options_matching_right[$i] ?? '',
                    'is_correct' => true
                ]);
            }
        }
    }

    public function destroy($id) {
        if (!session('is_admin')) return redirect()->route('admin.login');
        $q = Question::findOrFail($id);
        if ($q->image_path) Storage::disk('public')->delete($q->image_path);
        if ($q->audio_path) Storage::disk('public')->delete($q->audio_path);
        $q->delete();
        return redirect()->route('admin.dashboard')->with('success', 'Soal dihapus!');
    }

    public function cleanup() {
        if (!session('is_admin')) return redirect()->route('admin.login');

        $duplicates = Question::select('question_text', 'category_id', DB::raw('count(*) as total'))
                        ->groupBy('question_text', 'category_id')
                        ->having('total', '>', 1)
                        ->get();

        $deletedCount = 0;

        foreach ($duplicates as $dup) {
            $ids = Question::where('question_text', $dup->question_text)
                           ->where('category_id', $dup->category_id)
                           ->orderBy('id', 'asc')
                           ->pluck('id')
                           ->toArray();
            
            $idsToDelete = array_slice($ids, 1);
            
            if (!empty($idsToDelete)) {
                Question::destroy($idsToDelete);
                $deletedCount += count($idsToDelete);
            }
        }

        return redirect()->route('admin.dashboard')->with('success', "Berhasil menghapus $deletedCount soal duplikat.");
    }
}