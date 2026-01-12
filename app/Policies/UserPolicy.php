<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Admin and Manager can view the list of users
        return $user->isManager();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admin/Manager can view anyone. Employee can view themselves.
        return $user->isManager() || $user->id === $model->id;
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
    public function update(User $user, User $model): bool
    {
        // Admin can update anyone.
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can update others, but NOT Admins.
        if ($user->isManager()) {
            return !$model->isAdmin();
        }

        // Employee can update themselves (or restricted fields)
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Manager cannot delete Admin
        if ($model->isAdmin()) {
            return $user->isAdmin(); // Only Admin can delete Admin
        }

        return $user->isManager();
    }
}
