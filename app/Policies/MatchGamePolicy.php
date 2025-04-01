<?php

namespace App\Policies;

use App\Models\MatchGame;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatchGamePolicy
{
    use HandlesAuthorization;

    /**
     * Si el usuario es admin, tiene acceso a todo automáticamente.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->rol === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * No se listan partidos de forma pública.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Permitir ver un partido solo si el torneo es público
     * o el usuario es creador o está invitado.
     */
    public function view(?User $user, MatchGame $matchGame): bool
    {
        $tournament = $matchGame->torneo;

        if ($tournament->visibilidad === 'publico') {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $user->id === $tournament->user_id ||
            $tournament->invitedUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Solo el creador del torneo o invitados con rol editor pueden crear partidos.
     */
    public function create(User $user, MatchGame $matchGame): bool
    {
        $tournament = $matchGame->torneo;

        return $user->id === $tournament->user_id ||
            $tournament->invitedUsers()
                ->where('user_id', $user->id)
                ->where('tournament_user.role', 'editor')
                ->exists();
    }

    /**
     * Igual que `create`.
     */
    public function update(User $user, MatchGame $matchGame): bool
    {
        $tournament = $matchGame->torneo;

        return $user->id === $tournament->user_id ||
            $tournament->invitedUsers()
                ->where('user_id', $user->id)
                ->where('tournament_user.role', 'editor')
                ->exists();
    }

    /**
     * Igual que `update`.
     */
    public function delete(User $user, MatchGame $matchGame): bool
    {
        $tournament = $matchGame->torneo;

        return $user->id === $tournament->user_id ||
            $tournament->invitedUsers()
                ->where('user_id', $user->id)
                ->where('tournament_user.role', 'editor')
                ->exists();
    }

    public function restore(User $user, MatchGame $matchGame): bool
    {
        return false;
    }

    public function forceDelete(User $user, MatchGame $matchGame): bool
    {
        return false;
    }
}
