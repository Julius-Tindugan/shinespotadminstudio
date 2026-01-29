<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;
use App\Models\SystemSetting;

class SettingsService
{
    /**
     * Get all users (both admin and staff) with their roles.
     *
     * @return array
     */
    public function getAllUsers()
    {
        $admins = Admin::with('roles')->get()->map(function ($admin) {
            return [
                'id' => $admin->admin_id,
                'type' => 'admin',
                'username' => $admin->username,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'is_active' => $admin->is_active,
                'last_login' => $admin->last_login_at,
                'failed_attempts' => $admin->failed_login_attempts,
                'is_locked' => $admin->isLocked(),
                'roles' => $admin->roles->pluck('role_name')->toArray(),
                'created_at' => $admin->created_at,
            ];
        });

        $staff = Staff::with('roles')->get()->map(function ($staff) {
            return [
                'id' => $staff->staff_id,
                'type' => 'staff',
                'username' => $staff->username,
                'full_name' => $staff->full_name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'is_active' => $staff->status === 'active',
                'last_login' => $staff->last_login_at,
                'failed_attempts' => $staff->failed_login_attempts,
                'is_locked' => $staff->isLocked(),
                'roles' => $staff->roles->pluck('role_name')->toArray(),
                'created_at' => $staff->created_at,
            ];
        });

        return [
            'admins' => $admins,
            'staff' => $staff,
            'all' => $admins->merge($staff)->sortBy('created_at'),
        ];
    }

