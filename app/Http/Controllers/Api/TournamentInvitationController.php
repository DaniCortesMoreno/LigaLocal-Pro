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
}

