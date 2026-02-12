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
            'etos-sandi-iii.json' => [
                'slug' => 'etos-sandi-iii', 'name' => 'Etos Sandi III', 
                'desc' => 'Wawasan kebangsaan dan etika profesi sandi.', 'icon' => 'FiShield'
            ],
            'kriptografi.json' => [
                'slug' => 'kriptografi-terapan', 'name' => 'Kriptografi Terapan', 
                'desc' => 'Algoritma enkripsi modern (AES, RSA, ECC).', 'icon' => 'FiLock'
            ],
            'sistem-telekomunikasi.json' => [
                'slug' => 'sistem-telekomunikasi', 'name' => 'Sistem Telekomunikasi', 
                'desc' => 'Konsep satelit, seluler, fiber optik, dan OSI Layer.', 'icon' => 'FiRadio'
            ],
            'pemrograman-lanjutan.json' => [
                'slug' => 'pemrograman-lanjutan', 'name' => 'Pemrograman Lanjutan', 
                'desc' => 'SDLC, OOP, dan Arsitektur Web.', 'icon' => 'FiCode'
            ],
            'sistem-operasi-virtualisasi.json' => [
                'slug' => 'sistem-operasi-virtualisasi', 'name' => 'Sistem Operasi & Virtualisasi', 
                'desc' => 'Manajemen proses, memori, virtualisasi, dan kontainerisasi.', 'icon' => 'FiServer'
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

            if (!$questions) {
                $this->command->error("Gagal decode JSON: {$filename}");
                continue;
            }

            foreach ($questions as $q) {
                // --- VALIDASI: Lewati jika bukan soal (misal: header section) ---
                if (!isset($q['question'])) {
                    continue;
                }

                // Tentukan tipe soal, default ke 'single' jika tidak ada
                $type = $q['type'] ?? 'single';

                // Masukkan Soal
                $quest = Question::create([
                    'category_id' => $cat->id,
                    'question_text' => $q['question'],
                    'explanation' => $q['explanation'] ?? null,
                    'reference' => $q['reference'] ?? null,
                    'type' => $type
                ]);

                // Logika Insert Opsi Berdasarkan Tipe
                if ($type === 'matching') {
                    // Tipe Matching
                    if(isset($q['pairs'])) {
                        foreach ($q['pairs'] as $pair) {
                            Option::create([
                                'question_id' => $quest->id,
                                'option_text' => $pair['left'],      
                                'matching_pair' => $pair['right'],   
                                'is_correct' => true
                            ]);
                        }
                    }
                } 
                elseif ($type === 'ordering') {
                    // Tipe Ordering
                    $items = $q['correctOrder'] ?? ($q['items'] ?? []); 
                    foreach ($items as $index => $itemText) {
                        Option::create([
                            'question_id' => $quest->id,
                            'option_text' => $itemText,
                            'correct_order' => $index + 1, 
                            'is_correct' => true
                        ]);
                    }
                } 
                else {
                    // Tipe Single & Multiple (termasuk True/False yang kini jadi Single)
                    if(isset($q['options'])) {
                        foreach ($q['options'] as $idx => $optText) {
                            $isCorrect = false;

                            if ($type === 'multiple') {
                                $isCorrect = in_array($idx, $q['correct']);
                            } else {
                                // Single choice
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
            }
            $this->command->info("Sukses import: {$info['name']}");
        }
    }
}