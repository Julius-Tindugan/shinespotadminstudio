
@extends('layouts.public')
@section('title', 'Booking Canceled')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-2xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <div class="mb-4" >
                        <svg class="h-16 w-16 text-red-500 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 mb-3" > Canceled </span>
                        <h1 class="text-3xl font-bold text-gray-900" >Booking Canceled</h1>

                        <p class="text-gray-600 mt-2" >Reference: {{ $booking->booking_reference }}</p>

                        <p class="text-gray-600 mt-2" > Canceled on {{ \Carbon\Carbon::parse($booking->canceled_at)->format('F j, Y \a\t g:i A') }} </p>

                    </div>
                    <!-- Booking Details -->
                        <div class="bg-gray-50 rounded-lg p-6 mb-6" >

                            <h3 class="text-lg font-medium text-gray-900 mb-4" >Booking Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >

                                <div>

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Client:</span> {{ $booking->client_first_name }} {{ $booking->client_last_name }} </p>

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Email:</span> {{ $booking->client_email }} </p>

                                    @if($booking->client_phone)
                                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Phone:</span> {{ $booking->client_phone }} </p>

                                    @endif

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Package:</span> {{ $booking->package->title ?? 'No package' }} </p>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Scheduled Date:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }} </p>

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Scheduled Time:</span> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }} </p>

                                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Total Amount:</span> ${{ number_format($booking->total_amount, 2) }} </p>

                                </div>

                            </div>

                        </div>

                        <div class="bg-blue-50 p-4 rounded-md mb-8" >

                            <div class="flex" >

                                <div class="flex-shrink-0" >
                                    <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    </div>

                                    <div class="ml-3 flex-1 md:flex md:justify-between" >

                                        <p class="text-sm text-blue-700" > If you would like to book another session, please visit our website or contact us directly. </p>

                                    </div>

                                </div>

                            </div>

                            <div class="text-center" >
                                <a href="#" onclick="window.close(); return false;" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Close Window </a>
                            </div>

                            <div class="mt-8 text-center border-t pt-6" >

                                <p class="text-sm text-gray-600" > Need help? Contact our support team at <a href="mailto:support@shinestudio.com" class="text-accent hover:underline" >support@shinestudio.com</a></p>

                            </div>

                        </div>

                    </div>
                @endsection