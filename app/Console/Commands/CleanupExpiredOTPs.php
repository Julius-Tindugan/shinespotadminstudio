<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailOTPService;

class CleanupExpiredOTPs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'otp:cleanup
                          {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and old verified OTP records';

    /**
     * The OTP service instance.
     *
     * @var EmailOTPService
     */
    protected $otpService;

    /**
     * Create a new command instance.
     *
     * @param EmailOTPService $otpService
     */
    public function __construct(EmailOTPService $otpService)
    {
        parent::__construct();
        $this->otpService = $otpService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting OTP cleanup process...');
        
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete expired and old verified OTP records. Continue?')) {
                $this->warn('Cleanup cancelled.');
                return Command::FAILURE;
            }
        }

        $this->info('Cleaning up expired and old OTP records...');
        
        try {
            $deletedCount = $this->otpService->cleanupOldOTPs();
            
            if ($deletedCount > 0) {
                $this->info("✓ Successfully cleaned up {$deletedCount} OTP records.");
                $this->line('');
                $this->line('Records removed:');
                $this->line('  - Expired unverified OTPs');
                $this->line('  - Verified OTPs older than 7 days');
            } else {
                $this->info('✓ No OTP records to clean up.');
            }
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('✗ Failed to cleanup OTP records');
            $this->error('Error: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
