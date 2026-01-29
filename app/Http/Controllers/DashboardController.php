<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Staff;
use App\Services\KpiService;
use App\Services\ActivityLoggerService;

class DashboardController extends Controller
{
    /**
     * KPI Service instance.
     *
     * @var \App\Services\KpiService
     */
    protected $kpiService;
    
    /**
     * Activity Logger Service instance.
     *
     * @var \App\Services\ActivityLoggerService
     */
    protected $activityLogger;
    
    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\KpiService  $kpiService
     * @param  \App\Services\ActivityLoggerService  $activityLogger
     * @return void
     */
    public function __construct(KpiService $kpiService, ActivityLoggerService $activityLogger)
    {
        $this->kpiService = $kpiService;
        $this->activityLogger = $activityLogger;
    }

    /**
     * Show the dashboard with appropriate data based on user type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $userData = [];
        $userType = Session::get('user_type');
        
        if ($userType === 'admin') {
            // Load admin data
            $adminId = Session::get('admin_id');
            $admin = Admin::with('roles')->find($adminId);
            
            if (!$admin) {
                // Invalid session data, log them out
                return redirect()->route('logout');
            }
            
            $userData = [
                'id' => $admin->admin_id,
                'name' => $admin->username,
                'email' => $admin->email,
                'roles' => $admin->roles->pluck('role_name')->toArray(),
                'type' => 'admin',
                'has_finance_access' => true // Admins always have finance access
            ];
        } elseif ($userType === 'staff') {
            // Load staff data
            $staffId = Session::get('staff_id');
            $staff = Staff::with('roles')->find($staffId);
            
            if (!$staff) {
                // Invalid session data, log them out
                return redirect()->route('logout');
            }
            
            $userData = [
                'id' => $staff->staff_id,
                'name' => $staff->first_name . ' ' . $staff->last_name,
                'email' => $staff->email,
                'roles' => $staff->roles->pluck('role_name')->toArray(),
                'type' => 'staff',
                'has_finance_access' => false // Staff users do NOT have finance access (Admin only)
            ];
        } else {
            // No valid user type in session
            return redirect()->route('logout');
        }
        
        try {
            // Get all KPI statistics
            $kpis = $this->kpiService->getDashboardKpis();
            
            // Check if KPI service returned an error
            if (isset($kpis['error'])) {
                \Log::error('KpiService returned error: ' . $kpis['message']);
                throw new \Exception('KPI service error: ' . $kpis['message']);
            }
            
            // Validate that we have the expected structure
            if (!isset($kpis['revenue']['current'])) {
                \Log::error('KPI data missing revenue.current key. Structure: ' . json_encode(array_keys($kpis)));
                throw new \Exception('Invalid KPI data structure: missing revenue.current');
            }
            
            // Add revenue statistics to daily revenue data
            if (!isset($kpis['dailyRevenue']['stats'])) {
                $dailyRevenueData = $kpis['dailyRevenue']['data'] ?? [];
                $totalRevenue = array_sum($dailyRevenueData);
                $avgRevenue = count($dailyRevenueData) > 0 ? $totalRevenue / count($dailyRevenueData) : 0;
                $maxRevenue = count($dailyRevenueData) > 0 ? max($dailyRevenueData) : 0;
                $minRevenue = count($dailyRevenueData) > 0 ? min(array_filter($dailyRevenueData)) : 0;
                
                $kpis['dailyRevenue']['stats'] = [
                    'total' => $totalRevenue,
                    'average' => $avgRevenue,
                    'max' => $maxRevenue,
                    'min' => $minRevenue ?: 0,
                    'period' => 'daily'
                ];
            }
            
            // Add quick finance summary for users with finance access
            if ($userData['has_finance_access'] ?? false) {
                $kpis['finance_summary'] = $this->getQuickFinanceSummary();
            }
            
            // Get recent activity logs for admin users only
            $recentActivities = [];
            $activityStats = [];
            if ($userType === 'admin') {
                $recentActivities = $this->activityLogger->getRecentActivity(10);
                $activityStats = $this->getActivityStats();
            }
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in DashboardController: ' . $e->getMessage());
            
            // Provide empty KPI data to avoid template errors
            $kpis = [
                'revenue' => [
                    'current' => 0,
                    'previous' => 0,
                    'formattedCurrent' => '0.00',
                    'change' => 0,
                    'trending' => 'up'
                ],
                'bookings' => [
                    'currentMonth' => 0,
                    'previousMonth' => 0,
                    'currentWeek' => 0,
                    'previousWeek' => 0,
                    'monthlyChange' => 0,
                    'weeklyChange' => 0,
                    'monthlyTrending' => 'up',
                    'weeklyTrending' => 'up'
                ],
                'upcomingBookings' => [],
                'dailyRevenue' => [
                    'labels' => [],
                    'data' => [],
                    'stats' => [
                        'total' => 0,
                        'average' => 0,
                        'max' => 0,
                        'min' => 0,
                        'period' => 'daily'
                    ]
                ]
            ];
        }
        
        // Return the dashboard view with user data and KPI statistics
        return view('dashboard', [
            'user' => $userData,
            'kpis' => $kpis,
            'recentActivities' => $recentActivities ?? [],
            'activityStats' => $activityStats ?? []
        ]);
    }
    
    /**
     * Get quick finance summary for dashboard card
     *
     * @return array
     */
    private function getQuickFinanceSummary()
    {
        try {
            $currentMonth = now()->month;
            $currentYear = now()->year;
            
            // Get monthly revenue using completed payments
            $monthlyRevenue = \App\Models\Payment::whereMonth('payment_date', $currentMonth)
                ->whereYear('payment_date', $currentYear)
                ->completed()
                ->sum('amount');
            
            // Get monthly expenses
            $monthlyExpenses = \App\Models\Expense::whereMonth('expense_date', $currentMonth)
                ->whereYear('expense_date', $currentYear)
                ->sum('amount');
            
            // Calculate net profit
            $netProfit = $monthlyRevenue - $monthlyExpenses;
            
            // Get pending payments (unpaid bookings)
            $pendingPayments = \App\Models\Booking::where('payment_status', 'unpaid')
                ->sum('total_amount');
            
            return [
                'monthly_revenue' => $monthlyRevenue,
                'monthly_expenses' => $monthlyExpenses,
                'net_profit' => $netProfit,
                'pending_payments' => $pendingPayments,
                'profit_margin' => $monthlyRevenue > 0 ? ($netProfit / $monthlyRevenue) * 100 : 0
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting finance summary: ' . $e->getMessage());
            return [
                'monthly_revenue' => 0,
                'monthly_expenses' => 0,
                'net_profit' => 0,
                'pending_payments' => 0,
                'profit_margin' => 0
            ];
        }
    }
    
    /**
     * Get activity statistics for dashboard widget
     *
     * @return array
     */
    private function getActivityStats()
    {
        try {
            return [
                'today' => \App\Models\ActivityLog::whereDate('created_at', today())->count(),
                'this_week' => \App\Models\ActivityLog::whereBetween('created_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'critical_count' => \App\Models\ActivityLog::where('action', 'deleted')
                    ->whereDate('created_at', '>=', now()->subDays(7))
                    ->count(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting activity stats: ' . $e->getMessage());
            return [
                'today' => 0,
                'this_week' => 0,
                'critical_count' => 0
            ];
        }
    }
}
