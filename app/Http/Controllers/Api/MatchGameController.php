<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MatchGame;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class MatchGameController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $matches = MatchGame::with(['equipo1', 'equipo2', 'torneo'])->get();

        return response()->json(['success' => true, 'data' => $matches]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'torneo_id' => 'required|exists:tournaments,id',
            'equipo1_id' => 'required|exists:teams,id',
            'equipo2_id' => 'required|exists:teams,id',
            'fecha_partido' => 'nullable|date',
            'goles_equipo1' => 'nullable|integer|min:0',
            'goles_equipo2' => 'nullable|integer|min:0',
            'estado_partido' => 'required|string',
            'arbitro' => 'nullable|string',
        ]);

        $match = new MatchGame($validated);
        $this->authorize('create', $match);
        $match->save();

        return response()->json(['success' => true, 'data' => $match], 201);
    }

    public function show($id)
    {
        $match = MatchGame::with(['equipo1', 'equipo2', 'torneo'])->find($id);

        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Partido no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $match]);
    }

    public function update(Request $request, $id)
    {
        $match = MatchGame::find($id);

        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Partido no encontrado'], 404);
        }

        // Autorización con MatchGamePolicy@update
        $this->authorize('update', $match);

        $validated = $request->validate([
            'torneo_id' => 'sometimes|exists:tournaments,id',
            'equipo1_id' => 'sometimes|exists:teams,id',
            'equipo2_id' => 'sometimes|exists:teams,id',
            'fecha_partido' => 'nullable|date',
            'goles_equipo1' => 'nullable|integer|min:0',
            'goles_equipo2' => 'nullable|integer|min:0',
            'estado_partido' => 'nullable|string',
            'arbitro' => 'nullable|string',
        ]);

        $match->update($validated);

        return response()->json(['success' => true, 'data' => $match]);
    }

    public function destroy($id)
    {
        $match = MatchGame::find($id);

        if (!$match) {
            return response()->json(['success' => false, 'message' => 'Partido no encontrado'], 404);
        }

        // Autorización con MatchGamePolicy@delete
        $this->authorize('delete', $match);

        $match->delete();

        return response()->json(['success' => true, 'message' => 'Partido eliminado correctamente']);
    }

    public function getByTournament(Tournament $tournament)
{
    // Si el torneo es privado y no tienes acceso, deniega
    if ($tournament->visibilidad === 'privado') {
        $user = auth('sanctum')->user();

        if (!$user || (
            $tournament->user_id !== $user->id &&
            !$tournament->invitedUsers()->where('user_id', $user->id)->exists()
        )) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver los partidos de este torneo.'
            ], 403);
        }
    }

    $matches = $tournament->matches()->with(['equipo1', 'equipo2'])->get();

    return response()->json([
        'success' => true,
        'data' => $matches
    ]);
}

}
