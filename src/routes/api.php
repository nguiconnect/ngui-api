<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProviderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| URL base: /api
*/

/**
 * Healthcheck simple: GET /api/health -> { "status": "ok" }
 */
Route::get('/health', fn() => ['status' => 'ok']);

/**
 * Exemple d’endpoint protégé pour récupérer l’utilisateur courant
 * GET /api/user (avec token Sanctum)
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Versionnée: /api/v1/...
 */
Route::prefix('v1')->group(function () {
    // --- Public (lecture) ---
    Route::get('/providers', [ProviderController::class, 'index'])
        ->name('providers.index');

    Route::get('/providers/{provider}', [ProviderController::class, 'show'])
        ->name('providers.show');

    // --- Écriture protégée par Sanctum ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/providers', [ProviderController::class, 'store'])
            ->name('providers.store');

        Route::put('/providers/{provider}', [ProviderController::class, 'update'])
            ->name('providers.update');

        Route::delete('/providers/{provider}', [ProviderController::class, 'destroy'])
            ->name('providers.destroy');
    });
});
