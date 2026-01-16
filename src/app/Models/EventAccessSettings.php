<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAccessSettings extends Model
{
    protected $fillable = [
        'event_id',
        'access_mode',
        'allowed_roles',
        'invited_emails',
        'access_codes',
        'show_chat',
        'show_condolences',
        'public_replay',
        'note_organizer',
    ];

    protected $casts = [
        'allowed_roles'    => 'array',
        'invited_emails'   => 'array',
        'access_codes'     => 'array',
        'show_chat'        => 'boolean',
        'show_condolences' => 'boolean',
        'public_replay'    => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
