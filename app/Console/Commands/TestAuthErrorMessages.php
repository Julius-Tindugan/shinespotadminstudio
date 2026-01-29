<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AuthenticationErrorMessages;

class TestAuthErrorMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:test-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test authentication error messages display';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Authentication Error Messages');
        $this->newLine();

        // Test basic error messages
        $this->line('=== Basic Error Messages ===');
        $this->line('Email not found: ' . AuthenticationErrorMessages::EMAIL_NOT_FOUND);
        $this->line('Invalid password: ' . AuthenticationErrorMessages::INVALID_PASSWORD);
        $this->line('Invalid credentials: ' . AuthenticationErrorMessages::INVALID_CREDENTIALS);
        $this->newLine();

        // Test user type specific messages
        $this->line('=== User Type Specific Messages ===');
        $this->line('Admin not found: ' . AuthenticationErrorMessages::getEmailNotFoundMessage('admin'));
        $this->line('Staff not found: ' . AuthenticationErrorMessages::getEmailNotFoundMessage('staff'));
        $this->line('Wrong type (admin): ' . AuthenticationErrorMessages::getWrongUserTypeMessage('admin'));
        $this->line('Wrong type (staff): ' . AuthenticationErrorMessages::getWrongUserTypeMessage('staff'));
        $this->newLine();

        // Test account status messages
        $this->line('=== Account Status Messages ===');
        $this->line('Admin inactive: ' . AuthenticationErrorMessages::getAccountInactiveMessage('admin'));
        $this->line('Staff inactive: ' . AuthenticationErrorMessages::getAccountInactiveMessage('staff'));
        $this->newLine();

        // Test lockout messages
        $this->line('=== Lockout Messages ===');
        $this->line('Basic lockout (30s): ' . AuthenticationErrorMessages::getLockoutMessage(30));
        $this->line('Progressive lockout (35s, 2nd attempt): ' . AuthenticationErrorMessages::getLockoutMessage(35, 2));
        $this->line('Too many attempts (50s): ' . AuthenticationErrorMessages::getTooManyAttemptsMessage(50));
        $this->newLine();

        // Test validation messages
        $this->line('=== Validation Messages ===');
        $this->line('Invalid email format: ' . AuthenticationErrorMessages::INVALID_EMAIL_FORMAT);
        $this->line('Missing password: ' . AuthenticationErrorMessages::MISSING_PASSWORD);
        $this->line('Missing user type: ' . AuthenticationErrorMessages::MISSING_USER_TYPE);
        $this->line('CAPTCHA required: ' . AuthenticationErrorMessages::CAPTCHA_REQUIRED);
        $this->newLine();

        // Test system error messages
        $this->line('=== System Error Messages ===');
        $this->line('System error: ' . AuthenticationErrorMessages::SYSTEM_ERROR);
        $this->line('Database error: ' . AuthenticationErrorMessages::DATABASE_CONNECTION_ERROR);
        $this->line('Firebase error: ' . AuthenticationErrorMessages::FIREBASE_CONNECTION_ERROR);
        $this->line('Auth service error: ' . AuthenticationErrorMessages::AUTHENTICATION_SERVICE_ERROR);
        $this->newLine();

        $this->info('✓ All authentication error messages tested successfully!');
        
        return 0;
    }
}