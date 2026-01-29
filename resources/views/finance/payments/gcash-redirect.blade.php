@extends('layouts.app')
@section('title', 'GCash Payment - Processing')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                Payment Link Created!
            </h2>
            <p class="text-sm text-gray-600 mb-6">
                Payment ID: #{{ $payment->transaction_id }}
            </p>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-white shadow-lg rounded-lg p-6 space-y-4">
            <div class="border-b pb-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking Reference:</span>
                        <span class="font-medium text-gray-900">{{ $payment->booking->booking_reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Client Name:</span>
                        <span class="font-medium text-gray-900">
                            {{ $payment->booking->client_first_name }} {{ $payment->booking->client_last_name }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-bold text-lg text-green-600">₱{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="font-medium text-gray-900">GCash</span>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Next Steps:</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Click the button below to proceed to GCash payment</li>
                            <li>Complete the payment on Xendit's secure page</li>
                            <li>You'll be redirected back automatically after payment</li>
                            <li>Payment status will update automatically via webhook</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3 pt-2">
                <a href="{{ $paymentUrl }}" target="_blank"
                   class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                    Proceed to GCash Payment
                </a>

                <button onclick="copyPaymentLink()" 
                        class="w-full flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Copy Payment Link
                </button>

                <button onclick="closeAndRefresh()"
                        class="w-full flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close Window
                </button>
            </div>
        </div>

        <!-- Warning -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex">
                <svg class="h-5 w-5 text-yellow-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm text-yellow-700">
                    <p class="font-medium">Important:</p>
                    <p>The payment is currently <strong>PENDING</strong>. After completing the GCash payment, the system will automatically update the status via webhook. You can close this page and check the payment management page for the updated status.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 right-4 z-50"></div>

<script>
    const paymentUrl = @json($paymentUrl);
    const paymentsIndexUrl = @json(route('finance.payments.index'));
    const paymentId = @json($payment->transaction_id);

    function copyPaymentLink() {
        navigator.clipboard.writeText(paymentUrl).then(() => {
            showToast('Payment link copied to clipboard!', 'success');
        }).catch(err => {
            showToast('Failed to copy link', 'error');
            console.error('Failed to copy:', err);
        });
    }

    function closeAndRefresh() {
        // Show loading message
        showToast('Redirecting to payment management...', 'info');
        
        // Redirect to payments index page
        setTimeout(() => {
            window.location.href = paymentsIndexUrl;
        }, 500);
    }

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        const bgColor = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        }[type] || 'bg-gray-500';
        
        const icon = {
            'success': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>',
            'error': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>',
            'warning': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
            'info': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        }[type] || '';
        
        toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-0 opacity-100 max-w-md`;
        toast.innerHTML = `
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            <span class="flex-1">${message}</span>
            <button class="ml-2 hover:bg-white hover:bg-opacity-20 rounded p-1 transition-colors" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.transform = 'translateX(400px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Listen for messages from the payment window (if opened in new tab/window)
    window.addEventListener('message', function(event) {
        // Check if message is from Xendit or payment completion
        if (event.data && event.data.type === 'payment_completed') {
            showToast('Payment completed! Refreshing payment status...', 'success');
            setTimeout(() => {
                window.location.href = paymentsIndexUrl;
            }, 2000);
        }
    });

    // Check payment status periodically after user might have completed payment
    let statusCheckInterval;
    let checkCount = 0;
    const maxChecks = 30; // Check for 5 minutes (every 10 seconds)

    function startStatusMonitoring() {
        // Start checking after 30 seconds (give user time to start payment)
        setTimeout(() => {
            statusCheckInterval = setInterval(checkPaymentStatus, 10000); // Every 10 seconds
        }, 30000);
    }

    async function checkPaymentStatus() {
        checkCount++;
        
        if (checkCount > maxChecks) {
            clearInterval(statusCheckInterval);
            return;
        }

        try {
            const response = await fetch(`{{ url('finance/payments') }}/${paymentId}/check-status`);
            const data = await response.json();
            
            if (data.success && (data.status === 'completed' || data.xendit_status === 'SUCCEEDED' || data.xendit_status === 'PAID' || data.xendit_status === 'SETTLED')) {
                clearInterval(statusCheckInterval);
                showToast('Payment confirmed! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = paymentsIndexUrl;
                }, 2000);
            }
        } catch (error) {
            console.log('Status check error:', error);
        }
    }

    // Start monitoring
    startStatusMonitoring();

    // Auto-redirect removed - user should manually proceed or close
    // This gives them control over when to proceed to payment
</script>
@endsection
