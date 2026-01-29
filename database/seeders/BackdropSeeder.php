<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backdrop;

class BackdropSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create initial backdrop options
        $backdrops = [
            [
                'name' => 'White',
                'color_code' => '#FFFFFF',
                'description' => 'Standard white backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Black',
                'color_code' => '#000000',
                'description' => 'Standard black backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Gray',
                'color_code' => '#808080',
                'description' => 'Standard gray backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Beige',
                'color_code' => '#F5F5DC',
                'description' => 'Neutral beige backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Blue',
                'color_code' => '#0000FF',
                'description' => 'Blue backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Green',
                'color_code' => '#008000',
                'description' => 'Green backdrop',
                'is_active' => true
            ],
            [
                'name' => 'Pink',
                'color_code' => '#FFC0CB',
                'description' => 'Pink backdrop',
                'is_active' => true
            ],
        ];

        foreach ($backdrops as $backdrop) {
            Backdrop::create($backdrop);
        }
    }
}
