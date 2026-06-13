<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SavedPlaceController;
use App\Http\Controllers\Api\GameSessionController;
use App\Http\Controllers\Api\LeaderboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| BGame API Routes
|--------------------------------------------------------------------------
*/

// ── Public Routes ────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',    [AuthController::class, 'login']);
});

// Public Leaderboard
Route::get('leaderboard', [LeaderboardController::class, 'index']);

// ── Protected Routes (Sanctum) ───────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me',      [AuthController::class, 'me']);

    // Saved Places (max 3 per user)
    Route::apiResource('places', SavedPlaceController::class)
         ->except(['show']);

    // Game Sessions
    Route::post('sessions/start',      [GameSessionController::class, 'start']);
    Route::post('sessions/{gameSession}/end', [GameSessionController::class, 'end']);

    // My Rank
    Route::get('leaderboard/my-rank',  [LeaderboardController::class, 'myRank']);
});
