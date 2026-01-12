<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlannedShift extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_ORDERED = 'ordered'; // Nařízeno managerem
    const STATUS_OFFERED = 'offered'; // Nabídnuto managerem
    const STATUS_REQUESTED = 'requested'; // Poptáno zaměstnancem
    const STATUS_CONFIRMED = 'confirmed'; // Potvrzeno zaměstnancem (akceptace nabídky)
    const STATUS_REJECTED = 'rejected'; // Zamítnuto

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shiftPlan(): BelongsTo
    {
        return $this->belongsTo(ShiftPlan::class);
    }
}