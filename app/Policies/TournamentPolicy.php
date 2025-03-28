<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TournamentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Todos los usuarios pueden ver torneos
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tournament $tournament): bool
    {
        if ($tournament->visibilidad === 'publico')
            return true;
        if (!$user)
            return false;

        return $tournament->user_id === $user->id
            || $tournament->invitedUsers()->where('user_id', $user->id)->exists();
        //return $tournament->visibilidad === 'publico' || $user->id === $tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Todo los usuarios registrados pueden crear torneos
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->user_id 
        || $tournament->invitedUsers()
        ->where('user_id', $user->id)
        ->where('tournament_user.role', 'editor')
        ->exists() 
        || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->user_id 
        || $tournament->invitedUsers()
        ->where('user_id', $user->id)
        ->where('tournament_user.role', 'editor')
        ->exists()
        || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tournament $tournament): bool
    {
        return $user->rol === 'admin' || $tournament->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tournament $tournament): bool
    {
        return false;
    }
    public function createTeamForTournament(User $user, Tournament $tournament): bool
    {
        return $user->id === $tournament->user_id || $user->rol === 'admin';
    }

}
