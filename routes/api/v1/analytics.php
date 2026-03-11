<?php

use App\Http\Controllers\Api\V1\AnalyticsController;
use Illuminate\Support\Facades\Route;


Route::middleware('throttle:analytics')->group(function () {

    Route::get('/analytics/overall-summary', [AnalyticsController::class, 'overallSummary'])
        ->name('api.v1.analytics.overall-summary');

    Route::get('/analytics/barangay-list', [AnalyticsController::class, 'barangayList'])
        ->name('api.v1.analytics.barangay-list');

    Route::get('/analytics/barangay/{budgetId}', [AnalyticsController::class, 'barangayAnalytics'])
        ->name('api.v1.analytics.barangay');
});