<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSeo;

class Event extends Model
{
    use HasFactory, HasSeo;

    // TOTO JE TO, CO CHYBĚLO:
    protected $fillable = [
        'title',
        'slug',
        'category',
        'start_at',
        'end_at',
        'perex',
        'description',
        'image_path',
        'is_published',
        'is_commercial',
        'capacity_limit',
        'required_fields',
        'offline_consumed_count',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_published' => 'boolean',
        'is_commercial' => 'boolean',
        'required_fields' => 'array', // Aby se JSON uložil správně
    ];

    // Vazba na vouchery
    public function claims()
    {
        return $this->hasMany(EventClaim::class);
    }

    public function getRemainingCapacityAttribute()
    {
        if (is_null($this->capacity_limit)) return 999999;
        
        // Celková kapacita MÍNUS (pouze uplatněné online nároky + offline spotřeba)
        // Dle požadavku: odečítá voucher až po jeho načtení adminem
        $used = $this->claims()->whereNotNull('redeemed_at')->count() + $this->offline_consumed_count;
        
        return max(0, $this->capacity_limit - $used);
    }
}