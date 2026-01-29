<?php

namespace App\Services;

class AuthenticationErrorMessages
{
    // User not found errors
    const EMAIL_NOT_FOUND = 'No account found with this email address. Please verify your email or contact an administrator.';
    const ADMIN_NOT_FOUND = 'No admin account found with this email address. Please verify your credentials or contact support.';
    const STAFF_NOT_FOUND = 'No staff account found with this email address. Please verify your credentials or contact your administrator.';
    
    // Password errors
    const INVALID_PASSWORD = 'The password you entered is incorrect. Please check your password and try again.';
    const INVALID_CREDENTIALS = 'The email or password you entered is incorrect. Please check your credentials and try again.';
    
    // Account status errors
    const ADMIN_ACCOUNT_INACTIVE = 'Your admin account is currently inactive. Please contact the system administrator for assistance.';
    const STAFF_ACCOUNT_INACTIVE = 'Your staff account is currently inactive. Please contact your administrator or HR department.';
    const ACCOUNT_DISABLED = 'Your account has been disabled. Please contact support for assistance.';
    
    // User type mismatch errors
    const WRONG_USER_TYPE_ADMIN = 'This email is registered as an admin account. Please select "Admin" as your login type.';
    const WRONG_USER_TYPE_STAFF = 'This email is registered as a staff account. Please select "Staff" as your login type.';
    const USER_TYPE_MISMATCH = 'The selected user type does not match your account. Please verify your login type.';
    
    // Account lockout and security
    const ACCOUNT_LOCKED = 'Your account has been temporarily locked due to multiple failed login attempts. Please try again in :seconds seconds.';
    const TOO_MANY_ATTEMPTS = 'Too many failed login attempts. Your account has been locked for :seconds seconds for security.';
    const PROGRESSIVE_LOCKOUT = 'Multiple failed attempts detected. Account locked for :seconds seconds. Attempt :attempt of 5.';
    
    // System and technical errors
    const FIREBASE_CONNECTION_ERROR = 'Unable to connect to authentication service. Please try again in a few moments.';
    const DATABASE_CONNECTION_ERROR = 'Database connection error. Please try again later or contact support.';
    const AUTHENTICATION_SERVICE_ERROR = 'Authentication service is temporarily unavailable. Please try again later.';
    const SYSTEM_ERROR = 'A system error occurred during login. Please try again or contact support if the issue persists.';
    
    // Validation errors
    const INVALID_EMAIL_FORMAT = 'Please enter a valid email address.';
    const MISSING_PASSWORD = 'Please enter your password.';
    const MISSING_USER_TYPE = 'Please select whether you are logging in as Admin or Staff.';
    const CAPTCHA_REQUIRED = 'Please complete the security verification (reCAPTCHA).';
    
    // Session and security
    const SESSION_EXPIRED = 'Your session has expired. Please log in again.';
    const CONCURRENT_LOGIN = 'Another session is active for this account. Please try again or contact support.';
    
    /**
     * Get error message for email not found based on user type attempted
     */
    public static function getEmailNotFoundMessage(string $attemptedUserType = null): string
    {
        switch ($attemptedUserType) {
            case 'admin':
                return self::ADMIN_NOT_FOUND;
            case 'staff':
                return self::STAFF_NOT_FOUND;
            default:
                return self::EMAIL_NOT_FOUND;
        }
    }
    
    /**
     * Get account inactive message based on user type
     */
    public static function getAccountInactiveMessage(string $userType): string
    {
        return $userType === 'admin' ? self::ADMIN_ACCOUNT_INACTIVE : self::STAFF_ACCOUNT_INACTIVE;
    }
    
    /**
     * Get wrong user type message 
     */
    public static function getWrongUserTypeMessage(string $actualUserType): string
    {
        return $actualUserType === 'admin' ? self::WRONG_USER_TYPE_ADMIN : self::WRONG_USER_TYPE_STAFF;
    }
    
    /**
     * Get lockout message with time replacement
     */
    public static function getLockoutMessage(int $seconds, int $attempt = null): string
    {
        if ($attempt !== null) {
            return str_replace([':seconds', ':attempt'], [$seconds, $attempt], self::PROGRESSIVE_LOCKOUT);
        }
        return str_replace(':seconds', $seconds, self::ACCOUNT_LOCKED);
    }
    
    /**
     * Get too many attempts message with time replacement
     */
    public static function getTooManyAttemptsMessage(int $seconds): string
    {
        return str_replace(':seconds', $seconds, self::TOO_MANY_ATTEMPTS);
    }
}