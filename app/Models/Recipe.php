<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_id',
        'yield',
        'prep_time',
        'temperature',
        'video_url',
        'description',
        'ingredients',
        'images',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'images' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
