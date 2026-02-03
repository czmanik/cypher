<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftChecklistResult extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }
}
