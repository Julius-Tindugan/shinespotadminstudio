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
        Schema::table('registration_otps', function (Blueprint $table) {
            // Make phone nullable since we're using email OTP now
            $table->string('phone', 20)->nullable()->change();
        });

        // Add indexes only if they don't exist
        $indexExists = collect(DB::select("SHOW INDEXES FROM registration_otps WHERE Key_name = 'registration_otps_email_index'"))->isNotEmpty();
        if (!$indexExists) {
            Schema::table('registration_otps', function (Blueprint $table) {
                $table->index('email');
            });
        }

        $indexExists = collect(DB::select("SHOW INDEXES FROM registration_otps WHERE Key_name = 'registration_otps_is_verified_index'"))->isNotEmpty();
        if (!$indexExists) {
            Schema::table('registration_otps', function (Blueprint $table) {
                $table->index('is_verified');
            });
        }

        $indexExists = collect(DB::select("SHOW INDEXES FROM registration_otps WHERE Key_name = 'idx_email_usertype_verified'"))->isNotEmpty();
        if (!$indexExists) {
            Schema::table('registration_otps', function (Blueprint $table) {
                $table->index(['email', 'user_type', 'is_verified'], 'idx_email_usertype_verified');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_otps', function (Blueprint $table) {
            // Drop indices if they exist
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('registration_otps');
            
            if (array_key_exists('registration_otps_email_index', $indexes)) {
                $table->dropIndex(['email']);
            }
            if (array_key_exists('registration_otps_is_verified_index', $indexes)) {
                $table->dropIndex(['is_verified']);
            }
            if (array_key_exists('idx_email_usertype_verified', $indexes)) {
                $table->dropIndex('idx_email_usertype_verified');
            }
            
            // Make phone required again
            $table->string('phone', 20)->nullable(false)->change();
        });
    }
};
