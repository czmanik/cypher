<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkShift extends Model
{
    protected $guarded = []; // Povolíme vše

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    // Směna patří uživateli
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Směna má mnoho reportů (výdaje, nákupy...)
    public function reportItems(): HasMany
    {
        return $this->hasMany(ShiftReportItem::class);
    }

    // Směna má odškrtané úkoly
    public function checklistResults(): HasMany
    {
        return $this->hasMany(ShiftChecklistResult::class);
    }
}