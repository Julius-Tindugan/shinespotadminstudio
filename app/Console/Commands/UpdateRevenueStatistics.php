<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\RevenueStatistic;
use Carbon\Carbon;

class UpdateRevenueStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'revenue:update {date? : The date to update (YYYY-MM-DD format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update daily revenue statistics for the specified date or today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get date parameter or use today
        $dateInput = $this->argument('date');
        $date = $dateInput ? Carbon::parse($dateInput) : Carbon::today();
        
        $this->info("Updating revenue statistics for {$date->format('Y-m-d')}");
        
        // Get daily revenue
        $dailyRevenue = Payment::whereDate('payment_date', $date)
            ->where('status', 'completed')
            ->sum('amount');
            
        // Get weekly revenue
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();
        $weeklyRevenue = Payment::whereBetween('payment_date', [$weekStart, $weekEnd])
            ->where('status', 'completed')
            ->sum('amount');
            
        // Get monthly revenue
        $monthStart = $date->copy()->startOfMonth();
        $monthEnd = $date->copy()->endOfMonth();
        $monthlyRevenue = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->sum('amount');
            
        // Get yearly revenue
        $yearStart = $date->copy()->startOfYear();
        $yearEnd = $date->copy()->endOfYear();
        $yearlyRevenue = Payment::whereBetween('payment_date', [$yearStart, $yearEnd])
            ->where('status', 'completed')
            ->sum('amount');
            
        // Get transaction count
        $transactionCount = Payment::whereDate('payment_date', $date)
            ->where('status', 'completed')
            ->count();
        
        // Update or create statistics record
        $statistics = RevenueStatistic::updateOrCreate(
            ['date' => $date->format('Y-m-d')],
            [
                'daily_revenue' => $dailyRevenue,
                'weekly_revenue' => $weeklyRevenue,
                'monthly_revenue' => $monthlyRevenue,
                'yearly_revenue' => $yearlyRevenue,
                'year' => $date->year,
                'month' => $date->month,
                'week' => $date->week,
                'day' => $date->day,
                'transaction_count' => $transactionCount,
            ]
        );
        
        $this->info("Statistics updated successfully:");
        $this->table(
            ['Date', 'Daily Revenue', 'Weekly Revenue', 'Monthly Revenue', 'Yearly Revenue', 'Transactions'],
            [[
                $date->format('Y-m-d'),
                '₱' . number_format($dailyRevenue, 2),
                '₱' . number_format($weeklyRevenue, 2),
                '₱' . number_format($monthlyRevenue, 2),
                '₱' . number_format($yearlyRevenue, 2),
                $transactionCount
            ]]
        );
    }
}
