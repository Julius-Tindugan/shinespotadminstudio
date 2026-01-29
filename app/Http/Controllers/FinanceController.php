<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\RevenueStatistic;
use App\Models\Booking;
use App\Models\ExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\FinanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Get dashboard summary data for API
     */
    public function getDashboardSummary()
    {
        // Get summary data for current month, previous month and year-to-date
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $previousMonth = Carbon::now()->subMonth();
        
        // Current Month
        $currentMonthRevenue = Payment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->completed()
            ->sum('amount');
            
        $currentMonthExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');
            
        // Previous Month
        $previousMonthRevenue = Payment::whereMonth('payment_date', $previousMonth->month)
            ->whereYear('payment_date', $previousMonth->year)
            ->completed()
            ->sum('amount');
            
        $previousMonthExpenses = Expense::whereMonth('expense_date', $previousMonth->month)
            ->whereYear('expense_date', $previousMonth->year)
            ->sum('amount');
            
        // Year to Date
        $ytdRevenue = Payment::whereYear('payment_date', $currentYear)
            ->completed()
            ->sum('amount');
            
        $ytdExpenses = Expense::whereYear('expense_date', $currentYear)
            ->sum('amount');
            
        // Bookings counts
        $completedBookings = Booking::where('status', 'completed')
            ->whereMonth('booking_date', $currentMonth)
            ->whereYear('booking_date', $currentYear)
            ->count();
            
        $cancelledBookings = Booking::where('status', 'cancelled')
            ->whereMonth('booking_date', $currentMonth)
            ->whereYear('booking_date', $currentYear)
            ->count();
            
        // Calculate profits
        $currentMonthProfit = $currentMonthRevenue - $currentMonthExpenses;
        $previousMonthProfit = $previousMonthRevenue - $previousMonthExpenses;
        $ytdProfit = $ytdRevenue - $ytdExpenses;
        
        // Calculate month-over-month changes
        $revenueChange = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 100;
            
        $expenseChange = $previousMonthExpenses > 0 
            ? (($currentMonthExpenses - $previousMonthExpenses) / $previousMonthExpenses) * 100 
            : 100;
            
        $profitChange = $previousMonthProfit > 0 
            ? (($currentMonthProfit - $previousMonthProfit) / $previousMonthProfit) * 100 
            : 100;
            
        return response()->json([
            'current_month' => [
                'revenue' => $currentMonthRevenue,
                'expenses' => $currentMonthExpenses,
                'profit' => $currentMonthProfit,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings
            ],
            'previous_month' => [
                'revenue' => $previousMonthRevenue,
                'expenses' => $previousMonthExpenses,
                'profit' => $previousMonthProfit
            ],
            'ytd' => [
                'revenue' => $ytdRevenue,
                'expenses' => $ytdExpenses,
                'profit' => $ytdProfit
            ],
            'changes' => [
                'revenue' => $revenueChange,
                'expenses' => $expenseChange,
                'profit' => $profitChange
            ]
        ]);
    }
    
    /**
     * Get revenue trend data for API
     */
    public function getRevenueTrend(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $limit = $request->input('limit', 6);
        
        if ($period === 'monthly') {
            $result = Payment::completed()
                ->select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit($limit)
                ->get();
            
            $formatted = $result->map(function ($item) {
                $date = Carbon::createFromDate($item->year, $item->month, 1);
                return [
                    'label' => $date->format('M Y'),
                    'total' => $item->total
                ];
            });
        } else {
            // Daily trend for last N days
            $result = Payment::completed()
                ->select(
                    DB::raw('DATE(payment_date) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('payment_date', '>=', now()->subDays($limit))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $formatted = $result->map(function ($item) {
                $date = Carbon::parse($item->date);
                return [
                    'label' => $date->format('M d'),
                    'total' => $item->total
                ];
            });
        }
        
        return response()->json(array_values($formatted->toArray()));
    }
    
    /**
     * Get expense trend data for API
     */
    public function getExpenseTrend(Request $request)
    {
        $period = $request->input('period', 'monthly');
        $limit = $request->input('limit', 6);
        
        if ($period === 'monthly') {
            $result = Expense::select(
                    DB::raw('YEAR(expense_date) as year'),
                    DB::raw('MONTH(expense_date) as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->limit($limit)
                ->get();
            
            $formatted = $result->map(function ($item) {
                $date = Carbon::createFromDate($item->year, $item->month, 1);
                return [
                    'label' => $date->format('M Y'),
                    'total' => $item->total
                ];
            });
        } else {
            // Daily trend for last N days
            $result = Expense::select(
                    DB::raw('DATE(expense_date) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('expense_date', '>=', now()->subDays($limit))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
                
            $formatted = $result->map(function ($item) {
                $date = Carbon::parse($item->date);
                return [
                    'label' => $date->format('M d'),
                    'total' => $item->total
                ];
            });
        }
        
        return response()->json(array_values($formatted->toArray()));
    }
    /**
     * Display the finance dashboard with key metrics and overview.
     */
    public function index()
    {
        // Get summary data
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Get total revenue for current month
        $monthlyRevenue = Payment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->completed()
            ->sum('amount');
            
        // Get total expenses for current month
        $monthlyExpenses = Expense::whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->sum('amount');
        
        // Calculate net profit
        $netProfit = $monthlyRevenue - $monthlyExpenses;
        
        // Get pending payments (bookings with unpaid status)
        $pendingPayments = Booking::where('payment_status', 'unpaid')
            ->sum('total_amount');
            
        // Get top expense categories
        $topExpenseCategories = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->whereMonth('expense_date', $currentMonth)
            ->whereYear('expense_date', $currentYear)
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                // Load the category relationship for each item
                $categoryModel = ExpenseCategory::where('name', $item->category)->first();
                $item->categoryRelation = $categoryModel;
                return $item;
            });
            
        // Get revenue for the past 6 months for chart
        $sixMonthsAgo = Carbon::now()->subMonths(5);
        $revenueByMonth = Payment::select(
                DB::raw('YEAR(payment_date) as year'),
                DB::raw('MONTH(payment_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('payment_date', '>=', $sixMonthsAgo)
            ->completed()
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        $expensesByMonth = Expense::select(
                DB::raw('YEAR(expense_date) as year'),
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('expense_date', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Format the chart data
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];
        
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $chartLabels[] = $monthYear;
            
            // Find revenue for this month
            $revenue = $revenueByMonth->first(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            
            $chartRevenue[] = $revenue ? $revenue->total : 0;
            
            // Find expenses for this month
            $expense = $expensesByMonth->first(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            
            $chartExpenses[] = $expense ? $expense->total : 0;
        }
        
        // Reverse arrays to show oldest to newest
        $chartLabels = array_reverse($chartLabels);
        $chartRevenue = array_reverse($chartRevenue);
        $chartExpenses = array_reverse($chartExpenses);
        
        // Recent payments (last 5)
        $recentPayments = Payment::with(['booking.package'])
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();
            
        // Recent expenses (last 5)
        $recentExpenses = Expense::with('categoryRelation')
            ->orderBy('expense_date', 'desc')
            ->limit(5)
            ->get();
            
        return view('finance.dashboard', compact(
            'monthlyRevenue', 
            'monthlyExpenses', 
            'netProfit', 
            'pendingPayments',
            'topExpenseCategories',
            'chartLabels',
            'chartRevenue',
            'chartExpenses',
            'recentPayments',
            'recentExpenses'
        ));
    }
    
    /**
     * Get financial reports page
     */
    public function reports(Request $request)
    {
        $expenseCategories = ExpenseCategory::all();
        $paymentMethods = PaymentMethod::all();
        
        // Get filter parameters
        $reportType = $request->input('report_type', 'profit_loss');
        $period = $request->input('period', 'monthly');
        
        // Calculate date range based on period
        $dateFrom = null;
        $dateTo = null;
        
        if ($period === 'custom') {
            $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from) : now()->startOfMonth();
            $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to) : now();
        } else {
            $dateTo = now();
            switch ($period) {
                case 'monthly':
                    $dateFrom = now()->startOfMonth();
                    break;
                case 'quarterly':
                    $dateFrom = now()->startOfQuarter();
                    break;
                case 'yearly':
                    $dateFrom = now()->startOfYear();
                    break;
                default:
                    $dateFrom = now()->startOfMonth();
            }
        }
        
        // Set report title and date range
        $reportTitle = ucwords(str_replace('_', ' & ', $reportType)) . ' Report';
        $reportDateRange = $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y');
        
        // Get payment transactions (revenue) using correct table name
        $revenueQuery = DB::table('payment_transactions')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->where(function($query) {
                $query->where('payment_method', 'onsite_cash')
                      ->orWhere('payment_method', 'onsite_card')
                      ->orWhere('xendit_status', 'PAID')
                      ->orWhere('xendit_status', 'COMPLETED');
            });
        
        $totalRevenue = $revenueQuery->sum('amount');
        
        // Get expenses
        $totalExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
        
        // Calculate metrics
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        // Generate period data (monthly breakdown)
        $periodData = [];
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];
        $chartProfit = [];
        $chartBookings = [];
        
        // Determine the grouping based on period
        if ($period === 'yearly' || $dateFrom->diffInMonths($dateTo) > 3) {
            // Group by month
            $currentDate = $dateFrom->copy()->startOfMonth();
            while ($currentDate <= $dateTo) {
                $monthStart = $currentDate->copy()->startOfMonth();
                $monthEnd = $currentDate->copy()->endOfMonth();
                
                $monthRevenue = DB::table('payment_transactions')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->where(function($query) {
                        $query->where('payment_method', 'onsite_cash')
                              ->orWhere('payment_method', 'onsite_card')
                              ->orWhere('xendit_status', 'PAID')
                              ->orWhere('xendit_status', 'COMPLETED');
                    })
                    ->sum('amount');
                    
                $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])->sum('amount');
                $monthProfit = $monthRevenue - $monthExpenses;
                $monthMargin = $monthRevenue > 0 ? ($monthProfit / $monthRevenue) * 100 : 0;
                
                $monthBookings = Booking::whereBetween('start_time', [$monthStart, $monthEnd])->count();
                
                $label = $currentDate->format('M Y');
                $chartLabels[] = $label;
                $chartRevenue[] = (float)$monthRevenue;
                $chartExpenses[] = (float)$monthExpenses;
                $chartProfit[] = (float)$monthProfit;
                $chartBookings[] = $monthBookings;
                
                $periodData[$label] = [
                    'revenue' => $monthRevenue,
                    'expenses' => $monthExpenses,
                    'profit' => $monthProfit,
                    'margin' => $monthMargin
                ];
                
                $currentDate->addMonth();
            }
        } else {
            // Group by day for shorter periods
            $currentDate = $dateFrom->copy();
            while ($currentDate <= $dateTo) {
                $dayRevenue = DB::table('payment_transactions')
                    ->whereDate('payment_date', $currentDate)
                    ->where(function($query) {
                        $query->where('payment_method', 'onsite_cash')
                              ->orWhere('payment_method', 'onsite_card')
                              ->orWhere('xendit_status', 'PAID')
                              ->orWhere('xendit_status', 'COMPLETED');
                    })
                    ->sum('amount');
                    
                $dayExpenses = Expense::whereDate('expense_date', $currentDate)->sum('amount');
                $dayProfit = $dayRevenue - $dayExpenses;
                $dayMargin = $dayRevenue > 0 ? ($dayProfit / $dayRevenue) * 100 : 0;
                
                $dayBookings = Booking::whereDate('start_time', $currentDate)->count();
                
                $label = $currentDate->format('M d');
                $chartLabels[] = $label;
                $chartRevenue[] = (float)$dayRevenue;
                $chartExpenses[] = (float)$dayExpenses;
                $chartProfit[] = (float)$dayProfit;
                $chartBookings[] = $dayBookings;
                
                $periodData[$label] = [
                    'revenue' => $dayRevenue,
                    'expenses' => $dayExpenses,
                    'profit' => $dayProfit,
                    'margin' => $dayMargin
                ];
                
                $currentDate->addDay();
            }
        }
        
        // Revenue Analysis Data
        $packageRevenueData = DB::table('payment_transactions as pt')
            ->join('bookings as b', 'pt.booking_id', '=', 'b.booking_id')
            ->join('packages as p', 'b.package_id', '=', 'p.package_id')
            ->whereBetween('pt.payment_date', [$dateFrom, $dateTo])
            ->where(function($query) {
                $query->where('pt.payment_method', 'onsite_cash')
                      ->orWhere('pt.payment_method', 'onsite_card')
                      ->orWhere('pt.xendit_status', 'PAID')
                      ->orWhere('pt.xendit_status', 'COMPLETED');
            })
            ->select('p.title as name', DB::raw('SUM(pt.amount) as amount'))
            ->groupBy('p.package_id', 'p.title')
            ->get()
            ->toArray();
            
        $paymentMethodData = DB::table('payment_transactions')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->where(function($query) {
                $query->where('payment_method', 'onsite_cash')
                      ->orWhere('payment_method', 'onsite_card')
                      ->orWhere('xendit_status', 'PAID')
                      ->orWhere('xendit_status', 'COMPLETED');
            })
            ->select('payment_method as name', DB::raw('SUM(amount) as amount'))
            ->groupBy('payment_method')
            ->get()
            ->map(function($item) {
                $item->name = ucwords(str_replace('_', ' ', $item->name));
                return $item;
            })
            ->toArray();
            
        $revenueSources = collect($packageRevenueData)->map(function($item) use ($totalRevenue) {
            return [
                'name' => $item->name,
                'amount' => $item->amount,
                'percentage' => $totalRevenue > 0 ? ($item->amount / $totalRevenue) * 100 : 0
            ];
        })->sortByDesc('amount')->take(5)->values()->toArray();
        
        // Expense Analysis Data
        $expenseByCategory = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->select('category', DB::raw('SUM(amount) as amount'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get()
            ->map(function($item) use ($totalExpenses) {
                return [
                    'name' => $item->category ?? 'Uncategorized',
                    'amount' => $item->amount,
                    'count' => $item->count,
                    'average' => $item->count > 0 ? $item->amount / $item->count : 0,
                    'percentage' => $totalExpenses > 0 ? ($item->amount / $totalExpenses) * 100 : 0,
                    'color' => $this->getColorForCategory($item->category)
                ];
            })
            ->toArray();
            
        $topExpenses = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])
            ->orderBy('amount', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) use ($totalExpenses) {
                return [
                    'title' => $item->title ?? $item->description ?? 'Untitled Expense',
                    'amount' => $item->amount,
                    'percentage' => $totalExpenses > 0 ? ($item->amount / $totalExpenses) * 100 : 0,
                    'color' => $this->getColorForCategory($item->category)
                ];
            })
            ->toArray();
            
        $totalExpenseCount = Expense::whereBetween('expense_date', [$dateFrom, $dateTo])->count();
        $averageExpense = $totalExpenseCount > 0 ? $totalExpenses / $totalExpenseCount : 0;
        
        // Booking Performance Data (using start_time as the primary date field)
        $totalBookings = Booking::whereBetween('start_time', [$dateFrom, $dateTo])->count();
        $completedBookings = Booking::whereBetween('start_time', [$dateFrom, $dateTo])
            ->where('status', 'completed')->count();
        $completedBookingsPercentage = $totalBookings > 0 ? ($completedBookings / $totalBookings) * 100 : 0;
        $averageBookingValue = $totalBookings > 0 ? Booking::whereBetween('start_time', [$dateFrom, $dateTo])->avg('total_amount') : 0;
        $revenuePerBooking = $completedBookings > 0 ? $totalRevenue / $completedBookings : 0;
        
        $packageBookingData = DB::table('bookings as b')
            ->join('packages as p', 'b.package_id', '=', 'p.package_id')
            ->whereBetween('b.start_time', [$dateFrom, $dateTo])
            ->select('p.title as name', DB::raw('COUNT(*) as count'))
            ->groupBy('p.package_id', 'p.title')
            ->get()
            ->toArray();
            
        $bookingStatusData = Booking::whereBetween('start_time', [$dateFrom, $dateTo])
            ->select('status as name', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                $item->name = ucwords(str_replace('_', ' ', $item->name));
                return $item;
            })
            ->toArray();
            
        $packagePerformance = DB::table('bookings as b')
            ->join('packages as p', 'b.package_id', '=', 'p.package_id')
            ->leftJoin('payment_transactions as pt', 'b.booking_id', '=', 'pt.booking_id')
            ->whereBetween('b.start_time', [$dateFrom, $dateTo])
            ->select(
                'p.title as name',
                DB::raw('COUNT(DISTINCT b.booking_id) as count'),
                DB::raw('SUM(CASE WHEN pt.xendit_status IN ("PAID", "COMPLETED") OR pt.payment_method IN ("onsite_cash", "onsite_card") THEN pt.amount ELSE 0 END) as revenue')
            )
            ->groupBy('p.package_id', 'p.title')
            ->get()
            ->map(function($item) use ($totalRevenue) {
                $item->average = $item->count > 0 ? $item->revenue / $item->count : 0;
                $item->percentage = $totalRevenue > 0 ? ($item->revenue / $totalRevenue) * 100 : 0;
                return $item;
            })
            ->toArray();
        
        return view('finance.reports', compact(
            'expenseCategories', 
            'paymentMethods', 
            'reportTitle', 
            'reportDateRange',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'profitMargin',
            'periodData',
            'chartLabels',
            'chartRevenue',
            'chartExpenses',
            'chartProfit',
            'packageRevenueData',
            'paymentMethodData',
            'revenueSources',
            'expenseByCategory',
            'topExpenses',
            'totalExpenseCount',
            'averageExpense',
            'packageBookingData',
            'bookingStatusData',
            'chartBookings',
            'totalBookings',
            'completedBookings',
            'completedBookingsPercentage',
            'averageBookingValue',
            'revenuePerBooking',
            'packagePerformance'
        ));
    }
    
    /**
     * Get color for expense category
     */
    private function getColorForCategory($category)
    {
        $colors = [
            'Supplies' => '#3B82F6',
            'Utilities' => '#10B981',
            'Rent' => '#F59E0B',
            'Salaries' => '#8B5CF6',
            'Marketing' => '#EC4899',
            'Equipment' => '#6366F1',
            'Maintenance' => '#14B8A6',
            'Other' => '#6B7280',
        ];
        
        return $colors[$category] ?? '#' . substr(md5($category ?? 'default'), 0, 6);
    }
    
    /**
     * Generate a financial report based on filters
     */
    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:revenue,expenses,profit_loss,payments',
        ]);
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $result = [];
        
        switch ($request->report_type) {
            case 'revenue':
                $result = $this->generateRevenueReport($startDate, $endDate, $request);
                break;
            case 'expenses':
                $result = $this->generateExpensesReport($startDate, $endDate, $request);
                break;
            case 'profit_loss':
                $result = $this->generateProfitLossReport($startDate, $endDate, $request);
                break;
            case 'payments':
                $result = $this->generatePaymentsReport($startDate, $endDate, $request);
                break;
        }
        
        return response()->json($result);
    }
    
    /**
     * Generate a revenue report
     */
    private function generateRevenueReport($startDate, $endDate, $request)
    {
        $query = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->completed();
            
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        $data = $query->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $totalRevenue = $query->sum('amount');
        
        return [
            'data' => $data,
            'total' => $totalRevenue,
            'title' => 'Revenue Report: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
        ];
    }
    
    /**
     * Generate an expenses report
     */
    private function generateExpensesReport($startDate, $endDate, $request)
    {
        $query = Expense::whereBetween('expense_date', [$startDate, $endDate]);
            
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        $expensesByCategory = $query->select(
                'category',
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('category')
            ->get();
            
        $expensesByDate = $query->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $totalExpenses = $query->sum('amount');
        
        return [
            'byCategory' => $expensesByCategory,
            'byDate' => $expensesByDate,
            'total' => $totalExpenses,
            'title' => 'Expenses Report: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
        ];
    }
    
    /**
     * Generate a profit/loss report
     */
    private function generateProfitLossReport($startDate, $endDate, $request)
    {
        // Get revenue by day
        $revenue = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->completed()
            ->select(
                DB::raw('DATE(payment_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
            
        // Get expenses by day
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(expense_date) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
            
        // Combine revenue and expenses into profit/loss report
        $profitLoss = [];
        $totalRevenue = 0;
        $totalExpenses = 0;
        
        $current = clone $startDate;
        while ($current <= $endDate) {
            $date = $current->format('Y-m-d');
            
            $dailyRevenue = isset($revenue[$date]) ? $revenue[$date]->total : 0;
            $dailyExpenses = isset($expenses[$date]) ? $expenses[$date]->total : 0;
            $dailyProfit = $dailyRevenue - $dailyExpenses;
            
            $totalRevenue += $dailyRevenue;
            $totalExpenses += $dailyExpenses;
            
            $profitLoss[] = [
                'date' => $current->format('M d, Y'),
                'revenue' => $dailyRevenue,
                'expenses' => $dailyExpenses,
                'profit' => $dailyProfit
            ];
            
            $current->addDay();
        }
        
        $totalProfit = $totalRevenue - $totalExpenses;
        
        return [
            'data' => $profitLoss,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'title' => 'Profit/Loss Report: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
        ];
    }
    
    /**
     * Generate a payments report
     */
    private function generatePaymentsReport($startDate, $endDate, $request)
    {
        $query = Payment::whereBetween('payment_date', [$startDate, $endDate]);
            
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Note: Removed status filter as payment_transactions table doesn't have a status column
        // Payment completion is determined by xendit_status or payment_method type
        
        $payments = $query->with(['booking.package'])
            ->orderBy('payment_date')
            ->get();
            
        $totalAmount = $payments->sum('amount');
        
        $paymentsByMethod = $payments->groupBy('payment_method')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
            
        // Group by xendit_status for online payments or payment_method type
        $paymentsByStatus = $payments->groupBy(function($payment) {
                if ($payment->payment_method === 'gcash') {
                    return $payment->xendit_status ?? 'PENDING';
                }
                return 'COMPLETED'; // Onsite payments are automatically completed
            })
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total' => $group->sum('amount')
                ];
            });
        
        return [
            'payments' => $payments,
            'byMethod' => $paymentsByMethod,
            'byStatus' => $paymentsByStatus,
            'total' => $totalAmount,
            'count' => $payments->count(),
            'title' => 'Payments Report: ' . $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y')
        ];
    }
    
    /**
     * Display Key Performance Indicators page
     */
    public function kpis(Request $request)
    {
        // Get date range from request
        $period = $request->input('period', 'monthly');
        $startDate = null;
        $endDate = now();
        
        switch ($period) {
            case 'monthly':
                $startDate = now()->startOfMonth();
                break;
            case 'quarterly':
                $startDate = now()->startOfQuarter();
                break;
            case 'yearly':
                $startDate = now()->startOfYear();
                break;
            case 'custom':
                $startDate = $request->filled('date_from') ? Carbon::parse($request->date_from) : now()->startOfMonth();
                $endDate = $request->filled('date_to') ? Carbon::parse($request->date_to) : now();
                break;
        }
        
        // Get previous period for comparison
        $previousPeriodLength = $endDate->diffInDays($startDate);
        $previousEndDate = $startDate->copy()->subDay();
        $previousStartDate = $previousEndDate->copy()->subDays($previousPeriodLength);
        
        // Get financial data for current period
        $revenue = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->completed()
            ->sum('amount');
            
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->sum('amount');
            
        $netProfit = $revenue - $expenses;
        
        // Get financial data for previous period
        $previousRevenue = Payment::whereBetween('payment_date', [$previousStartDate, $previousEndDate])
            ->completed()
            ->sum('amount');
            
        $previousExpenses = Expense::whereBetween('expense_date', [$previousStartDate, $previousEndDate])
            ->sum('amount');
            
        $previousNetProfit = $previousRevenue - $previousExpenses;
        
        // Calculate KPI metrics
        $revenueGrowth = $previousRevenue > 0 ? (($revenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $profitMargin = $revenue > 0 ? ($netProfit / $revenue) * 100 : 0;
        $costEfficiency = $revenue > 0 ? ($expenses / $revenue) * 100 : 0;
        
        $revenueGrowthTrend = $previousRevenue > 0 ? (($revenue - $previousRevenue) / $previousRevenue) * 100 : 0;
        $profitMarginTrend = $previousRevenue > 0 && $previousNetProfit > 0 ? 
            $profitMargin - (($previousNetProfit / $previousRevenue) * 100) : 0;
        $costEfficiencyTrend = $previousRevenue > 0 && $previousExpenses > 0 ? 
            $costEfficiency - (($previousExpenses / $previousRevenue) * 100) : 0;
        
        // Additional KPIs
        $totalBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])->count();
        $completedBookings = Booking::whereBetween('booking_date', [$startDate, $endDate])->where('status', 'completed')->count();
        $bookingEfficiency = $totalBookings > 0 ? $completedBookings / $totalBookings : 0;
        
        $avgTransactionValue = $completedBookings > 0 ? $revenue / $completedBookings : 0;
        $previousAvgTransactionValue = $previousRevenue > 0 ? 
            $previousRevenue / Booking::whereBetween('booking_date', [$previousStartDate, $previousEndDate])
                ->where('status', 'completed')->count() : 0;
        $avgTransactionValueTrend = $previousAvgTransactionValue > 0 ? 
            (($avgTransactionValue - $previousAvgTransactionValue) / $previousAvgTransactionValue) * 100 : 0;
        
        // Get chart data
        $months = 6;
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];
        $chartMargins = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthRevenue = Payment::whereBetween('payment_date', [$monthStart, $monthEnd])
                ->completed()
                ->sum('amount');
                
            $monthExpenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $monthProfit = $monthRevenue - $monthExpenses;
            $monthMargin = $monthRevenue > 0 ? ($monthProfit / $monthRevenue) * 100 : 0;
            
            $chartLabels[] = $monthStart->format('M Y');
            $chartRevenue[] = $monthRevenue;
            $chartExpenses[] = $monthExpenses;
            $chartMargins[] = $monthMargin;
        }
        
        // Get KPI targets from settings
        $kpiSettings = FinanceSetting::getGroupAsArray('kpi');
        
        $targetProfitMargin = (float) ($kpiSettings['kpi_target_profit_margin'] ?? 25) / 100;
        $targetCostEfficiency = (float) ($kpiSettings['kpi_target_cost_efficiency'] ?? 65) / 100;
        $targetCashFlow = (float) ($kpiSettings['kpi_target_cash_flow'] ?? 100000);
        $targetAccountsReceivable = (float) ($kpiSettings['kpi_target_accounts_receivable'] ?? 50000);
        $targetDebtRatio = (float) ($kpiSettings['kpi_target_debt_ratio'] ?? 40) / 100;
        $targetRevenuePerEmployee = (float) ($kpiSettings['kpi_target_revenue_per_employee'] ?? 30000);
        $targetDaysSalesOutstanding = (float) ($kpiSettings['kpi_target_days_sales_outstanding'] ?? 30);
        $targetBookingEfficiency = (float) ($kpiSettings['kpi_target_booking_efficiency'] ?? 85) / 100;

        $targetROI = (float) ($kpiSettings['kpi_target_roi'] ?? 20) / 100;
        
        // Mock data for additional KPI metrics
        $cashFlow = $netProfit + 15000; // Adjusted for non-cash expenses
        $accountsReceivable = 45000;
        $debtRatio = 0.35;
        $revenuePerEmployee = $revenue / 2; // Assuming 2 employees
        $daysSalesOutstanding = 25;

        $roi = $netProfit > 0 && $expenses > 0 ? $netProfit / $expenses : 0;
        
        // Bookings and revenue data for the charts
        $totalExpenseCount = Expense::whereBetween('expense_date', [$startDate, $endDate])->count();
        $averageExpense = $totalExpenseCount > 0 ? $expenses / $totalExpenseCount : 0;
        
        // Create an array of KPI targets for the view
        $kpiTargets = [
            'Financial' => [
                'Profit Margin' => $targetProfitMargin,
                'Cost Efficiency' => $targetCostEfficiency,
                'Cash Flow' => $targetCashFlow,
                'Accounts Receivable' => $targetAccountsReceivable,
                'Debt Ratio' => $targetDebtRatio
            ],
            'Operational' => [
                'Revenue Per Employee' => $targetRevenuePerEmployee,
                'Days Sales Outstanding' => $targetDaysSalesOutstanding,
                'Booking Efficiency' => $targetBookingEfficiency
            ],
            'Growth' => [
                'ROI on Expenses' => $targetROI
            ]
        ];
        
        return view('finance.kpis', compact(
            'revenue',
            'expenses',
            'netProfit',
            'revenueGrowth',
            'profitMargin',
            'costEfficiency',
            'revenueGrowthTrend',
            'profitMarginTrend',
            'costEfficiencyTrend',
            'avgTransactionValue',
            'avgTransactionValueTrend',
            'chartLabels',
            'chartRevenue',
            'chartExpenses',
            'chartMargins',
            'cashFlow',
            'accountsReceivable',
            'debtRatio',
            'revenuePerEmployee',
            'daysSalesOutstanding',
            'bookingEfficiency',

            'roi',
            'targetProfitMargin',
            'targetCostEfficiency',
            'targetCashFlow',
            'targetAccountsReceivable',
            'targetDebtRatio',
            'targetRevenuePerEmployee',
            'targetDaysSalesOutstanding',
            'targetBookingEfficiency',

            'targetROI',
            'kpiTargets',
            'totalExpenseCount',
            'averageExpense'
        ));
    }
    
    /**
     * Export KPI data as CSV
     */
    public function exportKpis(Request $request)
    {
        // Implementation would create a CSV download with KPI metrics
        $period = $request->input('period', 'monthly');
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="kpi_report_' . now()->format('Y-m-d') . '.csv"',
        ];
        
        $callback = function() use ($request) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['KPI Report', now()->format('F d, Y')]);
            fputcsv($file, ['KPI', 'Value', 'Target', 'Status']);
            
            // Add KPI data
            fputcsv($file, ['Revenue Growth', $request->input('revenueGrowth') . '%', '', '']);
            fputcsv($file, ['Profit Margin', $request->input('profitMargin') . '%', '25%', '']);
            fputcsv($file, ['Cost Efficiency', $request->input('costEfficiency') . '%', '65%', '']);
            fputcsv($file, ['Average Transaction Value', '₱' . number_format($request->input('avgTransactionValue'), 2), '', '']);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Update KPI target values
     */
    public function updateKpiTargets(Request $request)
    {
        $validated = $request->validate([
            'profit_margin' => 'nullable|numeric|min:0|max:100',
            'cost_efficiency' => 'nullable|numeric|min:0|max:100',
            'cash_flow' => 'nullable|numeric|min:0',
            'accounts_receivable' => 'nullable|numeric|min:0',
            'debt_ratio' => 'nullable|numeric|min:0|max:100',
            'revenue_per_employee' => 'nullable|numeric|min:0',
            'days_sales_outstanding' => 'nullable|numeric|min:0',
            'booking_efficiency' => 'nullable|numeric|min:0|max:100',
            'client_retention' => 'nullable|numeric|min:0|max:100',
            'new_client_rate' => 'nullable|numeric|min:0|max:100',
            'roi' => 'nullable|numeric|min:0|max:100',
        ]);
        
        $kpiSettings = [
            'kpi_target_profit_margin' => $request->input('profit_margin', 25),
            'kpi_target_cost_efficiency' => $request->input('cost_efficiency', 65),
            'kpi_target_cash_flow' => $request->input('cash_flow', 100000),
            'kpi_target_accounts_receivable' => $request->input('accounts_receivable', 50000),
            'kpi_target_debt_ratio' => $request->input('debt_ratio', 40),
            'kpi_target_revenue_per_employee' => $request->input('revenue_per_employee', 30000),
            'kpi_target_days_sales_outstanding' => $request->input('days_sales_outstanding', 30),
            'kpi_target_booking_efficiency' => $request->input('booking_efficiency', 85),
            'kpi_target_client_retention' => $request->input('client_retention', 80),
            'kpi_target_new_client_rate' => $request->input('new_client_rate', 15),
            'kpi_target_roi' => $request->input('roi', 20),
        ];
        
        FinanceSetting::updateSettings($kpiSettings);
        
        return redirect()->route('finance.kpis')
            ->with('success', 'KPI targets have been updated successfully.');
    }
    
    /**
     * Display financial settings page
     */
    public function settings()
    {
        // Get payment methods
        $paymentMethods = PaymentMethod::orderBy('method_name')->get();
        
        // Get expense categories
        $expenseCategories = ExpenseCategory::orderBy('name')->get();
        
        // Get settings from database
        $settings = FinanceSetting::getGroupAsArray('general');
        $taxSettings = FinanceSetting::getGroupAsArray('tax');
        $invoiceSettings = FinanceSetting::getGroupAsArray('invoice');
        
        return view('finance.settings', compact(
            'paymentMethods',
            'expenseCategories',
            'settings',
            'taxSettings',
            'invoiceSettings'
        ));
    }
    
    /**
     * Update general financial settings
     */
    public function updateGeneralSettings(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|size:3',
            'fiscal_year_start' => 'required|integer|min:1|max:12',
            'decimal_separator' => 'required|string|size:1',
            'thousand_separator' => 'required|string|size:1',
            'payment_terms' => 'required|string',
            'custom_payment_terms' => 'required_if:payment_terms,custom|nullable|integer|min:1',
            'enable_late_fees' => 'boolean',
            'late_fee_type' => 'required|in:percentage,fixed',
            'late_fee_value' => 'required|numeric|min:0',
        ]);
        
        $settings = [
            'currency' => $request->input('currency'),
            'fiscal_year_start' => $request->input('fiscal_year_start'),
            'decimal_separator' => $request->input('decimal_separator'),
            'thousand_separator' => $request->input('thousand_separator'),
            'payment_terms' => $request->input('payment_terms'),
            'custom_payment_terms' => $request->input('custom_payment_terms'),
            'enable_late_fees' => $request->has('enable_late_fees') ? '1' : '0',
            'late_fee_type' => $request->input('late_fee_type'),
            'late_fee_value' => $request->input('late_fee_value'),
        ];
        
        foreach ($settings as $key => $value) {
            FinanceSetting::setValue($key, $value);
        }
        
        return redirect()->route('finance.settings')
            ->with('success', 'Financial settings have been updated successfully.');
    }
    
    /**
     * Update tax settings
     */
    public function updateTaxSettings(Request $request)
    {
        $validated = $request->validate([
            'enable_tax' => 'boolean',
            'tax_name' => 'required|string|max:50',
            'tax_number' => 'nullable|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'tax_type' => 'required|in:exclusive,inclusive',
            'enable_multiple_tax_rates' => 'boolean',
            'tax_note' => 'nullable|string|max:255',
        ]);
        
        $settings = [
            'enable_tax' => $request->has('enable_tax') ? '1' : '0',
            'tax_name' => $request->input('tax_name'),
            'tax_number' => $request->input('tax_number'),
            'tax_rate' => $request->input('tax_rate'),
            'tax_type' => $request->input('tax_type'),
            'enable_multiple_tax_rates' => $request->has('enable_multiple_tax_rates') ? '1' : '0',
            'tax_note' => $request->input('tax_note'),
        ];
        
        foreach ($settings as $key => $value) {
            FinanceSetting::setValue($key, $value);
        }
        
        return redirect()->route('finance.settings')
            ->with('success', 'Tax settings have been updated successfully.');
    }
    
    /**
     * Update invoice settings
     */
    public function updateInvoiceSettings(Request $request)
    {
        $validated = $request->validate([
            'invoice_template' => 'required|string',
            'invoice_prefix' => 'nullable|string|max:10',
            'next_invoice_number' => 'required|integer|min:1',
            'invoice_footer' => 'nullable|string|max:500',
            'invoice_terms' => 'nullable|string|max:1000',
            'email_invoice_automatically' => 'boolean',
        ]);
        
        $settings = [
            'invoice_template' => $request->input('invoice_template'),
            'invoice_prefix' => $request->input('invoice_prefix'),
            'next_invoice_number' => $request->input('next_invoice_number'),
            'invoice_footer' => $request->input('invoice_footer'),
            'invoice_terms' => $request->input('invoice_terms'),
            'email_invoice_automatically' => $request->has('email_invoice_automatically') ? '1' : '0',
        ];
        
        foreach ($settings as $key => $value) {
            FinanceSetting::setValue($key, $value);
        }
        
        return redirect()->route('finance.settings')
            ->with('success', 'Invoice settings have been updated successfully.');
    }
    
    /**
     * Store a new payment method
     */
    public function storePaymentMethod(Request $request)
    {
        // Validate and store new payment method
        $request->validate([
            'method_name' => 'required|string|max:50',
            'method_type' => 'required|in:online,onsite',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        PaymentMethod::create([
            'method_name' => $request->method_name,
            'method_type' => $request->method_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('finance.settings')
            ->with('success', 'Payment method has been created successfully.');
    }
    
    /**
     * Get payment method data for editing
     */
    public function editPaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return response()->json($paymentMethod);
    }
    
    /**
     * Update an existing payment method
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        // Validate and update payment method
        $paymentMethod = PaymentMethod::findOrFail($id);
        
        $request->validate([
            'method_name' => 'required|string|max:50',
            'method_type' => 'required|in:online,onsite',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        $paymentMethod->update([
            'method_name' => $request->method_name,
            'method_type' => $request->method_type,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('finance.settings')
            ->with('success', 'Payment method has been updated successfully.');
    }
    
    /**
     * Delete a payment method
     */
    public function deletePaymentMethod($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        
        return redirect()->route('finance.settings')
            ->with('success', 'Payment method has been deleted successfully.');
    }
    
    /**
     * Store a new expense category
     */
    public function storeExpenseCategory(Request $request)
    {
        // Validate and store new expense category
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'budget_tracking' => 'boolean',
            'budget' => 'nullable|numeric|min:0',
        ]);
        
        ExpenseCategory::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'budget_tracking' => $request->has('budget_tracking'),
            'budget' => $request->budget ?? 0,
        ]);
        
        return redirect()->route('finance.settings')
            ->with('success', 'Expense category has been created successfully.');
    }
    
    /**
     * Get expense category data for editing
     */
    public function editExpenseCategory($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        return response()->json($expenseCategory);
    }
    
    /**
     * Update an existing expense category
     */
    public function updateExpenseCategory(Request $request, $id)
    {
        // Validate and update expense category
        $expenseCategory = ExpenseCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:50',
            'budget_tracking' => 'boolean',
            'budget' => 'nullable|numeric|min:0',
        ]);
        
        $expenseCategory->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'budget_tracking' => $request->has('budget_tracking'),
            'budget' => $request->has('budget_tracking') ? ($request->budget ?? 0) : 0,
        ]);
        
        return redirect()->route('finance.settings')
            ->with('success', 'Expense category has been updated successfully.');
    }
    
    /**
     * Delete an expense category
     */
    public function deleteExpenseCategory($id)
    {
        $expenseCategory = ExpenseCategory::findOrFail($id);
        $expenseCategory->delete();
        
        return redirect()->route('finance.settings')
            ->with('success', 'Expense category has been deleted successfully.');
    }
}
