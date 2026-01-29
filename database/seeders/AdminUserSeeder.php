<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default super admin user
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@shinespot.com'],
            [
                'username' => 'admin',
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'password' => 'password123', // This will be hashed by the model setter
                'is_active' => true,
            ]
        );
        
        // Create a test admin user
        $testAdmin = Admin::firstOrCreate(
            ['email' => 'test@shinespot.com'],
            [
                'username' => 'testadmin',
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'password' => 'test123', // This will be hashed by the model setter
                'is_active' => true,
            ]
        );
        
        // Assign super admin role to admin user if the role exists
        $superAdminRole = Role::where('role_name', 'Super Admin')->first();
        if ($superAdminRole && $admin) {
            if (!$admin->hasRoleId($superAdminRole->role_id)) {
                $admin->roles()->attach($superAdminRole->role_id);
            }
        }
        
        // Assign admin role to test admin user if the role exists
        $adminRole = Role::where('role_name', 'Admin')->first();
        if ($adminRole && $testAdmin) {
            if (!$testAdmin->hasRoleId($adminRole->role_id)) {
                $testAdmin->roles()->attach($adminRole->role_id);
            }
        }
    }
}
