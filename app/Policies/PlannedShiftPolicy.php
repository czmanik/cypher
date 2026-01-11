<?php

namespace App\Policies;

use App\Models\PlannedShift;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlannedShiftPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Everyone can see shifts, but the list is filtered in the Resource/Widget
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlannedShift $plannedShift): bool
    {
        // Manager can see all. User sees own or published ones.
        if ($user->isManager()) {
            return true;
        }

        // User sees own
        if ($plannedShift->user_id === $user->id) {
            return true;
        }

        // User sees others only if published (optional, usually employees don't see others' shifts unless configured)
        // For now, let's say NO, only own.
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Manager can create always.
        // Employee can create "Requests" (handled by Widget action, not generic create)
        // Allowing create here for safety, but UI will guide the logic.
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlannedShift $plannedShift): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Employee can update own shift only if it is a REQUEST or OFFERED (to accept)
        if ($plannedShift->user_id === $user->id) {
            return in_array($plannedShift->status, [
                PlannedShift::STATUS_REQUESTED,
                PlannedShift::STATUS_OFFERED
            ]);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlannedShift $plannedShift): bool
    {
        if ($user->isManager()) {
            return true;
        }

        // Employee can delete own REQUEST
        if ($plannedShift->user_id === $user->id) {
            return $plannedShift->status === PlannedShift::STATUS_REQUESTED;
        }

        return false;
    }
}
