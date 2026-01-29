@extends('layouts.app')
@section('title', 'Create New Booking')
@section('content')
    <!-- Alert Container for Dynamic Alerts -->
    <div id="alert-container" class="fixed top-4 right-4 z-50 space-y-3" style="max-width: 400px;"></div>

    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-primary-text mb-2">Create New Booking</h1>
                    <p class="text-secondary-text text-sm">Fill in the details below to create a new booking</p>
                </div>
                <a href="{{ route('bookings.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition-all duration-200 shadow-sm border border-border-color hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Bookings
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 mb-6 shadow-sm" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h3 class="text-red-800 font-semibold text-sm mb-2">Please correct the following errors:</h3>
                        <ul class="list-disc list-inside space-y-1 text-red-700 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="bg-card-bg rounded-xl shadow-lg border border-border-color overflow-hidden">
            <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
                @csrf
                
                <!-- Client Information Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Client Information</h2>
                            <p class="text-sm text-gray-600">Enter the client's contact details</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="client_first_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="client_first_name" name="client_first_name" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400"
                                    placeholder="Enter first name" value="{{ old('client_first_name') }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="client_last_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" id="client_last_name" name="client_last_name" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400"
                                    placeholder="Enter last name" value="{{ old('client_last_name') }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="client_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input type="email" id="client_email" name="client_email" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400"
                                    placeholder="client@example.com" value="{{ old('client_email') }}">
                            </div>
                        </div>

                        <div>
                            <label for="client_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="tel" id="client_phone" name="client_phone" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400"
                                    placeholder="+63 912 345 6789" value="{{ old('client_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Booking Details Section -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-t border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Booking Details</h2>
                            <p class="text-sm text-gray-600">Select package, staff, and schedule</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="lg:col-span-2">
                            <label for="package_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Package <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="package_id" name="package_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900 appearance-none cursor-pointer" 
                                    required>
                                    <option value="" disabled selected>Select a package</option>
                                    @foreach ($packages as $package)
                                        <option value="{{ $package->package_id }}" 
                                            data-price="{{ $package->price }}"
                                            data-inclusions="{{ $package->inclusions->pluck('inclusion_text')->toJson() }}"
                                            data-free-items="{{ $package->freeItems->pluck('free_item_text')->toJson() }}"
                                            {{ old('package_id') == $package->package_id ? 'selected' : '' }}>
                                            {{ $package->title }} - ₱{{ number_format($package->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <!-- Package Details Section -->
                        <div id="package-details" class="lg:col-span-2 hidden">
                            <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6 shadow-inner">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-indigo-900">Package Details</h3>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-6">
                                    <!-- Package Inclusions -->
                                    <div id="package-inclusions">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <h4 class="text-sm font-bold text-indigo-800 uppercase tracking-wide">Inclusions</h4>
                                        </div>
                                        <ul id="inclusions-list" class="space-y-2">
                                            <!-- Inclusions will be populated here -->
                                        </ul>
                                    </div>
                                    
                                    <!-- Package Free Items -->
                                    <div id="package-free-items">
                                        <div class="flex items-center mb-3">
                                            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                            </svg>
                                            <h4 class="text-sm font-bold text-green-800 uppercase tracking-wide">Free Items</h4>
                                        </div>
                                        <ul id="free-items-list" class="space-y-2">
                                            <!-- Free items will be populated here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="primary_staff_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Primary Staff
                            </label>
                            <div class="relative">
                                <select id="primary_staff_id" name="primary_staff_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900 appearance-none cursor-pointer">
                                    <option value="" disabled selected>Select staff member</option>
                                    @foreach ($staff as $staffMember)
                                        <option value="{{ $staffMember->staff_id }}"
                                            {{ old('primary_staff_id') == $staffMember->staff_id ? 'selected' : '' }}>
                                            {{ $staffMember->first_name }} {{ $staffMember->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="backdrop_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Backdrop (Optional)
                            </label>
                            <div class="relative">
                                <select id="backdrop_id" name="backdrop_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900 appearance-none cursor-pointer">
                                    <option value="">No Backdrop</option>
                                    @foreach ($backdrops as $backdrop)
                                        <option value="{{ $backdrop->backdrop_id }}"
                                            {{ old('backdrop_id') == $backdrop->backdrop_id ? 'selected' : '' }}>
                                            {{ $backdrop->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="booking_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <input type="date" id="booking_date" name="booking_date" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
                                    value="{{ old('booking_date') }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                Status
                            </label>
                            <div class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-600">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Pending (will auto-confirm when paid)
                                </span>
                            </div>
                            <input type="hidden" name="status" value="pending">
                            <p class="text-xs text-gray-500 mt-1">
                                Status will automatically change to "Confirmed" once payment is received
                            </p>
                        </div>

                        <div>
                            <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input type="time" id="start_time" name="start_time" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
                                    value="{{ old('start_time') }}" required>
                            </div>
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input type="time" id="end_time" name="end_time" 
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white text-gray-900"
                                    value="{{ old('end_time') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add-ons Section -->
                <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-t border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Add-ons</h2>
                            <p class="text-sm text-gray-600">Select additional services (optional)</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="space-y-3">
                        @foreach ($addons as $addon)
                            <div class="bg-white border-2 border-gray-200 rounded-lg p-4 hover:border-green-300 hover:shadow-md transition-all duration-200">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-start flex-1">
                                        <div class="flex items-center h-5 mt-1">
                                            <input id="addon-{{ $addon->addon_id }}" 
                                                name="addons[]" 
                                                value="{{ $addon->addon_id }}" 
                                                type="checkbox"
                                                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2 cursor-pointer addon-checkbox" 
                                                data-price="{{ $addon->addon_price }}"
                                                {{ in_array($addon->addon_id, old('addons', [])) ? 'checked' : '' }}>
                                        </div>
                                        <label for="addon-{{ $addon->addon_id }}" class="ml-3 cursor-pointer flex-1">
                                            <div class="font-semibold text-gray-900 text-base">{{ $addon->addon_name }}</div>
                                            <div class="flex items-center mt-1">
                                                <svg class="w-4 h-4 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span class="text-green-700 font-bold">₱{{ number_format($addon->addon_price, 2) }}</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center bg-gray-50 rounded-lg px-3 py-2 border border-gray-300">
                                        <label for="addon_qty_{{ $addon->addon_id }}" class="text-sm font-medium text-gray-700 mr-2 whitespace-nowrap">Qty:</label>
                                        <input type="number"
                                            id="addon_qty_{{ $addon->addon_id }}"
                                            name="addon_qty[{{ $addon->addon_id }}]" 
                                            min="1" 
                                            max="99"
                                            value="{{ old('addon_qty.' . $addon->addon_id, 1) }}"
                                            class="w-16 px-2 py-1 text-center border border-gray-300 rounded focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white text-gray-900 font-semibold addon-qty" 
                                            disabled>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Booking Summary Section -->
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 border-b border-t border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Booking Summary</h2>
                            <p class="text-sm text-gray-600">Review your booking costs</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-6 border-2 border-amber-200 shadow-inner">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-amber-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">Package Cost:</span>
                                </div>
                                <span id="package-cost" class="text-gray-900 font-bold text-lg">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center pb-3 border-b border-amber-200">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-700 font-medium">Add-ons Cost:</span>
                                </div>
                                <span id="addons-cost" class="text-gray-900 font-bold text-lg">₱0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-amber-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-gray-800 font-bold text-lg">Total Amount:</span>
                                </div>
                                <span id="total-amount" class="text-amber-700 font-bold text-2xl">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Notes Section -->
                <div class="bg-gradient-to-r from-slate-50 to-gray-50 px-6 py-4 border-b border-t border-border-color">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Additional Notes</h2>
                            <p class="text-sm text-gray-600">Any special requests or information</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <textarea id="notes" name="notes" rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-500 focus:border-slate-500 transition-all duration-200 bg-white text-gray-900 placeholder-gray-400 resize-none"
                        placeholder="Add any additional notes, special requests, or important information about this booking...">{{ old('notes') }}</textarea>
                </div>

                <!-- Action Buttons -->
                <div class="bg-gray-50 px-6 py-5 border-t border-border-color flex flex-col sm:flex-row justify-end gap-3">
                    <a href="{{ route('bookings.index') }}" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition-all duration-200 shadow-sm border-2 border-gray-300 hover:border-gray-400">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const packageSelect = document.getElementById('package_id');
    const backdropSelect = document.getElementById('backdrop_id');
    const customBackdropDiv = document.getElementById('custom_backdrop_div');
    const addonCheckboxes = document.querySelectorAll('.addon-checkbox');
    const addonQtyInputs = document.querySelectorAll('.addon-qty');
    const packageDetails = document.getElementById('package-details');
    const inclusionsList = document.getElementById('inclusions-list');
    const freeItemsList = document.getElementById('free-items-list');

    // Alert utility functions
    function showAlert(message, type = 'error') {
        const alertContainer = document.getElementById('alert-container');
        const alertId = 'alert-' + Date.now();
        
        const alertColors = {
            error: 'bg-red-50 border-red-500 text-red-800',
            success: 'bg-green-50 border-green-500 text-green-800',
            warning: 'bg-amber-50 border-amber-500 text-amber-800',
            info: 'bg-blue-50 border-blue-500 text-blue-800'
        };
        
        const alertIcons = {
            error: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>',
            success: '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>',
            warning: '<path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>',
            info: '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>'
        };
        
        const alertDiv = document.createElement('div');
        alertDiv.id = alertId;
        alertDiv.className = `${alertColors[type]} border-l-4 rounded-lg p-4 shadow-lg transform transition-all duration-300 ease-in-out translate-x-0 opacity-100`;
        alertDiv.innerHTML = `
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    ${alertIcons[type]}
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-sm">${message}</p>
                </div>
                <button onclick="dismissAlert('${alertId}')" class="ml-3 flex-shrink-0 hover:opacity-70 transition-opacity">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        `;
        
        alertContainer.appendChild(alertDiv);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => dismissAlert(alertId), 5000);
    }
    
    window.dismissAlert = function(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.style.transform = 'translateX(100%)';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }
    };

    function showValidationErrors(errors) {
        const errorMessages = errors.join('<br>• ');
        showAlert('Please fix the following errors:<br>• ' + errorMessages, 'error');
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
    });

    function validateForm() {
        const errors = [];
        
        // Validate client information
        const firstName = document.getElementById('client_first_name').value.trim();
        const lastName = document.getElementById('client_last_name').value.trim();
        const email = document.getElementById('client_email').value.trim();
        const phone = document.getElementById('client_phone').value.trim();
        
        if (!firstName || !/^[a-zA-Z\s]+$/.test(firstName)) {
            errors.push('Please enter a valid first name (letters and spaces only)');
        }
        
        if (!lastName || !/^[a-zA-Z\s]+$/.test(lastName)) {
            errors.push('Please enter a valid last name (letters and spaces only)');
        }
        
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('Please enter a valid email address');
        }
        
        if (phone && !/^[\d\s\-\+\(\)]+$/.test(phone)) {
            errors.push('Please enter a valid phone number');
        }

        // Validate booking details
        const packageId = packageSelect.value;
        const staffId = document.getElementById('primary_staff_id').value;
        const bookingDate = document.getElementById('booking_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (!packageId) {
            errors.push('Please select a package');
        }
        
        if (!bookingDate) {
            errors.push('Please select a booking date');
        } else {
            const selectedDate = new Date(bookingDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('Booking date cannot be in the past');
            }
        }
        
        if (!startTime || !endTime) {
            errors.push('Please select both start and end times');
        } else {
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            
            if (end <= start) {
                errors.push('End time must be after start time');
            }
            
            const diffMinutes = (end - start) / (1000 * 60);
            if (diffMinutes < 60) {
                errors.push('Booking duration must be at least 1 hour');
            }
            
            if (start.getHours() < 8 || end.getHours() > 22) {
                errors.push('Bookings are only allowed between 8:00 AM and 10:00 PM');
            }
        }

        if (errors.length > 0) {
            showValidationErrors(errors);
            // Scroll to top to see the alert
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        }
        
        return true;
    }

    // Handle backdrop selection
    if (backdropSelect) {
        backdropSelect.addEventListener('change', function() {
            if (customBackdropDiv) {
                customBackdropDiv.style.display = this.value === 'custom' ? 'block' : 'none';
            }
        });
        
        // Initialize backdrop display
        if (backdropSelect.value === 'custom' && customBackdropDiv) {
            customBackdropDiv.style.display = 'block';
        }
    }

    // Handle package selection and display inclusions/free items
    packageSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value && packageDetails) {
            packageDetails.classList.remove('hidden');
            
            // Get inclusions and free items data
            const inclusions = JSON.parse(selectedOption.dataset.inclusions || '[]');
            const freeItems = JSON.parse(selectedOption.dataset.freeItems || '[]');
            
            // Populate inclusions
            if (inclusionsList) {
                inclusionsList.innerHTML = '';
                if (inclusions.length > 0) {
                    inclusions.forEach(function(inclusion) {
                        const li = document.createElement('li');
                        li.className = 'flex items-start text-sm';
                        li.innerHTML = `
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 text-indigo-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-indigo-900 font-medium">${inclusion}</span>
                        `;
                        inclusionsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.className = 'text-sm text-gray-500 italic pl-7';
                    li.textContent = 'No inclusions specified';
                    inclusionsList.appendChild(li);
                }
            }
            
            // Populate free items
            if (freeItemsList) {
                freeItemsList.innerHTML = '';
                if (freeItems.length > 0) {
                    freeItems.forEach(function(freeItem) {
                        const li = document.createElement('li');
                        li.className = 'flex items-start text-sm';
                        li.innerHTML = `
                            <svg class="w-5 h-5 mr-2 flex-shrink-0 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-green-900 font-medium">${freeItem}</span>
                        `;
                        freeItemsList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.className = 'text-sm text-gray-500 italic pl-7';
                    li.textContent = 'No free items included';
                    freeItemsList.appendChild(li);
                }
            }
        } else if (packageDetails) {
            packageDetails.classList.add('hidden');
        }
        updateBookingSummary();
    });

    // Initialize package details on page load if package is already selected
    if (packageSelect.value) {
        packageSelect.dispatchEvent(new Event('change'));
    }

    // Handle addon quantity inputs
    addonCheckboxes.forEach(function(checkbox) {
        const addonId = checkbox.value;
        const qtyInput = document.getElementById('addon_qty_' + addonId);
        
        if (qtyInput) {
            checkbox.addEventListener('change', function() {
                qtyInput.disabled = !this.checked;
                if (!this.checked) {
                    qtyInput.value = 1;
                }
                updateBookingSummary();
            });
            
            if (checkbox.checked) {
                qtyInput.disabled = false;
            }
        }
    });

    // Handle quantity changes
    addonQtyInputs.forEach(function(input) {
        input.addEventListener('change', updateBookingSummary);
    });

    function updateBookingSummary() {
        let packageCost = 0;
        let addonsCost = 0;

        // Get package cost
        const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
        if (selectedPackage && selectedPackage.dataset.price) {
            packageCost = parseFloat(selectedPackage.dataset.price);
        }

        // Calculate addons cost
        addonCheckboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                const addonPrice = parseFloat(checkbox.dataset.price) || 0;
                const addonId = checkbox.value;
                const qtyInput = document.getElementById('addon_qty_' + addonId);
                const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;
                addonsCost += addonPrice * qty;
            }
        });

        const totalAmount = packageCost + addonsCost;

        // Update display with animation
        const packageCostEl = document.getElementById('package-cost');
        const addonsCostEl = document.getElementById('addons-cost');
        const totalAmountEl = document.getElementById('total-amount');
        
        if (packageCostEl) {
            packageCostEl.textContent = '₱' + packageCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        if (addonsCostEl) {
            addonsCostEl.textContent = '₱' + addonsCost.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
        if (totalAmountEl) {
            totalAmountEl.textContent = '₱' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            // Add pulse animation when total changes
            totalAmountEl.classList.add('animate-pulse');
            setTimeout(() => totalAmountEl.classList.remove('animate-pulse'), 500);
        }
    }

    // Initialize summary on page load
    updateBookingSummary();
});
</script> @endsection
