<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\ProviderController;

Route::prefix('v1')->group(function () {
    // Public
    Route::get('providers', [ProviderController::class, 'index']);
    Route::get('providers/{provider}', [ProviderController::class, 'show']);

    // Protégé (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('providers', [ProviderController::class, 'store']);
        Route::put('providers/{provider}', [ProviderController::class, 'update']);
        Route::delete('providers/{provider}', [ProviderController::class, 'destroy']);
    });
});
