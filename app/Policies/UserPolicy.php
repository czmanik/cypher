<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Kdo může vidět seznam uživatelů v menu?
     * (Pokud vrátí false, položka "Zaměstnanci" zmizí z levého menu)
     */
    public function viewAny(User $user): bool
    {
        return $user->is_manager;
    }

    /**
     * Kdo může vidět detail konkrétního uživatele?
     */
    public function view(User $user, User $model): bool
    {
        // Manažer vidí všechny, zaměstnanec jen svůj vlastní profil
        return $user->is_manager || $user->id === $model->id;
    }

    /**
     * Kdo může vytvářet nové uživatele?
     */
    public function create(User $user): bool
    {
        return $user->is_manager;
    }

    /**
     * Kdo může upravovat uživatele?
     */
    public function update(User $user, User $model): bool
    {
        // ZDE JE ZMĚNA:
        // Povolíme to manažerovi NEBO pokud uživatel upravuje sám sebe.
        return $user->is_manager || $user->id === $model->id;
    }

    /**
     * Kdo může mazat uživatele?
     */
    public function delete(User $user, User $model): bool
    {
        // Mazat může vždy jen manažer (aby si zaměstnanec omylem nesmazal účet i s historií směn)
        return $user->is_manager;
    }
}