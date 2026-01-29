
@extends('layouts.app')
@section('title', 'Equipment Details')
    @section('content')
        <div class="container px-6 py-8 mx-auto" >

            <div class="flex justify-between items-center mb-6" >

                <h1 class="text-2xl font-semibold text-primary-text" > Equipment Details </h1>

                <div class="flex space-x-2" >
                    <a href="{{ route('equipment.edit', $equipment->equipment_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg> Edit </a><a href="{{ route('equipment.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg> Back to List </a>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" >
                        <!-- Equipment Details Card -->
                            <div class="lg:col-span-2 bg-white shadow-md rounded-lg overflow-hidden" >

                                <div class="p-6" >

                                    <div class="flex items-center mb-6" >

                                        @if($equipment->image)
                                            <div class="h-24 w-24 flex-shrink-0 mr-4 rounded-lg overflow-hidden" >
                                                <img class="h-24 w-24 object-cover" src="{{ asset('storage/' . $equipment->image) }}" alt="{{ $equipment->name }}">
                                            </div>

                                            @else

                                            <div class="h-24 w-24 flex-shrink-0 mr-4 bg-gray-100 rounded-lg flex items-center justify-center" >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                </div>

                                            @endif

                                            <div>

                                                <h2 class="text-xl font-bold text-primary-text" >{{ $equipment->name }}</h2>

                                                <p class="text-secondary-text" >{{ $equipment->type }}</p>

                                            </div>

                                        </div>
                                        <!-- Details -->
                                            <div class="mt-6 border-t border-border-color pt-6" >

                                                <h3 class="font-medium text-primary-text mb-4" >Equipment Details</h3>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4" >

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Quantity</p>

                                                        <p class="text-primary-text" >{{ $equipment->quantity }}</p>

                                                    </div>

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Condition</p>
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $equipment->condition == 'Excellent' ? 'bg-green-100 text-green-800' : '' }} {{ $equipment->condition == 'Good' ? 'bg-blue-100 text-blue-800' : '' }} {{ $equipment->condition == 'Fair' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $equipment->condition == 'Poor' ? 'bg-orange-100 text-orange-800' : '' }} {{ $equipment->condition == 'Needs Repair' ? 'bg-red-100 text-red-800' : '' }}" > {{ $equipment->condition }} </span>
                                                    </div>

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Cost</p>

                                                        <p class="text-primary-text" > {{ $equipment->cost ? '₱' . number_format($equipment->cost, 2) : 'N/A' }} </p>

                                                    </div>

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Purchase Date</p>

                                                        <p class="text-primary-text" > {{ $equipment->purchase_date ? $equipment->purchase_date->format('M d, Y') : 'N/A' }} </p>

                                                    </div>

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Availability</p>
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $equipment->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" > {{ $equipment->is_available ? 'Available' : 'Not Available' }} </span>
                                                    </div>

                                                    <div>

                                                        <p class="text-sm text-secondary-text" >Status</p>
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $equipment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}" > {{ $equipment->is_active ? 'Active' : 'Inactive' }} </span>
                                                    </div>

                                                </div>

                                                <div class="mt-6" >

                                                    <p class="text-sm text-secondary-text" >Description</p>

                                                    <p class="text-primary-text whitespace-pre-line" >{{ $equipment->description ?: 'No description available.' }}</p>

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

                                                            <p class="text-secondary-text" > No bookings are using this equipment. </p>

                                                        </div>

                                                    @endif

                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                @endsection