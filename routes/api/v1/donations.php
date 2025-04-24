<?php

use App\Http\Controllers\Api\V1\DonationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('donations')->group(function () {
        Route::post('/', [DonationController::class, 'store']);
        Route::get('/', [DonationController::class, 'index']);

        Route::get('/campaigns/{id}/donations', [DonationController::class, 'byCampaign']);
    });

    // Could have used this instead, but I prefer to be explicit
    // Route::prefix('donations')->apiResource('donations', DonationController::class)->only(['index', 'store']);
    // Like this you can read exactly what routes are available
});
