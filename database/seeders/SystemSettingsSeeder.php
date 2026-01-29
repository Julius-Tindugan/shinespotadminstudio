<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // General Settings
        SystemSetting::create([
            'setting_key' => 'app_name',
            'setting_value' => 'Studio Admin',
            'setting_type' => 'string',
            'category' => 'general',
            'description' => 'Application name'
        ]);

        SystemSetting::create([
            'setting_key' => 'app_description',
            'setting_value' => 'Professional studio booking management system',
            'setting_type' => 'string',
            'category' => 'general',
            'description' => 'Application description'
        ]);

        SystemSetting::create([
            'setting_key' => 'app_timezone',
            'setting_value' => 'Asia/Manila',
            'setting_type' => 'string',
            'category' => 'general',
            'description' => 'Application timezone'
        ]);

        SystemSetting::create([
            'setting_key' => 'app_language',
            'setting_value' => 'en',
            'setting_type' => 'string',
            'category' => 'general',
            'description' => 'Default application language'
        ]);

        SystemSetting::create([
            'setting_key' => 'maintenance_mode',
            'setting_value' => 'false',
            'setting_type' => 'boolean',
            'category' => 'general',
            'description' => 'Enable/disable maintenance mode'
        ]);

        SystemSetting::create([
            'setting_key' => 'default_currency',
            'setting_value' => 'PHP',
            'setting_type' => 'string',
            'category' => 'general',
            'description' => 'Default currency code'
        ]);

        // Payment Settings
        SystemSetting::create([
            'setting_key' => 'xendit_enabled',
            'setting_value' => 'false',
            'setting_type' => 'boolean',
            'category' => 'payment',
            'description' => 'Enable/disable Xendit payment integration'
        ]);

        SystemSetting::create([
            'setting_key' => 'xendit_secret_key',
            'setting_value' => '',
            'setting_type' => 'string',
            'category' => 'payment',
            'description' => 'Xendit secret key'
        ]);

        SystemSetting::create([
            'setting_key' => 'xendit_public_key',
            'setting_value' => '',
            'setting_type' => 'string',
            'category' => 'payment',
            'description' => 'Xendit public key'
        ]);

        SystemSetting::create([
            'setting_key' => 'xendit_webhook_token',
            'setting_value' => '',
            'setting_type' => 'string',
            'category' => 'payment',
            'description' => 'Xendit webhook verification token'
        ]);

        SystemSetting::create([
            'setting_key' => 'payment_methods',
            'setting_value' => json_encode(['cash', 'bank_transfer', 'gcash']),
            'setting_type' => 'json',
            'category' => 'payment',
            'description' => 'Available payment methods'
        ]);

        // Security Settings
        SystemSetting::create([
            'setting_key' => 'max_login_attempts',
            'setting_value' => '5',
            'setting_type' => 'integer',
            'category' => 'security',
            'description' => 'Maximum login attempts before account lock'
        ]);

        SystemSetting::create([
            'setting_key' => 'account_lockout_duration',
            'setting_value' => '30',
            'setting_type' => 'integer',
            'category' => 'security',
            'description' => 'Account lockout duration in minutes'
        ]);

        SystemSetting::create([
            'setting_key' => 'password_min_length',
            'setting_value' => '8',
            'setting_type' => 'integer',
            'category' => 'security',
            'description' => 'Minimum password length'
        ]);

        SystemSetting::create([
            'setting_key' => 'password_require_uppercase',
            'setting_value' => 'true',
            'setting_type' => 'boolean',
            'category' => 'security',
            'description' => 'Require uppercase letters in passwords'
        ]);

        SystemSetting::create([
            'setting_key' => 'password_require_lowercase',
            'setting_value' => 'true',
            'setting_type' => 'boolean',
            'category' => 'security',
            'description' => 'Require lowercase letters in passwords'
        ]);

        SystemSetting::create([
            'setting_key' => 'password_require_numbers',
            'setting_value' => 'true',
            'setting_type' => 'boolean',
            'category' => 'security',
            'description' => 'Require numbers in passwords'
        ]);

        SystemSetting::create([
            'setting_key' => 'password_require_symbols',
            'setting_value' => 'false',
            'setting_type' => 'boolean',
            'category' => 'security',
            'description' => 'Require special characters in passwords'
        ]);

        SystemSetting::create([
            'setting_key' => 'session_timeout',
            'setting_value' => '120',
            'setting_type' => 'integer',
            'category' => 'security',
            'description' => 'Session timeout in minutes'
        ]);

        SystemSetting::create([
            'setting_key' => 'two_factor_enabled',
            'setting_value' => 'false',
            'setting_type' => 'boolean',
            'category' => 'security',
            'description' => 'Enable two-factor authentication'
        ]);

        // Notification Settings
        SystemSetting::create([
            'setting_key' => 'email_notifications',
            'setting_value' => 'true',
            'setting_type' => 'boolean',
            'category' => 'notifications',
            'description' => 'Enable email notifications'
        ]);

        SystemSetting::create([
            'setting_key' => 'sms_notifications',
            'setting_value' => 'false',
            'setting_type' => 'boolean',
            'category' => 'notifications',
            'description' => 'Enable SMS notifications'
        ]);

        SystemSetting::create([
            'setting_key' => 'booking_reminder_hours',
            'setting_value' => '24',
            'setting_type' => 'integer',
            'category' => 'notifications',
            'description' => 'Hours before booking to send reminder'
        ]);

        // Business Settings
        SystemSetting::create([
            'setting_key' => 'advance_booking_days',
            'setting_value' => '30',
            'setting_type' => 'integer',
            'category' => 'business',
            'description' => 'Maximum days in advance for bookings'
        ]);

        SystemSetting::create([
            'setting_key' => 'cancellation_policy_hours',
            'setting_value' => '24',
            'setting_type' => 'integer',
            'category' => 'business',
            'description' => 'Minimum hours before booking for cancellation'
        ]);

        SystemSetting::create([
            'setting_key' => 'default_package_duration',
            'setting_value' => '60',
            'setting_type' => 'integer',
            'category' => 'business',
            'description' => 'Default package duration in minutes'
        ]);
    }
}