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
        // Remove firebase_uid column from admin_users table
        Schema::table('admin_users', function (Blueprint $table) {
            $table->dropIndex(['firebase_uid']); // Drop index first
            $table->dropUnique(['firebase_uid']); // Drop unique constraint
            $table->dropColumn('firebase_uid');
        });
        
        // Remove firebase_uid column from staff_users table
        Schema::table('staff_users', function (Blueprint $table) {
            $table->dropIndex(['firebase_uid']); // Drop index first
            $table->dropUnique(['firebase_uid']); // Drop unique constraint
            $table->dropColumn('firebase_uid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add firebase_uid column back to admin_users table
        Schema::table('admin_users', function (Blueprint $table) {
            $table->string('firebase_uid', 255)->nullable()->after('admin_id');
            $table->unique('firebase_uid');
            $table->index('firebase_uid');
        });
        
        // Add firebase_uid column back to staff_users table
        Schema::table('staff_users', function (Blueprint $table) {
            $table->string('firebase_uid', 255)->nullable()->after('staff_id');
            $table->unique('firebase_uid');
            $table->index('firebase_uid');
        });
    }
};
