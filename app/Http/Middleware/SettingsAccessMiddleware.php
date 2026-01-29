<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SettingsAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userType = Session::get('user_type');
        
        // If no user type in session, redirect to login
        if (!$userType) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access this area.');
        }
        
        // Staff are blocked from settings sections
        if ($userType === 'staff') {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. Settings management is restricted to administrators only.');
        }
        
        // Admins can access settings
        if ($userType === 'admin') {
            return $next($request);
        }
        
        // Unknown user type
        return redirect()->route('login')
            ->with('error', 'Invalid user session. Please log in again.');
    }
}