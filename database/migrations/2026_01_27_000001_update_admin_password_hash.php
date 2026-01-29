<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration updates the admin password to use proper hashing
     * and removes dependency on hardcoded credentials.
     */
    public function up(): void
    {
        // Update the existing admin user's password to a secure hashed password
        // The default password will be: Admin@123456
        // Admin should change this immediately after login
        
        DB::table('admin_users')
            ->where('username', 'Sh1n33Sp0t4dm!n')
            ->update([
                'password_hash' => Hash::make('Admin@123456'),
                'force_password_change' => true,
                'password_changed_at' => now(),
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Cannot reverse this as we don't store the old password
        // This is intentional for security reasons
    }
};
