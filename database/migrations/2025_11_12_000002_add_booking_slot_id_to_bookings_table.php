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
        Schema::table('bookings', function (Blueprint $table) {
            // Add booking_slot_id column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'booking_slot_id')) {
                $table->unsignedBigInteger('booking_slot_id')->nullable()->after('booking_date');
                
                // Add foreign key constraint
                $table->foreign('booking_slot_id')
                      ->references('slot_id')
                      ->on('booking_slots')
                      ->onDelete('set null');
                
                // Add index for faster queries
                $table->index('booking_slot_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'booking_slot_id')) {
                // Drop foreign key and column
                $table->dropForeign(['booking_slot_id']);
                $table->dropColumn('booking_slot_id');
            }
        });
    }
};
