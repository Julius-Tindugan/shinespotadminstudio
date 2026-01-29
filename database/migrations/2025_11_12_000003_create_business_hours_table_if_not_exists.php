<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create if table doesn't exist
        if (!Schema::hasTable('business_hours')) {
            Schema::create('business_hours', function (Blueprint $table) {
                $table->id();
                $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
                $table->time('open_time')->nullable();
                $table->time('close_time')->nullable();
                $table->boolean('is_closed')->default(false);
                $table->timestamps();
                
                // Add unique constraint to prevent duplicate days
                $table->unique('day_of_week');
                
                // Add index for faster queries
                $table->index('day_of_week');
            });
            
            // Seed default business hours
            $this->seedDefaultBusinessHours();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
    
    /**
     * Seed default business hours
     */
    private function seedDefaultBusinessHours(): void
    {
        $defaultHours = [
            ['day_of_week' => 0, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => true],  // Sunday
            ['day_of_week' => 1, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => false], // Monday
            ['day_of_week' => 2, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => false], // Tuesday
            ['day_of_week' => 3, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => false], // Wednesday
            ['day_of_week' => 4, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => false], // Thursday
            ['day_of_week' => 5, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => false], // Friday
            ['day_of_week' => 6, 'open_time' => '09:00:00', 'close_time' => '17:00:00', 'is_closed' => true],  // Saturday
        ];
        
        foreach ($defaultHours as $hours) {
            \DB::table('business_hours')->insert(array_merge($hours, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
};
