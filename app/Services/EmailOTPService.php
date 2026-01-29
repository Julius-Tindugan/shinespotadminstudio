<?php

namespace App\Services;

use App\Models\RegistrationOTP;
use App\Mail\RegistrationOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailOTPService
{
    /**
     * Generate and send OTP via email for registration
     *
     * @param string $email User's email address
     * @param string $userName User's first name for personalization
     * @param string $userType 'admin' or 'staff'
     * @param string|null $ipAddress Request IP address
     * @param string|null $userAgent Request user agent
     * @return array
     */
    public function generateAndSendOTP(
        string $email,
        string $userName,
        string $userType,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): array {
        try {
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'success' => false,
                    'message' => 'Invalid email address format.',
                ];
            }

            // Check if there's an existing unverified OTP for this email and user type
            $existingOTP = RegistrationOTP::byEmailAndType($email, $userType)->first();
            
            if ($existingOTP) {
                // Check if locked
                if ($existingOTP->isLocked()) {
                    $minutesLeft = Carbon::now()->diffInMinutes($existingOTP->locked_until) + 1;
                    return [
                        'success' => false,
                        'message' => "Too many failed attempts. Please try again in {$minutesLeft} minutes.",
                        'locked' => true,
                        'locked_until' => $existingOTP->locked_until,
                    ];
                }

                // Check if OTP is still valid and resend cooldown is active
                if (!$existingOTP->isExpired() && !$existingOTP->canResend()) {
                    $secondsLeft = (int) config('otp.resend_cooldown_seconds', 60) - 
                                  Carbon::now()->diffInSeconds($existingOTP->last_resent_at);
                    return [
                        'success' => false,
                        'message' => "Please wait {$secondsLeft} seconds before requesting a new code.",
                        'cooldown' => true,
                    ];
                }

                // Check resend limit
                if (!$existingOTP->canResend()) {
                    return [
                        'success' => false,
                        'message' => 'Maximum resend limit reached. Please try again later.',
                        'max_resends' => true,
                    ];
                }
            }

            // Generate new OTP
            $otpLength = (int) config('otp.length', 6);
            $otp = str_pad((string)random_int(100000, 999999), $otpLength, '0', STR_PAD_LEFT);
            
            // Calculate expiration
            $expirationMinutes = (int) config('otp.expiration_minutes', 10);
            $expiresAt = Carbon::now()->addMinutes($expirationMinutes);
            
            // Generate unique session token
            $sessionToken = Str::random(64);

            // Create or update OTP record
            if ($existingOTP) {
                $existingOTP->update([
                    'otp_code' => $otp,
                    'otp_expires_at' => $expiresAt,
                    'attempts' => 0,
                    'locked_until' => null,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'session_token' => $sessionToken,
                ]);
                $existingOTP->incrementResendCount();
                $otpRecord = $existingOTP;
                $isResend = true;
            } else {
                $otpRecord = RegistrationOTP::create([
                    'email' => $email,
                    'user_type' => $userType,
                    'otp_code' => $otp,
                    'otp_expires_at' => $expiresAt,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'session_token' => $sessionToken,
                ]);
                $isResend = false;
            }

            // Send OTP via Email using Brevo
            Mail::to($email)->send(new RegistrationOtpMail($otp, $userName, $userType, $expirationMinutes));

            Log::info('Email OTP generated and sent successfully', [
                'email' => $email,
                'user_type' => $userType,
                'is_resend' => $isResend,
                'otp_id' => $otpRecord->otp_id,
            ]);

            return [
                'success' => true,
                'message' => $isResend 
                    ? 'A new verification code has been sent to your email.'
                    : 'Verification code sent to your email. Please check your inbox.',
                'session_token' => $sessionToken,
                'expires_at' => $expiresAt,
                'expiration_minutes' => $expirationMinutes,
                'is_resend' => $isResend,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to generate and send email OTP', [
                'email' => $email,
                'user_type' => $userType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send verification code. Please try again later.',
            ];
        }
    }

    /**
     * Verify OTP code
     *
     * @param string $sessionToken Session token from generateAndSendOTP
     * @param string $otpCode OTP code entered by user
     * @param string|null $ipAddress Request IP address
     * @return array
     */
    public function verifyOTP(string $sessionToken, string $otpCode, ?string $ipAddress = null): array
    {
        try {
            // Find OTP record by session token
            $otpRecord = RegistrationOTP::bySessionToken($sessionToken)->first();

            if (!$otpRecord) {
                Log::warning('OTP verification failed - Invalid session token', [
                    'session_token' => substr($sessionToken, 0, 10) . '...',
                    'ip' => $ipAddress
                ]);

                return [
                    'success' => false,
                    'message' => 'Invalid verification session. Please request a new code.',
                ];
            }

            // Check if already verified
            if ($otpRecord->is_verified) {
                return [
                    'success' => false,
                    'message' => 'This verification code has already been used.',
                ];
            }

            // Check if locked
            if ($otpRecord->isLocked()) {
                $minutesLeft = Carbon::now()->diffInMinutes($otpRecord->locked_until) + 1;
                return [
                    'success' => false,
                    'message' => "Too many failed attempts. Please try again in {$minutesLeft} minutes.",
                    'locked' => true,
                ];
            }

            // Check if expired
            if ($otpRecord->isExpired()) {
                Log::warning('OTP verification failed - Code expired', [
                    'otp_id' => $otpRecord->otp_id,
                    'email' => $otpRecord->email,
                    'expired_at' => $otpRecord->otp_expires_at
                ]);

                return [
                    'success' => false,
                    'message' => 'Verification code has expired. Please request a new code.',
                    'expired' => true,
                ];
            }

            // Verify OTP code
            if ($otpRecord->otp_code !== $otpCode) {
                $otpRecord->incrementAttempts();
                
                $remainingAttempts = (int) config('otp.max_attempts', 5) - $otpRecord->attempts;
                
                Log::warning('OTP verification failed - Invalid code', [
                    'otp_id' => $otpRecord->otp_id,
                    'email' => $otpRecord->email,
                    'attempts' => $otpRecord->attempts,
                    'remaining' => $remainingAttempts,
                    'ip' => $ipAddress
                ]);

                if ($remainingAttempts <= 0) {
                    return [
                        'success' => false,
                        'message' => 'Too many failed attempts. Your session has been locked.',
                        'locked' => true,
                    ];
                }

                return [
                    'success' => false,
                    'message' => "Invalid verification code. {$remainingAttempts} attempts remaining.",
                    'attempts_remaining' => $remainingAttempts,
                ];
            }

            // Mark as verified
            $otpRecord->markAsVerified();

            Log::info('OTP verified successfully', [
                'otp_id' => $otpRecord->otp_id,
                'email' => $otpRecord->email,
                'user_type' => $otpRecord->user_type,
                'attempts_used' => $otpRecord->attempts + 1,
                'ip' => $ipAddress
            ]);

            return [
                'success' => true,
                'message' => 'Email verified successfully!',
                'email' => $otpRecord->email,
                'user_type' => $otpRecord->user_type,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to verify OTP', [
                'session_token' => substr($sessionToken, 0, 10) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Verification failed. Please try again later.',
            ];
        }
    }

    /**
     * Resend OTP code
     *
     * @param string $sessionToken Session token from previous OTP request
     * @param string $userName User's name for email personalization
     * @param string|null $ipAddress Request IP address
     * @param string|null $userAgent Request user agent
     * @return array
     */
    public function resendOTP(
        string $sessionToken, 
        string $userName,
        ?string $ipAddress = null, 
        ?string $userAgent = null
    ): array {
        try {
            $otpRecord = RegistrationOTP::bySessionToken($sessionToken)->first();

            if (!$otpRecord) {
                return [
                    'success' => false,
                    'message' => 'Invalid session. Please start the registration process again.',
                ];
            }

            // Use the generateAndSendOTP method to handle resend logic
            return $this->generateAndSendOTP(
                $otpRecord->email,
                $userName,
                $otpRecord->user_type,
                $ipAddress,
                $userAgent
            );

        } catch (\Exception $e) {
            Log::error('Failed to resend OTP', [
                'session_token' => substr($sessionToken, 0, 10) . '...',
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to resend verification code. Please try again later.',
            ];
        }
    }

    /**
     * Cleanup expired and old OTPs
     *
     * @return int Number of deleted records
     */
    public function cleanupOldOTPs(): int
    {
        try {
            // Delete expired unverified OTPs
            $expiredCount = RegistrationOTP::expired()->delete();
            
            // Delete old verified OTPs (older than 7 days)
            $oldVerifiedCount = RegistrationOTP::oldVerified(7)->delete();
            
            $totalDeleted = $expiredCount + $oldVerifiedCount;
            
            if ($totalDeleted > 0) {
                Log::info('Cleaned up old OTP records', [
                    'expired_unverified' => $expiredCount,
                    'old_verified' => $oldVerifiedCount,
                    'total' => $totalDeleted
                ]);
            }
            
            return $totalDeleted;

        } catch (\Exception $e) {
            Log::error('Failed to cleanup old OTPs', [
                'error' => $e->getMessage()
            ]);
            
            return 0;
        }
    }

    /**
     * Check if email needs verification for registration
     *
     * @param string $email
     * @param string $userType
     * @return bool
     */
    public function needsVerification(string $email, string $userType): bool
    {
        $verifiedOTP = RegistrationOTP::where('email', $email)
            ->where('user_type', $userType)
            ->where('is_verified', true)
            ->where('verified_at', '>=', Carbon::now()->subHours(1)) // Valid for 1 hour
            ->first();
            
        return !$verifiedOTP;
    }

    /**
     * Get OTP service status
     *
     * @return array
     */
    public function getServiceStatus(): array
    {
        return [
            'email_enabled' => true,
            'email_configured' => config('mail.mailers.smtp.host') !== null,
            'otp_expiration_minutes' => (int) config('otp.expiration_minutes', 10),
            'max_attempts' => (int) config('otp.max_attempts', 5),
            'max_resends' => (int) config('otp.max_resends', 3),
        ];
    }
}
