<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PlayerController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    // CRUDs
    Route::apiResource('tournaments', TournamentController::class);
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('players', PlayerController::class);

    // Aquí podrías aplicar roles cuando tengas el middleware
    // Route::middleware('role:admin')->group(...);
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin-area', fn() => 'Bienvenido, Admin');
    });
    
    Route::middleware(['auth:sanctum', 'role:admin,gestor'])->group(function () {
        Route::apiResource('tournaments', TournamentController::class);
    });
});
