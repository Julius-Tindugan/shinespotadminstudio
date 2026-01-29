<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

// Include test routes for debugging
require __DIR__.'/test_routes.php';
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SimplePasswordResetController;


// Redirect root to login page
Route::get('/', function () {
    return redirect()->route('login');
})->name('welcome');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])
    ->middleware('login.limit')
    ->name('login.authenticate');
Route::get('/login/lockout-status', [LoginController::class, 'checkLockoutStatus'])->name('login.lockout.status');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Debug route to check session data
Route::get('/debug-session', function() {
    return response()->json([
        'all_session' => session()->all(),
        'admin_logged_in' => session('admin_logged_in'),
        'admin_id' => session('admin_id'),
        'admin_roles' => session('admin_roles'),
        'admin_name' => session('admin_name'),
        'user_type' => session('user_type'),
        'last_activity' => session('last_activity'),
    ]);
})->middleware(['admin.auth'])->name('debug.session');

// Test route for fixed login
Route::get('/test-fixed-login', function() {
    return view('test-fixed-login');
})->name('test.fixed.login');

// Test route for AJAX redirect
Route::get('/test-ajax-redirect', function() {
    return view('test-ajax-redirect');
})->name('test.ajax.redirect');

// Test route for button functionality
Route::get('/test-button', function() {
    return view('test-button-functionality');
})->name('test.button');

// Simple Password Reset Routes
Route::get('/forgot-password', [SimplePasswordResetController::class, 'showForgotPasswordForm'])->name('simple.password.request');
Route::post('/forgot-password', [SimplePasswordResetController::class, 'sendResetEmail'])->name('simple.password.email');

// Custom Password Reset with Beautiful Email Template
Route::get('/password/reset/{token}', function(Request $request, string $token) {
    $email = $request->query('email');
    
    if (!$email) {
        Log::warning('Password reset link accessed without email', [
            'token' => substr($token, 0, 10) . '...',
            'ip' => $request->ip()
        ]);
        return redirect()->route('login')->with('error', 'Invalid password reset link.');
    }
    
    Log::info('Password reset link accessed', [
        'email' => $email,
        'token_prefix' => substr($token, 0, 10) . '...',
        'ip' => $request->ip()
    ]);
    
    // Verify the token exists and is not expired
    $resetToken = \DB::table('password_reset_tokens')
        ->where('email', $email)
        ->where('token', hash('sha256', $token))
        ->where('expires_at', '>', now())
        ->whereNull('used_at')
        ->first();
    
    if (!$resetToken) {
        // Check if token exists with different conditions for better error messages
        $anyToken = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', hash('sha256', $token))
            ->first();
            
        if ($anyToken) {
            Log::warning('Password reset token found but invalid', [
                'email' => $email,
                'expires_at' => $anyToken->expires_at,
                'current_time' => now()->toDateTimeString(),
                'used_at' => $anyToken->used_at,
                'is_expired' => now()->greaterThan($anyToken->expires_at),
                'is_used' => !is_null($anyToken->used_at)
            ]);
            
            if (now()->greaterThan($anyToken->expires_at)) {
                return redirect()->route('login')->with('error', 'Password reset link has expired. Please request a new one.');
            } elseif (!is_null($anyToken->used_at)) {
                return redirect()->route('login')->with('error', 'Password reset link has already been used. Please request a new one.');
            }
        } else {
            Log::warning('Password reset token not found in database', [
                'email' => $email,
                'token_hash' => hash('sha256', $token),
                'ip' => $request->ip()
            ]);
        }
        
        return redirect()->route('login')->with('error', 'Password reset link is invalid or has expired.');
    }
    
    Log::info('Password reset form displayed', [
        'email' => $email,
        'user_type' => $resetToken->user_type
    ]);
    
    return view('auth.custom-password-reset', [
        'title' => 'Reset Password',
        'token' => $token,
        'email' => $email,
        'userType' => $resetToken->user_type ?? 'user'
    ]);
})->name('custom.password.reset');

