<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuoteController extends Controller
{
    /**
     * Statuts autorisés (MVP)
     */
    private const STATUSES = ['pending', 'contacted', 'done'];

    /**
     * GET /api/v1/quotes
     * Query params:
     * - status (pending/contacted/done)
     * - provider_id
     * - search (name/email/phone/details/provider name)
     * - page
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'status'      => ['nullable', 'string', Rule::in(self::STATUSES)],
            'provider_id' => ['nullable', 'integer'],
            'search'      => ['nullable', 'string', 'max:255'],
            'page'        => ['nullable', 'integer'],
        ]);

        $q = Quote::query()
            ->with(['provider:id,name,category,city,region,department'])
            ->latest();

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        if (!empty($validated['provider_id'])) {
            $q->where('provider_id', $validated['provider_id']);
        }

        if (!empty($validated['search'])) {
            $term = $validated['search'];

            $q->where(function ($sub) use ($term) {
                $sub->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('details', 'like', "%{$term}%")
                    ->orWhereHas('provider', function ($p) use ($term) {
                        $p->where('name', 'like', "%{$term}%");
                    });
            });
        }

        return response()->json(
            $q->paginate(20)
        );
    }

    /**
     * GET /api/v1/quotes/{quote}
     */
    public function show(Quote $quote)
    {
        $quote->load(['provider:id,name,category,city,region,department,email,phone,whatsapp']);
        return response()->json(['data' => $quote]);
    }

    /**
     * PATCH /api/v1/quotes/{quote}
     * Body: { "status": "contacted" }
     */
    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(self::STATUSES)],
        ]);

        // ✅ BUG FIX: une seule fois "status" et on écrit une vraie valeur DB
        $quote->update([
            'status' => $validated['status'],
        ]);

        $quote->load(['provider:id,name,category,city,region,department']);

        return response()->json([
            'message' => 'Quote updated',
            'data'    => $quote,
        ]);
    }

    /**
     * POST /api/v1/quotes
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => ['required', 'integer', 'exists:providers,id'],
            'name'        => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'details'     => ['nullable', 'string', 'max:5000'],
        ]);

        // MVP UX: exiger au moins un contact
        if (empty($validated['phone']) && empty($validated['email'])) {
            return response()->json([
                'message' => 'Please provide at least phone or email.',
                'errors'  => ['contact' => ['Please provide at least phone or email.']],
            ], 422);
        }

        $quote = Quote::create([
            'provider_id' => $validated['provider_id'],
            'name'        => $validated['name'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'email'       => $validated['email'] ?? null,
            'details'     => $validated['details'] ?? null,
            'status'      => 'pending',
        ]);

        $quote->load(['provider:id,name,category,city,region,department']);

        return response()->json([
            'message' => 'Quote request created',
            'data'    => $quote,
        ], 201);
    }
}
