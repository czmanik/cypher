<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkShift;

class WorkShiftPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only managers see the full WorkShift list (HR & Provoz)
        return $user->is_manager;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkShift $workShift): bool
    {
        return $user->is_manager || $user->id === $workShift->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_manager; // Or employees start via "Punch in"? But here via Resource, manager only.
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkShift $workShift): bool
    {
        return $user->is_manager;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkShift $workShift): bool
    {
        return $user->is_manager;
    }
}
