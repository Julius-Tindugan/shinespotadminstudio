<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Centralized Security Service
 * 
 * Provides comprehensive security measures against:
 * - SQL Injection
 * - XSS (Cross-Site Scripting)
 * - CSRF (Cross-Site Request Forgery)
 * - Data Poisoning
 * - Session Hijacking
 * - Brute Force Attacks
 */
class SecurityService
{
    /**
     * Sanitize input to prevent XSS attacks
     * 
     * @param string|array $input
     * @return string|array
     */
    public function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        
        // Remove HTML tags and encode special characters
        $sanitized = htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
        
        // Remove potentially dangerous characters
        $sanitized = preg_replace('/[^\p{L}\p{N}\s@._-]/u', '', $sanitized);
        
        return $sanitized;
    }
    
    /**
     * Validate and sanitize email address
     * 
     * @param string $email
     * @return string|null
     */
    public function sanitizeEmail(string $email): ?string
    {
        $email = strtolower(trim($email));
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        
        // Check for suspicious patterns
        if ($this->hasSuspiciousPattern($email)) {
            Log::warning('Suspicious email pattern detected', ['email' => $email]);
            return null;
        }
        
        return $email;
    }
    
    /**
     * Check for suspicious patterns in input
     * 
     * @param string $input
     * @return bool
     */
    public function hasSuspiciousPattern(string $input): bool
    {
        $suspiciousPatterns = [
            // SQL injection patterns
            '/(\bunion\b|\bselect\b|\binsert\b|\bupdate\b|\bdelete\b|\bdrop\b|\balter\b)/i',
            '/(\bexec\b|\bexecute\b|\bscript\b)/i',
            // XSS patterns
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i', // Event handlers like onclick=
            // Path traversal
            '/\.\.[\/\\\\]/',
            // NULL byte injection
            '/\x00/',
            // Command injection
            '/[;&|`$(){}]/i',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Validate form timing to prevent automated bots
     * Bots typically submit forms too quickly
     * 
     * @param int $timestamp Form load timestamp
     * @param int $minSeconds Minimum seconds required (default: 3)
     * @param int $maxSeconds Maximum seconds allowed (default: 3600 = 1 hour)
     * @return bool
     */
    public function validateFormTiming(int $timestamp, int $minSeconds = 3, int $maxSeconds = 3600): bool
    {
        $currentTime = time();
        $elapsedTime = $currentTime - $timestamp;
        
        // Too fast - likely a bot
        if ($elapsedTime < $minSeconds) {
            Log::warning('Form submitted too quickly (bot detection)', [
                'elapsed_time' => $elapsedTime,
                'timestamp' => $timestamp
            ]);
            return false;
        }
        
        // Too slow - token might be stale or replayed
        if ($elapsedTime > $maxSeconds) {
            Log::warning('Form submission timeout (potential replay attack)', [
                'elapsed_time' => $elapsedTime,
                'timestamp' => $timestamp
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate a secure form token
     * 
     * @param string $purpose
     * @return string
     */
    public function generateFormToken(string $purpose = 'registration'): string
    {
        $token = Str::random(64);
        session()->put("form_token_{$purpose}", $token);
        session()->put("form_timestamp_{$purpose}", time());
        
        return $token;
    }
    
    /**
     * Validate form token to prevent CSRF
     * 
     * @param string $token
     * @param string $purpose
     * @param bool $clearToken Whether to clear the token after validation (default: false)
     * @return bool
     */
    public function validateFormToken(string $token, string $purpose = 'registration', bool $clearToken = false): bool
    {
        $sessionToken = session()->get("form_token_{$purpose}");
        $timestamp = session()->get("form_timestamp_{$purpose}");
        
        // Token doesn't exist in session
        if (!$sessionToken || !$timestamp) {
            Log::warning('Form token not found in session', ['purpose' => $purpose]);
            return false;
        }
        
        // Token doesn't match
        if (!hash_equals($sessionToken, $token)) {
            Log::warning('Form token mismatch (CSRF attempt)', [
                'purpose' => $purpose,
                'ip' => request()->ip()
            ]);
            return false;
        }
        
        // Validate timing
        if (!$this->validateFormTiming($timestamp)) {
            return false;
        }
        
        // Clear token after validation only if requested (e.g., on successful registration)
        if ($clearToken) {
            session()->forget("form_token_{$purpose}");
            session()->forget("form_timestamp_{$purpose}");
        }
        
        return true;
    }
    
    /**
     * Validate honeypot field to detect bots
     * Honeypot is a hidden field that should remain empty
     * 
     * @param mixed $honeypotValue
     * @return bool
     */
    public function validateHoneypot($honeypotValue): bool
    {
        if (!empty($honeypotValue)) {
            Log::warning('Honeypot field filled (bot detected)', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Clear form token from session
     * Should be called after successful form processing
     * 
     * @param string $purpose
     * @return void
     */
    public function clearFormToken(string $purpose = 'registration'): void
    {
        session()->forget("form_token_{$purpose}");
        session()->forget("form_timestamp_{$purpose}");
    }
    
    /**
     * Regenerate session ID to prevent session fixation
     * Should be called after successful login/registration
     */
    public function regenerateSession(): void
    {
        session()->regenerate();
        session()->put('last_regeneration', time());
        
        Log::info('Session regenerated', [
            'session_id' => session()->getId(),
            'ip' => request()->ip()
        ]);
    }
    
    /**
     * Validate session integrity to prevent session hijacking
     * 
     * @return bool
     */
    public function validateSessionIntegrity(): bool
    {
        $currentFingerprint = $this->generateSessionFingerprint();
        $storedFingerprint = session()->get('session_fingerprint');
        
        if (!$storedFingerprint) {
            // First time - store fingerprint
            session()->put('session_fingerprint', $currentFingerprint);
            return true;
        }
        
        // Validate fingerprint
        if (!hash_equals($storedFingerprint, $currentFingerprint)) {
            Log::alert('Session fingerprint mismatch (session hijacking attempt)', [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate session fingerprint based on user characteristics
     * 
     * @return string
     */
    private function generateSessionFingerprint(): string
    {
        $components = [
            request()->ip(),
            request()->userAgent(),
            // Note: Not including Accept-Language as it can change
        ];
        
        return hash('sha256', implode('|', $components));
    }
    
    /**
     * Validate IP address format and detect suspicious IPs
     * 
     * @param string $ip
     * @return bool
     */
    public function validateIpAddress(string $ip): bool
    {
        // Validate IP format
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        
        // Check if IP is from private/reserved range (potential proxy/VPN abuse)
        // This is informational - we don't block private IPs in development
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            // Public IP - continue
        } else {
            Log::info('Private/Reserved IP detected', ['ip' => $ip]);
        }
        
        return true;
    }
    
    /**
     * Secure database query execution using prepared statements
     * All Laravel Eloquent and Query Builder methods use prepared statements by default
     * This method is for explicit verification and logging
     * 
     * @param string $query
     * @param array $bindings
     * @return mixed
     */
    public function executeSecureQuery(string $query, array $bindings = [])
    {
        // Log query for audit trail (without sensitive data)
        Log::info('Executing secure database query', [
            'query_hash' => hash('sha256', $query),
            'binding_count' => count($bindings)
        ]);
        
        try {
            // Execute with prepared statement (prevents SQL injection)
            return DB::select($query, $bindings);
        } catch (\Exception $e) {
            Log::error('Database query execution failed', [
                'error' => $e->getMessage(),
                'query_hash' => hash('sha256', $query)
            ]);
            throw $e;
        }
    }
    
    /**
     * Validate password strength beyond basic requirements
     * 
     * @param string $password
     * @return array ['valid' => bool, 'issues' => array]
     */
    public function validatePasswordStrength(string $password): array
    {
        $issues = [];
        
        // Check for common passwords
        $commonPasswords = ['password', '12345678', 'qwerty', 'admin123', 'letmein'];
        if (in_array(strtolower($password), $commonPasswords)) {
            $issues[] = 'Password is too common';
        }
        
        // Check for sequential characters
        if (preg_match('/(?:abc|bcd|cde|def|efg|fgh|ghi|hij|ijk|jkl|klm|lmn|mno|nop|opq|pqr|qrs|rst|stu|tuv|uvw|vwx|wxy|xyz|012|123|234|345|456|567|678|789)/i', $password)) {
            $issues[] = 'Password contains sequential characters';
        }
        
        // Check for repeated characters
        if (preg_match('/(.)\1{2,}/', $password)) {
            $issues[] = 'Password contains too many repeated characters';
        }
        
        return [
            'valid' => empty($issues),
            'issues' => $issues
        ];
    }
    
    /**
     * Detect and log potential data poisoning attempts
     * 
     * @param array $data
     * @return bool
     */
    public function detectDataPoisoning(array $data): bool
    {
        $suspiciousIndicators = 0;
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                // Check for suspicious patterns
                if ($this->hasSuspiciousPattern($value)) {
                    $suspiciousIndicators++;
                }
                
                // Check for excessive length
                if (strlen($value) > 1000) {
                    $suspiciousIndicators++;
                }
                
                // Check for encoded payloads
                if (preg_match('/base64|eval|exec|system/i', $value)) {
                    $suspiciousIndicators++;
                }
            }
        }
        
        if ($suspiciousIndicators >= 2) {
            Log::alert('Potential data poisoning attempt detected', [
                'ip' => request()->ip(),
                'suspicious_indicators' => $suspiciousIndicators,
                'data_keys' => array_keys($data)
            ]);
            return true;
        }
        
        return false;
    }
}
