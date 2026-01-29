{{-- resources/views/bookings/partials/booking-list.blade.php --}}
@forelse($bookings as $booking)
<tr class="table-row-hover transition-all duration-200 ease-in-out hover:bg-accent/5 border-b border-border-color/50 group" 
    data-booking-id="{{ $booking->booking_id }}" 
    data-status="{{ $booking->status }}">
    
    <!-- ID/Reference Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-accent/20 to-accent/10 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-primary-text tracking-tight">
                    #{{ $booking->booking_id }}
                </div>
                <div class="text-xs text-secondary-text {{ $booking->booking_reference ? 'cursor-pointer hover:text-accent copy-reference transition-colors' : '' }}" 
                     @if($booking->booking_reference) data-reference="{{ $booking->booking_reference }}" @endif>
                    @if($booking->booking_reference)
                        <span class="flex items-center">
                            {{ $booking->booking_reference }}
                            <svg class="w-3 h-3 ml-1 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                            </svg>
                        </span>
                    @else
                        <span class="italic opacity-60">No Ref</span>
                    @endif
                </div>
            </div>
        </div>
    </td>

    <!-- Client Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-accent/15 to-accent/5 rounded-full flex items-center justify-center mr-3 shadow-sm ring-2 ring-accent/10">
                <span class="text-sm font-bold text-accent">
                    {{ strtoupper(substr($booking->client_first_name, 0, 1)) }}{{ strtoupper(substr($booking->client_last_name, 0, 1)) }}
                </span>
            </div>
            <div>
                <div class="text-sm font-semibold text-primary-text">
                    {{ $booking->client_first_name }} {{ $booking->client_last_name }}
                </div>
                <div class="text-xs text-secondary-text">
                    {{ $booking->client_phone ?: $booking->client_email ?: 'N/A' }}
                </div>
            </div>
        </div>
    </td>

    <!-- Date & Time Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-accent/15 to-accent/5 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <div class="text-sm font-semibold text-primary-text">
                    {{ $booking->booking_date->format('M d, Y') }}
                </div>
                <div class="text-xs text-secondary-text flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                </div>
            </div>
        </div>
    </td>    <!-- Package Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @if($booking->package_id)
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-accent/15 to-accent/5 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="text-sm font-medium text-primary-text">
                    {{ $booking->package->title ?? 'Package #'.$booking->package_id }}
                </div>
            </div>
        @endif
        @if($booking->booking_reference && !$booking->package_id)
            <div class="text-xs text-secondary-text italic px-2 py-1 bg-gray-50 rounded-md inline-block">
                {{ $booking->booking_reference }}
            </div>
        @elseif(!$booking->package_id && !$booking->booking_reference)
            <div class="text-xs text-secondary-text italic opacity-60 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                No package
            </div>
        @endif
    </td>

    <!-- Staff Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @if($booking->primaryStaff)
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-accent/15 to-accent/5 rounded-full flex items-center justify-center ring-2 ring-accent/10">
                    <span class="text-xs font-bold text-accent">
                        {{ strtoupper(substr($booking->primaryStaff->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($booking->primaryStaff->last_name ?? '', 0, 1)) }}
                    </span>
                </div>
                <div class="text-sm font-medium text-primary-text">
                    {{ $booking->primaryStaff->first_name ?? '' }} {{ $booking->primaryStaff->last_name ?? '' }}
                </div>
            </div>
        @else
            <div class="text-xs text-secondary-text italic opacity-60 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Not assigned
            </div>
        @endif
    </td>

    <!-- Backdrop Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm text-primary-text">
            @if($booking->backdrop_id && $booking->backdrop) 
                <div class="flex items-center gap-2">
                    @if($booking->backdrop->color_code) 
                        <span class="inline-block w-7 h-7 rounded-lg border-2 border-white shadow-md ring-1 ring-gray-200" style="background-color: {{ $booking->backdrop->color_code }};"></span>
                    @endif
                    <span class="font-medium">{{ $booking->backdrop->name }}</span>
                </div>
            @elseif($booking->backdrop_selections) 
                @php
                    // Use the formatted accessor to get properly enriched backdrop data
                    $selections = $booking->formatted_backdrop_selections;
                @endphp
                @if(!empty($selections))
                    <div class="flex items-center gap-1.5">
                        @foreach($selections as $index => $selection)
                            @if(isset($selection['color']) && $index < 3)
                                <span class="inline-block w-7 h-7 rounded-lg border-2 border-white shadow-md ring-1 ring-gray-200 hover:scale-110 transition-transform cursor-help" 
                                      style="background-color: {{ $selection['color'] }};" 
                                      title="{{ $selection['name'] ?? $selection['color'] }}"></span>
                            @endif
                        @endforeach
                        @if(count($selections) > 3)
                            <span class="text-xs text-secondary-text font-medium bg-gray-100 px-2 py-1 rounded-full">+{{ count($selections) - 3 }}</span>
                        @elseif(count($selections) > 1 && count($selections) <= 3)
                            <span class="text-xs text-secondary-text font-medium">({{ count($selections) }})</span>
                        @endif
                    </div>
                @else
                    <span class="text-secondary-text italic opacity-60">Custom Selection</span>
                @endif
            @else
                <span class="text-secondary-text italic opacity-60 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    None
                </span>
            @endif
        </div>
    </td>

    <!-- Addons Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @php
            $bookingAddons = null;
            $addonCount = 0;
            $addonNames = [];
            
            // Check if bookingAddons relationship is loaded
            if ($booking->relationLoaded('bookingAddons')) {
                $bookingAddons = $booking->bookingAddons;
                $addonCount = $bookingAddons->count();
                $addonNames = $bookingAddons->pluck('addon_name')->toArray();
            }
            // Fallback: check via accessor which loads the relationship
            elseif ($booking->addons instanceof \Illuminate\Database\Eloquent\Collection) {
                $bookingAddons = $booking->addons;
                $addonCount = $bookingAddons->count();
                $addonNames = $bookingAddons->pluck('addon_name')->toArray();
            }
            // Legacy: check JSON column for old data
            elseif (is_array($booking->getRawOriginal('addons')) && !empty($booking->getRawOriginal('addons'))) {
                $jsonAddons = $booking->getRawOriginal('addons');
                $addonCount = count($jsonAddons);
                $addonNames = array_column($jsonAddons, 'name');
            }
        @endphp
        
        @if($addonCount > 0)
            <div class="flex items-center gap-2">
                <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-accent/15 to-accent/5 rounded-lg flex items-center justify-center">
                    <span class="text-xs font-bold text-accent">{{ $addonCount }}</span>
                </div>
                <div class="flex flex-col">
                    <div class="text-sm text-primary-text font-semibold">
                        {{ $addonCount }} {{ $addonCount > 1 ? 'Items' : 'Item' }}
                    </div>
                    <div class="text-xs text-secondary-text truncate max-w-[150px]" title="{{ implode(', ', array_filter($addonNames)) }}">
                        {{ implode(', ', array_filter($addonNames)) }}
                    </div>
                </div>
            </div>
        @else
            <span class="text-secondary-text italic opacity-60 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                None
            </span>
        @endif
    </td>

    <!-- Notes Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @if($booking->notes)
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-accent flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                </svg>
                <div class="text-sm text-primary-text max-w-xs truncate" title="{{ $booking->notes }}">
                    {{ $booking->notes }}
                </div>
            </div>
        @else
            <span class="text-secondary-text italic opacity-60">—</span>
        @endif
    </td>
    <!-- Payment Status Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @php
            $statusConfig = match($booking->payment_status) {
                'paid' => [
                    'class' => 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200 ring-2 ring-green-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'text' => 'Paid'
                ],
                'refunded' => [
                    'class' => 'bg-gradient-to-r from-purple-50 to-violet-50 text-purple-700 border border-purple-200 ring-2 ring-purple-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>',
                    'text' => 'Refunded'
                ],
                default => [
                    'class' => 'bg-gradient-to-r from-orange-50 to-red-50 text-orange-700 border border-orange-200 ring-2 ring-orange-100',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                    'text' => 'Unpaid'
                ]
            };
        @endphp
        <span class="inline-flex items-center text-xs rounded-xl px-3 py-2 font-bold {{ $statusConfig['class'] }} shadow-sm hover:shadow-md transition-all">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                {!! $statusConfig['icon'] !!}
            </svg>
            {{ $statusConfig['text'] }}
        </span>
    </td>

    <!-- Amount Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-accent/15 to-accent/5 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-sm font-bold text-accent">
                ₱{{ number_format($booking->total_amount, 2) }}
            </div>
        </div>
    </td>

    <!-- Status Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        @php
            $statusClasses = match($booking->status) {
                'confirmed' => 'bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 border border-green-200 focus:ring-green-500',
                'pending' => 'bg-gradient-to-r from-yellow-50 to-amber-50 text-yellow-700 border border-yellow-200 focus:ring-yellow-500',
                'completed' => 'bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 border border-blue-200 focus:ring-blue-500',
                'canceled' => 'bg-gradient-to-r from-red-50 to-rose-50 text-red-700 border border-red-200 focus:ring-red-500',
                default => 'bg-gray-50 text-gray-700 border border-gray-200 focus:ring-gray-500'
            };
        @endphp
        <select class="booking-status-select text-xs rounded-xl px-3 py-2 font-bold cursor-pointer transition-all shadow-sm hover:shadow-md focus:ring-2 focus:ring-offset-1 {{ $statusClasses }}" 
                data-booking-id="{{ $booking->booking_id }}" 
                data-original-status="{{ $booking->status }}">
            @php $availableStatuses = isset($statuses) ? $statuses : ['pending', 'confirmed', 'completed', 'canceled']; @endphp
            @foreach($availableStatuses as $status)
                <option value="{{ $status }}" {{ $booking->status === $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
    </td>

    <!-- Created Column -->
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-2">
            <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-accent/15 to-accent/5 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <div class="text-sm text-primary-text font-semibold">
                    {{ $booking->created_at ? $booking->created_at->format('M d, Y') : 'N/A' }}
                </div>
                <div class="text-xs text-secondary-text">
                    {{ $booking->created_at ? $booking->created_at->format('h:i A') : '' }}
                </div>
            </div>
        </div>
    </td>

    <!-- Actions Column -->
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
        <div class="flex space-x-2 justify-end items-center">
            <a href="{{ route('bookings.show', $booking->booking_id) }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-accent/20 to-accent/10 text-accent hover:from-accent hover:to-accent-hover hover:text-white transition-all hover:scale-110 shadow-sm hover:shadow-md" 
               title="View Details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </a>
            <a href="{{ route('bookings.edit', $booking->booking_id) }}" 
               class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-yellow-100 to-amber-100 text-yellow-600 hover:from-yellow-500 hover:to-amber-500 hover:text-white transition-all hover:scale-110 shadow-sm hover:shadow-md" 
               title="Edit Booking">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </a>
            <form action="{{ route('bookings.destroy', $booking->booking_id) }}" 
                  method="POST" 
                  class="inline delete-booking-form">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-red-100 to-rose-100 text-red-600 hover:from-red-500 hover:to-rose-500 hover:text-white transition-all hover:scale-110 shadow-sm hover:shadow-md" 
                        title="Delete Booking">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="13" class="px-6 py-16 text-center bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-gradient-to-br from-accent/20 to-accent/10 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="text-base font-semibold text-primary-text mb-2">No bookings found</p>
            <p class="text-sm text-secondary-text">Try adjusting your filters or search terms</p>
        </div>
    </td>
</tr>
@endforelse