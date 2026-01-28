<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Endpoint untuk integrasi Mobile/External
| Base URL: /api
|
*/

Route::get('/categories', [ApiController::class, 'categories']);
Route::get('/quiz/{id}', [ApiController::class, 'quiz']);
Route::get('/leaderboard', [ApiController::class, 'leaderboard']);
Route::get('/achievements', [ApiController::class, 'achievements']);
