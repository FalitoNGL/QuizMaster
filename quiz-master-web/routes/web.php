<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LiveGameController;
use App\Http\Controllers\ProfileController;

// --- AUTHENTICATION ROUTES ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- MAIN APP ROUTES ---
Route::get('/', [QuizController::class, 'index'])->name('menu');
Route::get('/quiz/{slug}', [QuizController::class, 'show'])->name('quiz.play');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
Route::get('/review/{id}', [QuizController::class, 'review'])->name('quiz.review');

// Fitur Tambahan
Route::get('/leaderboard', [QuizController::class, 'leaderboard'])->name('quiz.leaderboard');
Route::get('/stats', [StatsController::class, 'index'])->name('stats');
Route::post('/stats/login', [StatsController::class, 'login'])->name('stats.login');
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements');

// Pengaturan
Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::post('/settings/reset', [SettingsController::class, 'resetHistory'])->name('settings.reset');
Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.update-profile');

// --- SOCIAL & PROFILE ROUTES ---
// Halaman Utama Sosial (Hub) - Wajib Login
Route::get('/social', [ProfileController::class, 'index'])->name('social.index')->middleware('auth');

// Profil Publik (Bisa diakses tamu, tapi terbatas)
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');

// Aksi Sosial (Butuh Login)
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit/me', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/follow/{id}', [ProfileController::class, 'follow'])->name('profile.follow');
});

// --- LIVE DUEL ROUTES ---
Route::middleware('auth')->group(function () {
    // Lobby & Room Management
    Route::get('/live', [LiveGameController::class, 'index'])->name('live.lobby');
    Route::post('/live/create', [LiveGameController::class, 'createRoom'])->name('live.create');
    Route::post('/live/join', [LiveGameController::class, 'joinRoom'])->name('live.join');
    
    // Gameplay
    Route::get('/live/{roomCode}', [LiveGameController::class, 'play'])->name('live.play');
    Route::post('/live/score', [LiveGameController::class, 'updateScore'])->name('live.score');
    Route::post('/live/finish', [LiveGameController::class, 'finishGame'])->name('live.finish'); // PENTING: Untuk mengakhiri game
    
    // Challenge System (Direct Duel)
    Route::post('/live/challenge/send', [LiveGameController::class, 'sendChallenge'])->name('live.challenge.send');
    Route::get('/live/challenge/accept/{id}', [LiveGameController::class, 'acceptChallenge'])->name('live.challenge.accept');
    Route::get('/live/challenge/reject/{id}', [LiveGameController::class, 'rejectChallenge'])->name('live.challenge.reject');
});

// --- ADMIN ROUTES ---
Route::prefix('admin')->group(function () {
    // Auth Admin
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::get('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Dashboard & CRUD Soal
    // (Akses diamankan via logic di controller)
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/create', [AdminController::class, 'create'])->name('admin.create');
    Route::post('/store', [AdminController::class, 'store'])->name('admin.store');
    Route::get('/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
    Route::post('/update/{id}', [AdminController::class, 'update'])->name('admin.update');
    Route::get('/delete/{id}', [AdminController::class, 'destroy'])->name('admin.delete');
    Route::get('/cleanup', [AdminController::class, 'cleanup'])->name('admin.cleanup');
    // ... rute admin lainnya ...

    // Import JSON
    Route::get('/import', [AdminController::class, 'import'])->name('admin.import');
    Route::post('/import', [AdminController::class, 'processImport'])->name('admin.import.process');

    // Manajemen Kategori
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/edit/{id}', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::post('/categories/update/{id}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::get('/categories/delete/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
});