    /**
     * Create a new staff user.
     *
     * @param array $data
     * @return \App\Models\Staff
     */
    public function createStaff(array $data)
    {
        DB::beginTransaction();
        
        try {
            $staff = Staff::create([
                'username' => $data['username'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'status' => 'active',
                'admin_id' => $data['admin_id'] ?? null, // Track which admin created this staff
                'password_changed_at' => now(),
                'force_password_change' => false,
            ]);

            // Attach roles
            if (isset($data['roles'])) {
                $staff->roles()->attach($data['roles']);
            }

            DB::commit();
            return $staff;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update a staff member.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Staff
     */
    public function updateStaff($id, array $data)
    {
        DB::beginTransaction();
        
        try {
            $staff = Staff::findOrFail($id);
            $staff->update([
                'username' => $data['username'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ]);

            // Update roles if provided
            if (isset($data['roles'])) {
                $staff->roles()->sync($data['roles']);
            }

            DB::commit();
            return $staff;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Toggle staff status.
     *
     * @param int $id
     * @return \App\Models\Staff
     */
    public function toggleStaffStatus($id)
    {
        $staff = Staff::findOrFail($id);
        $newStatus = $staff->status === 'active' ? 'inactive' : 'active';
        $staff->update(['status' => $newStatus]);
        return $staff;
    }

    /**
     * Reset staff password.
     *
     * @param int $id
     * @param string $password
     * @return void
     */
    public function resetStaffPassword($id, $password)
    {
        $staff = Staff::findOrFail($id);
        $staff->update([
            'password_hash' => Hash::make($password),
            'force_password_change' => true,
            'password_changed_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Get staff security logs.
     *
     * @param int $id
     * @return array
     */
    public function getStaffSecurityLogs($id)
    {
        $staff = Staff::findOrFail($id);
        return [
            'full_name' => $staff->full_name,
            'email' => $staff->email,
            'last_login' => $staff->last_login_at,
            'last_login_ip' => $staff->last_login_ip,
            'failed_attempts' => $staff->failed_login_attempts,
            'is_locked' => $staff->isLocked(),
            'locked_until' => $staff->locked_until,
            'password_changed_at' => $staff->password_changed_at,
            'force_password_change' => $staff->force_password_change,
        ];
    }

    /**
     * Unlock a staff account.
     *
     * @param int $id
     * @return \App\Models\Staff
     */
    public function unlockStaff($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->unlockAccount();
        return $staff;
    }

    /**
     * Get user statistics.
     *
     * @return array
     */
    public function getUserStats()
    {
        $totalAdmins = Admin::count();
        $activeAdmins = Admin::where('is_active', true)->count();
        $lockedAdmins = Admin::whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->count();

        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $lockedStaff = Staff::whereNotNull('locked_until')
            ->where('locked_until', '>', now())
            ->count();

        return [
            'total_users' => $totalAdmins + $totalStaff,
            'total_admins' => $totalAdmins,
            'active_admins' => $activeAdmins,
            'locked_admins' => $lockedAdmins,
            'total_staff' => $totalStaff,
            'active_staff' => $activeStaff,
            'locked_staff' => $lockedStaff,
        ];
    }

    /**
     * Initialize default system settings.
     *
     * @return void
     */
    public function initializeDefaultSettings()
    {
        $defaultSettings = [
            // General settings
            [
                'key' => 'system_name',
                'value' => 'Shine Spot Studio Admin',
                'type' => 'string',
                'category' => 'general',
                'description' => 'System name displayed in the application',
                'is_public' => true,
            ],
            [
                'key' => 'system_description',
                'value' => 'Photography Studio Management System',
                'type' => 'string',
                'category' => 'general',
                'description' => 'Brief description of the system',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'category' => 'general',
                'description' => 'Enable or disable maintenance mode',
                'is_public' => false,
            ],
            [
                'key' => 'booking_advance_days',
                'value' => 30,
                'type' => 'integer',
                'category' => 'general',
                'description' => 'How many days in advance bookings can be made',
                'is_public' => false,
            ],
            [
                'key' => 'cancellation_hours',
                'value' => 24,
                'type' => 'integer',
                'category' => 'general',
                'description' => 'Minimum hours before booking can be cancelled',
                'is_public' => false,
            ],
            [
                'key' => 'email_notifications',
                'value' => true,
                'type' => 'boolean',
                'category' => 'general',
                'description' => 'Enable email notifications',
                'is_public' => false,
            ],
            [
                'key' => 'sms_notifications',
                'value' => false,
                'type' => 'boolean',
                'category' => 'general',
                'description' => 'Enable SMS notifications',
                'is_public' => false,
            ],
            [
                'key' => 'booking_reminders',
                'value' => true,
                'type' => 'boolean',
                'category' => 'general',
                'description' => 'Enable automatic booking reminders',
                'is_public' => false,
            ],
            
            // Payment settings
            [
                'key' => 'payment_integration_enabled',
                'value' => false,
                'type' => 'boolean',
                'category' => 'payment',
                'description' => 'Enable or disable Xendit payment integration for GCash',
                'is_public' => false,
            ],
            [
                'key' => 'xendit_api_key',
                'value' => '',
                'type' => 'string',
                'category' => 'payment',
                'description' => 'Xendit API key for payment processing',
                'is_public' => false,
            ],
            [
                'key' => 'enabled_payment_methods',
                'value' => json_encode(['gcash']),
                'type' => 'json',
                'category' => 'payment',
                'description' => 'Enabled payment methods',
                'is_public' => false,
            ],
            [
                'key' => 'payment_currency',
                'value' => 'PHP',
                'type' => 'string',
                'category' => 'payment',
                'description' => 'Default payment currency',
                'is_public' => false,
            ],
            
            // Security settings
            [
                'key' => 'max_login_attempts',
                'value' => 5,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Maximum failed login attempts before account lockout',
                'is_public' => false,
            ],
            [
                'key' => 'account_lockout_duration',
                'value' => 30,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Account lockout duration in minutes',
                'is_public' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => 8,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Minimum password length requirement',
                'is_public' => false,
            ],
            [
                'key' => 'password_expiry_days',
                'value' => 90,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Password expiry in days (0 = never expire)',
                'is_public' => false,
            ],
            [
                'key' => 'require_uppercase',
                'value' => true,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Require uppercase letters in passwords',
                'is_public' => false,
            ],
            [
                'key' => 'require_lowercase',
                'value' => true,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Require lowercase letters in passwords',
                'is_public' => false,
            ],
            [
                'key' => 'require_numbers',
                'value' => true,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Require numbers in passwords',
                'is_public' => false,
            ],
            [
                'key' => 'require_special_chars',
                'value' => false,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Require special characters in passwords',
                'is_public' => false,
            ],
            [
                'key' => 'session_timeout',
                'value' => 120,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Session timeout in minutes',
                'is_public' => false,
            ],
            [
                'key' => 'concurrent_sessions',
                'value' => 3,
                'type' => 'integer',
                'category' => 'security',
                'description' => 'Maximum concurrent sessions per user',
                'is_public' => false,
            ],
            [
                'key' => 'enforce_finance_restriction',
                'value' => true,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Enforce finance access restriction for staff',
                'is_public' => false,
            ],
            [
                'key' => 'log_all_actions',
                'value' => true,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Log all user actions for security auditing',
                'is_public' => false,
            ],
            [
                'key' => 'require_password_change',
                'value' => false,
                'type' => 'boolean',
                'category' => 'security',
                'description' => 'Force password change on first login',
                'is_public' => false,
            ],
        ];

        foreach ($defaultSettings as $setting) {
            SystemSetting::updateOrCreate(
                ['setting_key' => $setting['key']],
                [
                    'setting_value' => SystemSetting::formatValue($setting['value'], $setting['type']),
                    'setting_type' => $setting['type'],
                    'category' => $setting['category'],
                    'description' => $setting['description'],
                    'is_public' => $setting['is_public'],
                ]
            );
        }
    }
}