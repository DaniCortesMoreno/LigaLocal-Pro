<?php

namespace App\Policies;

use App\Models\Player;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlayerPolicy
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
    public function view(?User $user, Player $player): bool
    {
        $tournament = $player->team->tournament;

        // Si el torneo es público, cualquiera puede ver
        if ($tournament->visibilidad === 'publico') {
            return true;
        }
    
        // Si no está autenticado y el torneo es privado, no puede ver
        if (!$user) {
            return false;
        }
    
        // Si es el creador del torneo, puede ver
        if ($user->id === $tournament->user_id) {
            return true;
        }
    
        // Si es un usuario invitado al torneo (viewer o editor), también puede ver
        return $tournament->invitedUsers()->where('user_id', $user->id)->exists();
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
    public function update(User $user, Player $player): bool
    {
        return $user->id === $player->team->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Player $player): bool
    {
        return $user->id === $player->team->tournament->user_id || $user->rol === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Player $player): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Player $player): bool
    {
        return false;
    }
}
