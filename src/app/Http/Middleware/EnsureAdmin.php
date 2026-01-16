<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Pas connecté => 401 (Sanctum gère déjà souvent ça)
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Pas admin => 403
        if (($user->role ?? null) !== 'admin') {
            return response()->json(['message' => 'Forbidden (admin only).'], 403);
        }

        return $next($request);
    }
}
