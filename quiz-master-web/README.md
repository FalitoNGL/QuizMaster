<p align="left">
  <img src="https://img.shields.io/badge/Laravel_11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/PHP_8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Tailwind-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind" />
  <img src="https://img.shields.io/badge/WebSocket-010101?style=for-the-badge&logo=socket.io&logoColor=white" alt="WebSocket" />
</p>

# QuizMaster Web (Backend & Admin)

Direktori ini memuat implementasi *Core System* platform QuizMaster. Sistem ini berfungsi sebagai *API Provider* pusat dan pusat otorisasi data untuk integrasi lintas platform.

## 🛡️ Keamanan & Integritas: Server-Side Scoring

Dalam sistem kuis interaktif, pengerjaan evaluasi sangat rentan terhadap manipulasi sisi klien (*client-side manipulation*). QuizMaster memitigasi risiko ini melalui mekanisme **Server-Side Scoring**. 

### Mengapa Client-Side Scoring Berbahaya?
- Klien dapat dengan mudah memodifikasi variabel skor secara lokal sebelum dikirim ke server.
- Jawaban benar yang disimpan di memori klien dapat diekstrak atau diubah melalui *debugging tools*.

### Implementasi Teknis (Snippet)
Server memproses setiap jawaban secara atomik dan melakukan rekalkulasi poin berdasarkan ID soal yang valid dalam sesi aktif.

```php
// Contoh Logika Validasi Skor di ApiController.php
foreach ($data['answers'] as $ans) {
    $question = Question::with('options')->find($ans['question_id']);
    $isCorrect = false;
    
    if ($question->type === 'single') {
        $opt = $question->options->where('id', (int)$ans['answer'])->first();
        $isCorrect = $opt && $opt->is_correct;
    }

    if ($isCorrect) {
        // Bonus poin berdasarkan sisa waktu (Server-calculated)
        $calculatedScore += 100 + min(max((int)$ans['time_left'], 0), 300);
        $correctCount++;
    }
}
```

---

## 📑 Dokumentasi REST API

Semua interaksi menggunakan JSON *payload* dan membutuhkan header `Authorization: Bearer <token>` melalui Laravel Sanctum.

| Method | Endpoint | Request Payload (Example) | Deskripsi |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | `{"email": "...", "password": "..."}` | Memperoleh access token user. |
| `GET` | `/api/categories` | `-` | List kategori beserta metadata kuis. |
| `GET` | `/api/quiz/{id}` | `{"limit": 10}` | Fetch soal acak berdasarkan kategori. |
| `POST` | `/api/submit` | `{"category_id": 1, "answers": [...]}` | Submit jawaban untuk sinkronisasi nilai. |
| `GET` | `/api/leaderboard` | `{"limit": 20}` | Mengambil data peringkat global. |
| `GET` | `/api/stats` | `-` | Statistik personal (XP, Level, Akurasi). |
| `POST` | `/api/profile` | `{"name": "...", "bio": "..."}` | Update metadata profil pengguna. |

---

## 🏗️ Fitur Bulk Import (JSON Format)

Untuk memudahkan penambahan bank soal skala besar, sistem menyediakan utilitas *import*. Format JSON yang diharapkan:

```json
[
  {
    "question": "Apa ibukota Indonesia?",
    "type": "single",
    "options": [
      {"text": "Jakarta", "is_correct": true},
      {"text": "Bandung", "is_correct": false}
    ]
  }
]
```

---

## ⚙️ Local Setup
1.  **Dependencies**: `composer install` & `npm install`.
2.  **Environment**: Konfigurasi file `.env` dan jalankan `php artisan key:generate`.
3.  **Migration**: `php artisan migrate --seed` (Inisialisasi database awal).
4.  **Running**: 
    - Server: `php artisan serve`
    - WebSocket: `php artisan reverb:start`

---

## 📸 Antarmuka Administratif & Web (Gallery)

### 🛠️ Admin Command Center
| Login Admin| Dashboard Utama | Manajemen Kategori |
| :---: | :---: | :---: |
| <img src="docs/screenshots/web_01_login.jpeg" width="250"> | <img src="docs/screenshots/web_02_dashboard_admin.jpeg" width="250"> | <img src="docs/screenshots/web_03_manage_category.jpeg" width="250"> |

| Bank Soal | Bulk Import Tool |
| :---: | :---: |
| <img src="docs/screenshots/web_04_manage_question.jpeg" width="250"> | <img src="docs/screenshots/web_05_import_json.jpeg" width="250"> |

### 🎮 Web Client & Gameplay
| Login | Menu Utama | Profil Publik | Statistik User |
| :---: | :---: | :---: | :---: |
| <img src="docs/screenshots/web_login.jpeg" width="250"> | <img src="docs/screenshots/02_menu.jpeg" width="250"> | <img src="docs/screenshots/web_10_public_profile.jpeg" width="250"> | <img src="docs/screenshots/web_11_stats.jpeg" width="250"> |

| Arena Kuis | Feedback Jawaban | Summary Hasil |
| :---: | :---: | :---: |
| <img src="docs/screenshots/web_12_quiz_play.jpeg" width="250"> | <img src="docs/screenshots/web_13_quiz_correct.jpeg" width="250"> | <img src="docs/screenshots/web_15_quiz_result.jpeg" width="250"> |

### 🏆 Social & Real-Time Sync
| Global Leaderboard | Achievement Wall | Lobby Arena |
| :---: | :---: | :---: |
| <img src="docs/screenshots/web_08_leaderboard.jpeg" width="250"> | <img src="docs/screenshots/web_09_achievements.jpeg" width="250"> | <img src="docs/screenshots/web_06_live_lobby.jpeg" width="250"> |

| Real-Time Duel | Review Pengerjaan | Feedback (Salah) |
| :---: | :---: | :---: |
| <img src="docs/screenshots/web_07_live_duel.jpeg" width="250"> | <img src="docs/screenshots/web_16_quiz_review.jpeg" width="250"> | <img src="docs/screenshots/web_14_quiz_incorrect.jpeg" width="250"> |