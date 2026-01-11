<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = ['code', 'value', 'used_at', 'used_by_user_id'];

    protected $casts = [
        'used_at' => 'datetime',
    ];
}
