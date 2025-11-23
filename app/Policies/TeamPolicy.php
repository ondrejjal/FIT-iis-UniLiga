<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Log;

class TeamPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        //
        return $user->id === $team->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        //
        return $user->id === $team->user_id;
    }

    public function viewPending(User $authUser, Team $team): bool
    {
        $result = ($team->players->contains($authUser->id)) || ($team->captain->id === $authUser->id);
        return $result;
    }
}