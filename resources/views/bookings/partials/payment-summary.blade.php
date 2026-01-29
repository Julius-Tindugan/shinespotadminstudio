{{-- Payment Summary Card --}}
<div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            Payment Status
        </h3>
    </div>

    <div class="p-6">
        {{-- Payment Status Badge --}}
        <div class="text-center mb-6">
            @php
                $statusClass = match($paymentSummary['payment_status']) {
                    'paid' => 'bg-green-100 text-green-800',
                    'refunded' => 'bg-purple-100 text-purple-800',
                    default => 'bg-red-100 text-red-800'
                };
                
                $statusText = match($paymentSummary['payment_status']) {
                    'paid' => 'Fully Paid',
                    'refunded' => 'Refunded',
                    default => 'Unpaid'
                };
                
                // Check for partial payment (total paid > 0 but < total amount)
                if ($paymentSummary['payment_status'] === 'unpaid' && $paymentSummary['total_paid'] > 0) {
                    $statusClass = 'bg-yellow-100 text-yellow-800';
                    $statusText = 'Partially Paid';
                }
                
                // Check for pending payments
                if ($paymentSummary['payment_status'] === 'unpaid' && $paymentSummary['has_pending_payments']) {
                    $statusClass = 'bg-blue-100 text-blue-800';
                    $statusText = 'Payment Pending';
                }
            @endphp
            
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClass }}">
                @if($paymentSummary['payment_status'] === 'paid')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @elseif($statusText === 'Partially Paid' || $statusText === 'Payment Pending')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                @elseif($paymentSummary['payment_status'] === 'refunded')
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                @endif
                {{ $statusText }}
            </span>
        </div>

        {{-- Payment Summary --}}
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-border-color">
                <span class="text-sm text-secondary-text">Total Amount:</span>
                <span class="text-sm font-medium text-primary-text">₱{{ number_format($paymentSummary['total_amount'], 2) }}</span>
            </div>
            
            <div class="flex justify-between items-center py-2 border-b border-border-color">
                <span class="text-sm text-secondary-text">Amount Paid:</span>
                <span class="text-sm font-medium text-green-600">₱{{ number_format($paymentSummary['total_paid'], 2) }}</span>
            </div>
            
            @if($paymentSummary['total_pending'] > 0)
            <div class="flex justify-between items-center py-2 border-b border-border-color">
                <span class="text-sm text-secondary-text">Pending Payment:</span>
                <span class="text-sm font-medium text-yellow-600">₱{{ number_format($paymentSummary['total_pending'], 2) }}</span>
            </div>
            @endif
            
            @if($paymentSummary['remaining_balance'] > 0)
            <div class="flex justify-between items-center py-2">
                <span class="text-sm font-medium text-secondary-text">Remaining Balance:</span>
                <span class="text-sm font-bold text-red-600">₱{{ number_format($paymentSummary['remaining_balance'], 2) }}</span>
            </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="mt-6 space-y-2">
            @if($paymentSummary['remaining_balance'] > 0)
                <button type="button" onclick="openPaymentModal('onsite')" 
                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Record Onsite Payment
                </button>
                
                @if(\App\Models\SystemSetting::getValue('payment_integration_enabled', false))
                <button type="button" onclick="initiateGCashPayment()" 
                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Process GCash Payment
                </button>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Payment Transactions History --}}
<div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden mt-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-white flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Payment History
        </h3>
    </div>

    <div class="p-6">
        @forelse($paymentSummary['transactions'] as $transaction)
            <div class="flex items-center justify-between py-3 border-b border-border-color last:border-b-0">
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-primary-text">
                            {{ $transaction->formatted_payment_method }}
                        </span>
                        <span class="text-sm font-bold text-primary-text">
                            {{ $transaction->amount < 0 ? '-' : '+' }}₱{{ number_format(abs($transaction->amount), 2) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-xs text-secondary-text">
                            {{ $transaction->payment_date->format('M d, Y h:i A') }}
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $transaction->status_badge_class }}">
                            {{ $transaction->status_display }}
                        </span>
                    </div>
                    
                    @if($transaction->transaction_reference)
                        <div class="text-xs text-secondary-text mt-1">
                            Ref: {{ $transaction->transaction_reference }}
                        </div>
                    @endif
                    
                    @if($transaction->notes)
                        <div class="text-xs text-secondary-text mt-1">
                            {{ $transaction->notes }}
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-12 h-12 mx-auto text-secondary-text mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-sm text-secondary-text">No payment transactions recorded</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Payment Modals --}}
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Record Payment</h3>
            
            <form id="paymentForm">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Payment Method</option>
                        <option value="onsite_cash">Cash (Onsite)</option>
                        <option value="onsite_card">Card (Onsite)</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $paymentSummary['remaining_balance'] }}" 
                           value="{{ $paymentSummary['remaining_balance'] }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Utility function to show UI alerts
function showAlert(message, type = 'error') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
        type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
        type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
        'bg-blue-100 border border-blue-400 text-blue-700'
    } flex items-center justify-between max-w-md`;
    
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'error' ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
        <button class="ml-3 text-current hover:opacity-70" onclick="this.parentElement.remove()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 5000);
}

// Confirmation modal for GCash payment
function showGCashConfirmation(amount, callback) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
    modal.innerHTML = `
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Initiate GCash Payment</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Initiate GCash payment for ₱${amount.toLocaleString('en-US', {minimumFractionDigits: 2})}?
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmGCash" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Proceed
                    </button>
                    <button id="cancelGCash" class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    modal.querySelector('#confirmGCash').addEventListener('click', () => {
        modal.remove();
        callback();
    });
    
    modal.querySelector('#cancelGCash').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function openPaymentModal(type) {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

function initiateGCashPayment() {
    const remainingBalance = {{ $paymentSummary['remaining_balance'] }};
    
    showGCashConfirmation(remainingBalance, () => {
        fetch('/payment-transactions/gcash', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                booking_id: {{ $booking->booking_id }},
                amount: remainingBalance
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('GCash payment initiated. Redirecting to payment page...', 'success');
                window.open(data.payment_url, '_blank');
                // Refresh page after a delay to show updated status
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('Failed to initiate GCash payment: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while initiating payment', 'error');
        });
    });
}

// Handle payment form submission
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/payment-transactions', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Payment recorded successfully!', 'success');
            closePaymentModal();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to record payment: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while recording payment', 'error');
    });
});
</script>