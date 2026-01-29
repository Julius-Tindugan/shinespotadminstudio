<?php

namespace App\Http\Controllers;

use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ActivityLogController extends Controller
{
    protected $activityLogger;

    public function __construct(ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Display the activity logs dashboard.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['user_type', 'action', 'model_type', 'start_date', 'end_date', 'search']);
        
        $logs = $this->activityLogger->getFilteredActivity($filters, 15);
        
        // Get available filter options
        $userTypes = ['admin', 'staff'];
        $actions = ['created', 'updated', 'deleted', 'login', 'logout', 'viewed', 'failed_login'];
        
        // Get available model types from the database
        $modelTypes = \App\Models\ActivityLog::whereNotNull('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(function($type) {
                return [
                    'value' => $type,
                    'label' => class_basename($type)
                ];
            })
            ->sortBy('label')
            ->values();
        
        return view('activity-logs.index', compact('logs', 'filters', 'userTypes', 'actions', 'modelTypes'));
    }

    /**
     * Get recent activity logs as JSON (for AJAX requests).
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $logs = $this->activityLogger->getRecentActivity($limit);
        
        $formattedLogs = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'user_name' => $log->user_name,
                'user_type' => $log->user_type,
                'action' => $log->action,
                'description' => $log->action_description,
                'created_at' => $log->created_at->format('M d, Y H:i'),
                'created_at_human' => $log->created_at->diffForHumans(),
                'model_type' => $log->model_type ? class_basename($log->model_type) : null,
            ];
        });
        
        return response()->json($formattedLogs);
    }

    /**
     * Show detailed view of a specific activity log.
     */
    public function show($id): View
    {
        $log = \App\Models\ActivityLog::findOrFail($id);
        
        return view('activity-logs.show', compact('log'));
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['user_type', 'action', 'start_date', 'end_date', 'search']);
        
        // Get all logs without pagination for export
        $logs = $this->activityLogger->getFilteredActivity($filters, 10000);
        
        $filename = 'activity_logs_' . now()->format('Y_m_d_H_i_s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Date & Time',
                'User Type',
                'User Name',
                'Action',
                'Description',
                'Model Type',
                'Model ID',
                'IP Address',
                'URL'
            ]);
            
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user_type,
                    $log->user_name,
                    $log->action,
                    $log->action_description,
                    $log->model_type ? class_basename($log->model_type) : '',
                    $log->model_id,
                    $log->ip_address,
                    $log->url
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get activity statistics for dashboard widgets.
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'today_total' => \App\Models\ActivityLog::whereDate('created_at', today())->count(),
            'week_total' => \App\Models\ActivityLog::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'month_total' => \App\Models\ActivityLog::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'top_actions' => \App\Models\ActivityLog::selectRaw('action, COUNT(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('action')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'user_activity' => \App\Models\ActivityLog::selectRaw('user_type, COUNT(*) as count')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->groupBy('user_type')
                ->get(),
        ];
        
        return response()->json($stats);
    }
}
