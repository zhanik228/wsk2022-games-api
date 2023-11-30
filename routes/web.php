<?php

use App\Http\Controllers\GamesController;
use App\Http\Controllers\PathController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::post('api/v1/games/{game}/upload',
    [GamesController::class, 'upload']);
});

Route::get('games/{game}/{version}/{path}',
[PathController::class, 'index']);

Route::get('games/{game}',
[PathController::class, 'getHtml']);
