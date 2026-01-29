
@extends('layouts.public')
@section('title', 'Manage Your Booking')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-2xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <h1 class="text-3xl font-bold text-gray-900" >Manage Your Booking</h1>

                    <p class="text-gray-600 mt-2" >Enter your booking details below to manage, reschedule, or cancel your appointment.</p>

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

        <form action="{{ route('public.bookings.lookup.submit') }}" method="POST" class="space-y-6" >
            @csrf
            <div>
                <label for="booking_reference" class="block text-sm font-medium text-gray-700" > Booking Reference Code </label>
                <input type="text" id="booking_reference" name="booking_reference" value="{{ old('booking_reference') }}" placeholder="BK-1234-ABC" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary text-black" >

                    <p class="mt-1 text-sm text-gray-500" > Enter the booking reference code you received in your confirmation email/SMS. </p>

                </div>

                <div>
                    <label for="contact_info" class="block text-sm font-medium text-gray-700" > Email or Phone Number </label>
                    <input type="text" id="contact_info" name="contact_info" value="{{ old('contact_info') }}" placeholder="Email or phone used during booking" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary text-black" >

                        <p class="mt-1 text-sm text-gray-500" > Enter the email address or phone number you provided when making the booking. </p>

                    </div>

                    <div class="flex justify-center" >

                        <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition duration-150" > Find My Booking </button>

                    </div>

                </form>

                <div class="mt-8 text-center border-t pt-6" >

                    <p class="text-sm text-gray-600" > Need help? Contact our support team at <a href="mailto:support@shinestudio.com" class="text-accent hover:underline" >support@shinestudio.com</a></p>

                </div>

            </div>

        </div>
    @endsection