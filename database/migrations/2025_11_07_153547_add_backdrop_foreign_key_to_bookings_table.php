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
     * This migration adds a proper foreign key constraint to the backdrop_id column
     * in the bookings table to ensure data integrity and prevent orphaned records.
     */
    public function up(): void
    {
        // First, ensure any invalid backdrop_ids are set to NULL
        DB::statement("
            UPDATE bookings 
            SET backdrop_id = NULL 
            WHERE backdrop_id IS NOT NULL 
            AND backdrop_id NOT IN (SELECT backdrop_id FROM backdrops)
        ");
        
        Schema::table('bookings', function (Blueprint $table) {
            // Check if foreign key already exists
            $foreignKeys = $this->getTableForeignKeys('bookings');
            
            // Drop existing foreign key if it exists
            if (in_array('bookings_backdrop_id_foreign', $foreignKeys)) {
                $table->dropForeign('bookings_backdrop_id_foreign');
            }
            
            // Add foreign key constraint with SET NULL on delete
            // This ensures that when a backdrop is deleted, bookings remain but backdrop_id is set to NULL
            $table->foreign('backdrop_id', 'bookings_backdrop_id_foreign')
                  ->references('backdrop_id')
                  ->on('backdrops')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop the foreign key constraint
            if ($this->foreignKeyExists('bookings', 'bookings_backdrop_id_foreign')) {
                $table->dropForeign('bookings_backdrop_id_foreign');
            }
        });
    }
    
    /**
     * Get all foreign keys for a table
     */
    private function getTableForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
            AND TABLE_SCHEMA = ? 
            AND TABLE_NAME = ?
        ", [$dbName, $table]);
        
        return array_map(function($key) {
            return $key->CONSTRAINT_NAME;
        }, $foreignKeys);
    }
    
    /**
     * Check if foreign key exists
     */
    private function foreignKeyExists($table, $name)
    {
        return in_array($name, $this->getTableForeignKeys($table));
    }
};
