<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivateAccountRequest;
use App\Services\AccountActivationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AccountActivationController extends Controller
{
    public function __construct(
        private readonly AccountActivationService $activationService
    ) {}

    /**
     * Show activation form
     */
    public function show(Request $request, string $token)
    {
        // Verify signed URL
        if (!$request->hasValidSignature()) {
            return view('pages.auth.activation-expired', [
                'reason' => 'This activation link has expired or is invalid.'
            ]);
        }

        // Verify token
        $tokenRecord = $this->activationService->verifyToken($token);

        if (!$tokenRecord) {
            return view('pages.auth.activation-expired', [
                'reason' => 'This activation link has already been used or has expired.'
            ]);
        }

        return view('pages.auth.activate-account', [
            'token' => $token,
            'user' => $tokenRecord->user,
            'expiresAt' => $tokenRecord->expires_at->diffForHumans(),
        ]);
    }

    /**
     * Process account activation
     */
    public function activate(ActivateAccountRequest $request, string $token)
    {
        // Verify signed URL
        if (!$request->hasValidSignature()) {
            return back()->withErrors(['token' => 'Invalid or expired activation link.']);
        }

        // Verify token
        $tokenRecord = $this->activationService->verifyToken($token);

        if (!$tokenRecord) {
            return back()->withErrors(['token' => 'This activation link has already been used or has expired.']);
        }

        // Activate account
        try {
            $this->activationService->activateAccount(
                $tokenRecord,
                $request->input('password'),
                $request->ip()
            );

            Log::info('Account activated successfully', [
                'user_id' => $tokenRecord->user_id,
                'email' => $tokenRecord->user->email,
            ]);

            // Logout any existing web session (e.g., admin session) before redirecting
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('login')
                ->with('success', 'Your account has been successfully activated! You may now log in.');
        } catch (\Exception $e) {
            Log::error('Account activation failed', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['activation' => 'Account activation failed. Please try again or contact support.']);
        }
    }
}
