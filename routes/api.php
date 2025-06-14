<?php

use App\Http\Controllers\Api\MatchGameController;
use App\Http\Controllers\Api\PDFController;
use App\Http\Controllers\Api\PlayerMatchGameController;
use App\Http\Controllers\Api\TournamentInvitationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\PlayerController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/tournaments/public', [TournamentController::class, 'publicTournaments']);
Route::get('/tournaments/{tournament}/teams', [TeamController::class, 'getByTournament']);
Route::get('users/{user}', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->post('/tournaments/{tournament}/teams', [TeamController::class, 'storeForTournament']);
Route::get('/teams/{team}/players', [PlayerController::class, 'getPlayersByTeam']);
Route::get('/teams/{team}', [TeamController::class, 'show']);
Route::get('/players/{player}', [PlayerController::class, 'show']);
Route::get('/tournaments/{tournament}/match_games', [MatchGameController::class, 'getByTournament']);

Route::get('/match_games/{match}', [MatchGameController::class, 'show']);

Route::get('/partidos/{match}/comentarios', [CommentController::class, 'index']);

Route::get('/tournaments/{tournament}/clasificacion', [TournamentController::class, 'clasificacion']);
Route::get('/tournaments/{tournament}/ranking', [TournamentController::class, 'rankingEstadisticas']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tournaments/{tournament}/invite', [TournamentInvitationController::class, 'invite']);
    Route::delete('/tournaments/{tournament}/invite/{user}', [TournamentInvitationController::class, 'removeInvite']);
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::get('/tournaments/private', [TournamentController::class, 'privateTournaments']);
    Route::get('/tournaments/user/{id}', [TournamentController::class, 'tournamentsByUser']);

    Route::apiResource('tournaments', TournamentController::class)->except(['show']);
    Route::apiResource('teams', TeamController::class)->except(['show']);
    Route::apiResource('players', PlayerController::class)->except(['show']);
    Route::apiResource('match_games', MatchGameController::class)->except(['show']);
    Route::apiResource('users', UserController::class)->except(['store', 'show']);

    Route::post('/partidos/{match}/comentarios', [CommentController::class, 'store']);
    Route::delete('/comentarios/{comment}', [CommentController::class, 'destroy']);

    Route::get('/tournaments/{tournament}/invited-users', [TournamentController::class, 'invitedUsers']);

    Route::post('/teams/{team}/players', [PlayerController::class, 'storeForTeam']);

    Route::post('/match_games/{match}/stats', [PlayerMatchGameController::class, 'store']);
    Route::get('/match_games/{match}/stats', [PlayerMatchGameController::class, 'show']);

    Route::get('/tournaments/invited', [TournamentController::class, 'invitedTournaments']);

    Route::delete('/tournaments/{tournament}/invitations/leave', [TournamentInvitationController::class, 'leave']);
    Route::delete('/tournaments/{tournament}/invitations/{user}', [TournamentInvitationController::class, 'removeUser']);

    Route::post('/tournaments/{id}/generar-partidos', [TournamentController::class, 'generarPartidos']);

    Route::delete('/tournaments/{tournament}/abandonar', [TournamentController::class, 'abandonarTorneo']);
    Route::delete('/tournaments/{tournament}/expulsar/{user}', [TournamentController::class, 'expulsarInvitado']);

    Route::get('/tournaments/{tournament}/descargar-partidos', [PDFController::class, 'descargarPartidos']);

    // Route::middleware('role:admin')->group(...);
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/admin-area', fn() => 'Bienvenido, Admin');
    });

    /*Route::middleware(['auth:sanctum', 'role:admin,gestor'])->group(function () {
        Route::apiResource('tournaments', TournamentController::class);
    });*/
});

Route::get('tournaments/{tournament}', [TournamentController::class, 'show']);
