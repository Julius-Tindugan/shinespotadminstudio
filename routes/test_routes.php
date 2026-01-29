<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// Debug route for testing account update functionality
Route::get('/debug-account-update', function () {
    $adminId = Session::get('admin_id');
    $userType = Session::get('user_type');
    
    // Get admin from database
    $admin = null;
    if ($adminId) {
        $admin = DB::table('admin_users')->where('admin_id', $adminId)->first();
    }
    
    return response()->json([
        'session' => [
            'admin_id' => $adminId,
            'user_type' => $userType,
            'admin_logged_in' => Session::get('admin_logged_in'),
        ],
        'admin' => $admin ? [
            'id' => $admin->admin_id,
            'username' => $admin->username,
            'has_password_hash' => !empty($admin->password_hash),
            'password_hash_length' => strlen($admin->password_hash ?? ''),
            'password_changed_at' => $admin->password_changed_at ?? 'never',
        ] : null,
        'config' => [
            'app_url' => config('app.url'),
            'session_driver' => config('session.driver'),
        ],
        'request' => [
            'is_secure' => request()->secure(),
            'url' => request()->url(),
        ]
    ]);
})->middleware('admin.auth');

// Test password hash route
Route::post('/test-password-update', function (Request $request) {
    $adminId = Session::get('admin_id');
    
    if (!$adminId) {
        return response()->json(['error' => 'Not logged in']);
    }
    
    $testPassword = 'TestPassword123!';
    $hashedPassword = Hash::make($testPassword);
    
    // Try to update
    $updated = DB::table('admin_users')
        ->where('admin_id', $adminId)
        ->update([
            'password_hash' => $hashedPassword,
            'updated_at' => now()
        ]);
    
    // Verify the update
    $admin = DB::table('admin_users')->where('admin_id', $adminId)->first();
    $verifyHash = Hash::check($testPassword, $admin->password_hash);
    
    return response()->json([
        'rows_updated' => $updated,
        'new_hash_prefix' => substr($hashedPassword, 0, 20),
        'stored_hash_prefix' => substr($admin->password_hash, 0, 20),
        'password_verify' => $verifyHash,
        'test_password' => $testPassword,
    ]);
})->middleware('admin.auth');

// Temporary test route removed - Firebase no longer used

// Test route to simulate successful login redirect (for testing our fix)
Route::post('/test-login-redirect', function (Request $request) {
    // Check if this is an AJAX request
    if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
            'redirect' => url('test-dashboard'),
            'message' => 'Login successful'
        ]);
    }

    // For regular HTTP requests, redirect directly
    return redirect('test-dashboard')->with('success', 'Login successful');
});

// Test dashboard page (no authentication required for testing)
Route::get('/test-dashboard', function () {
    return view('test-dashboard');
});

// Test page route
Route::get('/test-password-errors', function () {
    return view('test-password-errors');
});