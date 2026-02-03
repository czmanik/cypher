<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSeo;

class Page extends Model
{
    use HasFactory, HasSeo;

    // 1. POVOLENÍ ZÁPISU (Mass Assignment)
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
    ];

    // 2. FORMÁTOVÁNÍ DAT
    protected $casts = [
        'content' => 'array',   // DŮLEŽITÉ: Aby se bloky ukládaly jako JSON
        'is_active' => 'boolean',
    ];
}