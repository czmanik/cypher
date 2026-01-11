<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Konstanty pro typy zaměstnanců (aby se neudělal překlep)
    const TYPE_KITCHEN = 'kitchen';
    const TYPE_FLOOR = 'floor';
    const TYPE_SUPPORT = 'support';
    const TYPE_MANAGER = 'manager';

    public function canAccessPanel(Panel $panel): bool
    {
        // Allow all active users to access the panel
        return $this->is_active;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'pin_code',
        'salary_type',
        'hourly_rate',
        'is_active',
        'is_manager',
        'employee_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_manager' => 'boolean', // <--- NOVÉ
            'hourly_rate' => 'decimal:2',
        ];
    }

    public function plannedShifts(): HasMany
    {
        return $this->hasMany(PlannedShift::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(ShiftAvailability::class);
    }

    // Helper pro hezký výpis pozice v češtině
    public function getEmployeeTypeLabelAttribute(): string
    {
        return match($this->employee_type) {
            self::TYPE_KITCHEN => 'Kuchyň',
            self::TYPE_FLOOR => 'Plac / Bar',
            self::TYPE_SUPPORT => 'Pomocný personál',
            self::TYPE_MANAGER => 'Management',
            default => 'Neurčeno',
        };
    }
}