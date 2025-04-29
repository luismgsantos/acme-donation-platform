<?php

use App\Http\Controllers\Api\V1\CampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('campaigns')->group(function () {
        Route::get('', [CampaignController::class, 'index'])->name('api.campaigns.index');
        Route::post('', [CampaignController::class, 'store'])->name('api.campaigns.store');
        Route::get('{id}', [CampaignController::class, 'show'])->name('api.campaigns.show');
        Route::match(['put', 'patch'], '{id}', [CampaignController::class, 'update']);

        Route::post('{id}/donations', [CampaignController::class, 'donate'])->name('api.campaigns.donate');
    });
});
