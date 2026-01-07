<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'slug', 'perex', 'description', 
        'start_at', 'image_path', 'is_published'
    ];
    
    protected $casts = [
        'start_at' => 'datetime',
        'is_published' => 'boolean',
    ];
}