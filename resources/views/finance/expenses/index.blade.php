@extends('layouts.app')
@section('title', 'Expense Management')
@section('content')
    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex justify-between items-center">

                <h1 class="text-2xl font-semibold text-primary-text"> Expense Management </h1>

                <div>
                    <a href="{{ route('finance.expenses.create') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"><svg
                            class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg> New Expense </a>
                </div>

            </div>
            <!-- Filters -->
            <div class="mt-6 bg-card-bg shadow-subtle rounded-lg p-4">

                <form action="{{ route('finance.expenses.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label for="category" class="block text-sm font-medium text-secondary-text">Category</label>
                        <select id="category" name="category"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}"
                                    {{ request('category') == $category->name ? 'selected' : '' }}> {{ $category->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    <div>
                        <label for="booking_id" class="block text-sm font-medium text-secondary-text">Related
                            Booking</label>
                        <select id="booking_id" name="booking_id"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                            <option value="">All Bookings</option>
                            <option value="none" {{ request('booking_id') == 'none' ? 'selected' : '' }}>No Booking
                            </option>
                            @foreach ($bookings as $booking)
                                <option value="{{ $booking->booking_id }}"
                                    {{ request('booking_id') == $booking->booking_id ? 'selected' : '' }}>
                                    {{ $booking->booking_reference }} - {{ $booking->client_first_name }}
                                    {{ $booking->client_last_name }} </option>
                            @endforeach

                        </select>

                    </div>

                    <div>
                        <label for="date_from" class="block text-sm font-medium text-secondary-text">Date From</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div>
                        <label for="date_to" class="block text-sm font-medium text-secondary-text">Date To</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div class="md:col-span-3">
                        <label for="search" class="block text-sm font-medium text-secondary-text">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by title, description, or vendor..."
                            class="mt-1 block w-full rounded-md border-border-color bg-input-bg text-primary-text shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">

                    </div>

                    <div class="flex items-end">

                        <button type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"><svg
                                class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg> Filter </button>

                    </div>

                </form>

            </div>
            <!-- Expense Summary Cards -->
            <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Total Expenses -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="p-5">

                        <div class="flex items-center">

                            <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
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
                <!-- This Month -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="p-5">

                        <div class="flex items-center">

                            <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate"> This Month </dt>
                                    <dd>
                                        <div class="text-lg font-semibold text-primary-text">
                                            ₱{{ number_format($thisMonthExpenses, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>

                        </div>

                    </div>

                </div>
                <!-- Average Expense -->
                <div class="bg-card-bg overflow-hidden shadow-subtle rounded-lg">

                    <div class="p-5">

                        <div class="flex items-center">

                            <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                                <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-secondary-text truncate"> Average Expense </dt>
                                    <dd>
                                        <div class="text-lg font-semibold text-primary-text">
                                            ₱{{ number_format($averageExpense, 2) }}
                                        </div>
                                    </dd>
                                </dl>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
            <!-- Expenses Table -->
            <div class="mt-6 bg-card-bg shadow-subtle rounded-lg overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border-color">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Title/Description </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Category </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Amount </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Date </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                    Related To </th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-card-bg divide-y divide-border-color">
                            @forelse($expenses as $expense)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-primary-text">
                                            {{ $expense->title }}
                                        </div>

                                        <div class="text-sm text-secondary-text">
                                            {{ Str::limit($expense->description, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">

                                            <div class="flex-shrink-0 h-3 w-3 rounded-full mr-2"
                                                style="background-color: {{ $expense->categoryRelation->color_code ?? '#808080' }}">
                                            </div>

                                            <div class="text-sm text-primary-text">
                                                {{ $expense->categoryRelation->name ?? 'Uncategorized' }}
                                            </div>

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-primary-text">
                                            ₱{{ number_format($expense->amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-primary-text">
                                            {{ $expense->expense_date->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-primary-text">

                                            @if ($expense->booking)
                                                <a href="{{ route('bookings.show', $expense->booking->booking_id) }}"
                                                    class="text-accent hover:text-accent-hover">
                                                    {{ $expense->booking->booking_reference }} </a>
                                            @else
                                                <span class="text-secondary-text">No booking</span>
                                            @endif

                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-3">
                                            <a href="{{ route('finance.expenses.show', $expense->expense_id) }}"
                                                class="inline-block text-accent hover:text-accent-hover" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('finance.expenses.edit', $expense->expense_id) }}"
                                                class="inline-block text-accent hover:text-accent-hover" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <button type="button"
                                                onclick="confirmDelete({{ $expense->expense_id }});"
                                                class="inline-block text-red-600 hover:text-red-900 focus:outline-none relative z-10"
                                                title="Delete"
                                                style="cursor: pointer; min-width: 20px; min-height: 20px; padding: 2px;">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 pointer-events-none" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            <form id="delete-form-{{ $expense->expense_id }}"
                                                action="{{ route('finance.expenses.destroy', $expense->expense_id) }}"
                                                method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                            </tr> @empty <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-secondary-text"> No
                                        expenses found. <a href="{{ route('finance.expenses.create') }}"
                                            class="text-accent hover:text-accent-hover">Create a new expense</a>. </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-border-color">
                    {{ $expenses->withQueryString()->links() }}
                </div>

            </div>
            <!-- Expense Categories Chart -->
            <div class="mt-6">

                <div class="bg-card-bg shadow-subtle rounded-lg overflow-hidden">

                    <div class="px-6 py-4 border-b border-border-color">

                        <h3 class="text-lg font-medium text-primary-text">Expense Breakdown by Category</h3>

                    </div>

                    <div class="p-6">

                        <div class="flex flex-col md:flex-row">

                            <div class="w-full md:w-1/2">
                                <canvas id="expenseCategoriesChart" height="300"></canvas>
                            </div>

                            <div class="w-full md:w-1/2 mt-6 md:mt-0 md:pl-6">
                                <ul class="space-y-4">
                                    @foreach ($expensesByCategory as $category)
                                        <li class="flex items-center">
                                            <div class="w-3 h-3 rounded-full mr-3"
                                                style="background-color: {{ $category->color_code ?? '#808080' }}">
                                            </div>

                                            <div class="flex-grow">

                                                <div class="flex justify-between">
                                                    <span class="text-sm font-medium text-primary-text">
                                                        {{ $category->name }} </span><span
                                                        class="text-sm text-secondary-text">
                                                        ₱{{ number_format($category->total, 2) }} </span>
                                                </div>

                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">

                                                    <div class="h-1.5 rounded-full"
                                                        style="width: {{ ($category->total / $totalExpenses) * 100 }}%; background-color: {{ $category->color_code ?? '#808080' }}">
                                                    </div>

                                                </div>

                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed z-[9999] inset-0 overflow-y-auto hidden">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

            <div class="fixed inset-0 transition-opacity" aria-hidden="true">

                <div class="absolute inset-0 bg-gray-500 opacity-75">

                </div>

            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-card-bg rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <div class="bg-card-bg px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                    <div class="sm:flex sm:items-start">

                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">

                            <h3 class="text-lg leading-6 font-medium text-primary-text" id="modal-title"> Delete Expense
                            </h3>

                            <div class="mt-2">

                                <p class="text-sm text-secondary-text"> Are you sure you want to delete this expense
                                    record? This action cannot be undone. </p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">

                    <button onclick="executeDelete()" type="button"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete </button>

                    <button onclick="closeModal()" type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-border-color shadow-sm px-4 py-2 bg-card-bg text-base font-medium text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel </button>

                </div>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        // Delete Confirmation
        let deleteId = null;

        function confirmDelete(id) {
            console.log('Delete button clicked for ID:', id);
            deleteId = id;
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeModal() {
            const modal = document.getElementById('deleteModal');
            if (modal) {
                modal.classList.add('hidden');
            }
            deleteId = null;
        }

        function executeDelete() {
            if (deleteId) {
                const form = document.getElementById('delete-form-' + deleteId);
                if (form) {
                    form.submit();
                }
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Expense Categories Chart
        document.addEventListener('DOMContentLoaded', function() {
            const categoryData = @json($expensesByCategory->map(function($category) {
                return [
                    'name' => $category->name,
                    'total' => $category->total,
                    'color' => $category->color_code ?? '#808080'
                ];
            }));

            const ctx = document.getElementById('expenseCategoriesChart').getContext('2d');
            const expenseCategoriesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.name),
                    datasets: [{
                        data: categoryData.map(item => item.total),
                        backgroundColor: categoryData.map(item => item.color),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${context.label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
