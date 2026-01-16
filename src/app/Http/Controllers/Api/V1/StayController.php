<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Stay;
use Illuminate\Http\Request;

class StayController extends Controller
{
    // GET /api/v1/stays
    public function index(Request $request)
    {
        $query = Stay::query();

        // Filtres simples
        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($city = $request->string('city')->toString()) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($region = $request->string('region')->toString()) {
            $query->where('region', 'like', "%{$region}%");
        }

        if ($min = $request->integer('price_min')) {
            $query->where('starting_price', '>=', $min);
        }
        if ($max = $request->integer('price_max')) {
            $query->where('starting_price', '<=', $max);
        }

        $perPage = min(30, $request->integer('per_page', 12));

        return $query
            ->orderBy('starting_price')
            ->paginate($perPage)
            ->withQueryString();
    }

    // GET /api/v1/stays/{stay}
    public function show(Stay $stay)
    {
        return $stay;
    }
}
