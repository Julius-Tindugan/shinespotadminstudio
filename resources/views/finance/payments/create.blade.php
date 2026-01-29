@extends('layouts.app')

@section('title', 'Create New Payment')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-semibold text-primary-text leading-tight">Create New Payment</h2>
                </div>
                <div class="mt-4 flex md:mt-0">
                    <a href="{{ route('finance.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Payments
                    </a>
                </div>
            </div>

            <div class="mt-6 bg-card-bg shadow-subtle overflow-hidden rounded-lg">
                <form action="{{ route('finance.payments.store') }}" method="POST" class="space-y-6 p-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Booking Selection -->
                        <div class="sm:col-span-3">
                            <label for="booking_id" class="block text-sm font-medium text-secondary-text">Booking</label>
                            <div class="mt-1">
                                <select id="booking_id" name="booking_id" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('booking_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                                    <option value="">Select a booking</option>
                                    @foreach($bookings as $booking)
                                        <option value="{{ $booking->booking_id }}" {{ old('booking_id') == $booking->booking_id ? 'selected' : '' }}>
                                            {{ $booking->booking_reference }} - {{ $booking->client_first_name }} {{ $booking->client_last_name }} - ₱{{ number_format($booking->total_amount, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('booking_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="sm:col-span-3">
                            <label for="amount" class="block text-sm font-medium text-secondary-text">Amount (₱)</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount') }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full pl-7 sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('amount') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" placeholder="0.00" required>
                            </div>
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="sm:col-span-3">
                            <label for="payment_method" class="block text-sm font-medium text-secondary-text">Payment Method</label>
                            <div class="mt-1">
                                <select id="payment_method" name="payment_method" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('payment_method') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                                    <option value="">Select a payment method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method['value'] }}" {{ old('payment_method') == $method['value'] ? 'selected' : '' }}>
                                            {{ $method['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('payment_method')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Payment Date -->
                        <div class="sm:col-span-3">
                            <label for="payment_date" class="block text-sm font-medium text-secondary-text">Payment Date</label>
                            <div class="mt-1">
                                <input type="datetime-local" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d\TH:i')) }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('payment_date') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                            </div>
                            @error('payment_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Transaction Reference -->
                        <div class="sm:col-span-3">
                            <label for="transaction_reference" class="block text-sm font-medium text-secondary-text">Transaction Reference</label>
                            <div class="mt-1">
                                <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference') }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('transaction_reference') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" placeholder="Transaction reference number">
                            </div>
                            @error('transaction_reference')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="sm:col-span-6">
                            <label for="notes" class="block text-sm font-medium text-secondary-text">Notes</label>
                            <div class="mt-1">
                                <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('notes') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" placeholder="Additional payment information">{{ old('notes') }}</textarea>
                            </div>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="submitBtn" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                            Create Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const paymentMethodSelect = document.getElementById('payment_method');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.querySelector('form');
        const originalBtnText = 'Create Payment';
        
        // Update button text based on payment method
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'gcash') {
                submitBtn.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Generate GCash Payment Link
                `;
            } else {
                submitBtn.textContent = originalBtnText;
            }
        });
        
        // Show loading state on submit
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            const selectedMethod = paymentMethodSelect.value;
            
            if (selectedMethod === 'gcash') {
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating Payment Link...
                `;
            } else {
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                `;
            }
        });
    </script>
@endsection