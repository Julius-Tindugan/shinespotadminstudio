
@extends('layouts.app')
@section('title', isset($payment) ? 'Edit Payment' : 'Create Payment')
    @section('content')
        <div class="py-6" >

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" >

                <div class="md:flex md:items-center md:justify-between" >

                    <div class="flex-1 min-w-0" >

                        <h2 class="text-2xl font-semibold text-primary-text leading-tight" > {{ isset($payment) ? 'Edit Payment' : 'Create New Payment' }} </h2>

                    </div>

                    <div class="mt-4 flex md:mt-0" >
                        <a href="{{ route('finance.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Back to Payments </a>
                        </div>

                    </div>

                    <div class="mt-6 bg-card-bg shadow-subtle overflow-hidden rounded-lg" >

                        <form action="{{ isset($payment) ? route('finance.payments.update', $payment->
                            id) : route('finance.payments.store') }}" method="POST" class="space-y-6 p-6" > @csrf
                            @if(isset($payment)) @method('PUT')
                            @endif

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6" >
                                <!-- Booking Selection -->
                                    <div class="sm:col-span-3" >
                                        <label for="booking_id" class="block text-sm font-medium text-secondary-text" > Booking </label>
                                        <div class="mt-1" >

                                            <select id="booking_id" name="booking_id" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('booking_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" >
                                                <option value="">Select a booking</option>
                                                @foreach($bookings as $booking) <option value="{{ $booking->booking_id }}" {{ (old('booking_id', $payment->booking_id ?? null) == $booking->booking_id) ? 'selected' : '' }}> {{ $booking->booking_reference }} - {{ $booking->client_first_name }} {{ $booking->client_last_name }} - ₱{{ number_format($booking->total_amount, 2) }} </option>
                                                @endforeach

                                            </select>

                                        </div>
                                        @error('booking_id')
                                        <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                    @enderror
                                </div>
                                <!-- Amount -->
                                    <div class="sm:col-span-3" >
                                        <label for="amount" class="block text-sm font-medium text-secondary-text" > Amount (₱) </label>
                                        <div class="mt-1 relative rounded-md shadow-sm" >

                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" >
                                                <span class="text-gray-500 sm:text-sm" >₱</span>
                                            </div>

                                            <input type="number" name="amount" id="amount" step="0.01" min="0" value="{{ old('amount', isset($payment) ? $payment->
                                                amount : '') }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full pl-7 sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('amount') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" placeholder="0.00" required>
                                            </div>
                                            @error('amount')
                                            <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Payment Method -->
                                        <div class="sm:col-span-3" >
                                            <label for="payment_method_id" class="block text-sm font-medium text-secondary-text" > Payment Method </label>
                                            <div class="mt-1" >


                                                <select id="payment_method_id" name="payment_method_id" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('payment_method_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                                                    <option value="">Select a payment method</option>
                                                    @foreach($paymentMethods as $method)
                                                        @php
                                                            // Support multiple shapes: Eloquent model or array
                                                            if (is_array($method)) {
                                                                $mId = $method['method_id'] ?? $method['id'] ?? $method['value'] ?? null;
                                                                $mLabel = $method['name'] ?? $method['label'] ?? $method['method_name'] ?? null;
                                                            } else {
                                                                $mId = $method->method_id ?? ($method->id ?? ($method->value ?? null));
                                                                $mLabel = $method->name ?? ($method->label ?? ($method->method_name ?? null));
                                                            }
                                                            // Fallback to string cast to avoid null option values
                                                            $mId = $mId === null ? '' : $mId;
                                                            $mLabel = $mLabel === null ? (string) $mId : $mLabel;
                                                        @endphp

                                                        <option value="{{ $mId }}" {{ (old('payment_method_id', $payment->payment_method_id ?? null) == $mId) ? 'selected' : '' }}>{{ $mLabel }}</option>
                                                    @endforeach

                                                </select>

                                            </div>
                                            @error('payment_method_id')
                                            <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Payment Status -->
                                        <div class="sm:col-span-3" >
                                            <label for="status" class="block text-sm font-medium text-secondary-text" > Payment Status </label>
                                            <div class="mt-1" >

                                                <select id="status" name="status" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('status') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                                                    <option value="completed" {{ (old('status', $payment->status ?? '') == 'completed') ? 'selected' : '' }}>Completed</option><option value="pending" {{ (old('status', $payment->status ?? '') == 'pending') ? 'selected' : '' }}>Pending</option><option value="failed" {{ (old('status', $payment->status ?? '') == 'failed') ? 'selected' : '' }}>Failed</option>
                                                </select>

                                            </div>
                                            @error('status')
                                            <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Transaction ID -->
                                        <div class="sm:col-span-3" >
                                            <label for="transaction_id" class="block text-sm font-medium text-secondary-text" > Transaction ID </label>
                                            <div class="mt-1" >

                                                <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id', $payment->
                                                    transaction_id ?? '') }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('transaction_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" placeholder="Transaction Reference ID">
                                                </div>
                                                @error('transaction_id')
                                                <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <!-- Payment Date -->
                                            <div class="sm:col-span-3" >
                                                <label for="payment_date" class="block text-sm font-medium text-secondary-text" > Payment Date </label>
                                                <div class="mt-1" >

                                                    <input type="datetime-local" name="payment_date" id="payment_date" value="{{ old('payment_date', isset($payment) ? $payment->
                                                        payment_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('payment_date') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" required>
                                                    </div>
                                                    @error('payment_date')
                                                    <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <!-- Description -->
                                                <div class="sm:col-span-6" >
                                                    <label for="description" class="block text-sm font-medium text-secondary-text" > Description </label>
                                                    <div class="mt-1" >

                                                        <textarea id="description" name="description" rows="4" class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('description') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror" >{{ old('description', $payment->description ?? '') }}</textarea>

                                                    </div>
                                                    @error('description')
                                                    <p class="mt-2 text-sm text-red-600" >{{ $message }}</p>
                                                @enderror
                                            </div>

                                        </div>

                                        <div class="pt-5 border-t border-border-color" >

                                            <div class="flex justify-end" >
                                                <a href="{{ route('finance.payments.index') }}" class="bg-white py-2 px-4 border border-border-color rounded-md shadow-sm text-sm font-medium text-secondary-text hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Cancel </a>
                                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > {{ isset($payment) ? 'Update Payment' : 'Create Payment' }} </button>

                                            </div>

                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>
                    @endsection
                    @section('scripts') <script> // Dynamically update the amount field when booking is selected document.addEventListener('DOMContentLoaded', function() { const bookingSelect = document.getElementById('booking_id'); const amountInput = document.getElementById('amount'); const bookingData = @json($bookings->keyBy('id')); bookingSelect.addEventListener('change', function() { const bookingId = this.value; if (bookingId && bookingData[bookingId]) { // Set amount to the remaining balance if it exists if (bookingData[bookingId].remaining_balance) { amountInput.value = bookingData[bookingId].remaining_balance; } else { amountInput.value = bookingData[bookingId].total_amount; } } }); }); </script> @endsection