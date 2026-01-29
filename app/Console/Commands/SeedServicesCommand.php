<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedServicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the services table with predefined services';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding services table...');
        
        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\ServiceSeeder'
        ]);
        
        $this->info('Services seeded successfully!');
    }
}
