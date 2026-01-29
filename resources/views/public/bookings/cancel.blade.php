
@extends('layouts.public')
@section('title', 'Cancel Your Booking')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-2xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <h1 class="text-3xl font-bold text-gray-900" >Cancel Your Booking</h1>

                    <p class="text-gray-600 mt-2" >Please confirm that you want to cancel this booking.</p>

                </div>
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">

                        <p class="font-bold" >Please check the following errors:</p>
                        <ul class="list-disc ml-8" > @foreach ($errors->all() as $error) <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif
        <!-- Booking Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6" >

                <h3 class="text-lg font-medium text-gray-900 mb-4" >Booking Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >

                    <div>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Reference:</span> {{ $booking->booking_reference }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Client:</span> {{ $booking->client_first_name }} {{ $booking->client_last_name }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Package:</span> {{ $booking->package->title ?? 'No package' }} </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Date:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Time:</span> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Status:</span> {{ ucfirst($booking->status) }} </p>

                    </div>

                </div>

            </div>

            <div class="bg-red-50 p-4 rounded-md mb-6" >

                <div class="flex items-start" >

                    <div class="flex-shrink-0" >
                        <svg class="h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                        </div>

                        <div class="ml-3" >

                            <h3 class="text-sm font-medium text-red-800" >Cancellation Policy</h3>

                            <div class="mt-2 text-sm text-red-700" >
                                <ul class="list-disc pl-5 space-y-1" ><li>Cancellations made more than 48 hours before the scheduled session may be eligible for a full refund.</li><li>Cancellations made within 48 hours of the scheduled session may be subject to a cancellation fee.</li><li>No-shows will be charged the full booking amount.</li></ul>
                            </div>

                        </div>

                    </div>

                </div>

                <form action="{{ route('public.bookings.cancel.submit', ['reference' =>
                    $booking->booking_reference]) }}" method="POST" class="space-y-6" > @csrf
                    <div>
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700" > Reason for Cancellation (Optional) </label>
                        <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary text-black" placeholder="Please let us know why you're cancelling this booking..." >{{ old('cancellation_reason') }}</textarea>

                        <p class="mt-1 text-sm text-gray-500" > Your feedback helps us improve our services. </p>

                    </div>

                    <div class="border-t border-gray-200 pt-6 mt-6 flex justify-between" >
                        <a href="{{ route('public.bookings.manage', ['reference' => $booking->booking_reference]) }}" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" > Go Back </a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.');" > Confirm Cancellation </button>

                    </div>

                </form>

            </div>

        </div>
    @endsection