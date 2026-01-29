
@extends('layouts.public')
@section('title', 'Manage Your Booking')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-3xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <div class="mb-3" >
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @else
                            if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @else
                            if($booking->status == 'completed') bg-blue-100 text-blue-800
                            @else
                            if($booking->status == 'canceled') bg-red-100 text-red-800
                            @else
                            if($booking->status == 'rescheduled') bg-purple-100 text-purple-800
                        @endif
                        "> {{ ucfirst($booking->status) }} </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900" >Your Booking Details</h1>

                    <p class="text-gray-600 mt-2 text-lg font-semibold" >{{ $booking->booking_reference }}</p>

                </div>
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">

                        <p class="font-bold" >Please check the following errors:</p>
                        <ul class="list-disc ml-8" > @foreach ($errors->all() as $error) <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">

                <p>{{ session('success') }}</p>

            </div>

        @endif
        <!-- Booking Details Card -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6" >

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" >

                    <div>

                        <h3 class="text-lg font-medium text-gray-900" >Client Information</h3>

                        <div class="mt-2 space-y-2" >

                            <p class="text-sm text-gray-600" ><span class="font-semibold" >Name:</span> {{ $booking->client_first_name }} {{ $booking->client_last_name }} </p>

                            <p class="text-sm text-gray-600" ><span class="font-semibold" >Email:</span> {{ $booking->client_email }} </p>

                            @if($booking->client_phone)
                                <p class="text-sm text-gray-600" ><span class="font-semibold" >Phone:</span> {{ $booking->client_phone }} </p>

                            @endif

                        </div>

                    </div>

                    <div>

                        <h3 class="text-lg font-medium text-gray-900" >Session Details</h3>

                        <div class="mt-2 space-y-2" >

                            <p class="text-sm text-gray-600" ><span class="font-semibold" >Date:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }} </p>

                            <p class="text-sm text-gray-600" ><span class="font-semibold" >Time:</span> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }} </p>

                            @if($booking->package)
                                <p class="text-sm text-gray-600" ><span class="font-semibold" >Package:</span> {{ $booking->package->title }} </p>

                            @endif

                            @if($booking->backdrop)
                                <p class="text-sm text-gray-600" ><span class="font-semibold" >Backdrop:</span> {{ $booking->backdrop->name }} </p>

                                @else
                                if($booking->custom_backdrop)
                                <p class="text-sm text-gray-600" ><span class="font-semibold" >Custom Backdrop:</span> {{ $booking->custom_backdrop }} </p>

                            @endif

                        </div>

                    </div>

                </div>

                @if($booking->addons && count($booking->addons) > 0)
                    <div class="mt-6" >

                        <h3 class="text-lg font-medium text-gray-900" >Add-ons</h3>

                        <div class="mt-2" >
                            <ul class="list-disc list-inside space-y-1" >
                                @foreach($booking->addons as $addon) <li class="text-sm text-gray-600" > {{ $addon->name }}
                                    @if($addon->pivot->quantity > 1) ({{ $addon->pivot->quantity }} x ${{ number_format($addon->pivot->price, 2) }})
                                        @else
                                        (${{ number_format($addon->pivot->price, 2) }})
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            @endif

            <div class="mt-6" >

                <h3 class="text-lg font-medium text-gray-900" >Payment Information</h3>

                <div class="mt-2 space-y-2" >

                    <p class="text-sm text-gray-600" ><span class="font-semibold" >Total Amount:</span> ${{ number_format($booking->total_amount, 2) }} </p>

                </div>

            </div>

        </div>
        <!-- Management Options -->
            @if($booking->status != 'canceled' && $booking->status != 'completed')
                <div class="border-t border-gray-200 pt-6 mt-6" >

                    <h3 class="text-lg font-medium text-gray-900 text-center mb-4" >Booking Management</h3>

                    <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4" >

                        @if(!$isPastBooking) <a href="{{ route('public.bookings.reschedule', ['reference' => $booking->booking_reference]) }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Reschedule Booking </a><a href="{{ route('public.bookings.cancel', ['reference' => $booking->booking_reference]) }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Cancel Booking </a>
                                @else

                                <div class="text-center text-gray-600 p-4 border border-gray-200 rounded-md" >

                                    <p>This booking is in the past and cannot be modified.</p>

                                </div>

                            @endif

                        </div>

                    </div>

                @endif

                <div class="mt-8 text-center border-t pt-6" >
                    <a href="{{ route('public.bookings.lookup') }}" class="text-accent hover:text-accent-dark hover:underline" > Back to Booking Lookup </a>
                </div>

            </div>

        </div>
    @endsection