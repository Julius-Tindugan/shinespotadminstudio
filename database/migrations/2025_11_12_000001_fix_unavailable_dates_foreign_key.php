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
        // Get the actual foreign key name from the database
        $foreignKeys = $this->getForeignKeys('unavailable_dates');
        
        Schema::table('unavailable_dates', function (Blueprint $table) use ($foreignKeys) {
            // Drop the existing foreign key if it exists
            foreach ($foreignKeys as $fk) {
                if ($fk->COLUMN_NAME === 'created_by') {
                    $table->dropForeign([$fk->COLUMN_NAME]);
                }
            }
            
            // Add the correct foreign key constraint
            $table->foreign('created_by')
                  ->references('admin_id')
                  ->on('admin_users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unavailable_dates', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['created_by']);
            
            // Note: Not restoring old FK as it was incorrect
        });
    }
    
    /**
     * Get foreign keys for a table
     */
    private function getForeignKeys($table)
    {
        $conn = Schema::getConnection();
        $dbName = $conn->getDatabaseName();
        
        return DB::select("
            SELECT 
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$dbName, $table]);
    }
};
