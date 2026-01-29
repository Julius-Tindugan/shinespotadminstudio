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
        Schema::table('staff_users', function (Blueprint $table) {
            // Add username field if it doesn't exist
            if (!Schema::hasColumn('staff_users', 'username')) {
                $table->string('username', 50)->unique()->after('staff_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            if (Schema::hasColumn('staff_users', 'username')) {
                $table->dropColumn('username');
            }
        });
    }
};
