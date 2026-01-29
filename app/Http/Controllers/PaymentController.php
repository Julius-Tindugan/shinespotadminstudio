<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.package']);
        
        // Apply filters
        
        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'completed':
                    // For completed: onsite payments are always completed, or GCash with SUCCEEDED/PAID/SETTLED status
                    $query->where(function($q) {
                        $q->whereIn('payment_method', ['onsite_cash', 'onsite_card'])
                          ->orWhere(function($q2) {
                              $q2->where('payment_method', 'gcash')
                                 ->whereIn('xendit_status', ['SUCCEEDED', 'PAID', 'SETTLED']);
                          });
                    });
                    break;
                case 'pending':
                    // For pending: GCash payments that are not completed or failed
                    $query->where('payment_method', 'gcash')
                          ->where(function($q) {
                              $q->whereNull('xendit_status')
                                ->orWhereIn('xendit_status', ['PENDING', 'PENDING_PAYMENT'])
                                ->orWhereNotIn('xendit_status', ['SUCCEEDED', 'PAID', 'SETTLED', 'FAILED', 'EXPIRED']);
                          });
                    break;
                case 'failed':
                    // For failed: GCash payments with FAILED or EXPIRED status
                    $query->where('payment_method', 'gcash')
                          ->whereIn('xendit_status', ['FAILED', 'EXPIRED']);
                    break;
            }
        }
        
        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Date filters
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('booking', function($q2) use ($search) {
                    $q2->where('booking_reference', 'like', "%{$search}%")
                       ->orWhere('client_first_name', 'like', "%{$search}%")
                       ->orWhere('client_last_name', 'like', "%{$search}%")
                       ->orWhere('client_email', 'like', "%{$search}%");
                })
                ->orWhere('transaction_reference', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        $payments = $query->orderBy('payment_date', 'desc')
            ->paginate(10)
            ->appends($request->except('page'));
        
        // Get available payment methods from the enum values
        $paymentMethods = [
            ['value' => 'gcash', 'label' => 'GCash (Online)'],
            ['value' => 'onsite_cash', 'label' => 'Cash (Onsite)'],
            ['value' => 'onsite_card', 'label' => 'Card (Onsite)']
        ];
        
        // Calculate payment summary statistics using the completed scope
        $totalCompletedPayments = Payment::completed()->sum('amount');
        
        // Get unpaid bookings total
        $totalPendingPayments = Booking::where('payment_status', 'unpaid')->sum('total_amount');
        
        // For failed payments, check xendit_status = 'FAILED' or 'EXPIRED' for gcash payments
        $totalFailedPayments = Payment::where('payment_method', 'gcash')
            ->whereIn('xendit_status', ['FAILED', 'EXPIRED'])
            ->sum('amount');
        
        // Calculate average payment amount (only for completed payments)
        $completedPaymentsCount = Payment::completed()->count();
        $averagePayment = $completedPaymentsCount > 0 
            ? $totalCompletedPayments / $completedPaymentsCount 
            : 0;
        
        return view('finance.payments.index', compact(
            'payments', 
            'paymentMethods', 
            'totalCompletedPayments', 
            'totalPendingPayments',
            'totalFailedPayments',
            'averagePayment'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->get();
        
        // Check if payment integration is enabled
        $paymentIntegrationEnabled = \App\Models\SystemSetting::getValue('payment_integration_enabled', false);
        
        // Get available payment methods from the enum values
        $paymentMethods = [
            ['value' => 'onsite_cash', 'label' => 'Cash (Onsite)'],
            ['value' => 'onsite_card', 'label' => 'Card (Onsite)']
        ];
        
        // Only add GCash if payment integration is enabled
        if ($paymentIntegrationEnabled) {
            array_unshift($paymentMethods, ['value' => 'gcash', 'label' => 'GCash (Online)']);
        }
        
        return view('finance.payments.create', compact('bookings', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:gcash,onsite_cash,onsite_card',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Validate payment amount doesn't exceed remaining balance
        $booking = Booking::findOrFail($request->booking_id);
        $totalPaid = $booking->paymentTransactions()
            ->where('xendit_status', 'PAID')
            ->orWhereIn('payment_method', ['onsite_cash', 'onsite_card'])
            ->sum('amount');
        
        $remainingBalance = $booking->total_amount - $totalPaid;
        
        if ($request->amount > $remainingBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', sprintf(
                    'Payment amount (₱%s) exceeds remaining balance (₱%s). Total booking amount: ₱%s, Already paid: ₱%s',
                    number_format($request->amount, 2),
                    number_format($remainingBalance, 2),
                    number_format($booking->total_amount, 2),
                    number_format($totalPaid, 2)
                ));
        }
        
        // Handle GCash payments differently - they need Xendit integration
        if ($request->payment_method === 'gcash') {
            return $this->processGCashPayment($request);
        }
        
        // Use database transaction to ensure data consistency for onsite payments
        DB::beginTransaction();
        
        try {
            $payment = new Payment([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'notes' => $request->notes,
                'processed_by' => auth()->id(),
                'xendit_status' => 'PAID', // Onsite payments are immediately completed
            ]);
            
            $payment->save();
            
            // Refresh booking from database and update payment status if needed
            $booking = Booking::lockForUpdate()->findOrFail($request->booking_id);
            
            // Calculate total paid including this new payment
            $totalPaid = Payment::where('booking_id', $booking->booking_id)
                ->completed()
                ->sum('amount');
            
            \Log::info('Payment added - checking booking status', [
                'booking_id' => $booking->booking_id,
                'total_amount' => $booking->total_amount,
                'total_paid' => $totalPaid,
                'current_payment_status' => $booking->payment_status,
                'current_booking_status' => $booking->status
            ]);
            
            // Update payment status if total paid amount meets or exceeds booking amount
            if ($totalPaid >= $booking->total_amount) {
                if ($booking->payment_status !== 'paid') {
                    $booking->payment_status = 'paid';
                    \Log::info('Updated payment_status to paid', ['booking_id' => $booking->booking_id]);
                }
                
                // Auto-confirm booking if fully paid and currently pending
                if ($booking->status === 'pending') {
                    $booking->status = 'confirmed';
                    \Log::info('Auto-confirmed booking after full payment', [
                        'booking_id' => $booking->booking_id,
                        'payment_method' => $request->payment_method
                    ]);
                }
                
                $booking->save();
            } elseif ($totalPaid < $booking->total_amount && $booking->payment_status === 'paid') {
                // Revert to unpaid if total paid is less than booking amount (partial payment removed)
                $booking->payment_status = 'unpaid';
                $booking->save();
                \Log::info('Reverted payment_status to unpaid - partial payment', ['booking_id' => $booking->booking_id]);
            }
            
            DB::commit();
            
            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment recorded successfully. Booking status updated.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error recording payment', [
                'error' => $e->getMessage(),
                'booking_id' => $request->booking_id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record payment. Please try again.');
        }
    }
    
    /**
     * Process GCash payment via Xendit integration.
     */
    protected function processGCashPayment(Request $request)
    {
        try {
            $booking = Booking::findOrFail($request->booking_id);
            
            // Validate payment amount doesn't exceed remaining balance
            $totalPaid = $booking->paymentTransactions()
                ->where('xendit_status', 'PAID')
                ->orWhereIn('payment_method', ['onsite_cash', 'onsite_card'])
                ->sum('amount');
            
            $remainingBalance = $booking->total_amount - $totalPaid;
            
            if ($request->amount > $remainingBalance) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', sprintf(
                        'Payment amount (₱%s) exceeds remaining balance (₱%s). Total booking amount: ₱%s, Already paid: ₱%s',
                        number_format($request->amount, 2),
                        number_format($remainingBalance, 2),
                        number_format($booking->total_amount, 2),
                        number_format($totalPaid, 2)
                    ));
            }
            
            // Integrate with Xendit API to create payment request
            $xenditService = app(\App\Services\XenditService::class);
            
            // Generate unique reference ID for this transaction
            $referenceId = 'GCASH-' . now()->format('YmdHis') . '-' . $request->booking_id;
            
            // Create Xendit invoice
            $paymentData = $xenditService->createEWalletPayment([
                'reference_id' => $referenceId,
                'amount' => $request->amount,
                'description' => 'Shine Spot Studio - Booking #' . $booking->booking_reference,
                'customer_email' => $booking->client_email ?? 'customer@shinespot.com',
                'customer_name' => $booking->client_first_name . ' ' . $booking->client_last_name,
            ]);
            
            // Check if payment creation was successful
            if (!$paymentData['success']) {
                \Log::error('Xendit payment creation failed', [
                    'booking_id' => $request->booking_id,
                    'error' => $paymentData['message'] ?? 'Unknown error'
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', $paymentData['message'] ?? 'Failed to create payment link. Please try again.');
            }
            
            // Extract payment details from Xendit response
            $xenditPaymentId = $paymentData['data']['id'];
            $paymentUrl = $paymentData['data']['invoice_url'] ?? $paymentData['data']['actions'][0]['url'] ?? null;
            
            if (!$paymentUrl) {
                \Log::error('No payment URL returned from Xendit', [
                    'booking_id' => $request->booking_id,
                    'payment_data' => $paymentData
                ]);
                
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to generate payment link. Please contact support.');
            }
            
            \Log::info('Xendit payment created successfully via PaymentController', [
                'booking_id' => $request->booking_id,
                'xendit_payment_id' => $xenditPaymentId,
                'payment_url' => $paymentUrl,
                'reference_id' => $referenceId
            ]);
            
            // Create payment record with PENDING status
            $payment = new Payment([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'payment_date' => now(),
                'payment_method' => 'gcash',
                'transaction_reference' => $referenceId,
                'notes' => $request->notes,
                'processed_by' => auth()->id(),
                'xendit_payment_id' => $xenditPaymentId,
                'xendit_status' => 'PENDING', // Will be updated by webhook
            ]);
            
            $payment->save();
            
            // Refresh model to get auto-incremented ID
            $payment->refresh();
            
            \Log::info('GCash payment record created', [
                'payment_id' => $payment->transaction_id,
                'booking_id' => $booking->booking_id,
                'amount' => $request->amount,
                'xendit_payment_id' => $xenditPaymentId
            ]);
            
            // Redirect to a payment redirect page with the payment URL
            return redirect()->route('finance.payments.gcash-redirect', ['payment_id' => $payment->transaction_id])
                ->with('payment_url', $paymentUrl)
                ->with('success', 'GCash payment initiated. Please complete the payment to finalize.');
                
        } catch (\Exception $e) {
            \Log::error('Failed to process GCash payment via PaymentController', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to process GCash payment: ' . $e->getMessage());
        }
    }
    
    /**
     * Show GCash payment redirect page.
     */
    public function gcashRedirect($paymentId)
    {
        $payment = Payment::with('booking')->findOrFail($paymentId);
        $paymentUrl = session('payment_url');
        
        if (!$paymentUrl) {
            \Log::error('No payment URL in session for GCash redirect', [
                'payment_id' => $paymentId
            ]);
            
            return redirect()->route('finance.payments.index')
                ->with('error', 'Payment URL not found. Please try again.');
        }
        
        return view('finance.payments.gcash-redirect', compact('payment', 'paymentUrl'));
    }

    /**
     * Check single payment status (for AJAX polling).
     */
    public function checkPaymentStatus($paymentId)
    {
        try {
            $payment = Payment::with('booking:booking_id,payment_status')->findOrFail($paymentId);
            
            return response()->json([
                'success' => true,
                'payment_id' => $payment->transaction_id,
                'status' => $payment->status,
                'xendit_status' => $payment->xendit_status,
                'payment_method' => $payment->payment_method,
                'booking_payment_status' => $payment->booking ? $payment->booking->payment_status : null,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date ? $payment->payment_date->toISOString() : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $payment = Payment::with(['booking.package'])
            ->findOrFail($id);
        
        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $payment = Payment::findOrFail($id);
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->get();
        
        // Check if payment integration is enabled
        $paymentIntegrationEnabled = \App\Models\SystemSetting::getValue('payment_integration_enabled', false);
        
        // Get available payment methods from the enum values
        $paymentMethods = [
            ['value' => 'onsite_cash', 'label' => 'Cash (Onsite)'],
            ['value' => 'onsite_card', 'label' => 'Card (Onsite)']
        ];
        
        // Only add GCash if payment integration is enabled
        if ($paymentIntegrationEnabled) {
            array_unshift($paymentMethods, ['value' => 'gcash', 'label' => 'GCash (Online)']);
        }
        
        return view('finance.payments.edit', compact('payment', 'bookings', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:gcash,onsite_cash,onsite_card',
            'transaction_reference' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Use database transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            $payment = Payment::findOrFail($id);
            $oldBookingId = $payment->booking_id;
            
            $payment->update([
                'booking_id' => $request->booking_id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'notes' => $request->notes,
            ]);
            
            // Update old booking if booking was changed
            if ($oldBookingId != $request->booking_id) {
                $oldBooking = Booking::lockForUpdate()->find($oldBookingId);
                if ($oldBooking) {
                    $oldTotalPaid = Payment::where('booking_id', $oldBooking->booking_id)
                        ->completed()
                        ->sum('amount');
                    
                    if ($oldTotalPaid < $oldBooking->total_amount && $oldBooking->payment_status === 'paid') {
                        $oldBooking->payment_status = 'unpaid';
                        $oldBooking->save();
                        \Log::info('Updated old booking payment_status to unpaid', ['booking_id' => $oldBooking->booking_id]);
                    }
                }
            }
            
            // Update current booking payment status
            $booking = Booking::lockForUpdate()->findOrFail($request->booking_id);
            $totalPaid = Payment::where('booking_id', $booking->booking_id)
                ->completed()
                ->sum('amount');
            
            \Log::info('Payment updated - checking booking status', [
                'booking_id' => $booking->booking_id,
                'total_amount' => $booking->total_amount,
                'total_paid' => $totalPaid,
                'current_payment_status' => $booking->payment_status,
                'current_booking_status' => $booking->status
            ]);
                
            if ($totalPaid >= $booking->total_amount) {
                if ($booking->payment_status !== 'paid') {
                    $booking->payment_status = 'paid';
                    \Log::info('Updated payment_status to paid', ['booking_id' => $booking->booking_id]);
                }
                
                // Auto-confirm booking if fully paid and currently pending
                if ($booking->status === 'pending') {
                    $booking->status = 'confirmed';
                    \Log::info('Auto-confirmed booking after payment update', [
                        'booking_id' => $booking->booking_id,
                        'payment_id' => $id
                    ]);
                }
                
                $booking->save();
            } elseif ($totalPaid < $booking->total_amount && $booking->payment_status === 'paid') {
                $booking->payment_status = 'unpaid';
                $booking->save();
                \Log::info('Reverted payment_status to unpaid - partial payment', ['booking_id' => $booking->booking_id]);
            }
            
            DB::commit();
            
            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment updated successfully. Booking status updated.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating payment', [
                'error' => $e->getMessage(),
                'payment_id' => $id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update payment. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        
        try {
            $payment = Payment::findOrFail($id);
            
            // Store booking_id before deleting payment
            $booking_id = $payment->booking_id;
            
            // Delete the payment
            $payment->delete();
            
            // Check if booking payment status needs to be updated
            $booking = Booking::lockForUpdate()->find($booking_id);
            if ($booking) {
                $totalPaid = Payment::where('booking_id', $booking->booking_id)
                    ->completed()
                    ->sum('amount');
                
                \Log::info('Payment deleted - checking booking status', [
                    'booking_id' => $booking->booking_id,
                    'total_amount' => $booking->total_amount,
                    'total_paid' => $totalPaid,
                    'current_payment_status' => $booking->payment_status
                ]);
                    
                // Update payment status based on total paid
                if ($totalPaid >= $booking->total_amount && $booking->payment_status !== 'paid') {
                    $booking->payment_status = 'paid';
                    $booking->save();
                    \Log::info('Updated payment_status to paid after delete', ['booking_id' => $booking->booking_id]);
                } elseif ($totalPaid < $booking->total_amount && $booking->payment_status === 'paid') {
                    $booking->payment_status = 'unpaid';
                    $booking->save();
                    \Log::info('Reverted payment_status to unpaid after delete', ['booking_id' => $booking->booking_id]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('finance.payments.index')
                ->with('success', 'Payment deleted successfully. Booking status updated.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting payment', [
                'error' => $e->getMessage(),
                'payment_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete payment. Please try again.');
        }
    }
    
    /**
     * Get payment details for a specific booking
     */
    public function getBookingPayments(string $booking_id)
    {
        $payments = Payment::where('booking_id', $booking_id)
            ->orderBy('payment_date', 'desc')
            ->get();
            
        $booking = Booking::findOrFail($booking_id);
        
        $totalPaid = Payment::where('booking_id', $booking_id)->completed()->sum('amount');
        $remaining = $booking->total_amount - $totalPaid;
        
        return response()->json([
            'payments' => $payments,
            'booking' => $booking,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining,
        ]);
    }
    
    /**
     * Get payment methods list for API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentMethodsList()
    {
        // Check if payment integration is enabled
        $paymentIntegrationEnabled = \App\Models\SystemSetting::getValue('payment_integration_enabled', false);
        
        // Build payment methods array based on settings
        $paymentMethods = [
            ['value' => 'onsite_cash', 'label' => 'Cash (Onsite)', 'type' => 'onsite'],
            ['value' => 'onsite_card', 'label' => 'Card (Onsite)', 'type' => 'onsite']
        ];
        
        // Only add GCash if payment integration is enabled
        if ($paymentIntegrationEnabled) {
            array_unshift($paymentMethods, ['value' => 'gcash', 'label' => 'GCash (Online)', 'type' => 'online']);
        }
            
        return response()->json($paymentMethods);
    }
    
    /**
     * Check payment statuses for AJAX updates.
     * Returns current status for each payment based on booking payment_status.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatuses(Request $request)
    {
        try {
            $bookingIds = $request->input('booking_ids', []);
            
            if (empty($bookingIds)) {
                return response()->json(['payments' => []]);
            }
            
            // Get all payments for these bookings with their current booking payment_status
            $payments = Payment::with('booking:booking_id,payment_status')
                ->whereIn('booking_id', $bookingIds)
                ->get();
            
            $paymentStatuses = [];
            
            foreach ($payments as $payment) {
                // Use the Payment model's getStatusAttribute() method
                $paymentStatuses[$payment->transaction_id] = [
                    'status' => $payment->status,
                    'booking_payment_status' => $payment->booking ? $payment->booking->payment_status : 'unknown',
                    'xendit_status' => $payment->xendit_status,
                    'payment_method' => $payment->payment_method
                ];
            }
            
            return response()->json([
                'success' => true,
                'payments' => $paymentStatuses
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error checking payment statuses', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment statuses'
            ], 500);
        }
    }
    
    /**
     * Manually sync payment status from Xendit API.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function syncFromXendit($id)
    {
        try {
            $payment = Payment::findOrFail($id);
            
            if ($payment->payment_method !== 'gcash') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only GCash payments can be synced from Xendit'
                ], 400);
            }
            
            if (empty($payment->xendit_payment_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment has no Xendit payment ID'
                ], 400);
            }
            
            $paymentMonitoringService = app(\App\Services\PaymentMonitoringService::class);
            $success = $paymentMonitoringService->syncPaymentStatusFromXendit($id);
            
            if ($success) {
                // Refresh payment to get updated status
                $payment->refresh();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Payment status synced successfully',
                    'payment' => [
                        'xendit_status' => $payment->xendit_status,
                        'status' => $payment->status,
                        'booking_payment_status' => $payment->booking ? $payment->booking->payment_status : null
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync payment status. Please try again.'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Error syncing payment from Xendit', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync all recent pending GCash payments from Xendit.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncRecentPendingPayments()
    {
        try {
            // Check if we've synced recently (within last 2 minutes) to avoid repeated API calls
            $lastSync = cache('last_payment_sync_time');
            if ($lastSync && now()->diffInSeconds($lastSync) < 120) {
                return response()->json([
                    'success' => true,
                    'message' => 'Recently synced - skipping to avoid rate limits',
                    'synced_count' => 0,
                    'paid_count' => 0
                ]);
            }

            // Find recent pending GCash payments (last 1 hour only, reduced from 2 hours)
            $recentPendingPayments = Payment::where('payment_method', 'gcash')
                ->where(function($query) {
                    $query->whereIn('xendit_status', ['PENDING', 'PENDING_PAYMENT'])
                          ->orWhereNull('xendit_status');
                })
                ->whereNotNull('xendit_payment_id')
                ->where('created_at', '>=', now()->subHour()) // Changed from 2 hours to 1 hour
                ->orderBy('created_at', 'desc')
                ->take(5) // Reduced from 10 to 5 for faster execution
                ->get();

            if ($recentPendingPayments->isEmpty()) {
                // Cache the sync time even if no payments found
                cache(['last_payment_sync_time' => now()], now()->addMinutes(2));
                
                return response()->json([
                    'success' => true,
                    'message' => 'No recent pending payments to sync',
                    'synced_count' => 0,
                    'paid_count' => 0
                ]);
            }

            \Log::info('Syncing recent pending payments', [
                'count' => $recentPendingPayments->count(),
                'payment_ids' => $recentPendingPayments->pluck('transaction_id')->toArray()
            ]);

            $syncedCount = 0;
            $paidCount = 0;
            $paymentMonitoringService = app(\App\Services\PaymentMonitoringService::class);

            foreach ($recentPendingPayments as $payment) {
                try {
                    if ($paymentMonitoringService->syncPaymentStatusFromXendit($payment->transaction_id)) {
                        $syncedCount++;
                        
                        // Refresh to check if it's now paid
                        $payment->refresh();
                        if (in_array($payment->xendit_status, ['PAID', 'SETTLED', 'SUCCEEDED'])) {
                            $paidCount++;
                        }
                    }
                } catch (\Exception $e) {
                    // Log but continue with other payments
                    \Log::warning('Failed to sync individual payment', [
                        'payment_id' => $payment->transaction_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Cache the sync time
            cache(['last_payment_sync_time' => now()], now()->addMinutes(2));

            return response()->json([
                'success' => true,
                'message' => "Synced {$syncedCount} payments, {$paidCount} now paid",
                'synced_count' => $syncedCount,
                'paid_count' => $paidCount
            ]);

        } catch (\Exception $e) {
            \Log::error('Error syncing recent pending payments', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error syncing payments: ' . $e->getMessage()
            ], 500);
        }
    }
}
