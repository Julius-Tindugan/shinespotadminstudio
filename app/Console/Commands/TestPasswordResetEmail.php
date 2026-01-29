<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use App\Models\Staff;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestPasswordResetEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset email sending to a specific email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing password reset email for: {$email}");
        $this->newLine();
        
        // Check mail configuration
        $this->info("Mail Configuration:");
        $this->line("  Driver: " . config('mail.default'));
        $this->line("  Host: " . config('mail.mailers.smtp.host'));
        $this->line("  Port: " . config('mail.mailers.smtp.port'));
        $this->line("  From Address: " . config('mail.from.address'));
        $this->line("  From Name: " . config('mail.from.name'));
        $this->newLine();
        
        try {
            $this->info("Step 1: Validating email exists in database...");
            
            // Find user in database
            $user = Admin::where('email', $email)->first();
            $userType = 'admin';
            
            if (!$user) {
                $user = Staff::where('email', $email)->first();
                $userType = 'staff';
            }
            
            if (!$user) {
                $this->error("Email not found in database!");
                return 1;
            }
            
            $fullName = $user->first_name . ' ' . $user->last_name;
            $this->info("✓ Email found: {$fullName} ({$userType})");
            $this->newLine();
            
            $this->info("Step 2: Generating password reset token...");
            $token = Str::random(64);
            
            // Store token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => hash('sha256', $token),
                    'created_at' => now(),
                    'expires_at' => now()->addHours(1),
                    'used_at' => null,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Test Command'
                ]
            );
            
            $resetUrl = url('/password/reset/' . $token . '?email=' . urlencode($email));
            $this->info("✓ Token generated");
            $this->newLine();
            
            $this->info("Step 3: Sending password reset email...");
            
            Mail::to($email)->send(new PasswordResetMail($fullName, $resetUrl, '127.0.0.1'));
            
            $this->info("✓ Email sent successfully!");
            $this->line("Reset URL: {$resetUrl}");
            $this->newLine();
            $this->warn("IMPORTANT: Check the following:");
            $this->line("1. Check your email inbox for: {$email}");
            $this->line("2. Check your spam/junk folder");
            $this->line("3. Check Laravel logs: storage/logs/laravel.log");
            $this->line("4. If using Brevo, check their dashboard for delivery status");
            $this->newLine();
            $this->info("If email is not received, try:");
            $this->line("1. Verify sender email is configured in Brevo");
            $this->line("2. Check Brevo sending limits/quota");
            $this->line("3. Temporarily change MAIL_MAILER=log in .env to see email in logs");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Exception occurred!");
            $this->line("Error: " . $e->getMessage());
            $this->line("File: " . $e->getFile() . ":" . $e->getLine());
            return 1;
        }
    }
}