Route::post('/password/reset/{token}', function(Request $request, string $token) {
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput($request->except('password', 'password_confirmation'));
    }

    $email = $request->input('email');
    
    // Verify and get the reset token
    $resetToken = \DB::table('password_reset_tokens')
        ->where('email', $email)
        ->where('token', hash('sha256', $token))
        ->where('expires_at', '>', now())
        ->whereNull('used_at')
        ->first();
    
    if (!$resetToken) {
        // Enhanced debugging for expired/invalid tokens
        $anyToken = \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', hash('sha256', $token))
            ->first();
            
        if ($anyToken) {
            Log::warning('Password reset token found but invalid', [
                'email' => $email,
                'expires_at' => $anyToken->expires_at,
                'current_time' => now()->toDateTimeString(),
                'used_at' => $anyToken->used_at,
                'is_expired' => now()->greaterThan($anyToken->expires_at),
                'is_used' => !is_null($anyToken->used_at)
            ]);
            
            if (now()->greaterThan($anyToken->expires_at)) {
                return back()->with('error', 'Password reset link has expired. Please request a new one.');
            } elseif (!is_null($anyToken->used_at)) {
                return back()->with('error', 'Password reset link has already been used. Please request a new one.');
            }
        }
        
        return back()->with('error', 'Password reset link is invalid or has expired.');
    }

    try {
        // Reset the password directly in the database
        $userType = $resetToken->user_type ?? 'admin';
        $user = null;
        $newPassword = $request->input('password');

        if ($userType === 'staff') {
            $user = \App\Models\Staff::where('email', $email)->first();
            if ($user) {
                $user->password_hash = bcrypt($newPassword);
                $user->force_password_change = false;
                $user->password_changed_at = now();
                $user->save();
            }
        } else {
            $user = \App\Models\Admin::where('email', $email)->first();
            if ($user) {
                $user->password_hash = bcrypt($newPassword);
                $user->force_password_change = false;
                $user->password_changed_at = now();
                $user->save();
            }
        }

        if (!$user) {
            Log::warning('Password reset failed - user not found', [
                'email' => $email,
                'user_type' => $userType,
                'ip' => $request->ip()
            ]);
            return back()->with('error', 'User account not found.');
        }

        // Mark token as used
        \DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', hash('sha256', $token))
            ->update(['used_at' => now()]);

        Log::info('Custom password reset completed successfully', [
            'email' => $email,
            'user_type' => $userType,
            'ip' => $request->ip()
        ]);

        return redirect()->route('login')->with('status', 
            'Password has been reset successfully! You can now log in with your new password.'
        );

    } catch (\Exception $e) {
        Log::error('Custom password reset failed', [
            'email' => $email,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'ip' => $request->ip(),
            'trace' => $e->getTraceAsString()
        ]);

        // Return more detailed error in debug mode
        $errorMessage = config('app.debug') 
            ? "Reset failed: {$e->getMessage()}" 
            : 'An unexpected error occurred while resetting password. Please try again.';
            
        return back()->with('error', $errorMessage);
    }
})->name('custom.password.update');

// Legacy route redirects for backwards compatibility - ENABLED
Route::get('/password-request', function () {
    return redirect()->route('simple.password.request');
})->name('password.request');

Route::get('/firebase/forgot-password', function () {
    return redirect()->route('firebase.password.request');
});

Route::get('/admin/forgot-password', function () {
    return redirect()->route('firebase.password.request');
})->name('admin.password.request');

