<?php

namespace App\Http\Controllers;

use App\Services\ReportExportService;
use App\Services\ActivityLoggerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportExportController extends Controller
{
    protected $exportService;
    protected $activityLogger;

    public function __construct(ReportExportService $exportService, ActivityLoggerService $activityLogger)
    {
        $this->exportService = $exportService;
        $this->activityLogger = $activityLogger;
    }

    /**
     * Export dashboard report
     * 
     * @param Request $request
     * @return mixed
     */
    public function exportDashboard(Request $request)
    {
        try {
            $validated = $request->validate([
                'format' => 'required|in:pdf,excel,csv',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $format = $validated['format'];
            $filters = [
                'start_date' => $request->start_date ?? now()->startOfMonth(),
                'end_date' => $request->end_date ?? now()->endOfMonth(),
            ];

            // Log the export activity
            $this->activityLogger->log(
                'export_report',
                null, // model
                null, // old values
                [
                    'format' => $format,
                    'start_date' => $filters['start_date'],
                    'end_date' => $filters['end_date'],
                ], // new values
                'Exported dashboard report in ' . strtoupper($format) . ' format' // description
            );

            return $this->exportService->exportDashboardReport($format, $filters);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid export parameters',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Dashboard export failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export payment transactions report
     * 
     * @param Request $request
     * @return mixed
     */
    public function exportPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'format' => 'required|in:pdf,excel,csv',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            $format = $validated['format'];
            $filters = [
                'start_date' => $request->start_date ?? now()->startOfMonth(),
                'end_date' => $request->end_date ?? now()->endOfMonth(),
            ];

            // Log the export activity
            $this->activityLogger->log(
                'export_report',
                null, // model
                null, // old values
                [
                    'format' => $format,
                    'start_date' => $filters['start_date'],
                    'end_date' => $filters['end_date'],
                ], // new values
                'Exported payment report in ' . strtoupper($format) . ' format' // description
            );

            return $this->exportService->exportPaymentReport($format, $filters);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid export parameters',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment export failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to export report: ' . $e->getMessage()
            ], 500);
        }
    }
}
