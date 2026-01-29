<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if we have an admin session
        if (Session::has('admin_logged_in') && Session::has('admin_id')) {
            $adminId = Session::get('admin_id');
            $admin = Admin::find($adminId);
            
            // Verify the admin exists in the database
            if (!$admin) {
                Session::forget(['admin_logged_in', 'admin_id', 'admin_roles', 'admin_name', 'last_activity']);
                return redirect()->route('login')->with('error', 'Your session is invalid. Please log in again.');
            }
            
            // Check if the session has been inactive for too long (30 minutes)
            if (Session::has('last_activity') && (time() - Session::get('last_activity') > 1800)) {
                Session::forget(['admin_logged_in', 'admin_id', 'admin_roles', 'admin_name', 'last_activity']);
                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please log in again.');
            }
            
            // Update last activity time
            Session::put('last_activity', time());
        } 
        // Check if we have a staff session
        elseif (Session::has('staff_logged_in') && Session::has('staff_id')) {
            $staffId = Session::get('staff_id');
            $staff = \App\Models\Staff::find($staffId);
            
            // Verify the staff exists in the database
            if (!$staff) {
                Session::forget(['staff_logged_in', 'staff_id', 'staff_roles', 'staff_name', 'last_activity']);
                return redirect()->route('login')->with('error', 'Your session is invalid. Please log in again.');
            }
            
            // Check if the session has been inactive for too long (30 minutes)
            if (Session::has('last_activity') && (time() - Session::get('last_activity') > 1800)) {
                Session::forget(['staff_logged_in', 'staff_id', 'staff_roles', 'staff_name', 'last_activity']);
                return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please log in again.');
            }
            
            // Update last activity time
            Session::put('last_activity', time());
        } 
        // Neither admin nor staff is logged in
        else {
            return redirect()->route('login')->with('error', 'You must be logged in to access this area.');
        }
        
        return $next($request);
    }
}
