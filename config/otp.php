<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for storing the configuration for email-based OTP
    | verification system for user registration.
    |
    */

    'length' => env('OTP_LENGTH', 6),
    
    'expiration_minutes' => env('OTP_EXPIRATION_MINUTES', 10),
    
    'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
    
    'lockout_minutes' => env('OTP_LOCKOUT_MINUTES', 15),
    
    'max_resends' => env('OTP_MAX_RESENDS', 3),
    
    'resend_cooldown_seconds' => env('OTP_RESEND_COOLDOWN_SECONDS', 60),
    
    'verification_validity_hours' => env('OTP_VERIFICATION_VALIDITY_HOURS', 1),
];
