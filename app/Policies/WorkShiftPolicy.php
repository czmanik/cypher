<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Auth\Access\Response;

class WorkShiftPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Everyone can view list (filtered)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkShift $workShift): bool
    {
        return $user->isManager() || $workShift->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkShift $workShift): bool
    {
        return $user->isManager();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkShift $workShift): bool
    {
        return $user->isManager();
    }
}
