<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('expense_categories')->insert([
            [
                'name' => 'Rent',
                'description' => 'Studio rental expenses',
                'color_code' => '#FF5733',
                'color' => '#FF5733',
                'budget_tracking' => true,
                'budget' => 20000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Equipment',
                'description' => 'Camera equipment, lighting, etc.',
                'color_code' => '#33A1FF',
                'color' => '#33A1FF',
                'budget_tracking' => true,
                'budget' => 15000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Utilities',
                'description' => 'Electricity, water, internet, etc.',
                'color_code' => '#33FF57',
                'color' => '#33FF57',
                'budget_tracking' => true,
                'budget' => 8000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salary',
                'description' => 'Staff salaries and compensation',
                'color_code' => '#A233FF',
                'color' => '#A233FF',
                'budget_tracking' => true,
                'budget' => 25000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'description' => 'Advertising and promotion expenses',
                'color_code' => '#FF33A2',
                'color' => '#FF33A2',
                'budget_tracking' => true,
                'budget' => 10000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Props',
                'description' => 'Studio props and consumables',
                'color_code' => '#FFD700',
                'color' => '#FFD700',
                'budget_tracking' => false,
                'budget' => 0.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maintenance',
                'description' => 'Studio and equipment maintenance',
                'color_code' => '#8B4513',
                'color' => '#8B4513',
                'budget_tracking' => true,
                'budget' => 5000.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Other',
                'description' => 'Miscellaneous expenses',
                'color_code' => '#808080',
                'color' => '#808080',
                'budget_tracking' => false,
                'budget' => 0.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
