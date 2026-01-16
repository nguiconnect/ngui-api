<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\EventAccessSettings;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'city',
        'region',
        'location_name',
        'start_at',
        'end_at',
        'is_live',
        'live_url',
        'description',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function accessSettings()
    {
        return $this->hasOne(EventAccessSettings::class);
    }
}
