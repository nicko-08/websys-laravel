<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // User Status Constants
    public const STATUS_PENDING_ACTIVATION = 'pending_activation';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_DEACTIVATED = 'deactivated';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check if user is pending activation
     */
    public function isPendingActivation(): bool
    {
        return $this->status === self::STATUS_PENDING_ACTIVATION;
    }

    /**
     * Activate the user account
     */
    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Check if user can login
     */
    public function canLogin(): bool
    {
        return $this->isActive() && $this->hasVerifiedEmail();
    }

    /**
     * Activation tokens relationship
     */
    public function activationTokens()
    {
        return $this->hasMany(AccountActivationToken::class);
    }
}
