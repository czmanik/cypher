<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'product_id',
        'storyous_product_id',
        'name',
        'quantity',
        'price_per_unit',
        'total_price',
        'vat_rate',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'price_per_unit' => 'decimal:2',
        'total_price' => 'decimal:2',
        'vat_rate' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
