
@extends('layouts.app')
@section('title', 'Backdrop Details')
    @section('content')
        <div class="container px-6 py-8 mx-auto" >

            <div class="flex justify-between items-center mb-6" >

                <h1 class="text-2xl font-semibold text-primary-text" > Backdrop Details </h1>

                <div class="flex space-x-2" >
                    <a href="{{ route('backdrops.edit', $backdrop->backdrop_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg> Edit </a><a href="{{ route('backdrops.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg> Back to List </a>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" >
                        <!-- Backdrop Details Card -->
                            <div class="lg:col-span-2 bg-white shadow-md rounded-lg overflow-hidden" >

                                <div class="p-6" >

                                    <div class="flex items-center mb-6" >

                                        <div class="flex-shrink-0 mr-4" >

                                            <div class="h-20 w-20 rounded-md" style="background-color: {{ $backdrop->
                                                color_code ?? '#FFFFFF' }};">
                                            </div>

                                        </div>

                                        <div>

                                            <h2 class="text-xl font-bold text-primary-text" >{{ $backdrop->name }}</h2>

                                            <p class="text-secondary-text" > {{ $backdrop->color_code ?? 'No color code specified' }} </p>

                                        </div>

                                    </div>
                                    <!-- Details -->
                                        <div class="mt-6 border-t border-border-color pt-6" >

                                            <h3 class="font-medium text-primary-text mb-4" >Backdrop Details</h3>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4" >

                                                <div>

                                                    <p class="text-sm text-secondary-text" >Status</p>
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $backdrop->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" > {{ $backdrop->is_active ? 'Active' : 'Inactive' }} </span>
                                                </div>

                                                <div>

                                                    <p class="text-sm text-secondary-text" >Created On</p>

                                                    <p class="text-primary-text" > {{ $backdrop->created_at->format('M d, Y') }} </p>

                                                </div>

                                                <div>

                                                    <p class="text-sm text-secondary-text" >Last Updated</p>

                                                    <p class="text-primary-text" > {{ $backdrop->updated_at->format('M d, Y') }} </p>

                                                </div>

                                                <div>

                                                    <p class="text-sm text-secondary-text" >Bookings Count</p>

                                                    <p class="text-primary-text" > {{ $backdrop->bookings->count() }} </p>

                                                </div>

                                            </div>

                                            <div class="mt-6" >

                                                <p class="text-sm text-secondary-text" >Description</p>

                                                <p class="text-primary-text whitespace-pre-line" >{{ $backdrop->description ?: 'No description available.' }}</p>

                                            </div>

                                        </div>

                                    </div>

                                </div>
                                <!-- Related Bookings Card -->
                                    <div class="bg-white shadow-md rounded-lg overflow-hidden" >

                                        <div class="px-6 py-4 bg-gray-50 border-b border-border-color" >

                                            <h3 class="font-medium text-primary-text" > Related Bookings ({{ $bookings->total() }}) </h3>

                                        </div>

                                        <div class="p-6" >

                                            @if($bookings->count() > 0)
                                                <div class="space-y-4" >

                                                    @foreach($bookings as $booking)
                                                        <div class="border-b border-border-color pb-4 last:border-b-0 last:pb-0" >

                                                            <div class="flex justify-between items-start" >

                                                                <div>

                                                                    <h4 class="font-medium text-primary-text" > Anonymous </h4>

                                                                    <p class="text-sm text-secondary-text" > {{ $booking->booking_date->format('M d, Y') }} </p>

                                                                    <p class="text-xs text-secondary-text" > {{ date('h:i A', strtotime($booking->start_time)) }} - {{ date('h:i A', strtotime($booking->end_time)) }} </p>

                                                                </div>
                                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-800' : '' }} {{ $booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $booking->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }} {{ $booking->status == 'canceled' ? 'bg-red-100 text-red-800' : '' }}" > {{ ucfirst($booking->status) }} </span>
                                                            </div>

                                                            <div class="mt-2" >
                                                                <a href="{{ route('bookings.show', $booking->booking_id) }}" class="text-sm text-accent hover:underline" > View Booking Details </a>
                                                            </div>

                                                        </div>

                                                    @endforeach

                                                </div>
                                                <!-- Pagination -->
                                                    <div class="mt-4" >
                                                        {{ $bookings->links() }}
                                                    </div>

                                                    @else

                                                    <div class="text-center py-4" >

                                                        <p class="text-secondary-text" > No bookings are using this backdrop. </p>

                                                    </div>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </div>
                            @endsection