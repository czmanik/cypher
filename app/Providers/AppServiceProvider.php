<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate; // <--- NOVÉ: Potřeba pro politiky
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

// --- Import Politiky ---
use App\Policies\ManagerOnlyPolicy;

// --- Import Modelů, které chceš skrýt ---
use App\Models\Category;
use App\Models\Product;
use App\Models\ChecklistTemplate;
use App\Models\ContentBlock;
use App\Models\Page;
use App\Models\Event;
use App\Models\EventClaim; // Získané kontakty
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\OpeningHour;
use App\Models\User; 

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
        // -----------------------------------------------------------
        // 1. ZABEZPEČENÍ ADMINU (Policies)
        // -----------------------------------------------------------
        // Tímto říkáme: "Pro tyto modely použij přísnou ManagerOnlyPolicy"
        // (Pokud uživatel není manažer, vůbec tyto položky v menu neuvidí)

        Gate::policy(Category::class, ManagerOnlyPolicy::class);
        Gate::policy(Product::class, ManagerOnlyPolicy::class);
        Gate::policy(ChecklistTemplate::class, ManagerOnlyPolicy::class);
        Gate::policy(ContentBlock::class, ManagerOnlyPolicy::class);
        Gate::policy(Page::class, ManagerOnlyPolicy::class);
        Gate::policy(Event::class, ManagerOnlyPolicy::class);
        Gate::policy(EventClaim::class, ManagerOnlyPolicy::class);
        Gate::policy(MenuItem::class, ManagerOnlyPolicy::class);
        Gate::policy(Table::class, ManagerOnlyPolicy::class);
        Gate::policy(OpeningHour::class, ManagerOnlyPolicy::class);

        // Uživatelé (Zaměstnanci)
        // Pokud máš vytvořenou speciální UserPolicy (jak jsme řešili dříve), 
        // tento řádek smaž, jinak by se bila s tou speciální.
        // Pokud speciální UserPolicy nemáš, nech to tady - skryje to uživatele všem kromě managera.
        // Gate::policy(User::class, ManagerOnlyPolicy::class); 


        // -----------------------------------------------------------
        // 2. GLOBÁLNÍ MENU PRO WEB (Tvoje původní logika)
        // -----------------------------------------------------------
        try {
            if (Schema::hasTable('menu_items')) {
                $menu = MenuItem::with('page')
                    ->orderBy('sort_order')
                    ->get();
                
                View::share('globalMenu', $menu);
            } else {
                View::share('globalMenu', collect());
            }
        } catch (\Exception $e) {
            View::share('globalMenu', collect());
        }

        // --- 3. SESSION KEEPER FOR SHIFTS ---
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => Blade::render('@livewire(\'shift-session-keeper\')'),
        );
    }
}