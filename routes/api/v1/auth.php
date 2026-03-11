<?php
Route::middleware('throttle:auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->name('api.v1.login');
    });
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.v1.logout');
});