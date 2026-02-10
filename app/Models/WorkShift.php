<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class WorkShift extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'total_hours' => 'decimal:2',
        'calculated_wage' => 'decimal:2',
        'bonus' => 'decimal:2',
        'penalty' => 'decimal:2',
        'advance_amount' => 'decimal:2',
    ];

    // Konstanty stavů (aby se nám nepletly texty)
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending_approval';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    const PAYMENT_METHOD_CASH = 'cash';
    const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['status', 'start_at', 'end_at', 'bonus', 'penalty', 'advance_amount', 'user_id', 'total_hours', 'calculated_wage', 'note', 'manager_note'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "Směna byla " . match($eventName) {
            'created' => 'vytvořena',
            'updated' => 'upravena',
            'deleted' => 'smazána',
            'restored' => 'obnovena',
            default => $eventName,
        });
    }

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

    public function getFinalPayoutAttribute(): float
    {
        // prevent negative payout
        return max(0, (float)$this->calculated_wage + (float)$this->bonus - (float)$this->penalty - (float)$this->advance_amount);
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
