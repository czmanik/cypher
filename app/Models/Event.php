<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

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
        // Nová pole pro slevy:
        'is_commercial',
        'capacity_limit',
        'required_fields',
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

    // Pomocná metoda pro výpočet kapacity
    public function getRemainingCapacityAttribute()
    {
        // Pokud je limit null (neomezeně), vrátíme "nekonečno" (hodně velké číslo)
        if (is_null($this->capacity_limit)) return 999999;
        
        return $this->capacity_limit - $this->claims()->count();
    }
}