<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailOTPService;
use App\Models\RegistrationOTP;
use Illuminate\Support\Facades\Schema;

class TestOTPSystem extends Command
{
    protected $signature = 'otp:test';
    protected $description = 'Test the Email OTP system configuration and availability';

    public function handle()
    {
        $this->info('=== Email OTP System Verification ===');
        $this->newLine();

        // Configuration Check
        $this->info('1. Configuration Check:');
        $this->line('   OTP Length: ' . config('otp.length'));
        $this->line('   Expiration: ' . config('otp.expiration_minutes') . ' minutes');
        $this->line('   Max Attempts: ' . config('otp.max_attempts'));
        $this->line('   Lockout: ' . config('otp.lockout_minutes') . ' minutes');
        $this->line('   Max Resends: ' . config('otp.max_resends'));
        $this->line('   Email Host: ' . config('mail.mailers.smtp.host'));
        $this->newLine();

        // Service Check
        $this->info('2. Service Check:');
        try {
            $service = app(EmailOTPService::class);
            $this->line('   ✅ EmailOTPService: Available');
            
            $status = $service->getServiceStatus();
            $this->line('   Email Enabled: ' . ($status['email_enabled'] ? 'Yes' : 'No'));
            $this->line('   Email Configured: ' . ($status['email_configured'] ? 'Yes' : 'No'));
        } catch (\Exception $e) {
            $this->error('   ❌ EmailOTPService: ' . $e->getMessage());
        }
        $this->newLine();

        // Database Check
        $this->info('3. Database Check:');
        $tableExists = Schema::hasTable('registration_otps');
        $this->line('   Table exists: ' . ($tableExists ? 'Yes' : 'No'));
        
        if ($tableExists) {
            $count = RegistrationOTP::count();
            $this->line('   Total OTPs: ' . $count);
            
            $columns = ['otp_id', 'email', 'phone', 'user_type', 'otp_code', 'session_token', 'is_verified'];
            $this->line('   Columns: ' . implode(', ', $columns));
        }
        $this->newLine();

        // Routes Check
        $this->info('4. Routes Check:');
        $routes = [
            'admin.register.send-otp',
            'admin.register.verify-otp',
            'admin.register.resend-otp',
            'staff.register.send-otp',
            'staff.register.verify-otp',
            'staff.register.resend-otp',
        ];
        
        foreach ($routes as $routeName) {
            $exists = \Route::has($routeName);
            $status = $exists ? '✅' : '❌';
            $this->line("   $status $routeName");
        }
        $this->newLine();

        $this->info('✅ System verification complete!');
        $this->newLine();
        
        $this->comment('Next steps:');
        $this->line('1. Test sending OTP: POST /admin/register/send-otp');
        $this->line('2. Check your email inbox');
        $this->line('3. Verify OTP: POST /admin/register/verify-otp');
        
        return 0;
    }
}
