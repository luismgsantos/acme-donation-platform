<?php

use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Resources\V1\CampaignResource;
use App\Models\Campaign;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('campaigns')->group(function () {
        Route::get('/', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/create', [CampaignController::class, 'create'])->name('campaigns.create');
        Route::get('/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