// Protected Routes for Authenticated Users
Route::middleware(['admin.auth', 'log.activity'])->group(function () {
    // Dashboard - Available to all authenticated users
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Activity Logs - Admin only
    Route::middleware(['admin.only'])->prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('index');
        Route::get('/recent', [App\Http\Controllers\ActivityLogController::class, 'recent'])->name('recent');
        Route::get('/stats', [App\Http\Controllers\ActivityLogController::class, 'stats'])->name('stats');
        Route::get('/export', [App\Http\Controllers\ActivityLogController::class, 'export'])->name('export');
        Route::get('/{id}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('show');
    });
    
    // Finance Management Routes - Staff and Admin can access
    Route::prefix('finance')->name('finance.')->middleware(['finance.access'])->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\FinanceController::class, 'index'])->name('dashboard');
        
        // Reports
        Route::get('/reports', [App\Http\Controllers\FinanceController::class, 'reports'])->name('reports');
        Route::post('/reports/generate', [App\Http\Controllers\FinanceController::class, 'generateReport'])->name('reports.generate');
        
        // KPIs
        Route::get('/kpis', [App\Http\Controllers\FinanceController::class, 'kpis'])->name('kpis');
        Route::get('/kpis/export', [App\Http\Controllers\FinanceController::class, 'exportKpis'])->name('kpis.export');
        Route::put('/kpis/targets/update', [App\Http\Controllers\FinanceController::class, 'updateKpiTargets'])->name('kpis.targets.update');
        
        // Settings
        Route::get('/settings', [App\Http\Controllers\FinanceController::class, 'settings'])->name('settings');
        Route::put('/settings/general/update', [App\Http\Controllers\FinanceController::class, 'updateGeneralSettings'])->name('settings.general.update');
        Route::put('/settings/tax/update', [App\Http\Controllers\FinanceController::class, 'updateTaxSettings'])->name('settings.tax.update');
        Route::put('/settings/invoices/update', [App\Http\Controllers\FinanceController::class, 'updateInvoiceSettings'])->name('settings.invoices.update');
        
        // Payment Methods
        Route::post('/payment-methods', [App\Http\Controllers\FinanceController::class, 'storePaymentMethod'])->name('payment-methods.store');
        Route::get('/payment-methods/{id}/edit', [App\Http\Controllers\FinanceController::class, 'editPaymentMethod'])->name('payment-methods.edit');
        Route::put('/payment-methods/{id}', [App\Http\Controllers\FinanceController::class, 'updatePaymentMethod'])->name('payment-methods.update');
        Route::delete('/payment-methods/{id}', [App\Http\Controllers\FinanceController::class, 'deletePaymentMethod'])->name('payment-methods.delete');
        
        // Expense Categories
        Route::post('/expense-categories', [App\Http\Controllers\FinanceController::class, 'storeExpenseCategory'])->name('expense-categories.store');
        Route::get('/expense-categories/{id}/edit', [App\Http\Controllers\FinanceController::class, 'editExpenseCategory'])->name('expense-categories.edit');
        Route::put('/expense-categories/{id}', [App\Http\Controllers\FinanceController::class, 'updateExpenseCategory'])->name('expense-categories.update');
        Route::delete('/expense-categories/{id}', [App\Http\Controllers\FinanceController::class, 'deleteExpenseCategory'])->name('expense-categories.delete');
        
        // Payment Management
        // Define specific routes before the resource route to avoid conflicts
        Route::get('/payments/gcash-redirect/{payment_id}', [App\Http\Controllers\PaymentController::class, 'gcashRedirect'])->name('payments.gcash-redirect');
        Route::get('/payments/{payment_id}/check-status', [App\Http\Controllers\PaymentController::class, 'checkPaymentStatus'])->name('payments.check-status');
        Route::get('/booking/{booking_id}/payments', [App\Http\Controllers\PaymentController::class, 'getBookingPayments'])->name('payments.by-booking');
        Route::post('/payments/check-statuses', [App\Http\Controllers\PaymentController::class, 'checkStatuses'])->name('payments.check-statuses');
        Route::post('/payments/{id}/sync-xendit', [App\Http\Controllers\PaymentController::class, 'syncFromXendit'])->name('payments.sync-xendit');
        Route::post('/payments/sync-recent-pending', [App\Http\Controllers\PaymentController::class, 'syncRecentPendingPayments'])->name('payments.sync-recent-pending');
        Route::resource('payments', App\Http\Controllers\PaymentController::class);
        
        // Expense Management
        Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
        Route::get('/api/expense-categories', [App\Http\Controllers\ExpenseController::class, 'getCategoriesJson'])->name('api.expense-categories');
        Route::get('/api/expenses/by-category', [App\Http\Controllers\ExpenseController::class, 'getExpensesByCategory'])->name('api.expenses.by-category');
        Route::get('/api/expenses/trend', [App\Http\Controllers\ExpenseController::class, 'getExpensesTrend'])->name('api.expenses.trend');
        
        // API Endpoints for Charts and Reports
        Route::get('/api/dashboard/summary', [App\Http\Controllers\FinanceController::class, 'getDashboardSummary'])->name('api.dashboard.summary');
        Route::get('/api/dashboard/revenue-trend', [App\Http\Controllers\FinanceController::class, 'getRevenueTrend'])->name('api.dashboard.revenue-trend');
        Route::get('/api/dashboard/expense-trend', [App\Http\Controllers\FinanceController::class, 'getExpenseTrend'])->name('api.dashboard.expense-trend');
    });
    
    // All the following routes allow BOTH ADMIN AND STAFF access
    Route::middleware(['management.access'])->group(function () {
        
        // Revenue API endpoints
        Route::get('/api/revenue', [App\Http\Controllers\RevenueController::class, 'getRevenueData'])->name('api.revenue');
        Route::get('/api/revenue/stats', [App\Http\Controllers\RevenueController::class, 'getDashboardRevenueStats'])->name('api.revenue.stats');
        
        // Package API endpoints
        Route::get('/api/packages/{id}/details', [App\Http\Controllers\PackageController::class, 'getPackageDetails'])->name('api.packages.details');
        Route::get('/api/packages/list', [App\Http\Controllers\PackageController::class, 'getPackagesList'])->name('api.packages.list');
        Route::get('/packages/{id}/image', [App\Http\Controllers\PackageController::class, 'serveImage'])->name('package.image');
        
        // Staff API endpoints
        Route::get('/api/staff/active', [App\Http\Controllers\BookingController::class, 'getActiveStaff'])->name('api.staff.active');
        
        // Backdrop API endpoints
        Route::get('/api/backdrops/list', [App\Http\Controllers\BackdropController::class, 'getBackdropsList'])->name('api.backdrops.list');
        
        // Addon API endpoints
        Route::get('/api/addons/list', [App\Http\Controllers\AddonController::class, 'getAddonsList'])->name('api.addons.list');
        
        // Payment Method API endpoints
        Route::get('/api/payment-methods/list', [App\Http\Controllers\PaymentController::class, 'getPaymentMethodsList'])->name('api.payment-methods.list');
        
        // Bookings
        Route::resource('bookings', App\Http\Controllers\BookingController::class);
        Route::post('/bookings/{booking}/status', [App\Http\Controllers\BookingController::class, 'updateStatus'])->name('bookings.update-status');
        Route::post('/bookings/{booking}/add-payment', [App\Http\Controllers\BookingController::class, 'addPayment'])->name('bookings.addPayment');
        Route::post('/bookings/{booking}/generate-invoice', [App\Http\Controllers\BookingController::class, 'generateInvoice'])->name('bookings.generateInvoice');
        Route::get('/upcoming-bookings', [App\Http\Controllers\BookingController::class, 'upcomingBookings'])->name('bookings.upcoming');
        
        // Payment Transactions
        Route::prefix('payment-transactions')->name('payment-transactions.')->group(function () {
            Route::get('/', [App\Http\Controllers\PaymentTransactionController::class, 'index'])->name('index');
            Route::get('/booking/{bookingId}', [App\Http\Controllers\PaymentTransactionController::class, 'index'])->name('booking');
            Route::post('/', [App\Http\Controllers\PaymentTransactionController::class, 'store'])->name('store');
            Route::post('/gcash', [App\Http\Controllers\PaymentTransactionController::class, 'processGCashPayment'])->name('gcash');
            Route::post('/send-payment-link', [App\Http\Controllers\PaymentTransactionController::class, 'sendPaymentLinkEmail'])->name('send-payment-link');
            Route::get('/summary/{bookingId}', [App\Http\Controllers\PaymentTransactionController::class, 'getPaymentSummary'])->name('summary');
            Route::post('/{transactionId}/refund', [App\Http\Controllers\PaymentTransactionController::class, 'refund'])->name('refund');
        });
        
        
    });
}); // End of first admin.auth middleware group started on line 268

