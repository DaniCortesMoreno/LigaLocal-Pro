<?php

namespace App\Policies;

use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MatchGamePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MatchGame $matchGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MatchGame $matchGame): bool
    {
        return $user->id === $matchGame->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MatchGame $matchGame): bool
    {
        return $user->id === $matchGame->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MatchGame $matchGame): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MatchGame $matchGame): bool
    {
        return false;
    }
}
