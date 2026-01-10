<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    protected $guarded = [];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    // Pomocná funkce, která vrátí správnou URL pro frontend
    public function getUrlAttribute(): string
    {
        return match ($this->type) {
            'page' => $this->page ? route('page.show', $this->page->slug) : '#',
            'route' => $this->route_name ? route($this->route_name) : '#',
            'url' => $this->url ?? '#',
            default => '#',
        };
    }
    
    // Zjistí, jestli má být položka vidět (Bezpečnostní pojistka!)
    public function getIsVisibleAttribute(): bool
    {
        if ($this->type === 'page') {
            // Pokud je to stránka, musí existovat A musí být aktivní
            return $this->page && $this->page->is_active;
        }
        return true; // Ostatní odkazy jsou vždy vidět
    }
}