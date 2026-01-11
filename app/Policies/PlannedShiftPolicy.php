<?php

namespace App\Policies;

use App\Models\PlannedShift;
use App\Models\User;

class PlannedShiftPolicy
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
    public function view(User $user, PlannedShift $plannedShift): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_manager;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlannedShift $plannedShift): bool
    {
        // Manager can update
        if ($user->is_manager) return true;

        // Employee can "update" (claim) if they are the one claiming it
        // Or if it's their shift (e.g. requesting change)
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlannedShift $plannedShift): bool
    {
        return $user->is_manager;
    }
}
