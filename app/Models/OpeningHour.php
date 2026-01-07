<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningHour extends Model
{
    protected $fillable = [
        'day_of_week', 'bar_open', 'bar_close', 
        'kitchen_open', 'kitchen_close', 'is_closed'
    ];
}
