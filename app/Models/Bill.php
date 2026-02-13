<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'storyous_bill_id',
        'bill_number',
        'paid_at',
        'total_amount',
        'currency',
        'table_number',
        'person_count',
        'raw_data',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'person_count' => 'integer',
        'raw_data' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }
}
