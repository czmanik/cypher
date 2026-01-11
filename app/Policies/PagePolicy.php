<?php

namespace App\Policies;

use App\Models\User;

class PagePolicy
{
    /**
     * Determine whether the user can view the open shift market page.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
