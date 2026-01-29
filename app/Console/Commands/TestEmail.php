<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';
    protected $description = 'Test email sending functionality';

    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            $this->info('Mail Configuration:');
            $this->info('Driver: ' . config('mail.default'));
            $this->info('Host: ' . config('mail.mailers.smtp.host'));
            $this->info('Port: ' . config('mail.mailers.smtp.port'));
            $this->info('Username: ' . config('mail.mailers.smtp.username'));
            $this->info('From: ' . config('mail.from.address'));
            $this->info('');
            
            $this->info('Sending test email to ' . $email . '...');
            
            Mail::raw('This is a test email from Shine Spot Admin to verify SMTP configuration is working correctly.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Shine Spot Admin');
            });
            
            $this->info('✅ Email sent successfully!');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error sending email: ' . $e->getMessage());
            $this->error('');
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
