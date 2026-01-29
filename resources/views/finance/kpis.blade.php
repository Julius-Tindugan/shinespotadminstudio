
@extends('layouts.app')
@section('title', 'Financial KPIs')
    @section('content')
        <div class="py-6" >

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" >

                <div class="flex justify-between items-center" >

                    <h1 class="text-2xl font-semibold text-primary-text" > Financial KPIs </h1>

                    <div>

                        <button id="exportKpiBtn" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg> Export KPI Data </button>

                        </div>

                    </div>
                    <!-- Date Filter -->
                        <div class="mt-6 bg-card-bg shadow-subtle rounded-lg p-4" >

                            <form action="{{ route('finance.kpis') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4" >

                                <div>
                                    <label for="period" class="block text-sm font-medium text-secondary-text" >Time Period</label>
                                    <select id="period" name="period" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                        <option value="monthly" {{ request('period', 'monthly') == 'monthly' ? 'selected' : '' }}>Current Month</option><option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Current Quarter</option><option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Current Year</option><option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Date Range</option>
                                    </select>

                                </div>

                                <div class="date-field" >
                                    <label for="date_from" class="block text-sm font-medium text-secondary-text" >Date From</label>
                                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from', now()->
                                        startOfMonth()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                    </div>

                                    <div class="date-field" >
                                        <label for="date_to" class="block text-sm font-medium text-secondary-text" >Date To</label>
                                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to', now()->
                                            format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >
                                        </div>

                                        <div class="md:col-span-3 flex justify-end" >

                                            <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg> Apply Filters </button>

                                            </div>

                                        </form>

                                    </div>
                                    <!-- KPI Summary Cards -->
                                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4" >
                                            <!-- Revenue Growth KPI -->
                                                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg" >

                                                    <div class="p-5" >

                                                        <div class="flex items-center justify-between" >

                                                            <div>
                                                                <dt class="text-sm font-medium text-secondary-text truncate" > Revenue Growth </dt><dd class="mt-1 text-2xl font-semibold text-primary-text" > {{ number_format($revenueGrowth, 1) }}% </dd>
                                                            </div>

                                                            <div class="h-12 w-12 flex items-center justify-center rounded-md {{ $revenueGrowth >
                                                                = 0 ? 'bg-green-100' : 'bg-red-100' }}" >
                                                                @if($revenueGrowth >= 0) <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                                                    @else
                                                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6" /></svg>
                                                                    @endif

                                                                </div>

                                                            </div>

                                                            <div class="mt-2 flex items-center text-sm" >
                                                                <span class="text-xs {{ $revenueGrowthTrend >= 0 ? 'text-green-600' : 'text-red-600' }}" > {{ $revenueGrowthTrend >= 0 ? '+' : '' }}{{ number_format($revenueGrowthTrend, 1) }}% </span><span class="text-xs text-secondary-text ml-2" >vs previous period</span>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <!-- Profit Margin KPI -->
                                                        <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg" >

                                                            <div class="p-5" >

                                                                <div class="flex items-center justify-between" >

                                                                    <div>
                                                                        <dt class="text-sm font-medium text-secondary-text truncate" > Profit Margin </dt><dd class="mt-1 text-2xl font-semibold text-primary-text" > {{ number_format($profitMargin, 1) }}% </dd>
                                                                    </div>

                                                                    <div class="h-12 w-12 flex items-center justify-center rounded-md {{ $profitMargin >
                                                                        = $targetProfitMargin ? 'bg-green-100' : 'bg-yellow-100' }}" >
                                                                        @if($profitMargin >= $targetProfitMargin) <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                                            @else
                                                                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                                            @endif

                                                                        </div>

                                                                    </div>

                                                                    <div class="mt-2 flex items-center text-sm" >
                                                                        <span class="text-xs {{ $profitMarginTrend >= 0 ? 'text-green-600' : 'text-red-600' }}" > {{ $profitMarginTrend >= 0 ? '+' : '' }}{{ number_format($profitMarginTrend, 1) }}% </span><span class="text-xs text-secondary-text ml-2" >vs previous period</span>
                                                                    </div>

                                                                </div>

                                                            </div>
                                                            <!-- Cost Efficiency KPI -->
                                                                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg" >

                                                                    <div class="p-5" >

                                                                        <div class="flex items-center justify-between" >

                                                                            <div>
                                                                                <dt class="text-sm font-medium text-secondary-text truncate" > Cost Efficiency </dt><dd class="mt-1 text-2xl font-semibold text-primary-text" > {{ number_format($costEfficiency, 1) }}% </dd>
                                                                            </div>

                                                                            <div class="h-12 w-12 flex items-center justify-center rounded-md {{ $costEfficiency <= $targetCostEfficiency ? 'bg-green-100' : 'bg-red-100' }}" >

                                                                                @if($costEfficiency <= $targetCostEfficiency) <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                                                    @else
                                                                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                                                    @endif

                                                                                </div>

                                                                            </div>

                                                                            <div class="mt-2 flex items-center text-sm" >
                                                                                <span class="text-xs {{ $costEfficiencyTrend <= 0 ? 'text-green-600' : 'text-red-600' }}" > {{ $costEfficiencyTrend <= 0 ? '' : '+' }}{{ number_format($costEfficiencyTrend, 1) }}% </span><span class="text-xs text-secondary-text ml-2" >vs previous period</span>
                                                                            </div>

                                                                        </div>

                                                                    </div>
                                                                    <!-- Average Transaction Value KPI -->
                                                                        <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg" >

                                                                            <div class="p-5" >

                                                                                <div class="flex items-center justify-between" >

                                                                                    <div>
                                                                                        <dt class="text-sm font-medium text-secondary-text truncate" > Avg. Transaction Value </dt><dd class="mt-1 text-2xl font-semibold text-primary-text" > ₱{{ number_format($avgTransactionValue, 0) }} </dd>
                                                                                    </div>

                                                                                    <div class="h-12 w-12 flex items-center justify-center rounded-md bg-blue-100" >
                                                                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                                                        </div>

                                                                                    </div>

                                                                                    <div class="mt-2 flex items-center text-sm" >
                                                                                        <span class="text-xs {{ $avgTransactionValueTrend >= 0 ? 'text-green-600' : 'text-red-600' }}" > {{ $avgTransactionValueTrend >= 0 ? '+' : '' }}{{ number_format($avgTransactionValueTrend, 1) }}% </span><span class="text-xs text-secondary-text ml-2" >vs previous period</span>
                                                                                    </div>

                                                                                </div>

                                                                            </div>

                                                                        </div>
                                                                        <!-- KPI Performance Trends -->
                                                                            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2" >
                                                                                <!-- Revenue vs Expense Trend -->
                                                                                    <div class="bg-card-bg shadow-subtle rounded-lg" >

                                                                                        <div class="px-6 py-4 border-b border-border-color" >

                                                                                            <h3 class="text-lg font-medium text-primary-text" >Revenue vs Expenses</h3>

                                                                                        </div>

                                                                                        <div class="p-6" >

                                                                                            <div class="h-80" >
                                                                                                <canvas id="revenueExpenseChart"></canvas>
                                                                                            </div>

                                                                                        </div>

                                                                                    </div>
                                                                                    <!-- Profit Margin Trend -->
                                                                                        <div class="bg-card-bg shadow-subtle rounded-lg" >

                                                                                            <div class="px-6 py-4 border-b border-border-color" >

                                                                                                <h3 class="text-lg font-medium text-primary-text" >Profit Margin Trend</h3>

                                                                                            </div>

                                                                                            <div class="p-6" >

                                                                                                <div class="h-80" >
                                                                                                    <canvas id="profitMarginChart"></canvas>
                                                                                                </div>

                                                                                            </div>

                                                                                        </div>

                                                                                    </div>
                                                                                    <!-- Additional KPIs -->
                                                                                        <div class="mt-6 bg-card-bg shadow-subtle rounded-lg" >

                                                                                            <div class="px-6 py-4 border-b border-border-color" >

                                                                                                <h3 class="text-lg font-medium text-primary-text" >Key Performance Indicators</h3>

                                                                                            </div>

                                                                                            <div class="p-6" >

                                                                                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6" >

                                                                                                    <div>

                                                                                                        <h4 class="text-base font-medium text-primary-text mb-4" >Financial Health</h4>
                                                                                                        <ul class="space-y-4" ><li class="flex flex-col space-y-1" >
                                                                                                            <div class="flex justify-between" >
                                                                                                                <span class="text-sm text-secondary-text" >Cash Flow</span><span class="text-sm font-medium text-primary-text" >₱{{ number_format($cashFlow, 2) }}</span>
                                                                                                            </div>

                                                                                                            <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                <div class="h-1.5 rounded-full {{ $cashFlow >
                                                                                                                    = 0 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, abs($cashFlow) / ($targetCashFlow > 0 ? $targetCashFlow : 1) * 100) }}%">
                                                                                                                </div>

                                                                                                            </div>

                                                                                                            <div class="flex justify-between text-xs" >
                                                                                                                <span>Target: ₱{{ number_format($targetCashFlow, 2) }}</span><span class="{{ $cashFlow >= $targetCashFlow ? 'text-green-600' : 'text-red-600' }}" > {{ $cashFlow >= $targetCashFlow ? 'On Target' : 'Below Target' }} </span>
                                                                                                            </div>
                                                                                                        </li><li class="flex flex-col space-y-1" >
                                                                                                            <div class="flex justify-between" >
                                                                                                                <span class="text-sm text-secondary-text" >Accounts Receivable</span><span class="text-sm font-medium text-primary-text" >₱{{ number_format($accountsReceivable, 2) }}</span>
                                                                                                            </div>

                                                                                                            <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                <div class="h-1.5 rounded-full {{ $accountsReceivable <= $targetAccountsReceivable ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ min(100, $accountsReceivable / ($targetAccountsReceivable * 1.5) * 100) }}%">

                                                                                                                </div>

                                                                                                            </div>

                                                                                                            <div class="flex justify-between text-xs" >
                                                                                                                <span>Target: < ₱{{ number_format($targetAccountsReceivable, 2) }}</span><span class="{{ $accountsReceivable <= $targetAccountsReceivable ? 'text-green-600' : 'text-yellow-600' }}" > {{ $accountsReceivable <= $targetAccountsReceivable ? 'Good' : 'Needs Attention' }} </span>
                                                                                                                </div>
                                                                                                            </li><li class="flex flex-col space-y-1" >
                                                                                                                <div class="flex justify-between" >
                                                                                                                    <span class="text-sm text-secondary-text" >Debt Ratio</span><span class="text-sm font-medium text-primary-text" >{{ number_format($debtRatio * 100, 1) }}%</span>
                                                                                                                </div>

                                                                                                                <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                    <div class="h-1.5 rounded-full {{ $debtRatio <= $targetDebtRatio ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, $debtRatio / 0.5 * 100) }}%">

                                                                                                                    </div>

                                                                                                                </div>

                                                                                                                <div class="flex justify-between text-xs" >
                                                                                                                    <span>Target: < {{ number_format($targetDebtRatio * 100, 1) }}%</span><span class="{{ $debtRatio <= $targetDebtRatio ? 'text-green-600' : 'text-red-600' }}" > {{ $debtRatio <= $targetDebtRatio ? 'Healthy' : 'Concerning' }} </span>
                                                                                                                    </div>
                                                                                                                </li></ul>
                                                                                                            </div>

                                                                                                            <div>

                                                                                                                <h4 class="text-base font-medium text-primary-text mb-4" >Operational Efficiency</h4>
                                                                                                                <ul class="space-y-4" ><li class="flex flex-col space-y-1" >
                                                                                                                    <div class="flex justify-between" >
                                                                                                                        <span class="text-sm text-secondary-text" >Revenue Per Employee</span><span class="text-sm font-medium text-primary-text" >₱{{ number_format($revenuePerEmployee, 2) }}</span>
                                                                                                                    </div>

                                                                                                                    <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                        <div class="h-1.5 rounded-full {{ $revenuePerEmployee >
                                                                                                                            = $targetRevenuePerEmployee ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ min(100, $revenuePerEmployee / ($targetRevenuePerEmployee * 1.2) * 100) }}%">
                                                                                                                        </div>

                                                                                                                    </div>

                                                                                                                    <div class="flex justify-between text-xs" >
                                                                                                                        <span>Target: ₱{{ number_format($targetRevenuePerEmployee, 2) }}</span><span class="{{ $revenuePerEmployee >= $targetRevenuePerEmployee ? 'text-green-600' : 'text-yellow-600' }}" > {{ $revenuePerEmployee >= $targetRevenuePerEmployee ? 'Efficient' : 'Below Target' }} </span>
                                                                                                                    </div>
                                                                                                                </li><li class="flex flex-col space-y-1" >
                                                                                                                    <div class="flex justify-between" >
                                                                                                                        <span class="text-sm text-secondary-text" >Days Sales Outstanding</span><span class="text-sm font-medium text-primary-text" >{{ $daysSalesOutstanding }} days</span>
                                                                                                                    </div>

                                                                                                                    <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                        <div class="h-1.5 rounded-full {{ $daysSalesOutstanding <= $targetDaysSalesOutstanding ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ min(100, $daysSalesOutstanding / 60 * 100) }}%">

                                                                                                                        </div>

                                                                                                                    </div>

                                                                                                                    <div class="flex justify-between text-xs" >
                                                                                                                        <span>Target: < {{ $targetDaysSalesOutstanding }} days</span><span class="{{ $daysSalesOutstanding <= $targetDaysSalesOutstanding ? 'text-green-600' : 'text-yellow-600' }}" > {{ $daysSalesOutstanding <= $targetDaysSalesOutstanding ? 'Good' : 'Needs Improvement' }} </span>
                                                                                                                        </div>
                                                                                                                    </li><li class="flex flex-col space-y-1" >
                                                                                                                        <div class="flex justify-between" >
                                                                                                                            <span class="text-sm text-secondary-text" >Booking Efficiency</span><span class="text-sm font-medium text-primary-text" >{{ number_format($bookingEfficiency * 100, 1) }}%</span>
                                                                                                                        </div>

                                                                                                                        <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                            <div class="h-1.5 rounded-full {{ $bookingEfficiency >
                                                                                                                                = $targetBookingEfficiency ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ min(100, $bookingEfficiency * 100) }}%">
                                                                                                                            </div>

                                                                                                                        </div>

                                                                                                                        <div class="flex justify-between text-xs" >
                                                                                                                            <span>Target: {{ number_format($targetBookingEfficiency * 100, 1) }}%</span><span class="{{ $bookingEfficiency >= $targetBookingEfficiency ? 'text-green-600' : 'text-yellow-600' }}" > {{ $bookingEfficiency >= $targetBookingEfficiency ? 'Efficient' : 'Below Target' }} </span>
                                                                                                                        </div>
                                                                                                                    </li></ul>
                                                                                                                </div>

                                                                                                                <div>

                                                                                                                    <h4 class="text-base font-medium text-primary-text mb-4" >Growth & Sustainability</h4>
                                                                                                                    <ul class="space-y-4" ><li class="flex flex-col space-y-1" >
                                                                                                                        <div class="flex justify-between" >
                                                                                                                            <span class="text-sm text-secondary-text" >ROI on Expenses</span><span class="text-sm font-medium text-primary-text" >{{ number_format($roi * 100, 1) }}%</span>
                                                                                                                        </div>

                                                                                                                        <div class="w-full bg-gray-200 rounded-full h-1.5" >

                                                                                                                            <div class="h-1.5 rounded-full {{ $roi >
                                                                                                                                = $targetROI ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, max(0, $roi) / ($targetROI * 1.2) * 100) }}%">
                                                                                                                            </div>

                                                                                                                        </div>

                                                                                                                        <div class="flex justify-between text-xs" >
                                                                                                                            <span>Target: {{ number_format($targetROI * 100, 1) }}%</span><span class="{{ $roi >= $targetROI ? 'text-green-600' : 'text-red-600' }}" > {{ $roi >= $targetROI ? 'Good Return' : 'Low Return' }} </span>
                                                                                                                        </div>
                                                                                                                    </li></ul>
                                                                                                                </div>

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>
                                                                                                    <!-- Target Setting -->
                                                                                                        <div class="mt-6 bg-card-bg shadow-subtle rounded-lg" >

                                                                                                            <div class="px-6 py-4 border-b border-border-color flex justify-between items-center" >

                                                                                                                <h3 class="text-lg font-medium text-primary-text" >KPI Target Settings</h3>

                                                                                                                <button id="editTargetsBtn" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Edit Targets </button>

                                                                                                            </div>

                                                                                                            <div class="p-6" >

                                                                                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" >

                                                                                                                    @foreach($kpiTargets as $category => $targets)
                                                                                                                        <div>

                                                                                                                            <h4 class="text-base font-medium text-primary-text mb-4" >{{ $category }}</h4>
                                                                                                                            <dl class="space-y-3" >
                                                                                                                                @foreach($targets as $name => $value)
                                                                                                                                    <div class="flex justify-between" >
                                                                                                                                        <dt class="text-sm text-secondary-text" >{{ $name }}</dt><dd class="text-sm font-medium text-primary-text" >
                                                                                                                                            @if(is_float($value) && $value <= 1) {{ number_format($value * 100, 1) }}%
                                                                                                                                                @else
                                                                                                                                                if(strpos($name, 'days') !== false || strpos($name, 'Days') !== false) {{ $value }} days
                                                                                                                                                @else
                                                                                                                                                if($value >= 1000) ₱{{ number_format($value, 0) }}
                                                                                                                                                @else
                                                                                                                                                {{ $value }}
                                                                                                                                            @endif
                                                                                                                                        </dd>
                                                                                                                                    </div>

                                                                                                                                @endforeach
                                                                                                                            </dl>
                                                                                                                        </div>

                                                                                                                    @endforeach

                                                                                                                </div>

                                                                                                            </div>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>
                                                                                                <!-- Target Edit Modal -->
                                                                                                    <div id="targetModal" class="fixed inset-0 flex items-center justify-center z-50 hidden" >

                                                                                                        <div class="absolute inset-0 bg-gray-900 bg-opacity-50" >

                                                                                                        </div>

                                                                                                        <div class="relative bg-card-bg rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto" >

                                                                                                            <div class="px-6 py-4 border-b border-border-color flex justify-between items-center" >

                                                                                                                <h3 class="text-lg font-medium text-primary-text" >Edit KPI Targets</h3>

                                                                                                                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-500" ><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>

                                                                                                                </div>

                                                                                                                <form action="{{ route('finance.kpis.targets.update') }}" method="POST" class="p-6" >
                                                                                                                    @csrf @method('PUT')
                                                                                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" >

                                                                                                                        <div>

                                                                                                                            <h4 class="text-base font-medium text-primary-text mb-4" >Financial Targets</h4>

                                                                                                                            <div class="space-y-4" >

                                                                                                                                <div>
                                                                                                                                    <label for="target_profit_margin" class="block text-sm font-medium text-secondary-text" >Profit Margin (%)</label>
                                                                                                                                    <input type="number" step="0.1" id="target_profit_margin" name="targets[profit_margin]" value="{{ number_format($targetProfitMargin * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                    </div>

                                                                                                                                    <div>
                                                                                                                                        <label for="target_cost_efficiency" class="block text-sm font-medium text-secondary-text" >Cost Efficiency (%)</label>
                                                                                                                                        <input type="number" step="0.1" id="target_cost_efficiency" name="targets[cost_efficiency]" value="{{ number_format($targetCostEfficiency * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                        </div>

                                                                                                                                        <div>
                                                                                                                                            <label for="target_cash_flow" class="block text-sm font-medium text-secondary-text" >Cash Flow Target (₱)</label>
                                                                                                                                            <input type="number" id="target_cash_flow" name="targets[cash_flow]" value="{{ $targetCashFlow }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                            </div>

                                                                                                                                            <div>
                                                                                                                                                <label for="target_accounts_receivable" class="block text-sm font-medium text-secondary-text" >Accounts Receivable Limit (₱)</label>
                                                                                                                                                <input type="number" id="target_accounts_receivable" name="targets[accounts_receivable]" value="{{ $targetAccountsReceivable }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                </div>

                                                                                                                                                <div>
                                                                                                                                                    <label for="target_debt_ratio" class="block text-sm font-medium text-secondary-text" >Debt Ratio Target (%)</label>
                                                                                                                                                    <input type="number" step="0.1" id="target_debt_ratio" name="targets[debt_ratio]" value="{{ number_format($targetDebtRatio * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                    </div>

                                                                                                                                                </div>

                                                                                                                                            </div>

                                                                                                                                            <div>

                                                                                                                                                <h4 class="text-base font-medium text-primary-text mb-4" >Operational & Growth Targets</h4>

                                                                                                                                                <div class="space-y-4" >

                                                                                                                                                    <div>
                                                                                                                                                        <label for="target_revenue_per_employee" class="block text-sm font-medium text-secondary-text" >Revenue Per Employee (₱)</label>
                                                                                                                                                        <input type="number" id="target_revenue_per_employee" name="targets[revenue_per_employee]" value="{{ $targetRevenuePerEmployee }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                        </div>

                                                                                                                                                        <div>
                                                                                                                                                            <label for="target_days_sales_outstanding" class="block text-sm font-medium text-secondary-text" >Days Sales Outstanding (days)</label>
                                                                                                                                                            <input type="number" id="target_days_sales_outstanding" name="targets[days_sales_outstanding]" value="{{ $targetDaysSalesOutstanding }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                            </div>

                                                                                                                                                            <div>
                                                                                                                                                                <label for="target_booking_efficiency" class="block text-sm font-medium text-secondary-text" >Booking Efficiency Target (%)</label>
                                                                                                                                                                <input type="number" step="0.1" id="target_booking_efficiency" name="targets[booking_efficiency]" value="{{ number_format($targetBookingEfficiency * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                                </div>

                                                                                                                                                                <div>
                                                                                                                                                                    <label for="target_client_retention_rate" class="block text-sm font-medium text-secondary-text" >Client Retention Rate (%)</label>
                                                                                                                                                                    <input type="number" step="0.1" id="target_client_retention_rate" name="targets[client_retention_rate]" value="{{ number_format($targetClientRetentionRate * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                                    </div>

                                                                                                                                                                    <div>
                                                                                                                                                                        <label for="target_new_client_rate" class="block text-sm font-medium text-secondary-text" >New Client Acquisition Target (%)</label>
                                                                                                                                                                        <input type="number" step="0.1" id="target_new_client_rate" name="targets[new_client_rate]" value="{{ $targetNewClientRate }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                                        </div>

                                                                                                                                                                        <div>
                                                                                                                                                                            <label for="target_roi" class="block text-sm font-medium text-secondary-text" >ROI Target (%)</label>
                                                                                                                                                                            <input type="number" step="0.1" id="target_roi" name="targets[roi]" value="{{ number_format($targetROI * 100, 1) }}" class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50" >

                                                                                                                                                                            </div>

                                                                                                                                                                        </div>

                                                                                                                                                                    </div>

                                                                                                                                                                </div>

                                                                                                                                                                <div class="mt-6 flex justify-end" >

                                                                                                                                                                    <button type="button" id="cancelTargetsBtn" class="mr-3 inline-flex justify-center py-2 px-4 border border-border-color rounded-md shadow-sm text-sm font-medium text-primary-text bg-input-bg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Cancel </button>

                                                                                                                                                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Save Changes </button>

                                                                                                                                                                </div>

                                                                                                                                                            </form>

                                                                                                                                                        </div>

                                                                                                                                                    </div>
                                                                                                                                                @endsection
                                                                                                                                                @section('scripts') <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script><script> document.addEventListener('DOMContentLoaded', function() { // Toggle date fields based on period selection const periodSelect = document.getElementById('period'); const dateFields = document.querySelectorAll('.date-field'); function toggleDateFields() { const isCustom = periodSelect.value === 'custom'; dateFields.forEach(field => { field.style.display = isCustom ? 'block' : 'none'; }); } periodSelect.addEventListener('change', toggleDateFields); toggleDateFields(); // Call on page load // Revenue vs Expense Chart const revenueExpenseCtx = document.getElementById('revenueExpenseChart').getContext('2d'); new Chart(revenueExpenseCtx, { type: 'bar', data: { labels: @json($chartLabels), datasets: [ { label: 'Revenue', data: @json($chartRevenue), backgroundColor: 'rgba(34, 197, 94, 0.7)', borderColor: 'rgba(34, 197, 94, 1)', borderWidth: 1 }, { label: 'Expenses', data: @json($chartExpenses), backgroundColor: 'rgba(239, 68, 68, 0.7)', borderColor: 'rgba(239, 68, 68, 1)', borderWidth: 1 } ] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString(); } } } } } }); // Profit Margin Chart const profitMarginCtx = document.getElementById('profitMarginChart').getContext('2d'); new Chart(profitMarginCtx, { type: 'line', data: { labels: @json($chartLabels), datasets: [ { label: 'Profit Margin', data: @json($chartMargins), backgroundColor: 'rgba(59, 130, 246, 0.2)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 2, fill: true, tension: 0.3 }, { label: 'Target', data: Array({{ count($chartLabels) }}).fill({{ $targetProfitMargin * 100 }}), borderColor: 'rgba(217, 70, 239, 0.7)', borderWidth: 2, borderDash: [5, 5], fill: false, pointRadius: 0 } ] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return value + '%'; } } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y.toFixed(1) + '%'; } } } } } }); // Modal functionality for KPI targets const targetModal = document.getElementById('targetModal'); const editTargetsBtn = document.getElementById('editTargetsBtn'); const closeModalBtn = document.getElementById('closeModalBtn'); const cancelTargetsBtn = document.getElementById('cancelTargetsBtn'); function openModal() { targetModal.classList.remove('hidden'); } function closeModal() { targetModal.classList.add('hidden'); } editTargetsBtn.addEventListener('click', openModal); closeModalBtn.addEventListener('click', closeModal); cancelTargetsBtn.addEventListener('click', closeModal); // Export KPI functionality document.getElementById('exportKpiBtn').addEventListener('click', function() { window.location.href = "{{ route('finance.kpis.export') }}?" + new URLSearchParams({ period: document.getElementById('period').value, date_from: document.getElementById('date_from').value, date_to: document.getElementById('date_to').value }).toString(); }); }); </script> @endsection