<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;
use App\Services\AuthenticationErrorMessages;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected ActivityLoggerService $activityLogger;
    
    public function __construct(ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login authentication with username/password for admin and staff
     */
    public function authenticate(Request $request)
    {
        Log::info('Authentication attempt started.', ['username' => $request->input('username')]);
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();
            $message = 'Please fill in all required fields correctly.';
            $errorType = 'validation_error';
            
            // Provide specific validation error messages with error types
            if (isset($errors['username'])) {
                $message = 'Please enter your username.';
                $errorType = 'invalid_username';
            } elseif (isset($errors['password'])) {
                $message = AuthenticationErrorMessages::MISSING_PASSWORD;
                $errorType = 'missing_password';
            } elseif (isset($errors['g-recaptcha-response'])) {
                $message = AuthenticationErrorMessages::CAPTCHA_REQUIRED;
                $errorType = 'captcha_required';
            }
            
            // Check if this is an AJAX request for validation errors
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'locked' => false,
                    'error_type' => $errorType,
                    'message' => $message,
                    'errors' => $errors
                ], 422);
            }

            // For regular HTTP requests, redirect back with errors
            return back()->withErrors($errors)->withInput()->with('error', $message);
        }

        // Check if the user is locked out due to too many failed attempts
        $username = $request->input('username');
        $lockoutKey = 'login_lockout_' . Str::slug($username);
        
        if (Session::has($lockoutKey)) {
            $lockoutData = Session::get($lockoutKey);
            $lockoutUntil = $lockoutData['until'];
            if (now()->lt($lockoutUntil)) {
                $timeRemaining = (int) ceil(now()->diffInSeconds($lockoutUntil));
                $lockoutMessage = AuthenticationErrorMessages::getLockoutMessage($timeRemaining);
                
                // Check if this is an AJAX request for lockout
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'locked' => true,
                        'lockout_seconds' => $timeRemaining,
                        'message' => $lockoutMessage
                    ], 423);
                }

                // For regular HTTP requests, redirect back with lockout message
                return back()->withInput()->with('error', $lockoutMessage);
            } else {
                // Lockout period is over, remove the key
                Session::forget($lockoutKey);
                Session::forget('login_attempts_' . Str::slug($username));
            }
        }

        try {
            // Automatic role detection - try admin first, then staff
            $password = $request->input('password');
            $authResult = null;
            $localUser = null;
            
            // Try admin authentication first - check both username and email
            $adminUser = Admin::with('roles')
                ->where(function($query) use ($username) {
                    $query->where('username', $username)
                          ->orWhere('email', $username);
                })
                ->first();
            
            if ($adminUser && Hash::check($password, $adminUser->password_hash)) {
                // Check if admin account is active
                if (!$adminUser->is_active) {
                    $authResult = [
                        'success' => false,
                        'error_type' => 'account_inactive',
                        'message' => 'Your account is inactive. Please contact the administrator.'
                    ];
                } else {
                    $authResult = [
                        'success' => true,
                        'local_user' => $adminUser,
                        'user_type' => 'admin'
                    ];
                }
            } else {
                // Try staff authentication - check both username and email
                $localUser = Staff::with('roles')
                    ->where(function($query) use ($username) {
                        $query->where('username', $username)
                              ->orWhere('email', $username);
                    })
                    ->first();
                
                if ($localUser && Hash::check($password, $localUser->password_hash)) {
                    // Check if staff account is active
                    if ($localUser->status !== 'active') {
                        $authResult = [
                            'success' => false,
                            'error_type' => 'account_inactive',
                            'message' => 'Your account is inactive. Please contact the administrator.'
                        ];
                    } else {
                        $authResult = [
                            'success' => true,
                            'local_user' => $localUser,
                            'user_type' => 'staff'
                        ];
                    }
                } else {
                    // Neither admin nor valid staff
                    $authResult = [
                        'success' => false,
                        'error_type' => 'invalid_credentials',
                        'message' => 'Invalid username or password.'
                    ];
                }
            }
            
            if (!$authResult['success']) {
                // Login failed, increment failed attempts counter
                $attemptKey = 'login_attempts_' . Str::slug($username);
                $attempts = Session::get($attemptKey, 0) + 1;
                Session::put($attemptKey, $attempts);
                
                // Determine the specific error message based on error type and user type selection
                $errorMessage = $authResult['message'] ?? 'Invalid username or password.';
                
                // Progressive lockout: 30 seconds for 1st fail, +5 seconds for each additional fail
                // 1st fail: 30s, 2nd: 35s, 3rd: 40s, 4th: 45s, 5th: 50s
                if ($attempts >= 5) {
                    $lockoutSeconds = 25 + (5 * $attempts); // 30, 35, 40, 45, 50 seconds
                    $lockoutUntil = now()->addSeconds($lockoutSeconds);
                    
                    Session::put($lockoutKey, [
                        'until' => $lockoutUntil,
                        'attempts' => $attempts
                    ]);
                    
                    // Log the lockout
                    Log::warning('Account locked due to failed login attempts', [
                        'username' => $username,
                        'ip' => $request->ip(),
                        'attempts' => $attempts,
                        'lockout_seconds' => $lockoutSeconds,
                        'locked_until' => $lockoutUntil,
                        'error_type' => $authResult['error_type'] ?? 'unknown'
                    ]);
                    
                    $lockoutMessage = AuthenticationErrorMessages::getTooManyAttemptsMessage($lockoutSeconds);
                    
                    // Check if this is an AJAX request for lockout
                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'locked' => true,
                            'lockout_seconds' => $lockoutSeconds,
                            'attempts' => $attempts,
                            'message' => $lockoutMessage
                        ], 423);
                    }

                    // For regular HTTP requests, redirect back with lockout message
                    return back()->withInput()->with('error', $lockoutMessage);
                }

                // Log the failed login attempt with error type
                Log::info('Failed login attempt', [
                    'username' => $username, 
                    'ip' => $request->ip(), 
                    'attempt' => $attempts,
                    'error_type' => $authResult['error_type'] ?? 'unknown'
                ]);

                // Log to activity logs
                $this->activityLogger->logFailedLogin($username, "Failed login attempt: " . $errorMessage);

                // Check if this is an AJAX request for failed login
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'locked' => false,
                        'attempts' => $attempts,
                        'error_type' => $authResult['error_type'] ?? 'unknown',
                        'message' => $errorMessage
                    ], 401);
                }

                // For regular HTTP requests, redirect back with error
                return back()->withInput()->with('error', $errorMessage);
            }

            // Login successful, reset any failed attempt counters
            Session::forget('login_attempts_' . Str::slug($username));
            Session::forget($lockoutKey);
            
            $localUser = $authResult['local_user'];
            $authenticatedUserType = $authResult['user_type'];
            
            // Automatically log in user based on their detected role
            if ($authenticatedUserType === 'admin') {
                // Get admin's roles (handle case where admin has no roles)
                $roles = $localUser->roles ? $localUser->roles->pluck('role_name')->toArray() : [];
                
                // Store admin session information
                Session::put('admin_logged_in', true);
                Session::put('admin_id', $localUser->admin_id);
                Session::put('admin_roles', $roles);
                Session::put('admin_name', $localUser->first_name . ' ' . $localUser->last_name);
                Session::put('user_type', 'admin');
                Session::put('last_activity', time());
                
                // Explicitly save session to ensure persistence
                Session::save();
                
                // Update last login (using 'last_login_at' column for admin_users table)
                $localUser->update(['last_login_at' => now()]);
                
                // Log the successful login
                Log::info('Admin login successful', [
                    'admin_id' => $localUser->admin_id, 
                    'username' => $localUser->username,
                    'ip' => $request->ip(),
                    'roles' => $roles,
                    'session_data' => [
                        'admin_logged_in' => Session::get('admin_logged_in'),
                        'admin_roles' => Session::get('admin_roles'),
                        'user_type' => Session::get('user_type')
                    ]
                ]);

                // Log to activity logs
                $this->activityLogger->logLogin('admin', $localUser->admin_id, "Admin logged in successfully");
                
            } elseif ($authenticatedUserType === 'staff') {
                // Get staff's roles (handle case where staff has no roles)
                $roles = $localUser->roles ? $localUser->roles->pluck('role_name')->toArray() : [];
                
                // Store staff session information
                Session::put('staff_logged_in', true);
                Session::put('staff_id', $localUser->staff_id);
                Session::put('staff_roles', $roles);
                Session::put('staff_name', $localUser->first_name . ' ' . $localUser->last_name);
                Session::put('user_type', 'staff');
                Session::put('last_activity', time());
                
                // Update last login
                $localUser->update(['last_login_at' => now()]);
                
                // Log the successful login
                Log::info('Staff login successful', [
                    'staff_id' => $localUser->staff_id,
                    'username' => $localUser->username,
                    'ip' => $request->ip(),
                    'roles' => $roles
                ]);

                // Log to activity logs
                $this->activityLogger->logLogin('staff', $localUser->staff_id, "Staff logged in successfully");
            }
            
            // IMPORTANT: Do NOT call session()->regenerate() here
            // It causes session data loss with database driver
            // Session fixation is already prevented by Laravel's session middleware
            // which automatically regenerates the token on login

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => url('dashboard'),
                    'message' => 'Login successful'
                ]);
            }

            // For regular HTTP requests, redirect directly
            return redirect()->intended('dashboard')->with('success', 'Login successful');
            
        } catch (\Exception $e) {
            Log::error('Authentication system error in controller', [
                'username' => $username ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            $systemErrorMessage = 'A system error occurred. Please try again later.';
            
            // Check if this is an AJAX request for system error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'locked' => false,
                    'error_type' => 'system_error',
                    'message' => $systemErrorMessage,
                    'debug' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            // For regular HTTP requests, redirect back with error
            return back()->withInput()->with('error', $systemErrorMessage);
        }
    }

    /**
     * Check lockout status for AJAX requests
     */
    public function checkLockoutStatus(Request $request)
    {
        $username = $request->input('username');
        if (!$username) {
            return response()->json(['locked' => false]);
        }

        $lockoutKey = 'login_lockout_' . Str::slug($username);
        
        if (Session::has($lockoutKey)) {
            $lockoutData = Session::get($lockoutKey);
            $lockoutUntil = $lockoutData['until'];
            if (now()->lt($lockoutUntil)) {
                $timeRemaining = (int) ceil(now()->diffInSeconds($lockoutUntil));
                return response()->json([
                    'locked' => true,
                    'lockout_seconds' => $timeRemaining,
                    'attempts' => $lockoutData['attempts'] ?? 0
                ]);
            } else {
                // Lockout period is over, remove the key
                Session::forget($lockoutKey);
                Session::forget('login_attempts_' . Str::slug($username));
            }
        }

        return response()->json(['locked' => false]);
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        // Get user info before clearing session for activity logging
        $userType = Session::get('user_type');
        $userId = null;
        
        if ($userType === 'admin') {
            $userId = Session::get('admin_id');
        } elseif ($userType === 'staff') {
            $userId = Session::get('staff_id');
        }

        // Log logout activity before clearing session
        if ($userType && $userId) {
            $this->activityLogger->logAction('logout', ucfirst($userType) . " logged out successfully");
        }

        // Clear user authentication
        Auth::logout();
        
        // Clear all session data
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear specific session variables for both admin and staff
        Session::forget([
            'admin_logged_in', 
            'admin_id', 
            'admin_roles', 
            'admin_name',
            'staff_logged_in',
            'staff_id',
            'staff_roles',
            'staff_name',
            'user_type',
            'last_activity'
        ]);

        // Redirect to login page
        return redirect()->route('login')
            ->with('success', 'You have been successfully logged out.');
    }
}
