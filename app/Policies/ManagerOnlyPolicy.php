<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ManagerOnlyPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability)
    {
        if ($user->isManager()) {
            return true;
        }

        // Explicitly deny if not a manager
        return false;
    }

    // Standard policy methods required by Filament to know what to check.
    // The before() method handles the actual logic, so these can just return false/null.
    // However, explicitly returning false is safer if before() logic changes.

    public function viewAny(User $user): bool { return false; }
    public function view(User $user, $model): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, $model): bool { return false; }
    public function delete(User $user, $model): bool { return false; }
    public function restore(User $user, $model): bool { return false; }
    public function forceDelete(User $user, $model): bool { return false; }
}
