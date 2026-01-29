<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationOtpMail;

class TestEmailSending extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email sending with OTP';

    public function handle()
    {
        $email = $this->argument('email');
        $otpCode = rand(100000, 999999);
        
        $this->info("Attempting to send OTP email to: {$email}");
        $this->info("OTP Code: {$otpCode}");
        
        try {
            Mail::to($email)->send(new RegistrationOtpMail(
                $otpCode,
                'Test User',
                'admin',
                10
            ));
            
            $this->info("✅ Email sent successfully!");
            $this->info("Check your inbox: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
        
        return 0;
    }
}
