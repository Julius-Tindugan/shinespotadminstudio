<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $userType = Session::get('user_type');
        
        // If no user type in session, redirect to login
        if (!$userType) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access this area.');
        }
        
        // Get user roles from session
        $userRoles = [];
        if ($userType === 'admin') {
            $userRoles = Session::get('admin_roles', []);
        } elseif ($userType === 'staff') {
            $userRoles = Session::get('staff_roles', []);
        }
        
        // Convert role names to lowercase for comparison
        $userRolesLower = array_map('strtolower', $userRoles);
        $requiredRoleLower = strtolower($role);
        
        // Admin role has access to everything
        if (in_array('admin', $userRolesLower)) {
            return $next($request);
        }
        
        // Staff role can only access finance
        if (in_array('staff', $userRolesLower)) {
            // Staff can only access if the required role is 'staff' or 'finance'
            if ($requiredRoleLower === 'staff' || $requiredRoleLower === 'finance') {
                return $next($request);
            }
            
            // Deny access to admin-only sections
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This section is restricted to administrators only.');
        }
        
        // Check if user has the specific required role
        if (in_array($requiredRoleLower, $userRolesLower)) {
            return $next($request);
        }
        
        // User doesn't have required role
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access that resource.');
    }
}
