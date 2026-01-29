<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Create a rate limiter key based on IP and email
        $key = Str::lower($request->input('email')) . '|' . $request->ip();
        
        // Check if rate limit is exceeded (5 attempts per minute)
        if (RateLimiter::tooManyAttempts($key, 5)) {
            // Get the number of seconds until the user can attempt another login
            $seconds = (int) ceil(RateLimiter::availableIn($key));
            
            return response()->json([
                'success' => false,
                'locked' => true,
                'lockout_seconds' => $seconds,
                'message' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        // Increment the rate limiter counter
        RateLimiter::hit($key);
        
        return $next($request);
    }
}
