<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatchGame;

class MatchGameController extends Controller
{
    public function index()
    {
        $matches = MatchGame::all();
        return response()->json(['success' => true, 'data' => $matches]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'torneo_id' => 'required|exists:tournaments,id',
            'equipo1_id' => 'required|exists:teams,id',
            'equipo2_id' => 'required|exists:teams,id',
            'fecha_partido' => 'required|date',
            'resultado' => 'nullable|string',
            'estado_partido' => 'required|string',
            'marcador_parcial' => 'nullable|string',
            'arbitro' => 'nullable|string',
        ]);

        $match = MatchGame::create($validated);
        return response()->json(['success' => true, 'data' => $match], 201);
    }

    public function show($id)
    {
        $match = MatchGame::find($id);

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

        $validated = $request->validate([
            'torneo_id' => 'sometimes|exists:tournaments,id',
            'equipo1_id' => 'sometimes|exists:teams,id',
            'equipo2_id' => 'sometimes|exists:teams,id',
            'fecha_partido' => 'sometimes|date',
            'resultado' => 'nullable|string',
            'estado_partido' => 'sometimes|string',
            'marcador_parcial' => 'nullable|string',
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

        $match->delete();
        return response()->json(['success' => true, 'message' => 'Partido eliminado correctamente']);
    }
}
