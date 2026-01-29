<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SetupKpiTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpi:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup tables needed for KPI statistics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up KPI related tables...');
        
        try {
            // Check if we need to run the migration
            if (Schema::hasTable('bookings')) {
                $this->warn('KPI tables already exist. Skipping setup.');
                return 0;
            }
            
            // Run the migration
            $this->call('migrate', ['--path' => 'database/migrations/2025_08_28_000004_create_kpi_related_tables.php']);
            
            $this->info('KPI tables setup complete!');
            $this->info('You can now view KPI statistics on your dashboard.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error setting up KPI tables: ' . $e->getMessage());
            return 1;
        }
    }
}
