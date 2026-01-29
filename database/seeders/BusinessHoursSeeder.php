<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHour;

class BusinessHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define default business hours
        $businessHours = [
            [
                'day_of_week' => 0, // Sunday
                'open_time' => null,
                'close_time' => null,
                'is_closed' => true, // Closed on Sunday
            ],
            [
                'day_of_week' => 1, // Monday
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 2, // Tuesday
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 3, // Wednesday
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 4, // Thursday
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 5, // Friday
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
                'is_closed' => false,
            ],
            [
                'day_of_week' => 6, // Saturday
                'open_time' => '10:00:00',
                'close_time' => '15:00:00', // Shorter hours
                'is_closed' => false,
            ],
        ];

        foreach ($businessHours as $hours) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $hours['day_of_week']],
                $hours
            );
        }
    }
}
