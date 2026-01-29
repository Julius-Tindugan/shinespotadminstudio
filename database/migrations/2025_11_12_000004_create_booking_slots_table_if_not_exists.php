<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only create if table doesn't exist
        if (!Schema::hasTable('booking_slots')) {
            Schema::create('booking_slots', function (Blueprint $table) {
                $table->id('slot_id');
                $table->date('date');
                $table->time('time_slot');
                $table->integer('max_bookings')->default(1);
                $table->integer('current_bookings')->default(0);
                $table->boolean('is_available')->default(true);
                $table->timestamps();
                
                // Add unique constraint to prevent duplicate slots for same date/time
                $table->unique(['date', 'time_slot']);
                
                // Add indexes for faster queries
                $table->index('date');
                $table->index('is_available');
                $table->index(['date', 'is_available']);
            });
        } else {
            // If table exists, add missing indexes if they don't exist
            $this->addMissingIndexes();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_slots');
    }
    
    /**
     * Add missing indexes to existing table
     */
    private function addMissingIndexes(): void
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        // Get existing indexes
        $indexes = DB::select("
            SELECT DISTINCT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'booking_slots'
        ", [$dbName]);
        
        $existingIndexes = array_column($indexes, 'INDEX_NAME');
        
        Schema::table('booking_slots', function (Blueprint $table) use ($existingIndexes) {
            // Add individual date index if not exists
            if (!in_array('booking_slots_date_index', $existingIndexes)) {
                $table->index('date');
            }
            
            // Add is_available index if not exists
            if (!in_array('booking_slots_is_available_index', $existingIndexes)) {
                $table->index('is_available');
            }
            
            // Note: Composite index might already exist with different name
            // Check if any index covers both columns
            $hasCompositeIndex = false;
            foreach ($existingIndexes as $idx) {
                if (str_contains($idx, 'date') && str_contains($idx, 'is_available')) {
                    $hasCompositeIndex = true;
                    break;
                }
            }
            
            if (!$hasCompositeIndex && !in_array('booking_slots_date_is_available_index', $existingIndexes)) {
                $table->index(['date', 'is_available']);
            }
        });
    }
};
