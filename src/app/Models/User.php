<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Provider;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // ✅ important pour que User::create() enregistre bien le rôle
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Retourne toujours un rôle cohérent (par défaut: family).
     */
    public function getRoleAttribute($value): string
    {
        return $value ?: 'family';
    }

    // ✅ PRO: providers.user_id = users.id
    public function provider()
    {
        return $this->hasOne(Provider::class, 'user_id', 'id');
    }

    /**
     * ✅ Fallback (legacy Option A): providers.id = users.id
     * Utile tant que tu as des données seedées comme ça.
     */
    public function providerLegacyById()
    {
        return $this->hasOne(Provider::class, 'id', 'id');
    }

    /**
     * ✅ Helper: récupérer “le provider du user” (pro → sinon fallback)
     */
    public function resolveProvider()
    {
        return $this->provider()->first() ?: $this->providerLegacyById()->first();
    }
}
