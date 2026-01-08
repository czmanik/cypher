<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'slug', 'perex', 'description', 
        'category',
        'start_at', 'end_at',
        'start_at', 'image_path', 'is_published'
    ];
    
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_published' => 'boolean',
    ];
}