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
            'logo' => 'nullable|string|max:65535', // Aquí aceptamos imagen
            'color_equipacion' => 'nullable|string',
            'entrenador' => 'nullable|string',
        ]);

        $tournament = Tournament::findOrFail($request->tournament_id);
        $this->authorize('create', [Team::class, $tournament]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('logos', 'public');
            $validated['logo'] = $path;
        }

        $team = Team::create($validated);

        return response()->json(['success' => true, 'data' => $team, 'message' => 'Equipo creado correctamente.']);
    }

    public function show(Team $team)
    {
        if (!app(\App\Policies\TeamPolicy::class)->view(auth('sanctum')->user(), $team)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para ver este equipo'], 403);
        }

        // Carga también los invitados del torneo relacionado
        $team->load('tournament.invitedUsers');

        return response()->json([
            'success' => true,
            'data' => $team
        ]);
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
            'logo' => 'nullable|string|max:65535',
            'color_equipacion' => 'nullable|string',
            'entrenador' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('logos', 'public');
            $validated['logo'] = $path;
        }

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
        if ($tournament->visibilidad === 'publico') {
            return response()->json([
                'success' => true,
                'data' => $tournament->teams
            ]);
        }

        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        if ($tournament->user_id === $user->id) {
            return response()->json([
                'success' => true,
                'data' => $tournament->teams
            ]);
        }

        if ($tournament->invitedUsers()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => true,
                'data' => $tournament->teams
            ]);
        }

        $this->authorize('view', $tournament);

        return response()->json([
            'success' => true,
            'data' => $tournament->teams
        ]);
    }


    public function storeForTournament(Request $request, Tournament $tournament)
    {
        $this->authorize('create', [Team::class, $tournament]);

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
