<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\TeamController;

Route::get('/tournaments', [TournamentController::class, 'index']);
Route::get('/tournaments/{id}', [TournamentController::class, 'show']);
Route::get('/tournaments/{id}/teams', [TournamentController::class, 'teams']);

Route::get('/teams/{id}/players', [TeamController::class, 'players']);

Route::post('/tournaments', [TournamentController::class, 'store']);
Route::put('/tournaments/{id}', [TournamentController::class, 'update']);
Route::delete('/tournaments/{id}', [TournamentController::class, 'destroy']);

