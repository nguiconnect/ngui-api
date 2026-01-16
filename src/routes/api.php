<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProviderController;
use App\Http\Controllers\Api\V1\StayController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\QuoteController;
use App\Http\Controllers\Api\V1\ProviderQuoteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| URL base: /api
*/

// Healthcheck : GET /api/health -> { "status": "ok" }
Route::get('/health', fn() => ['status' => 'ok']);

// Auth publique
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/register', [AuthController::class, 'register'])->name('api.register');

// Logout + user courant (protégé)
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout'])->name('api.logout');
Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

// Versionnée: /api/v1/...
Route::prefix('v1')->group(function () {

    // -------------------------
    // Public (lecture)
    // -------------------------
    Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
    Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('providers.show');

    Route::get('/stays', [StayController::class, 'index']);
    Route::get('/stays/{stay}', [StayController::class, 'show']);

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    Route::get('/events/{event}/access-settings', [EventController::class, 'getAccessSettings'])
        ->name('events.access-settings.show');

    // ✅ Devis (public): créer une demande
    Route::post('/quotes', [QuoteController::class, 'store'])->name('quotes.store');

    // -------------------------
    // Admin-only (inbox devis)
    // -------------------------
    Route::middleware(['auth:sanctum', 'can:admin'])->group(function () {
        Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
        Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
        Route::patch('/quotes/{quote}', [QuoteController::class, 'update'])->name('quotes.update');
    });

    // -------------------------
    // Provider-only (prestataire connecté)
    // -------------------------
    Route::middleware(['auth:sanctum', 'can:provider'])->group(function () {
        Route::get('/provider/quotes', [ProviderQuoteController::class, 'index'])->name('provider.quotes.index');
        Route::patch('/provider/quotes/{quote}', [ProviderQuoteController::class, 'update'])->name('provider.quotes.update');
        Route::get('/provider/me', [ProviderQuoteController::class, 'me'])->name('provider.me');
    });

    // -------------------------
    // Écriture protégée (providers + access settings)
    // -------------------------
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/providers', [ProviderController::class, 'store'])->name('providers.store');
        Route::put('/providers/{provider}', [ProviderController::class, 'update'])->name('providers.update');
        Route::delete('/providers/{provider}', [ProviderController::class, 'destroy'])->name('providers.destroy');

        Route::post('/events/{event}/access-settings', [EventController::class, 'updateAccessSettings'])
            ->name('events.access-settings.update');
    });
});
