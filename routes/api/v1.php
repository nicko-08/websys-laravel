<?php

use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BudgetCategoryController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\BudgetItemController;
use App\Http\Controllers\Api\V1\ExpenseController;
use App\Http\Controllers\Api\V1\FiscalYearController;
use App\Http\Controllers\Api\V1\GovernmentUnitController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    //Public Analytics (High Rate Limit)
    Route::middleware('throttle:analytics')->group(function () {
        Route::get('/analytics/overall-summary', [AnalyticsController::class, 'overallSummary'])
            ->name('api.v1.analytics.overall-summary');

        Route::get('/analytics/barangay-list', [AnalyticsController::class, 'barangayList'])
            ->name('api.v1.analytics.barangay-list');

        Route::get('/analytics/barangay/{budgetId}', [AnalyticsController::class, 'barangayAnalytics'])
            ->name('api.v1.analytics.barangay');
    });

    // Authentication
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->name('api.v1.login');
    });

    // Public Read-Only Resources
    Route::apiResource('/budgets', BudgetController::class)
        ->only(['index', 'show'])
        ->names('api.v1.public.budgets');

    Route::apiResource('/budget-items', BudgetItemController::class)
        ->only(['index', 'show'])
        ->names('api.v1.public.budget-items');

    Route::get('/budget-items/{budgetItem}/summary', [BudgetItemController::class, 'summary'])
        ->name('api.v1.public.budget-items.summary');

    Route::apiResource('/expenses', ExpenseController::class)
        ->only(['index', 'show'])
        ->names('api.v1.public.expenses');

    // Authenticated & Throttled
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        // Budget Operations (Admin + Budget Officer)
        Route::middleware('role:admin,budget-officer')->group(function () {
            Route::apiResource('/budgets', BudgetController::class)
                ->except(['index', 'show'])
                ->names('api.v1.protected.budgets');

            Route::apiResource('/budget-categories', BudgetCategoryController::class)
                ->names('api.v1.protected.budget-categories');

            Route::apiResource('/budget-items', BudgetItemController::class)
                ->except(['index', 'show'])
                ->names('api.v1.protected.budget-items');

            Route::apiResource('/expenses', ExpenseController::class)
                ->except(['index', 'show'])
                ->names('api.v1.protected.expenses');
        });

        // Admin-Only 
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('/government-units', GovernmentUnitController::class)
                ->names('api.v1.protected.government-units');

            Route::apiResource('/fiscal-years', FiscalYearController::class)
                ->names('api.v1.protected.fiscal-years');

            Route::apiResource('/users', UserController::class)
                ->names('api.v1.protected.users');
        });

        // Audit Logs (Admin, Auditor)
        Route::middleware('role:admin,auditor')->group(function () {
            Route::get('/audit-logs', [AuditLogController::class, 'index'])
                ->name('api.v1.protected.audit-logs.index');

            Route::get('/audit-logs/by-date', [AuditLogController::class, 'byDate'])
                ->name('api.v1.protected.audit-logs.by-date');

            Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
                ->name('api.v1.protected.audit-logs.show');
        });

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.v1.logout');
    });

    // Public Reference Data (Read-Only)
    Route::apiResource('/budget-categories', BudgetCategoryController::class)
        ->only(['index', 'show']);

    Route::apiResource('/government-units', GovernmentUnitController::class)
        ->only(['index', 'show']);

    Route::apiResource('/fiscal-years', FiscalYearController::class)
        ->only(['index', 'show']);

    Route::get('/health', fn() => response()->json(['status' => 'ok']));
});
