@extends('layouts.app')
@section('title', 'Financial Reports')
@section('content')
    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center">

                <h1 class="text-2xl font-semibold text-primary-text"> Financial Reports </h1>

                <div>

                    <button id="printReportBtn"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"><svg
                            class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg> Print Report </button>

                </div>

            </div>
            <!-- Report Filters -->
            <div class="mt-6 bg-card-bg shadow-subtle rounded-lg p-4">

                <form action="{{ route('finance.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label for="report_type" class="block text-sm font-medium text-secondary-text">Report Type</label>
                        <select id="report_type" name="report_type"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="profit_loss"
                                {{ request('report_type', 'profit_loss') == 'profit_loss' ? 'selected' : '' }}>Profit & Loss
                            </option>
                            <option value="revenue" {{ request('report_type') == 'revenue' ? 'selected' : '' }}>Revenue
                                Analysis</option>
                            <option value="expense" {{ request('report_type') == 'expense' ? 'selected' : '' }}>Expense
                                Analysis</option>
                            <option value="booking" {{ request('report_type') == 'booking' ? 'selected' : '' }}>Booking
                                Performance</option>
                        </select>

                    </div>

                    <div>
                        <label for="period" class="block text-sm font-medium text-secondary-text">Time Period</label>
                        <select id="period" name="period"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="monthly" {{ request('period', 'monthly') == 'monthly' ? 'selected' : '' }}>
                                Monthly</option>
                            <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>Quarterly
                            </option>
                            <option value="yearly" {{ request('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Date Range
                            </option>
                        </select>

                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-secondary-text">Date From</label>
                        <input type="date" id="date_from" name="date_from"
                            value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-secondary-text">Date To</label>
                        <input type="date" id="date_to" name="date_to"
                            value="{{ request('date_to', now()->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                    </div>

                    <div class="md:col-span-4 flex justify-end">

                        <button type="submit"
                            class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"><svg
                                class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg> Generate Report </button>

                    </div>

                </form>

            </div>

            <div id="reportContent">
                <!-- Report Header -->
                <div class="mt-6 text-center">

                    <h2 class="text-xl font-bold text-primary-text"> {{ $reportTitle }} </h2>

                    <p class="text-sm text-secondary-text mt-1"> {{ $reportDateRange }} </p>

                </div>
                <!-- Financial Summary -->
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

                    <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                        <div class="p-5">

                            <div class="flex items-center">

                                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-secondary-text truncate"> Total Revenue </dt>
                                        <dd>
                                            <div class="text-lg font-semibold text-primary-text">
                                                ₱{{ number_format($totalRevenue, 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                        <div class="p-5">

                            <div class="flex items-center">

                                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </div>

                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-secondary-text truncate"> Total Expenses </dt>
                                        <dd>
                                            <div class="text-lg font-semibold text-primary-text">
                                                ₱{{ number_format($totalExpenses, 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                        <div class="p-5">

                            <div class="flex items-center">

                                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>

                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-secondary-text truncate"> Net Profit </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-primary-text {{ $netProfit < 0 ? 'text-red-600' : '' }}">
                                                ₱{{ number_format($netProfit, 2) }}
                                            </div>
                                        </dd>
                                    </dl>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                        <div class="p-5">

                            <div class="flex items-center">

                                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>

                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-secondary-text truncate"> Profit Margin </dt>
                                        <dd>
                                            <div
                                                class="text-lg font-semibold text-primary-text {{ $profitMargin < 0 ? 'text-red-600' : '' }}">
                                                {{ number_format($profitMargin, 1) }}%
                                            </div>
                                        </dd>
                                    </dl>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                @if (request('report_type', 'profit_loss') == 'profit_loss') <!-- Profit & Loss Report -->
                    <div class="mt-6">

                        <div class="bg-card-bg shadow-subtle rounded-lg">

                            <div class="px-6 py-4 border-b border-border-color">

                                <h3 class="text-lg font-medium text-primary-text">Profit & Loss Statement</h3>

                            </div>

                            <div class="p-6">
                                <!-- Main Chart -->
                                <div class="mb-8">
                                    <canvas id="profitLossChart" height="300"></canvas>
                                </div>
                                <!-- Detailed P&L Table -->
                                <table class="min-w-full divide-y divide-border-color">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Period </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Revenue </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Expenses </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Profit/Loss </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Margin </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-card-bg divide-y divide-border-color">
                                        @foreach ($periodData as $period => $data)
                                            <tr>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">
                                                    {{ $period }} </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                    ₱{{ number_format($data['revenue'], 2) }} </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                    ₱{{ number_format($data['expenses'], 2) }} </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right {{ $data['profit'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    ₱{{ number_format($data['profit'], 2) }} </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-right {{ $data['margin'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ number_format($data['margin'], 1) }}% </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr>
                                            <th scope="row"
                                                class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                Total </th>
                                            <th scope="row"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                ₱{{ number_format($totalRevenue, 2) }} </th>
                                            <th scope="row"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                ₱{{ number_format($totalExpenses, 2) }} </th>
                                            <th scope="row"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider {{ $netProfit < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                ₱{{ number_format($netProfit, 2) }} </th>
                                            <th scope="row"
                                                class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider {{ $profitMargin < 0 ? 'text-red-600' : 'text-green-600' }}">
                                                {{ number_format($profitMargin, 1) }}% </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>

                    </div>

                @endif

                @if (request('report_type') == 'revenue') <!-- Revenue Analysis Report -->
                    <div class="mt-6">

                        <div class="bg-card-bg shadow-subtle rounded-lg">

                            <div class="px-6 py-4 border-b border-border-color">

                                <h3 class="text-lg font-medium text-primary-text">Revenue Analysis</h3>

                            </div>

                            <div class="p-6">

                                <div class="mb-8">
                                    <canvas id="revenueChart" height="300"></canvas>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Revenue by Package</h4>

                                        <div class="h-64">
                                            <canvas id="packageRevenueChart"></canvas>
                                        </div>

                                    </div>

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Revenue by Payment Method
                                        </h4>

                                        <div class="h-64">
                                            <canvas id="paymentMethodChart"></canvas>
                                        </div>

                                    </div>

                                </div>
                                <!-- Top Revenue Sources -->
                                <div class="mt-8">

                                    <h4 class="text-base font-medium text-primary-text mb-4">Top Revenue Sources</h4>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-border-color">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Source </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Amount </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        % of Total </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-card-bg divide-y divide-border-color">
                                                @foreach ($revenueSources as $source)
                                                    <tr>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">
                                                            {{ $source['name'] }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            ₱{{ number_format($source['amount'], 2) }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            {{ number_format($source['percentage'], 1) }}% </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

                @if (request('report_type') == 'expense') <!-- Expense Analysis Report -->
                    <div class="mt-6">

                        <div class="bg-card-bg shadow-subtle rounded-lg">

                            <div class="px-6 py-4 border-b border-border-color">

                                <h3 class="text-lg font-medium text-primary-text">Expense Analysis</h3>

                            </div>

                            <div class="p-6">

                                <div class="mb-8">
                                    <canvas id="expenseChart" height="300"></canvas>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Expenses by Category</h4>

                                        <div class="h-64">
                                            <canvas id="expenseCategoryChart"></canvas>
                                        </div>

                                    </div>

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Top Expense Items</h4>
                                        <ul class="space-y-3">
                                            @foreach ($topExpenses as $expense)
                                                <li class="flex items-center">
                                                    <div class="w-3 h-3 rounded-full mr-3"
                                                        style="background-color: {{ $expense['color'] ?? '#808080' }}">

                                                    </div>

                                                    <div class="flex-grow">

                                                        <div class="flex justify-between">
                                                            <span class="text-sm font-medium text-primary-text">
                                                                {{ $expense['title'] }} </span><span
                                                                class="text-sm text-secondary-text">
                                                                ₱{{ number_format($expense['amount'], 2) }} </span>
                                                        </div>

                                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">

                                                            <div class="h-1.5 rounded-full"
                                                                style="width: {{ $expense['percentage'] }}%; background-color: {{ $expense['color'] ?? '#808080' }}">

                                                            </div>

                                                        </div>

                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>

                                </div>
                                <!-- Expense Categories Breakdown -->
                                <div class="mt-8">

                                    <h4 class="text-base font-medium text-primary-text mb-4">Expense Categories Breakdown
                                    </h4>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-border-color">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Category </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Amount </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        % of Total </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Transactions </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Avg. Amount </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-card-bg divide-y divide-border-color">
                                                @foreach ($expenseCategories as $category)
                                                    <tr>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">
                                                            <div class="flex items-center">

                                                                <div class="h-3 w-3 rounded-full mr-2"
                                                                    style="background-color: {{ $category['color'] ?? '#808080' }}">

                                                                </div>
                                                                {{ $category['name'] }}
                                                            </div>
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            ₱{{ number_format($category['amount'], 2) }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            {{ number_format($category['percentage'], 1) }}% </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            {{ $category['count'] }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            ₱{{ number_format($category['average'], 2) }} </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr>
                                                    <th scope="row"
                                                        class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Total </th>
                                                    <th scope="row"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        ₱{{ number_format($totalExpenses, 2) }} </th>
                                                    <th scope="row"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        100% </th>
                                                    <th scope="row"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        {{ $totalExpenseCount }} </th>
                                                    <th scope="row"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        ₱{{ number_format($averageExpense, 2) }} </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

                @if (request('report_type') == 'booking') <!-- Booking Performance Report -->
                    <div class="mt-6">

                        <div class="bg-card-bg shadow-subtle rounded-lg">

                            <div class="px-6 py-4 border-b border-border-color">

                                <h3 class="text-lg font-medium text-primary-text">Booking Performance</h3>

                            </div>

                            <div class="p-6">

                                <div class="mb-8">
                                    <canvas id="bookingChart" height="300"></canvas>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

                                    <div class="bg-blue-50 rounded-lg p-4">

                                        <div class="text-sm font-medium text-blue-800">
                                            Total Bookings
                                        </div>

                                        <div class="mt-1 text-2xl font-bold text-blue-900">
                                            {{ $totalBookings }}
                                        </div>

                                    </div>

                                    <div class="bg-green-50 rounded-lg p-4">

                                        <div class="text-sm font-medium text-green-800">
                                            Completed Bookings
                                        </div>

                                        <div class="mt-1 text-2xl font-bold text-green-900">
                                            {{ $completedBookings }}
                                        </div>

                                        <div class="text-xs text-green-700">
                                            {{ $completedBookingsPercentage }}% of total
                                        </div>

                                    </div>

                                    <div class="bg-yellow-50 rounded-lg p-4">

                                        <div class="text-sm font-medium text-yellow-800">
                                            Average Booking Value
                                        </div>

                                        <div class="mt-1 text-2xl font-bold text-yellow-900">
                                            ₱{{ number_format($averageBookingValue, 2) }}
                                        </div>

                                    </div>

                                    <div class="bg-purple-50 rounded-lg p-4">

                                        <div class="text-sm font-medium text-purple-800">
                                            Revenue Per Booking
                                        </div>

                                        <div class="mt-1 text-2xl font-bold text-purple-900">
                                            ₱{{ number_format($revenuePerBooking, 2) }}
                                        </div>

                                    </div>

                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Bookings by Package</h4>

                                        <div class="h-64">
                                            <canvas id="packageBookingsChart"></canvas>
                                        </div>

                                    </div>

                                    <div>

                                        <h4 class="text-base font-medium text-primary-text mb-4">Booking Status
                                            Distribution</h4>

                                        <div class="h-64">
                                            <canvas id="bookingStatusChart"></canvas>
                                        </div>

                                    </div>

                                </div>
                                <!-- Top Packages Table -->
                                <div class="mt-8">

                                    <h4 class="text-base font-medium text-primary-text mb-4">Package Performance</h4>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-border-color">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Package </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Bookings </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Revenue </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        Avg. Revenue </th>
                                                    <th scope="col"
                                                        class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider">
                                                        % of Total Revenue </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-card-bg divide-y divide-border-color">
                                                @foreach ($packagePerformance as $package)
                                                    <tr>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text">
                                                            {{ $package['title'] }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            {{ $package['count'] }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            ₱{{ number_format($package['revenue'], 2) }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            ₱{{ number_format($package['average'], 2) }} </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-primary-text">
                                                            {{ number_format($package['percentage'], 1) }}% </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
                const reportType = '{{ request('report_type', 'profit_loss') }}';
                const chartLabels =
                @json($chartLabels); // Print report functionality document.getElementById('printReportBtn').addEventListener('click', function() { const printContents = document.getElementById('reportContent').innerHTML; const originalContents = document.body.innerHTML; document.body.innerHTML = `
                <
                div class = "p-8" >

                <
                h1 class = "text-2xl font-bold mb-6 text-center" > {{ $reportTitle }} < /h1>

                    <
                    p class = "text-center mb-8" > {{ $reportDateRange }} < /p>
                $ {
                    printContents
                } <
                /div>
                `; window.print(); document.body.innerHTML = originalContents; location.reload(); }); // Create charts based on report type if (reportType === 'profit_loss' || reportType === '') { // Profit & Loss Chart const plCtx = document.getElementById('profitLossChart').getContext('2d'); new Chart(plCtx, { type: 'bar', data: { labels: chartLabels, datasets: [ { label: 'Revenue', data: @json($chartRevenue), backgroundColor: 'rgba(34, 197, 94, 0.5)', borderColor: 'rgba(34, 197, 94, 1)', borderWidth: 1 }, { label: 'Expenses', data: @json($chartExpenses), backgroundColor: 'rgba(239, 68, 68, 0.5)', borderColor: 'rgba(239, 68, 68, 1)', borderWidth: 1 }, { label: 'Profit/Loss', data: @json($chartProfit), type: 'line', borderColor: 'rgba(59, 130, 246, 1)', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2, fill: false, tension: 0.1 } ] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString(); } } } } } }); } if (reportType === 'revenue') { // Revenue Chart const revCtx = document.getElementById('revenueChart').getContext('2d'); new Chart(revCtx, { type: 'line', data: { labels: chartLabels, datasets: [{ label: 'Revenue', data: @json($chartRevenue), borderColor: 'rgba(34, 197, 94, 1)', backgroundColor: 'rgba(34, 197, 94, 0.1)', borderWidth: 2, fill: true, tension: 0.3 }] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString(); } } } } } }); // Package Revenue Chart const packageData = @json($packageRevenueData); const pkgCtx = document.getElementById('packageRevenueChart').getContext('2d'); new Chart(pkgCtx, { type: 'pie', data: { labels: packageData.map(item => item.name), datasets: [{ data: packageData.map(item => item.amount), backgroundColor: [ 'rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(217, 70, 239, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(99, 102, 241, 0.7)' ], borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 12 } }, tooltip: { callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((acc, val) => acc + val, 0); const percentage = ((value / total) * 100).toFixed(1); return `₱
                $ {
                    value.toLocaleString()
                }($ {
                    percentage
                } %
                )`; } } } } } }); // Payment Method Chart const methodData = @json($paymentMethodData); const pmCtx = document.getElementById('paymentMethodChart').getContext('2d'); new Chart(pmCtx, { type: 'doughnut', data: { labels: methodData.map(item => item.name), datasets: [{ data: methodData.map(item => item.amount), backgroundColor: [ 'rgba(16, 185, 129, 0.7)', 'rgba(59, 130, 246, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(217, 70, 239, 0.7)', 'rgba(239, 68, 68, 0.7)' ], borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 12 } }, tooltip: { callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((acc, val) => acc + val, 0); const percentage = ((value / total) * 100).toFixed(1); return `₱
                $ {
                    value.toLocaleString()
                }($ {
                    percentage
                } %
                )`; } } } } } }); } if (reportType === 'expense') { // Expense Chart const expCtx = document.getElementById('expenseChart').getContext('2d'); new Chart(expCtx, { type: 'line', data: { labels: chartLabels, datasets: [{ label: 'Expenses', data: @json($chartExpenses), borderColor: 'rgba(239, 68, 68, 1)', backgroundColor: 'rgba(239, 68, 68, 0.1)', borderWidth: 2, fill: true, tension: 0.3 }] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString(); } } } } } }); // Expense Category Chart const catData = @json($expenseCategories); const catCtx = document.getElementById('expenseCategoryChart').getContext('2d'); new Chart(catCtx, { type: 'doughnut', data: { labels: catData.map(item => item.name), datasets: [{ data: catData.map(item => item.amount), backgroundColor: catData.map(item => item.color || 'rgba(99, 102, 241, 0.7)'), borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 12 } }, tooltip: { callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((acc, val) => acc + val, 0); const percentage = ((value / total) * 100).toFixed(1); return `₱
                $ {
                    value.toLocaleString()
                }($ {
                    percentage
                } %
                )`; } } } } } }); } if (reportType === 'booking') { // Booking Chart const bookCtx = document.getElementById('bookingChart').getContext('2d'); new Chart(bookCtx, { type: 'bar', data: { labels: chartLabels, datasets: [{ label: 'Bookings', data: @json($chartBookings), backgroundColor: 'rgba(59, 130, 246, 0.5)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }] }, options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { tooltip: { callbacks: { label: function(context) { return context.dataset.label + ': ' + context.parsed.y; } } } } } }); // Package Bookings Chart const pkgBookData = @json($packageBookingData); const pkgBookCtx = document.getElementById('packageBookingsChart').getContext('2d'); new Chart(pkgBookCtx, { type: 'pie', data: { labels: pkgBookData.map(item => item.name), datasets: [{ data: pkgBookData.map(item => item.count), backgroundColor: [ 'rgba(59, 130, 246, 0.7)', 'rgba(16, 185, 129, 0.7)', 'rgba(245, 158, 11, 0.7)', 'rgba(217, 70, 239, 0.7)', 'rgba(239, 68, 68, 0.7)', 'rgba(99, 102, 241, 0.7)' ], borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 12 } }, tooltip: { callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((acc, val) => acc + val, 0); const percentage = ((value / total) * 100).toFixed(1); return `
                $ {
                    value
                }
                bookings($ {
                        percentage
                    } %
                    )`; } } } } } }); // Booking Status Chart const statusData = @json($bookingStatusData); const statusCtx = document.getElementById('bookingStatusChart').getContext('2d'); new Chart(statusCtx, { type: 'doughnut', data: { labels: statusData.map(item => item.name), datasets: [{ data: statusData.map(item => item.count), backgroundColor: [ 'rgba(16, 185, 129, 0.7)', // completed 'rgba(59, 130, 246, 0.7)', // confirmed 'rgba(245, 158, 11, 0.7)', // pending 'rgba(217, 70, 239, 0.7)', // in-progress 'rgba(239, 68, 68, 0.7)', // cancelled 'rgba(99, 102, 241, 0.7)' // no-show ], borderWidth: 1 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { boxWidth: 12 } }, tooltip: { callbacks: { label: function(context) { const value = context.parsed; const total = context.dataset.data.reduce((acc, val) => acc + val, 0); const percentage = ((value / total) * 100).toFixed(1); return `
                $ {
                    value
                }
                bookings($ {
                        percentage
                    } %
                    )`; } } } } } }); } // Toggle date inputs based on period selection const periodSelect = document.getElementById('period'); const dateFields = document.querySelectorAll('#date_from, #date_to'); function toggleDateFields() { const isCustom = periodSelect.value === 'custom'; dateFields.forEach(field => { field.parentElement.style.display = isCustom ? 'block' : 'none'; }); } periodSelect.addEventListener('change', toggleDateFields); toggleDateFields(); // Call on page load }); 
</script> @endsection
