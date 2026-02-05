<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = ['key', 'title', 'content', 'image_path', 'buttons'];

    protected $casts = [
        'buttons' => 'array',
    ];
}
