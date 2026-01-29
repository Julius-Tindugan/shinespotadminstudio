<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
           
            [
                'role_name' => 'Admin',
                'description' => 'Has access to most administrative features',
            ],
           
            [
                'role_name' => 'Staff',
                'description' => 'Regular staff with limited permissions',
            ],
           
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['role_name' => $role['role_name']],
                ['description' => $role['description']]
            );
        }
    }
}
