@extends('layouts.app')
@section('title', 'Financial Reports')
@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-primary-text">Financial Reports</h1>
            <p class="mt-1 text-sm text-secondary-text">Comprehensive financial analysis and insights</p>
        </div>

        <!-- Report Filters Card -->
        <div class="bg-card-bg shadow-subtle rounded-lg border border-border-color mb-6">
            <div class="px-6 py-4 border-b border-border-color">
                <h2 class="text-lg font-semibold text-primary-text flex items-center">
                    <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Report Filters
                </h2>
            </div>
            <form id="reportFilterForm" action="{{ route('finance.reports') }}" method="GET" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Report Type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-secondary-text mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Report Type
                        </label>
                        <select id="report_type" name="report_type" class="mt-1 block w-full rounded-lg border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-50 transition-colors">
                            <option value="profit_loss" {{ request('report_type', 'profit_loss') == 'profit_loss' ? 'selected' : '' }}>Profit & Loss</option>
                            <option value="revenue" {{ request('report_type') == 'revenue' ? 'selected' : '' }}>Revenue Analysis</option>
                            <option value="expense" {{ request('report_type') == 'expense' ? 'selected' : '' }}>Expense Analysis</option>
                            <option value="booking" {{ request('report_type') == 'booking' ? 'selected' : '' }}>Booking Performance</option>
                        </select>
                    </div>

                    <!-- Time Period -->
                    <div>
                        <label for="period" class="block text-sm font-medium text-secondary-text mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Time Period
                        </label>
                        <select id="period" name="period" class="mt-1 block w-full rounded-lg border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-50 transition-colors">
                            <option value="monthly" {{ request('period', 'monthly') == 'monthly' ? 'selected' : '' }}>This Month</option>
                            <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>This Quarter</option>
                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div id="date_from_wrapper" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }}">
                        <label for="date_from" class="block text-sm font-medium text-secondary-text mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                            Date From
                        </label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-50 transition-colors">
                    </div>

                    <!-- Date To -->
                    <div id="date_to_wrapper" style="display: {{ request('period') == 'custom' ? 'block' : 'none' }}">
                        <label for="date_to" class="block text-sm font-medium text-secondary-text mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                            Date To
                        </label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring-2 focus:ring-accent focus:ring-opacity-50 transition-colors">
                    </div>
                </div>
                
                <!-- Apply Filters Button (only shown for custom date range) -->
                <div id="apply_filter_wrapper" class="mt-6 flex justify-end" style="display: {{ request('period') == 'custom' ? 'flex' : 'none' }}">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition-colors">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div id="reportContent">
            <!-- Report Header Card -->
            <div class="bg-gradient-to-r from-accent to-accent-hover shadow-subtle rounded-lg border border-border-color mb-6 overflow-hidden">
                <div class="px-6 py-8 text-center">
                    <h2 class="text-2xl font-bold text-white">{{ $reportTitle }}</h2>
                    <p class="text-sm text-white/90 mt-2 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $reportDateRange }}
                    </p>
                </div>
            </div>

            <!-- Financial Summary Cards -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                <!-- Total Revenue Card -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg border border-border-color hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-3 shadow-md">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate">Total Revenue</dt>
                                    <dd class="mt-1">
                                        <div class="text-xl font-bold text-green-600">
                                            ₱{{ number_format($totalRevenue, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 px-6 py-2">
                        <p class="text-xs text-green-700 font-medium">Income generated</p>
                    </div>
                </div>

                <!-- Total Expenses Card -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg border border-border-color hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-red-400 to-red-600 rounded-lg p-3 shadow-md">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate">Total Expenses</dt>
                                    <dd class="mt-1">
                                        <div class="text-xl font-bold text-red-600">
                                            ₱{{ number_format($totalExpenses, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-50 px-6 py-2">
                        <p class="text-xs text-red-700 font-medium">Costs incurred</p>
                    </div>
                </div>

                <!-- Net Profit Card -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg border border-border-color hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-3 shadow-md">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate">Net Profit</dt>
                                    <dd class="mt-1">
                                        <div class="text-xl font-bold {{ $netProfit < 0 ? 'text-red-600' : 'text-blue-600' }}">
                                            ₱{{ number_format($netProfit, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $netProfit < 0 ? 'bg-red-50' : 'bg-blue-50' }} px-6 py-2">
                        <p class="text-xs {{ $netProfit < 0 ? 'text-red-700' : 'text-blue-700' }} font-medium">
                            {{ $netProfit < 0 ? 'Loss amount' : 'Revenue - Expenses' }}
                        </p>
                    </div>
                </div>

                <!-- Profit Margin Card -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg border border-border-color hover:shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-3 shadow-md">
                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate">Profit Margin</dt>
                                    <dd class="mt-1">
                                        <div class="text-xl font-bold {{ $profitMargin < 0 ? 'text-red-600' : 'text-purple-600' }}">
                                            {{ number_format($profitMargin, 1) }}%
                                        </div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="{{ $profitMargin < 0 ? 'bg-red-50' : 'bg-purple-50' }} px-6 py-2">
                        <p class="text-xs {{ $profitMargin < 0 ? 'text-red-700' : 'text-purple-700' }} font-medium">Profitability ratio</p>
                    </div>
                </div>
            </div>

            @if(request('report_type', 'profit_loss') == 'profit_loss')
            <!-- Profit & Loss Report -->
            <div class="bg-card-bg shadow-subtle rounded-lg border border-border-color">
                <div class="px-6 py-4 border-b border-border-color bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Profit & Loss Statement
                    </h3>
                </div>
                <div class="p-6">
                    @if(count($periodData) > 0)
                    <!-- Main Chart -->
                    <div class="mb-8 bg-white p-4 rounded-lg border border-gray-200">
                        <canvas id="profitLossChart" height="80"></canvas>
                    </div>
                    <!-- Detailed P&L Table -->
                    <div class="overflow-x-auto rounded-lg border border-border-color">
                        <table class="min-w-full divide-y divide-border-color">
                            <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Period</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Revenue</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Expenses</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Profit/Loss</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Margin</th>
                                </tr>
                            </thead>
                            <tbody class="bg-card-bg divide-y divide-border-color">
                                @foreach($periodData as $period => $data)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">{{ $period }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">₱{{ number_format($data['revenue'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600 font-semibold">₱{{ number_format($data['expenses'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right {{ $data['profit'] < 0 ? 'text-red-600' : 'text-green-600' }}">₱{{ number_format($data['profit'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-right {{ $data['margin'] < 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($data['margin'], 1) }}%</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gradient-to-r from-gray-100 to-gray-50 border-t-2 border-accent">
                                <tr>
                                    <th scope="row" class="px-6 py-4 text-left text-sm font-bold text-primary-text uppercase">Total</th>
                                    <th scope="row" class="px-6 py-4 text-right text-sm font-bold text-green-600">₱{{ number_format($totalRevenue, 2) }}</th>
                                    <th scope="row" class="px-6 py-4 text-right text-sm font-bold text-red-600">₱{{ number_format($totalExpenses, 2) }}</th>
                                    <th scope="row" class="px-6 py-4 text-right text-sm font-bold {{ $netProfit < 0 ? 'text-red-600' : 'text-green-600' }} uppercase">₱{{ number_format($netProfit, 2) }}</th>
                                    <th scope="row" class="px-6 py-4 text-right text-sm font-bold {{ $profitMargin < 0 ? 'text-red-600' : 'text-green-600' }} uppercase">{{ number_format($profitMargin, 1) }}%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">No Financial Data Available</h3>
                        <p class="text-sm text-secondary-text max-w-md mx-auto">There are no transactions or expenses recorded for the selected period. Try selecting a different date range or add some transactions to see your profit & loss report.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(request('report_type') == 'revenue')
            <!-- Revenue Analysis Report -->
            <div class="bg-card-bg shadow-subtle rounded-lg border border-border-color">
                <div class="px-6 py-4 border-b border-border-color bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Revenue Analysis
                    </h3>
                </div>
                <div class="p-6">
                    @if($totalRevenue > 0)
                    <!-- Revenue Trend Chart -->
                    <div class="mb-8 bg-white p-4 rounded-lg border border-gray-200">
                        <canvas id="revenueChart" height="80"></canvas>
                    </div>

                    <!-- Charts Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Revenue by Package
                            </h4>
                            <div class="h-64">
                                <canvas id="packageRevenueChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Revenue by Payment Method
                            </h4>
                            <div class="h-64">
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Top Revenue Sources Table -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-primary-text flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Top Revenue Sources
                            </h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-border-color">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Source</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-card-bg divide-y divide-border-color">
                                    @foreach($revenueSources as $source)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">{{ $source['name'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">₱{{ number_format($source['amount'], 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ min($source['percentage'], 100) }}%"></div>
                                                </div>
                                                <span class="text-sm font-semibold text-primary-text">{{ number_format($source['percentage'], 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">No Revenue Data Available</h3>
                        <p class="text-sm text-secondary-text max-w-md mx-auto">There are no revenue transactions recorded for the selected period. Revenue analysis will appear here once payments are received.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(request('report_type') == 'expense')
            <!-- Expense Analysis Report -->
            <div class="bg-card-bg shadow-subtle rounded-lg border border-border-color">
                <div class="px-6 py-4 border-b border-border-color bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Expense Analysis
                    </h3>
                </div>
                <div class="p-6">
                    @if($totalExpenses > 0)
                    <!-- Expense Trend Chart -->
                    <div class="mb-8 bg-white p-4 rounded-lg border border-gray-200">
                        <canvas id="expenseChart" height="80"></canvas>
                    </div>

                    <!-- Charts Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                                Expenses by Category
                            </h4>
                            <div class="h-64">
                                <canvas id="expenseCategoryChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Top Expense Items
                            </h4>
                            <ul class="space-y-3">
                                @foreach($topExpenses as $expense)
                                <li class="flex items-center">
                                    <div class="w-3 h-3 rounded-full mr-3 flex-shrink-0" style="background-color: {{ $expense['color'] ?? '#808080' }}"></div>
                                    <div class="flex-grow min-w-0">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-sm font-medium text-primary-text truncate">{{ $expense['title'] }}</span>
                                            <span class="text-sm text-red-600 font-semibold ml-2">₱{{ number_format($expense['amount'], 2) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="h-1.5 rounded-full transition-all" style="width: {{ $expense['percentage'] }}%; background-color: {{ $expense['color'] ?? '#808080' }}"></div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Expense Categories Breakdown Table -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-primary-text flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Expense Categories Breakdown
                            </h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-border-color">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Category</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">% of Total</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Transactions</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Avg. Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-card-bg divide-y divide-border-color">
                                    @foreach($expenseByCategory as $category)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">
                                            <div class="flex items-center">
                                                <div class="h-3 w-3 rounded-full mr-2 flex-shrink-0" style="background-color: {{ $category['color'] ?? '#808080' }}"></div>
                                                {{ $category['name'] }}
                                            </div>
                                        </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">₱{{ number_format($category['amount'], 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">{{ number_format($category['percentage'], 1) }}%</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">{{ $category['count'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">₱{{ number_format($category['average'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <th scope="row" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Total</th>
                                            <th scope="row" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">₱{{ number_format($totalExpenses, 2) }}</th>
                                            <th scope="row" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">100%</th>
                                            <th scope="row" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">{{ $totalExpenseCount }}</th>
                                            <th scope="row" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">₱{{ number_format($averageExpense, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        @else
                        <!-- Empty State -->
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Expense Data Available</h3>
                            <p class="mt-2 text-sm text-gray-500">There are no expenses recorded for the selected period.</p>
                            <p class="mt-1 text-sm text-gray-500">Add business expenses to track your spending and improve profit margins.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(request('report_type') == 'booking')
            <!-- Booking Performance Report -->
            <div class="bg-card-bg shadow-subtle rounded-lg border border-border-color">
                <div class="px-6 py-4 border-b border-border-color bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-lg font-semibold text-primary-text flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Booking Performance Analysis
                    </h3>
                </div>
                <div class="p-6">
                    @if($totalBookings > 0)
                    
                    <!-- Key Metrics Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Bookings Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border border-blue-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-semibold text-blue-800">Total Bookings</div>
                                <div class="bg-blue-500 rounded-lg p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-blue-900">{{ number_format($totalBookings) }}</div>
                            <div class="text-xs text-blue-700 mt-1">All booking records</div>
                        </div>

                        <!-- Completed Bookings Card -->
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border border-green-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-semibold text-green-800">Completed</div>
                                <div class="bg-green-500 rounded-lg p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-green-900">{{ number_format($completedBookings) }}</div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-xs text-green-700">Completion rate</span>
                                <span class="text-xs font-bold text-green-800 bg-green-200 px-2 py-0.5 rounded-full">{{ number_format($completedBookingsPercentage, 1) }}%</span>
                            </div>
                        </div>

                        <!-- Average Booking Value Card -->
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-5 border border-yellow-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-semibold text-yellow-800">Avg. Value</div>
                                <div class="bg-yellow-500 rounded-lg p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-yellow-900">₱{{ number_format($averageBookingValue, 2) }}</div>
                            <div class="text-xs text-yellow-700 mt-1">Per booking</div>
                        </div>

                        <!-- Revenue Per Booking Card -->
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-5 border border-purple-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm font-semibold text-purple-800">Revenue/Booking</div>
                                <div class="bg-purple-500 rounded-lg p-2">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-purple-900">₱{{ number_format($revenuePerBooking, 2) }}</div>
                            <div class="text-xs text-purple-700 mt-1">Actual revenue</div>
                        </div>
                    </div>

                    <!-- Booking Trend Chart - Compact -->
                    <div class="mb-6 bg-white p-4 sm:p-5 rounded-lg border border-gray-200 shadow-sm">
                        <h4 class="text-sm sm:text-base font-semibold text-primary-text mb-3 flex items-center">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Booking Trends Over Time</span>
                            <span class="ml-2 text-xs font-normal text-secondary-text hidden sm:inline">(Daily Activity)</span>
                        </h4>
                        <div class="h-48 sm:h-56">
                            <canvas id="bookingChart"></canvas>
                        </div>
                    </div>

                    <!-- Charts Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <!-- Bookings by Package -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Bookings by Package
                            </h4>
                            <div class="h-64">
                                <canvas id="packageBookingsChart"></canvas>
                            </div>
                        </div>

                        <!-- Booking Status Distribution -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="text-base font-semibold text-primary-text mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Status Distribution
                            </h4>
                            <div class="h-64">
                                <canvas id="bookingStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Package Performance Table -->
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <h4 class="text-base font-semibold text-primary-text flex items-center">
                                <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Package Performance Details
                            </h4>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-border-color">
                                <thead class="bg-gradient-to-r from-gray-100 to-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                                Package
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Bookings</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Revenue</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">Avg. Revenue</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">% of Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-card-bg divide-y divide-border-color">
                                    @foreach($packagePerformance as $package)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-accent to-accent-hover rounded-lg flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-semibold text-primary-text">{{ $package->name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ number_format($package->count) }} bookings
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-semibold">
                                            ₱{{ number_format($package->revenue, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text font-medium">
                                            ₱{{ number_format($package->average, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end">
                                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                    <div class="bg-accent h-2 rounded-full" style="width: {{ min($package->percentage, 100) }}%"></div>
                                                </div>
                                                <span class="text-sm font-semibold text-primary-text">{{ number_format($package->percentage, 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gradient-to-r from-gray-100 to-gray-50 border-t-2 border-accent">
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-bold text-primary-text">TOTAL</td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-200 text-blue-900">
                                                {{ number_format($totalBookings) }} bookings
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-right text-green-600">
                                            ₱{{ number_format($totalRevenue, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-right text-primary-text">
                                            ₱{{ number_format($averageBookingValue, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-right text-primary-text">100%</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-primary-text mb-2">No Booking Data Available</h3>
                        <p class="text-sm text-secondary-text max-w-md mx-auto">There are no bookings recorded for the selected period. Booking performance data will appear here once customers start making reservations.</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportType = '{{ request('report_type', 'profit_loss') }}';
    const chartLabels = @json($chartLabels);
    
    // Common chart options for better UX
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                },
                displayColors: true
            }
        }
    };
    
    // Auto-submit form when filters change (except for custom date range)
    const reportTypeSelect = document.getElementById('report_type');
    const periodSelect = document.getElementById('period');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');
    const dateFromWrapper = document.getElementById('date_from_wrapper');
    const dateToWrapper = document.getElementById('date_to_wrapper');
    const applyFilterWrapper = document.getElementById('apply_filter_wrapper');
    const form = document.getElementById('reportFilterForm');
    
    // Toggle date fields and apply button based on period selection
    function toggleDateFields() {
        const isCustom = periodSelect.value === 'custom';
        dateFromWrapper.style.display = isCustom ? 'block' : 'none';
        dateToWrapper.style.display = isCustom ? 'block' : 'none';
        applyFilterWrapper.style.display = isCustom ? 'flex' : 'none';
        
        // Auto-submit if not custom period
        if (!isCustom && periodSelect.dataset.changed === 'true') {
            form.submit();
        }
    }
    
    // Auto-submit on report type change
    reportTypeSelect.addEventListener('change', function() {
        if (periodSelect.value !== 'custom') {
            form.submit();
        }
    });
    
    // Handle period change
    periodSelect.addEventListener('change', function() {
        periodSelect.dataset.changed = 'true';
        toggleDateFields();
    });
    
    // Initialize date field visibility
    toggleDateFields();
    
    // Create charts based on report type
    if (reportType === 'profit_loss' || reportType === '') {
        const hasData = chartLabels.length > 0;
        
        if (hasData) {
            // Profit & Loss Chart
            const plCtx = document.getElementById('profitLossChart').getContext('2d');
            new Chart(plCtx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($chartRevenue),
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Expenses',
                            data: @json($chartExpenses),
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2
                        },
                        {
                            label: 'Profit/Loss',
                            data: @json($chartProfit),
                            type: 'line',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    
    if (reportType === 'revenue') {
        const hasRevenue = {{ $totalRevenue > 0 ? 'true' : 'false' }};
        
        if (hasRevenue) {
            // Revenue Chart
            const revCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: @json($chartRevenue),
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(34, 197, 94, 1)'
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
            
            // Package Revenue Chart
            const packageData = @json($packageRevenueData);
            if (packageData.length > 0) {
                const pkgCtx = document.getElementById('packageRevenueChart').getContext('2d');
                new Chart(pkgCtx, {
                    type: 'doughnut',
                    data: {
                        labels: packageData.map(item => item.name),
                        datasets: [{
                            data: packageData.map(item => item.amount),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(217, 70, 239, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(99, 102, 241, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `₱${value.toLocaleString('en-US', {minimumFractionDigits: 2})} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Payment Method Chart
            const methodData = @json($paymentMethodData);
            if (methodData.length > 0) {
                const pmCtx = document.getElementById('paymentMethodChart').getContext('2d');
                new Chart(pmCtx, {
                    type: 'doughnut',
                    data: {
                        labels: methodData.map(item => item.name),
                        datasets: [{
                            data: methodData.map(item => item.amount),
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(217, 70, 239, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `₱${value.toLocaleString('en-US', {minimumFractionDigits: 2})} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
    
    if (reportType === 'expense') {
        const hasExpenses = {{ $totalExpenses > 0 ? 'true' : 'false' }};
        
        if (hasExpenses) {
            // Expense Chart
            const expCtx = document.getElementById('expenseChart').getContext('2d');
            new Chart(expCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Expenses',
                        data: @json($chartExpenses),
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgba(239, 68, 68, 1)'
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            ...commonOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return 'Expenses: ₱' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                }
            });
            
            // Expense Category Chart
            const catData = @json($expenseByCategory);
            if (catData.length > 0) {
                const catCtx = document.getElementById('expenseCategoryChart').getContext('2d');
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: catData.map(item => item.name),
                        datasets: [{
                            data: catData.map(item => item.amount),
                            backgroundColor: catData.map(item => item.color || 'rgba(99, 102, 241, 0.8)'),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `₱${value.toLocaleString('en-US', {minimumFractionDigits: 2})} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
    
    if (reportType === 'booking') {
        const hasBookings = {{ $totalBookings > 0 ? 'true' : 'false' }};
        
        if (hasBookings) {
            // Booking Chart - Compact & Mobile-Friendly
            const bookCtx = document.getElementById('bookingChart').getContext('2d');
            const isMobile = window.innerWidth < 640;
            
            new Chart(bookCtx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Bookings',
                        data: @json($chartBookings),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: isMobile ? 1.5 : 2.5,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: isMobile ? 10 : 12
                                }
                            },
                            grid: {
                                display: true,
                                drawBorder: false
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: isMobile ? 9 : 11
                                },
                                maxRotation: isMobile ? 45 : 0,
                                minRotation: isMobile ? 45 : 0
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: isMobile ? 8 : 12,
                            titleFont: {
                                size: isMobile ? 11 : 13
                            },
                            bodyFont: {
                                size: isMobile ? 10 : 12
                            },
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Bookings: ' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
            
            // Package Bookings Chart
            const pkgBookData = @json($packageBookingData);
            if (pkgBookData.length > 0) {
                const pkgBookCtx = document.getElementById('packageBookingsChart').getContext('2d');
                new Chart(pkgBookCtx, {
                    type: 'doughnut',
                    data: {
                        labels: pkgBookData.map(item => item.name),
                        datasets: [{
                            data: pkgBookData.map(item => item.count),
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(217, 70, 239, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(99, 102, 241, 0.8)'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${value} bookings (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
            
            // Booking Status Chart
            const statusData = @json($bookingStatusData);
            if (statusData.length > 0) {
                const statusCtx = document.getElementById('bookingStatusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusData.map(item => item.name),
                        datasets: [{
                            data: statusData.map(item => item.count),
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',  // completed
                                'rgba(59, 130, 246, 0.8)',  // confirmed
                                'rgba(245, 158, 11, 0.8)',  // pending
                                'rgba(239, 68, 68, 0.8)',   // cancelled
                                'rgba(99, 102, 241, 0.8)'   // no-show
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                ...commonOptions.plugins.tooltip,
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${value} bookings (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    }
});
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    
    canvas {
        max-height: 400px;
    }
}
</style>
@endsection
