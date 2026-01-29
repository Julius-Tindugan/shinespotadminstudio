@extends('layouts.app')
@section('title', 'Payment Management')
@section('content')
    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>
    
    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center">

                <h1 class="text-2xl font-semibold text-primary-text"> Payment Management </h1>

                <div>
                    <a href="{{ route('finance.payments.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"><svg
                            class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg> New Payment </a>
                </div>

            </div>
            <!-- Filters -->
            <div class="mt-6 bg-card-bg shadow-subtle rounded-lg p-4">

                <form action="{{ route('finance.payments.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label for="status" class="block text-sm font-medium text-secondary-text">Payment Status</label>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="">All Statuses</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>

                    </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-secondary-text">Payment
                            Method</label>
                        <select id="payment_method" name="payment_method"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="">All Methods</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method['value'] }}"
                                    {{ request('payment_method') == $method['value'] ? 'selected' : '' }}>
                                    {{ $method['label'] }}</option>
                            @endforeach

                        </select>

                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-secondary-text">Date From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-secondary-text">Date To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div class="md:col-span-3">
                        <label for="search" class="block text-sm font-medium text-secondary-text">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by client name, booking reference, or description..."
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div class="flex items-end">

                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"><svg
                                class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg> Filter </button>

                    </div>

                </form>

            </div>
            <!-- Payments Table -->
            <div class="mt-6 bg-card-bg shadow-subtle rounded-lg overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border-color">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Booking/Client </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Payment Details </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Amount </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Status </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Date </th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-card-bg divide-y divide-border-color">
                            @forelse($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-text">
                                            {{ $payment->booking->booking_reference ?? 'N/A' }}
                                        </div>

                                        <div class="text-sm text-secondary-text">
                                            @if ($payment->booking)
                                                {{ $payment->booking->client_first_name }}
                                                {{ $payment->booking->client_last_name }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-primary-text">
                                            @if ($payment->payment_method == 'gcash')
                                                GCash (Online)
                                            @elseif($payment->payment_method == 'onsite_cash')
                                                Cash (Onsite)
                                            @elseif($payment->payment_method == 'onsite_card')
                                                Card (Onsite)
                                            @else
                                                Unknown Method
                                            @endif
                                        </div>

                                        @if ($payment->transaction_reference)
                                            <div class="text-xs text-secondary-text">
                                                Ref: {{ $payment->transaction_reference }}
                                            </div>
                                        @endif

                                        <div class="text-xs text-secondary-text">
                                            {{ Str::limit($payment->notes, 40) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-text">
                                            ₱{{ number_format($payment->amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            // Use the Payment model's getStatusAttribute() method
                                            // This properly checks payment_method, xendit_status, and booking payment_status
                                            $status = $payment->status; // Uses Payment model's getStatusAttribute()
                                            
                                            // Set user-friendly labels
                                            $statusLabel = ucfirst($status);
                                        @endphp
                                        <span class="payment-status px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $status == 'completed' ? 'bg-green-100 text-green-800 light:bg-green-900/30 light:text-green-400' : ($status == 'pending' ? 'bg-yellow-100 text-black-800 light:bg-yellow-900/30 light:text-black-400' : 'bg-red-100 text-red-800 light:bg-red-900/30 light:text-red-400') }}" data-payment-id="{{ $payment->transaction_id }}" data-booking-id="{{ $payment->booking_id }}"> {{ $statusLabel }} </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-primary-text">
                                            {{ $payment->payment_date->format('M d, Y') }}
                                        </div>

                                        <div class="text-xs text-secondary-text">
                                            {{ $payment->payment_date->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @if($payment->payment_method === 'gcash' && $payment->xendit_payment_id && in_array($payment->xendit_status, ['PENDING', 'PENDING_PAYMENT', null]))
                                                <button onclick="syncPaymentStatus({{ $payment->transaction_id }})" 
                                                    class="text-blue-600 hover:text-blue-900 sync-btn" 
                                                    data-payment-id="{{ $payment->transaction_id }}"
                                                    title="Sync status from Xendit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            @endif
                                            <a href="{{ route('finance.payments.show', $payment->transaction_id) }}"
                                                class="text-accent hover:text-accent-hover"><svg
                                                    xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></a><a
                                                href="{{ route('finance.payments.edit', $payment->transaction_id) }}"
                                                class="text-accent hover:text-accent-hover"><svg
                                                    xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg></a>
                                            <button onclick="confirmDelete('{{ $payment->transaction_id }}')"
                                                class="text-red-600 hover:text-red-900" type="button"><svg
                                                    xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button>

                                            <form id="delete-form-{{ $payment->transaction_id }}"
                                                action="{{ route('finance.payments.destroy', $payment->transaction_id) }}"
                                                method="POST" class="hidden"> @csrf @method('DELETE')
                                            </form>

                                        </div>
                                    </td>
                            </tr> @empty <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-secondary-text"> No
                                        payments found. <a href="{{ route('finance.payments.create') }}"
                                            class="text-accent hover:text-accent-hover">Create a new payment</a>. </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-border-color">
                    {{ $payments->withQueryString()->links() }}
                </div>

            </div>
            <!-- Payment Summary -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">

                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-secondary-text truncate"> Total Completed Payments </dt>
                        <dd class="mt-1 text-3xl font-semibold text-primary-text">
                            ₱{{ number_format($totalCompletedPayments, 2) }} </dd>
                    </div>

                </div>

                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-secondary-text truncate"> Total Pending Payments </dt>
                        <dd class="mt-1 text-3xl font-semibold text-primary-text">
                            ₱{{ number_format($totalPendingPayments, 2) }} </dd>
                    </div>

                </div>

                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-secondary-text truncate"> Average Payment Amount </dt>
                        <dd class="mt-1 text-3xl font-semibold text-primary-text">
                            ₱{{ number_format($averagePayment, 2) }} </dd>
                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 transition-opacity" aria-hidden="true">

                <div class="absolute inset-0 bg-gray-500 opacity-75">

                </div>

            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-card-bg rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <div class="bg-card-bg px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                    <div class="sm:flex sm:items-start">

                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">

                            <h3 class="text-lg leading-6 font-medium text-primary-text" id="modal-title"> Delete Payment
                            </h3>

                            <div class="mt-2">

                                <p class="text-sm text-secondary-text"> Are you sure you want to delete this payment
                                    record? This action cannot be undone. </p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                    <button onclick="executeDelete()" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete </button>

                    <button onclick="closeModal()" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-border-color shadow-sm px-4 py-2 bg-card-bg text-base font-medium text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel </button>

                </div>

            </div>

        </div>

    </div>
@endsection
@section('scripts')
<script>
    let deleteId = null;

    function confirmDelete(id) {
        deleteId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        deleteId = null;
    }

    function executeDelete() {
        if (deleteId) {
            document.getElementById('delete-form-' + deleteId).submit();
        }
    } 
    
    // Close modal when clicking outside 
    window.onclick = function(event) { 
        const modal = document.getElementById('deleteModal'); 
        if (event.target === modal) { 
            closeModal(); 
        } 
    } 
    
    /**
     * Toast Notification System
     */
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
    
    /**
     * Manually sync payment status from Xendit
     */
    function syncPaymentStatus(paymentId) {
        const syncBtn = document.querySelector(`.sync-btn[data-payment-id="${paymentId}"]`);
        
        if (!syncBtn) return;
        
        // Disable button and show loading state
        syncBtn.disabled = true;
        syncBtn.innerHTML = '<svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        fetch(`/finance/payments/${paymentId}/sync-xendit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message with toast
                showToast('Payment status synced successfully! Status: ' + (data.payment.xendit_status || 'Unknown'), 'success');
                
                // Reload page to show updated status after a brief delay
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showToast('Failed to sync payment status: ' + (data.message || 'Unknown error'), 'error');
                
                // Re-enable button
                syncBtn.disabled = false;
                syncBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
            }
        })
        .catch(error => {
            console.error('Error syncing payment status:', error);
            showToast('Error syncing payment status. Please try again.', 'error');
            
            // Re-enable button
            syncBtn.disabled = false;
            syncBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
        });
    }
    
    // Auto-refresh payment statuses using AJAX every 30 seconds
    // This ensures that webhook updates to booking payment_status are reflected in the UI
    let autoRefreshInterval = null;
    let isPageActive = true;
    
    // Only refresh when page is visible
    document.addEventListener('visibilitychange', function() {
        isPageActive = !document.hidden;
    });
    
    function updatePaymentStatuses() {
        if (!isPageActive) return;
        
        // Get all payment status elements
        const statusElements = document.querySelectorAll('.payment-status');
        
        if (statusElements.length === 0) return;
        
        // Collect booking IDs to check
        const bookingIds = [...new Set(Array.from(statusElements).map(el => el.dataset.bookingId).filter(id => id))];
        
        if (bookingIds.length === 0) return;
        
        // Fetch updated payment statuses via AJAX
        fetch('{{ route("finance.payments.check-statuses") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ booking_ids: bookingIds })
        })
        .then(response => response.json())
        .then(data => {
            // Update each status element
            statusElements.forEach(element => {
                const paymentId = element.dataset.paymentId;
                const bookingId = element.dataset.bookingId;
                
                if (data.payments && data.payments[paymentId]) {
                    const newStatus = data.payments[paymentId].status;
                    const currentStatus = element.textContent.trim().toLowerCase();
                    
                    // Only update if status has changed
                    if (newStatus !== currentStatus) {
                        // Remove old status classes
                        element.classList.remove(
                            'bg-green-100', 'text-green-800', 'light:bg-green-900/30', 'light:text-green-400',
                            'bg-yellow-100', 'text-yellow-800', 'light:bg-yellow-900/30', 'light:text-yellow-400',
                            'bg-red-100', 'text-red-800', 'light:bg-red-900/30', 'light:text-red-400'
                        );
                        
                        // Add new status classes
                        if (newStatus === 'completed') {
                            element.classList.add('bg-green-100', 'text-green-800', 'light:bg-green-900/30', 'light:text-green-400');
                        } else if (newStatus === 'pending') {
                            element.classList.add('bg-yellow-100', 'text-yellow-800', 'light:bg-yellow-900/30', 'light:text-yellow-400');
                        } else if (newStatus === 'failed') {
                            element.classList.add('bg-red-100', 'text-red-800', 'light:bg-red-900/30', 'light:text-red-400');
                        }
                        
                        // Update text
                        element.textContent = ' ' + newStatus.charAt(0).toUpperCase() + newStatus.slice(1) + ' ';
                        
                        // Add a brief animation to indicate change
                        element.style.animation = 'pulse 0.5s';
                        setTimeout(() => {
                            element.style.animation = '';
                        }, 500);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error updating payment statuses:', error);
        });
    }
    
    function startAutoRefresh() {
        // Update immediately on page load
        setTimeout(updatePaymentStatuses, 2000);
        
        // Then refresh every 30 seconds
        autoRefreshInterval = setInterval(updatePaymentStatuses, 30000);
    }
    
    // Start auto-refresh when page loads
    startAutoRefresh();
    
    // Clear interval when page is about to unload
    window.addEventListener('beforeunload', function() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
    });
</script> @endsection
