<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('v1')->group(function () {
    Route::get('/ping', fn () => response()->json(['pong' => true]));
});
