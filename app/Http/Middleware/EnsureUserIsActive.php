<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->isActive()) {
            // Revoke the current access token for API requests
            $request->user()->currentAccessToken()?->delete();

            // For API requests, return JSON response
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Your account is not active. Please contact your administrator.'
                ], 403);
            }

            // For web requests, redirect to login
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account is not active. Please contact your administrator.']);
        }

        return $next($request);
    }
}
