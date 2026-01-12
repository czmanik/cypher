<?php

namespace App\Models;

// --- 1. PŘIDÁNY IMPORTY PRO FILAMENT ---
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// --- 2. PŘIDÁNO "implements FilamentUser" ---
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Konstanty pro typy zaměstnanců (aby se neudělal překlep)
    const TYPE_KITCHEN = 'kitchen';
    const TYPE_FLOOR = 'floor';
    const TYPE_SUPPORT = 'support';
    const TYPE_MANAGER = 'manager';

    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'manager';
    const ROLE_EMPLOYEE = 'employee';

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
        'role',
        'qualifications',
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
            'is_manager' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'qualifications' => 'array',
        ];
    }

    // --- 3. PŘIDÁNA METODA PRO PŘÍSTUP DO ADMINU ---
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active;
    }

    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER || $this->isAdmin();
    }

    public function hasQualification(string $role): bool
    {
        // Admin/Manager can do everything? Maybe not, but for filtering shifts they are special.
        // Let's stick to explicit qualifications.
        if (empty($this->qualifications)) {
            return false;
        }
        return in_array($role, $this->qualifications);
    }

    // Helper pro hezký výpis pozic v češtině
    public function getQualificationsLabelsAttribute(): string
    {
        if (empty($this->qualifications)) return 'Neurčeno';

        $labels = [
            self::TYPE_KITCHEN => 'Kuchyň',
            self::TYPE_FLOOR => 'Plac / Bar',
            self::TYPE_SUPPORT => 'Pomoc',
            self::TYPE_MANAGER => 'Management',
        ];

        $myLabels = array_map(fn($q) => $labels[$q] ?? $q, $this->qualifications);
        return implode(', ', $myLabels);
    }
}