// Xendit Webhook (public route - no auth or CSRF middleware)
// Accept both POST (for actual webhooks) and GET (for testing)
// IMPORTANT: This must be outside any middleware groups
Route::match(['post', 'get'], '/webhooks/xendit', [App\Http\Controllers\PaymentTransactionController::class, 'xenditWebhook'])
    ->name('webhooks.xendit');

// Payment callback route (public - for Xendit redirects after payment)
Route::get('/payment/callback', [App\Http\Controllers\PaymentTransactionController::class, 'paymentCallback'])
    ->name('payment.callback');

Route::middleware(['admin.auth', 'log.activity'])->group(function () {
    
        // Calendar Management
        Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
        Route::prefix('admin/calendar')->name('calendar.')->group(function () {
            Route::get('/bookings', [App\Http\Controllers\CalendarController::class, 'getBookings'])->name('bookings');
            Route::get('/unavailable-dates', [App\Http\Controllers\CalendarController::class, 'getUnavailableDates'])->name('unavailable-dates');
            Route::post('/unavailable-dates', [App\Http\Controllers\CalendarController::class, 'storeUnavailableDate'])->name('store-unavailable-dates');
            Route::delete('/unavailable-dates/{id}', [App\Http\Controllers\CalendarController::class, 'removeUnavailableDate'])->name('remove-unavailable-dates');
            Route::get('/business-hours', [App\Http\Controllers\CalendarController::class, 'getBusinessHours'])->name('business-hours');
            Route::get('/all-business-hours', [App\Http\Controllers\CalendarController::class, 'getAllBusinessHours'])->name('all-business-hours');
            Route::post('/business-hours', [App\Http\Controllers\CalendarController::class, 'updateBusinessHours'])->name('update-business-hours');
            Route::get('/booking-slots', [App\Http\Controllers\CalendarController::class, 'getBookingSlots'])->name('booking-slots');
            Route::post('/booking-slots/synchronize', [App\Http\Controllers\CalendarController::class, 'synchronizeBookingSlots'])->name('booking-slots.synchronize');
        });

        // Test page
        Route::get('/test', function () {
            return view('test');
        });
        

        
        // Package Management Routes
        // NOTE: Custom routes must come BEFORE Route::resource to avoid conflicts
        Route::get('/packages/featured/stats', [App\Http\Controllers\PackageController::class, 'getFeaturedStats'])->name('packages.featured-stats');
        Route::post('/packages/{id}/update-with-image', [App\Http\Controllers\PackageController::class, 'updateWithImage'])->name('packages.update-with-image');
        Route::post('/packages/{id}/toggle-status', [App\Http\Controllers\PackageController::class, 'toggleStatus'])->name('packages.toggle-status');
        Route::post('/packages/{id}/toggle-featured', [App\Http\Controllers\PackageController::class, 'toggleFeatured'])->name('packages.toggle-featured');
        Route::delete('/packages/{id}/remove-image', [App\Http\Controllers\PackageController::class, 'removeImage'])->name('packages.remove-image');
        Route::resource('packages', App\Http\Controllers\PackageController::class);
        
        // Addon Management Routes
        Route::resource('addons', App\Http\Controllers\AddonController::class);
        Route::post('/addons/{id}/toggle-status', [App\Http\Controllers\AddonController::class, 'toggleStatus'])->name('addons.toggle-status');
        Route::get('/api/addons', [App\Http\Controllers\AddonController::class, 'getAddonsJson'])->name('api.addons');
        
        // Report Export Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::post('/export/dashboard', [App\Http\Controllers\ReportExportController::class, 'exportDashboard'])->name('export.dashboard');
            Route::post('/export/payment', [App\Http\Controllers\ReportExportController::class, 'exportPayment'])->name('export.payment');
        });
        
        // Studio Management Routes
        Route::prefix('studio')->group(function () {
            // Main Dashboard
            Route::get('/', [App\Http\Controllers\StudioManagementController::class, 'index'])->name('studio.index');
            
            // Equipment Management
            Route::resource('equipment', App\Http\Controllers\EquipmentController::class);
            
            // Backdrop Management
            Route::resource('backdrops', App\Http\Controllers\BackdropController::class);
        });
    
    // The following routes are ADMIN-ONLY (still within admin.auth middleware)
    Route::middleware(['admin.only'])->group(function () {
        
        // Settings Management Routes
        Route::middleware(['settings.access'])->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\SettingsController::class, 'index'])->name('index');
            Route::get('/users', [App\Http\Controllers\SettingsController::class, 'users'])->name('users');
            
            // Admin Account Management (for current logged-in admin)
            Route::get('/account', [App\Http\Controllers\SettingsController::class, 'getAdminAccount'])->name('account');
            Route::match(['put', 'post'], '/account/username', [App\Http\Controllers\SettingsController::class, 'updateAdminUsername'])->name('account.username');
            Route::match(['put', 'post'], '/account/password', [App\Http\Controllers\SettingsController::class, 'updateAdminPassword'])->name('account.password');
            
            // Staff Management (Admins Only)
            Route::post('/users/staff', [App\Http\Controllers\SettingsController::class, 'storeStaff'])->name('users.staff.store');
            Route::get('/users/staff/{id}', [App\Http\Controllers\SettingsController::class, 'getStaff'])->name('users.staff.show');
            Route::put('/users/staff/{id}', [App\Http\Controllers\SettingsController::class, 'updateStaff'])->name('users.staff.update');
            Route::post('/users/staff/{id}/toggle-status', [App\Http\Controllers\SettingsController::class, 'toggleStaffStatus'])->name('users.staff.toggle-status');
            Route::post('/users/staff/{id}/reset-password', [App\Http\Controllers\SettingsController::class, 'resetStaffPassword'])->name('users.staff.reset-password');
            Route::post('/users/staff/{id}/unlock', [App\Http\Controllers\SettingsController::class, 'unlockStaff'])->name('users.staff.unlock');
            Route::get('/users/staff/{id}/security-logs', [App\Http\Controllers\SettingsController::class, 'getStaffSecurityLogs'])->name('users.staff.security-logs');
            
            // System Settings
            Route::post('/system', [App\Http\Controllers\SettingsController::class, 'updateSystemSettings'])->name('system.update');
            Route::post('/security', [App\Http\Controllers\SettingsController::class, 'updateSecuritySettings'])->name('security.update');
            
            // Payment Settings
            Route::post('/payment/toggle', [App\Http\Controllers\SettingsController::class, 'togglePaymentIntegration'])->name('payment.toggle');
            Route::post('/payment/configure', [App\Http\Controllers\SettingsController::class, 'configurePayment'])->name('payment.configure');
            Route::post('/payment/test-connection', [App\Http\Controllers\SettingsController::class, 'testPaymentConnection'])->name('payment.test');
        });
        
        // Role management
        Route::get('/admin/roles', [App\Http\Controllers\AdminRoleController::class, 'index'])->name('admin.roles.index');
        Route::get('/admin/roles/create', [App\Http\Controllers\AdminRoleController::class, 'create'])->name('admin.roles.create');
        Route::post('/admin/roles', [App\Http\Controllers\AdminRoleController::class, 'store'])->name('admin.roles.store');
        Route::get('/admin/roles/{id}/edit', [App\Http\Controllers\AdminRoleController::class, 'edit'])->name('admin.roles.edit');
        Route::put('/admin/roles/{id}', [App\Http\Controllers\AdminRoleController::class, 'update'])->name('admin.roles.update');
        Route::delete('/admin/roles/{id}', [App\Http\Controllers\AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
        Route::post('/admin/assign-roles', [App\Http\Controllers\AdminRoleController::class, 'assignRoles'])->name('admin.roles.assign');
        
        // User management
        Route::get('/admin/users/admins', [App\Http\Controllers\UserController::class, 'adminIndex'])->name('admin.users.admin-index');
        Route::get('/admin/users/staff', [App\Http\Controllers\UserController::class, 'staffIndex'])->name('admin.users.staff-index');
        Route::get('/admin/users/admins/create', [App\Http\Controllers\UserController::class, 'createAdmin'])->name('admin.users.admin-create');
        Route::get('/admin/users/staff/create', [App\Http\Controllers\UserController::class, 'createStaff'])->name('admin.users.staff-create');
        Route::post('/admin/users/admins', [App\Http\Controllers\UserController::class, 'storeAdmin'])->name('admin.users.admin-store');
        Route::post('/admin/users/staff', [App\Http\Controllers\UserController::class, 'storeStaff'])->name('admin.users.staff-store');
        Route::get('/admin/users/admins/{id}/edit', [App\Http\Controllers\UserController::class, 'editAdmin'])->name('admin.users.admin-edit');
        Route::get('/admin/users/staff/{id}/edit', [App\Http\Controllers\UserController::class, 'editStaff'])->name('admin.users.staff-edit');
        Route::put('/admin/users/admins/{id}', [App\Http\Controllers\UserController::class, 'updateAdmin'])->name('admin.users.admin-update');
        Route::put('/admin/users/staff/{id}', [App\Http\Controllers\UserController::class, 'updateStaff'])->name('admin.users.staff-update');
        Route::delete('/admin/users/admins/{id}', [App\Http\Controllers\UserController::class, 'destroyAdmin'])->name('admin.users.admin-destroy');
        Route::delete('/admin/users/staff/{id}', [App\Http\Controllers\UserController::class, 'destroyStaff'])->name('admin.users.staff-destroy');
    });
}); // End of admin.auth middleware group started on line 379

