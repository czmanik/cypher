<?php

namespace App\Policies;

use App\Models\User;

class InventoryItemPolicy extends ManagerOnlyPolicy
{
    // Inherits 'before' check from ManagerOnlyPolicy
    // effectively allowing managers to do everything
    // and blocking everyone else.
}
