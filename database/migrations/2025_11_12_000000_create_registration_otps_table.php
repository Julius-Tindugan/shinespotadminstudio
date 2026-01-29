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
        if (!Schema::hasTable('registration_otps')) {
            Schema::create('registration_otps', function (Blueprint $table) {
                $table->id('otp_id');
                $table->string('email', 100);
                $table->string('phone', 20)->nullable();
                $table->string('user_type', 20); // 'admin' or 'staff'
                $table->string('otp_code', 10);
                $table->timestamp('otp_expires_at');
                $table->integer('attempts')->default(0);
                $table->timestamp('locked_until')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->timestamp('verified_at')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->integer('resend_count')->default(0);
                $table->timestamp('last_resent_at')->nullable();
                $table->string('session_token', 64)->unique();
                $table->timestamps();
                
                // Indexes for performance
                $table->index('session_token');
                $table->index(['email', 'user_type']);
                $table->index('otp_expires_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_otps');
    }
};
