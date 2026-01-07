<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Důležitý import

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_path',
        'is_available',
        'stock_qty',
        'is_shippable',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_shippable' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Vztah: Produkt patří do jedné Kategorie.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}