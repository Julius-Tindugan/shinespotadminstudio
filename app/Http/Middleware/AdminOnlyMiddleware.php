<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminOnlyMiddleware
{
    /**
     * Handle an incoming request.
     * This middleware ensures only admin role users can access certain routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userType = Session::get('user_type');
        
        // If no user type in session, redirect to login
        if (!$userType) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access this area.');
        }
        
        // Staff users are immediately denied
        if ($userType === 'staff') {
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This section is restricted to administrators only.');
        }
        
        // For admin user type, check roles
        if ($userType === 'admin') {
            $userRoles = Session::get('admin_roles', []);
            
            // Convert role names to lowercase for comparison
            $userRolesLower = array_map('strtolower', $userRoles);
            
            // Allow access if user has admin role
            if (in_array('admin', $userRolesLower)) {
                return $next($request);
            }
        }
        
        // Deny access for non-admin users
        return redirect()->route('dashboard')
            ->with('error', 'Access denied. This section is restricted to administrators only.');
    }
}