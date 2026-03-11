<?php

use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\BudgetItemController;
use App\Http\Controllers\Api\V1\BudgetCategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/budgets', BudgetController::class)
    ->only(['index', 'show'])
    ->names('api.v1.public.budgets');

Route::apiResource('/budget-items', BudgetItemController::class)
    ->only(['index', 'show'])
    ->names('api.v1.public.budget-items');

Route::get('/budget-items/{budgetItem}/summary', [BudgetItemController::class, 'summary'])
    ->name('api.v1.public.budget-items.summary');

Route::apiResource('/budget-categories', BudgetCategoryController::class)
    ->only(['index', 'show']);

Route::middleware(['auth:sanctum', 'throttle:api', 'role:admin,budget-officer'])->group(function () {

    Route::apiResource('/budgets', BudgetController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.budgets');

    Route::apiResource('/budget-categories', BudgetCategoryController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.budget-categories');

    Route::apiResource('/budget-items', BudgetItemController::class)
        ->except(['index', 'show'])
        ->names('api.v1.protected.budget-items');
});