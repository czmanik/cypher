<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function shifts(): HasMany
    {
        return $this->hasMany(PlannedShift::class);
    }
}
