<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RegistrationOTP extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'registration_otps';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'otp_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'user_type',
        'otp_code',
        'otp_expires_at',
        'attempts',
        'locked_until',
        'is_verified',
        'verified_at',
        'ip_address',
        'user_agent',
        'resend_count',
        'last_resent_at',
        'session_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'otp_expires_at' => 'datetime',
        'locked_until' => 'datetime',
        'verified_at' => 'datetime',
        'last_resent_at' => 'datetime',
        'is_verified' => 'boolean',
        'attempts' => 'integer',
        'resend_count' => 'integer',
    ];

    /**
     * Check if OTP is expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->otp_expires_at);
    }

    /**
     * Check if OTP is locked due to too many attempts
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        if (!$this->locked_until) {
            return false;
        }
        
        return Carbon::now()->isBefore($this->locked_until);
    }

    /**
     * Increment verification attempts
     *
     * @return void
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
        
        $maxAttempts = (int) config('otp.max_attempts', 5);
        
        if ($this->attempts >= $maxAttempts) {
            $lockoutMinutes = (int) config('otp.lockout_minutes', 15);
            $this->locked_until = Carbon::now()->addMinutes($lockoutMinutes);
            $this->save();
        }
    }

    /**
     * Mark OTP as verified
     *
     * @return void
     */
    public function markAsVerified(): void
    {
        $this->is_verified = true;
        $this->verified_at = Carbon::now();
        $this->save();
    }

    /**
     * Check if can resend OTP
     *
     * @return bool
     */
    public function canResend(): bool
    {
        $maxResends = (int) config('otp.max_resends', 3);
        
        if ($this->resend_count >= $maxResends) {
            return false;
        }
        
        if (!$this->last_resent_at) {
            return true;
        }
        
        $cooldownSeconds = (int) config('otp.resend_cooldown_seconds', 60);
        return Carbon::now()->diffInSeconds($this->last_resent_at) >= $cooldownSeconds;
    }

    /**
     * Increment resend count
     *
     * @return void
     */
    public function incrementResendCount(): void
    {
        $this->resend_count++;
        $this->last_resent_at = Carbon::now();
        $this->save();
    }

    /**
     * Scope for finding active OTP by session token
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $token
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBySessionToken($query, string $token)
    {
        return $query->where('session_token', $token)
                    ->where('is_verified', false);
    }

    /**
     * Scope for finding active OTP by email and user type
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $email
     * @param string $userType
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEmailAndType($query, string $email, string $userType)
    {
        return $query->where('email', $email)
                    ->where('user_type', $userType)
                    ->where('is_verified', false)
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for cleanup expired OTPs
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('otp_expires_at', '<', Carbon::now())
                    ->where('is_verified', false);
    }

    /**
     * Scope for cleanup old verified OTPs
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $days
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOldVerified($query, int $days = 7)
    {
        return $query->where('is_verified', true)
                    ->where('verified_at', '<', Carbon::now()->subDays($days));
    }
}
