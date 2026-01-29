<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('payment_methods')->insert([
            [
                'name' => 'Cash',
                'description' => 'Cash payment',
                'icon' => 'fa-money-bill',
                'fee' => 0.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Credit Card',
                'description' => 'Credit card payment',
                'icon' => 'fa-credit-card',
                'fee' => 2.50,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bank Transfer',
                'description' => 'Direct bank transfer',
                'icon' => 'fa-university',
                'fee' => 0.00,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PayPal',
                'description' => 'PayPal payment',
                'icon' => 'fa-paypal',
                'fee' => 3.50,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'GCash',
                'description' => 'GCash payment',
                'icon' => 'fa-mobile-alt',
                'fee' => 2.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maya',
                'description' => 'Maya payment',
                'icon' => 'fa-wallet',
                'fee' => 2.00,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
