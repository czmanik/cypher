<?php

namespace App\Policies;

use App\Models\User;

class OpenShiftMarketPolicy
{
    /**
     * Determine whether the user can view the page.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
