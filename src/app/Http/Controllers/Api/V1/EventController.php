<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // GET /api/v1/events
    public function index(Request $request)
    {
        $query = Event::query();

        // uniquement futurs par défaut
        $now = now();
        $query->where('start_at', '>=', $now->copy()->subDays(1));

        if ($type = $request->string('type')->toString()) {
            $query->where('type', $type);
        }

        if ($city = $request->string('city')->toString()) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($request->boolean('only_live')) {
            $query->where('is_live', true);
        }

        if ($from = $request->date('from', null)) {
            $query->where('start_at', '>=', $from->startOfDay());
        }
        if ($to = $request->date('to', null)) {
            $query->where('start_at', '<=', $to->endOfDay());
        }

        $perPage = min(50, $request->integer('per_page', 20));

        return $query
            ->orderBy('start_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    // GET /api/v1/events/{event}
    public function show(Event $event)
    {
        return $event;
    }

    // 👉 NOUVEAU : GET /api/v1/events/{event}/access-settings (lecture)
    public function getAccessSettings(Event $event)
    {
        $settings = $event->accessSettings; // relation hasOne

        if (!$settings) {
            // valeurs par défaut si rien n’est encore configuré
            return response()->json([
                'event_id' => $event->id,
                'settings' => [
                    'access_mode'      => 'public',
                    'allowed_roles'    => [],
                    'invited_emails'   => [],
                    'access_codes'     => [],
                    'show_chat'        => true,
                    'show_condolences' => true,
                    'public_replay'    => false,
                    'note_organizer'   => null,
                ],
            ]);
        }

        return response()->json([
            'event_id' => $event->id,
            'settings' => $settings,
        ]);
    }

    // POST /api/v1/events/{event}/access-settings (écriture)
    public function updateAccessSettings(Request $request, Event $event)
    {
        $user = $request->user();

        // Seuls organizer / admin peuvent configurer le direct
        if (!$user || !in_array($user->role, ['organizer', 'admin'])) {
            return response()->json([
                'message' => 'Vous n’êtes pas autorisé à modifier les paramètres de cet évènement.',
            ], 403);
        }

        $data = $request->validate([
            'access_mode' => ['required', 'in:public,restricted'],

            'allowed_roles'   => ['nullable', 'array'],
            'allowed_roles.*' => ['string'],

            'invited_emails'   => ['nullable', 'array'],
            'invited_emails.*' => ['email'],

            'access_codes'   => ['nullable', 'array'],
            'access_codes.*' => ['string'],

            'show_chat'        => ['boolean'],
            'show_condolences' => ['boolean'],
            'public_replay'    => ['boolean'],

            'note_organizer' => ['nullable', 'string'],
        ]);

        // Sauvegarde ou mise à jour
        $settings = $event->accessSettings()->updateOrCreate(
            ['event_id' => $event->id],
            $data
        );

        return response()->json([
            'event_id' => $event->id,
            'settings' => $settings,
        ]);
    }
}
