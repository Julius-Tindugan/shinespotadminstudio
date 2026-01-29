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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->bigIncrements('transaction_id');
            $table->unsignedBigInteger('booking_id');
            $table->enum('payment_method', ['gcash', 'onsite_cash', 'onsite_card']);
            $table->decimal('amount', 10, 2);
            $table->string('transaction_reference', 100)->nullable();
            $table->string('xendit_payment_id', 100)->nullable();
            $table->string('xendit_status', 50)->nullable();
            $table->timestamp('payment_date')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('booking_id')->references('booking_id')->on('bookings')->onDelete('cascade');
            
            // Indexes
            $table->index('xendit_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
