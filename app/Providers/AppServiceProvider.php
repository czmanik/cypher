<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // DŮLEŽITÉ
use Illuminate\Support\Facades\Schema; // DŮLEŽITÉ
use App\Models\MenuItem; // DŮLEŽITÉ

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Poslání menu do všech pohledů (Global Variable)
        try {
            // Kontrola, jestli tabulka existuje (aby nepadaly migrace)
            if (Schema::hasTable('menu_items')) {
                $menu = MenuItem::with('page') // Eager loading stránky pro kontrolu is_active
                    ->orderBy('sort_order')
                    ->get();
                
                View::share('globalMenu', $menu);
            } else {
                View::share('globalMenu', collect());
            }
        } catch (\Exception $e) {
            // Pokud se něco pokazí (DB connection error atd.), pošleme prázdné menu
            View::share('globalMenu', collect());
        }
    }
}