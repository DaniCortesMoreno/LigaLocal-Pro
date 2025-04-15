<?php

namespace App\Http\Controllers\Api;

use App\Models\MatchGame;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
class TournamentController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        return Tournament::with('user')->get();
    }

    public function show(Tournament $tournament)
    {
        $tournament->load('invitedUsers:id,nombre,apellidos,email', 'user:id,nombre,apellidos');

        if ($tournament->visibilidad === 'publico') {
            return response()->json([
                'success' => true,
                'data' => $tournament
            ]);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        if (
            $tournament->user_id === $user->id ||
            $tournament->invitedUsers()->where('user_id', $user->id)->exists()
        ) {
            return response()->json([
                'success' => true,
                'data' => $tournament
            ]);
        }

        $this->authorize('view', $tournament);

        return response()->json([
            'success' => true,
            'data' => $tournament
        ]);
    }





    public function teams($id)
    {
        $tournament = Tournament::with('teams')->findOrFail($id);
        return $tournament->teams;
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string',
            'tipo' => 'required|in:sala,futbol7,futbol11',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'cantidad_equipos' => 'nullable|integer|min:2',
            'cantidad_jugadores' => 'nullable|integer|min:1',
            'estado' => 'in:pendiente,en_curso,finalizado',
            'formato' => 'required|in:liguilla,eliminacion,grupos_playoffs',
            'reglamento' => 'nullable|string',
            'visibilidad' => 'required|in:publico,privado',
            'user_id' => 'required|exists:users,id',
        ]);

        return Tournament::create($validated);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string',
            'tipo' => 'sometimes|required|in:sala,futbol7,futbol11',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes||date|after_or_equal:fecha_inicio',
            'cantidad_equipos' => 'sometimes||integer|min:2',
            'cantidad_jugadores' => 'sometimes||integer|min:1',
            'estado' => 'sometimes||in:pendiente,en_curso,finalizado',
            'formato' => 'sometimes|required|in:liguilla,eliminacion,grupos_playoffs',
            'visibilidad' => 'required|in:publico,privado',
            'reglamento' => 'nullable|string',
        ]);

        $tournament->update($validated);

        return $tournament;
    }

    public function destroy($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->delete();

        return response()->json(['message' => 'Torneo eliminado'], 200);
    }

    public function publicTournaments()
    {
        $tournaments = Tournament::where('visibilidad', 'publico')->get();
        return response()->json(['success' => true, 'data' => $tournaments]);
    }

    public function privateTournaments()
    {
        $user = Auth::user();

        $tournaments = Tournament::where('visibilidad', 'privado')
            //->where('user_id', $user->id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tournaments
        ]);
    }

    public function tournamentsByUser($id)
    {
        $tournaments = Tournament::where('user_id', $id)->get();

        if ($tournaments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario no ha creado ningún torneo.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tournaments
        ]);
    }

    public function invitedTournaments(Request $request)
    {
        $user = $request->user();

        $tournaments = $user->invitedTournaments()
            ->with(['user', 'teams', 'matches']) // Creador, equipos y partidos
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tournaments
        ]);
    }

    public function invitedUsers(Tournament $tournament)
    {
        $user = auth('sanctum')->user();

        // Verificar que es el creador o un invitado
        if (
            !$user ||
            ($user->id !== $tournament->user_id &&
                !$tournament->invitedUsers()->where('user_id', $user->id)->exists())
        ) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver los invitados de este torneo.'
            ], 403);
        }

        $invitados = $tournament->invitedUsers()->select('users.id', 'users.nombre', 'users.apellidos', 'users.email', 'tournament_user.role')->get();

        return response()->json([
            'success' => true,
            'data' => $invitados
        ]);
    }

    public function clasificacion(Tournament $tournament)
    {
        $matchGames = $tournament->matches()->where('estado_partido', 'finalizado')->get();

        // Cargamos todos los equipos del torneo
        $teams = $tournament->teams()->pluck('nombre', 'id'); // id => nombre

        $stats = [];

        foreach ($matchGames as $match) {
            foreach (['equipo1_id', 'equipo2_id'] as $teamKey) {
                $teamId = $match->$teamKey;
                if (!isset($stats[$teamId])) {
                    $stats[$teamId] = [
                        'equipo_id' => $teamId,
                        'nombre_equipo' => $teams[$teamId] ?? 'Equipo desconocido', // 👈 Aquí añadimos el nombre
                        'jugados' => 0,
                        'ganados' => 0,
                        'empatados' => 0,
                        'perdidos' => 0,
                        'goles_favor' => 0,
                        'goles_contra' => 0,
                        'puntos' => 0,
                    ];
                }
            }

            $stats[$match->equipo1_id]['jugados']++;
            $stats[$match->equipo2_id]['jugados']++;

            $stats[$match->equipo1_id]['goles_favor'] += $match->goles_equipo1;
            $stats[$match->equipo1_id]['goles_contra'] += $match->goles_equipo2;

            $stats[$match->equipo2_id]['goles_favor'] += $match->goles_equipo2;
            $stats[$match->equipo2_id]['goles_contra'] += $match->goles_equipo1;

            if ($match->goles_equipo1 > $match->goles_equipo2) {
                $stats[$match->equipo1_id]['ganados']++;
                $stats[$match->equipo2_id]['perdidos']++;
                $stats[$match->equipo1_id]['puntos'] += 3;
            } elseif ($match->goles_equipo1 < $match->goles_equipo2) {
                $stats[$match->equipo2_id]['ganados']++;
                $stats[$match->equipo1_id]['perdidos']++;
                $stats[$match->equipo2_id]['puntos'] += 3;
            } else {
                $stats[$match->equipo1_id]['empatados']++;
                $stats[$match->equipo2_id]['empatados']++;
                $stats[$match->equipo1_id]['puntos'] += 1;
                $stats[$match->equipo2_id]['puntos'] += 1;
            }
        }

        // Añadir diferencia de goles
        foreach ($stats as &$equipo) {
            $equipo['diferencia_goles'] = $equipo['goles_favor'] - $equipo['goles_contra'];
        }

        // Ordenar por puntos y luego por diferencia de goles
        usort($stats, function ($a, $b) {
            return $b['puntos'] <=> $a['puntos']
                ?: $b['diferencia_goles'] <=> $a['diferencia_goles'];
        });

        return response()->json([
            'success' => true,
            'data' => array_values($stats),
        ]);
    }


    public function rankingEstadisticas(Tournament $tournament)
    {
        // Jugadores de partidos finalizados en este torneo
        $matchIds = $tournament->matches()->where('estado_partido', 'finalizado')->pluck('id');

        // Agrupar estadísticas por jugador
        $stats = \DB::table('player_match_game')
            ->select(
                'player_id',
                \DB::raw('SUM(goles) as total_goles'),
                \DB::raw('SUM(asistencias) as total_asistencias'),
                \DB::raw('SUM(amarillas) as total_amarillas'),
                \DB::raw('SUM(rojas) as total_rojas')
            )
            ->whereIn('match_game_id', $matchIds)
            ->groupBy('player_id')
            ->orderByDesc('total_goles') // puedes cambiar el orden según el ranking
            ->get();

        // Cargar datos de los jugadores
        $players = \App\Models\Player::whereIn('id', $stats->pluck('player_id'))->get()->keyBy('id');

        // Mezclamos para tener los nombres en la respuesta
        $ranking = $stats->map(function ($stat) use ($players) {
            $player = $players[$stat->player_id] ?? null;
            return [
                'player_id' => $stat->player_id,
                'nombre' => $player?->nombre . ' ' . $player?->apellidos,
                'equipo' => $player?->team?->nombre,
                'goles' => $stat->total_goles,
                'asistencias' => $stat->total_asistencias,
                'amarillas' => $stat->total_amarillas,
                'rojas' => $stat->total_rojas,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $ranking
        ]);
    }

    public function generarPartidos(Request $request, $id)
    {
        $torneo = Tournament::with('teams')->findOrFail($id);
        $equipos = $torneo->teams;

        if ($equipos->count() < 2) {
            return response()->json(['error' => 'Se necesitan al menos 2 equipos.'], 400);
        }

        if ($torneo->formato === 'liguilla') {
            // Round Robin
            for ($i = 0; $i < count($equipos); $i++) {
                for ($j = $i + 1; $j < count($equipos); $j++) {
                    MatchGame::create([
                        'torneo_id' => $torneo->id,
                        'equipo1_id' => $equipos[$i]->id,
                        'equipo2_id' => $equipos[$j]->id,
                        'estado_partido' => 'pendiente'
                    ]);
                }
            }
        } elseif ($torneo->formato === 'eliminacion') {
            // Elimination Bracket
            $equiposMezclados = $equipos->shuffle()->values();

            $total = $equiposMezclados->count();
            $potencia = pow(2, ceil(log($total, 2))); // siguiente potencia de 2
            $byes = $potencia - $total;

            // Añadimos 'byes' como partidos sin oponente
            $ronda1 = [];
            $index = 0;

            for ($i = 0; $i < $potencia / 2; $i++) {
                $equipo1 = $equiposMezclados[$index++] ?? null;
                $equipo2 = $equiposMezclados[$index++] ?? null;

                $match = MatchGame::create([
                    'torneo_id' => $torneo->id,
                    'equipo1_id' => $equipo1?->id,
                    'equipo2_id' => $equipo2?->id,
                    'estado_partido' => 'pendiente'
                ]);

                $ronda1[] = $match;
            }
        }

        return response()->json(['success' => true, 'message' => 'Partidos generados']);
    }



}
