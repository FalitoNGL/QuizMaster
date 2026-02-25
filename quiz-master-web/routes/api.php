<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint untuk integrasi Mobile/External
| Base URL: /api
|
*/

// Public Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Require Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/stats', [ApiController::class, 'stats']);
    Route::post('/user/profile', [ApiController::class, 'updateProfile']);
});

// Public Quiz & Data Routes
Route::get('/categories', [ApiController::class, 'categories']);
Route::get('/quiz/{id}', [ApiController::class, 'quiz']);
Route::post('/quiz/submit', [ApiController::class, 'submit']);
Route::get('/leaderboard', [ApiController::class, 'leaderboard']);
Route::get('/achievements', [ApiController::class, 'achievements']);
