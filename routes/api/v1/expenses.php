<?php

use App\Http\Controllers\Api\V1\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::get('/expenses/summary', [ExpenseController::class, 'summary'])
    ->name('api.v1.public.expenses.summary');

Route::apiResource('/expenses', ExpenseController::class)
    ->only(['index', 'show'])
    ->names('api.v1.public.expenses');

Route::middleware(['auth:sanctum', 'throttle:api', 'role:admin,budget-officer'])->group(function () {

    Route::apiResource('/expenses', ExpenseController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.expenses');
});