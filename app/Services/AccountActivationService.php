<?php

namespace App\Services;

use App\Mail\AccountActivationMail;
use App\Models\AccountActivationToken;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AccountActivationService
{
    /**
     * Send activation email to newly created user
     */
    public function sendActivationEmail(User $user, ?string $adminIp = null): void
    {
        DB::transaction(function () use ($user, $adminIp) {
            // Invalidate any existing tokens for this user
            $user->activationTokens()->where('used', false)->update(['used' => true]);

            // Generate new activation token
            $token = AccountActivationToken::create([
                'user_id' => $user->id,
                'token' => AccountActivationToken::generateToken(),
                'expires_at' => now()->addHours(24),
            ]);

            // Generate activation URL
            $activationUrl = URL::temporarySignedRoute(
                'account.activate.show',
                now()->addHours(24),
                ['token' => $token->token]
            );

            // Send email
            Mail::to($user->email)->send(
                new AccountActivationMail(
                    $user,
                    $activationUrl,
                    $token->expires_at->format('F d, Y h:i A')
                )
            );

            // Log the event
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'account_activation_email_sent',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'details' => [
                    'email' => $user->email,
                    'expires_at' => $token->expires_at->toDateTimeString(),
                ],
                'ip_address' => $adminIp ?? request()->ip(),
            ]);
        });
    }

    /**
     * Verify activation token
     */
    public function verifyToken(string $tokenString): ?AccountActivationToken
    {
        $token = AccountActivationToken::with('user')
            ->where('token', $tokenString)
            ->valid()
            ->first();

        return $token;
    }

    /**
     * Activate user account with password
     */
    public function activateAccount(AccountActivationToken $token, string $password, string $ipAddress): bool
    {
        return DB::transaction(function () use ($token, $password, $ipAddress) {
            $user = $token->user;

            // Update user
            $user->update([
                'password' => $password,
                'status' => User::STATUS_ACTIVE,
                'email_verified_at' => now(),
            ]);

            // Mark token as used
            $token->markAsUsed($ipAddress);

            // Log activation
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'account_activated',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'details' => [
                    'status' => User::STATUS_ACTIVE,
                    'email_verified_at' => now()->toDateTimeString(),
                ],
                'ip_address' => $ipAddress,
            ]);

            return true;
        });
    }

    /**
     * Resend activation email
     */
    public function resendActivationEmail(User $user, ?string $adminIp = null): void
    {
        if (!$user->isPendingActivation()) {
            throw new \Exception('User account is not in pending activation status.');
        }

        $this->sendActivationEmail($user, $adminIp);
    }
}
