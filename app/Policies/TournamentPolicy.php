<?php

namespace App\Policies;

use App\Models\Tournament;
use App\Models\User;

class TournamentPolicy
{
    /**
     * Determine whether the user can view any models.
     * Everyone can view the tournament list.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Everyone can view a tournament (even pending ones for now).
     */
    public function view(?User $user, Tournament $tournament): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Any authenticated user can create a tournament.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     * Only the organizer (creator) can update.
     */
    public function update(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     * Only the organizer (creator) can delete.
     */
    public function delete(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can approve/reject the tournament.
     * Only admins can approve tournaments.
     */
    public function approve(User $user, Tournament $tournament): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can manage contestants (approve/reject registrations).
     * Only the organizer can manage contestants.
     */
    public function manageContestants(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can generate/manage bracket.
     * Only the organizer can generate and manage the bracket.
     */
    public function manageBracket(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can shuffle contestant order.
     * Only the organizer can shuffle contestants before bracket generation.
     */
    public function shuffle(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can clear/remove the bracket.
     * Only the organizer can clear the bracket.
     */
    public function clearBracket(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    /**
     * Determine whether the user can reorder contestants.
     * Only the organizer can reorder contestants before bracket generation.
     */
    public function reorder(User $user, Tournament $tournament): bool
    {
        return $user->id == $tournament->user_id;
    }

    public function viewOwnMatches(User $user, Tournament $tournament): bool
    {
        // Check single contestants
        $isSingleContestant = $tournament->singleContestants()
            ->where('users.id', $user->id)
            ->exists();

        if ($isSingleContestant) {
            return true;
        }

        // Check team contestants
        $isTeamContestant = $tournament->teamContestants()
            ->whereHas('players', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->orWhereHas('captain', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->exists();

        return $isTeamContestant;
    }
}