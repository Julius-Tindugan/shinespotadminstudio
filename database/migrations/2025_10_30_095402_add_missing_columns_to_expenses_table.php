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
        Schema::table('expenses', function (Blueprint $table) {
            // Add title column before description
            $table->string('title', 255)->after('expense_id')->nullable();
            
            // Add booking relationship
            $table->unsignedBigInteger('booking_id')->nullable()->after('category');
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('set null');
            
            // Add vendor and receipt tracking
            $table->string('vendor', 255)->nullable()->after('booking_id');
            $table->string('receipt_number', 100)->nullable()->after('vendor');
            $table->string('receipt_image', 255)->nullable()->after('receipt_number');
            
            // Add recurring expense support
            $table->boolean('is_recurring')->default(false)->after('receipt_image');
            $table->enum('recurring_interval', ['daily', 'weekly', 'monthly', 'yearly'])->nullable()->after('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop columns in reverse order
            $table->dropColumn(['recurring_interval', 'is_recurring', 'receipt_image', 'receipt_number', 'vendor']);
            $table->dropForeign(['booking_id']);
            $table->dropColumn('booking_id');
            $table->dropColumn('title');
        });
    }
};