// Public API routes for client website to check availability
Route::prefix('api/calendar')->group(function () {
    Route::get('/check-availability', [App\Http\Controllers\CalendarController::class, 'checkDateAvailability']);
    Route::get('/unavailable-dates', [App\Http\Controllers\CalendarController::class, 'getUnavailableDates']);
    Route::get('/business-hours', [App\Http\Controllers\CalendarController::class, 'getBusinessHours']);
    Route::get('/booking-slots', [App\Http\Controllers\CalendarController::class, 'getBookingSlots']);
    Route::get('/all-business-hours', [App\Http\Controllers\CalendarController::class, 'getAllBusinessHours']);
});

// Public booking management routes
Route::prefix('bookings')->name('public.bookings.')->group(function () {
    // Booking lookup
    Route::get('/manage', [App\Http\Controllers\PublicBookingController::class, 'showLookupForm'])->name('lookup');
    Route::post('/manage', [App\Http\Controllers\PublicBookingController::class, 'lookupBooking'])->name('lookup.submit');
    
    // OTP verification
    Route::get('/verify/{reference}', [App\Http\Controllers\PublicBookingController::class, 'showOtpForm'])->name('verify-otp');
    Route::post('/verify/{reference}', [App\Http\Controllers\PublicBookingController::class, 'verifyOtp'])->name('verify-otp.submit');
    
    // Manage booking (after verification)
    Route::get('/manage/{reference}', [App\Http\Controllers\PublicBookingController::class, 'showManageBooking'])->name('manage');
    
    // Reschedule booking
    Route::get('/reschedule/{reference}', [App\Http\Controllers\PublicBookingController::class, 'showRescheduleForm'])->name('reschedule');
    Route::post('/reschedule/{reference}', [App\Http\Controllers\PublicBookingController::class, 'rescheduleBooking'])->name('reschedule.submit');
    
    // Cancel booking
    Route::get('/cancel/{reference}', [App\Http\Controllers\PublicBookingController::class, 'showCancelForm'])->name('cancel');
    Route::post('/cancel/{reference}', [App\Http\Controllers\PublicBookingController::class, 'cancelBooking'])->name('cancel.submit');
});
