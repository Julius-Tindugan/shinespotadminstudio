<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RevenueStatistic;
use App\Models\Staff;
use App\Models\Package;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KpiService
{
    // Cache TTL in minutes - Reduced for real-time updates
    const CACHE_TTL = 5;

    /**
     * Get all KPI statistics for dashboard
     * 
     * @return array
     */
    public function getDashboardKpis()
    {
        return Cache::remember('dashboard_kpis', self::CACHE_TTL, function () {
            try {
                return [
                    'revenue' => $this->getRevenueStatistics(),
                    'profit' => $this->getProfitStatistics(),
                    'bookings' => $this->getBookingStatistics(),
                    'upcomingBookings' => $this->getUpcomingBookings(),
                    'dailyRevenue' => $this->getDailyRevenue(),
                    'staffPerformance' => $this->getStaffPerformance(),
                    'packageAnalytics' => $this->getPackageAnalytics()
                ];
            } catch (\Exception $e) {
                Log::error('Error in getDashboardKpis: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving KPI data',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Get revenue statistics with enhanced metrics
     * 
     * @return array
     */
    public function getRevenueStatistics()
    {
        return Cache::remember('revenue_statistics', self::CACHE_TTL, function () {
            try {
                // Current month queries
                $currentMonthQuery = Booking::whereYear('booking_date', now()->year)
                    ->whereMonth('booking_date', now()->month);
                
                $currentMonthRevenue = $this->calculateNetRevenue($currentMonthQuery);
                
                // Track refund details for current month
                // Get paid amount from payment_transactions table (based on payment_date)
                $currentMonthPaidFromTransactions = DB::table('payment_transactions')
                    ->whereYear('payment_date', now()->year)
                    ->whereMonth('payment_date', now()->month)
                    ->where(function($q) {
                        $q->where(function($subQ) {
                            $subQ->where('payment_method', 'gcash')
                                 ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'COMPLETED', 'SETTLED']);
                        })
                        ->orWhere(function($subQ) {
                            // Onsite payments are always successful
                            $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card']);
                        });
                    })
                    ->sum('amount');
                
                // Get paid amount from bookings table (based on when payment_status was updated)
                $currentMonthPaidFromBookings = Booking::whereYear('updated_at', now()->year)
                    ->whereMonth('updated_at', now()->month)
                    ->where('payment_status', 'paid')
                    ->sum('total_amount');
                
                // Use the higher value to represent actual gross revenue
                $currentMonthPaid = max($currentMonthPaidFromBookings, $currentMonthPaidFromTransactions);
                
                $currentMonthRefunded = Booking::whereYear('updated_at', now()->year)
                    ->whereMonth('updated_at', now()->month)
                    ->where('payment_status', 'refunded')
                    ->sum('total_amount');
                
                $currentMonthRefundCount = Booking::whereYear('updated_at', now()->year)
                    ->whereMonth('updated_at', now()->month)
                    ->where('payment_status', 'refunded')
                    ->count();
                
                // Previous month total revenue from paid bookings minus refunds
                $previousMonthRevenue = $this->calculateNetRevenue(
                    Booking::whereYear('booking_date', now()->subMonth()->year)
                        ->whereMonth('booking_date', now()->subMonth()->month)
                );
                
                // Year-to-date revenue from paid bookings minus refunds
                $ytdQuery = Booking::whereYear('booking_date', now()->year);
                $ytdRevenue = $this->calculateNetRevenue($ytdQuery);
                
                $ytdRefunded = Booking::whereYear('booking_date', now()->year)
                    ->where('payment_status', 'refunded')
                    ->sum('total_amount');
                
                $ytdRefundCount = Booking::whereYear('booking_date', now()->year)
                    ->where('payment_status', 'refunded')
                    ->count();
                
                // Previous year same period from paid bookings minus refunds
                $previousYearSamePeriod = $this->calculateNetRevenue(
                    Booking::whereYear('booking_date', now()->subYear()->year)
                        ->whereMonth('booking_date', '<=', now()->month)
                );
                
                // Last 7 days revenue from paid bookings minus refunds
                $last7DaysRevenue = $this->calculateNetRevenue(
                    Booking::where('booking_date', '>=', now()->subDays(7))
                );
                
                // Last 30 days revenue from paid bookings minus refunds
                $last30DaysRevenue = $this->calculateNetRevenue(
                    Booking::where('booking_date', '>=', now()->subDays(30))
                );
                
                // Calculate percentage changes
                $monthlyChange = $this->calculatePercentageChange($currentMonthRevenue, $previousMonthRevenue);
                $yearlyChange = $this->calculatePercentageChange($ytdRevenue, $previousYearSamePeriod);
                
                // Calculate average daily revenue for current and previous month
                $daysInCurrentMonth = now()->daysInMonth;
                $daysPassedInMonth = min(now()->day, $daysInCurrentMonth);
                $avgDailyRevenue = $daysPassedInMonth > 0 ? $currentMonthRevenue / $daysPassedInMonth : 0;
                
                $daysInPreviousMonth = now()->subMonth()->daysInMonth;
                $avgDailyPreviousMonth = $daysInPreviousMonth > 0 ? $previousMonthRevenue / $daysInPreviousMonth : 0;
                
                // Update or create revenue statistics record for better querying
                $this->updateRevenueStatistics(now(), $currentMonthRevenue);
                
                // Forecast for current month based on daily average
                $forecastedMonthlyRevenue = $avgDailyRevenue * $daysInCurrentMonth;
                
                // Revenue sources breakdown
                $revenueSources = $this->getRevenueSourcesBreakdown();
                
                // Format revenue values
                $formattedCurrentMonthRevenue = number_format($currentMonthRevenue, 2);
                $formattedYtdRevenue = number_format($ytdRevenue, 2);
                
                // Return data with enhanced metrics including refund tracking
                return [
                    'current' => $currentMonthRevenue,
                    'previous' => $previousMonthRevenue,
                    'ytd' => $ytdRevenue,
                    'previousYearSamePeriod' => $previousYearSamePeriod,
                    'last7Days' => $last7DaysRevenue,
                    'last30Days' => $last30DaysRevenue,
                    'formattedCurrent' => $formattedCurrentMonthRevenue,
                    'formattedYtd' => $formattedYtdRevenue,
                    'monthlyChange' => $monthlyChange,
                    'yearlyChange' => $yearlyChange,
                    'trending' => $monthlyChange >= 0 ? 'up' : 'down',
                    'yearlyTrending' => $yearlyChange >= 0 ? 'up' : 'down',
                    'avgDailyRevenue' => $avgDailyRevenue,
                    'avgDailyPreviousMonth' => $avgDailyPreviousMonth,
                    'forecastedMonthlyRevenue' => $forecastedMonthlyRevenue,
                    'sources' => $revenueSources,
                    // Refund tracking for transparency
                    'refunds' => [
                        'currentMonth' => [
                            'amount' => $currentMonthRefunded,
                            'count' => $currentMonthRefundCount,
                            'formatted' => number_format($currentMonthRefunded, 2)
                        ],
                        'yearToDate' => [
                            'amount' => $ytdRefunded,
                            'count' => $ytdRefundCount,
                            'formatted' => number_format($ytdRefunded, 2)
                        ],
                        'grossRevenue' => $currentMonthPaid,
                        'netRevenue' => $currentMonthRevenue
                    ]
                ];
            } catch (\Exception $e) {
                Log::error('Error in getRevenueStatistics: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving revenue statistics',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Calculate net revenue (paid bookings minus refunded bookings)
     * Always returns a positive value or zero, never negative
     * Uses PAYMENT DATE (when money was received) instead of booking date
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return float
     */
    private function calculateNetRevenue($query)
    {
        // Get the raw SQL and bindings to understand the date filters
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        
        // Revenue should be counted when payment is received, not when booking is scheduled
        // We'll use payment_date from payment_transactions table
        $transactionRevenue = 0;
        
        // Check if query has year/month filters (for current month revenue)
        if (strpos($sql, 'year') !== false && strpos($sql, 'month') !== false) {
            // Extract year and month from bindings (they're usually the last two)
            $year = count($bindings) >= 2 ? $bindings[count($bindings) - 2] : now()->year;
            $month = count($bindings) >= 1 ? $bindings[count($bindings) - 1] : now()->month;
            
            // Get revenue from payment_transactions based on when payment was made
            $transactionRevenue = DB::table('payment_transactions')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->where(function($q) {
                    $q->where(function($subQ) {
                        // GCash payments that succeeded
                        $subQ->where('payment_method', 'gcash')
                             ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'COMPLETED', 'SETTLED']);
                    })
                    ->orWhere(function($subQ) {
                        // Onsite payments are always successful (PAID status or NULL for older records)
                        $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card']);
                    });
                })
                ->sum('amount');
                
            // Also get revenue from bookings marked as paid in this period
            // (for cases where payment_transaction doesn't exist)
            $bookingRevenue = Booking::whereYear('updated_at', $year)
                ->whereMonth('updated_at', $month)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $transactionRevenue = max($transactionRevenue, $bookingRevenue);
            
        } elseif (strpos($sql, 'year') !== false) {
            // Year-only filter (YTD)
            $year = count($bindings) >= 1 ? $bindings[count($bindings) - 1] : now()->year;
            
            $transactionRevenue = DB::table('payment_transactions')
                ->whereYear('payment_date', $year)
                ->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('payment_method', 'gcash')
                             ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'COMPLETED', 'SETTLED']);
                    })
                    ->orWhere(function($subQ) {
                        // Onsite payments are always successful
                        $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card']);
                    });
                })
                ->sum('amount');
                
            $bookingRevenue = Booking::whereYear('updated_at', $year)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $transactionRevenue = max($transactionRevenue, $bookingRevenue);
            
        } elseif (strpos($sql, 'booking_date') !== false && strpos($sql, '>=') !== false) {
            // Date range filter (last 7 days, last 30 days, etc.)
            $dateValue = count($bindings) >= 1 ? $bindings[0] : now()->subDays(7)->format('Y-m-d');
            
            $transactionRevenue = DB::table('payment_transactions')
                ->where('payment_date', '>=', $dateValue)
                ->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('payment_method', 'gcash')
                             ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'COMPLETED', 'SETTLED']);
                    })
                    ->orWhere(function($subQ) {
                        // Onsite payments are always successful
                        $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card']);
                    });
                })
                ->sum('amount');
                
            $bookingRevenue = Booking::where('updated_at', '>=', $dateValue)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $transactionRevenue = max($transactionRevenue, $bookingRevenue);
        }
        
        // Get refunded bookings for the same period
        $refundedAmount = 0;
        if (strpos($sql, 'year') !== false && strpos($sql, 'month') !== false) {
            $year = count($bindings) >= 2 ? $bindings[count($bindings) - 2] : now()->year;
            $month = count($bindings) >= 1 ? $bindings[count($bindings) - 1] : now()->month;
            $refundedAmount = Booking::whereYear('updated_at', $year)
                ->whereMonth('updated_at', $month)
                ->where('payment_status', 'refunded')
                ->sum('total_amount');
        }
        
        // Net revenue = paid - refunded, but never negative
        $netRevenue = $transactionRevenue - $refundedAmount;
        
        return max(0, $netRevenue); // Ensures never negative
    }
    
    /**
     * Calculate percentage change between two values
     * 
     * @param float $current
     * @param float $previous
     * @return float
     */
    private function calculatePercentageChange($current, $previous)
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 2);
        }
        return $current > 0 ? 100 : 0;
    }
    
    /**
     * Update or create revenue statistics record
     * 
     * @param \Carbon\Carbon $date
     * @param float $dailyRevenue
     * @return void
     */
    private function updateRevenueStatistics($date, $dailyRevenue)
    {
        try {
            $day = $date->day;
            $week = $date->weekOfYear;
            $month = $date->month;
            $year = $date->year;
            
            // Calculate weekly revenue (paid minus refunded)
            $weeklyRevenue = $this->calculateNetRevenue(
                Booking::whereYear('booking_date', $year)
                    ->whereRaw('WEEK(booking_date) = ?', [$week])
            );
                
            // Calculate monthly revenue (paid minus refunded)
            $monthlyRevenue = $this->calculateNetRevenue(
                Booking::whereYear('booking_date', $year)
                    ->whereMonth('booking_date', $month)
            );
                
            // Calculate yearly revenue (paid minus refunded)
            $yearlyRevenue = $this->calculateNetRevenue(
                Booking::whereYear('booking_date', $year)
            );
                
            $transactionCount = Booking::whereDate('booking_date', $date->toDateString())
                ->where('payment_status', 'paid')
                ->count();
                
            // Update or create revenue statistic record
            RevenueStatistic::updateOrCreate(
                ['date' => $date->toDateString()],
                [
                    'daily_revenue' => $dailyRevenue,
                    'weekly_revenue' => $weeklyRevenue,
                    'monthly_revenue' => $monthlyRevenue,
                    'yearly_revenue' => $yearlyRevenue,
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
                    'day' => $day,
                    'transaction_count' => $transactionCount
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error in updateRevenueStatistics: ' . $e->getMessage());
        }
    }
    
    /**
     * Get revenue sources breakdown
     * 
     * @return array
     */
    private function getRevenueSourcesBreakdown()
    {
        // Get current month revenue by package from paid bookings
        $paidBookings = DB::table('bookings')
            ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
            ->whereMonth('bookings.booking_date', now()->month)
            ->whereYear('bookings.booking_date', now()->year)
            ->where('bookings.payment_status', 'paid')
            ->select('packages.title as package_name', DB::raw('SUM(bookings.total_amount) as total'))
            ->groupBy('packages.package_id', 'packages.title')
            ->get();
        
        // Get current month refunded amounts by package
        $refundedBookings = DB::table('bookings')
            ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
            ->whereMonth('bookings.booking_date', now()->month)
            ->whereYear('bookings.booking_date', now()->year)
            ->where('bookings.payment_status', 'refunded')
            ->select('packages.title as package_name', DB::raw('SUM(bookings.total_amount) as total'))
            ->groupBy('packages.package_id', 'packages.title')
            ->get()
            ->keyBy('package_name');
        
        // Calculate net revenue per package (paid - refunded)
        $netRevenueByPackage = $paidBookings->map(function($item) use ($refundedBookings) {
            $refundedAmount = $refundedBookings->get($item->package_name)->total ?? 0;
            $item->total = $item->total - $refundedAmount;
            return $item;
        })->sortByDesc('total')->values();
            
        return [
            'packages' => $netRevenueByPackage
        ];
    }
    
    /**
     * Get profit statistics by calculating revenue minus expenses
     * 
     * @return array
     */
    public function getProfitStatistics()
    {
        return Cache::remember('profit_statistics', self::CACHE_TTL, function () {
            try {
                // Get revenue data from paid bookings minus refunds
                $currentMonthRevenue = $this->calculateNetRevenue(
                    Booking::whereYear('booking_date', now()->year)
                        ->whereMonth('booking_date', now()->month)
                );
                
                $previousMonthRevenue = $this->calculateNetRevenue(
                    Booking::whereYear('booking_date', now()->subMonth()->year)
                        ->whereMonth('booking_date', now()->subMonth()->month)
                );
                
                $ytdRevenue = $this->calculateNetRevenue(
                    Booking::whereYear('booking_date', now()->year)
                );
                
                // Get expense data - if Expense model exists and has data
                $currentMonthExpense = 0;
                $previousMonthExpense = 0;
                $ytdExpense = 0;
                
                try {
                    if (class_exists('App\\Models\\Expense')) {
                        $currentMonthExpense = DB::table('expenses')
                            ->whereMonth('expense_date', now()->month)
                            ->whereYear('expense_date', now()->year)
                            ->sum('amount');
                            
                        $previousMonthExpense = DB::table('expenses')
                            ->whereMonth('expense_date', now()->subMonth()->month)
                            ->whereYear('expense_date', now()->subMonth()->year)
                            ->sum('amount');
                            
                        $ytdExpense = DB::table('expenses')
                            ->whereYear('expense_date', now()->year)
                            ->sum('amount');
                    }
                } catch (\Exception $e) {
                    Log::warning('Expenses table may not exist: ' . $e->getMessage());
                }
                
                // Calculate profits
                $currentMonthProfit = $currentMonthRevenue - $currentMonthExpense;
                $previousMonthProfit = $previousMonthRevenue - $previousMonthExpense;
                $ytdProfit = $ytdRevenue - $ytdExpense;
                
                // Calculate profit margin
                $currentMonthMargin = $currentMonthRevenue > 0 ? 
                    round(($currentMonthProfit / $currentMonthRevenue) * 100, 2) : 0;
                $previousMonthMargin = $previousMonthRevenue > 0 ? 
                    round(($previousMonthProfit / $previousMonthRevenue) * 100, 2) : 0;
                $ytdMargin = $ytdRevenue > 0 ? 
                    round(($ytdProfit / $ytdRevenue) * 100, 2) : 0;
                
                // Calculate percentage change
                $profitChange = $this->calculatePercentageChange($currentMonthProfit, $previousMonthProfit);
                $marginChange = $previousMonthMargin > 0 ? 
                    $currentMonthMargin - $previousMonthMargin : 0;
                
                return [
                    'currentProfit' => $currentMonthProfit,
                    'previousProfit' => $previousMonthProfit,
                    'ytdProfit' => $ytdProfit,
                    'formattedCurrentProfit' => number_format($currentMonthProfit, 2),
                    'formattedYtdProfit' => number_format($ytdProfit, 2),
                    'profitChange' => $profitChange,
                    'trending' => $profitChange >= 0 ? 'up' : 'down',
                    'currentMargin' => $currentMonthMargin,
                    'previousMargin' => $previousMonthMargin,
                    'ytdMargin' => $ytdMargin,
                    'marginChange' => $marginChange,
                    'marginTrending' => $marginChange >= 0 ? 'up' : 'down',
                    'expenses' => [
                        'current' => $currentMonthExpense,
                        'previous' => $previousMonthExpense,
                        'ytd' => $ytdExpense
                    ]
                ];
            } catch (\Exception $e) {
                Log::error('Error in getProfitStatistics: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving profit statistics',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Get enhanced booking statistics
     * Uses booking completion/update dates for completed bookings,
     * and booking_date for upcoming/pending bookings
     * 
     * @return array
     */
    public function getBookingStatistics()
    {
        return Cache::remember('booking_statistics', self::CACHE_TTL, function () {
            try {
                // Current month bookings - count ALL bookings that were:
                // 1. Completed this month (use updated_at)
                // 2. Created this month but not yet completed (use created_at or booking_date)
                $currentMonthCompleted = Booking::where('status', 'completed')
                    ->whereYear('updated_at', now()->year)
                    ->whereMonth('updated_at', now()->month)
                    ->count();
                    
                $currentMonthCanceled = Booking::whereIn('status', ['cancelled', 'no_show'])
                    ->whereYear('updated_at', now()->year)
                    ->whereMonth('updated_at', now()->month)
                    ->count();
                
                // Pending/confirmed bookings scheduled for this month
                $currentMonthPending = Booking::whereIn('status', ['pending', 'confirmed'])
                    ->whereYear('booking_date', now()->year)
                    ->whereMonth('booking_date', now()->month)
                    ->count();
                
                // Total current month bookings
                $currentMonthBookings = $currentMonthCompleted + $currentMonthCanceled + $currentMonthPending;
                
                // Previous month bookings
                $previousMonthCompleted = Booking::where('status', 'completed')
                    ->whereYear('updated_at', now()->subMonth()->year)
                    ->whereMonth('updated_at', now()->subMonth()->month)
                    ->count();
                    
                $previousMonthCanceled = Booking::whereIn('status', ['cancelled', 'no_show'])
                    ->whereYear('updated_at', now()->subMonth()->year)
                    ->whereMonth('updated_at', now()->subMonth()->month)
                    ->count();
                    
                $previousMonthPending = Booking::whereIn('status', ['pending', 'confirmed'])
                    ->whereYear('booking_date', now()->subMonth()->year)
                    ->whereMonth('booking_date', now()->subMonth()->month)
                    ->where('booking_date', '<', now()->startOfMonth()) // Only count past pending from prev month
                    ->count();
                
                $previousMonthBookings = $previousMonthCompleted + $previousMonthCanceled + $previousMonthPending;
                
                // Weekly bookings - bookings that are scheduled for this week OR completed this week
                $startOfWeek = now()->startOfWeek();
                $endOfWeek = now()->endOfWeek();
                
                $currentWeekCompleted = Booking::where('status', 'completed')
                    ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                    ->count();
                    
                $currentWeekScheduled = Booking::whereIn('status', ['pending', 'confirmed'])
                    ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
                    ->count();
                    
                $currentWeekBookings = $currentWeekCompleted + $currentWeekScheduled;
                
                $previousWeekCompleted = Booking::where('status', 'completed')
                    ->whereBetween('updated_at', [
                        now()->subWeek()->startOfWeek(),
                        now()->subWeek()->endOfWeek()
                    ])
                    ->count();
                    
                $previousWeekScheduled = Booking::whereIn('status', ['pending', 'confirmed'])
                    ->whereBetween('booking_date', [
                        now()->subWeek()->startOfWeek(),
                        now()->subWeek()->endOfWeek()
                    ])
                    ->count();
                    
                $previousWeekBookings = $previousWeekCompleted + $previousWeekScheduled;
                
                // Calculate completion rates
                $currentCompletionRate = $currentMonthBookings > 0 ? 
                    round(($currentMonthCompleted / $currentMonthBookings) * 100, 2) : 0;
                $previousCompletionRate = $previousMonthBookings > 0 ? 
                    round(($previousMonthCompleted / $previousMonthBookings) * 100, 2) : 0;
                
                // Calculate cancellation rates
                $currentCancellationRate = $currentMonthBookings > 0 ? 
                    round(($currentMonthCanceled / $currentMonthBookings) * 100, 2) : 0;
                $previousCancellationRate = $previousMonthBookings > 0 ? 
                    round(($previousMonthCanceled / $previousMonthBookings) * 100, 2) : 0;
                
                // Calculate percentage changes
                $monthlyChange = $this->calculatePercentageChange($currentMonthBookings, $previousMonthBookings);
                $weeklyChange = $this->calculatePercentageChange($currentWeekBookings, $previousWeekBookings);
                $completionRateChange = $currentCompletionRate - $previousCompletionRate;
                $cancellationRateChange = $currentCancellationRate - $previousCancellationRate;
                
                // Average booking value - calculate from completed bookings
                $currentMonthBookingValue = $currentMonthCompleted > 0 ?
                    Booking::where('status', 'completed')
                        ->whereYear('updated_at', now()->year)
                        ->whereMonth('updated_at', now()->month)
                        ->sum('total_amount') / $currentMonthCompleted : 0;
                        
                $previousMonthBookingValue = $previousMonthCompleted > 0 ?
                    Booking::where('status', 'completed')
                        ->whereYear('updated_at', now()->subMonth()->year)
                        ->whereMonth('updated_at', now()->subMonth()->month)
                        ->sum('total_amount') / $previousMonthCompleted : 0;
                        
                $bookingValueChange = $this->calculatePercentageChange($currentMonthBookingValue, $previousMonthBookingValue);
                
                // Return enhanced data
                return [
                    'currentMonth' => $currentMonthBookings,
                    'previousMonth' => $previousMonthBookings,
                    'currentWeek' => $currentWeekBookings,
                    'previousWeek' => $previousWeekBookings,
                    'completed' => [
                        'current' => $currentMonthCompleted,
                        'previous' => $previousMonthCompleted,
                    ],
                    'canceled' => [
                        'current' => $currentMonthCanceled,
                        'previous' => $previousMonthCanceled,
                    ],
                    'monthlyChange' => $monthlyChange,
                    'weeklyChange' => $weeklyChange,
                    'monthlyTrending' => $monthlyChange >= 0 ? 'up' : 'down',
                    'weeklyTrending' => $weeklyChange >= 0 ? 'up' : 'down',
                    'completionRate' => [
                        'current' => $currentCompletionRate,
                        'previous' => $previousCompletionRate,
                        'change' => $completionRateChange,
                        'trending' => $completionRateChange >= 0 ? 'up' : 'down'
                    ],
                    'cancellationRate' => [
                        'current' => $currentCancellationRate,
                        'previous' => $previousCancellationRate,
                        'change' => $cancellationRateChange,
                        'trending' => $cancellationRateChange <= 0 ? 'up' : 'down' // Lower cancellation is better
                    ],
                    'averageValue' => [
                        'current' => $currentMonthBookingValue,
                        'previous' => $previousMonthBookingValue,
                        'formattedCurrent' => number_format($currentMonthBookingValue, 2),
                        'change' => $bookingValueChange,
                        'trending' => $bookingValueChange >= 0 ? 'up' : 'down'
                    ]
                ];
            } catch (\Exception $e) {
                Log::error('Error in getBookingStatistics: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving booking statistics',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    

    

    
    /**
     * Get staff performance metrics
     * 
     * @return array
     */
    public function getStaffPerformance()
    {
        return Cache::remember('staff_performance', self::CACHE_TTL, function () {
            try {
                // Staff with most bookings this month
                $staffWithMostBookings = DB::table('bookings')
                    ->join('staff_users', 'bookings.primary_staff_id', '=', 'staff_users.staff_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->select(
                        'staff_users.staff_id',
                        DB::raw("CONCAT(staff_users.first_name, ' ', staff_users.last_name) as staff_name"),
                        DB::raw('COUNT(bookings.booking_id) as booking_count')
                    )
                    ->groupBy('staff_users.staff_id', 'staff_users.first_name', 'staff_users.last_name')
                    ->orderByDesc('booking_count')
                    ->limit(10)
                    ->get();
                
                // Staff generating most revenue this month from paid bookings
                $staffByRevenue = DB::table('bookings')
                    ->join('staff_users', 'bookings.primary_staff_id', '=', 'staff_users.staff_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->where('bookings.payment_status', 'paid')
                    ->select(
                        'staff_users.staff_id',
                        DB::raw("CONCAT(staff_users.first_name, ' ', staff_users.last_name) as staff_name"),
                        DB::raw('SUM(bookings.total_amount) as total_revenue'),
                        DB::raw('COUNT(DISTINCT bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) / COUNT(DISTINCT bookings.booking_id) as avg_booking_value')
                    )
                    ->groupBy('staff_users.staff_id', 'staff_users.first_name', 'staff_users.last_name')
                    ->orderByDesc('total_revenue')
                    ->limit(10)
                    ->get();
                
                // Staff with highest average booking value from paid bookings
                $staffByAvgValue = DB::table('bookings')
                    ->join('staff_users', 'bookings.primary_staff_id', '=', 'staff_users.staff_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->where('bookings.payment_status', 'paid')
                    ->select(
                        'staff_users.staff_id',
                        DB::raw("CONCAT(staff_users.first_name, ' ', staff_users.last_name) as staff_name"),
                        DB::raw('SUM(bookings.total_amount) as total_revenue'),
                        DB::raw('COUNT(DISTINCT bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) / COUNT(DISTINCT bookings.booking_id) as avg_booking_value')
                    )
                    ->groupBy('staff_users.staff_id', 'staff_users.first_name', 'staff_users.last_name')
                    ->having('booking_count', '>=', 3) // Minimum 3 bookings to be considered
                    ->orderByDesc('avg_booking_value')
                    ->limit(10)
                    ->get();
                
                return [
                    'byBookingCount' => $staffWithMostBookings,
                    'byRevenue' => $staffByRevenue,
                    'byAverageValue' => $staffByAvgValue
                ];
            } catch (\Exception $e) {
                Log::error('Error in getStaffPerformance: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving staff performance metrics',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Get upcoming bookings for the timeline
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingBookings($limit = 5)
    {
        try {
            return Booking::with(['primaryStaff', 'package'])
                ->upcoming()
                ->take($limit)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->booking_id,
                        'client' => $booking->client_first_name . ' ' . $booking->client_last_name,
                        'date' => $booking->booking_date->format('M d, Y'),
                        'time' => Carbon::parse($booking->start_time)->format('h:i A') . ' - ' . 
                                  Carbon::parse($booking->end_time)->format('h:i A'),
                        'status' => $booking->status,
                        'package' => $booking->package ? $booking->package->title : 'No package',
                        'amount' => $booking->total_amount,
                        'primary_staff' => $booking->primaryStaff ? $booking->primaryStaff->first_name . ' ' . $booking->primaryStaff->last_name : null
                    ];
                });
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in getUpcomingBookings: ' . $e->getMessage());
            // Return empty collection
            return collect([]);
        }
    }
    
    /**
     * Get package analytics
     * 
     * @return array
     */
    public function getPackageAnalytics()
    {
        return Cache::remember('package_analytics', self::CACHE_TTL, function () {
            try {
                // Most popular packages by booking count
                $popularPackages = DB::table('bookings')
                    ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->select(
                        'packages.package_id',
                        'packages.title',
                        'packages.price',
                        DB::raw('COUNT(bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) as total_revenue')
                    )
                    ->groupBy('packages.package_id', 'packages.title', 'packages.price')
                    ->orderByDesc('booking_count')
                    ->limit(10)
                    ->get();
                
                // Most profitable packages
                $profitablePackages = DB::table('bookings')
                    ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->select(
                        'packages.package_id',
                        'packages.title',
                        'packages.price',
                        DB::raw('COUNT(bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) as total_revenue'),
                        DB::raw('SUM(bookings.total_amount) / COUNT(bookings.booking_id) as avg_revenue')
                    )
                    ->groupBy('packages.package_id', 'packages.title', 'packages.price')
                    ->orderByDesc('total_revenue')
                    ->limit(10)
                    ->get();
                
                // Package distribution by title
                $packageTypeDistribution = DB::table('bookings')
                    ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
                    ->whereNotNull('bookings.package_id')
                    ->whereMonth('bookings.booking_date', now()->month)
                    ->whereYear('bookings.booking_date', now()->year)
                    ->select(
                        'packages.title',
                        DB::raw('COUNT(bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) as total_revenue')
                    )
                    ->groupBy('packages.title')
                    ->orderByDesc('booking_count')
                    ->get();
                
                // Year to date package performance
                $ytdPackagePerformance = DB::table('bookings')
                    ->join('packages', 'bookings.package_id', '=', 'packages.package_id')
                    ->whereNotNull('bookings.package_id')
                    ->whereYear('bookings.booking_date', now()->year)
                    ->select(
                        'packages.package_id',
                        'packages.title',
                        DB::raw('COUNT(bookings.booking_id) as booking_count'),
                        DB::raw('SUM(bookings.total_amount) as total_revenue')
                    )
                    ->groupBy('packages.package_id', 'packages.title')
                    ->orderByDesc('total_revenue')
                    ->limit(10)
                    ->get();
                
                // Monthly package booking trends
                $monthlyTrends = [];
                
                for ($i = 5; $i >= 0; $i--) {
                    $month = now()->subMonths($i);
                    
                    $packageCount = DB::table('bookings')
                        ->whereNotNull('package_id')
                        ->whereMonth('booking_date', $month->month)
                        ->whereYear('booking_date', $month->year)
                        ->count();
                    
                    $packageRevenue = DB::table('bookings')
                        ->whereNotNull('package_id')
                        ->whereMonth('booking_date', $month->month)
                        ->whereYear('booking_date', $month->year)
                        ->sum('total_amount');
                    
                    $monthlyTrends[] = [
                        'month' => $month->format('M Y'),
                        'booking_count' => $packageCount,
                        'revenue' => $packageRevenue
                    ];
                }
                
                return [
                    'mostPopular' => $popularPackages,
                    'mostProfitable' => $profitablePackages,
                    'typeDistribution' => $packageTypeDistribution,
                    'ytdPerformance' => $ytdPackagePerformance,
                    'monthlyTrends' => $monthlyTrends
                ];
            } catch (\Exception $e) {
                Log::error('Error in getPackageAnalytics: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving package analytics',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Get revenue chart data for the specified period
     * 
     * @param string $period 'daily', 'weekly', 'monthly', or 'yearly'
     * @param string|null $compareWith 'previous_period', 'previous_year', or null
     * @return array
     */
    public function getDailyRevenue($period = 'daily', $compareWith = null)
    {
        $cacheKey = "revenue_chart_{$period}_{$compareWith}";
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($period, $compareWith) {
            try {
                switch ($period) {
                    case 'weekly':
                        $result = $this->getWeeklyRevenue($compareWith);
                        break;
                    case 'monthly':
                        $result = $this->getMonthlyRevenue($compareWith);
                        break;
                    case 'yearly':
                        $result = $this->getYearlyRevenue($compareWith);
                        break;
                    case 'daily':
                    default:
                        $result = $this->calculateDailyRevenue($compareWith);
                        break;
                }
                
                return $result;
            } catch (\Exception $e) {
                Log::error('Error in getDailyRevenue: ' . $e->getMessage());
                return [
                    'error' => 'Error retrieving revenue chart data',
                    'message' => $e->getMessage()
                ];
            }
        });
    }
    
    /**
     * Calculate daily revenue with comparison data
     * Uses payment_date from payment_transactions and updated_at from bookings
     * to show revenue when payment was actually received
     * 
     * @param string|null $compareWith 'previous_period', 'previous_year', or null
     * @return array
     */
    protected function calculateDailyRevenue($compareWith = null)
    {
        try {
            // Primary period - last 30 days based on when payment was received
            $startDate = now()->subDays(29)->startOfDay();
            $endDate = now()->endOfDay();
            
            // Get revenue from payment_transactions (when payment was received)
            $dailyTransactionRevenue = DB::table('payment_transactions')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where(function($q) {
                    $q->where(function($subQ) {
                        $subQ->where('payment_method', 'gcash')
                             ->where('xendit_status', 'SUCCEEDED');
                    })
                    ->orWhere(function($subQ) {
                        $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card'])
                             ->where('xendit_status', 'PAID');
                    });
                })
                ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            // Get revenue from bookings marked as paid (when status was updated)
            $dailyBookingRevenue = Booking::whereBetween('updated_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
            
            // Get refunded bookings (when refund was processed)
            $dailyRefundedRevenue = Booking::whereBetween('updated_at', [$startDate, $endDate])
                ->where('payment_status', 'refunded')
                ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');
                
            $dateLabels = [];
            $revenueData = [];
            $compareData = null;
            $totalRevenue = 0;
            $avgRevenue = 0;
            $maxRevenue = 0;
            $minRevenue = PHP_FLOAT_MAX;
            $dayCount = 0;
            
            // Process primary period data
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dateString = $date->format('Y-m-d');
                $dateLabels[] = $date->format('M d');
                
                // Get revenue from both sources and use the higher value
                $transactionAmount = $dailyTransactionRevenue->has($dateString) ? 
                    (float)$dailyTransactionRevenue->get($dateString)->total : 0;
                $bookingAmount = $dailyBookingRevenue->has($dateString) ? 
                    (float)$dailyBookingRevenue->get($dateString)->total : 0;
                
                $paidAmount = max($transactionAmount, $bookingAmount);
                $refundedAmount = $dailyRefundedRevenue->has($dateString) ? 
                    (float)$dailyRefundedRevenue->get($dateString)->total : 0;
                $netAmount = $paidAmount - $refundedAmount;
                
                $revenueData[] = $netAmount;
                
                // Calculate statistics
                $totalRevenue += $netAmount;
                $dayCount++;
                if ($netAmount > $maxRevenue) $maxRevenue = $netAmount;
                if ($netAmount > 0 && $netAmount < $minRevenue) $minRevenue = $netAmount;
            }
            
            $avgRevenue = $dayCount > 0 ? $totalRevenue / $dayCount : 0;
            if ($minRevenue === PHP_FLOAT_MAX) $minRevenue = 0;
            
            // Process comparison data if requested
            if ($compareWith) {
                switch ($compareWith) {
                    case 'previous_period':
                        // Previous 30 days
                        $compareStartDate = now()->subDays(59)->startOfDay();
                        $compareEndDate = now()->subDays(30)->endOfDay();
                        $comparePeriodName = 'Previous 30 Days';
                        break;
                    case 'previous_year':
                        // Same 30-day period last year
                        $compareStartDate = now()->subYear()->subDays(29)->startOfDay();
                        $compareEndDate = now()->subYear()->endOfDay();
                        $comparePeriodName = 'Same Period Last Year';
                        break;
                    default:
                        $compareStartDate = null;
                        $compareEndDate = null;
                }
                
                if ($compareStartDate && $compareEndDate) {
                    // Get payment transactions for comparison period
                    $compareTransactionRevenue = DB::table('payment_transactions')
                        ->whereBetween('payment_date', [$compareStartDate, $compareEndDate])
                        ->where(function($q) {
                            $q->where(function($subQ) {
                                $subQ->where('payment_method', 'gcash')
                                     ->where('xendit_status', 'SUCCEEDED');
                            })
                            ->orWhere(function($subQ) {
                                $subQ->whereIn('payment_method', ['onsite_cash', 'onsite_card'])
                                     ->where('xendit_status', 'PAID');
                            });
                        })
                        ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');
                    
                    // Get bookings marked as paid for comparison period
                    $compareBookingRevenue = Booking::whereBetween('updated_at', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'paid')
                        ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');
                    
                    // Get refunded bookings for comparison period
                    $compareRefundedRevenue = Booking::whereBetween('updated_at', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'refunded')
                        ->selectRaw('DATE(updated_at) as date, SUM(total_amount) as total')
                        ->groupBy('date')
                        ->orderBy('date')
                        ->get()
                        ->keyBy('date');
                    
                    $compareData = [];
                    $compareTotalRevenue = 0;
                    
                    for ($date = $compareStartDate->copy(); $date <= $compareEndDate; $date->addDay()) {
                        $dateString = $date->format('Y-m-d');
                        
                        $transactionAmount = $compareTransactionRevenue->has($dateString) ? 
                            (float)$compareTransactionRevenue->get($dateString)->total : 0;
                        $bookingAmount = $compareBookingRevenue->has($dateString) ? 
                            (float)$compareBookingRevenue->get($dateString)->total : 0;
                        
                        $paidAmount = max($transactionAmount, $bookingAmount);
                        $refundedAmount = $compareRefundedRevenue->has($dateString) ? 
                            (float)$compareRefundedRevenue->get($dateString)->total : 0;
                        $netAmount = $paidAmount - $refundedAmount;
                        
                        $compareData[] = $netAmount;
                        $compareTotalRevenue += $netAmount;
                    }
                    
                    $compareAvgRevenue = count($compareData) > 0 ? $compareTotalRevenue / count($compareData) : 0;
                    $revenueChange = $this->calculatePercentageChange($totalRevenue, $compareTotalRevenue);
                }
            }
            
            $result = [
                'labels' => $dateLabels,
                'data' => $revenueData,
                'stats' => [
                    'total' => $totalRevenue,
                    'average' => $avgRevenue,
                    'max' => $maxRevenue,
                    'min' => $minRevenue,
                    'period' => 'daily'
                ]
            ];
            
            if ($compareWith && isset($compareData)) {
                $result['compare'] = [
                    'data' => $compareData,
                    'name' => $comparePeriodName,
                    'total' => $compareTotalRevenue,
                    'average' => $compareAvgRevenue,
                    'change' => $revenueChange,
                    'trending' => $revenueChange >= 0 ? 'up' : 'down'
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in calculateDailyRevenue: ' . $e->getMessage());
            return [
                'error' => 'Error calculating daily revenue',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get weekly revenue for the last 12 weeks with optional comparison
     * 
     * @param string|null $compareWith 'previous_period', 'previous_year', or null
     * @return array
     */
    protected function getWeeklyRevenue($compareWith = null)
    {
        try {
            $startDate = now()->subWeeks(11)->startOfWeek();
            $endDate = now()->endOfWeek();
            
            // Get paid bookings by week
            $weeklyPaidRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->selectRaw('YEAR(booking_date) as year, WEEK(booking_date) as week, SUM(total_amount) as total')
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get();
            
            // Get refunded bookings by week
            $weeklyRefundedRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'refunded')
                ->selectRaw('YEAR(booking_date) as year, WEEK(booking_date) as week, SUM(total_amount) as total')
                ->groupBy('year', 'week')
                ->orderBy('year')
                ->orderBy('week')
                ->get()
                ->mapWithKeys(function($item) {
                    return ["{$item->year}-{$item->week}" => $item->total];
                });
                
            $labels = [];
            $data = [];
            $compareData = null;
            $totalRevenue = 0;
            $maxRevenue = 0;
            $minRevenue = PHP_FLOAT_MAX;
            $weekCount = 0;
            
            // Process primary period data (paid - refunded)
            for ($date = $startDate->copy(); $date <= $endDate; $date->addWeek()) {
                $year = $date->format('Y');
                $week = $date->format('W');
                $labels[] = 'Week ' . $week;
                
                $weekPaid = $weeklyPaidRevenue->first(function($item) use ($year, $week) {
                    return $item->year == $year && $item->week == $week;
                });
                
                $paidAmount = $weekPaid ? (float)$weekPaid->total : 0;
                $refundedAmount = $weeklyRefundedRevenue->get("{$year}-{$week}", 0);
                $netAmount = $paidAmount - $refundedAmount;
                
                $data[] = $netAmount;
                
                // Calculate statistics
                $totalRevenue += $netAmount;
                $weekCount++;
                if ($netAmount > $maxRevenue) $maxRevenue = $netAmount;
                if ($netAmount > 0 && $netAmount < $minRevenue) $minRevenue = $netAmount;
            }
            
            $avgRevenue = $weekCount > 0 ? $totalRevenue / $weekCount : 0;
            if ($minRevenue === PHP_FLOAT_MAX) $minRevenue = 0;
            
            // Process comparison data if requested
            if ($compareWith) {
                switch ($compareWith) {
                    case 'previous_period':
                        // Previous 12 weeks
                        $compareStartDate = now()->subWeeks(23)->startOfWeek();
                        $compareEndDate = now()->subWeeks(12)->endOfWeek();
                        $comparePeriodName = 'Previous 12 Weeks';
                        break;
                    case 'previous_year':
                        // Same 12-week period last year
                        $compareStartDate = now()->subYear()->subWeeks(11)->startOfWeek();
                        $compareEndDate = now()->subYear()->endOfWeek();
                        $comparePeriodName = 'Same Period Last Year';
                        break;
                    default:
                        $compareStartDate = null;
                        $compareEndDate = null;
                }
                
                if ($compareStartDate && $compareEndDate) {
                    // Get paid bookings for comparison period
                    $comparePaidRevenue = Booking::whereBetween('booking_date', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'paid')
                        ->selectRaw('YEAR(booking_date) as year, WEEK(booking_date) as week, SUM(total_amount) as total')
                        ->groupBy('year', 'week')
                        ->orderBy('year')
                        ->orderBy('week')
                        ->get();
                    
                    // Get refunded bookings for comparison period
                    $compareRefundedRevenue = Booking::whereBetween('booking_date', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'refunded')
                        ->selectRaw('YEAR(booking_date) as year, WEEK(booking_date) as week, SUM(total_amount) as total')
                        ->groupBy('year', 'week')
                        ->orderBy('year')
                        ->orderBy('week')
                        ->get()
                        ->mapWithKeys(function($item) {
                            return ["{$item->year}-{$item->week}" => $item->total];
                        });
                    
                    $compareData = [];
                    $compareTotalRevenue = 0;
                    
                    for ($date = $compareStartDate->copy(); $date <= $compareEndDate; $date->addWeek()) {
                        $year = $date->format('Y');
                        $week = $date->format('W');
                        
                        $weekPaid = $comparePaidRevenue->first(function($item) use ($year, $week) {
                            return $item->year == $year && $item->week == $week;
                        });
                        
                        $paidAmount = $weekPaid ? (float)$weekPaid->total : 0;
                        $refundedAmount = $compareRefundedRevenue->get("{$year}-{$week}", 0);
                        $netAmount = $paidAmount - $refundedAmount;
                        
                        $compareData[] = $netAmount;
                        $compareTotalRevenue += $netAmount;
                    }
                    
                    $compareAvgRevenue = count($compareData) > 0 ? $compareTotalRevenue / count($compareData) : 0;
                    $revenueChange = $this->calculatePercentageChange($totalRevenue, $compareTotalRevenue);
                }
            }
            
            $result = [
                'labels' => $labels,
                'data' => $data,
                'stats' => [
                    'total' => $totalRevenue,
                    'average' => $avgRevenue,
                    'max' => $maxRevenue,
                    'min' => $minRevenue,
                    'period' => 'weekly'
                ]
            ];
            
            if ($compareWith && isset($compareData)) {
                $result['compare'] = [
                    'data' => $compareData,
                    'name' => $comparePeriodName,
                    'total' => $compareTotalRevenue,
                    'average' => $compareAvgRevenue,
                    'change' => $revenueChange,
                    'trending' => $revenueChange >= 0 ? 'up' : 'down'
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getWeeklyRevenue: ' . $e->getMessage());
            return [
                'error' => 'Error retrieving weekly revenue',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get monthly revenue for the last 12 months with optional comparison
     * 
     * @param string|null $compareWith 'previous_period', 'previous_year', or null
     * @return array
     */
    protected function getMonthlyRevenue($compareWith = null)
    {
        try {
            $startDate = now()->subMonths(11)->startOfMonth();
            $endDate = now()->endOfMonth();
            
            // Get paid bookings by month
            $monthlyPaidRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, SUM(total_amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
            
            // Get refunded bookings by month
            $monthlyRefundedRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'refunded')
                ->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, SUM(total_amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
                ->mapWithKeys(function($item) {
                    return ["{$item->year}-{$item->month}" => $item->total];
                });
                
            $labels = [];
            $data = [];
            $compareData = null;
            $totalRevenue = 0;
            $maxRevenue = 0;
            $minRevenue = PHP_FLOAT_MAX;
            $monthCount = 0;
            
            // Process primary period data (paid - refunded)
            for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
                $year = $date->format('Y');
                $month = $date->format('n');
                $labels[] = $date->format('M Y');
                
                $monthPaid = $monthlyPaidRevenue->first(function($item) use ($year, $month) {
                    return $item->year == $year && $item->month == $month;
                });
                
                $paidAmount = $monthPaid ? (float)$monthPaid->total : 0;
                $refundedAmount = $monthlyRefundedRevenue->get("{$year}-{$month}", 0);
                $netAmount = $paidAmount - $refundedAmount;
                
                $data[] = $netAmount;
                
                // Calculate statistics
                $totalRevenue += $netAmount;
                $monthCount++;
                if ($netAmount > $maxRevenue) $maxRevenue = $netAmount;
                if ($netAmount > 0 && $netAmount < $minRevenue) $minRevenue = $netAmount;
            }
            
            $avgRevenue = $monthCount > 0 ? $totalRevenue / $monthCount : 0;
            if ($minRevenue === PHP_FLOAT_MAX) $minRevenue = 0;
            
            // Process comparison data if requested
            if ($compareWith) {
                switch ($compareWith) {
                    case 'previous_period':
                        // Previous 12 months
                        $compareStartDate = now()->subMonths(23)->startOfMonth();
                        $compareEndDate = now()->subMonths(12)->endOfMonth();
                        $comparePeriodName = 'Previous 12 Months';
                        break;
                    case 'previous_year':
                        // Same 12-month period last year
                        $compareStartDate = now()->subYear()->subMonths(11)->startOfMonth();
                        $compareEndDate = now()->subYear()->endOfMonth();
                        $comparePeriodName = 'Same Period Last Year';
                        break;
                    default:
                        $compareStartDate = null;
                        $compareEndDate = null;
                }
                
                if ($compareStartDate && $compareEndDate) {
                    // Get paid bookings for comparison period
                    $comparePaidRevenue = Booking::whereBetween('booking_date', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'paid')
                        ->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, SUM(total_amount) as total')
                        ->groupBy('year', 'month')
                        ->orderBy('year')
                        ->orderBy('month')
                        ->get();
                    
                    // Get refunded bookings for comparison period
                    $compareRefundedRevenue = Booking::whereBetween('booking_date', [$compareStartDate, $compareEndDate])
                        ->where('payment_status', 'refunded')
                        ->selectRaw('YEAR(booking_date) as year, MONTH(booking_date) as month, SUM(total_amount) as total')
                        ->groupBy('year', 'month')
                        ->orderBy('year')
                        ->orderBy('month')
                        ->get()
                        ->mapWithKeys(function($item) {
                            return ["{$item->year}-{$item->month}" => $item->total];
                        });
                    
                    $compareData = [];
                    $compareTotalRevenue = 0;
                    
                    for ($date = $compareStartDate->copy(); $date <= $compareEndDate; $date->addMonth()) {
                        $year = $date->format('Y');
                        $month = $date->format('n');
                        
                        $monthPaid = $comparePaidRevenue->first(function($item) use ($year, $month) {
                            return $item->year == $year && $item->month == $month;
                        });
                        
                        $paidAmount = $monthPaid ? (float)$monthPaid->total : 0;
                        $refundedAmount = $compareRefundedRevenue->get("{$year}-{$month}", 0);
                        $netAmount = $paidAmount - $refundedAmount;
                        
                        $compareData[] = $netAmount;
                        $compareTotalRevenue += $netAmount;
                    }
                    
                    $compareAvgRevenue = count($compareData) > 0 ? $compareTotalRevenue / count($compareData) : 0;
                    $revenueChange = $this->calculatePercentageChange($totalRevenue, $compareTotalRevenue);
                }
            }
            
            $result = [
                'labels' => $labels,
                'data' => $data,
                'stats' => [
                    'total' => $totalRevenue,
                    'average' => $avgRevenue,
                    'max' => $maxRevenue,
                    'min' => $minRevenue,
                    'period' => 'monthly'
                ]
            ];
            
            if ($compareWith && isset($compareData)) {
                $result['compare'] = [
                    'data' => $compareData,
                    'name' => $comparePeriodName,
                    'total' => $compareTotalRevenue,
                    'average' => $compareAvgRevenue,
                    'change' => $revenueChange,
                    'trending' => $revenueChange >= 0 ? 'up' : 'down'
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getMonthlyRevenue: ' . $e->getMessage());
            return [
                'error' => 'Error retrieving monthly revenue',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get yearly revenue for the last 5 years with optional comparison
     * 
     * @param string|null $compareWith Not applicable for yearly view, kept for consistency
     * @return array
     */
    protected function getYearlyRevenue($compareWith = null)
    {
        try {
            $startDate = now()->subYears(4)->startOfYear();
            $endDate = now()->endOfYear();
            
            // Get paid bookings by year
            $yearlyPaidRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->selectRaw('YEAR(booking_date) as year, SUM(total_amount) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->keyBy('year');
            
            // Get refunded bookings by year
            $yearlyRefundedRevenue = Booking::whereBetween('booking_date', [$startDate, $endDate])
                ->where('payment_status', 'refunded')
                ->selectRaw('YEAR(booking_date) as year, SUM(total_amount) as total')
                ->groupBy('year')
                ->orderBy('year')
                ->get()
                ->mapWithKeys(function($item) {
                    return [$item->year => $item->total];
                });
                
            $labels = [];
            $data = [];
            $totalRevenue = 0;
            $maxRevenue = 0;
            $minRevenue = PHP_FLOAT_MAX;
            $yearCount = 0;
            
            // Process primary period data (paid - refunded)
            for ($date = $startDate->copy(); $date <= $endDate; $date->addYear()) {
                $year = $date->format('Y');
                $labels[] = $year;
                
                $paidAmount = $yearlyPaidRevenue->has($year) ? (float)$yearlyPaidRevenue->get($year)->total : 0;
                $refundedAmount = $yearlyRefundedRevenue->get($year, 0);
                $netAmount = $paidAmount - $refundedAmount;
                
                $data[] = $netAmount;
                
                // Calculate statistics
                $totalRevenue += $netAmount;
                $yearCount++;
                if ($netAmount > $maxRevenue) $maxRevenue = $netAmount;
                if ($netAmount > 0 && $netAmount < $minRevenue) $minRevenue = $netAmount;
            }
            
            $avgRevenue = $yearCount > 0 ? $totalRevenue / $yearCount : 0;
            if ($minRevenue === PHP_FLOAT_MAX) $minRevenue = 0;
            
            // Calculate year-over-year growth rates
            $growthRates = [];
            for ($i = 1; $i < count($data); $i++) {
                $previousYear = $data[$i-1];
                $currentYear = $data[$i];
                $growthRates[] = $previousYear > 0 ? 
                    (($currentYear - $previousYear) / $previousYear) * 100 : 
                    ($currentYear > 0 ? 100 : 0);
            }
            
            return [
                'labels' => $labels,
                'data' => $data,
                'stats' => [
                    'total' => $totalRevenue,
                    'average' => $avgRevenue,
                    'max' => $maxRevenue,
                    'min' => $minRevenue,
                    'period' => 'yearly',
                    'growthRates' => $growthRates
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error in getYearlyRevenue: ' . $e->getMessage());
            return [
                'error' => 'Error retrieving yearly revenue',
                'message' => $e->getMessage()
            ];
        }
    }
}
