<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Achievement;
use Illuminate\Support\Facades\DB;

class AchievementSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama biar tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Achievement::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $badges = [
            [
                'slug' => 'newbie',
                'name' => 'Pendatang Baru',
                'description' => 'Menyelesaikan permainan pertama Anda.',
                'icon_class' => 'fas fa-baby',
                'color_class' => 'text-blue-400'
            ],
            [
                'slug' => 'sharpshooter',
                'name' => 'Penembak Jitu',
                'description' => 'Menjawab semua soal dengan benar (100%).',
                'icon_class' => 'fas fa-bullseye',
                'color_class' => 'text-red-500'
            ],
            [
                'slug' => 'veteran',
                'name' => 'Veteran Kuis',
                'description' => 'Telah memainkan lebih dari 5 permainan.',
                'icon_class' => 'fas fa-medal',
                'color_class' => 'text-yellow-500'
            ],
            [
                'slug' => 'speedster',
                'name' => 'Si Kilat',
                'description' => 'Mendapatkan skor di atas 1000 poin.',
                'icon_class' => 'fas fa-bolt',
                'color_class' => 'text-yellow-300'
            ]
        ];

        foreach ($badges as $badge) {
            Achievement::create($badge);
        }
    }
}