<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('json.response')->prefix('/v1')->group(function() {
    Route::prefix('/auth')->group(function() {
        Route::post('/signup', [AuthController::class, 'signup']);
        Route::post('/signin', [AuthController::class, 'signin']);
        Route::post('/signout', [AuthController::class, 'signout']);
    });

    Route::apiResource('games', GamesController::class)->only(['index', 'show']);
    Route::apiResource('games.scores', ScoreController::class);
    Route::middleware('auth:sanctum')->group(function() {
        Route::apiResource('games', GamesController::class)->only(['store', 'destroy']);
        Route::apiResource('users', UsersController::class);
    });
});
