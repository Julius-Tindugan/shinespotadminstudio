
@extends('layouts.app')
@section('title', 'Payment Details')
    @section('content')
        <div class="py-6" >

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" >

                <div class="md:flex md:items-center md:justify-between" >

                    <div class="flex-1 min-w-0" >

                        <h2 class="text-2xl font-semibold text-primary-text leading-tight" > Payment Details </h2>

                    </div>

                    <div class="mt-4 flex md:mt-0 space-x-3" >
                        <a href="{{ route('finance.payments.edit', $payment->transaction_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg> Edit </a><a href="{{ route('finance.payments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" ><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg> Back </a>
                            </div>

                        </div>

                        <div class="mt-6 bg-card-bg shadow-subtle overflow-hidden rounded-lg" >

                            <div class="px-4 py-5 sm:p-6" >
                                <!-- Payment Status Banner -->
                                    <div @class([ 'mb-6 rounded-md p-4', 'bg-green-50 border border-green-200' =>
                                        $payment->status == 'completed', 'bg-yellow-50 border border-yellow-200' => $payment->status == 'pending', 'bg-red-50 border border-red-200' => $payment->status == 'failed', ])>
                                        <div class="flex" >

                                            <div class="flex-shrink-0" >

                                                @if($payment->status == 'completed') <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                    @elseif($payment->status == 'pending') <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                                        @else
                                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                                        @endif

                                                    </div>

                                                    <div class="ml-3" >

                                                        <h3 @class([ 'text-sm font-medium', 'text-green-800' => $payment->status == 'completed', 'text-yellow-800' => $payment->status == 'pending', 'text-red-800' => $payment->status == 'failed', ])> Payment {{ ucfirst($payment->status) }} </h3>

                                                        <div @class([ 'mt-2 text-sm', 'text-green-700' =>
                                                            $payment->status == 'completed', 'text-yellow-700' => $payment->status == 'pending', 'text-red-700' => $payment->status == 'failed', ])>
                                                            <p>
                                                            @if($payment->status == 'completed') This payment has been successfully processed and recorded.
                                                                @elseif($payment->status == 'pending') This payment is awaiting processing or confirmation.
                                                                @else
                                                                This payment attempt has failed or been declined.
                                                            @endif
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>
                                        <!-- Payment Information -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" >

                                                <div>

                                                    <h3 class="text-lg font-medium text-primary-text mb-4" >Payment Information</h3>
                                                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2" >
                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Amount </dt><dd class="mt-1 text-lg font-semibold text-primary-text" > ₱{{ number_format($payment->amount, 2) }} </dd>
                                                        </div>

                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Payment Method </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->payment_method_name }} </dd>
                                                        </div>

                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Payment Date </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->payment_date->format('M d, Y h:i A') }} </dd>
                                                        </div>

                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Transaction ID </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->transaction_id ?: 'N/A' }} </dd>
                                                        </div>

                                                        <div class="sm:col-span-2" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Description </dt><dd class="mt-1 text-sm text-primary-text whitespace-pre-wrap" > {{ $payment->description ?: 'No description provided.' }} </dd>
                                                        </div>
                                                    </dl>
                                                </div>

                                                <div>

                                                    <h3 class="text-lg font-medium text-primary-text mb-4" >Booking Information</h3>

                                                    @if($payment->booking) <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2" >
                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Booking Reference </dt><dd class="mt-1 text-sm text-primary-text" ><a href="{{ route('bookings.show', $payment->booking->booking_id) }}" class="text-accent hover:text-accent-hover" > {{ $payment->booking->booking_reference }} </a></dd>
                                                        </div>
                                                        <div class="sm:col-span-1" >
                                                            <dt class="text-sm font-medium text-secondary-text" > Client Name </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->booking->client_first_name }} {{ $payment->booking->client_last_name }} </dd>
                                                        </div>
                                                        <!-- Client information has been removed -->
                                                            <div class="sm:col-span-1" >
                                                                <dt class="text-sm font-medium text-secondary-text" > Package </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->booking->package->title ?? 'N/A' }} </dd>
                                                            </div>

                                                            <div class="sm:col-span-1" >
                                                                <dt class="text-sm font-medium text-secondary-text" > Booking Total </dt><dd class="mt-1 text-sm text-primary-text" > ₱{{ number_format($payment->booking->total_amount, 2) }} </dd>
                                                            </div>

                                                            <div class="sm:col-span-1" >
                                                                <dt class="text-sm font-medium text-secondary-text" > Booking Date </dt><dd class="mt-1 text-sm text-primary-text" > {{ $payment->booking->start_time ? $payment->booking->start_time->format('M d, Y h:i A') : 'N/A' }} </dd>
                                                            </div>

                                                            <div class="sm:col-span-1" >
                                                                <dt class="text-sm font-medium text-secondary-text" > Booking Status </dt><dd class="mt-1 text-sm" ><span @class([ 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full', 'bg-green-100 text-green-800' => in_array($payment->booking->status, ['confirmed', 'completed']), 'bg-yellow-100 text-yellow-800' => in_array($payment->booking->status, ['pending', 'tentative']), 'bg-red-100 text-red-800' => in_array($payment->booking->status, ['cancelled', 'no-show']), 'bg-blue-100 text-blue-800' => $payment->booking->status == 'in-progress', ])> {{ ucfirst($payment->booking->status) }} </span></dd>
                                                            </div>
                                                        </dl><!-- Booking Payment Progress -->
                                                            <div class="mt-6" >

                                                                <h4 class="text-sm font-medium text-secondary-text mb-2" >Payment Progress</h4>

                                                                <div>
                                                                    @php $totalPaid = $payment->booking->payments->where('status', 'completed')->sum('amount'); $percentPaid = $payment->booking->total_amount > 0 ? min(100, ($totalPaid / $payment->booking->total_amount) * 100) : 0; @endphp
                                                                    <div class="flex items-center justify-between mb-1" >

                                                                        <div>
                                                                            <span class="text-xs font-medium text-secondary-text" >₱{{ number_format($totalPaid, 2) }} of ₱{{ number_format($payment->booking->total_amount, 2) }}</span>
                                                                        </div>

                                                                        <div>
                                                                            <span class="text-xs font-medium text-secondary-text" >{{ number_format($percentPaid, 1) }}%</span>
                                                                        </div>

                                                                    </div>

                                                                    <div class="w-full bg-gray-200 rounded-full h-2" >

                                                                        <div class="bg-accent h-2 rounded-full" style="width: {{ $percentPaid }}%">

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                            @else

                                                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4" >

                                                                <div class="flex" >

                                                                    <div class="flex-shrink-0" >
                                                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                                        </div>

                                                                        <div class="ml-3" >

                                                                            <h3 class="text-sm font-medium text-yellow-800" > No Booking Associated </h3>

                                                                            <div class="mt-2 text-sm text-yellow-700" >

                                                                                <p> This payment is not associated with any booking. It may be a standalone payment or the booking might have been deleted. </p>

                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </div>

                                                            @endif

                                                        </div>

                                                    </div>
                                                    <!-- Other Payments for Same Booking -->
                                                        @if($payment->booking && $payment->booking->payments->count() > 1)
                                                            <div class="mt-8" >

                                                                <h3 class="text-lg font-medium text-primary-text mb-4" >Other Payments for this Booking</h3>

                                                                <div class="overflow-x-auto" >
                                                                    <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Date </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Method </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Amount </th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" > Status </th><th scope="col" class="relative px-6 py-3" ><span class="sr-only" >View</span></th></tr></thead><tbody class="bg-card-bg divide-y divide-border-color" >
                                                                        @foreach($payment->booking->payments->where('transaction_id', '!=', $payment->transaction_id) as $otherPayment) <tr><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text" > {{ $otherPayment->payment_date->format('M d, Y h:i A') }} </td><td class="px-6 py-4 whitespace-nowrap text-sm text-primary-text" > {{ $otherPayment->payment_method_name }} </td><td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-text" > ₱{{ number_format($otherPayment->amount, 2) }} </td><td class="px-6 py-4 whitespace-nowrap" ><span @class([ 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full', 'bg-green-100 text-green-800' => $otherPayment->status == 'completed', 'bg-yellow-100 text-yellow-800' => $otherPayment->status == 'pending', 'bg-red-100 text-red-800' => $otherPayment->status == 'failed' ])> {{ ucfirst($otherPayment->status) }} </span></td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" ><a href="{{ route('finance.payments.show', $otherPayment->transaction_id) }}" class="text-accent hover:text-accent-hover" > View </a></td></tr>
                                                                        @endforeach
                                                                    </tbody></table>
                                                                </div>

                                                            </div>

                                                        @endif
                                                        <!-- Admin Notes -->
                                                            <div class="mt-8 pt-8 border-t border-border-color" >

                                                                <h3 class="text-lg font-medium text-primary-text mb-4" >Admin Notes</h3>

                                                                <div class="bg-gray-50 rounded-md p-4" >

                                                                    <p class="text-sm text-secondary-text" ><span class="font-medium" >Created:</span> {{ $payment->created_at->format('M d, Y h:i A') }}
                                                                    @if($payment->created_by) by {{ $payment->created_by->name ?? 'Unknown' }}
                                                                    @endif
                                                                </p>

                                                                @if($payment->updated_at && $payment->updated_at->ne($payment->created_at))
                                                                    <p class="text-sm text-secondary-text mt-1" ><span class="font-medium" >Last Updated:</span> {{ $payment->updated_at->format('M d, Y h:i A') }}
                                                                    @if($payment->updated_by) by {{ $payment->updated_by->name ?? 'Unknown' }}
                                                                    @endif
                                                                </p>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="px-4 py-4 bg-gray-50 sm:px-6 border-t border-border-color" >

                                                    <div class="flex justify-end" >

                                                            <div class="flex space-x-3" >

                                                                <button onclick="confirmDelete('{{ $payment->transaction_id }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" type="button"><svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg> Delete Payment </button>

                                                                    <form id="delete-form-{{ $payment->transaction_id }}" action="{{ route('finance.payments.destroy', $payment->transaction_id) }}" method="POST" class="hidden" > @csrf @method('DELETE')
                                                                    </form>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                            <!-- Delete Confirmation Modal -->
                                                <div id="deleteModal" class="fixed z-10 inset-0 overflow-y-auto hidden" >

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

                                                                            <h3 class="text-lg leading-6 font-medium text-primary-text" id="modal-title"> Delete Payment </h3>

                                                                            <div class="mt-2" >

                                                                                <p class="text-sm text-secondary-text" > Are you sure you want to delete this payment record? This action cannot be undone and may impact associated booking records. </p>

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