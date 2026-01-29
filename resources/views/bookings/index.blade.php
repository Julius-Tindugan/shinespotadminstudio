@extends('layouts.app')
@section('title', 'Bookings Management')
@section('content')
    <div class="container mx-auto px-6 py-8 max-w-[1800px]">

        <!-- Enhanced Page Header -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-primary-text font-heading tracking-tight">Bookings Management</h1>
                <p class="text-secondary-text mt-2 text-sm">View, manage, and track all studio bookings in one place</p>
            </div>
            <a href="{{ route('bookings.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 bg-accent hover:bg-accent-hover text-white font-medium rounded-xl transition-all duration-200 shadow-lg shadow-accent/20 hover:shadow-xl hover:shadow-accent/30 hover:-translate-y-0.5 btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                <span>Add New Booking</span>
            </a>
        </div>

        <!-- Success/Error Notifications -->
        @if (session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl mb-6 shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-800 px-6 py-4 rounded-xl mb-6 shadow-sm"
                role="alert">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="ml-3 font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif
        @php $activeFilters = request()->filled('search') || (request()->has('status') && request()->get('status') != 'all') || (request()->has('filter') && request()->get('filter') != 'all'); @endphp

        <!-- Main Content Card -->
        <div class="bg-card-bg border border-border-color rounded-2xl shadow-lg overflow-hidden">

            <div class="p-6 lg:p-8">

                <!-- Compact Active Filters Indicator -->
                @if ($activeFilters)
                    <div
                        class="mb-4 flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg px-4 py-2.5">
                        <div class="flex items-center gap-2 text-sm text-blue-800">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                            <span class="font-medium">Filters:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @if (request()->has('filter') && request()->get('filter') != 'all')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-xs font-medium">
                                        {{ ucfirst(str_replace('_', ' ', request()->get('filter'))) }}
                                    </span>
                                @endif
                                @if (request()->has('status') && request()->get('status') != 'all')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-xs font-medium">
                                        {{ ucfirst(request()->get('status')) }}
                                    </span>
                                @endif
                                @if (request()->filled('search'))
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-xs font-medium">
                                        "{{ request()->get('search') }}"
                                    </span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('bookings.index') }}"
                            class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium hover:underline transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </a>
                    </div>
                @endif

                <!-- Compact Filter Form - Single Row -->
                <form id="booking-filter-form" method="GET" action="{{ route('bookings.index') }}" class="mb-6">
                    <div class="flex flex-wrap items-center gap-3">

                        <!-- Search Bar - Flexible Width -->
                        <div class="flex-1 min-w-[300px]">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-secondary-text" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                    </svg>
                                </div>
                                <input type="text" id="search" name="search"
                                    placeholder="Search by client name, reference, or status..."
                                    class="w-full pl-10 pr-5 py-2 text-sm rounded-lg border border-border-color bg-background text-primary-text placeholder-secondary-text focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all"
                                    value="{{ request()->get('search', '') }}">
                                <button type="button" id="clear-search"
                                    class="absolute inset-y-0 right-0 flex items-center pr-2.5 {{ request()->get('search') ? '' : 'hidden' }} text-secondary-text hover:text-accent transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Status Filter - Compact -->
                        <div class="w-auto">
                            <select id="statusFilter" name="status"
                                class="booking-filters pl-2.5 pr-8 py-2 text-sm rounded-lg border border-border-color bg-background text-primary-text focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all cursor-pointer">
                                <option value="all">All Status</option>
                                <option value="pending" {{ request()->get('status') == 'pending' ? 'selected' : '' }}>⏳
                                    Pending</option>
                                <option value="confirmed" {{ request()->get('status') == 'confirmed' ? 'selected' : '' }}>
                                    ✅ Confirmed</option>
                                <option value="completed" {{ request()->get('status') == 'completed' ? 'selected' : '' }}>
                                    🎉 Completed</option>
                                <option value="canceled" {{ request()->get('status') == 'canceled' ? 'selected' : '' }}>❌
                                    Canceled</option>
                            </select>
                        </div>

                        <!-- Date Filter - Compact -->
                        <div class="w-auto">
                            <select id="date-filter" name="filter"
                                class="pl-2.5 pr-8 py-2 text-sm rounded-lg border border-border-color bg-background text-primary-text focus:outline-none focus:ring-2 focus:ring-accent/50 focus:border-accent transition-all cursor-pointer">
                                <option value="all" {{ isset($filter) && $filter == 'all' ? 'selected' : '' }}>All
                                    Dates</option>
                                <option value="upcoming" {{ isset($filter) && $filter == 'upcoming' ? 'selected' : '' }}>
                                    📅 Upcoming</option>
                                <option value="today" {{ isset($filter) && $filter == 'today' ? 'selected' : '' }}>📌
                                    Today</option>
                                <option value="this_week"
                                    {{ isset($filter) && $filter == 'this_week' ? 'selected' : '' }}>📆 This Week</option>
                                <option value="this_month"
                                    {{ isset($filter) && $filter == 'this_month' ? 'selected' : '' }}>🗓️ This Month
                                </option>
                                <option value="past" {{ isset($filter) && $filter == 'past' ? 'selected' : '' }}>🕒 Past
                                </option>
                            </select>
                        </div>

                        <!-- Apply Button - Compact -->
                        <div class="w-auto">
                            <button type="submit" id="apply-filters"
                                class="px-4 py-2 {{ $activeFilters ? 'bg-green-600 hover:bg-green-700' : 'bg-accent hover:bg-accent-hover' }} text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md inline-flex items-center gap-2"
                                title="Apply Filters">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                    </path>
                                </svg>
                                <span>Filter</span>
                            </button>
                        </div>

                    </div>
                </form>

            </div>

            <!-- Table Section -->
            <div class="overflow-x-auto rounded-xl">
                <table class="min-w-full divide-y divide-border-color">
                    <thead class="bg-gradient-to-r from-accent/10 via-accent/5 to-accent/10">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    ID/Reference
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Client
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Date & Time
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    Package
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    Staff
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01">
                                        </path>
                                    </svg>
                                    Backdrop
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Addons
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                                        </path>
                                    </svg>
                                    Notes
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                        </path>
                                    </svg>
                                    Payment
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Amount
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Created
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-accent uppercase tracking-wider border-b-2 border-accent/20">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                        </path>
                                    </svg>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="bookings-list" class="bg-card-bg divide-y divide-border-color">
                        @include('bookings.partials.booking-list', [
                            'bookings' => $bookings,
                            'statuses' => $statuses,
                        ])
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            <div
                class="px-6 py-5 bg-gradient-to-r from-accent/5 via-accent/3 to-accent/5 border-t border-accent/20 flex flex-col sm:flex-row justify-between items-center gap-4 rounded-b-xl">
                <div id="bookings-count" class="text-sm text-primary-text font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Showing {{ $bookings->count() }} of {{ $bookings->total() }} booking(s)
                </div>
                <div>
                    {{ $bookings->appends(request()->except('page'))->links() }}
                </div>
            </div>

        </div>

    </div>
    </div>

    <!-- Enhanced Delete Confirmation Modal -->
    <div id="deleteConfirmationModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 transition-all duration-300">
        <div
            class="bg-card-bg rounded-2xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 scale-95 opacity-0 modal-content">

            <!-- Modal Header -->
            <div class="p-6 border-b border-border-color">
                <div class="flex items-center">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-red-100 to-rose-100 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-primary-text font-heading">Confirm Deletion</h3>
                        <p class="text-sm text-secondary-text mt-1">This action cannot be undone</p>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-primary-text leading-relaxed">
                    Are you sure you want to delete this booking? All associated data will be permanently removed from the
                    system.
                </p>
            </div>

            <!-- Modal Footer -->
            <div
                class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-border-color rounded-b-2xl flex justify-end space-x-3">
                <button id="cancelDelete"
                    class="px-6 py-2.5 bg-card-bg border-2 border-border-color hover:bg-gray-100 text-primary-text font-medium rounded-xl transition-all duration-200 hover:shadow-md">
                    Cancel
                </button>
                <button id="confirmDelete"
                    class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-medium rounded-xl transition-all duration-200 shadow-lg shadow-red-500/30 hover:shadow-xl hover:shadow-red-500/40">
                    Delete Booking
                </button>
            </div>

        </div>
    </div>
