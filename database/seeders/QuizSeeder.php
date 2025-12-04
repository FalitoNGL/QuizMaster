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
        Category::truncate(); Question::truncate(); Option::truncate();
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
        ];

        foreach ($files as $filename => $info) {
            // Cek apakah file ada di storage/app/json/
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
                // Masukkan Soal
                $quest = Question::create([
                    'category_id' => $cat->id,
                    'question_text' => $q['question']
                ]);

                // Masukkan Opsi
                foreach ($q['options'] as $idx => $optText) {
                    Option::create([
                        'question_id' => $quest->id,
                        'option_text' => $optText,
                        'is_correct' => ($idx == $q['correct'])
                    ]);
                }
            }
            $this->command->info("Sukses import: {$info['name']}");
        }
    }
}