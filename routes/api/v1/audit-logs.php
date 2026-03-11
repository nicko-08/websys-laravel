<?php

use App\Http\Controllers\Api\V1\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api', 'role:admin,auditor'])->group(function () {

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('api.v1.protected.audit-logs.index');

    Route::get('/audit-logs/by-date', [AuditLogController::class, 'byDate'])
        ->name('api.v1.protected.audit-logs.by-date');

    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])
        ->name('api.v1.protected.audit-logs.show');
});