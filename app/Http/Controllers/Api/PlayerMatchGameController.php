<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchGame;
use App\Models\Player;

class PlayerMatchGameController extends Controller
{
    // Añadir estadísticas a un jugador en un partido
    public function store(Request $request, MatchGame $match)
    {
        $request->validate([
            'stats' => 'required|array',
            'stats.*.player_id' => 'required|exists:players,id',
            'stats.*.goles' => 'nullable|integer|min:0',
            'stats.*.asistencias' => 'nullable|integer|min:0',
            'stats.*.amarillas' => 'nullable|integer|min:0|max:2',
            'stats.*.rojas' => 'nullable|integer|min:0|max:1',
            'stats.*.partidos_jugados' => 'nullable|integer|min:0',
        ]);

        foreach ($request->stats as $stat) {
            $player = Player::findOrFail($stat['player_id']);

            $existingStats = $match->players()->where('players.id', $player->id)->first();

            // Si ya tenía estadísticas previas, restarlas del acumulado
            if ($existingStats) {
                $pivot = $existingStats->pivot;

                $player->goles -= $pivot->goles ?? 0;
                $player->asistencias -= $pivot->asistencias ?? 0;
                $player->amarillas -= $pivot->amarillas ?? 0;
                $player->rojas -= $pivot->rojas ?? 0;
                $player->partidos_jugados -= $pivot->partidos_jugados ?? 0;
            }

            // Sumar nuevas estadísticas (se puede usar 0 por defecto)
            $player->goles += $stat['goles'] ?? 0;
            $player->asistencias += $stat['asistencias'] ?? 0;
            $player->amarillas += $stat['amarillas'] ?? 0;
            $player->rojas += $stat['rojas'] ?? 0;
            $player->partidos_jugados += $stat['partidos_jugados'] ?? 0;
            $player->save();

            // Actualizar en tabla intermedia
            $match->players()->syncWithoutDetaching([
                $player->id => [
                    'goles' => $stat['goles'] ?? 0,
                    'asistencias' => $stat['asistencias'] ?? 0,
                    'amarillas' => $stat['amarillas'] ?? 0,
                    'rojas' => $stat['rojas'] ?? 0,
                    'partidos_jugados' => $stat['partidos_jugados'] ?? 0,
                ]
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Estadísticas actualizadas correctamente.']);
    }



    // Obtener estadísticas de un partido
    public function show(MatchGame $match)
    {
        $stats = $match->players()->withPivot(['goles', 'asistencias', 'amarillas', 'rojas', 'partidos_jugados'])->get();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
