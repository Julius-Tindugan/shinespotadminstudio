<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\PaymentTransaction;
use App\Services\PaymentMonitoringService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentLinkMail;

class PaymentTransactionController extends Controller
{
    protected $paymentMonitoringService;

    public function __construct(PaymentMonitoringService $paymentMonitoringService)
    {
        $this->paymentMonitoringService = $paymentMonitoringService;
    }

    /**
     * Display payment transactions for a booking.
     */
    public function index(Request $request, $bookingId = null)
    {
        $query = PaymentTransaction::with(['booking']);
        
        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }
        
        // Apply filters
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'successful') {
                $query->successful();
            } elseif ($request->status === 'pending') {
                $query->pending();
            } elseif ($request->status === 'failed') {
                $query->failed();
            }
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        $transactions = $query->orderBy('payment_date', 'desc')->paginate(15);
        
        // Get payment methods as enum values
        $paymentMethods = ['gcash', 'onsite_cash', 'onsite_card'];
        
        return view('finance.payments.index', compact('transactions', 'paymentMethods', 'bookingId'));
    }

    /**
     * Store a new payment transaction (for onsite payments).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'payment_method' => 'required|in:onsite_cash,onsite_card',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = $this->paymentMonitoringService->processOnsitePayment(
                $request->booking_id,
                $request->payment_method,
                $request->amount,
                [
                    'notes' => $request->notes,
                    'transaction_reference' => 'ONSITE-' . now()->format('YmdHis') . '-' . $request->booking_id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'transaction' => $transaction->load('booking')
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to record onsite payment', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process GCash payment via Xendit.
     */
    public function processGCashPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = Booking::findOrFail($request->booking_id);
            
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
                Log::error('Xendit payment creation failed', [
                    'booking_id' => $request->booking_id,
                    'error' => $paymentData['message'] ?? 'Unknown error'
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => $paymentData['message'] ?? 'Failed to create payment link. Please try again.'
                ], 500);
            }
            
            // Extract payment details from Xendit response
            $xenditPaymentId = $paymentData['data']['id'];
            $paymentUrl = $paymentData['data']['invoice_url'] ?? $paymentData['data']['actions'][0]['url'] ?? null;
            
            if (!$paymentUrl) {
                Log::error('No payment URL returned from Xendit', [
                    'booking_id' => $request->booking_id,
                    'payment_data' => $paymentData
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate payment link. Please contact support.'
                ], 500);
            }
            
            Log::info('Xendit payment created successfully', [
                'booking_id' => $request->booking_id,
                'xendit_payment_id' => $xenditPaymentId,
                'payment_url' => $paymentUrl,
                'reference_id' => $referenceId
            ]);
            
            // Store the transaction in database
            $transaction = $this->paymentMonitoringService->processGCashPayment(
                $request->booking_id,
                $request->amount,
                $xenditPaymentId,
                [
                    'transaction_reference' => $referenceId,
                    'metadata' => [
                        'payment_url' => $paymentUrl,
                        'client_name' => $booking->client_first_name . ' ' . $booking->client_last_name,
                        'client_email' => $booking->client_email,
                        'client_phone' => $booking->client_phone
                    ]
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'GCash payment initiated',
                'transaction' => $transaction,
                'payment_url' => $paymentUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process GCash payment', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process GCash payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send payment link via email.
     */
    public function sendPaymentLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,booking_id',
            'payment_url' => 'required|url',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $booking = Booking::findOrFail($request->booking_id);
            
            // Validate that booking has an email
            if (empty($booking->client_email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No email address found for this booking. Please add a client email first.'
                ], 400);
            }

            // Send the email
            Mail::to($booking->client_email)->send(
                new PaymentLinkMail($booking, $request->payment_url, $request->amount)
            );

            Log::info('Payment link email sent', [
                'booking_id' => $booking->booking_id,
                'email' => $booking->client_email,
                'payment_url' => $request->payment_url
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment link sent successfully to ' . $booking->client_email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send payment link email', [
                'booking_id' => $request->booking_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Xendit webhook for payment status updates.
     */
    public function xenditWebhook(Request $request)
    {
        // If it's a GET request, return a simple success response for testing
        if ($request->isMethod('get')) {
            Log::info('Xendit webhook GET test received');
            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook endpoint is reachable',
                'timestamp' => now()->toIso8601String()
            ], 200);
        }

        Log::info('Xendit webhook received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all()
        ]);

        try {
            // Verify webhook token from Xendit
            $callbackToken = $request->header('x-callback-token');
            $expectedToken = config('services.xendit.webhook_token') ?? env('XENDIT_WEBHOOK_TOKEN');
            
            if (empty($expectedToken)) {
                Log::error('Xendit webhook token not configured in environment');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Webhook token not configured'
                ], 500);
            }
            
            if ($callbackToken !== $expectedToken) {
                Log::warning('Xendit webhook token verification failed', [
                    'received_token' => $callbackToken ? substr($callbackToken, 0, 10) . '...' : 'null',
                    'expected_token' => substr($expectedToken, 0, 10) . '...'
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid callback token'
                ], 401);
            }
            
            Log::info('Xendit webhook token verified successfully');
            
            // Process the webhook - always returns true to acknowledge receipt
            $success = $this->paymentMonitoringService->handleXenditWebhook($request->all());
            
            // Always return 200 OK to acknowledge receipt
            // This prevents Xendit from retrying endlessly
            return response()->json([
                'status' => 'ok',
                'received' => true,
                'processed' => $success
            ], 200);

        } catch (\Throwable $e) {
            // Catch all errors including fatal errors
            Log::error('Xendit webhook processing failed with exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'webhook_data' => $request->all()
            ]);

            // Still return 200 to prevent retries
            // But indicate that processing failed
            return response()->json([
                'status' => 'error',
                'message' => 'Internal processing error',
                'received' => true,
                'processed' => false
            ], 200);
        }
    }

    /**
     * Get payment summary for a booking.
     */
    public function getPaymentSummary($bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);
            $summary = $this->paymentMonitoringService->getPaymentSummary($booking);

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund a payment transaction.
     */
    public function refund(Request $request, $transactionId)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $transaction = PaymentTransaction::findOrFail($transactionId);
            
            $success = $this->paymentMonitoringService->refundPayment(
                $transaction,
                $request->amount,
                $request->reason
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Refund processed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process refund'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process refund: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle payment callback from Xendit after customer completes/cancels payment.
     */
    public function paymentCallback(Request $request)
    {
        Log::info('Payment callback received', [
            'query_params' => $request->query(),
            'all_params' => $request->all(),
            'url' => $request->fullUrl()
        ]);

        // Xendit typically sends these parameters on redirect:
        // - external_id (for invoice)
        // - status (PAID, EXPIRED, etc.)
        // - id (payment ID)
        
        $externalId = $request->query('external_id');
        $status = $request->query('status');
        $paymentId = $request->query('id');
        
        Log::info('Extracted callback parameters', [
            'external_id' => $externalId,
            'status' => $status,
            'payment_id' => $paymentId
        ]);
        
        // If no parameters provided, log warning and try to sync most recent payment
        if (!$externalId && !$status && !$paymentId) {
            Log::warning('Payment callback received with no parameters - attempting to sync recent pending payments', [
                'url' => $request->fullUrl(),
                'all_params' => $request->all()
            ]);
            
            // Try to sync the most recent pending GCash transactions
            try {
                $recentPendingTransactions = PaymentTransaction::with('booking')
                    ->where('payment_method', 'gcash')
                    ->where(function($query) {
                        $query->whereIn('xendit_status', ['PENDING', 'PENDING_PAYMENT'])
                              ->orWhereNull('xendit_status');
                    })
                    ->whereNotNull('xendit_payment_id')
                    ->where('created_at', '>=', now()->subHours(2)) // Only last 2 hours
                    ->orderBy('created_at', 'desc')
                    ->take(5) // Check up to 5 recent transactions
                    ->get();
                
                if ($recentPendingTransactions->count() > 0) {
                    Log::info('Found recent pending transactions to sync', [
                        'count' => $recentPendingTransactions->count(),
                        'transaction_ids' => $recentPendingTransactions->pluck('transaction_id')->toArray()
                    ]);
                    
                    $syncedCount = 0;
                    $paymentMonitoringService = app(\App\Services\PaymentMonitoringService::class);
                    
                    foreach ($recentPendingTransactions as $transaction) {
                        if ($paymentMonitoringService->syncPaymentStatusFromXendit($transaction->transaction_id)) {
                            $syncedCount++;
                            
                            // Refresh transaction to see if it's now paid
                            $transaction->refresh();
                            
                            if (in_array($transaction->xendit_status, ['PAID', 'SETTLED'])) {
                                Log::info('Successfully synced and found paid transaction', [
                                    'transaction_id' => $transaction->transaction_id,
                                    'booking_id' => $transaction->booking_id,
                                    'xendit_status' => $transaction->xendit_status
                                ]);
                                
                                // If we found a paid one, show success message
                                $message = 'Payment successful! Your booking has been confirmed.';
                                $messageType = 'success';
                                
                                if (session()->has('admin_id') || session()->has('staff_id')) {
                                    return redirect()->route('bookings.index', ['from_payment' => '1'])
                                        ->with($messageType, $message);
                                }
                                
                                return view('payment.callback', [
                                    'status' => 'PAID',
                                    'message' => $message,
                                    'messageType' => $messageType
                                ]);
                            }
                        }
                    }
                    
                    Log::info('Synced pending transactions from callback', [
                        'synced_count' => $syncedCount,
                        'total_pending' => $recentPendingTransactions->count()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error syncing pending transactions from callback', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            $message = 'Payment received. Your booking status will be updated once payment is confirmed.';
            $messageType = 'info';
            
            if (session()->has('admin_id') || session()->has('staff_id')) {
                return redirect()->route('bookings.index', ['from_payment' => '1'])
                    ->with($messageType, $message)
                    ->with('warning', 'Please manually sync payment status from the Payments page if needed.');
            }
            
            return view('payment.callback', [
                'status' => null,
                'message' => $message,
                'messageType' => $messageType
            ]);
        }
        
        // Build appropriate message based on status
        $message = 'Your payment is being processed.';
        $messageType = 'info';
        
        // If payment is successful, update the transaction and booking status immediately
        if ($status === 'PAID' || $status === 'SETTLED') {
            try {
                // Find the transaction by external_id or payment_id
                $transaction = null;
                
                if ($externalId || $paymentId) {
                    $transaction = PaymentTransaction::with('booking')
                        ->where(function($query) use ($externalId, $paymentId) {
                            if ($externalId) {
                                $query->where('transaction_reference', $externalId);
                            }
                            if ($paymentId) {
                                $query->orWhere('xendit_payment_id', $paymentId);
                            }
                        })
                        ->first();
                }
                
                // Fallback: Try to find the most recent pending GCash transaction
                if (!$transaction) {
                    $transaction = PaymentTransaction::with('booking')
                        ->where('payment_method', 'gcash')
                        ->where('xendit_status', 'PENDING')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    Log::info('Using fallback: found most recent pending transaction', [
                        'transaction_id' => $transaction?->transaction_id
                    ]);
                }
                
                if ($transaction) {
                    Log::info('Found transaction to update', [
                        'transaction_id' => $transaction->transaction_id,
                        'current_status' => $transaction->xendit_status,
                        'booking_id' => $transaction->booking_id
                    ]);
                    
                    // Update transaction status if not already updated
                    if (!in_array($transaction->xendit_status, ['PAID', 'SETTLED', 'SUCCEEDED'])) {
                        $oldStatus = $transaction->xendit_status;
                        $transaction->xendit_status = strtoupper($status);
                        $transaction->payment_date = now();
                        
                        // Update xendit_payment_id if we have it and it's not set
                        if ($paymentId && !$transaction->xendit_payment_id) {
                            $transaction->xendit_payment_id = $paymentId;
                        }
                        
                        $transaction->save();
                        
                        Log::info('Transaction status updated from callback', [
                            'transaction_id' => $transaction->transaction_id,
                            'old_status' => $oldStatus,
                            'new_status' => $transaction->xendit_status
                        ]);
                    }
                    
                    // Update booking payment status
                    if ($transaction->booking) {
                        $oldPaymentStatus = $transaction->booking->payment_status;
                        $oldBookingStatus = $transaction->booking->status;
                        
                        $this->paymentMonitoringService->updateBookingPaymentStatus($transaction->booking);
                        
                        // Refresh to get updated values
                        $transaction->booking->refresh();
                        
                        Log::info('Booking status updated from callback', [
                            'booking_id' => $transaction->booking->booking_id,
                            'old_payment_status' => $oldPaymentStatus,
                            'new_payment_status' => $transaction->booking->payment_status,
                            'old_booking_status' => $oldBookingStatus,
                            'new_booking_status' => $transaction->booking->status
                        ]);
                    }
                } else {
                    Log::warning('No transaction found for callback', [
                        'external_id' => $externalId,
                        'payment_id' => $paymentId,
                        'status' => $status
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to update transaction from callback', [
                    'external_id' => $externalId,
                    'payment_id' => $paymentId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            $message = 'Payment successful! Your booking has been confirmed.';
            $messageType = 'success';
        } elseif ($status === 'EXPIRED') {
            $message = 'Payment link has expired. Please request a new payment link.';
            $messageType = 'warning';
        } elseif ($status === 'FAILED') {
            $message = 'Payment failed. Please try again or contact support.';
            $messageType = 'error';
        }
        
        // Redirect to bookings page with message
        // If user is authenticated, go to bookings index
        // Otherwise, show a generic thank you view
        
        if (session()->has('admin_id') || session()->has('staff_id')) {
            return redirect()->route('bookings.index', ['from_payment' => '1'])
                ->with($messageType, $message);
        }
        
        // For non-authenticated users (customers paying via link)
        // Create a simple thank you view
        return view('payment.callback', [
            'status' => $status,
            'message' => $message,
            'messageType' => $messageType
        ]);
    }
}
