<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    /**
     * Liste paginée + recherche.
     * GET /api/v1/providers?page=1&per_page=20&q=...
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min(100, $perPage)); // borne entre 1 et 100

        $query = Provider::query()->latest(); // ORDER BY created_at DESC

        if ($q !== '') {
            $like = "%{$q}%";
            $query->where(function ($w) use ($like) {
                $w->where('name', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('category', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like);
            });
        }

        // appends() conserve les paramètres dans les liens de pagination
        return $query->paginate($perPage)->appends([
            'q' => $q,
            'per_page' => $perPage,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'city'     => 'nullable|string|max:100',
            'phone'    => 'nullable|string|max:100',
            'email'    => 'nullable|email|max:255',
            'rating'   => 'nullable|numeric|min:0|max:5',
        ]);

        return response()->json(Provider::create($data), 201);
    }

    public function show(Provider $provider)
    {
        return $provider;
    }

    public function update(Request $request, Provider $provider)
    {
        $data = $request->validate([
            'name'     => 'sometimes|required|string|max:255',
            'category' => 'nullable|string|max:100',
            'city'     => 'nullable|string|max:100',
            'phone'    => 'nullable|string|max:100',
            'email'    => 'nullable|email|max:255',
            'rating'   => 'nullable|numeric|min:0|max:5',
        ]);

        $provider->update($data);
        return $provider;
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return response()->noContent();
    }
}
