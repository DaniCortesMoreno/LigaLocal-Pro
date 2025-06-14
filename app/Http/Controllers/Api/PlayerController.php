<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PlayerController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $players = Player::with('team')->get();
        return response()->json($players);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required|exists:teams,id',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'edad' => 'required|integer|min:0',
            'dorsal' => 'required|integer|min:0',
            'posición' => 'required|string',
            'estado' => 'required|in:activo,lesionado,suspendido',
            'foto' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('fotos', 'public');
            $request['foto'] = $path;
        }

        $player = Player::create($request->all());
        return response()->json(['message' => 'Jugador creado', 'player' => $player], 201);
    }

    /*public function show($id)
    {
        $player = Player::with('team')->find($id);
        if (!$player) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }
        return response()->json($player);
    }*/

    public function show(Player $player)
    {
        $user = auth('sanctum')->user();

        if (!app(\App\Policies\PlayerPolicy::class)->view($user, $player)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para ver este jugador.'
            ], 403);
        }

        $player->load('team.tournament', 'mvps');

        return response()->json([
            'success' => true,
            'data' => array_merge(
                $player->toArray(),
                ['team' => $player->team, 'mvps' => $player->mvps, 'mvp_count' => $player->mvps()->count()]
            )
        ]);
    }

    public function update(Request $request, $id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'team_id' => 'sometimes|required|exists:teams,id',
            'nombre' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'edad' => 'sometimes|required|integer|min:0',
            'dorsal' => 'sometimes|required|integer|min:0',
            'posición' => 'sometimes|required|string',
            'estado' => 'sometimes|required|in:activo,lesionado,suspendido',
            'foto' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('fotos', 'public');
            $request['foto'] = $path;
        }

        $player->update($request->all());
        return response()->json(['message' => 'Jugador actualizado', 'player' => $player]);
    }

    public function destroy($id)
    {
        $player = Player::find($id);
        if (!$player) {
            return response()->json(['message' => 'Jugador no encontrado'], 404);
        }

        $player->delete();
        return response()->json(['message' => 'Jugador eliminado']);
    }

    public function getPlayersByTeam($teamId)
    {
        $team = Team::with('players')->find($teamId);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Equipo no encontrado.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $team->players
        ]);
    }

    public function storeForTeam(Request $request, Team $team)
    {
        $tournament = $team->tournament;

        $this->authorize('create', [Player::class, $team]); // autorizamos por team

        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'edad' => 'sometimes|required|integer|min:0',
            'dorsal' => 'sometimes|required|integer|min:0',
            'posición' => 'sometimes|required|string',
            'estado' => 'sometimes|required|in:activo,lesionado,suspendido',
            'foto' => 'nullable|string',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $path = $file->store('fotos', 'public');
            $validated['foto'] = $path;
        }

        $player = $team->players()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $player
        ], 201);
    }


}
