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
        Schema::table('registration_otps', function (Blueprint $table) {
            // Check and add missing columns
            if (!Schema::hasColumn('registration_otps', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('attempts');
            }
            
            if (!Schema::hasColumn('registration_otps', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('is_verified');
            }
            
            if (!Schema::hasColumn('registration_otps', 'resend_count')) {
                $table->integer('resend_count')->default(0)->after('user_agent');
            }
            
            if (!Schema::hasColumn('registration_otps', 'last_resent_at')) {
                $table->timestamp('last_resent_at')->nullable()->after('resend_count');
            }
            
            if (!Schema::hasColumn('registration_otps', 'session_token')) {
                $table->string('session_token', 64)->nullable()->unique()->after('last_resent_at');
            }
            
            // Rename expires_at to otp_expires_at if needed
            if (Schema::hasColumn('registration_otps', 'expires_at') && !Schema::hasColumn('registration_otps', 'otp_expires_at')) {
                $table->renameColumn('expires_at', 'otp_expires_at');
            }
            
            // Drop old purpose column if exists
            if (Schema::hasColumn('registration_otps', 'purpose')) {
                $table->dropColumn('purpose');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_otps', function (Blueprint $table) {
            if (Schema::hasColumn('registration_otps', 'otp_expires_at')) {
                $table->renameColumn('otp_expires_at', 'expires_at');
            }
            
            $columns = ['locked_until', 'verified_at', 'resend_count', 'last_resent_at', 'session_token'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('registration_otps', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            if (!Schema::hasColumn('registration_otps', 'purpose')) {
                $table->string('purpose', 50)->default('registration');
            }
        });
    }
};
