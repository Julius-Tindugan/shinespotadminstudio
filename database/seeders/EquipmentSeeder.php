<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create initial equipment options
        $equipment = [
            [
                'name' => 'Canon EOS R5',
                'type' => 'Camera',
                'description' => 'Full-frame mirrorless camera with 45MP sensor',
                'quantity' => 2,
                'cost' => 3899.99,
                'purchase_date' => '2023-05-15',
                'condition' => 'Excellent',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Sony A7 III',
                'type' => 'Camera',
                'description' => 'Full-frame mirrorless camera with 24MP sensor',
                'quantity' => 1,
                'cost' => 1999.99,
                'purchase_date' => '2022-10-12',
                'condition' => 'Good',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Godox SL-60W LED Light',
                'type' => 'Lighting',
                'description' => '60W LED continuous light with Bowens mount',
                'quantity' => 4,
                'cost' => 139.99,
                'purchase_date' => '2023-01-20',
                'condition' => 'Good',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Softbox 80x80cm',
                'type' => 'Lighting Modifier',
                'description' => 'Square softbox with Bowens mount',
                'quantity' => 3,
                'cost' => 59.99,
                'purchase_date' => '2023-01-20',
                'condition' => 'Good',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Party Props Set',
                'type' => 'Prop',
                'description' => 'Set of 20 photo booth props including hats, glasses, and signs',
                'quantity' => 2,
                'cost' => 29.99,
                'purchase_date' => '2023-06-10',
                'condition' => 'Good',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Animal Masks Set',
                'type' => 'Prop',
                'description' => 'Set of 10 animal face masks',
                'quantity' => 2,
                'cost' => 24.99,
                'purchase_date' => '2023-07-05',
                'condition' => 'Excellent',
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Tripod - Manfrotto 055',
                'type' => 'Support',
                'description' => 'Professional camera tripod',
                'quantity' => 3,
                'cost' => 219.99,
                'purchase_date' => '2023-03-15',
                'condition' => 'Good',
                'is_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::create($item);
        }
    }
}
