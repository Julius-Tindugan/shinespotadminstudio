<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ManagementAccessMiddleware
{
    /**
     * Handle an incoming request.
     * This middleware allows both admin and staff to access management systems
     * except for finance and settings which have their own restrictions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userType = Session::get('user_type');
        
        // If no user type in session, check alternative session keys
        if (!$userType) {
            // Check if we have admin or staff logged in flags
            if (Session::has('admin_logged_in') && Session::get('admin_logged_in') === true) {
                // Restore missing user_type for admin
                Session::put('user_type', 'admin');
                Session::save(); // Explicitly save session
                $userType = 'admin';
                \Log::info('Restored missing user_type for admin', [
                    'admin_id' => Session::get('admin_id'),
                    'session_keys' => array_keys(Session::all())
                ]);
            } elseif (Session::has('staff_logged_in') && Session::get('staff_logged_in') === true) {
                // Restore missing user_type for staff
                Session::put('user_type', 'staff');
                Session::save(); // Explicitly save session
                $userType = 'staff';
                \Log::info('Restored missing user_type for staff', [
                    'staff_id' => Session::get('staff_id'),
                    'session_keys' => array_keys(Session::all())
                ]);
            } else {
                // Log session state for debugging
                \Log::warning('No user_type and no login flags in session', [
                    'url' => $request->url(),
                    'session_keys' => array_keys(Session::all()),
                    'has_admin_login' => Session::has('admin_logged_in'),
                    'has_staff_login' => Session::has('staff_logged_in')
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Please log in to access this area.');
            }
        }
        
        // Allow access based on user type directly
        // Both admin and staff users should have access to management systems
        if ($userType === 'admin' || $userType === 'staff') {
            return $next($request);
        }
        
        // No valid user type found
        return redirect()->route('dashboard')
            ->with('error', 'Access denied. You do not have permission to access this management section.');
    }
}