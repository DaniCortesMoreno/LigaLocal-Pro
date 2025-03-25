<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
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
}
