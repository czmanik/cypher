<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class WorkShift extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'calculated_wage' => 'decimal:2',
    ];

    // Konstanty stavů (aby se nám nepletly texty)
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plannedShift(): BelongsTo
    {
        return $this->belongsTo(PlannedShift::class);
    }

    public function reportItems(): HasMany
    {
        return $this->hasMany(ShiftReportItem::class);
    }

    public function checklistResults(): HasMany
    {
        return $this->hasMany(ShiftChecklistResult::class);
    }

    /**
     * Hlavní metoda pro přepočet směny.
     * Volá se před uložením, když manažer edituje časy.
     */
    public function calculateStats(): void
    {
        // Pokud směna ještě neskončila, nic nepočítáme
        if (!$this->end_at || !$this->start_at) {
            return;
        }

        // 1. Spočítat hodiny
        // diffInMinutes vrátí celé minuty, dělíme 60 pro hodiny
        $minutes = $this->start_at->diffInMinutes($this->end_at);
        $this->total_hours = round($minutes / 60, 2);

        // 2. Spočítat peníze podle nastavení uživatele
        $user = $this->user;
        
        if ($user && $user->hourly_rate > 0) {
            if ($user->salary_type === 'fixed') {
                // Fixní částka za směnu (bez ohledu na délku)
                $this->calculated_wage = $user->hourly_rate; 
            } else {
                // Hodinová sazba
                $this->calculated_wage = $this->total_hours * $user->hourly_rate;
            }
        } else {
            $this->calculated_wage = 0;
        }
    }

    /**
     * Automaticky přepočítat při ukládání modelu
     */
    protected static function booted()
    {
        static::saving(function ($shift) {
            $shift->calculateStats();
        });
    }
}
