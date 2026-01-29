
@extends('layouts.public')
@section('title', 'Verify Your Identity')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <h1 class="text-3xl font-bold text-gray-900" >Verify Your Identity</h1>

                    <p class="text-gray-600 mt-2" >Enter the verification code sent to your email or phone.</p>

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

        <form action="{{ route('public.bookings.verify-otp.submit', ['reference' =>
            $reference]) }}" method="POST" class="space-y-6" > @csrf
            <div>
                <label for="otp_code" class="block text-sm font-medium text-gray-700" > Verification Code (OTP) </label>
                <div class="mt-1 flex justify-center" >

                    <input type="text" id="otp_code" name="otp_code" value="{{ old('otp_code') }}" maxlength="6" placeholder="123456" required autocomplete="off" class="block w-48 text-center text-2xl tracking-widest font-mono border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary text-black" >

                    </div>

                    <p class="mt-2 text-sm text-center text-gray-500" > Enter the 6-digit code we sent to your email or phone. The code is valid for 10 minutes. </p>

                </div>

                <div class="flex justify-center" >

                    <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:shadow-outline transition duration-150" > Verify Code </button>

                </div>

            </form>

            <div class="mt-8 text-center" >

                <p class="text-sm text-gray-600" > Didn't receive the code? <a href="{{ route('public.bookings.lookup') }}" class="text-accent hover:underline" >Try again</a></p>

            </div>

            <div class="mt-4 text-center border-t pt-6" >

                <p class="text-sm text-gray-600" > Need help? Contact our support team at <a href="mailto:support@shinestudio.com" class="text-accent hover:underline" >support@shinestudio.com</a></p>

            </div>

        </div>

    </div>
@endsection