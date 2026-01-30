# 🚀 QuizMaster - Real-Time Multiplayer Quiz Platform

![Laravel](https://img.shields.io/badge/Laravel_11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS_4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Google](https://img.shields.io/badge/Google_OAuth-4285F4?style=for-the-badge&logo=google&logoColor=white)
![WebSocket](https://img.shields.io/badge/WebSocket-010101?style=for-the-badge&logo=socket.io&logoColor=white)

## 📌 Project Overview

**QuizMaster** adalah platform kuis interaktif berbasis web yang dirancang untuk kebutuhan evaluasi akademik maupun hiburan. Aplikasi ini mendukung fitur **Real-Time Multiplayer** (Live Duel), sistem **Gamifikasi** (Achievement & Leaderboard), fitur **Sosial** (Follow & Profile), serta manajemen soal yang komprehensif.

Dibangun menggunakan arsitektur **MVC** dengan keamanan tingkat lanjut (Server-Side Scoring) untuk mencegah kecurangan dalam penilaian.

---

## 📑 SRS (Software Requirements Specification)

### 1. Functional Requirements

| Modul | Deskripsi |
|-------|-----------|
| **Autentikasi** | Login via Google OAuth (Laravel Socialite), session management |
| **Manajemen Kuis** | CRUD Kategori, Soal multi-tipe (Single, Multiple, Ordering, Matching), Import JSON |
| **Mode Single Player** | Pengerjaan kuis mandiri dengan timer dan feedback instan |
| **Mode Live Duel** | Kompetisi real-time via WebSocket (Laravel Reverb) dalam Game Room |
| **Challenge System** | Tantangan langsung antar user dengan notifikasi real-time |
| **Sistem Penilaian** | Perhitungan skor 100% di server (Anti-Cheat) dengan bonus waktu |
| **Gamifikasi** | Achievement badges (Newbie, Veteran, Sharpshooter, Speedster) |
| **Fitur Sosial** | Profil publik, sistem follow/following, Social Hub |
| **Statistics** | Riwayat pengerjaan, statistik performa, review jawaban |

### 2. Non-Functional Requirements

- **Security:** Input validation ketat, CSRF Protection, Server-Side Scoring, Participant Verification
- **Performance:** Real-time scoring via WebSocket, optimized database queries
- **Reliability:** Multiple concurrent users support dalam Live Duel
- **Interoperability:** REST API untuk integrasi aplikasi Mobile

---

## 🛠 Feature List

### 👤 User Features

| Fitur | Route | Deskripsi |
|-------|-------|-----------|
| **Menu Kategori** | `/` | Pilih kategori kuis yang tersedia |
| **Play Quiz** | `/quiz/{slug}` | Kerjakan soal dengan timer dan feedback instan |
| **Review Jawaban** | `/review/{id}` | Lihat pembahasan dan jawaban benar |
| **Leaderboard** | `/leaderboard` | Peringkat skor tertinggi global |
| **Achievements** | `/achievements` | Koleksi badges yang telah diraih |
| **Statistics** | `/stats` | Statistik performa pribadi |
| **Settings** | `/settings` | Reset riwayat dan preferensi |

### 🎮 Live Duel Features

| Fitur | Route | Deskripsi |
|-------|-------|-----------|
| **Game Lobby** | `/live` | Buat atau join room untuk Live Duel |
| **Create Room** | `POST /live/create` | Buat room dengan kategori dan durasi custom |
| **Join Room** | `POST /live/join` | Bergabung dengan kode room 5 karakter |
| **Live Gameplay** | `/live/{roomCode}` | Bermain real-time dengan lawan |
| **Send Challenge** | `POST /live/challenge/send` | Tantang user lain secara langsung |
| **Accept/Reject** | `/live/challenge/accept/{id}` | Terima atau tolak tantangan |

### 👥 Social Features

| Fitur | Route | Deskripsi |
|-------|-------|-----------|
| **Social Hub** | `/social` | Pusat aktivitas sosial (wajib login) |
| **Public Profile** | `/profile/{id}` | Lihat profil user lain |
| **Edit Profile** | `/profile/edit/me` | Edit profil pribadi |
| **Follow User** | `POST /follow/{id}` | Follow/unfollow user lain |

### 🛡️ Admin Features

| Fitur | Route | Deskripsi |
|-------|-------|-----------|
| **Dashboard** | `/admin` | Ringkasan statistik dan aktivitas |
| **CRUD Soal** | `/admin/create`, `/admin/edit/{id}` | Tambah, edit, hapus soal |
| **Import JSON** | `/admin/import` | Import soal massal dari file JSON |
| **Manage Categories** | `/admin/categories` | CRUD kategori kuis |
| **Cleanup** | `/admin/cleanup` | Bersihkan data sampah |

---

## 📊 UML Diagrams

### 1. Use Case Diagram
Menggambarkan interaksi antara Aktor (Guest, User, Admin) dengan sistem QuizMaster.

![Use Case Diagram](docs/diagrams/use_case_diagram.png)

### 2. Activity Diagram
Alur kerja sistem dari Login hingga mendapatkan skor, mencakup mode Single Player dan Live Duel.

![Activity Diagram](docs/diagrams/activity_diagram.png)

### 3. Sequence Diagram
Detail pertukaran pesan antar komponen saat proses submit jawaban kuis (Server-Side Scoring).

![Sequence Diagram](docs/diagrams/sequence_diagram.png)

### 4. ERD (Entity Relationship Diagram)
Struktur database relasional dengan 10 tabel yang mendukung fitur gamifikasi dan Live Duel.

![ERD Diagram](docs/diagrams/erd_diagram.png)

---

## 🗄️ Database Schema (ERD)

### Tabel Utama

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   users     │     │  categories │     │  questions  │
├─────────────┤     ├─────────────┤     ├─────────────┤
│ id          │     │ id          │     │ id          │
│ name        │     │ name        │     │ category_id │◄──┐
│ email       │     │ slug        │     │ type        │   │
│ google_id   │     │ description │     │ question_text│  │
│ avatar      │     │ icon_class  │     │ explanation │   │
│ bio         │     └─────────────┘     │ image_path  │   │
│ xp          │            │            │ audio_path  │   │
└─────────────┘            │            └─────────────┘   │
      │                    │                   │          │
      │              ┌─────┴─────┐             │          │
      │              │           │             │          │
      ▼              ▼           ▼             ▼          │
┌─────────────┐ ┌─────────────┐ ┌─────────────┐           │
│  results    │ │  options    │ │result_answers│          │
├─────────────┤ ├─────────────┤ ├─────────────┤           │
│ id          │ │ id          │ │ id          │           │
│ user_id     │ │ question_id │ │ result_id   │           │
│ category_id │─┘ │ option_text │ │ question_id │          │
│ score       │   │ is_correct  │ │ option_id   │          │
│ correct_ans │   │ matching_pair│ │ is_correct │          │
│ total_ques  │   │ correct_order│ └─────────────┘         │
└─────────────┘   └─────────────┘                         │
                                                          │
┌─────────────┐     ┌─────────────┐     ┌─────────────┐   │
│ game_rooms  │     │ challenges  │     │achievements │   │
├─────────────┤     ├─────────────┤     ├─────────────┤   │
│ id          │     │ id          │     │ id          │   │
│ room_code   │◄────│ room_code   │     │ name        │   │
│ category_id │─────┼─────────────┼─────│ slug        │   │
│ host_id     │     │ sender_id   │     │ description │   │
│ challenger_id│    │ target_id   │     │ icon        │   │
│ host_score  │     │ status      │     └─────────────┘   │
│ challenger_ │     │ winner_id   │            │          │
│ status      │     └─────────────┘            │          │
│ duration    │                          ┌─────┴─────┐    │
│ total_ques  │                          │           │    │
└─────────────┘                          ▼           │    │
                               ┌─────────────────┐   │    │
                               │player_achievements│  │   │
                               ├─────────────────┤   │    │
                               │ user_id         │   │    │
                               │ player_name     │   │    │
                               │ achievement_id  │◄──┘    │
                               └─────────────────┘        │
```

### Tipe Soal yang Didukung

| Type | Deskripsi |
|------|-----------|
| `single` | Pilihan ganda (1 jawaban benar) |
| `multiple` | Pilihan ganda (>1 jawaban benar) |
| `ordering` | Urutkan opsi sesuai urutan benar |
| `matching` | Pasangkan opsi kiri dengan kanan |

---

## 🔄 SDLC (System Development Life Cycle)

Pengembangan **QuizMaster** mengikuti model **Waterfall**:

```
┌──────────────────┐
│ 1. REQUIREMENT   │ → Analisis kebutuhan Live Game, Anti-Cheat, Gamifikasi
└────────┬─────────┘
         ▼
┌──────────────────┐
│ 2. SYSTEM DESIGN │ → Rancang ERD, UI/UX, API Specification
└────────┬─────────┘
         ▼
┌──────────────────┐
│ 3. IMPLEMENTATION│ → Laravel 11 + Tailwind CSS + MySQL + Reverb
└────────┬─────────┘
         ▼
┌──────────────────┐
│ 4. TESTING       │ → PHPUnit (16 tests, 48 assertions) + Black Box
└────────┬─────────┘
         ▼
┌──────────────────┐
│ 5. DEPLOYMENT    │ → Server configuration, SSL, optimization
└────────┬─────────┘
         ▼
┌──────────────────┐
│ 6. MAINTENANCE   │ → Bug fixes, feature updates, monitoring
└──────────────────┘
```

---

## 💻 Tech Stack

### Backend
| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.2+ | Server-side language |
| Laravel | 11.x | MVC Framework |
| MySQL | 8.0 | Relational Database |
| Laravel Reverb | 1.x | WebSocket for Real-time |
| Laravel Socialite | 5.x | Google OAuth |
| Maatwebsite Excel | 3.x | Excel Export/Import |

### Frontend
| Technology | Version | Purpose |
|------------|---------|---------|
| Blade Templates | - | Server-side rendering |
| Tailwind CSS | 4.x | Utility-first CSS |
| Vite | 7.x | Build tool |
| Alpine.js | - | Lightweight reactivity |
| Pusher JS | 8.x | WebSocket client |
| Laravel Echo | 2.x | Real-time events |

---

## 🧪 Testing

### Menjalankan Test

```bash
php artisan test
```

### Test Coverage (16 Tests, 48 Assertions)

| Category | Tests |
|----------|-------|
| **User Flow** | Login → Menu → Quiz → Score |
| **API Endpoints** | `/api/categories`, `/api/quiz/{id}`, `/api/leaderboard`, `/api/achievements` |
| **Security** | Auth required for Live, Social, Admin routes |
| **Input Validation** | Invalid data rejection, parameter bounds |

---

## 📡 REST API Endpoints

Base URL: `http://localhost:8000/api`

| Method | Endpoint | Params | Response |
|--------|----------|--------|----------|
| GET | `/categories` | - | Semua kategori + questions_count |
| GET | `/quiz/{id}` | `?limit=10` | Soal acak (jawaban disembunyikan) |
| GET | `/leaderboard` | `?limit=20` | Top skor |
| GET | `/achievements` | `?user_id=X` | Achievement user |

### Contoh Response

```json
// GET /api/categories
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Jaringan Komputer",
      "slug": "jaringan-komputer",
      "questions_count": 50
    }
  ]
}
```

---

## 🚀 Installation

### Requirements
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0
- Git

### Step-by-Step

```bash
# 1. Clone repository
git clone https://github.com/FalitoNGL/QuizMaster.git
cd QuizMaster/quiz-master-backend

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies & build
npm install
npm run build

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
# DB_DATABASE=quizmaster
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Run migrations & seeders
php artisan migrate:fresh --seed

# 7. Start development server
php artisan serve

# 8. (Optional) Start all services
composer dev
```

### Development Mode (All Services)

```bash
composer dev
# Runs: Laravel Server + Queue + Logs + Vite concurrently
```

---

## 🔐 Security Features

| Feature | Implementation |
|---------|----------------|
| **Server-Side Scoring** | Skor dihitung 100% di server, client hanya kirim jawaban |
| **Input Validation** | Semua input divalidasi dengan rules ketat (tipe, range, regex) |
| **Anti Mass Assignment** | Explicit column assignment, tidak pakai `$request->all()` |
| **Participant Verification** | Verifikasi user adalah peserta sebelum aksi game |
| **CSRF Protection** | Token CSRF di semua form POST |
| **Route Protection** | Middleware `auth` untuk fitur yang memerlukan login |

---

## 📁 Project Structure

```
quiz-master-backend/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/
│   │   │   └── ApiController.php      # REST API
│   │   ├── AdminController.php        # Admin CRUD
│   │   ├── AuthController.php         # Google OAuth
│   │   ├── LiveGameController.php     # Live Duel
│   │   ├── ProfileController.php      # Social Features
│   │   ├── QuizController.php         # Quiz Logic
│   │   └── ...
│   ├── Events/
│   │   ├── GameUpdated.php            # WebSocket event
│   │   └── NewChallengeReceived.php
│   └── Models/
│       ├── Category.php
│       ├── Question.php
│       ├── Option.php
│       ├── Result.php
│       ├── GameRoom.php
│       ├── Challenge.php
│       ├── Achievement.php
│       └── User.php
├── database/
│   ├── migrations/                    # Schema definitions
│   └── seeders/                       # Sample data
├── routes/
│   ├── web.php                        # Web routes
│   └── api.php                        # API routes
├── tests/
│   └── Feature/
│       └── ComplexQuizTest.php        # 14 test cases
└── resources/
    └── views/                         # Blade templates
```

---

## 📸 Screenshots

> Tambahkan screenshot ke folder `docs/screenshots/`

| Screen | Path |
|--------|------|
| Login | `docs/screenshots/01_login.png` |
| Menu | `docs/screenshots/02_menu.png` |
| Quiz | `docs/screenshots/03_quiz.png` |
| Live Lobby | `docs/screenshots/04_live_lobby.png` |
| Leaderboard | `docs/screenshots/05_leaderboard.png` |

---

## 📄 License

MIT License - Free to use and modify.

---

<p align="center">
Created with ❤️ by <b>FalitoNGL</b> for Final Project<br>
Laravel 11 • Tailwind CSS 4 • MySQL • WebSocket
</p>
