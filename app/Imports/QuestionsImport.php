<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    protected $categoryId;

    public function __construct($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Validasi: Lewati jika pertanyaan kosong
            if (!isset($row['question'])) continue;

            // 1. Simpan Soal (Default ke 'single' jika dari Excel sederhana)
            $q = Question::create([
                'category_id' => $this->categoryId,
                'type' => 'single',
                'question_text' => $row['question'],
                'explanation' => $row['explanation'] ?? null,
                'reference' => $row['reference'] ?? null,
            ]);

            // 2. Simpan Opsi Jawaban (Format Kolom Excel: option_a, option_b, correct)
            // Kunci jawaban di Excel diharapkan berupa huruf 'A', 'B', 'C', atau 'D'
            $correctKey = isset($row['correct']) ? strtoupper($row['correct']) : ''; 

            $options = [
                'A' => $row['option_a'] ?? null,
                'B' => $row['option_b'] ?? null,
                'C' => $row['option_c'] ?? null,
                'D' => $row['option_d'] ?? null,
            ];

            foreach ($options as $key => $optText) {
                if ($optText) {
                    Option::create([
                        'question_id' => $q->id,
                        'option_text' => $optText,
                        'is_correct' => ($key === $correctKey)
                    ]);
                }
            }
        }
    }
}