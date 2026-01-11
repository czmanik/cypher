<?php

namespace App\Policies;

use App\Models\User;

class ManagerOnlyPolicy
{
    /**
     * Zákadní pravidlo: Pustit jen manažera.
     */
    public function before(User $user, $ability)
    {
        if ($user->is_manager) {
            return true;
        }
        
        // Pokud není manažer, vracíme false (zakázáno)
        return false;
    }

    // Tyto metody musí existovat, aby Filament věděl, co má dělat,
    // ale metoda before() výše je "přebije", takže tady stačí vrátit false (nebo null).
    
    public function viewAny(User $user): bool { return false; }
    public function view(User $user, $model): bool { return false; }
    public function create(User $user): bool { return false; }
    public function update(User $user, $model): bool { return false; }
    public function delete(User $user, $model): bool { return false; }
}