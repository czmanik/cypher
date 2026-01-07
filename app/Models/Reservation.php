<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'reservation_time',
        'duration_minutes',
        'guests_count',
        'status',
        'note',
    ];

    protected $casts = [
        'reservation_time' => 'datetime', // Důležité: Laravel s tím bude pracovat jako s datem
        'guests_count' => 'integer',
    ];

    // Rezervace patří k jednomu stolu
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}