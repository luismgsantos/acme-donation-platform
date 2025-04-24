<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CampaignController;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('campaigns')->group(function () {
        Route::get('/', [CampaignController::class, 'index']);
        Route::post('/', [CampaignController::class, 'store']);
        Route::get('/{id}', [CampaignController::class, 'show']);
        Route::put('/{id}', [CampaignController::class, 'update']);

        Route::post('/{id}/donations', [CampaignController::class, 'donate']);
    });
});
