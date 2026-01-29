<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\Staff;
use App\Services\AuthenticationErrorMessages;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for inactivity timeout (30 minutes)
        if (Session::has('last_activity')) {
            $lastActivity = Session::get('last_activity');
            
            // Handle both Carbon objects and timestamps
            if ($lastActivity instanceof \Carbon\Carbon) {
                $inactive = now()->diffInSeconds($lastActivity) > 1800;
            } else {
                $inactive = (time() - $lastActivity) > 1800;
            }
            
            if ($inactive) {
                // Clear all session data
                Session::flush();
                
                return redirect()->route('login')
                    ->with('error', AuthenticationErrorMessages::SESSION_EXPIRED);
            }
        }
        
        // Update last activity time (use timestamp for consistency)
        Session::put('last_activity', time());
        
        // Check if admin is logged in
        if (Session::has('admin_logged_in') && Session::get('admin_logged_in') === true) {
            $adminId = Session::get('admin_id');
            $admin = Admin::find($adminId);
            
            if (!$admin) {
                Session::flush();
                return redirect()->route('login')
                    ->with('error', AuthenticationErrorMessages::SESSION_EXPIRED);
            }
            
            // Ensure user_type is set for consistency across middlewares
            if (!Session::has('user_type')) {
                Session::put('user_type', 'admin');
                Session::save(); // Explicitly save session
            }
            
            // Admin is authenticated, proceed
            return $next($request);
        } 
        // Check if staff is logged in
        elseif (Session::has('staff_logged_in') && Session::get('staff_logged_in') === true) {
            $staffId = Session::get('staff_id');
            $staff = Staff::find($staffId);
            
            if (!$staff) {
                Session::flush();
                return redirect()->route('login')
                    ->with('error', AuthenticationErrorMessages::SESSION_EXPIRED);
            }
            
            // Ensure user_type is set for consistency across middlewares
            if (!Session::has('user_type')) {
                Session::put('user_type', 'staff');
                Session::save(); // Explicitly save session
            }
            
            // Staff is authenticated, proceed
            return $next($request);
        }
        
        // No valid authentication found
        return redirect()->route('login')
            ->with('error', 'Please log in to access this area.');
    }
}
