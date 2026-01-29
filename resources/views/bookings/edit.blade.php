@extends('layouts.app')
@section('title', 'Edit Booking')
@section('content')
    <div class="container mx-auto px-4 py-8 pb-32">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">

            <div class="flex-1">

                <h1 class="text-2xl font-bold text-primary-text">Edit Booking</h1>

                <p class="text-sm text-secondary-text mt-1">Booking #{{ $booking->booking_id }} | Reference:
                    {{ $booking->booking_reference ?? 'N/A' }}</p>

            </div>

                        <div class="flex flex-col sm:flex-row gap-2">
                <a href="{{ route('bookings.show', $booking) }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-info hover:bg-info/90 text-white rounded-md transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg> 
                    View Details
                </a>
                <a href="{{ route('bookings.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-background border border-border-color rounded-md hover:bg-card-bg text-secondary-text hover:text-primary-text transition-all duration-200 font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg> 
                    Back to Bookings
                </a>
            </div>

        </div>

        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded hidden" role="alert">

                <p>{{ session('error') }}</p>

            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">

                <p class="font-bold">Please fix the following errors:</p>
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif

        <form id="booking-form" action="{{ route('bookings.update', $booking) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
                <!-- Left Column - Main Form -->
                <div class="xl:col-span-3 space-y-6">
                    <!-- Client Information Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-6 py-4">

                            <h2 class="text-lg font-semibold text-white flex items-center"><svg class="w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg> Client Information </h2>

                            <p class="text-white/80 text-sm">Update client contact details</p>

                        </div>

                        <div class="p-6">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <label for="client_first_name"
                                        class="block text-sm font-medium text-secondary-text mb-2"> First Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="client_first_name" name="client_first_name"
                                        value="{{ old('client_first_name', $booking->client_first_name) }}"
                                        required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                                <div>
                                    <label for="client_last_name"
                                        class="block text-sm font-medium text-secondary-text mb-2"> Last Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="client_last_name" name="client_last_name"
                                        value="{{ old('client_last_name', $booking->client_last_name) }}"
                                        required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                                <div>
                                    <label for="client_email" class="block text-sm font-medium text-secondary-text mb-2">
                                        Email Address </label>
                                    <input type="email" id="client_email" name="client_email"
                                        value="{{ old('client_email', $booking->client_email) }}"
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                                <div>
                                    <label for="client_phone" class="block text-sm font-medium text-secondary-text mb-2">
                                        Phone Number </label>
                                    <input type="text" id="client_phone" name="client_phone"
                                        value="{{ old('client_phone', $booking->client_phone) }}"
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- Session & Booking Details Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-6 py-4">

                            <h2 class="text-lg font-semibold text-white flex items-center"><svg class="w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg> Session & Event Details </h2>

                            <p class="text-white/80 text-sm">Configure session timing, staff, and event specifics</p>

                        </div>

                        <div class="p-6 space-y-6">
                            <!-- Date and Time -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                <div>
                                    <label for="booking_date" class="block text-sm font-medium text-secondary-text mb-2">
                                        Date <span class="text-red-500">*</span></label>
                                    <input type="date" id="booking_date" name="booking_date"
                                        value="{{ old('booking_date', $booking->booking_date->format('Y-m-d')) }}"
                                        required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-secondary-text mb-2">
                                        Start Time <span class="text-red-500">*</span></label>
                                    <input type="time" id="start_time" name="start_time"
                                        value="{{ old('start_time', \Carbon\Carbon::parse($booking->start_time)->format('H:i')) }}"
                                        required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-secondary-text mb-2"> End
                                        Time <span class="text-red-500">*</span></label>
                                    <input type="time" id="end_time" name="end_time"
                                        value="{{ old('end_time', \Carbon\Carbon::parse($booking->end_time)->format('H:i')) }}"
                                        required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                </div>

                            </div>
                            <!-- Staff and Package -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <label for="primary_staff_id"
                                        class="block text-sm font-medium text-secondary-text mb-2"> Primary Staff</label>
                                    <select id="primary_staff_id" name="primary_staff_id"
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                        <option value="">Select Primary Staff</option>
                                        @foreach ($staff as $member)
                                            <option value="{{ $member->staff_id }}"
                                                {{ old('primary_staff_id', $booking->primary_staff_id) == $member->staff_id ? 'selected' : '' }}>
                                                {{ $member->first_name }} {{ $member->last_name }} </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div>
                                    <label for="package_id" class="block text-sm font-medium text-secondary-text mb-2">
                                        Package <span class="text-red-500">*</span></label>
                                    <select id="package_id" name="package_id" required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                        <option value="">Select Package</option>
                                        @foreach ($packages ?? [] as $package)
                                            <option value="{{ $package->package_id }}"
                                                data-price="{{ $package->price }}"
                                                {{ old('package_id', $booking->package_id) == $package->package_id ? 'selected' : '' }}>
                                                {{ $package->title }} - ₱{{ number_format($package->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                            <!-- Additional Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Backdrop and Status -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <label for="backdrop_id" class="block text-sm font-medium text-secondary-text mb-2">
                                        Backdrop </label>
                                    <select id="backdrop_id" name="backdrop_id"
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                        <option value="">Select Backdrop</option>
                                        @foreach (\App\Models\Backdrop::where('is_active', 1)->orderBy('name')->get() as $backdrop)
                                            <option value="{{ $backdrop->backdrop_id }}"
                                                {{ old('backdrop_id', $booking->backdrop_id) == $backdrop->backdrop_id ? 'selected' : '' }}>
                                                {{ $backdrop->name }} </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-secondary-text mb-2">
                                        Status <span class="text-red-500">*</span></label>
                                    <select id="status" name="status" required
                                        class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">

                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ old('status', $booking->status) == $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }} </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- Addons Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-6 py-4">

                            <h2 class="text-lg font-semibold text-white flex items-center"><svg class="w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg> Addons & Extras </h2>

                            <p class="text-white/80 text-sm">Select additional items to enhance the booking</p>

                        </div>

                        <div class="p-6">

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                                @foreach ($addons as $addon)
                                    <div
                                        class="addon-item bg-background border border-border-color rounded-lg p-4 transition-all hover:shadow-md hover:border-accent"
                                        data-addon-price="{{ $addon->addon_price }}">

                                        <div class="flex items-start justify-between">

                                            <div class="flex items-start">

                                                <input type="checkbox"
                                                    id="addon-{{ $addon->addon_id }}"
                                                    name="addons[]" value="{{ $addon->addon_id }}"
                                                    class="addon-checkbox mt-1 mr-3 h-4 w-4 rounded border-border-color text-accent focus:ring-accent"
                                                    {{ isset($selectedAddons[$addon->addon_id]) ? 'checked' : '' }}>
                                                <div class="flex-grow">
                                                    <label for="addon-{{ $addon->addon_id }}"
                                                        class="text-sm font-medium text-primary-text block cursor-pointer">
                                                        {{ $addon->addon_name }} </label><span
                                                        class="text-sm text-accent font-medium">
                                                        ₱{{ number_format($addon->addon_price, 2) }} </span>
                                                </div>

                                            </div>

                                        </div>

                                        <div class="mt-3 flex items-center justify-center">

                                            <button type="button"
                                                class="qty-decrease bg-background border border-border-color rounded-l px-3 py-1 text-sm border-r border-border-color hover:bg-card-bg text-secondary-text hover:text-primary-text transition-colors">-</button>

                                            <input type="number"
                                                id="addon-qty-{{ $addon->addon_id }}"
                                                name="addon_qty[{{ $addon->addon_id }}]" min="1"
                                                value="{{ isset($selectedAddons[$addon->addon_id]) ? $selectedAddons[$addon->addon_id] : 1 }}"
                                                class="addon-qty w-16 px-2 py-1 text-center text-sm border-y border-border-color bg-card-bg text-primary-text">
                                            <button type="button"
                                                class="qty-increase bg-background border border-border-color rounded-r px-3 py-1 text-sm border-l border-border-color hover:bg-card-bg text-secondary-text hover:text-primary-text transition-colors">+</button>

                                        </div>

                                    </div>
                                @endforeach

                            </div>

                        </div>

                    </div>
                    <!-- Financial Details Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-6 py-4">

                            <h2 class="text-lg font-semibold text-white flex items-center"><svg class="w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg> Financial Details </h2>

                            <p class="text-white/80 text-sm">Configure pricing and payment amounts</p>

                        </div>

                        <div class="p-6">
                            <!-- Cost Breakdown -->
                            <div class="mb-6 p-4 bg-background border border-border-color rounded-lg">

                                <h3 class="text-sm font-semibold text-primary-text mb-3">Cost Breakdown</h3>

                                <div class="space-y-2 text-sm">

                                    <div class="flex justify-between">
                                        <span class="text-secondary-text">Package:</span><span class="font-medium text-primary-text"
                                            id="package-cost-display">₱{{ number_format($booking->package ? $booking->package->price : 0, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-secondary-text">Addons:</span><span class="font-medium text-primary-text"
                                            id="addons-total-display">₱0.00</span>
                                    </div>

                                    <div class="flex justify-between pt-2 border-t border-border-color">
                                        <span class="font-semibold text-primary-text">Total:</span><span
                                            class="font-bold text-accent" id="total-cost-display">₱0.00</span>
                                    </div>

                                </div>

                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <label for="total_amount" class="block text-sm font-medium text-secondary-text mb-2">
                                        Total Amount (₱) <span class="text-red-500">*</span></label>
                                    <div class="relative">

                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-secondary-text">₱</span>
                                        </div>

                                        <input type="number" id="total_amount" name="total_amount"
                                            value="{{ old('total_amount', $booking->total_amount) }}"
                                            step="0.01" min="0" required
                                            class="w-full pl-7 px-3 py-2 bg-background border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent text-primary-text">
                                    </div>

                                    <p class="mt-1 text-xs text-secondary-text">Automatically calculated based on package
                                        and addons</p>

                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- Notes Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-6 py-4">

                            <h2 class="text-lg font-semibold text-white flex items-center"><svg class="w-5 h-5 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg> Additional Notes </h2>

                            <p class="text-white/80 text-sm">Any special requests or important information</p>

                        </div>

                        <div class="p-6">

                            <textarea id="notes" name="notes" rows="4"
                                placeholder="Enter any additional notes or special requests here..."
                                class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text text-sm">{{ old('notes', $booking->notes) }}</textarea>

                        </div>

                    </div>

                </div>
                <!-- Right Column - Payment Status & Actions -->
                <div class="xl:col-span-1 space-y-6">
                    <!-- Payment Status Card -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm overflow-hidden">

                        <div class="bg-gradient-to-r from-accent to-accent-hover px-4 py-3">

                            <h3 class="text-lg font-semibold text-white flex items-center"><svg class="w-4 h-4 mr-2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg> Payment Tracking </h3>

                        </div>

                        <div class="p-4">
                            @php
                                // Only count successful payments (PAID/SETTLED or onsite payments)
                                $totalPaid = $booking->payments
                                    ->where(function($payment) {
                                        return in_array($payment->xendit_status, ['PAID', 'SETTLED']) || 
                                               in_array($payment->payment_method, ['onsite_cash', 'onsite_card']);
                                    })
                                    ->sum('amount');
                                $balance = $booking->total_amount - $totalPaid;
                            @endphp <!-- Payment Status Badge -->
                            <div class="text-center mb-4">

                                @if ($balance <= 0)
                                    <div
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-success/10 text-success border border-success/20">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg> 
                                        Fully Paid
                                    </div>
                                @else
                                    <div
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-danger/10 text-danger border border-danger/20">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg> 
                                        Outstanding Balance
                                    </div>
                                @endif

                            </div>
                            <!-- Payment Summary -->
                            <div class="space-y-2 text-sm">

                                <div class="flex justify-between">
                                    <span class="text-secondary-text">Total Amount:</span><span
                                        class="font-medium">₱{{ number_format($booking->total_amount, 2) }}</span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-secondary-text">Amount Paid:</span><span
                                        class="font-medium text-success">₱{{ number_format($totalPaid, 2) }}</span>
                                </div>

                                <div class="flex justify-between pt-2 border-t border-border-color">
                                    <span class="text-secondary-text">Balance:</span><span
                                        class="font-bold {{ $balance <= 0 ? 'text-success' : 'text-danger' }}">
                                        ₱{{ number_format($balance, 2) }} </span>
                                </div>

                            </div>
                            <!-- Quick Payment Status Toggle -->
                            <div class="mt-4 pt-4 border-t border-border-color">

                                <h4 class="text-sm font-medium text-primary-text mb-2">Payment Status</h4>

                                <div class="flex flex-col space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_status" value="unpaid"
                                            {{ old('payment_status', $booking->payment_status) == 'unpaid' ? 'checked' : '' }}
                                            class="mr-2 text-accent focus:ring-accent">
                                        <span class="text-sm text-danger">Unpaid</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_status" value="paid"
                                            {{ old('payment_status', $booking->payment_status) == 'paid' ? 'checked' : '' }} 
                                            class="mr-2 text-accent focus:ring-accent">
                                        <span class="text-sm text-success">Paid</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="payment_status" value="refunded"
                                            {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'checked' : '' }} 
                                            class="mr-2 text-accent focus:ring-accent">
                                        <span class="text-sm text-warning">Refunded</span>
                                    </label>
                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- Quick Actions -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm">

                        <div class="px-4 py-3 bg-gradient-to-r from-accent to-accent-hover border-b border-border-color">

                            <h3 class="text-lg font-semibold text-white">Actions</h3>

                        </div>

                        <div class="p-4 space-y-2">

                            <button type="button" id="add-payment-btn"
                                class="w-full flex justify-center items-center bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-3 py-2 rounded-md transition-all duration-200 font-medium text-sm hover:border-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg> 
                                Record Full Payment
                            </button>

                            <button type="button" id="generate-invoice-btn"
                                class="w-full flex justify-center items-center bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 px-3 py-2 rounded-md transition-all duration-200 font-medium text-sm hover:border-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg> 
                                Generate Invoice
                            </button>

                        </div>

                    </div>
                    <!-- Booking Timeline -->
                    <div class="bg-card-bg border border-border-color rounded-lg shadow-sm">

                        <div class="px-4 py-3 bg-gradient-to-r from-accent to-accent-hover border-b border-border-color">

                            <h3 class="text-lg font-semibold text-white">Timeline</h3>

                        </div>

                        <div class="p-4">

                            <div class="space-y-3 text-xs">

                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-info rounded-full mr-2"></div>
                                    <span class="text-secondary-text">Created:
                                        {{ $booking->created_at->format('M d, Y H:i') }}</span>
                                </div>

                                @if ($booking->updated_at->gt($booking->created_at))
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-warning rounded-full mr-2"></div>
                                        <span class="text-secondary-text">Updated:
                                            {{ $booking->updated_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

        <!-- Add Payment Modal -->
        <div id="payment-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-card-bg rounded-lg shadow-xl max-w-md w-full">
                <div class="flex items-center justify-between p-4 border-b border-border-color">
                    <h3 class="text-lg font-semibold text-primary-text">Record Full Payment</h3>
                    <button type="button" id="close-payment-modal" class="text-secondary-text hover:text-primary-text">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="payment-form" action="{{ route('bookings.addPayment', $booking) }}" method="POST">
                    @csrf
                    <div class="p-4 space-y-4">
                        <!-- Payment Amount Display -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Full Payment Amount</p>
                                    <p class="text-xs text-blue-700">Only full payments are accepted</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-bold text-blue-900">₱{{ number_format($booking->total_amount, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-secondary-text mb-2">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select id="payment_method" name="payment_method" required
                                class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text">
                                <option value="">Select Payment Method</option>
                                @if(\App\Models\SystemSetting::getValue('payment_integration_enabled', false))
                                <option value="gcash">GCash (Online)</option>
                                @endif
                                <option value="onsite_cash">Cash (Onsite)</option>
                                <option value="onsite_card">Card (Onsite)</option>
                            </select>
                        </div>

                        <div>
                            <label for="payment_notes" class="block text-sm font-medium text-secondary-text mb-2">
                                Notes (Optional)
                            </label>
                            <textarea id="payment_notes" name="notes" rows="3"
                                class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text"
                                placeholder="Enter any additional notes..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 p-4 border-t border-border-color">
                        <button type="button" id="cancel-payment-btn"
                            class="px-4 py-2 bg-background border border-border-color rounded-md hover:bg-card-bg text-secondary-text hover:text-primary-text transition-all duration-200 font-medium text-sm">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-md transition-all duration-200 font-medium text-sm">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Fixed Action Footer -->
        <div class="fixed bottom-0 left-0 lg:left-64 right-0 bg-card-bg border-t-2 border-border-color shadow-2xl z-50">
            <div class="px-4 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                    <a href="{{ route('bookings.index') }}"
                        class="inline-flex items-center justify-center px-6 py-2.5 bg-background border border-border-color rounded-md hover:bg-card-bg text-secondary-text hover:text-primary-text transition-all duration-200 font-medium text-sm w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" form="booking-form"
                        class="inline-flex items-center justify-center px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-md transition-all duration-200 font-medium text-sm shadow-md hover:shadow-lg w-full sm:w-auto">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Booking
                    </button>
                </div>
            </div>
        </div>

        <!-- Xendit Payment Link Modal -->
        <div id="xendit-link-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-card-bg rounded-lg shadow-xl max-w-lg w-full">
                <div class="flex items-center justify-between p-4 border-b border-border-color bg-gradient-to-r from-accent to-accent-hover">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        GCash Payment Link Created
                    </h3>
                    <button type="button" id="close-xendit-modal" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6">
                    <div class="mb-4">
                        <p class="text-sm text-secondary-text mb-2">Share this payment link with the client:</p>
                        <div class="flex items-center gap-2">
                            <input type="text" id="xendit-payment-url" readonly
                                class="flex-1 px-3 py-2 border border-border-color rounded-md bg-background text-primary-text text-sm"
                                value="">
                            <button type="button" id="copy-xendit-link" 
                                class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-md transition-all duration-200 font-medium text-sm flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Copy
                            </button>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 mb-1">Instructions</h4>
                                <ul class="text-xs text-blue-800 space-y-1">
                                    <li>• Copy the link and send it to the client via email or messenger</li>
                                    <li>• The client can pay using their GCash account</li>
                                    <li>• Payment link expires in 24 hours</li>
                                    <li>• Payment status will update automatically when paid</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" id="send-via-email" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 transition-all duration-200 font-medium text-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send via Email
                        </button>
                        <button type="button" id="close-xendit-modal-btn" 
                            class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-md transition-all duration-200 font-medium text-sm">
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notification Container -->
        <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

        <!-- Confirmation Modal -->
        <div id="confirm-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-card-bg rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-primary-text" id="confirm-title">Confirm Action</h3>
                            <p class="mt-2 text-sm text-secondary-text" id="confirm-message">Are you sure?</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-border-color bg-gray-50">
                    <button type="button" id="confirm-cancel-btn"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700 transition-all duration-200 font-medium text-sm">
                        Cancel
                    </button>
                    <button type="button" id="confirm-ok-btn"
                        class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-md transition-all duration-200 font-medium text-sm">
                        Confirm
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toast Notification System
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

        // Confirmation Modal System
        function showConfirmDialog(title, message, onConfirm) {
            const modal = document.getElementById('confirm-modal');
            const titleEl = document.getElementById('confirm-title');
            const messageEl = document.getElementById('confirm-message');
            const okBtn = document.getElementById('confirm-ok-btn');
            const cancelBtn = document.getElementById('confirm-cancel-btn');
            
            titleEl.textContent = title;
            messageEl.textContent = message;
            modal.classList.remove('hidden');
            
            // Remove previous event listeners
            const newOkBtn = okBtn.cloneNode(true);
            const newCancelBtn = cancelBtn.cloneNode(true);
            okBtn.parentNode.replaceChild(newOkBtn, okBtn);
            cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
            
            // Add new event listeners
            newOkBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                onConfirm();
            });
            
            newCancelBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });
            
            // Close on backdrop click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        }

        // Backdrop selection handling
        const backdropSelect = document.getElementById('backdrop_id');
        const customBackdropField = document.getElementById('custom-backdrop-field');
        
        if (backdropSelect) {
            backdropSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customBackdropField.classList.remove('hidden');
                } else {
                    customBackdropField.classList.add('hidden');
                }
            });
        }

        // Show flash messages as toasts
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif
        
        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
        
        @if(session('warning'))
            showToast('{{ session('warning') }}', 'warning');
        @endif
        
        @if(session('info'))
            showToast('{{ session('info') }}', 'info');
        @endif

        // Show Xendit payment URL modal if available
        @if(session('payment_url'))
            setTimeout(() => {
                const paymentUrl = '{{ session('payment_url') }}';
                const xenditModal = document.getElementById('xendit-link-modal');
                const xenditUrlInput = document.getElementById('xendit-payment-url');
                
                xenditUrlInput.value = paymentUrl;
                xenditModal.classList.remove('hidden');
            }, 500);
        @endif

        // Xendit Modal Controls
        const xenditModal = document.getElementById('xendit-link-modal');
        const closeXenditModalBtns = document.querySelectorAll('#close-xendit-modal, #close-xendit-modal-btn');
        const copyXenditLinkBtn = document.getElementById('copy-xendit-link');
        const sendViaEmailBtn = document.getElementById('send-via-email');

        closeXenditModalBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                xenditModal.classList.add('hidden');
            });
        });

        // Close on backdrop click
        xenditModal?.addEventListener('click', function(e) {
            if (e.target === xenditModal) {
                xenditModal.classList.add('hidden');
            }
        });

        // Copy payment link
        copyXenditLinkBtn?.addEventListener('click', function() {
            const urlInput = document.getElementById('xendit-payment-url');
            urlInput.select();
            navigator.clipboard.writeText(urlInput.value).then(() => {
                showToast('Payment link copied to clipboard!', 'success');
                // Change button text temporarily
                const originalHtml = this.innerHTML;
                this.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Copied!
                `;
                this.classList.add('bg-green-600');
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.classList.remove('bg-green-600');
                }, 2000);
            });
        });

        // Send via email (optional feature)
        sendViaEmailBtn?.addEventListener('click', function() {
            const urlInput = document.getElementById('xendit-payment-url');
            const paymentUrl = urlInput.value;
            const bookingId = {{ $booking->booking_id }};
            const amount = {{ $booking->total_amount }};
            
            if (!paymentUrl) {
                showToast('No payment link available', 'error');
                return;
            }
            
            // Disable button and show loading state
            sendViaEmailBtn.disabled = true;
            sendViaEmailBtn.textContent = 'Sending...';
            
            // Send email via API
            fetch('{{ route('payment-transactions.send-payment-link') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    payment_url: paymentUrl,
                    amount: amount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Failed to send email', 'error');
                }
            })
            .catch(error => {
                console.error('Error sending email:', error);
                showToast('Failed to send email. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable button
                sendViaEmailBtn.disabled = false;
                sendViaEmailBtn.textContent = '📧 Send via Email';
            });
        });

        // Form submission handler - update totals before submitting
        const bookingForm = document.getElementById('booking-form');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(e) {
                // Update totals one more time before submission
                updateTotalAmount();
            });
        }

        // Addon quantity controls
        document.addEventListener('click', function(e) {
            // Decrease quantity
            if (e.target.classList.contains('qty-decrease')) {
                const input = e.target.nextElementSibling;
                const currentValue = parseInt(input.value) || 1;
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    updateTotalAmount();
                }
            }
            
            // Increase quantity
            if (e.target.classList.contains('qty-increase')) {
                const input = e.target.previousElementSibling;
                const currentValue = parseInt(input.value) || 1;
                input.value = currentValue + 1;
                updateTotalAmount();
            }
        });

        // Addon checkbox and input handling
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('addon-checkbox')) {
                updateTotalAmount();
            }
            
            if (e.target.id === 'package_id') {
                updateTotalAmount();
            }
            
            // Update total when quantity input is manually changed
            if (e.target.classList.contains('addon-qty')) {
                updateTotalAmount();
            }
        });

        function updateTotalAmount() {
            let packagePrice = 0;
            let addonTotal = 0;

            // Get package price
            const packageSelect = document.getElementById('package_id');
            if (packageSelect && packageSelect.value) {
                const selectedOption = packageSelect.options[packageSelect.selectedIndex];
                packagePrice = parseFloat(selectedOption.dataset.price || 0);
            }

            // Calculate addon total
            const addonCheckboxes = document.querySelectorAll('.addon-checkbox:checked');
            addonCheckboxes.forEach(checkbox => {
                const addonId = checkbox.value;
                const qtyInput = document.getElementById(`addon-qty-${addonId}`);
                const quantity = parseInt(qtyInput.value) || 1;

                // Get addon price from the data attribute on the addon-item container
                const addonItem = checkbox.closest('.addon-item');
                if (addonItem) {
                    const price = parseFloat(addonItem.dataset.addonPrice || 0);
                    
                    if (!isNaN(price) && !isNaN(quantity) && price > 0) {
                        addonTotal += price * quantity;
                    }
                }
            });

            const totalAmount = packagePrice + addonTotal;

            // Update displays
            const packageCostDisplay = document.getElementById('package-cost-display');
            const addonsTotalDisplay = document.getElementById('addons-total-display');
            const totalCostDisplay = document.getElementById('total-cost-display');
            const totalAmountInput = document.getElementById('total_amount');

            if (packageCostDisplay) {
                packageCostDisplay.textContent = `₱${packagePrice.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
            if (addonsTotalDisplay) {
                addonsTotalDisplay.textContent = `₱${addonTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
            if (totalCostDisplay) {
                totalCostDisplay.textContent = `₱${totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
            }
            if (totalAmountInput) {
                totalAmountInput.value = totalAmount.toFixed(2);
            }
        }

        // Initialize calculation on page load
        // Use setTimeout to ensure DOM is fully rendered
        setTimeout(function() {
            updateTotalAmount();
        }, 100);

        // Payment Modal Handling
        const paymentModal = document.getElementById('payment-modal');
        const addPaymentBtn = document.getElementById('add-payment-btn');
        const closePaymentModal = document.getElementById('close-payment-modal');
        const cancelPaymentBtn = document.getElementById('cancel-payment-btn');

        if (addPaymentBtn) {
            addPaymentBtn.addEventListener('click', function() {
                paymentModal.classList.remove('hidden');
            });
        }

        if (closePaymentModal) {
            closePaymentModal.addEventListener('click', function() {
                paymentModal.classList.add('hidden');
            });
        }

        if (cancelPaymentBtn) {
            cancelPaymentBtn.addEventListener('click', function() {
                paymentModal.classList.add('hidden');
            });
        }

        // Close modal when clicking outside
        paymentModal?.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
            }
        });

        // Generate Invoice Button Handler
        const generateInvoiceBtn = document.getElementById('generate-invoice-btn');
        if (generateInvoiceBtn) {
            generateInvoiceBtn.addEventListener('click', function() {
                const btn = this;
                
                showConfirmDialog(
                    'Send Invoice',
                    'Send invoice to {{ $booking->client_email }}?',
                    function() {
                        // Show loading state
                        const originalText = btn.innerHTML;
                        btn.disabled = true;
                        btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Sending...';
                        
                        // Send AJAX request
                        fetch('{{ route('bookings.generateInvoice', $booking) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            // Get the response text first
                            return response.text().then(text => {
                                // Try to parse as JSON
                                try {
                                    const data = JSON.parse(text);
                                    return { ok: response.ok, status: response.status, data: data };
                                } catch (e) {
                                    // If parsing fails, it's likely HTML error page
                                    console.error('Server returned non-JSON response:', text);
                                    throw new Error('Server error. Please check the logs.');
                                }
                            });
                        })
                        .then(result => {
                            if (result.ok && result.data.success) {
                                showToast('Invoice sent successfully to {{ $booking->client_email }}!', 'success');
                            } else {
                                showToast(result.data.message || 'Failed to send invoice', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast(error.message || 'Failed to send invoice. Please try again.', 'error');
                        })
                        .finally(() => {
                            btn.disabled = false;
                            btn.innerHTML = originalText;
                        });
                    }
                );
            });
        }
    });
</script>
@endsection
