<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Team $team): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Tournament $tournament): bool
    {
        // Es el creador del torneo
        if ($user->id === $tournament->user_id || $user->role === 'admin') {
            return true;
        }

        // Es un invitado con rol 'editor'
        return $tournament->invitedUsers()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'editor')
            ->exists();

        /*
        return $tournament->invited_users
->where('id', $user->id)
->where('pivot.role', 'editor')
->isNotEmpty();

        */

    }

    public function createForTournament(User $user, Tournament $tournament)
    {
        return $user->id === $tournament->user_id || $user->role === 'admin';
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        return $user->id === $team->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->id === $team->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Team $team): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Team $team): bool
    {
        return false;
    }
}
