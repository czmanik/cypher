<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category', // 'ingredient', 'operational'
        'stock_qty',
        'unit',
        'package_size',
        'min_stock_qty',
        'price',
    ];

    protected $casts = [
        'stock_qty' => 'decimal:4',
        'package_size' => 'decimal:4',
        'min_stock_qty' => 'decimal:4',
        'price' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->stock_qty <= 0) {
            return 'critical'; // Red
        }

        if ($this->stock_qty <= $this->min_stock_qty) {
            return 'low'; // Orange
        }

        return 'ok'; // Green
    }
}
