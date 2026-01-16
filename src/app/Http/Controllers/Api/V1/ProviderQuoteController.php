<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderQuoteController extends Controller
{
    /**
     * GET /api/v1/provider/me
     * Retourne la fiche Provider liée à l'utilisateur connecté
     * Format: { data: {...} } (attendu par le front)
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'provider') {
            abort(403, 'This action is unauthorized.');
        }

        $provider = $user->provider;
        if (!$provider) {
            return response()->json(['data' => null], 200);
        }

        return response()->json(['data' => $provider], 200);
    }

    /**
     * GET /api/v1/provider/quotes
     * Liste des devis du prestataire connecté (via user->provider->id)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'provider') {
            abort(403, 'This action is unauthorized.');
        }

        $providerId = optional($user->provider)->id;
        if (!$providerId) {
            abort(403, 'Provider profile not linked to this user.');
        }

        $q = Quote::query()->where('provider_id', $providerId);

        $status = $request->string('status')->toString();
        if ($status !== '') {
            $q->where('status', $status);
        }

        $search = $request->string('search')->toString();
        if ($search !== '') {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        $q->orderByDesc('created_at');

        return $q->paginate((int)($request->input('per_page', 10)));
    }

    /**
     * PATCH /api/v1/provider/quotes/{quote}
     * Le prestataire peut changer le statut de SES devis.
     */
    public function update(Request $request, Quote $quote)
    {
        $user = $request->user();

        if (!$user || $user->role !== 'provider') {
            abort(403, 'This action is unauthorized.');
        }

        $providerId = optional($user->provider)->id;
        if (!$providerId) {
            abort(403, 'Provider profile not linked to this user.');
        }

        if ((int) $quote->provider_id !== (int) $providerId) {
            abort(403, 'This action is unauthorized.');
        }

        $data = $request->validate([
            'status' => ['required', Rule::in(['pending', 'contacted', 'done'])],
        ]);

        $quote->update(['status' => $data['status']]);

        return $quote;
    }
}
