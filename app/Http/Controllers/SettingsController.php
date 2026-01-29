<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    protected $settingsService;
    
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }
    
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $userType = Session::get('user_type');
        $currentUserId = Session::get($userType . '_id');
        
        // Get current user
        $currentUser = null;
        if ($userType === 'admin') {
            $currentUser = Admin::with('roles')->find($currentUserId);
        } elseif ($userType === 'staff') {
            $currentUser = Staff::with('roles')->find($currentUserId);
        }
        
        if (!$currentUser) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }
        
        // Check if user can manage settings
        if ($userType === 'staff' || !$currentUser->canAccessSettings()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access settings.');
        }
        
        $data = [
            'users' => $this->settingsService->getAllUsers(),
            'roles' => Role::all(),
            'systemSettings' => SystemSetting::getByCategory('general'),
            'paymentSettings' => SystemSetting::getByCategory('payment'),
            'securitySettings' => SystemSetting::getByCategory('security'),
            'currentUser' => $currentUser,
            'userStats' => $this->settingsService->getUserStats(),
        ];
        
        return view('settings.index', $data);
    }
    
    /**
     * Display user management section.
     */
    public function users()
    {
        $users = $this->settingsService->getAllUsers();
        $roles = Role::all();
        
        return view('settings.users', compact('users', 'roles'));
    }
    
    /**
     * Store a new staff user.
     */
    public function storeStaff(Request $request)
    {
        // Ensure only admins can create staff
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can create staff accounts.'
            ], 403);
        }

        $request->validate([
            'username' => 'required|string|min:3|max:50|unique:staff_users,username|regex:/^[a-zA-Z0-9_]+$/',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:staff_users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            // Get current admin ID
            $adminId = Session::get('admin_id');
            $data = $request->all();
            $data['admin_id'] = $adminId; // Track which admin created this staff
            
            // Automatically assign 'staff' role
            $staffRole = Role::where('role_name', 'staff')->first();
            if ($staffRole) {
                $data['roles'] = [$staffRole->role_id];
            }
            
            $staff = $this->settingsService->createStaff($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff member created successfully.',
                'user' => $staff->load('roles')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create staff member: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update a staff member.
     */
    public function updateStaff(Request $request, $id)
    {
        // Ensure only admins can update staff
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update staff accounts.'
            ], 403);
        }

        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|min:3|max:50|unique:staff_users,username,' . $id . ',staff_id|regex:/^[a-zA-Z0-9_]+$/',
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|max:100|unique:staff_users,email,' . $id . ',staff_id',
                'phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $staff = $this->settingsService->updateStaff($id, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Staff member updated successfully.',
                'user' => $staff->load('roles')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update staff member', [
                'staff_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff member: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Toggle staff status (active/inactive).
     */
    public function toggleStaffStatus(Request $request, $id)
    {
        // Ensure only admins can toggle staff status
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can change staff status.'
            ], 403);
        }
        
        try {
            $staff = $this->settingsService->toggleStaffStatus($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff status updated successfully.',
                'is_active' => $staff->status === 'active'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update staff status: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the current admin's account information.
     */
    public function getAdminAccount()
    {
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can access account settings.'
            ], 403);
        }
        
        try {
            $adminId = Session::get('admin_id');
            $admin = Admin::findOrFail($adminId);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $admin->admin_id,
                    'username' => $admin->username,
                    'email' => $admin->email,
                    'first_name' => $admin->first_name,
                    'last_name' => $admin->last_name,
                    'password_changed_at' => $admin->password_changed_at ? $admin->password_changed_at->diffForHumans() : 'Never',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve account information: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the current admin's username.
     */
    public function updateAdminUsername(Request $request)
    {
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update account settings.'
            ], 403);
        }
        
        $adminId = Session::get('admin_id');
        
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:3|max:50|unique:admin_users,username,' . $adminId . ',admin_id|regex:/^[a-zA-Z0-9_!@#$%^&*]+$/',
            'current_password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $admin = Admin::findOrFail($adminId);
            
            // Verify current password
            if (!Hash::check($request->current_password, $admin->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 403);
            }
            
            $oldUsername = $admin->username;
            $admin->username = $request->username;
            $admin->save();
            
            Log::info('Admin username updated', [
                'admin_id' => $adminId,
                'old_username' => $oldUsername,
                'new_username' => $request->username,
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Username updated successfully. Please use your new username for future logins.',
                'new_username' => $request->username
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update admin username', [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update username: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the current admin's password.
     */
    public function updateAdminPassword(Request $request)
    {
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            Log::warning('Unauthorized password update attempt', [
                'user_type' => $userType,
                'ip' => $request->ip()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can update account settings.'
            ], 403);
        }
        
        $adminId = Session::get('admin_id');
        
        Log::info('Admin password update attempt', [
            'admin_id' => $adminId,
            'ip' => $request->ip()
        ]);
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);
        
        if ($validator->fails()) {
            Log::warning('Admin password update validation failed', [
                'admin_id' => $adminId,
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $admin = Admin::findOrFail($adminId);
            
            Log::info('Verifying current password', [
                'admin_id' => $adminId,
                'has_password_hash' => !empty($admin->password_hash)
            ]);
            
            // Verify current password
            if (!Hash::check($request->current_password, $admin->password_hash)) {
                Log::warning('Admin password update - current password incorrect', [
                    'admin_id' => $adminId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 403);
            }
            
            // Update password using direct database update to avoid any model issues
            $newPasswordHash = Hash::make($request->password);
            
            $updated = DB::table('admin_users')
                ->where('admin_id', $adminId)
                ->update([
                    'password_hash' => $newPasswordHash,
                    'password_changed_at' => now(),
                    'force_password_change' => false,
                    'updated_at' => now()
                ]);
            
            Log::info('Admin password updated', [
                'admin_id' => $adminId,
                'ip' => $request->ip(),
                'rows_affected' => $updated,
                'new_hash_prefix' => substr($newPasswordHash, 0, 20) . '...'
            ]);
            
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully. Please use your new password for future logins.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Password update failed. No rows were affected.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Failed to update admin password', [
                'admin_id' => $adminId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Reset staff password.
     */
    public function resetStaffPassword(Request $request, $id)
    {
        // Ensure only admins can reset staff passwords
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can reset staff passwords.'
            ], 403);
        }
        
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        try {
            $this->settingsService->resetStaffPassword($id, $request->password);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff password reset successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update system settings.
     */
    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.system_name' => 'string|max:100',
            'settings.system_description' => 'string|max:500',
            'settings.booking_advance_days' => 'integer|min:1|max:365',
            'settings.cancellation_hours' => 'integer|min:1|max:168',
        ]);
        
        try {
            foreach ($request->settings as $key => $value) {
                // Determine data type based on key
                $type = 'string';
                $category = 'general';
                
                switch ($key) {
                    case 'email_notifications':
                    case 'sms_notifications':
                    case 'booking_reminders':
                    case 'maintenance_mode':
                        $type = 'boolean';
                        $value = (bool) $value;
                        break;
                    case 'booking_advance_days':
                    case 'cancellation_hours':
                        $type = 'integer';
                        $value = (int) $value;
                        break;
                }
                
                SystemSetting::setValue($key, $value, $type, $category);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'System settings updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update system settings: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Toggle payment integration.
     */
    public function togglePaymentIntegration(Request $request)
    {
        $request->validate([
            'enabled' => 'required|boolean',
        ]);
        
        try {
            SystemSetting::setValue(
                'payment_integration_enabled',
                $request->enabled,
                'boolean',
                'payment',
                'Enable or disable Xendit payment integration for GCash transactions'
            );
            
            // Update the GCash payment method status in payment_methods table
            \DB::table('payment_methods')
                ->where('method_name', 'GCash (Online)')
                ->update(['is_active' => $request->enabled ? 1 : 0]);
            
            return response()->json([
                'success' => true,
                'message' => 'Payment integration ' . ($request->enabled ? 'enabled' : 'disabled') . ' successfully.',
                'enabled' => $request->enabled
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment integration: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get staff security logs.
     */
    public function getStaffSecurityLogs($id)
    {
        // Ensure only admins can view staff security logs
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can view security logs.'
            ], 403);
        }
        
        try {
            $logs = $this->settingsService->getStaffSecurityLogs($id);
            
            return response()->json([
                'success' => true,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve security logs: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Unlock a staff account.
     */
    public function unlockStaff(Request $request, $id)
    {
        // Ensure only admins can unlock staff accounts
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can unlock staff accounts.'
            ], 403);
        }
        
        try {
            $staff = $this->settingsService->unlockStaff($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff account unlocked successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlock staff account: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific staff member.
     */
    public function getStaff($id)
    {
        // Ensure only admins can view staff details
        $userType = Session::get('user_type');
        if ($userType !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only admins can view staff details.'
            ], 403);
        }
        
        try {
            $staff = Staff::with('roles')->findOrFail($id);
            $staffData = [
                'id' => $staff->staff_id,
                'username' => $staff->username,
                'email' => $staff->email,
                'first_name' => $staff->first_name,
                'last_name' => $staff->last_name,
                'phone' => $staff->phone,
                'is_active' => $staff->status === 'active',
                'roles' => $staff->roles->pluck('role_id')->toArray(),
            ];
            
            return response()->json([
                'success' => true,
                'user' => $staffData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Configure payment settings.
     */
    public function configurePayment(Request $request)
    {
        $request->validate([
            'xendit_api_key' => 'required|string',
            'payment_methods' => 'array',
            'currency' => 'required|string|in:PHP',
        ]);

        try {
            // Save payment configuration
            SystemSetting::setValue('xendit_api_key', $request->xendit_api_key, 'string', 'payment');
            SystemSetting::setValue('enabled_payment_methods', json_encode($request->payment_methods ?? ['gcash']), 'json', 'payment');
            SystemSetting::setValue('payment_currency', $request->currency, 'string', 'payment');

            return response()->json([
                'success' => true,
                'message' => 'Payment configuration updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test payment connection.
     */
    public function testPaymentConnection(Request $request)
    {
        try {
            // Get API key from request or system settings
            $apiKey = $request->input('api_key');
            
            // If no API key provided in request, try to get from settings
            if (!$apiKey) {
                $apiKey = SystemSetting::getValue('xendit_api_key');
            }
            
            // Trim whitespace
            $apiKey = trim($apiKey);
            
            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API key is required. Please enter your Xendit API key.'
                ], 400);
            }
            
            // Validate API key format (basic check)
            if (strlen($apiKey) < 20) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid API key format. Please check your Xendit API key.'
                ], 400);
            }
            
            // Check if this looks like a public key instead of secret key
            if (strpos($apiKey, 'xnd_public_') === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are using a Public Key. Please use your Secret API Key instead (starts with xnd_development_ or xnd_production_).'
                ], 400);
            }

            // Log the attempt (without exposing the full key)
            \Log::info('Testing Xendit API connection', [
                'key_prefix' => substr($apiKey, 0, 20) . '...',
                'key_length' => strlen($apiKey),
                'starts_with' => substr($apiKey, 0, 15)
            ]);

            // Try multiple endpoints to ensure connectivity
            // First, try to create a test invoice (most reliable method)
            $testInvoiceData = [
                'external_id' => 'test-connection-' . time(),
                'amount' => 10000,
                'description' => 'API Connection Test',
                'invoice_duration' => 86400,
                'customer' => [
                    'given_names' => 'Test',
                    'email' => 'test@example.com',
                    'mobile_number' => '+6281234567890',
                ],
                'success_redirect_url' => url('/'),
                'failure_redirect_url' => url('/'),
            ];

            $response = Http::timeout(15)
                ->withBasicAuth($apiKey, '')
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.xendit.co/v2/invoices', $testInvoiceData);

            if ($response->successful()) {
                $invoice = $response->json();
                
                // Delete the test invoice immediately
                try {
                    Http::withBasicAuth($apiKey, '')
                        ->post('https://api.xendit.co/invoices/' . $invoice['id'] . '/expire');
                } catch (\Exception $e) {
                    // Ignore if we can't delete it
                }
                
                // Log success
                \Log::info('Xendit API connection successful', [
                    'status' => $response->status(),
                    'invoice_id' => $invoice['id'] ?? 'unknown'
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful! Your Xendit API key is valid and working perfectly.',
                    'data' => [
                        'status' => 'Connected',
                        'mode' => strpos($apiKey, 'xnd_production_') === 0 ? 'Live Mode' : 'Test Mode',
                        'invoice_created' => true,
                    ]
                ]);
            } elseif ($response->status() === 401) {
                // Log the error details
                $errorData = $response->json();
                \Log::warning('Xendit authentication failed', [
                    'status' => 401,
                    'error' => $errorData,
                    'response_body' => $response->body()
                ]);
                
                // Check the exact error message
                $errorMessage = $errorData['message'] ?? 'Unknown error';
                
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed. The API key may be incorrect or expired.',
                    'debug_info' => [
                        'error' => $errorMessage,
                        'key_format' => 'Key starts with: ' . substr($apiKey, 0, 15) . '...',
                        'suggestion' => 'Please double-check that you copied the entire Secret API Key without any extra spaces or characters.'
                    ]
                ], 401);
            } elseif ($response->status() === 403) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access forbidden. Your API key may not have the required permissions to create invoices.'
                ], 403);
            } else {
                $errorData = $response->json();
                $errorMessage = 'Connection failed with status ' . $response->status();
                
                if (isset($errorData['message'])) {
                    $errorMessage = $errorData['message'];
                } elseif (isset($errorData['error_code'])) {
                    $errorMessage = $errorData['error_code'];
                }
                
                \Log::warning('Xendit API test failed', [
                    'status' => $response->status(),
                    'error' => $errorData,
                    'response_body' => $response->body()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $errorData
                ], $response->status());
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Xendit connection exception', [
                'error' => $e->getMessage(),
                'type' => 'ConnectionException'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Xendit API. Please check your internet connection or firewall settings.'
            ], 500);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('Xendit request exception', [
                'error' => $e->getMessage(),
                'type' => 'RequestException'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Request to Xendit API failed: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Payment connection test error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'type' => get_class($e)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update security settings.
     */
    public function updateSecuritySettings(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.max_login_attempts' => 'integer|min:3|max:10',
            'settings.account_lockout_duration' => 'integer|min:5',
            'settings.password_min_length' => 'integer|min:6|max:20',
            'settings.password_expiry_days' => 'integer|min:0',
            'settings.session_timeout' => 'integer|min:15',
            'settings.concurrent_sessions' => 'integer|min:1|max:10',
        ]);

        try {
            foreach ($request->settings as $key => $value) {
                // Determine data type based on key
                $type = 'integer';
                $category = 'security';
                
                switch ($key) {
                    case 'require_uppercase':
                    case 'require_lowercase':
                    case 'require_numbers':
                    case 'require_special_chars':
                    case 'enforce_finance_restriction':
                    case 'log_all_actions':
                    case 'require_password_change':
                        $type = 'boolean';
                        $value = (bool) $value;
                        break;
                    case 'max_login_attempts':
                    case 'account_lockout_duration':
                    case 'password_min_length':
                    case 'password_expiry_days':
                    case 'session_timeout':
                    case 'concurrent_sessions':
                        $type = 'integer';
                        $value = (int) $value;
                        break;
                }
                
                SystemSetting::setValue($key, $value, $type, $category);
            }

            return response()->json([
                'success' => true,
                'message' => 'Security settings updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update security settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
