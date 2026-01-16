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
        $q = Provider::query();

        // filtres optionnels ?search, ?category, ?city
        if ($s = trim((string) $request->query('search', ''))) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('city', 'like', "%{$s}%")
                    ->orWhere('category', 'like', "%{$s}%");
            });
        }
        if ($c = trim((string) $request->query('category', ''))) {
            $q->where('category', $c);
        }
        if ($city = trim((string) $request->query('city', ''))) {
            $q->where('city', $city);
        }

        // tri récent + pagination
        $items = $q->latest()->paginate(20);

        return response()->json($items);
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
