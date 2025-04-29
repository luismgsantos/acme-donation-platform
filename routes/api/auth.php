<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
