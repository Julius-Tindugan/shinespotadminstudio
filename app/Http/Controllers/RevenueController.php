<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KpiService;

class RevenueController extends Controller
{
    /**
     * KPI Service instance.
     *
     * @var \App\Services\KpiService
     */
    protected $kpiService;
    
    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\KpiService  $kpiService
     * @return void
     */
    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Get revenue data for the specified period.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRevenueData(Request $request)
    {
        try {
            $period = $request->input('period', 'daily');
            $validPeriods = ['daily', 'weekly', 'monthly', 'yearly'];
            
            if (!in_array($period, $validPeriods)) {
                return response()->json([
                    'error' => 'Invalid period specified', 
                    'valid_periods' => $validPeriods
                ], 400);
            }
            
            $revenueData = $this->kpiService->getDailyRevenue($period);
            
            // Add metadata to the response
            $response = $revenueData;
            $response['meta'] = [
                'period' => $period,
                'generated_at' => now()->toIso8601String(),
                'count' => count($revenueData['data'] ?? []),
                'has_data' => !empty(array_filter($revenueData['data'] ?? [])),
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error in RevenueController::getRevenueData: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to retrieve revenue data',
                'message' => 'An unexpected error occurred while processing your request.'
            ], 500);
        }
    }
    
    /**
     * Get comparative revenue data for dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDashboardRevenueStats()
    {
        $currentPeriodStats = $this->kpiService->getRevenueStatistics();
        return response()->json($currentPeriodStats);
    }
}
