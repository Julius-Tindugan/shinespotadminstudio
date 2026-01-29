<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Console\Commands\UpdateRevenueStatistics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class RevenueStatisticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding revenue statistics from historical payment data...');

        // Get the earliest payment date
        $earliestPayment = Payment::orderBy('payment_date', 'asc')->first();
        
        if (!$earliestPayment) {
            $this->command->info('No payment data found. Skipping revenue statistics seeding.');
            return;
        }

        $startDate = Carbon::parse($earliestPayment->payment_date)->startOfDay();
        $endDate = Carbon::today();
        
        $this->command->info("Generating statistics from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");
        
        // Process each day
        $currentDate = $startDate->copy();
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $bar = $this->command->getOutput()->createProgressBar($totalDays);
        
        $bar->start();
        
        while ($currentDate <= $endDate) {
            // Run the update revenue statistics command for this date
            Artisan::call('revenue:update', [
                'date' => $currentDate->format('Y-m-d')
            ]);
            
            $currentDate->addDay();
            $bar->advance();
        }
        
        $bar->finish();
        $this->command->info("\nRevenue statistics seeding completed!");
    }
}
