<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TeamController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        return response()->json(['success' => true, 'data' => Team::all()]);
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tournament_id' => 'required|exists:tournaments,id',
            'logo' => 'nullable|string',
            'color_equipacion' => 'nullable|string',
            'entrenador' => 'nullable|string',
        ]);

        $tournament = Tournament::findOrFail($request->tournament_id);
        $this->authorize('create', [Team::class, $tournament]);

        $team = Team::create($validated);

        return response()->json(['success' => true, 'data' => $team, 'message' => 'Equipo creado correctamente.']);
    }

    public function show($id)
    {
        $team = Team::find($id);
        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'data' => $team]);
    }

    public function update(Request $request, $id)
    {
        $team = Team::find($id);
        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado.'], 404);
        }

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'tournament_id' => 'sometimes|required|exists:tournaments,id',
            'logo' => 'nullable|string',
            'color_equipacion' => 'nullable|string',
            'entrenador' => 'nullable|string',
        ]);

        $team->update($validated);

        return response()->json(['success' => true, 'data' => $team, 'message' => 'Equipo actualizado correctamente.']);
    }

    public function destroy($id)
    {
        $team = Team::find($id);
        if (!$team) {
            return response()->json(['success' => false, 'message' => 'Equipo no encontrado.'], 404);
        }

        $team->delete();
        return response()->json(['success' => true, 'message' => 'Equipo eliminado correctamente.']);
    }

    public function getByTournament(Tournament $tournament)
    {
        // Si es privado y no tienes permiso para verlo, no puedes ver los equipos
        if ($tournament->visibilidad === 'privado' && !$this->authorize('view', $tournament)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para ver los equipos de este torneo privado.'
            ], 403);
        }

        $teams = $tournament->teams; // O usar relación si la tienes definida
        return response()->json([
            'success' => true,
            'data' => $teams
        ]);
    }


    public function storeForTournament(Request $request, Tournament $tournament)
    {
        $this->authorize('createTeamForTournament', $tournament);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'color_equipacion' => 'nullable|string|max:255',
            'entrenador' => 'nullable|string|max:255',
        ]);

        $team = Team::create([
            'nombre' => $validated['nombre'],
            'color_equipacion' => $validated['color_equipacion'] ?? null,
            'entrenador' => $validated['entrenador'] ?? null,
            'tournament_id' => $tournament->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $team,
        ], 201);
    }
}
