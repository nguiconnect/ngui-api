<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'phone',
        'email',
        'details',
        'status',
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
