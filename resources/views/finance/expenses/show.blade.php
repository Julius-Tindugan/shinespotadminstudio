
@extends('layouts.app')
@section('title', 'Expense Details')
    @section('content')
        <div class="py-6" >

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" >

                <div class="md:flex md:items-center md:justify-between" >

                    <div class="flex-1 min-w-0" >

                        <h2 class="text-2xl font-semibold text-primary-text leading-tight" > Expense Details </h2>

                    </div>

                    <div class="mt-4 flex md:mt-0 space-x-3" >
                        <a href="{{ route('finance.expenses.edit', $expense->expense_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg> Edit </a><a href="{{ route('finance.expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Back </a>
                            </div>

                        </div>

                        <div class="mt-6 bg-card-bg shadow-subtle overflow-hidden rounded-lg" >

                            <div class="px-4 py-5 sm:p-6" >
                                <!-- Expense Information -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" >

                                        <div class="md:col-span-2" >

                                            <h3 class="text-lg font-medium text-primary-text mb-4" >Expense Information</h3>
                                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2" >
                                                <div class="sm:col-span-2" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Title </dt><dd class="mt-1 text-lg font-semibold text-primary-text" > {{ $expense->title }} </dd>
                                                </div>

                                                <div class="sm:col-span-1" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Amount </dt><dd class="mt-1 text-lg font-semibold text-primary-text" > ₱{{ number_format($expense->amount, 2) }} </dd>
                                                </div>

                                                <div class="sm:col-span-1" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Category </dt><dd class="mt-1 text-sm text-primary-text flex items-center" > <span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: {{ $expense->categoryRelation->color_code ?? '#808080' }}"></span> {{ $expense->categoryRelation->name ?? $expense->category ?? 'Uncategorized' }} </dd>
                                                </div>

                                                <div class="sm:col-span-1" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Expense Date </dt><dd class="mt-1 text-sm text-primary-text" > {{ $expense->expense_date->format('M d, Y') }} </dd>
                                                </div>

                                                <div class="sm:col-span-1" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Vendor/Supplier </dt><dd class="mt-1 text-sm text-primary-text" > {{ $expense->vendor ?: 'Not specified' }} </dd>
                                                </div>

                                                <div class="sm:col-span-1" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Receipt/Invoice Number </dt><dd class="mt-1 text-sm text-primary-text" > {{ $expense->receipt_number ?: 'Not specified' }} </dd>
                                                </div>

                                                <div class="sm:col-span-2" >
                                                    <dt class="text-sm font-medium text-secondary-text" > Description </dt><dd class="mt-1 text-sm text-primary-text whitespace-pre-wrap" > {{ $expense->description ?: 'No description provided.' }} </dd>
                                                </div>
                                            </dl>
                                            @if($expense->booking)
                                                <div class="mt-6 pt-6 border-t border-border-color" >

                                                    <h4 class="text-base font-medium text-primary-text mb-3" >Related Booking</h4>

                                                    <div class="bg-gray-50 p-4 rounded-md" >

                                                        <div class="flex justify-between" >

                                                            <div>

                                                                <p class="text-sm font-medium text-primary-text" ><a href="{{ route('bookings.show', $expense->booking->booking_id) }}" class="text-accent hover:text-accent-hover" > {{ $expense->booking->booking_reference }} </a></p>
                                                                <!-- Client information has been removed -->
                                                                    <p class="text-xs text-secondary-text mt-1" > Package: {{ $expense->booking->package->title ?? 'No Package' }} </p>

                                                                </div>

                                                                <div class="text-right" >

                                                                    <p class="text-sm text-primary-text" > {{ $expense->booking->booking_date ? \Carbon\Carbon::parse($expense->booking->booking_date)->format('M d, Y') : 'No date' }} </p>

                                                                    <p class="text-xs text-secondary-text mt-1" > Status: <span @class([ 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full', 'bg-green-100 text-green-800' => in_array($expense->booking->status, ['confirmed', 'completed']), 'bg-yellow-100 text-yellow-800' => in_array($expense->booking->status, ['pending', 'tentative']), 'bg-red-100 text-red-800' => in_array($expense->booking->status, ['cancelled', 'no-show']), 'bg-blue-100 text-blue-800' => $expense->booking->status == 'in-progress', ])> {{ ucfirst($expense->booking->status) }} </span></p>

                                                                    <p class="text-xs text-secondary-text mt-1" > Total: ₱{{ number_format($expense->booking->total_amount, 2) }} </p>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                @endif

                                            </div>

                                            <div>

                                                <h3 class="text-lg font-medium text-primary-text mb-4" >Receipt Image</h3>

                                                @if($expense->receipt_image)
                                                    <div class="bg-gray-50 rounded-md p-4 text-center" >

                                                        <div class="mb-4" >
                                                            <img src="{{ asset('storage/' . $expense->receipt_image) }}" alt="Receipt for {{ $expense->title }}" class="max-w-full h-auto mx-auto rounded-md shadow-md" >
                                                        </div>
                                                        <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-accent hover:bg-accent-hover focus:outline-none focus:border-accent-700 focus:shadow-outline-accent active:bg-accent-800 transition ease-in-out duration-150" ><svg class="-ml-0.5 mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> View Full Size </a>
                                                        </div>

                                                        @else

                                                        <div class="bg-gray-50 rounded-md p-8 text-center" >

                                                            <div class="text-secondary-text" >
                                                                <svg class="h-16 w-16 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                                    <p class="mt-2 text-sm" >No receipt image uploaded</p>

                                                                </div>

                                                            </div>

                                                        @endif
                                                        <!-- Additional expense stats or info can go here -->
                                                            <div class="mt-6 bg-gray-50 rounded-md p-4" >

                                                                <h4 class="text-sm font-medium text-secondary-text mb-2" >Additional Information</h4>
                                                                <dl class="grid grid-cols-1 gap-y-3" >
                                                                    <div class="grid grid-cols-2 gap-x-4" >
                                                                        <dt class="text-xs text-secondary-text" >Created</dt><dd class="text-xs text-primary-text text-right" > {{ $expense->created_at->format('M d, Y h:i A') }} </dd>
                                                                    </div>

                                                                    @if($expense->created_at->ne($expense->updated_at))
                                                                        <div class="grid grid-cols-2 gap-x-4" >
                                                                            <dt class="text-xs text-secondary-text" >Last Updated</dt><dd class="text-xs text-primary-text text-right" > {{ $expense->updated_at->format('M d, Y h:i A') }} </dd>
                                                                        </div>

                                                                    @endif

                                                                    <div class="grid grid-cols-2 gap-x-4" >
                                                                        <dt class="text-xs text-secondary-text" >Created By</dt><dd class="text-xs text-primary-text text-right" > {{ $expense->created_by->name ?? 'Unknown' }} </dd>
                                                                    </div>
                                                                </dl>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <!-- Similar Expenses -->
                                                        @if(count($similarExpenses) > 0)
                                                            <div class="mt-8 pt-8 border-t border-border-color" >

                                                                <h3 class="text-lg font-medium text-primary-text mb-4" >Similar Expenses</h3>

                                                                <div class="overflow-x-auto" >
                                                                    <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Title </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Category </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Amount </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Date </th><th scope="col" class="relative px-6 py-3" ><span class="sr-only" >View</span></th></tr></thead><tbody class="bg-card-bg divide-y divide-border-color" >
                                                                        @foreach($similarExpenses as $similar) <tr><td class="px-6 py-4" >
                                                                            <div class="text-sm font-medium text-primary-text" >
                                                                                {{ $similar->title }}
                                                                            </div>
                                                                        </td><td class="px-6 py-4 whitespace-nowrap" >
                                                                            <div class="flex items-center" >
                                                                                <div class="flex-shrink-0 h-3 w-3 rounded-full mr-2" style="background-color: {{ $similar->categoryRelation->color_code ?? '#808080' }}">

                                                                                </div>

                                                                                <div class="text-sm text-primary-text" >
                                                                                    {{ $similar->categoryRelation->name ?? 'Uncategorized' }}
                                                                                </div>

                                                                            </div>
                                                                        </td><td class="px-6 py-4 whitespace-nowrap" >
                                                                            <div class="text-sm font-medium text-primary-text" >
                                                                                ₱{{ number_format($similar->amount, 2) }}
                                                                            </div>
                                                                        </td><td class="px-6 py-4 whitespace-nowrap" >
                                                                            <div class="text-sm text-primary-text" >
                                                                                {{ $similar->expense_date->format('M d, Y') }}
                                                                            </div>
                                                                        </td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" ><a href="{{ route('finance.expenses.show', $similar->expense_id) }}" class="text-accent hover:text-accent-hover" > View </a></td></tr>
                                                                    @endforeach
                                                                </tbody></table>
                                                            </div>

                                                        </div>

                                                    @endif

                                                </div>

                                                <div class="px-4 py-4 bg-gray-50 sm:px-6 border-t border-border-color" >

                                                    <div class="flex justify-between" >

                                                        <div>

                                                            @if($expense->receipt_image) <a href="{{ asset('storage/' . $expense->receipt_image) }}" target="_blank" download="{{ $expense->title }}_receipt" class="inline-flex items-center px-4 py-2 border border-border-color rounded-md shadow-sm text-sm font-medium text-primary-text bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg> Download Receipt </a>
                                                            @endif

                                                        </div>

                                                        <div>

                                                            <button onclick="confirmDelete('{{ $expense->expense_id }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" type="button"><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Delete Expense </button>

                                                                <form id="delete-form-{{ $expense->expense_id }}" action="{{ route('finance.expenses.destroy', $expense->expense_id) }}" method="POST" class="hidden" > @csrf @method('DELETE')
                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                        <!-- Delete Confirmation Modal -->
                                            <div id="deleteModal" class="fixed z-[9999] inset-0 overflow-y-auto hidden" >

                                                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" >

                                                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">

                                                        <div class="absolute inset-0 bg-gray-500 opacity-75" >

                                                        </div>

                                                    </div>
                                                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                    <div class="inline-block align-bottom bg-card-bg rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" >

                                                        <div class="bg-card-bg px-4 pt-5 pb-4 sm:p-6 sm:pb-4" >

                                                            <div class="sm:flex sm:items-start" >

                                                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10" >
                                                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                                    </div>

                                                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left" >

                                                                        <h3 class="text-lg leading-6 font-medium text-primary-text" id="modal-title"> Delete Expense </h3>

                                                                        <div class="mt-2" >

                                                                            <p class="text-sm text-secondary-text" > Are you sure you want to delete this expense record? This action cannot be undone. </p>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse" >

                                                                <button onclick="executeDelete()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" > Delete </button>

                                                                <button onclick="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-border-color shadow-sm px-4 py-2 bg-card-bg text-base font-medium text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" > Cancel </button>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>
                                            @endsection
                                            @section('scripts') <script> let deleteId = null; function confirmDelete(id) { deleteId = id; document.getElementById('deleteModal').classList.remove('hidden'); } function closeModal() { document.getElementById('deleteModal').classList.add('hidden'); deleteId = null; } function executeDelete() { if (deleteId) { document.getElementById('delete-form-' + deleteId).submit(); } } // Close modal when clicking outside window.onclick = function(event) { const modal = document.getElementById('deleteModal'); if (event.target === modal) { closeModal(); } } </script> @endsection