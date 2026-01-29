@extends('layouts.app')
@section('title', isset($expense) ? 'Edit Expense' : 'Create Expense')
@section('content')
    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="md:flex md:items-center md:justify-between">

                <div class="flex-1 min-w-0">

                    <h2 class="text-2xl font-semibold text-primary-text leading-tight">
                        {{ isset($expense) ? 'Edit Expense' : 'Create New Expense' }} </h2>

                </div>

                <div class="mt-4 flex md:mt-0">
                    <a href="{{ route('finance.expenses.index') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"><svg
                            class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg> Back to Expenses </a>
                </div>

            </div>

            <div class="mt-6 bg-card-bg shadow-subtle overflow-hidden rounded-lg">

                <form
                    action="{{ isset($expense) ? route('finance.expenses.update', $expense->expense_id) : route('finance.expenses.store') }}"
                    method="POST" class="space-y-6 p-6" enctype="multipart/form-data"> @csrf
                    @if (isset($expense))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Title -->
                        <div class="sm:col-span-4">
                            <label for="title" class="block text-sm font-medium text-secondary-text"> Title </label>
                            <div class="mt-1">

                                <input type="text" name="title" id="title"
                                    value="{{ old('title', $expense->title ?? '') }}"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('title') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="Enter expense title" required>
                            </div>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Amount -->
                        <div class="sm:col-span-2">
                            <label for="amount" class="block text-sm font-medium text-secondary-text"> Amount (₱) </label>
                            <div class="mt-1 relative rounded-md shadow-sm">

                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>

                                <input type="number" name="amount" id="amount" step="0.01" min="0"
                                    value="{{ old('amount', isset($expense) ? $expense->amount : '') }}"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full pl-7 sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('amount') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="0.00" required>
                            </div>
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Category -->
                        <div class="sm:col-span-3">
                            <label for="category" class="block text-sm font-medium text-secondary-text"> Category </label>
                            <div class="mt-1">

                                <select id="category" name="category"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('category') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->name }}"
                                            {{ old('category', $expense->category ?? null) == $category->name ? 'selected' : '' }}>
                                            {{ $category->name }} </option>
                                    @endforeach

                                </select>

                            </div>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Expense Date -->
                        <div class="sm:col-span-3">
                            <label for="expense_date" class="block text-sm font-medium text-secondary-text"> Expense Date
                            </label>
                            <div class="mt-1">

                                <input type="date" name="expense_date" id="expense_date"
                                    value="{{ old('expense_date', isset($expense) ? $expense->expense_date->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('expense_date') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    required>
                            </div>
                            @error('expense_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Booking Selection -->
                        <div class="sm:col-span-6">
                            <label for="booking_id" class="block text-sm font-medium text-secondary-text"> Related Booking
                                (Optional) </label>
                            <div class="mt-1">

                                <select id="booking_id" name="booking_id"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('booking_id') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">
                                    <option value="">No related booking</option>
                                    @foreach ($bookings as $booking)
                                        <option value="{{ $booking->booking_id }}"
                                            {{ old('booking_id', $expense->booking_id ?? null) == $booking->booking_id ? 'selected' : '' }}>
                                            {{ $booking->booking_reference }} - {{ $booking->client_first_name }}
                                            {{ $booking->client_last_name }} -
                                            {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') : 'No date' }}
                                        </option>
                                    @endforeach

                                </select>

                            </div>
                            @error('booking_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Vendor -->
                        <div class="sm:col-span-3">
                            <label for="vendor" class="block text-sm font-medium text-secondary-text"> Vendor/Supplier
                                (Optional) </label>
                            <div class="mt-1">

                                <input type="text" name="vendor" id="vendor"
                                    value="{{ old('vendor', $expense->vendor ?? '') }}"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('vendor') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="Name of vendor or supplier">
                            </div>
                            @error('vendor')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Receipt Number -->
                        <div class="sm:col-span-3">
                            <label for="receipt_number" class="block text-sm font-medium text-secondary-text">
                                Receipt/Invoice Number (Optional) </label>
                            <div class="mt-1">

                                <input type="text" name="receipt_number" id="receipt_number"
                                    value="{{ old('receipt_number', $expense->receipt_number ?? '') }}"
                                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('receipt_number') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                    placeholder="Receipt or invoice reference">
                            </div>
                            @error('receipt_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Receipt Image -->
                        <div class="sm:col-span-6">
                            <label for="receipt_image" class="block text-sm font-medium text-secondary-text"> Receipt
                                Image (Optional) </label>
                            <div class="mt-1 flex items-center">

                                <div class="flex-grow">

                                    <input type="file" name="receipt_image" id="receipt_image"
                                        class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('receipt_image') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror"
                                        accept="image/*">

                                </div>

                                @if (isset($expense) && $expense->receipt_image)
                                    <div class="ml-3">
                                        <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank"
                                            class="text-accent hover:text-accent-hover">View Current Receipt</a>
                                    </div>
                                @endif

                            </div>

                            <p class="mt-1 text-xs text-secondary-text">Upload JPG, PNG, or PDF file (max 2MB)</p>
                            @error('receipt_image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Description -->
        <!-- Description -->
        <div class="sm:col-span-6">
            <label for="description" class="block text-sm font-medium text-secondary-text"> Description (Optional)
            </label>
            <div class="mt-1">

                <textarea id="description" name="description" rows="4" placeholder="Enter additional details about this expense..."
                    class="shadow-sm focus:ring-accent focus:border-accent block w-full sm:text-sm border-border-color bg-input-bg text-primary-text rounded-md @error('description') border-red-300 text-red-900 placeholder-red-300 focus:outline-none focus:ring-red-500 focus:border-red-500 @enderror">{{ old('description', $expense->description ?? '') }}</textarea>

            </div>                    </div>

                    <div class="pt-5 border-t border-border-color">

                        <div class="flex justify-start">
                            <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                                {{ isset($expense) ? 'Update Expense' : 'Create Expense' }} </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
