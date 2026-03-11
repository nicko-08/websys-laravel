<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    require __DIR__ . '/v1/auth.php';
    require __DIR__ . '/v1/analytics.php';
    require __DIR__ . '/v1/budgets.php';
    require __DIR__ . '/v1/expenses.php';
    require __DIR__ . '/v1/admin.php';
    require __DIR__ . '/v1/audit-logs.php';

    Route::get('/health', fn() => response()->json(['status' => 'ok']));
});