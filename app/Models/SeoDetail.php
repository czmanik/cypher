<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'robots',
    ];

    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
