<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Staff;
use Symfony\Component\HttpFoundation\Response;

class FinanceAccessMiddleware
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
        
        // Get user roles from session
        $userRoles = [];
        if ($userType === 'admin') {
            $userRoles = Session::get('admin_roles', []);
        } elseif ($userType === 'staff') {
            $userRoles = Session::get('staff_roles', []);
        }
        
        // Convert role names to lowercase for comparison
        $userRolesLower = array_map('strtolower', $userRoles);
        
        // Admins have access to all features including finance
        if (in_array('admin', $userRolesLower)) {
            return $next($request);
        }
        
        // Staff role has access to finance section only
        if (in_array('staff', $userRolesLower)) {
            return $next($request);
        }
        
        // No valid role found
        return redirect()->route('dashboard')
            ->with('error', 'Access denied. You do not have permission to access financial data.');
    }
}
