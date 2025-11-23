<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $user): bool
    {
        return ($authUser->id === $user->id) || ($authUser->role === 'admin');
    }

    /**
     * Determine whether the user can view pending tournaments.
     */
    public function viewPending(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }

}