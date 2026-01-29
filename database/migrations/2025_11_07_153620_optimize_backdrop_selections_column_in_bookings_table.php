<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration optimizes the backdrop_selections column to properly store
     * JSON data with validation constraint for data integrity.
     */
    public function up(): void
    {
        // Get the database connection type
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // For MySQL/MariaDB, ensure the column is JSON type with validation
            DB::statement('ALTER TABLE bookings MODIFY backdrop_selections JSON NULL');
            
            // Add a comment to document the expected structure
            DB::statement("
                ALTER TABLE bookings 
                MODIFY backdrop_selections JSON NULL 
                COMMENT 'Stores array of backdrop IDs or objects. Format: {\"1\":\"5\",\"2\":\"10\"} or [{\"id\":5},{\"id\":10}]'
            ");
        } else {
            // For other databases, use Laravel's json column type
            Schema::table('bookings', function (Blueprint $table) {
                $table->json('backdrop_selections')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to longtext
        if (Schema::getConnection()->getDriverName() === 'mysql' || 
            Schema::getConnection()->getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE bookings MODIFY backdrop_selections LONGTEXT NULL');
        } else {
            Schema::table('bookings', function (Blueprint $table) {
                $table->longText('backdrop_selections')->nullable()->change();
            });
        }
    }
};
