<?php

use Illuminate\Support\Facades\Route;

require __DIR__.'/api/auth.php';

/**
 * Everything is divided into versions.
 * The first version is v1 and it is implmented.
 * V2 is not implmented, it is just a placeholder to showcase structured scalability in the future.
 */
Route::prefix('v1')->group(function () {
    require __DIR__.'/api/v1/campaigns.php';
});

Route::prefix('v2')->group(function () {
    // Other routes for v2...
    // require __DIR__.'/api/v2/campaigns.php';
});
