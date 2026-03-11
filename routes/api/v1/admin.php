<?php

use App\Http\Controllers\Api\V1\GovernmentUnitController;
use App\Http\Controllers\Api\V1\FiscalYearController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/government-units', GovernmentUnitController::class)
    ->only(['index', 'show']);

Route::apiResource('/fiscal-years', FiscalYearController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'throttle:api', 'role:admin'])->group(function () {

    Route::apiResource('/government-units', GovernmentUnitController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.government-units');

    Route::apiResource('/fiscal-years', FiscalYearController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.fiscal-years');

    Route::apiResource('/users', UserController::class)
        ->names('api.v1.protected.users');
});