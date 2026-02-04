<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'yield',
        'prep_time',
        'temperature',
        'video_url',
        'description',
        'ingredients',
        'allowed_roles',
        'images',
    ];

    protected $casts = [
        'ingredients' => 'array',
        'allowed_roles' => 'array',
        'images' => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
