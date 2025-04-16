<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentInvitationController extends Controller
{
    use AuthorizesRequests;
    public function invite(Request $request, Tournament $tournament)
    {
        $this->authorize('update', $tournament);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:viewer,editor'
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        if ($tournament->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes invitar al creador del torneo.'
            ], 400);
        }

        $tournament->invitedUsers()->syncWithoutDetaching([
            $user->id => ['role' => $request->role]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario invitado correctamente.'
        ]);
    }

    public function removeInvite(Request $request, Tournament $tournament, User $user)
    {
        $this->authorize('update', $tournament);

        $tournament->invitedUsers()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Invitación eliminada correctamente.'
        ]);
    }

    public function leave(Tournament $tournament)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No autenticado'], 401);
        }

        $tournament->invitedUsers()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Has salido del torneo']);
    }

    public function removeUser(Tournament $tournament, User $user)
    {
        $authUser = auth('sanctum')->user();

        if ($authUser->id !== $tournament->user_id) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $tournament->invitedUsers()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Usuario eliminado del torneo']);
    }

    public function abandonarTorneo(Request $request, Tournament $tournament)
    {
        $user = auth('sanctum')->user();

        if (!$tournament->invitedUsers()->where('user_id', $user->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'No estás invitado a este torneo'], 403);
        }

        // Eliminar al usuario de los invitados
        $tournament->invitedUsers()->detach($user->id);

        return response()->json(['success' => true, 'message' => 'Has abandonado el torneo']);
    }

    public function expulsarInvitado(Request $request, Tournament $tournament, $userId)
    {
        $authUser = auth('sanctum')->user();

        // Comprobar si es owner o editor
        $esOwner = $tournament->user_id === $authUser->id;
        $esEditor = $tournament->invitedUsers()
            ->where('user_id', $authUser->id)
            ->wherePivot('role', 'editor')
            ->exists();

        if (!$esOwner && !$esEditor) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos'], 403);
        }

        // Evitar que se expulse al owner
        if ($tournament->user_id == $userId) {
            return response()->json(['success' => false, 'message' => 'No puedes expulsar al creador del torneo'], 403);
        }

        $tournament->invitedUsers()->detach($userId);

        return response()->json(['success' => true, 'message' => 'Usuario expulsado']);
    }



}

