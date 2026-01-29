<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentMonitoringService
{
    /**
     * Process a payment for a booking.
     * 
     * @param int $bookingId
     * @param string $paymentMethod
     * @param float $amount
     * @param array $options
     * @return PaymentTransaction
     */
    public function processPayment($bookingId, $paymentMethod, $amount, $options = [])
    {
        DB::beginTransaction();
        
        try {
            $booking = Booking::findOrFail($bookingId);
            
            // Create payment transaction
            $transaction = PaymentTransaction::createTransaction(
                $bookingId,
                $paymentMethod,
                $amount,
                $options
            );
            
            // Update booking payment status if payment is successful
            if ($transaction->isSuccessful()) {
                $this->updateBookingPaymentStatus($booking);
            }
            
            DB::commit();
            
            Log::info('Payment processed successfully', [
                'booking_id' => $bookingId,
                'transaction_id' => $transaction->transaction_id,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'status' => $transaction->xendit_status
            ]);
            
            return $transaction;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Payment processing failed', [
                'booking_id' => $bookingId,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Handle Xendit webhook payment status update.
     * 
     * @param array $webhookData
     * @return bool
     */
    public function handleXenditWebhook($webhookData)
    {
        try {
            // Xendit webhook test - they send empty or minimal data to test connectivity
            // Return true to pass the test
            if (empty($webhookData) || count($webhookData) === 0) {
                Log::info('Xendit webhook test received (empty payload) - returning success');
                return true;
            }
            
            // Xendit sends different event types, we need to handle them
            // Common event types: invoice.paid, invoice.expired, ewallet.capture.completed
            $eventType = $webhookData['event'] ?? $webhookData['type'] ?? null;
            
            Log::info('Processing Xendit webhook', [
                'event_type' => $eventType,
                'has_id' => isset($webhookData['id']),
                'has_external_id' => isset($webhookData['external_id']),
                'has_status' => isset($webhookData['status']),
                'status' => $webhookData['status'] ?? null,
                'data_keys' => array_keys($webhookData)
            ]);
            
            // For Invoice API webhooks
            if (isset($webhookData['external_id'])) {
                return $this->handleInvoiceWebhook($webhookData);
            }
            
            // For E-Wallet API webhooks (GCash)
            if (isset($webhookData['id']) && isset($webhookData['status'])) {
                return $this->handleEWalletWebhook($webhookData);
            }
            
            // If no recognizable data structure but not empty, log and return true
            // This handles Xendit test webhooks that might have some data but not payment data
            Log::info('Webhook received without payment data - likely a connectivity test', [
                'webhook_data' => $webhookData
            ]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Xendit webhook processing failed', [
                'webhook_data' => $webhookData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return true to acknowledge receipt even on error
            // This prevents Xendit from retrying test webhooks
            return true;
        }
    }
    
    /**
     * Handle Xendit Invoice webhook
     * 
     * @param array $webhookData
     * @return bool
     */
    private function handleInvoiceWebhook($webhookData)
    {
        DB::beginTransaction();
        
        try {
            $externalId = $webhookData['external_id'] ?? null;
            $status = $webhookData['status'] ?? null;
            $paymentId = $webhookData['id'] ?? null;
            
            if (!$externalId || !$status) {
                Log::info('Invoice webhook missing required fields - likely a test webhook', [
                    'has_external_id' => isset($webhookData['external_id']),
                    'has_status' => isset($webhookData['status']),
                    'webhook_data' => $webhookData
                ]);
                DB::rollBack();
                // Return true to pass Xendit connectivity tests
                return true;
            }
            
            // Find transaction by external_id (transaction_reference) or xendit_payment_id
            // Eager load the booking relationship
            $transaction = PaymentTransaction::with('booking')
                ->where('transaction_reference', $externalId)
                ->orWhere('xendit_payment_id', $paymentId)
                ->first();
            
            if (!$transaction) {
                Log::warning('Transaction not found for external ID or payment ID', [
                    'external_id' => $externalId,
                    'payment_id' => $paymentId,
                    'searched_by' => [
                        'transaction_reference' => $externalId,
                        'xendit_payment_id' => $paymentId
                    ]
                ]);
                DB::rollBack();
                // Return true to acknowledge receipt even if transaction not found
                // This prevents Xendit from retrying endlessly
                return true;
            }
            
            $oldStatus = $transaction->xendit_status;
            
            // Map Xendit invoice status to our status
            // Possible statuses: PENDING, PAID, SETTLED, EXPIRED
            $xenditStatus = strtoupper($status);
            
            // Update transaction
            $transaction->xendit_status = $xenditStatus;
            $transaction->xendit_payment_id = $paymentId;
            $transaction->payment_date = now();
            
            // Safely merge metadata
            $existingMetadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $transaction->metadata = array_merge($existingMetadata, [
                'webhook_data' => $webhookData,
                'webhook_received_at' => now()->toIso8601String(),
                'old_status' => $oldStatus
            ]);
            
            $transaction->save();
            
            Log::info('Invoice webhook processed - transaction updated', [
                'transaction_id' => $transaction->transaction_id,
                'booking_id' => $transaction->booking_id,
                'external_id' => $externalId,
                'old_status' => $oldStatus,
                'new_status' => $xenditStatus,
                'amount' => $transaction->amount
            ]);
            
            // Update booking payment status
            $booking = $transaction->booking;
            if ($booking) {
                Log::info('Updating booking payment status from Invoice webhook', [
                    'booking_id' => $booking->booking_id,
                    'current_payment_status' => $booking->payment_status,
                    'current_booking_status' => $booking->status,
                    'total_amount' => $booking->total_amount
                ]);
                
                $this->updateBookingPaymentStatus($booking);
                
                // Refresh booking to get updated values
                $booking->refresh();
                
                Log::info('Booking status after update', [
                    'booking_id' => $booking->booking_id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->status
                ]);
            } else {
                Log::warning('Booking not found for transaction', [
                    'transaction_id' => $transaction->transaction_id,
                    'booking_id' => $transaction->booking_id
                ]);
            }
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Invoice webhook processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'webhook_data' => $webhookData
            ]);
            
            // Return true to acknowledge receipt and prevent retries
            // Even if there's an error, we don't want Xendit to keep retrying
            return true;
        }
    }
    
    /**
     * Handle Xendit E-Wallet webhook
     * 
     * @param array $webhookData
     * @return bool
     */
    private function handleEWalletWebhook($webhookData)
    {
        DB::beginTransaction();
        
        try {
            $paymentId = $webhookData['id'] ?? null;
            $status = $webhookData['status'] ?? null;
            $referenceId = $webhookData['reference_id'] ?? null;
            
            if (!$paymentId || !$status) {
                Log::info('E-wallet webhook missing required fields - likely a test webhook', [
                    'has_id' => isset($webhookData['id']),
                    'has_status' => isset($webhookData['status']),
                    'webhook_data' => $webhookData
                ]);
                DB::rollBack();
                // Return true to pass Xendit connectivity tests
                return true;
            }
            
            // Find transaction by xendit_payment_id or reference_id
            // Eager load the booking relationship
            $transaction = PaymentTransaction::with('booking')
                ->where('xendit_payment_id', $paymentId);
            
            if ($referenceId) {
                $transaction->orWhere('transaction_reference', $referenceId);
            }
            
            $transaction = $transaction->first();
            
            if (!$transaction) {
                Log::warning('Transaction not found for Xendit payment ID', [
                    'payment_id' => $paymentId,
                    'reference_id' => $referenceId,
                    'searched_by' => [
                        'xendit_payment_id' => $paymentId,
                        'transaction_reference' => $referenceId
                    ]
                ]);
                DB::rollBack();
                // Return true to acknowledge receipt even if transaction not found
                return true;
            }
            
            $oldStatus = $transaction->xendit_status;
            
            // Map Xendit e-wallet status to our status
            // Possible statuses: PENDING, SUCCEEDED, FAILED, VOIDED
            $xenditStatus = strtoupper($status);
            
            // Update transaction status
            $transaction->xendit_status = $xenditStatus;
            $transaction->payment_date = now();
            
            // Safely merge metadata
            $existingMetadata = is_array($transaction->metadata) ? $transaction->metadata : [];
            $transaction->metadata = array_merge($existingMetadata, [
                'webhook_data' => $webhookData,
                'webhook_received_at' => now()->toIso8601String(),
                'old_status' => $oldStatus
            ]);
            
            $transaction->save();
            
            Log::info('E-Wallet transaction status updated', [
                'transaction_id' => $transaction->transaction_id,
                'booking_id' => $transaction->booking_id,
                'old_status' => $oldStatus,
                'new_status' => $xenditStatus,
                'payment_id' => $paymentId,
                'amount' => $transaction->amount
            ]);
            
            // Update booking payment status and booking status
            $booking = $transaction->booking;
            if ($booking) {
                Log::info('Updating booking payment status from E-Wallet webhook', [
                    'booking_id' => $booking->booking_id,
                    'current_payment_status' => $booking->payment_status,
                    'current_booking_status' => $booking->status,
                    'total_amount' => $booking->total_amount
                ]);
                
                $this->updateBookingPaymentStatus($booking);
                
                // Refresh booking to get updated values
                $booking->refresh();
                
                Log::info('Booking status after update', [
                    'booking_id' => $booking->booking_id,
                    'payment_status' => $booking->payment_status,
                    'booking_status' => $booking->status
                ]);
            } else {
                Log::warning('Booking not found for transaction', [
                    'transaction_id' => $transaction->transaction_id,
                    'booking_id' => $transaction->booking_id
                ]);
            }
            
            DB::commit();
            
            Log::info('E-Wallet webhook processed successfully', [
                'transaction_id' => $transaction->transaction_id,
                'booking_id' => $transaction->booking_id,
                'status' => $xenditStatus
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('E-Wallet webhook processing exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'webhook_data' => $webhookData
            ]);
            
            // Return true to acknowledge receipt and prevent retries
            return true;
        }
    }
    
    /**
     * Update booking payment status based on payment transactions.
     * 
     * @param Booking $booking
     * @return void
     */
    public function updateBookingPaymentStatus(Booking $booking)
    {
        $transactions = $booking->paymentTransactions;
        
        $totalPaid = $transactions->filter(function ($transaction) {
            return $transaction->isSuccessful();
        })->sum('amount');
        
        $totalPending = $transactions->filter(function ($transaction) {
            return $transaction->isPending();
        })->sum('amount');
        
        $oldPaymentStatus = $booking->payment_status;
        $oldBookingStatus = $booking->status;
        
        // Determine payment status based on database enum: unpaid, paid, refunded
        if ($totalPaid >= $booking->total_amount) {
            $paymentStatus = 'paid';
        } else {
            $paymentStatus = 'unpaid';
        }
        
        Log::info('Determining booking payment status', [
            'booking_id' => $booking->booking_id,
            'total_amount' => $booking->total_amount,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'old_payment_status' => $oldPaymentStatus,
            'new_payment_status' => $paymentStatus
        ]);
        
        // Update booking if status changed
        if ($booking->payment_status !== $paymentStatus) {
            $booking->payment_status = $paymentStatus;
            $booking->save(); // This will trigger BookingObserver which auto-updates status
            
            // Refresh the booking to get the updated status from observer
            $booking->refresh();
            
            // Fallback: If payment is paid but status is still pending, update it manually
            // This ensures the status is updated even if the observer doesn't fire
            if ($paymentStatus === 'paid' && $booking->status === 'pending') {
                $booking->status = 'confirmed';
                $booking->saveQuietly(); // Save without triggering events to avoid loops
                
                Log::info('Booking status manually updated to confirmed (fallback)', [
                    'booking_id' => $booking->booking_id,
                    'reason' => 'payment_status changed to paid but observer did not update status'
                ]);
            }
            
            Log::info('Booking payment status updated', [
                'booking_id' => $booking->booking_id,
                'old_payment_status' => $oldPaymentStatus,
                'new_payment_status' => $paymentStatus,
                'old_booking_status' => $oldBookingStatus,
                'new_booking_status' => $booking->status,
                'total_amount' => $booking->total_amount,
                'total_paid' => $totalPaid
            ]);
            
            // Note: Booking status is now automatically updated by BookingObserver
            // when payment_status changes to 'paid' - no need to manually set status here
        } else {
            Log::info('Booking payment status unchanged', [
                'booking_id' => $booking->booking_id,
                'payment_status' => $paymentStatus,
                'booking_status' => $booking->status
            ]);
        }
    }
    
    /**
     * Process onsite payment.
     * 
     * @param int $bookingId
     * @param string $paymentMethod ('onsite_cash' or 'onsite_card')
     * @param float $amount
     * @param array $options
     * @return PaymentTransaction
     */
    public function processOnsitePayment($bookingId, $paymentMethod, $amount, $options = [])
    {
        if (!in_array($paymentMethod, ['onsite_cash', 'onsite_card'])) {
            throw new \InvalidArgumentException('Invalid onsite payment method');
        }
        
        $options = array_merge($options, [
            'xendit_status' => 'PAID',
            'payment_date' => now(),
            'processed_by' => auth()->id()
        ]);
        
        return $this->processPayment($bookingId, $paymentMethod, $amount, $options);
    }
    
    /**
     * Process GCash payment via Xendit.
     * 
     * @param int $bookingId
     * @param float $amount
     * @param string $xenditPaymentId
     * @param array $options
     * @return PaymentTransaction
     */
    public function processGCashPayment($bookingId, $amount, $xenditPaymentId, $options = [])
    {
        $options = array_merge($options, [
            'xendit_payment_id' => $xenditPaymentId,
            'xendit_status' => 'PENDING_PAYMENT',
            'payment_date' => now()
        ]);
        
        return $this->processPayment($bookingId, 'gcash', $amount, $options);
    }
    
    /**
     * Manually sync payment status from Xendit API.
     * 
     * @param int $transactionId
     * @return bool
     */
    public function syncPaymentStatusFromXendit($transactionId)
    {
        try {
            $transaction = PaymentTransaction::findOrFail($transactionId);
            
            if ($transaction->payment_method !== 'gcash' || !$transaction->xendit_payment_id) {
                Log::warning('Cannot sync non-GCash payment or payment without Xendit ID', [
                    'transaction_id' => $transactionId,
                    'payment_method' => $transaction->payment_method
                ]);
                return false;
            }
            
            // Don't sync if already successful
            if ($transaction->xendit_status === 'PAID' || $transaction->xendit_status === 'SETTLED') {
                Log::info('Payment already marked as paid, skipping sync', [
                    'transaction_id' => $transactionId
                ]);
                return true;
            }
            
            $xenditService = app(\App\Services\XenditService::class);
            $invoiceData = $xenditService->getInvoiceStatus($transaction->xendit_payment_id);
            
            if ($invoiceData && isset($invoiceData['status'])) {
                $oldStatus = $transaction->xendit_status;
                $newStatus = $invoiceData['status'];
                
                // Map Xendit invoice statuses
                // PENDING, PAID, SETTLED, EXPIRED
                $transaction->xendit_status = $newStatus;
                
                if ($newStatus === 'PAID' || $newStatus === 'SETTLED') {
                    $transaction->payment_date = now();
                }
                
                $transaction->save();
                
                Log::info('Payment status synced from Xendit', [
                    'transaction_id' => $transactionId,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'booking_id' => $transaction->booking_id
                ]);
                
                // Update booking payment status
                if ($transaction->booking) {
                    $this->updateBookingPaymentStatus($transaction->booking);
                    
                    Log::info('Booking payment status updated after sync', [
                        'booking_id' => $transaction->booking_id,
                        'payment_status' => $transaction->booking->fresh()->payment_status,
                        'booking_status' => $transaction->booking->fresh()->status
                    ]);
                }
                
                return true;
            }
            
            Log::warning('No valid invoice data received from Xendit', [
                'transaction_id' => $transactionId,
                'xendit_payment_id' => $transaction->xendit_payment_id
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Failed to sync payment status from Xendit', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get payment summary for a booking.
     * 
     * @param Booking $booking
     * @return array
     */
    public function getPaymentSummary(Booking $booking)
    {
        $transactions = $booking->paymentTransactions;
        
        $totalPaid = $transactions->where(function ($transaction) {
            return $transaction->isSuccessful();
        })->sum('amount');
        
        $totalPending = $transactions->where(function ($transaction) {
            return $transaction->isPending();
        })->sum('amount');
        
        $totalFailed = $transactions->where(function ($transaction) {
            return $transaction->isFailed();
        })->sum('amount');
        
        $remainingBalance = max(0, $booking->total_amount - $totalPaid);
        
        return [
            'total_amount' => $booking->total_amount,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'total_failed' => $totalFailed,
            'remaining_balance' => $remainingBalance,
            'payment_status' => $booking->payment_status,
            'transactions' => $transactions,
            'is_fully_paid' => $remainingBalance == 0,
            'has_pending_payments' => $totalPending > 0
        ];
    }
    
    /**
     * Refund a payment transaction.
     * 
     * @param PaymentTransaction $transaction
     * @param float|null $amount
     * @param string|null $reason
     * @return bool
     */
    public function refundPayment(PaymentTransaction $transaction, $amount = null, $reason = null)
    {
        try {
            $refundAmount = $amount ?? $transaction->amount;
            
            // For GCash payments, you would integrate with Xendit refund API here
            if ($transaction->payment_method === 'gcash' && $transaction->xendit_payment_id) {
                // TODO: Implement Xendit refund API call
                Log::info('GCash refund requested', [
                    'transaction_id' => $transaction->transaction_id,
                    'xendit_payment_id' => $transaction->xendit_payment_id,
                    'refund_amount' => $refundAmount,
                    'reason' => $reason
                ]);
            }
            
            // Create refund transaction record
            PaymentTransaction::create([
                'booking_id' => $transaction->booking_id,
                'payment_method' => $transaction->payment_method,
                'amount' => -$refundAmount, // Negative amount for refund
                'transaction_reference' => 'REFUND-' . $transaction->transaction_reference,
                'xendit_status' => 'REFUNDED',
                'payment_date' => now(),
                'processed_by' => auth()->id(),
                'notes' => $reason,
                'metadata' => [
                    'refund_for_transaction' => $transaction->transaction_id,
                    'refund_reason' => $reason
                ]
            ]);
            
            // Update booking payment status
            $booking = $transaction->booking;
            if ($booking) {
                $this->updateBookingPaymentStatus($booking);
            }
            
            Log::info('Payment refunded successfully', [
                'transaction_id' => $transaction->transaction_id,
                'refund_amount' => $refundAmount,
                'reason' => $reason
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Payment refund failed', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}