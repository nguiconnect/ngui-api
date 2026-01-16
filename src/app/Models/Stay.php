<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stay extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'region',
        'department',
        'city',
        'address',
        'starting_price',
        'currency',
        'rating',
        'rating_count',
        'capacity_guests',
        'capacity_rooms',
        'description',
        'services',
        'phone',
        'email',
        'whatsapp',
    ];

    protected $casts = [
        'services' => 'array',
    ];
}
