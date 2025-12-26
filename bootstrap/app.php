<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->api(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\LogApiRequests::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        ]);
        $middleware->throttleApi(env('API_RATE_LIMIT', 60) . ',1');
    })

    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('app:calculate-budget-summaries')
            ->daily()
            ->at('02:00')
            ->onFailure(function () {
                Log::error('Budget summaries calculation failed', [
                    'command' => 'app:calculate-budget-summaries',
                    'scheduled_time' => '02:00'
                ]);
            })
            ->onSuccess(function () {
                Log::info('Budget summaries calculated successfully');
            });
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                Log::warning('Authentication failed', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->url(),
                    'route' => $request->route()?->getName()
                ]);
            } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                Log::warning('Authorization failed', [
                    'user_id' => $request->user()?->id,
                    'ip' => $request->ip(),
                    'url' => $request->url(),
                    'route' => $request->route()?->getName(),
                    'message' => $e->getMessage()
                ]);
            } elseif ($e instanceof \Illuminate\Validation\ValidationException) {
                $logLevel = ($request->is('api/v1/budgets*') || $request->is('api/v1/expenses*')) ? 'info' : 'debug';
                Log::log($logLevel, 'Validation failed', [
                    'user_id' => $request->user()?->id,
                    'url' => $request->url(),
                    'route' => $request->route()?->getName(),
                    'errors' => $e->errors(),
                    'critical_endpoint' => $request->is('api/v1/budgets*') || $request->is('api/v1/expenses*')
                ]);
            } else {
                Log::error('Unexpected error', [
                    'user_id' => $request->user()?->id,
                    'url' => $request->url(),
                    'route' => $request->route()?->getName(),
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }
        });
    })->create();
