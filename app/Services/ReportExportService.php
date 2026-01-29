<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Models\Expense;
use App\Models\RevenueStatistic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportExportService
{
    protected $kpiService;

    public function __construct(KpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Export dashboard report based on format
     * 
     * @param string $format (pdf, excel, csv)
     * @param array $filters
     * @return mixed
     */
    public function exportDashboardReport($format, $filters = [])
    {
        // Log entry for debugging purposes
        Log::info('Starting dashboard export', ['format' => $format, 'filters' => $filters]);

        try {
            $data = $this->getDashboardReportData($filters);

            switch (strtolower($format)) {
                case 'pdf':
                    return $this->generatePDF($data, 'dashboard');
                case 'excel':
                    return $this->generateExcel($data, 'dashboard');
                case 'csv':
                    return $this->generateCSV($data, 'dashboard');
                default:
                    throw new \InvalidArgumentException('Invalid export format');
            }
        } catch (\Exception $e) {
            Log::error('exportDashboardReport failed: ' . $e->getMessage(), [
                'format' => $format,
                'filters' => $filters,
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to let controller handle the HTTP response, but after logging useful diagnostics
            throw $e;
        }
    }

    /**
     * Export payment transactions report
     * 
     * @param string $format
     * @param array $filters
     * @return mixed
     */
    public function exportPaymentReport($format, $filters = [])
    {
        // Log entry for debugging purposes
        Log::info('Starting payment export', ['format' => $format, 'filters' => $filters]);

        try {
            $data = $this->getPaymentReportData($filters);

            switch (strtolower($format)) {
                case 'pdf':
                    return $this->generatePDF($data, 'payment');
                case 'excel':
                    return $this->generateExcel($data, 'payment');
                case 'csv':
                    return $this->generateCSV($data, 'payment');
                default:
                    throw new \InvalidArgumentException('Invalid export format');
            }
        } catch (\Exception $e) {
            Log::error('exportPaymentReport failed: ' . $e->getMessage(), [
                'format' => $format,
                'filters' => $filters,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get dashboard report data
     * 
     * @param array $filters
     * @return array
     */
    protected function getDashboardReportData($filters = [])
    {
        // Get date range from filters or default to current month
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        // Ensure dates are Carbon instances
        if (!($startDate instanceof \Carbon\Carbon)) {
            $startDate = Carbon::parse($startDate);
        }
        if (!($endDate instanceof \Carbon\Carbon)) {
            $endDate = Carbon::parse($endDate);
        }

        // Get KPI data with error handling
        try {
            $kpis = $this->kpiService->getDashboardKpis();
        } catch (\Exception $e) {
            \Log::warning('Failed to get KPI data for export: ' . $e->getMessage());
            $kpis = [
                'revenue' => ['current_month' => ['net_revenue' => 0]],
            ];
        }

        // Get bookings data
        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->with(['package', 'primaryStaff'])
            ->orderBy('booking_date', 'desc')
            ->get();

        // Get payment transactions
        $transactions = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate])
            ->with('booking')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Get expenses
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->with('categoryRelation')
            ->orderBy('expense_date', 'desc')
            ->get();

        // Calculate summary statistics
        $totalRevenue = $kpis['revenue']['current_month']['net_revenue'] ?? 0;
        $totalExpenses = $expenses->sum('amount');
        
        $summary = [
            'total_revenue' => floatval($totalRevenue),
            'total_bookings' => $bookings->count(),
            'total_expenses' => floatval($totalExpenses),
            'net_profit' => floatval($totalRevenue) - floatval($totalExpenses),
            'pending_bookings' => $bookings->where('status', 'pending')->count(),
            'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
            'completed_bookings' => $bookings->where('status', 'completed')->count(),
            'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
            'paid_amount' => floatval($bookings->where('payment_status', 'paid')->sum('total_amount')),
            'unpaid_amount' => floatval($bookings->where('payment_status', 'unpaid')->sum('total_amount')),
        ];

        // Get current admin user
        $generatedBy = 'System';
        if (session('admin_id')) {
            try {
                $admin = \App\Models\Admin::find(session('admin_id'));
                if ($admin) {
                    $generatedBy = $admin->username ?? ($admin->first_name . ' ' . $admin->last_name);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get admin user for export: ' . $e->getMessage());
            }
        } elseif (auth()->check()) {
            $generatedBy = auth()->user()->username ?? auth()->user()->email ?? 'Admin';
        }

        return [
            'title' => 'Dashboard Report',
            'period' => [
                'start' => $startDate->format('M d, Y'),
                'end' => $endDate->format('M d, Y'),
            ],
            'generated_at' => now()->format('M d, Y h:i A'),
            'generated_by' => $generatedBy,
            'summary' => $summary,
            'bookings' => $bookings,
            'transactions' => $transactions,
            'expenses' => $expenses,
            'kpis' => $kpis,
        ];
    }

    /**
     * Get payment transactions report data
     * 
     * @param array $filters
     * @return array
     */
    protected function getPaymentReportData($filters = [])
    {
        $startDate = $filters['start_date'] ?? now()->startOfMonth();
        $endDate = $filters['end_date'] ?? now()->endOfMonth();

        // Ensure dates are Carbon instances
        if (!($startDate instanceof \Carbon\Carbon)) {
            $startDate = Carbon::parse($startDate);
        }
        if (!($endDate instanceof \Carbon\Carbon)) {
            $endDate = Carbon::parse($endDate);
        }

        $transactions = PaymentTransaction::whereBetween('payment_date', [$startDate, $endDate])
            ->with(['booking', 'booking.package', 'booking.client'])
            ->orderBy('payment_date', 'desc')
            ->get();

        $summary = [
            'total_transactions' => $transactions->count(),
            'total_amount' => floatval($transactions->sum('amount')),
            'gcash_transactions' => $transactions->where('payment_method', 'gcash')->count(),
            'gcash_amount' => floatval($transactions->where('payment_method', 'gcash')->sum('amount')),
            'cash_transactions' => $transactions->where('payment_method', 'onsite_cash')->count(),
            'cash_amount' => floatval($transactions->where('payment_method', 'onsite_cash')->sum('amount')),
            'card_transactions' => $transactions->where('payment_method', 'onsite_card')->count(),
            'card_amount' => floatval($transactions->where('payment_method', 'onsite_card')->sum('amount')),
            'successful_transactions' => $transactions->whereIn('xendit_status', ['SUCCEEDED', 'PAID'])->count(),
            'pending_transactions' => $transactions->where('xendit_status', 'PENDING')->count(),
        ];

        // Get current admin user
        $generatedBy = 'System';
        if (session('admin_id')) {
            try {
                $admin = \App\Models\Admin::find(session('admin_id'));
                if ($admin) {
                    $generatedBy = $admin->username ?? ($admin->first_name . ' ' . $admin->last_name);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get admin user for export: ' . $e->getMessage());
            }
        } elseif (auth()->check()) {
            $generatedBy = auth()->user()->username ?? auth()->user()->email ?? 'Admin';
        }

        return [
            'title' => 'Payment Transactions Report',
            'period' => [
                'start' => $startDate->format('M d, Y'),
                'end' => $endDate->format('M d, Y'),
            ],
            'generated_at' => now()->format('M d, Y h:i A'),
            'generated_by' => $generatedBy,
            'summary' => $summary,
            'transactions' => $transactions,
        ];
    }

    /**
     * Generate PDF report
     * 
     * @param array $data
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function generatePDF($data, $type)
    {
        $viewName = $type === 'dashboard' ? 'reports.dashboard-pdf' : 'reports.payment-pdf';
        $filename = $type . '-report-' . now()->format('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView($viewName, $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf->download($filename);
    }

    /**
     * Generate Excel report (using HTML table that can be opened in Excel)
     * 
     * @param array $data
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function generateExcel($data, $type)
    {
        $viewName = $type === 'dashboard' ? 'reports.dashboard-excel' : 'reports.payment-excel';
        $filename = $type . '-report-' . now()->format('Y-m-d') . '.xls';

        $html = view($viewName, $data)->render();

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }

    /**
     * Generate CSV report
     * 
     * @param array $data
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    protected function generateCSV($data, $type)
    {
        $filename = $type . '-report-' . now()->format('Y-m-d') . '.csv';
        
        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            if ($type === 'dashboard') {
                $this->writeDashboardCSV($file, $data);
            } else {
                $this->writePaymentCSV($file, $data);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Write dashboard data to CSV
     * 
     * @param resource $file
     * @param array $data
     */
    protected function writeDashboardCSV($file, $data)
    {
        // Header information
        fputcsv($file, ['Dashboard Report']);
        fputcsv($file, ['Period:', $data['period']['start'] . ' - ' . $data['period']['end']]);
        fputcsv($file, ['Generated:', $data['generated_at']]);
        fputcsv($file, ['Generated By:', $data['generated_by']]);
        fputcsv($file, []);

        // Summary section
        fputcsv($file, ['SUMMARY']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Total Revenue', '₱' . number_format($data['summary']['total_revenue'], 2)]);
        fputcsv($file, ['Total Bookings', $data['summary']['total_bookings']]);
        fputcsv($file, ['Total Expenses', '₱' . number_format($data['summary']['total_expenses'], 2)]);
        fputcsv($file, ['Net Profit', '₱' . number_format($data['summary']['net_profit'], 2)]);
        fputcsv($file, ['Pending Bookings', $data['summary']['pending_bookings']]);
        fputcsv($file, ['Confirmed Bookings', $data['summary']['confirmed_bookings']]);
        fputcsv($file, ['Completed Bookings', $data['summary']['completed_bookings']]);
        fputcsv($file, ['Cancelled Bookings', $data['summary']['cancelled_bookings']]);
        fputcsv($file, ['Paid Amount', '₱' . number_format($data['summary']['paid_amount'], 2)]);
        fputcsv($file, ['Unpaid Amount', '₱' . number_format($data['summary']['unpaid_amount'], 2)]);
        fputcsv($file, []);

        // Bookings section
        fputcsv($file, ['BOOKINGS']);
        fputcsv($file, ['Reference', 'Client Name', 'Package', 'Date', 'Status', 'Payment Status', 'Amount']);
        foreach ($data['bookings'] as $booking) {
            fputcsv($file, [
                $booking->booking_reference,
                $booking->client_first_name . ' ' . $booking->client_last_name,
                $booking->package->title ?? 'N/A',
                Carbon::parse($booking->booking_date)->format('M d, Y'),
                ucfirst($booking->status),
                ucfirst($booking->payment_status),
                '₱' . number_format($booking->total_amount, 2),
            ]);
        }
        fputcsv($file, []);

        // Expenses section
        fputcsv($file, ['EXPENSES']);
        fputcsv($file, ['Date', 'Category', 'Description', 'Amount']);
        foreach ($data['expenses'] as $expense) {
            fputcsv($file, [
                Carbon::parse($expense->expense_date)->format('M d, Y'),
                $expense->categoryRelation->name ?? $expense->category ?? 'N/A',
                $expense->description,
                '₱' . number_format($expense->amount, 2),
            ]);
        }
    }

    /**
     * Write payment data to CSV
     * 
     * @param resource $file
     * @param array $data
     */
    protected function writePaymentCSV($file, $data)
    {
        // Header information
        fputcsv($file, ['Payment Transactions Report']);
        fputcsv($file, ['Period:', $data['period']['start'] . ' - ' . $data['period']['end']]);
        fputcsv($file, ['Generated:', $data['generated_at']]);
        fputcsv($file, ['Generated By:', $data['generated_by']]);
        fputcsv($file, []);

        // Summary section
        fputcsv($file, ['SUMMARY']);
        fputcsv($file, ['Metric', 'Value']);
        fputcsv($file, ['Total Transactions', $data['summary']['total_transactions']]);
        fputcsv($file, ['Total Amount', '₱' . number_format($data['summary']['total_amount'], 2)]);
        fputcsv($file, ['GCash Transactions', $data['summary']['gcash_transactions']]);
        fputcsv($file, ['GCash Amount', '₱' . number_format($data['summary']['gcash_amount'], 2)]);
        fputcsv($file, ['Cash Transactions', $data['summary']['cash_transactions']]);
        fputcsv($file, ['Cash Amount', '₱' . number_format($data['summary']['cash_amount'], 2)]);
        fputcsv($file, ['Card Transactions', $data['summary']['card_transactions']]);
        fputcsv($file, ['Card Amount', '₱' . number_format($data['summary']['card_amount'], 2)]);
        fputcsv($file, ['Successful Transactions', $data['summary']['successful_transactions']]);
        fputcsv($file, ['Pending Transactions', $data['summary']['pending_transactions']]);
        fputcsv($file, []);

        // Transactions section
        fputcsv($file, ['TRANSACTIONS']);
        fputcsv($file, ['Transaction ID', 'Date', 'Booking Ref', 'Client', 'Method', 'Amount', 'Status', 'Reference']);
        foreach ($data['transactions'] as $transaction) {
            fputcsv($file, [
                $transaction->transaction_id,
                Carbon::parse($transaction->payment_date)->format('M d, Y h:i A'),
                $transaction->booking->booking_reference ?? 'N/A',
                ($transaction->booking->client_first_name ?? '') . ' ' . ($transaction->booking->client_last_name ?? ''),
                ucfirst(str_replace('_', ' ', $transaction->payment_method)),
                '₱' . number_format($transaction->amount, 2),
                $transaction->xendit_status,
                $transaction->transaction_reference ?? 'N/A',
            ]);
        }
    }
}
