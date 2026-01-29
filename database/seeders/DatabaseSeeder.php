<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run database seeders
        $this->call([
            AdminUserSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            DemoDataSeeder::class,
            RevenueStatisticsSeeder::class,
            PackageRelatedTablesSeeder::class,
            BackdropSeeder::class,
            ServiceSeeder::class,
            BusinessHoursSeeder::class,
            PaymentMethodSeeder::class,
            ExpenseCategorySeeder::class,
        ]);
    }
}
