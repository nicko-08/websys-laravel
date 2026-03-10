<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccountActivationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'used',
        'used_at',
        'ip_address',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Generate a secure random token
     */
    public static function generateToken(): string
    {
        return hash('sha256', Str::random(60) . microtime());
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if token is valid for use
     */
    public function isValid(): bool
    {
        return !$this->used && !$this->isExpired();
    }

    /**
     * Mark token as used
     */
    public function markAsUsed(string $ipAddress = null): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * User relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for valid tokens
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)
            ->where('expires_at', '>', now());
    }
}
