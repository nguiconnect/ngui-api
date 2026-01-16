<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // ✅ nouveau lien propre
        'name',
        'category',
        'region',
        'department',
        'city',
        'address',
        'starting_price',
        'currency',
        'rating',
        'rating_count',
        'description',
        'services',
        'phone',
        'email',
        'whatsapp',
        'image_url',
        'diaspora_friendly',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    // Accessor pour l'URL de l'image
    public function getImageUrlAttribute($value)
    {
        return $value ?? 'https://via.placeholder.com/900x600?text=No+Image+Available';
    }

    // Accessor pour "diaspora_friendly"
    public function getDiasporaFriendlyAttribute($value)
    {
        return $value ? 'Yes' : 'No';
    }

    public function user()
    {
        // ✅ Relation standard (Option C)
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
