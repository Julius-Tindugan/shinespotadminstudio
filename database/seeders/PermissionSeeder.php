<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            // Booking permissions
            ['permission_name' => 'booking.view', 'description' => 'View bookings'],
            ['permission_name' => 'booking.create', 'description' => 'Create bookings'],
            ['permission_name' => 'booking.edit', 'description' => 'Edit bookings'],
            ['permission_name' => 'booking.delete', 'description' => 'Delete bookings'],
            
            // User permissions
            ['permission_name' => 'user.view', 'description' => 'View users'],
            ['permission_name' => 'user.create', 'description' => 'Create users'],
            ['permission_name' => 'user.edit', 'description' => 'Edit users'],
            ['permission_name' => 'user.delete', 'description' => 'Delete users'],
            
            // Staff permissions
            ['permission_name' => 'staff.view', 'description' => 'View staff'],
            ['permission_name' => 'staff.create', 'description' => 'Create staff'],
            ['permission_name' => 'staff.edit', 'description' => 'Edit staff'],
            ['permission_name' => 'staff.delete', 'description' => 'Delete staff'],
            
            // Package permissions
            ['permission_name' => 'package.view', 'description' => 'View packages'],
            ['permission_name' => 'package.create', 'description' => 'Create packages'],
            ['permission_name' => 'package.edit', 'description' => 'Edit packages'],
            ['permission_name' => 'package.delete', 'description' => 'Delete packages'],
            
            // Settings permissions
            ['permission_name' => 'settings.view', 'description' => 'View settings'],
            ['permission_name' => 'settings.edit', 'description' => 'Edit settings'],
            
            // Reports permissions
            ['permission_name' => 'reports.view', 'description' => 'View reports'],
            ['permission_name' => 'reports.export', 'description' => 'Export reports'],
            
            // Admin permissions
            ['permission_name' => 'admin.access', 'description' => 'Access admin area'],
            ['permission_name' => 'admin.manage_roles', 'description' => 'Manage roles and permissions'],
        ];
        
        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['permission_name' => $permission['permission_name']],
                ['description' => $permission['description']]
            );
        }
        
        // Assign permissions to roles
        $superAdmin = Role::where('role_name', 'Super Admin')->first();
        $admin = Role::where('role_name', 'Admin')->first();
        $manager = Role::where('role_name', 'Manager')->first();
        $staff = Role::where('role_name', 'Staff')->first();
        $photographer = Role::where('role_name', 'Photographer')->first();
        $reception = Role::where('role_name', 'Reception')->first();
        
        if ($superAdmin) {
            // Super Admin gets all permissions
            $allPermissions = Permission::all();
            $superAdmin->permissions()->sync($allPermissions->pluck('permission_id')->toArray());
        }
        
        if ($admin) {
            // Admin gets most permissions except some super admin ones
            $adminPermissions = Permission::whereNotIn('permission_name', [
                'admin.manage_roles',
            ])->get();
            $admin->permissions()->sync($adminPermissions->pluck('permission_id')->toArray());
        }
        
        if ($manager) {
            // Manager gets operational permissions
            $managerPermissions = Permission::whereIn('permission_name', [
                'admin.access',
                'booking.view', 'booking.create', 'booking.edit',
                'user.view', 'user.create', 'user.edit',
                'staff.view',
                'package.view',
                'reports.view', 'reports.export',
                'settings.view',
            ])->get();
            $manager->permissions()->sync($managerPermissions->pluck('permission_id')->toArray());
        }
        
        if ($staff) {
            // Regular staff gets basic permissions
            $staffPermissions = Permission::whereIn('permission_name', [
                'admin.access',
                'booking.view', 'booking.create',
                'user.view',
                'package.view',
            ])->get();
            $staff->permissions()->sync($staffPermissions->pluck('permission_id')->toArray());
        }
        
        if ($photographer) {
            // Photographer gets photography related permissions
            $photographerPermissions = Permission::whereIn('permission_name', [
                'admin.access',
                'booking.view',
            ])->get();
            $photographer->permissions()->sync($photographerPermissions->pluck('permission_id')->toArray());
        }
        
        if ($reception) {
            // Reception gets front desk permissions
            $receptionPermissions = Permission::whereIn('permission_name', [
                'admin.access',
                'booking.view', 'booking.create', 'booking.edit',
                'user.view', 'user.create',
                'package.view',
            ])->get();
            $reception->permissions()->sync($receptionPermissions->pluck('permission_id')->toArray());
        }
    }
}
