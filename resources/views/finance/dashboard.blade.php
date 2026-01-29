
@extends('layouts.app')
@section('title', 'Finance Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary-text">Finance Dashboard</h1>
            <p class="mt-2 text-sm text-secondary-text">Monitor your financial performance and track revenue, expenses, and profitability</p>
        </div>
        
        <!-- Financial Summary Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Monthly Revenue Card -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg transition-all duration-200 hover:shadow-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-secondary-text truncate">Monthly Revenue</dt>
                                <dd>
                                    <div class="text-2xl font-bold text-primary-text">
                                        ₱{{ number_format($monthlyRevenue, 2) }}
                                    </div>
                                    @if($monthlyRevenue > 0)
                                        <p class="text-xs text-green-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Active revenue stream
                                            </span>
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">No revenue this month</p>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-border-color">
                    <div class="text-sm">
                        <a href="{{ route('finance.reports') }}?type=revenue" class="font-medium text-accent hover:text-accent-hover transition-colors">
                            View detailed report →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monthly Expenses Card -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg transition-all duration-200 hover:shadow-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-secondary-text truncate">Monthly Expenses</dt>
                                <dd>
                                    <div class="text-2xl font-bold text-primary-text">
                                        ₱{{ number_format($monthlyExpenses, 2) }}
                                    </div>
                                    @if($monthlyExpenses > 0)
                                        <p class="text-xs text-orange-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Track spending
                                            </span>
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">No expenses recorded</p>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-border-color">
                    <div class="text-sm">
                        <a href="{{ route('finance.expenses.index') }}" class="font-medium text-accent hover:text-accent-hover transition-colors">
                            View all expenses →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Net Profit Card -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg transition-all duration-200 hover:shadow-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 {{ $netProfit >= 0 ? 'bg-blue-100' : 'bg-red-100' }} rounded-md p-3">
                            <svg class="h-6 w-6 {{ $netProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-secondary-text truncate">Net Profit</dt>
                                <dd>
                                    <div class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                                        ₱{{ number_format($netProfit, 2) }}
                                    </div>
                                    @if($netProfit > 0)
                                        <p class="text-xs text-green-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Profitable
                                            </span>
                                        </p>
                                    @elseif($netProfit < 0)
                                        <p class="text-xs text-red-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                </svg>
                                                Loss incurred
                                            </span>
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 mt-1">Break even</p>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-border-color">
                    <div class="text-sm">
                        <a href="{{ route('finance.reports') }}?type=profit_loss" class="font-medium text-accent hover:text-accent-hover transition-colors">
                            View P&L statement →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Payments Card -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg transition-all duration-200 hover:shadow-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-secondary-text truncate">Pending Payments</dt>
                                <dd>
                                    <div class="text-2xl font-bold text-primary-text">
                                        ₱{{ number_format($pendingPayments, 2) }}
                                    </div>
                                    @if($pendingPayments > 0)
                                        <p class="text-xs text-yellow-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                Requires attention
                                            </span>
                                        </p>
                                    @else
                                        <p class="text-xs text-green-600 mt-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                All caught up
                                            </span>
                                        </p>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-5 py-3 border-t border-border-color">
                    <div class="text-sm">
                        <a href="{{ route('finance.payments.index') }}?status=pending" class="font-medium text-accent hover:text-accent-hover transition-colors">
                            View pending →
                        </a>
                    </div>
                </div>
            </div>
        </div>
                
        <!-- Revenue & Expenses Chart -->
        <div class="mt-8">
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">
                <div class="px-6 py-5 border-b border-border-color">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-primary-text">Revenue & Expenses Overview</h2>
                            <p class="mt-1 text-sm text-secondary-text">6-month financial trend comparison</p>
                        </div>
                        <div class="mt-3 sm:mt-0 flex flex-wrap gap-3">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-800 shadow-sm">
                                <span class="w-3 h-3 mr-2 bg-green-500 rounded-full"></span>
                                Revenue
                            </span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-red-100 text-red-800 shadow-sm">
                                <span class="w-3 h-3 mr-2 bg-red-500 rounded-full"></span>
                                Expenses
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(array_sum($chartRevenue) > 0 || array_sum($chartExpenses) > 0)
                        <div class="relative" style="height: 350px;">
                            <canvas id="revenueExpensesChart"></canvas>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16">
                            <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Financial Data Available</h3>
                            <p class="text-sm text-gray-500 text-center max-w-md">
                                Start recording payments and expenses to visualize your financial trends over time.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <a href="{{ route('finance.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-medium rounded-lg hover:bg-accent-hover transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Payment
                                </a>
                                <a href="{{ route('finance.expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Expense
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- Recent Payments and Expenses Section -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Payments -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">
                <div class="px-6 py-4 flex justify-between items-center border-b border-border-color">
                    <div>
                        <h2 class="text-lg font-bold text-primary-text">Recent Payments</h2>
                        <p class="mt-1 text-xs text-secondary-text">Latest incoming transactions</p>
                    </div>
                    <a href="{{ route('finance.payments.index') }}" class="text-sm font-medium text-accent hover:text-accent-hover transition-colors inline-flex items-center">
                        View all
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @if($recentPayments->count() > 0)
                    <ul class="divide-y divide-border-color">
                        @foreach($recentPayments as $payment)
                            <li class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div @class([
                                            'flex-shrink-0 w-2.5 h-2.5 rounded-full mr-3',
                                            'bg-green-500' => $payment->status == 'completed',
                                            'bg-yellow-500' => $payment->status == 'pending',
                                            'bg-red-500' => $payment->status == 'failed',
                                        ])></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-primary-text truncate">
                                                {{ $payment->booking->package->title ?? 'No Package' }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-secondary-text">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $payment->payment_date->format('M d, Y') }}</span>
                                                <span class="mx-2">•</span>
                                                <span class="capitalize">{{ ucfirst($payment->payment_method) }}</span>
                                                <span class="mx-2">•</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $payment->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <div class="text-base font-bold text-green-600">
                                            +₱{{ number_format($payment->amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-6 py-12">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <h3 class="mt-4 text-base font-semibold text-gray-700">No Payments Yet</h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                                Payment transactions will appear here once customers start booking services.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('finance.payments.index') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-medium rounded-lg hover:bg-accent-hover transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Record Payment
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Expenses -->
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">
                <div class="px-6 py-4 flex justify-between items-center border-b border-border-color">
                    <div>
                        <h2 class="text-lg font-bold text-primary-text">Recent Expenses</h2>
                        <p class="mt-1 text-xs text-secondary-text">Latest outgoing transactions</p>
                    </div>
                    <a href="{{ route('finance.expenses.index') }}" class="text-sm font-medium text-accent hover:text-accent-hover transition-colors inline-flex items-center">
                        View all
                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                @if($recentExpenses->count() > 0)
                    <ul class="divide-y divide-border-color">
                        @foreach($recentExpenses as $expense)
                            <li class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1 min-w-0">
                                        <div class="flex-shrink-0 w-2.5 h-2.5 rounded-full mr-3" 
                                             style="background-color: {{ $expense->category->color_code ?? '#808080' }}">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-primary-text truncate">
                                                {{ $expense->title }}
                                            </p>
                                            <div class="flex items-center mt-1 text-xs text-secondary-text">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ $expense->expense_date->format('M d, Y') }}</span>
                                                <span class="mx-2">•</span>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">
                                                    {{ $expense->categoryRelation->name ?? 'Uncategorized' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <div class="text-base font-bold text-red-600">
                                            -₱{{ number_format($expense->amount, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-6 py-12">
                        <div class="text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            <h3 class="mt-4 text-base font-semibold text-gray-700">No Expenses Recorded</h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                                Start tracking your business expenses to get insights into your spending patterns.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('finance.expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-medium rounded-lg hover:bg-accent-hover transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Expense
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Expense Categories -->
        <div class="mt-8">
            <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">
                <div class="px-6 py-5 border-b border-border-color">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-primary-text">Expense Categories Breakdown</h2>
                            <p class="mt-1 text-sm text-secondary-text">Distribution of expenses by category this month</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($topExpenseCategories->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Pie Chart -->
                            <div class="flex items-center justify-center">
                                <div class="w-full max-w-sm" style="height: 300px;">
                                    <canvas id="expenseCategoriesChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Category List -->
                            <div class="flex flex-col justify-center">
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Category Details</h3>
                                <ul class="space-y-4">
                                    @foreach($topExpenseCategories as $index => $category)
                                        <li class="group">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center flex-1">
                                                    <div class="w-4 h-4 rounded-full mr-3 shadow-sm" 
                                                         style="background-color: {{ $category->categoryRelation->color_code ?? '#808080' }}">
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-sm font-semibold text-primary-text">
                                                                {{ $category->categoryRelation->name ?? ($category->category ?: 'Unknown') }}
                                                            </span>
                                                            <span class="ml-3 text-base font-bold text-primary-text">
                                                                ₱{{ number_format($category->total, 2) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-full bg-gray-200">
                                                    <div style="width: {{ ($category->total / $topExpenseCategories->sum('total')) * 100 }}%; background-color: {{ $category->categoryRelation->color_code ?? '#808080' }}" 
                                                         class="shadow-sm flex flex-col text-center whitespace-nowrap text-white justify-center transition-all duration-500 group-hover:shadow-md">
                                                    </div>
                                                </div>
                                                <span class="text-xs text-gray-500 mt-1 inline-block">
                                                    {{ number_format(($category->total / $topExpenseCategories->sum('total')) * 100, 1) }}% of total expenses
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                @if($topExpenseCategories->count() > 0)
                                    <div class="mt-6 pt-4 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-semibold text-gray-700">Total Expenses</span>
                                            <span class="text-lg font-bold text-red-600">
                                                ₱{{ number_format($topExpenseCategories->sum('total'), 2) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-16">
                            <svg class="w-20 h-20 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">No Expense Categories</h3>
                            <p class="text-sm text-gray-500 text-center max-w-md mb-6">
                                Track your expenses by category to understand where your money goes and identify cost-saving opportunities.
                            </p>
                            <div class="flex gap-3">
                                <a href="{{ route('finance.expenses.index') }}" class="inline-flex items-center px-4 py-2 bg-accent text-white text-sm font-medium rounded-lg hover:bg-accent-hover transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Expense
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', 'system-ui', '-apple-system', 'sans-serif'";
    Chart.defaults.color = '#6B7280';
    
    // Revenue & Expenses Chart
    const hasRevenueExpensesData = {{ (array_sum($chartRevenue) > 0 || array_sum($chartExpenses) > 0) ? 'true' : 'false' }};
    
    if (hasRevenueExpensesData) {
        const revExpCtx = document.getElementById('revenueExpensesChart');
        if (revExpCtx) {
            const revenueExpensesChart = new Chart(revExpCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Revenue',
                            data: @json($chartRevenue),
                            backgroundColor: 'rgba(34, 197, 94, 0.8)',
                            borderColor: 'rgba(34, 197, 94, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(34, 197, 94, 0.9)',
                        },
                        {
                            label: 'Expenses',
                            data: @json($chartExpenses),
                            backgroundColor: 'rgba(239, 68, 68, 0.8)',
                            borderColor: 'rgba(239, 68, 68, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                            hoverBackgroundColor: 'rgba(239, 68, 68, 0.9)',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                font: {
                                    size: 12,
                                    weight: '500'
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                            },
                            ticks: {
                                callback: function(value) {
                                    return '₱' + value.toLocaleString('en-PH', {
                                        minimumFractionDigits: 0,
                                        maximumFractionDigits: 0
                                    });
                                },
                                font: {
                                    size: 11
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: '600'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            bodySpacing: 6,
                            usePointStyle: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += '₱' + context.parsed.y.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                    return label;
                                },
                                footer: function(tooltipItems) {
                                    let sum = 0;
                                    tooltipItems.forEach(function(tooltipItem) {
                                        sum += tooltipItem.parsed.y;
                                    });
                                    return 'Total: ₱' + sum.toLocaleString('en-PH', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }
    }
    
    // Expense Categories Pie Chart
    const expCatData = @json($topExpenseCategories->map(function($category) {
        return [
            'label' => $category->categoryRelation ? $category->categoryRelation->name : ($category->category ?: 'Unknown'),
            'value' => $category->total,
            'color' => $category->categoryRelation ? $category->categoryRelation->color_code : '#808080'
        ];
    }));
    
    const hasExpenseData = expCatData.length > 0;
    
    if (hasExpenseData) {
        const expCatCtx = document.getElementById('expenseCategoriesChart');
        if (expCatCtx) {
            const expenseCategoriesChart = new Chart(expCatCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: expCatData.map(item => item.label),
                    datasets: [{
                        data: expCatData.map(item => item.value),
                        backgroundColor: expCatData.map(item => item.color),
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverBorderWidth: 4,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            },
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return [
                                        context.label,
                                        '₱' + value.toLocaleString('en-PH', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        }),
                                        percentage + '% of total'
                                    ];
                                }
                            }
                        }
                    },
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    }
});
</script>
@endsection