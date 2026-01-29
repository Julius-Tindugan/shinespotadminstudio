<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RegistrationRateLimiter
{
    /**
     * Handle an incoming registration request with lenient rate limiting.
     *
     * This middleware protects against:
     * - DDoS attacks by limiting registration attempts per IP
     * - Automated bot registrations
     * - Brute force registration attempts
     * 
     * Lenient settings:
     * - 10 attempts per hour per IP address
     * - 3 attempts per 15 minutes per email address
     * - Progressive lockout (increases with repeated violations)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $email = strtolower($request->input('email', 'unknown'));
        
        // Create rate limiter keys
        $ipKey = 'registration:ip:' . $ip;
        $emailKey = 'registration:email:' . $email;
        $globalKey = 'registration:global';
        
        // Check if IP is in suspicious activity lockdown
        if ($this->isInSuspiciousLockdown($ip)) {
            $lockoutTime = $this->getSuspiciousLockoutTime($ip);
            
            return $this->createLockoutResponse(
                'Your IP address has been temporarily blocked due to suspicious activity. Please try again later.',
                $lockoutTime
            );
        }
        
        // Check IP-based rate limit (10 attempts per hour)
        if (RateLimiter::tooManyAttempts($ipKey, 10)) {
            $seconds = RateLimiter::availableIn($ipKey);
            
            // Track repeated violations for progressive lockout
            $this->trackSuspiciousActivity($ip);
            
            \Log::warning('Registration rate limit exceeded for IP', [
                'ip' => $ip,
                'email' => $email,
                'available_in' => $seconds
            ]);
            
            return $this->createLockoutResponse(
                'Too many registration attempts from your IP address. Please try again later.',
                $seconds
            );
        }
        
        // Check email-based rate limit (3 attempts per 15 minutes)
        if ($email !== 'unknown' && RateLimiter::tooManyAttempts($emailKey, 3)) {
            $seconds = RateLimiter::availableIn($emailKey);
            
            \Log::warning('Registration rate limit exceeded for email', [
                'ip' => $ip,
                'email' => $email,
                'available_in' => $seconds
            ]);
            
            return $this->createLockoutResponse(
                'Too many registration attempts with this email address. Please try again later.',
                $seconds
            );
        }
        
        // Check global rate limit (100 registrations per hour system-wide)
        if (RateLimiter::tooManyAttempts($globalKey, 100)) {
            \Log::alert('Global registration rate limit exceeded', [
                'ip' => $ip,
                'email' => $email
            ]);
            
            return $this->createLockoutResponse(
                'The system is currently experiencing high traffic. Please try again in a few minutes.',
                60
            );
        }
        
        // Increment rate limiters (15 minutes for email, 1 hour for IP and global)
        RateLimiter::hit($ipKey, 3600); // 1 hour
        RateLimiter::hit($emailKey, 900); // 15 minutes
        RateLimiter::hit($globalKey, 3600); // 1 hour
        
        // Log registration attempt
        \Log::info('Registration attempt', [
            'ip' => $ip,
            'email' => $email,
            'user_agent' => $request->userAgent()
        ]);
        
        return $next($request);
    }
    
    /**
     * Check if IP is in suspicious activity lockdown
     */
    private function isInSuspiciousLockdown(string $ip): bool
    {
        return Cache::has('suspicious:lockdown:' . $ip);
    }
    
    /**
     * Get remaining lockout time for suspicious IP
     */
    private function getSuspiciousLockoutTime(string $ip): int
    {
        $key = 'suspicious:lockdown:' . $ip;
        $ttl = Cache::get($key . ':ttl', 3600);
        return max(1, $ttl);
    }
    
    /**
     * Track suspicious activity and implement progressive lockout
     */
    private function trackSuspiciousActivity(string $ip): void
    {
        $violationKey = 'suspicious:violations:' . $ip;
        $lockdownKey = 'suspicious:lockdown:' . $ip;
        
        // Increment violation count
        $violations = Cache::get($violationKey, 0) + 1;
        Cache::put($violationKey, $violations, now()->addHours(24));
        
        // Progressive lockout based on violations
        if ($violations >= 5) {
            // 5+ violations in 24h = 24 hour lockout
            Cache::put($lockdownKey, true, now()->addHours(24));
            Cache::put($lockdownKey . ':ttl', 86400, now()->addHours(24));
            
            \Log::alert('IP placed in 24-hour lockdown due to repeated violations', [
                'ip' => $ip,
                'violations' => $violations
            ]);
        } elseif ($violations >= 3) {
            // 3-4 violations in 24h = 6 hour lockout
            Cache::put($lockdownKey, true, now()->addHours(6));
            Cache::put($lockdownKey . ':ttl', 21600, now()->addHours(6));
            
            \Log::warning('IP placed in 6-hour lockdown due to violations', [
                'ip' => $ip,
                'violations' => $violations
            ]);
        }
    }
    
    /**
     * Create a standardized lockout response
     */
    private function createLockoutResponse(string $message, int $seconds): Response
    {
        $minutes = ceil($seconds / 60);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'locked' => true,
                'lockout_seconds' => $seconds,
                'lockout_minutes' => $minutes,
                'message' => $message
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        return back()
            ->withInput(request()->except('password', 'password_confirmation'))
            ->with('lockout_seconds', $seconds)
            ->withErrors([
                'rate_limit' => $message . " (Please wait approximately {$minutes} minute(s))"
            ])
            ->setStatusCode(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