@endsection
@section('scripts')
<!-- Booking Reference Copy Functionality -->
<script src="{{ asset('js/booking-reference-copy.js') }}"></script>

<script>
    // Define the base URL for bookings index
    const bookingsIndexUrl = '{{ route('bookings.index') }}';

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

    // Auto-sync recent pending GCash payments
    function syncRecentPendingPayments() {
        // Only run if we're on the bookings page (not filtered views that wouldn't show new bookings)
        const urlParams = new URLSearchParams(window.location.search);
        const fromPayment = urlParams.get('from_payment');
        
        // Skip if user is actively filtering (they're looking for specific bookings)
        // But always run if coming from payment callback
        if (!fromPayment && (urlParams.get('search') || (urlParams.get('status') && urlParams.get('status') !== 'all'))) {
            return;
        }

        // Don't show loading indicator - make it completely silent and non-intrusive
        console.log('Auto-syncing payment statuses in background...');

        // Call the sync endpoint with a shorter timeout to avoid blocking
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout

        fetch('{{ route('finance.payments.sync-recent-pending') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            return response.json();
        })
        .then(data => {
            if (data.success && data.paid_count > 0) {
                // Show success message
                showAlert(`${data.paid_count} payment(s) confirmed! Refreshing bookings...`, 'success');
                
                // Refresh the bookings list after a short delay
                setTimeout(() => {
                    if (typeof window.refreshBookingsList === 'function') {
                        window.refreshBookingsList();
                    } else {
                        window.location.reload();
                    }
                }, 1500);
            } else {
                // Silently complete - no need to show message if no updates
                console.log('Payment sync complete:', data.message);
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            if (error.name === 'AbortError') {
                console.log('Payment sync timed out - continuing normally');
            } else {
                console.error('Error syncing payments:', error);
            }
            // Don't show error to user - fail silently to avoid disruption
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
                // Auto-sync recent pending payments when page loads
                // This ensures payment statuses are updated after Xendit redirects
                syncRecentPendingPayments();

                // Delete confirmation 
                const deleteModal = document.getElementById('deleteConfirmationModal');
                const cancelDeleteBtn = document.getElementById('cancelDelete');
                const confirmDeleteBtn = document.getElementById('confirmDelete');
                let formToSubmit = null;

                // Attach delete form handlers 
                function attachDeleteFormHandlers() {
                    const deleteButtons = document.querySelectorAll('.delete-booking-form');
                    deleteButtons.forEach(form => {
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            formToSubmit = form;
                            deleteModal.classList.remove('hidden');
                            // Trigger animation
                            setTimeout(() => {
                                const modalContent = deleteModal.querySelector(
                                '.modal-content');
                                if (modalContent) {
                                    modalContent.style.opacity = '1';
                                    modalContent.style.transform = 'scale(1)';
                                }
                            }, 10);
                        });
                    });
                }

                // Initially attach delete form handlers 
                attachDeleteFormHandlers();

                cancelDeleteBtn.addEventListener('click', function() {
                    const modalContent = deleteModal.querySelector('.modal-content');
                    if (modalContent) {
                        modalContent.style.opacity = '0';
                        modalContent.style.transform = 'scale(0.95)';
                    }
                    setTimeout(() => {
                        deleteModal.classList.add('hidden');
                        formToSubmit = null;
                    }, 200);
                });

                confirmDeleteBtn.addEventListener('click', function() {
                    if (formToSubmit) {
                        // Handle deletion via AJAX to maintain filters
                        const form = formToSubmit;
                        const url = form.action;
                        const method = form.querySelector('input[name="_method"]').value;

                        // Show loading state
                        confirmDeleteBtn.textContent = 'Deleting...';
                        confirmDeleteBtn.disabled = true;

                        fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .getAttribute('content'),
                                    'Accept': 'application/json',
                                    'X-HTTP-Method-Override': method
                                },
                                body: JSON.stringify({
                                    _method: method
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Reset button state
                                confirmDeleteBtn.textContent = 'Delete Booking';
                                confirmDeleteBtn.disabled = false;

                                // Animate modal close
                                const modalContent = deleteModal.querySelector('.modal-content');
                                if (modalContent) {
                                    modalContent.style.opacity = '0';
                                    modalContent.style.transform = 'scale(0.95)';
                                }
                                setTimeout(() => {
                                    deleteModal.classList.add('hidden');
                                    formToSubmit = null;
                                }, 200);

                                if (data.success) {
                                    // Show enhanced success message
                                    const notificationContainer = document.createElement('div');
                                    notificationContainer.classList.add('fixed', 'bottom-6', 'right-6',
                                        'z-50', 'status-update-notification', 'animate-slide-in');
                                    notificationContainer.innerHTML = `
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-2xl max-w-md">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">Success!</div>
                                    <div class="text-sm mt-0.5">${data.message}</div>
                                </div>
                            </div>
                        </div>
                    `;
                                    document.body.appendChild(notificationContainer);

                                    // Remove the notification after 3 seconds
                                    setTimeout(() => {
                                        notificationContainer.style.opacity = '0';
                                        notificationContainer.style.transform = 'translateY(20px)';
                                        setTimeout(() => notificationContainer.remove(), 300);
                                    }, 3000);

                                    // Refresh the bookings list while maintaining current filters
                                    refreshBookingsList();
                                } else {
                                    showAlert('Failed to delete booking: ' + (data.message ||
                                        'Unknown error'), 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Reset button state
                                confirmDeleteBtn.textContent = 'Delete';
                                confirmDeleteBtn.disabled = false;
                                deleteModal.classList.add('hidden');
                                formToSubmit = null;
                                showAlert('An error occurred while deleting the booking. Please try again.',
                                    'error');
                            });
                    }
                });

        // Function to attach status change event listeners with debounce
        let statusUpdateTimeout = null;
        
        function attachStatusChangeListeners() {
            const statusSelects = document.querySelectorAll('.booking-status-select');
            
            statusSelects.forEach(select => {
                // Remove existing event listeners to prevent duplicates
                const newSelect = select.cloneNode(true);
                select.parentNode.replaceChild(newSelect, select);
                
                newSelect.addEventListener('change', function() {
                    const bookingId = this.dataset.bookingId;
                    const newStatus = this.value;
                    const oldStatus = this.getAttribute('data-original-status') || this.value;
                    const selectElement = this;
                    
                    // If no change, do nothing
                    if (newStatus === oldStatus) {
                        return;
                    }
                    
                    // Store original status for potential reverting
                    if (!this.getAttribute('data-original-status')) {
                        this.setAttribute('data-original-status', oldStatus);
                    }
                    
                    // Change the select background color based on status
                    updateStatusSelectClass(this, newStatus);
                    
                    // Add a loading indicator
                    selectElement.classList.add('opacity-50');
                    
                    // Clear any pending update
                    if (statusUpdateTimeout) {
                        clearTimeout(statusUpdateTimeout);
                    }
                    
                    // Set a small delay to prevent rapid-fire changes
                    statusUpdateTimeout = setTimeout(() => {
                        console.log(`Updating booking #${bookingId} status to ${newStatus}`);
                        
                        // Send AJAX request to update status
                        fetch(`/bookings/${bookingId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: newStatus })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Remove loading indicator
                            selectElement.classList.remove('opacity-50');
                            
                            if (data.success) {
                                // Update the data-original-status attribute with new value
                                selectElement.setAttribute('data-original-status', newStatus);
                                
                                // Remove any existing notifications
                                const existingNotifications = document.querySelectorAll('.status-update-notification');
                                existingNotifications.forEach(notification => notification.remove());
                                
                                // Add a temporary success message
                                const notificationContainer = document.createElement('div');
                                notificationContainer.classList.add('fixed', 'bottom-4', 'right-4', 'bg-green-100', 'border', 'border-green-400', 'text-green-700', 'px-4', 'py-3', 'rounded-lg', 'shadow-lg', 'z-50', 'status-update-notification');
                                notificationContainer.innerHTML = `
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Status updated to <strong>${data.new_status}</strong> successfully!</span>
                                    </div>
                                `;
                                document.body.appendChild(notificationContainer);
                                
                                // Always refresh the booking list to ensure consistency
                                // Small delay to allow database update to complete
                                setTimeout(() => {
                                    refreshBookingsList();
                                    
                                    // If there's a status filter active that matches the new status,
                                    // ensure the updated booking is highlighted
                                    const currentStatusFilter = document.getElementById('statusFilter').value;
                                    if (currentStatusFilter === data.new_status) {
                                        setTimeout(() => {
                                            const updatedRow = document.querySelector(`tr[data-booking-id="${data.booking_id}"]`);
                                            if (updatedRow) {
                                                updatedRow.classList.add('bg-green-50', 'bg-green-900/20');
                                                setTimeout(() => {
                                                    updatedRow.classList.remove('bg-green-50', 'bg-green-900/20');
                                                }, 2000);
                                            }
                                        }, 500);
                                    }
                                }, 300);
                                
                                // Remove the notification after 3 seconds
                                setTimeout(() => {
                                    notificationContainer.remove();
                                }, 3000);
                            } else {
                                // Revert to original status on error
                                const originalStatus = selectElement.getAttribute('data-original-status');
                                selectElement.value = originalStatus;
                                updateStatusSelectClass(selectElement, originalStatus);
                                showAlert('Failed to update status: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            // Remove loading indicator
                            selectElement.classList.remove('opacity-50');
                            console.error('Error:', error);
                            
                            // Revert to original status on error
                            const originalStatus = selectElement.getAttribute('data-original-status');
                            selectElement.value = originalStatus;
                            updateStatusSelectClass(selectElement, originalStatus);
                            showAlert('An error occurred while updating the status. Please try again.', 'error');
                        });
                    }, 300); // 300ms delay to prevent rapid-fire requests
                });
            });
        }
        
        // Initially attach status change listeners
        attachStatusChangeListeners();
        
        function updateStatusSelectClass(select, status) {
            // Remove all existing status classes
            select.classList.remove(
                'bg-green-100', 'text-green-800',
                'bg-yellow-100', 'text-yellow-800',
                'bg-blue-100', 'text-blue-800',
                'bg-red-100', 'text-red-800',
                'bg-green-800', 'text-green-100',
                'bg-yellow-800', 'text-yellow-100',
                'bg-blue-800', 'text-blue-100',
                'bg-red-800', 'text-red-100'
            );
            
            // Add appropriate status classes
            switch(status) {
                case 'confirmed':
                    select.classList.add('bg-green-100', 'text-green-800', 'bg-green-800', 'text-green-100');
                    break;
                case 'pending':
                    select.classList.add('bg-yellow-100', 'text-yellow-800', 'bg-yellow-800', 'text-yellow-100');
                    break;
                case 'completed':
                    select.classList.add('bg-blue-100', 'text-blue-800', 'bg-blue-800', 'text-blue-100');
                    break;
                case 'canceled':
                    select.classList.add('bg-red-100', 'text-red-800', 'bg-red-800', 'text-red-100');
                    break;
            }
        }
        
        // Search and filter functionality
        const searchInput = document.getElementById('search');
        const statusFilter = document.getElementById('statusFilter');
        const dateFilter = document.getElementById('date-filter');
        
        // Setup form submission for both AJAX and fallback regular form submission
        const filterForm = document.getElementById('booking-filter-form');
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadFilteredBookings();
        });
        
        // Add event listeners for filters (but don't auto-submit)
        dateFilter.addEventListener('change', function() {
            // Clear the search input when changing date filter
            if (searchInput.value) {
                searchInput.value = '';
                const clearButton = document.getElementById('clear-search');
                clearButton.classList.add('hidden');
            }
        });
        
        // Automatically apply filter when status filter changes
        statusFilter.addEventListener('change', function() {
            if (window.refreshBookingsList) {
                window.refreshBookingsList();
            } else {
                loadFilteredBookings();
            }
        });
        
        // Show/hide clear button for search input and perform debounced search
        const debouncedSearch = debounce(function() {
            loadFilteredBookings();
        }, 500);
        
        searchInput.addEventListener('input', function() {
            const clearButton = document.getElementById('clear-search');
            if (searchInput.value) {
                clearButton.classList.remove('hidden');
                debouncedSearch(); // Perform search as user types (debounced)
            } else {
                clearButton.classList.add('hidden');
                loadFilteredBookings(); // If search is cleared, refresh results immediately
            }
        });
        
        // Also trigger search on Enter key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent form submission
                loadFilteredBookings();
            }
        });
        
        // Clear search button
        document.getElementById('clear-search').addEventListener('click', function() {
            searchInput.value = '';
            this.classList.add('hidden');
            loadFilteredBookings(); // Always refresh when clearing search
        });
        
        // Function to refresh the bookings list based on current filters
        function refreshBookingsList() {
            loadFilteredBookings();
        }
        
        // Function to load bookings based on filters
        function loadFilteredBookings() {
            const bookingList = document.getElementById('bookings-list');
            const dateValue = dateFilter.value;
            const statusValue = statusFilter.value;
            const searchValue = searchInput.value.trim();
            
            // Show loading state
            bookingList.innerHTML = '<tr><td colspan="9" class="px-6 py-4 text-center">Loading...</td></tr>';
            
            // Build the URL with query parameters
            const params = new URLSearchParams();
            
            // Add date filter
            if (dateValue && dateValue !== 'all') {
                params.append('filter', dateValue);
            } else {
                params.append('filter', 'all');
            }
            
            // Add status filter
            if (statusValue && statusValue !== 'all') {
                params.append('status', statusValue);
            }
            
            // Add search filter if there's any input
            if (searchValue) {
                params.append('search', searchValue);
            }
            
            // Add AJAX indicator
            params.append('ajax', '1');
            const url = bookingsIndexUrl + '?' + params.toString();
            
            // Log what we're searching for in debug mode
            console.log('Searching with parameters:', {
                date: dateValue,
                status: statusValue,
                search: searchValue,
                url: url
            });
            
            // Make the AJAX request
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                if (!response.ok) {
                    console.error('Server responded with status:', response.status);
                    throw new Error('Server returned status ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    bookingList.innerHTML = data.html;
                    
                    // Update the count display if any
                    const countDisplay = document.getElementById('bookings-count');
                    if (countDisplay) {
                        countDisplay.textContent = `${data.count} booking(s) found`;
                    }
                    
                    // Re-attach event listeners for the newly loaded elements
                    attachStatusChangeListeners();
                    attachDeleteFormHandlers();
                    attachCopyReferenceHandlers();
                    
                    // Highlight search terms in the results if search was performed
                    if (searchValue) {
                        highlightSearchTerms(searchValue);
                    }
                } else {
                    console.error('No HTML content returned from server');
                    bookingList.innerHTML = '<tr><td colspan="10" class="px-6 py-4 text-center text-red-500">Invalid response format. Please try again.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching bookings:', error);
                bookingList.innerHTML = '<tr><td colspan="10" class="px-6 py-4 text-center text-red-500">Error loading bookings: ' + error.message + '. Please try again.</td></tr>';
            });
        }
        
        // Function to highlight search terms in the results
        function highlightSearchTerms(searchText) {
            if (!searchText) return;
            
            const searchTerms = searchText.toLowerCase().split(/\s+/).filter(term => term.length > 1);
            if (searchTerms.length === 0) return;
            
            // Find all text nodes in the table rows (except headers)
            const tableRows = document.querySelectorAll('#bookings-list tr');
            
            tableRows.forEach(row => {
                const textNodes = [];
                
                function findTextNodes(element) {
                    if (element.nodeType === Node.TEXT_NODE) {
                        const text = element.nodeValue.trim();
                        if (text.length > 0) {
                            textNodes.push(element);
                        }
                    } else {
                        for (let i = 0; i < element.childNodes.length; i++) {
                            findTextNodes(element.childNodes[i]);
                        }
                    }
                }
                
                // Find all text nodes in this row
                findTextNodes(row);
                
                // Check each text node for matches
                textNodes.forEach(textNode => {
                    let nodeText = textNode.nodeValue;
                    let lowerText = nodeText.toLowerCase();
                    let highlight = false;
                    
                    // Check if any search term is in this text node
                    for (const term of searchTerms) {
                        if (lowerText.includes(term)) {
                            highlight = true;
                            break;
                        }
                    }
                    
                    // If this node has a search term, replace with highlighted version
                    if (highlight) {
                        const span = document.createElement('span');
                        span.innerHTML = nodeText;
                        span.className = 'bg-yellow-200';
                        textNode.parentNode.replaceChild(span, textNode);
                    }
                });
            });
        }
        
        // Helper function to debounce input events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Make the loadFilteredBookings available to the window scope
        window.loadFilteredBookings = loadFilteredBookings;
        
        // Connect our loadFilteredBookings function to refreshBookingsList if available
        if (!window.refreshBookingsList) {
            window.refreshBookingsList = function() {
                loadFilteredBookings();
            };
        }
        
        // Add a listener for the Enter key on the entire form
        document.getElementById('booking-filter-form').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent default form submission
                loadFilteredBookings();
            }
        });
        
        // Function to handle copying booking references to clipboard
        function attachCopyReferenceHandlers() {
            document.querySelectorAll('.copy-reference').forEach(el => {
                el.addEventListener('click', function() {
                    const reference = this.getAttribute('data-reference');
                    if (reference) {
                        navigator.clipboard.writeText(reference)
                            .then(() => {
                                // Show a brief success message
                                const originalContent = this.innerHTML;
                                this.innerHTML = '<span class="text-green-500 flex items-center">Copied! <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>';
                                setTimeout(() => {
                                    this.innerHTML = originalContent;
                                }, 2000);
                            })
                            .catch(err => {
                                console.error('Failed to copy text: ', err);
                                showAlert('Failed to copy to clipboard', 'error');
                            });
                    }
                });
            });
        }
        
        // Attach copy handlers on initial load
        attachCopyReferenceHandlers();
    });
</script>
@endsection
