<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Support\Facades\Auth;
class TournamentController extends Controller
{
    public function index()
    {
        return Tournament::with('user')->get();
    }

    public function show(Tournament $tournament)
    {
        if ($tournament->visibilidad === 'publico') {
            return response()->json([
                'success' => true,
                'data' => $tournament
            ]);
        }

        $user = auth('sanctum')->user();

        if (!$user || $user->id !== $tournament->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este torneo privado.'
            ], 403);
        }

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
            'fecha_fin' => 'date|after_or_equal:fecha_inicio',
            'cantidad_equipos' => 'integer|min:2',
            'cantidad_jugadores' => 'integer|min:1',
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


}
