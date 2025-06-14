<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;
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
    public function view(User $user, Comment $comment): bool
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
    public function update(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): bool
    {
        $comment->loadMissing('matchGame.torneo');

        $match = $comment->matchGame;

        if (!$match) {
            return false;
        }

        $tournament = $match->torneo;

        if (!$tournament) {
            return false;
        }

        // Si el usuario es el creador del torneo
        if ($user->id === $tournament->user_id) {
            return true;
        }

        // Si es el propietario del comentario
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Si es invitado como "editor"
        return $tournament->invitedUsers()
            ->where('user_id', $user->id)
            ->where('tournament_user.role', 'editor')
            ->exists();
    }






    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Comment $comment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }
}
