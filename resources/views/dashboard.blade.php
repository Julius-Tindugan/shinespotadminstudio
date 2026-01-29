@extends('layouts.app')

@section('content')
    <div class="space-y-6 dashboard-container">
        <!-- Page Header -->
        <div class="dashboard-header flex flex-col sm:flex-row justify-between sm:items-center gap-4 pb-4 border-b border-border-color">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-primary-text font-heading">Dashboard Overview</h1>
                <p class="text-xs sm:text-sm text-secondary-text mt-1">Welcome back! Here's what's happening today.</p>
            </div>
            <button
                type="button"
                onclick="openExportModal('dashboard')"
                class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-accent to-accent-hover hover:from-accent-hover hover:to-accent text-white rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer z-10 relative"
                style="pointer-events: auto;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg> 
                Export Report 
            </button>
        </div>
        
        <!-- Alert Messages -->
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-lg shadow-sm animate-fade-in" role="alert">
                <div class="flex items-start"> 
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif 
        
        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 rounded-lg shadow-sm animate-fade-in" role="alert">
                <div class="flex items-start"> 
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif 
        
        <!-- User Welcome Card --> 
        @isset($user)
            <div class="bg-gradient-to-r from-accent/10 via-accent/5 to-transparent rounded-xl shadow-sm p-6 border border-accent/20">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="bg-gradient-to-br from-accent to-accent-hover p-4 rounded-xl shadow-md flex-shrink-0"> 
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg> 
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-primary-text font-heading">Welcome back, {{ $user['name'] }}!</h2>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-secondary-text text-sm">
                            <p class="flex items-center">
                                <svg class="w-4 h-4 mr-1.5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ implode(', ', $user['roles']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endisset
        
        <!-- KPI Statistics Section -->
        <section>
            <h2 class="text-base sm:text-lg font-bold text-primary-text mb-4 flex items-center font-heading">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Key Performance Indicators
            </h2>
            <div class="kpi-grid grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3">
                
                <!-- Stat Card: Total Revenue -->
                <div class="kpi-card group bg-gradient-to-br from-green-50 to-emerald-50 p-4 sm:p-6 rounded-xl shadow-md border border-green-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-3">
                            <div class="bg-green-100 p-2.5 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xs sm:text-sm font-semibold text-gray-600">Total Revenue</h3>
                        </div>
                        
                        <p class="text-2xl sm:text-3xl font-bold text-green-700 mb-2">
                            ₱{{ number_format($kpis['revenue']['current'], 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mb-2">Month to Date</p>
                        
                        @if(isset($kpis['revenue']['refunds']['currentMonth']['count']) && $kpis['revenue']['refunds']['currentMonth']['count'] > 0)
                            <div class="bg-orange-50 border-l-2 border-orange-400 px-2 py-1.5 rounded text-xs text-orange-700 mb-2">
                                <div class="flex items-center">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <span>{{ $kpis['revenue']['refunds']['currentMonth']['count'] }} refund(s): -₱{{ $kpis['revenue']['refunds']['currentMonth']['formatted'] }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="trend-indicator flex items-center text-xs {{ $kpis['revenue']['trending'] === 'up' ? 'text-green-600' : 'text-red-600' }} mb-4">
                            @if ($kpis['revenue']['trending'] === 'up')
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            @endif 
                            <span class="font-semibold">{{ number_format(abs($kpis['revenue']['monthlyChange'] ?? 0), 1) }}%</span>
                            <span class="ml-1">vs last month</span>
                        </div>
                        
                        <!-- Revenue Breakdown Chart -->
                        <div class="mt-auto pt-4 border-t border-green-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600">Revenue Breakdown</span>
                            </div>
                            <div class="chart-container relative" style="height: 120px;">
                                <canvas id="revenueBreakdownChart"></canvas>
                            </div>
                            <div class="mt-3 space-y-1">
                                @php
                                    $paidAmount = $kpis['revenue']['refunds']['grossRevenue'] ?? $kpis['revenue']['current'];
                                    $refundedAmount = $kpis['revenue']['refunds']['currentMonth']['amount'] ?? 0;
                                    $netAmount = $kpis['revenue']['current'];
                                    $totalGross = $paidAmount + $refundedAmount;
                                    $paidPercentage = $totalGross > 0 ? round(($paidAmount / $totalGross) * 100, 1) : 100;
                                    $refundedPercentage = $totalGross > 0 ? round(($refundedAmount / $totalGross) * 100, 1) : 0;
                                @endphp
                                @if($paidAmount > 0 || $refundedAmount > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                        <span class="text-gray-600">Paid Bookings</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">₱{{ number_format($paidAmount, 2) }} ({{ $paidPercentage }}%)</span>
                                </div>
                                @if($refundedAmount > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                        <span class="text-gray-600">Refunded</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">₱{{ number_format($refundedAmount, 2) }} ({{ $refundedPercentage }}%)</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-green-200">
                                    <span class="text-gray-600 font-semibold">Net Revenue</span>
                                    <span class="font-bold text-green-700">₱{{ number_format($netAmount, 2) }}</span>
                                </div>
                                @else
                                <div class="text-center text-xs text-gray-500 py-2">
                                    <p>No revenue recorded yet this month</p>
                                    <p class="text-xs mt-1">Revenue will appear once bookings are marked as paid</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> 
                
                <!-- Stat Card: Upcoming Bookings -->
                <div class="group bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl shadow-md border border-blue-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-100 p-2.5 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-600">Upcoming Bookings</h3>
                        </div>
                        
                        <p class="text-3xl font-bold text-blue-700 mb-2">{{ $kpis['bookings']['currentWeek'] }}</p>
                        <p class="text-xs text-gray-500 mb-2">This Week</p>
                        
                        <div class="flex items-center text-xs {{ $kpis['bookings']['weeklyTrending'] === 'up' ? 'text-blue-600' : 'text-red-600' }} mb-4">
                            @if ($kpis['bookings']['weeklyTrending'] === 'up')
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            @endif 
                            <span class="font-semibold">{{ number_format(abs($kpis['bookings']['weeklyChange'] ?? 0), 1) }}%</span>
                            <span class="ml-1">vs last week</span>
                        </div>
                        
                        <!-- Booking Status Chart -->
                        <div class="mt-auto pt-4 border-t border-blue-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600">Status Distribution</span>
                            </div>
                            <div class="relative" style="height: 150px;">
                                <canvas id="bookingStatusChart"></canvas>
                            </div>
                            <div class="mt-3 space-y-1">
                                @php
                                    $pending = \App\Models\Booking::currentWeek()->where('status', 'pending')->count();
                                    $confirmed = \App\Models\Booking::currentWeek()->where('status', 'confirmed')->count();
                                    $completed = \App\Models\Booking::currentWeek()->where('status', 'completed')->count();
                                    $cancelled = \App\Models\Booking::currentWeek()->whereIn('status', ['cancelled', 'no_show'])->count();
                                    $total = $kpis['bookings']['currentWeek'];
                                @endphp
                                @if($pending > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                                        <span class="text-gray-600">Pending</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $pending }} ({{ $total > 0 ? round(($pending/$total)*100, 1) : 0 }}%)</span>
                                </div>
                                @endif
                                @if($confirmed > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                        <span class="text-gray-600">Confirmed</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $confirmed }} ({{ $total > 0 ? round(($confirmed/$total)*100, 1) : 0 }}%)</span>
                                </div>
                                @endif
                                @if($completed > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                        <span class="text-gray-600">Completed</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $completed }} ({{ $total > 0 ? round(($completed/$total)*100, 1) : 0 }}%)</span>
                                </div>
                                @endif
                                @if($cancelled > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                        <span class="text-gray-600">Cancelled</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $cancelled }} ({{ $total > 0 ? round(($cancelled/$total)*100, 1) : 0 }}%)</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div> 
                
                <!-- Stat Card: Total Bookings (Monthly) -->
                <div class="group bg-gradient-to-br from-amber-50 to-yellow-50 p-6 rounded-xl shadow-md border border-amber-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-3">
                            <div class="bg-amber-100 p-2.5 rounded-lg mr-3 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-600">Total Bookings</h3>
                        </div>
                        
                        <p class="text-3xl font-bold text-amber-700 mb-2">{{ $kpis['bookings']['currentMonth'] }}</p>
                        <p class="text-xs text-gray-500 mb-2">Month to Date</p>
                        
                        <div class="flex items-center text-xs {{ $kpis['bookings']['monthlyTrending'] === 'up' ? 'text-amber-600' : 'text-red-600' }} mb-4">
                            @if ($kpis['bookings']['monthlyTrending'] === 'up')
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            @endif 
                            <span class="font-semibold">{{ number_format(abs($kpis['bookings']['monthlyChange']), 1) }}%</span>
                            <span class="ml-1">vs last month</span>
                        </div>
                        
                        <!-- Performance Chart -->
                        <div class="mt-auto pt-4 border-t border-amber-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-600">Performance Metrics</span>
                            </div>
                            <div class="relative" style="height: 150px;">
                                <canvas id="bookingPerformanceChart"></canvas>
                            </div>
                            <div class="mt-3 space-y-1">
                                @php
                                    $completedCount = $kpis['bookings']['completed']['current'] ?? 0;
                                    $cancelledCount = $kpis['bookings']['canceled']['current'] ?? 0;
                                    $activeCount = $kpis['bookings']['currentMonth'] - $completedCount - $cancelledCount;
                                    $totalMonth = $kpis['bookings']['currentMonth'];
                                    $completionRate = $kpis['bookings']['completionRate']['current'] ?? 0;
                                    $cancellationRate = $kpis['bookings']['cancellationRate']['current'] ?? 0;
                                @endphp
                                @if($completedCount > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                        <span class="text-gray-600">Completed</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $completedCount }} ({{ number_format($completionRate, 1) }}%)</span>
                                </div>
                                @endif
                                @if($activeCount > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                                        <span class="text-gray-600">Active</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $activeCount }} ({{ $totalMonth > 0 ? round(($activeCount/$totalMonth)*100, 1) : 0 }}%)</span>
                                </div>
                                @endif
                                @if($cancelledCount > 0)
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center">
                                        <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                                        <span class="text-gray-600">Cancelled</span>
                                    </div>
                                    <span class="font-semibold text-gray-700">{{ $cancelledCount }} ({{ number_format($cancellationRate, 1) }}%)</span>
                                </div>
                                @endif
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-amber-200">
                                    <span class="text-gray-600 font-semibold">Success Rate</span>
                                    <span class="font-bold text-amber-700">{{ number_format($completionRate, 1) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Finance Summary Card (Admin Only) -->
        @if(Session::get('user_type') === 'admin' && isset($user['has_finance_access']) && $user['has_finance_access'] && isset($kpis['finance_summary']))
        <section>
            <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-xl mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-primary-text">Quick Finance Summary</h2>
                                <p class="text-sm text-secondary-text">Month-to-date financial overview</p>
                            </div>
                        </div>
                        <a href="{{ route('finance.dashboard') }}" 
                            class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all duration-200 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Full Finance Dashboard
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Monthly Revenue -->
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-purple-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-secondary-text">Revenue (MTD)</p>
                                <div class="bg-green-100 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 10v-1"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($kpis['finance_summary']['monthly_revenue'], 2) }}</p>
                        </div>

                        <!-- Monthly Expenses -->
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-purple-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-secondary-text">Expenses (MTD)</p>
                                <div class="bg-red-100 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-red-600">₱{{ number_format($kpis['finance_summary']['monthly_expenses'], 2) }}</p>
                        </div>

                        <!-- Net Profit -->
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-purple-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-secondary-text">Net Profit</p>
                                <div class="bg-{{ $kpis['finance_summary']['net_profit'] >= 0 ? 'blue' : 'orange' }}-100 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-{{ $kpis['finance_summary']['net_profit'] >= 0 ? 'blue' : 'orange' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-{{ $kpis['finance_summary']['net_profit'] >= 0 ? 'blue' : 'orange' }}-600">
                                ₱{{ number_format($kpis['finance_summary']['net_profit'], 2) }}
                            </p>
                            <p class="text-xs text-secondary-text mt-1">
                                {{ number_format($kpis['finance_summary']['profit_margin'], 1) }}% margin
                            </p>
                        </div>

                        <!-- Pending Payments -->
                        <div class="bg-white p-5 rounded-lg shadow-sm border border-purple-100 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-medium text-secondary-text">Pending Payments</p>
                                <div class="bg-yellow-100 p-2 rounded-lg">
                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-2xl font-bold text-yellow-600">₱{{ number_format($kpis['finance_summary']['pending_payments'], 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="text-sm text-purple-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">View detailed reports, expenses tracking, and payment management in the full Finance Dashboard.</span>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        <!-- Activity Logs Section (Admin Only) -->
        @if(Session::get('user_type') === 'admin' && isset($recentActivities) && count($recentActivities) > 0)
        <section x-data="{ expanded: false }">
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-2.5 sm:p-3 rounded-xl mr-3 sm:mr-4">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg sm:text-xl font-bold text-primary-text">Recent Activity Logs</h2>
                                <p class="text-xs sm:text-sm text-secondary-text">Monitor system activities in real-time</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                            <button @click="expanded = !expanded" 
                                class="flex-1 sm:flex-none px-3 sm:px-4 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-all duration-200 flex items-center justify-center text-xs sm:text-sm font-medium">
                                <span x-text="expanded ? 'Collapse' : 'Expand All'"></span>
                                <svg x-show="!expanded" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <svg x-show="expanded" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
                            </button>
                            <a href="{{ route('activity-logs.index') }}" 
                                class="flex-1 sm:flex-none px-3 sm:px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-xs sm:text-sm font-medium">
                                <svg class="w-4 h-4 mr-1.5 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">View All Logs</span>
                                <span class="sm:hidden">All Logs</span>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-5">
                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-indigo-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-secondary-text">Today's Activity</p>
                                    <p class="text-xl sm:text-2xl font-bold text-indigo-600">{{ $activityStats['today'] ?? 0 }}</p>
                                </div>
                                <div class="bg-indigo-100 p-2 sm:p-3 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-indigo-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-secondary-text">This Week</p>
                                    <p class="text-xl sm:text-2xl font-bold text-blue-600">{{ $activityStats['this_week'] ?? 0 }}</p>
                                </div>
                                <div class="bg-blue-100 p-2 sm:p-3 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-indigo-100 sm:col-span-2 md:col-span-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm font-medium text-secondary-text">Critical Actions</p>
                                    <p class="text-xl sm:text-2xl font-bold text-red-600">{{ $activityStats['critical_count'] ?? 0 }}</p>
                                </div>
                                <div class="bg-red-100 p-2 sm:p-3 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="bg-white rounded-lg border border-indigo-100 shadow-sm" :class="{ 'max-h-96 overflow-y-auto custom-scrollbar': !expanded }">
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Latest 10 Activities
                                <span class="ml-2 text-xs text-gray-500" id="activity-last-updated">Updated just now</span>
                            </h3>
                            
                            <div class="space-y-3" id="activity-logs-container">
                                @foreach($recentActivities as $activity)
                                <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150 border border-gray-100">
                                    <div class="flex-shrink-0 mr-3">
                                        @php
                                            $actionColors = [
                                                'created' => 'bg-green-100 text-green-600',
                                                'updated' => 'bg-blue-100 text-blue-600',
                                                'deleted' => 'bg-red-100 text-red-600',
                                                'login' => 'bg-purple-100 text-purple-600',
                                                'logout' => 'bg-gray-100 text-gray-600',
                                                'viewed' => 'bg-yellow-100 text-yellow-600',
                                                'failed_login' => 'bg-orange-100 text-orange-600'
                                            ];
                                            $colorClass = $actionColors[$activity->action] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <div class="p-2 rounded-full {{ $colorClass }}">
                                            @if($activity->action === 'created')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            @elseif($activity->action === 'updated')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            @elseif($activity->action === 'deleted')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            @elseif($activity->action === 'login')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $activity->user_name }}
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                                        {{ ucfirst($activity->user_type) }}
                                                    </span>
                                                </p>
                                                <p class="text-sm text-gray-600 mt-1">{{ $activity->action_description }}</p>
                                                @if($activity->model_type)
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        <span class="font-semibold">Model:</span> {{ class_basename($activity->model_type) }}
                                                        @if($activity->model_id)
                                                            #{{ $activity->model_id }}
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                            <span class="text-xs text-gray-500 ml-4 flex-shrink-0" title="{{ $activity->created_at->format('M d, Y H:i:s') }}">
                                                {{ $activity->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Auto-refresh indicator -->
                            <div class="mt-4 pt-3 border-t border-gray-200 flex items-center justify-between">
                                <p class="text-xs text-gray-500 flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></span>
                                    Auto-refreshing every 30 seconds
                                </p>
                                <button onclick="refreshActivityLogs()" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Refresh Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Info Banner -->
                    <div class="mt-4 p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                        <p class="text-sm text-indigo-700 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">View detailed logs, apply filters, and export reports in the full Activity Logs Dashboard.</span>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        @endif

        
        <!-- Bookings Timeline -->
        <section>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                <h2 class="text-lg font-bold text-primary-text flex items-center font-heading">
                    <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg> 
                    Upcoming Bookings
                </h2> 
                <a href="{{ route('bookings.index') }}"
                    class="px-4 py-2 text-sm bg-accent hover:bg-accent-hover text-white rounded-lg transition-all duration-200 flex items-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                    <span>View All Bookings</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
            <div class="bg-card-bg p-6 rounded-xl shadow-md border border-border-color">
                <div class="overflow-x-auto custom-scrollbar">
                    @if (isset($kpis['upcomingBookings']) && count($kpis['upcomingBookings']) > 0)
                        <div class="grid grid-cols-1 gap-3">
                            @foreach ($kpis['upcomingBookings'] as $booking)
                                <div class="bg-gradient-to-r from-background to-transparent p-4 rounded-lg hover:shadow-md transition-all duration-300 border border-border-color hover:border-accent/30">
                                    <a href="{{ route('bookings.show', $booking['id']) }}" class="block">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                            <div class="flex-1">
                                                <div class="flex items-center flex-wrap gap-2 mb-2">
                                                    <p class="font-bold text-primary-text">{{ $booking['client'] }}</p> 
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $booking['status'] === 'confirmed' ? 'bg-green-100 text-green-700 border border-green-200' : ($booking['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-200' : 'bg-gray-100 text-gray-700 border border-gray-200') }}">
                                                        {{ ucfirst($booking['status']) }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-secondary-text">
                                                    <svg class="w-4 h-4 inline mr-1 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                    {{ $booking['package'] ?? 'No package assigned' }}
                                                </p>
                                                @if (isset($booking['primary_staff']))
                                                    <p class="text-xs text-secondary-text mt-1.5 flex items-center"> 
                                                        <svg class="w-3 h-3 mr-1 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        <span class="font-medium">Staff:</span>
                                                        <span class="ml-1">{{ $booking['primary_staff'] }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="text-right bg-accent/10 px-4 py-2.5 rounded-lg border border-accent/20">
                                                    <p class="font-semibold text-primary-text text-sm">{{ $booking['date'] }}</p>
                                                    <p class="text-xs text-secondary-text mt-0.5">{{ $booking['time'] }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-bold text-accent">₱{{ number_format($booking['amount'], 2) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @elseif(isset($kpis['upcomingBookings']))
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-secondary-text font-medium">No upcoming bookings scheduled</p>
                            <p class="text-sm text-secondary-text mt-1">New bookings will appear here</p>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-secondary-text font-medium">Error loading upcoming bookings</p>
                            <p class="text-sm text-secondary-text mt-1">Please refresh the page</p>
                        </div>
                    @endif
                </div>
            </div>
        </section> 
        
        <!-- Revenue Analytics Section -->
        <section>
            <div class="bg-card-bg p-6 rounded-xl shadow-md border border-border-color relative">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                    <div class="flex items-center">
                        <div class="bg-accent/10 p-2.5 rounded-lg mr-3">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-primary-text font-heading">Revenue Analytics <span id="period-title" class="text-secondary-text font-normal text-base">- Last 30 Days</span></h3>
                            <p class="text-xs text-secondary-text mt-0.5">Track your revenue performance over time</p>
                        </div>
                    </div>
                    <div class="period-selector-group inline-flex p-1.5 bg-gray-100 rounded-lg gap-1 shadow-sm overflow-x-auto"> 
                        <button data-period="daily"
                            class="period-selector px-3 sm:px-4 py-2 text-xs font-semibold rounded-md transition-all duration-200 bg-accent text-white shadow-sm whitespace-nowrap">Daily</button>
                        <button data-period="weekly"
                            class="period-selector px-3 sm:px-4 py-2 text-xs font-semibold rounded-md hover:bg-gray-200 transition-all duration-200 text-secondary-text whitespace-nowrap">Weekly</button>
                        <button data-period="monthly"
                            class="period-selector px-3 sm:px-4 py-2 text-xs font-semibold rounded-md hover:bg-gray-200 transition-all duration-200 text-secondary-text whitespace-nowrap">Monthly</button>
                        <button data-period="yearly"
                            class="period-selector px-3 sm:px-4 py-2 text-xs font-semibold rounded-md hover:bg-gray-200 transition-all duration-200 text-secondary-text whitespace-nowrap">Yearly</button>
                    </div>
                </div>
                
                <div class="chart-stats-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 sm:p-5 rounded-lg border border-blue-100 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs sm:text-sm font-semibold text-gray-600">Average Revenue</p>
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <p id="avg-revenue" class="text-2xl font-bold text-blue-700">₱0.00</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-5 rounded-lg border border-green-100 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-gray-600">Highest Revenue</p>
                            <div class="bg-green-100 p-2 rounded-lg">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <p id="max-revenue" class="text-2xl font-bold text-green-700">₱0.00</p>
                    </div>
                    
                    <div class="bg-gradient-to-br from-red-50 to-rose-50 p-5 rounded-lg border border-red-100 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-semibold text-gray-600">Lowest Revenue</p>
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
                                </svg>
                            </div>
                        </div>
                        <p id="min-revenue" class="text-2xl font-bold text-red-700">₱0.00</p>
                    </div>
                </div>
                
                <div class="h-80 relative bg-gradient-to-b from-transparent to-gray-50/50 rounded-lg p-4">
                    <div id="no-data-message"
                        class="hidden absolute inset-0 flex items-center justify-center flex-col"> 
                        <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-gray-500 font-medium">No revenue data available</p>
                        <p class="text-sm text-gray-400 mt-1">Data will appear here once available</p>
                    </div> 
                    <canvas id="revenueChart"></canvas>
                </div>
                
                <div id="revenue-loading"
                    class="absolute inset-0 bg-white/90 backdrop-blur-sm flex items-center justify-center z-10 hidden rounded-xl">
                    <div class="flex flex-col items-center bg-white p-8 rounded-xl shadow-xl border border-border-color">
                        <div class="loader-pulse mb-4"></div>
                        <p class="text-sm font-semibold text-primary-text">Loading revenue data...</p>
                        <p class="text-xs text-secondary-text mt-1">Please wait</p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 text-xs mt-4 pt-4 border-t border-border-color">
                    <div class="flex items-center text-secondary-text"> 
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-2"></span> 
                        <span class="font-semibold">Real-time Updates</span> 
                    </div> 
                    <span id="last-updated" class="text-secondary-text">Last updated: Just now</span>
                </div>
            </div>
        </section>
</div> 

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/revenue-chart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/export-modal.css') }}?v={{ time() }}">
    <style>
        .loader-pulse {
            width: 48px;
            height: 48px;
            border: 4px solid hsl(var(--accent) / 0.1);
            border-radius: 50%;
            border-top-color: hsl(var(--accent));
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* KPI Chart Container Styling */
        .kpi-chart-container {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .kpi-chart-container:hover {
            transform: scale(1.02);
        }
        
        /* Custom scrollbar for chart legends */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
    </style>
@endpush 

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    
    <!-- Dashboard Data Configuration -->
    <script>
        // Pass server-side data to JavaScript
        window.dashboardData = {
            dailyRevenue: {
                data: @json($kpis['dailyRevenue']['data'] ?? []),
                labels: @json($kpis['dailyRevenue']['labels'] ?? []),
                stats: @json($kpis['dailyRevenue']['stats'] ?? null)
            },
            isAdmin: {{ Session::get('user_type') === 'admin' ? 'true' : 'false' }}
        };
        
        // Set data attributes for KPI charts
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $pending = \App\Models\Booking::currentWeek()->where('status', 'pending')->count();
                $confirmed = \App\Models\Booking::currentWeek()->where('status', 'confirmed')->count();
                $completed = \App\Models\Booking::currentWeek()->where('status', 'completed')->count();
                $cancelled = \App\Models\Booking::currentWeek()->whereIn('status', ['cancelled', 'no_show'])->count();
                $completedCount = $kpis['bookings']['completed']['current'] ?? 0;
                $cancelledCount = $kpis['bookings']['canceled']['current'] ?? 0;
                $activeCount = $kpis['bookings']['currentMonth'] - $completedCount - $cancelledCount;
            @endphp
            
            const revenueBreakdownCtx = document.getElementById('revenueBreakdownChart');
            if (revenueBreakdownCtx) {
                revenueBreakdownCtx.dataset.paidAmount = {{ $kpis['revenue']['refunds']['grossRevenue'] ?? $kpis['revenue']['current'] }};
                revenueBreakdownCtx.dataset.refundedAmount = {{ $kpis['revenue']['refunds']['currentMonth']['amount'] ?? 0 }};
            }
            
            const bookingStatusCtx = document.getElementById('bookingStatusChart');
            if (bookingStatusCtx) {
                bookingStatusCtx.dataset.pending = {{ $pending }};
                bookingStatusCtx.dataset.confirmed = {{ $confirmed }};
                bookingStatusCtx.dataset.completed = {{ $completed }};
                bookingStatusCtx.dataset.cancelled = {{ $cancelled }};
            }
            
            const bookingPerformanceCtx = document.getElementById('bookingPerformanceChart');
            if (bookingPerformanceCtx) {
                bookingPerformanceCtx.dataset.completed = {{ $completedCount }};
                bookingPerformanceCtx.dataset.active = {{ $activeCount }};
                bookingPerformanceCtx.dataset.cancelled = {{ $cancelledCount }};
            }
        });
    </script>
    
    <!-- Dashboard JavaScript Modules -->
    <script src="{{ asset('js/dashboard-kpi-charts.js') }}"></script>
    <script src="{{ asset('js/dashboard-revenue-chart.js') }}"></script>
    <script src="{{ asset('js/dashboard-activity-logs.js') }}"></script>
    <script src="{{ asset('js/dashboard-main.js') }}"></script>
    <script src="{{ asset('js/export-report.js') }}?v={{ time() }}"></script>
@endpush

<!-- Include Export Modal -->
@include('components.export-modal')

@endsection
