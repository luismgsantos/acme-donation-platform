<?php

use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\DonateToCampaignController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // Could have done this with a resource route, but I like to Cmd + Click to go to the Definition
    // plus, it is a bit more explicit for devs to know what's up without having to go deeper
    // Route::resource('campaigns', CampaignController::class)
    //     ->only(['index', 'store', 'show', 'update', 'destroy'])
    //     ->names([
    //         'index' => 'api.campaigns.index',
    //         'store' => 'api.campaigns.store',
    //         'show' => 'api.campaigns.show',
    //         'update' => 'api.campaigns.update',
    //         'destroy' => 'api.campaigns.destroy',
    //     ]);

    /** <APP_URL>/api/v1/campaigns */
    Route::prefix('campaigns')->group(function () {
        Route::get('', [CampaignController::class, 'index'])->name('api.campaigns.index');
        Route::post('', [CampaignController::class, 'store'])->name('api.campaigns.store');
        Route::get('{id}', [CampaignController::class, 'show'])->name('api.campaigns.show');
        Route::match(['put', 'patch'], '{id}', [CampaignController::class, 'update']);
        Route::delete('{id}', [CampaignController::class, 'destroy'])->name('api.campaigns.destroy');

        Route::post('{id}/donations', DonateToCampaignController::class)->name('api.campaigns.donate');
    });
});
