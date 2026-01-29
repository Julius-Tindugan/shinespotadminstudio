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
        Schema::table('packages', function (Blueprint $table) {
            // Change duration_hours from int to decimal(5,2) to support fractional hours
            // This allows values like 2.5 for 2h 30m, 0.25 for 15m, etc.
            $table->decimal('duration_hours', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('duration_hours')->nullable()->change();
        });
    }
};
