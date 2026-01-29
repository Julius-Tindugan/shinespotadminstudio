
@extends('layouts.public')
@section('title', 'Reschedule Your Booking')
    @section('content')
        <div class="container mx-auto px-4 py-8 max-w-3xl" >

            <div class="bg-white rounded-lg shadow-lg p-8" >

                <div class="text-center mb-8" >

                    <h1 class="text-3xl font-bold text-gray-900" >Reschedule Your Booking</h1>

                    <p class="text-gray-600 mt-2" >Choose a new date and time for your session.</p>

                </div>
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">

                        <p class="font-bold" >Please check the following errors:</p>
                        <ul class="list-disc ml-8" > @foreach ($errors->all() as $error) <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

        @endif
        <!-- Current Booking Info -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8" >

                <h3 class="text-lg font-medium text-gray-900 mb-4" >Current Booking Details</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >

                    <div>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Reference:</span> {{ $booking->booking_reference }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Client:</span> {{ $booking->client_first_name }} {{ $booking->client_last_name }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Package:</span> {{ $booking->package->title ?? 'No package' }} </p>

                    </div>

                    <div>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Current Date:</span> {{ \Carbon\Carbon::parse($booking->booking_date)->format('F j, Y') }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Current Time:</span> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }} </p>

                        <p class="text-sm text-gray-600" ><span class="font-semibold" >Status:</span> {{ ucfirst($booking->status) }} </p>

                    </div>

                </div>

            </div>

            <form action="{{ route('public.bookings.reschedule.submit', ['reference' =>
                $booking->booking_reference]) }}" method="POST" class="space-y-6" > @csrf
                <div>
                    <label for="booking_date" class="block text-lg font-medium text-gray-700 mb-2" > New Date </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4" >

                        @foreach($availableDates as $index => $dateInfo)
                            @if($index < 9) <!-- Limit to first 9 dates -->
                                <div>

                                    <input type="radio" id="date_{{ $index }}" name="booking_date" value="{{ $dateInfo['date'] }}" class="hidden peer" required >
                                        <label for="date_{{ $index }}" class="flex flex-col items-center p-4 border rounded-lg cursor-pointer peer-checked:border-accent peer-checked:bg-accent-light text-center hover:bg-gray-50" ><span class="block font-medium text-gray-900" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('D') }} </span><span class="block text-2xl font-bold text-gray-900" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('j') }} </span><span class="block text-gray-600" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('M Y') }} </span></label>
                                    </div>

                                @endif

                            @endforeach

                        </div>

                        <div id="moreAvailableDates" class="grid grid-cols-1 md:grid-cols-3 gap-4" style="display: none;">

                            @foreach($availableDates as $index => $dateInfo)
                                @if($index >= 9) <!-- Show rest of dates when expanded -->
                                    <div>

                                        <input type="radio" id="date_{{ $index }}" name="booking_date" value="{{ $dateInfo['date'] }}" class="hidden peer" required >
                                            <label for="date_{{ $index }}" class="flex flex-col items-center p-4 border rounded-lg cursor-pointer peer-checked:border-accent peer-checked:bg-accent-light text-center hover:bg-gray-50" ><span class="block font-medium text-gray-900" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('D') }} </span><span class="block text-2xl font-bold text-gray-900" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('j') }} </span><span class="block text-gray-600" > {{ \Carbon\Carbon::parse($dateInfo['date'])->format('M Y') }} </span></label>
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                            @if(count($availableDates) > 9)
                                <div class="text-center mt-2" >

                                    <button type="button" id="showMoreDatesBtn" class="text-accent hover:text-accent-dark hover:underline text-sm" > Show more dates </button>

                                </div>

                            @endif

                        </div>

                        <div>
                            <label for="start_time" class="block text-lg font-medium text-gray-700 mb-2" > New Time </label>
                            <select id="start_time" name="start_time" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary text-black" >
                                <option value="">Select a time slot</option>
                                @foreach(['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'] as $time) <option value="{{ $time }}">{{ \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A') }}</option>
                                @endforeach

                            </select>

                            <p class="mt-1 text-sm text-gray-500" > Time slots are subject to availability. Duration will be the same as your current booking. </p>

                        </div>

                        <div class="border-t border-gray-200 pt-6 mt-6 flex justify-between" >
                            <a href="{{ route('public.bookings.manage', ['reference' => $booking->booking_reference]) }}" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" > Cancel </a>
                            <button type="submit" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent" > Confirm Reschedule </button>

                        </div>

                    </form>

                </div>

            </div>

            @push('scripts') <script> document.addEventListener('DOMContentLoaded', function() { const showMoreBtn = document.getElementById('showMoreDatesBtn'); const moreDates = document.getElementById('moreAvailableDates'); if (showMoreBtn) { showMoreBtn.addEventListener('click', function() { if (moreDates.style.display === 'none') { moreDates.style.display = 'grid'; showMoreBtn.textContent = 'Show fewer dates'; } else { moreDates.style.display = 'none'; showMoreBtn.textContent = 'Show more dates'; } }); } }); </script>
            @endpush
        @endsection