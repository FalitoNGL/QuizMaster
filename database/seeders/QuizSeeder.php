<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\Question;
use App\Models\Option;

class QuizSeeder extends Seeder
{
    public function run()
    {
        // 1. Reset Database
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate(); 
        Question::truncate(); 
        Option::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Daftar Pemetaan File JSON -> Kategori Database
        // Saya menambahkan 'jaringan.json' di sini sesuai permintaan Anda
        $files = [
            'fundamental-keamanan.json' => [
                'slug' => 'fundamental-keamanan', 'name' => 'Fundamental Keamanan', 
                'desc' => 'Konsep dasar keamanan informasi.', 'icon' => 'FiKey'
            ],
            'biologi.json' => [
                'slug' => 'biologi-dasar', 'name' => 'Biologi Dasar', 
                'desc' => 'Konsep kehidupan dan organisme.', 'icon' => 'GiDna1'
            ],
            'intelijen.json' => [
                'slug' => 'intelijen-dasar', 'name' => 'Intelijen Dasar', 
                'desc' => 'Wawasan dunia intelijen.', 'icon' => 'GiBrain'
            ],
            'elektronika.json' => [
                'slug' => 'elektronika-dasar', 'name' => 'Elektronika Dasar', 
                'desc' => 'Komponen dan sirkuit elektronika.', 'icon' => 'FiCpu'
            ],
            'jaringan.json' => [
                'slug' => 'pemrograman-jaringan', 'name' => 'Pemrograman Jaringan', 
                'desc' => 'Konsep Socket, HTTP, dan Flask Python.', 'icon' => 'FiGlobe'
            ],
        ];

        foreach ($files as $filename => $info) {
            $path = storage_path("app/json/{$filename}");

            if (!File::exists($path)) {
                $this->command->warn("File tidak ditemukan: {$filename}. Lewati...");
                continue;
            }

            // Buat Kategori
            $cat = Category::create([
                'slug' => $info['slug'],
                'name' => $info['name'],
                'description' => $info['desc'],
                'icon_class' => $info['icon']
            ]);

            // Baca & Decode JSON
            $questions = json_decode(File::get($path), true);

            foreach ($questions as $q) {
                // Tentukan tipe soal, default ke 'single' jika tidak ada
                $type = $q['type'] ?? 'single';

                // Masukkan Soal (Sekarang support explanation dan type)
                $quest = Question::create([
                    'category_id' => $cat->id,
                    'question_text' => $q['question'],
                    'explanation' => $q['explanation'] ?? null,
                    'type' => $type
                ]);

                // Logika Insert Opsi Berdasarkan Tipe
                if ($type === 'matching') {
                    // Tipe Matching: Punya pasangan (pairs)
                    foreach ($q['pairs'] as $pair) {
                        Option::create([
                            'question_id' => $quest->id,
                            'option_text' => $pair['left'],      // Kiri (Soal)
                            'matching_pair' => $pair['right'],   // Kanan (Jawaban Pasangan)
                            'is_correct' => true
                        ]);
                    }
                } 
                elseif ($type === 'ordering') {
                    // Tipe Ordering: Menggunakan items dan urutan yang benar
                    // Kita simpan itemsnya, correct_order diset 1, 2, 3...
                    $items = $q['correctOrder'] ?? $q['items']; 
                    foreach ($items as $index => $itemText) {
                        Option::create([
                            'question_id' => $quest->id,
                            'option_text' => $itemText,
                            'correct_order' => $index + 1, // Urutan 1, 2, 3, dst
                            'is_correct' => true
                        ]);
                    }
                } 
                else {
                    // Tipe Single & Multiple
                    foreach ($q['options'] as $idx => $optText) {
                        $isCorrect = false;

                        if ($type === 'multiple') {
                            // Cek jika index ada di array jawaban benar (contoh: [0, 2])
                            $isCorrect = in_array($idx, $q['correct']);
                        } else {
                            // Single choice (contoh: 2)
                            $isCorrect = ($idx == $q['correct']);
                        }

                        Option::create([
                            'question_id' => $quest->id,
                            'option_text' => $optText,
                            'is_correct' => $isCorrect
                        ]);
                    }
                }
            }
            $this->command->info("Sukses import: {$info['name']}");
        }
    }
}