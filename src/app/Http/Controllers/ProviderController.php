<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index()
    {
        return Provider::latest()->paginate(20);
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
