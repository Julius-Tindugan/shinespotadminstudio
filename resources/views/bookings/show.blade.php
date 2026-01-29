@extends('layouts.app')
@section('title', 'Booking Details')
@section('content')
    <div @class(['container', 'mx-auto', 'px-4', 'py-8'])>

        <div @class(['flex', 'items-center', 'justify-between', 'mb-6'])>

            <div>

                <h1 @class(['text-2xl', 'font-bold', 'text-primary-text'])>Booking Details</h1>

                <p @class(['text-sm', 'text-secondary-text', 'mt-1'])>Booking #{{ $booking->booking_id }}</p>

            </div>

            <div @class(['flex', 'space-x-2'])>
                <a href="{{ route('bookings.edit', $booking) }}" @class([
                    'px-4',
                    'py-2',
                    'bg-accent',
                    'hover:bg-accent-hover',
                    'text-white',
                    'rounded-md',
                    'transition-colors',
                    'flex',
                    'items-center',
                ])><svg
                        @class(['w-4', 'h-4', 'mr-1']) fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg> Edit Booking </a><a href="{{ route('bookings.index') }}" @class([
                        'px-4',
                        'py-2',
                        'bg-background',
                        'border',
                        'border-border-color',
                        'rounded-md',
                        'hover:bg-gray-100',
                        'flex',
                        'items-center',
                        'transition-colors',
                    ])><svg
                        @class(['w-4', 'h-4', 'mr-1']) fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg> Back to Bookings </a>
            </div>

        </div>

        @if (session('success'))
            <div @class([
                'bg-green-100',
                'border-l-4',
                'border-green-500',
                'text-green-700',
                'p-4',
                'mb-4',
                'rounded',
            ]) role="alert">

                <p>{{ session('success') }}</p>

            </div>
        @endif

        @if (session('error'))
            <div @class([
                'bg-red-100',
                'border-l-4',
                'border-red-500',
                'text-red-700',
                'p-4',
                'mb-4',
                'rounded',
            ]) role="alert">

                <p>{{ session('error') }}</p>

            </div>
        @endif

        <div @class(['grid', 'grid-cols-1', 'xl:grid-cols-4', 'gap-6'])>
            <!-- Left Column - Main Booking Information -->
            <div @class(['xl:col-span-3', 'space-y-6'])>
                <!-- Booking Overview Card -->
                <div @class([
                    'bg-card-bg',
                    'border',
                    'border-border-color',
                    'rounded-lg',
                    'shadow-sm',
                    'overflow-hidden',
                ])>

                    <div @class([
                        'bg-gradient-to-r',
                        'from-accent',
                        'to-accent-hover',
                        'px-6',
                        'py-4',
                    ])>

                        <h2 @class([
                            'text-xl',
                            'font-semibold',
                            'text-white',
                            'flex',
                            'items-center',
                        ])><svg @class(['w-5', 'h-5', 'mr-2']) fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg> Booking Overview </h2>

                        <p @class(['text-white/80', 'text-sm', 'mt-1'])>Reference: {{ $booking->booking_reference ?? 'N/A' }}</p>

                    </div>

                    <div @class(['p-6'])>

                        <div @class(['grid', 'grid-cols-1', 'md:grid-cols-3', 'gap-6'])>
                            <!-- Client Information -->
                            <div @class([
                                'bg-blue-50',
                                'p-4',
                                'rounded-lg',
                                'border',
                                'border-blue-200',
                            ])>

                                <h3 @class([
                                    'text-sm',
                                    'font-semibold',
                                    'text-blue-800',
                                    'mb-3',
                                    'flex',
                                    'items-center',
                                ])><svg @class(['w-4', 'h-4', 'mr-2']) fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg> Client Details </h3>

                                <div @class(['space-y-2'])>

                                    <p @class(['font-medium', 'text-primary-text'])> {{ $booking->client_first_name }}
                                        {{ $booking->client_last_name }} </p>

                                    @if ($booking->client_email)
                                        <p @class(['text-sm', 'text-secondary-text', 'flex', 'items-center'])><svg @class(['w-3', 'h-3', 'mr-1']) fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg> {{ $booking->client_email }} </p>
                                    @endif

                                    @if ($booking->client_phone)
                                        <p @class(['text-sm', 'text-secondary-text', 'flex', 'items-center'])><svg @class(['w-3', 'h-3', 'mr-1']) fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg> {{ $booking->client_phone }} </p>
                                    @endif

                                </div>

                            </div>
                            <!-- Session Details -->
                            <div @class([
                                'bg-green-50',
                                'p-4',
                                'rounded-lg',
                                'border',
                                'border-green-200',
                            ])>

                                <h3 @class([
                                    'text-sm',
                                    'font-semibold',
                                    'text-green-800',
                                    'mb-3',
                                    'flex',
                                    'items-center',
                                ])><svg @class(['w-4', 'h-4', 'mr-2']) fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg> Session Info </h3>

                                <div @class(['space-y-2'])>

                                    <p @class(['text-sm'])><span @class(['font-medium', 'text-primary-text'])>Date:</span><span
                                            @class(['text-secondary-text'])>{{ $booking->booking_date->format('F d, Y') }}</span>
                                    </p>

                                    <p @class(['text-sm'])><span @class(['font-medium', 'text-primary-text'])>Time:</span><span
                                            @class(['text-secondary-text'])>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }}
                                            - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span></p>

                                    @if ($booking->venue)
                                        <p @class(['text-sm'])><span
                                                @class(['font-medium', 'text-primary-text'])>Venue:</span><span
                                                @class(['text-secondary-text'])>{{ $booking->venue }}</span></p>
                                    @endif

                                    <div @class(['flex', 'items-center', 'mt-2'])>
                                        <span @class(['text-sm', 'font-medium', 'text-primary-text', 'mr-2'])>Status:</span>
                                        <span @class([
                                            'status-badge',
                                            'inline-flex',
                                            'items-center',
                                            'px-2.5',
                                            'py-0.5',
                                            'rounded-full',
                                            'text-xs',
                                            'font-medium',
                                            'bg-green-100 text-green-800' => $booking->status === 'confirmed',
                                            'bg-yellow-100 text-yellow-800' => $booking->status === 'pending',
                                            'bg-blue-100 text-blue-800' => $booking->status === 'completed',
                                            'bg-red-100 text-red-800' => $booking->status === 'canceled',
                                        ])>
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>

                                </div>

                            </div>
                            <!-- Staff & Package -->
                            <div @class([
                                'bg-purple-50',
                                'p-4',
                                'rounded-lg',
                                'border',
                                'border-purple-200',
                            ])>

                                <h3 @class([
                                    'text-sm',
                                    'font-semibold',
                                    'text-purple-800',
                                    'mb-3',
                                    'flex',
                                    'items-center',
                                ])><svg @class(['w-4', 'h-4', 'mr-2']) fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg> Team & Package </h3>

                                <div @class(['space-y-2'])>

                                    @if ($booking->primaryStaff)
                                        <p @class(['text-sm'])><span
                                                @class(['font-medium', 'text-primary-text'])>Staff:</span><span
                                                @class(['text-secondary-text'])>{{ $booking->primaryStaff->first_name }}
                                                {{ $booking->primaryStaff->last_name }}</span></p>
                                    @endif

                                    @if ($booking->package)
                                        <p @class(['text-sm'])><span
                                                @class(['font-medium', 'text-primary-text'])>Package:</span><span
                                                @class(['text-secondary-text'])>{{ $booking->package->title }}</span></p>
                                    @endif

                                    @if ($booking->booking_reference)
                                        <p @class(['text-sm'])><span
                                                @class(['font-medium', 'text-primary-text'])>Reference:</span><span
                                                @class(['text-secondary-text'])>{{ $booking->booking_reference }}</span></p>
                                    @endif

                                    @if ($booking->backdrop_id || $booking->backdrop_selections)
                                        <p @class(['text-sm'])>
                                            <span @class(['font-medium', 'text-primary-text'])>Backdrop:</span>
                                            <span @class(['text-secondary-text'])>
                                                @if ($booking->backdrop_id && $booking->backdrop)
                                                    {{ $booking->backdrop->name ?? 'Unknown' }}
                                                    @if ($booking->backdrop->color_code)
                                                        <span @class([
                                                            'inline-block',
                                                            'w-3',
                                                            'h-3',
                                                            'ml-1',
                                                            'border',
                                                            'border-gray-300',
                                                            'rounded-sm',
                                                        ])
                                                            style="background-color: {{ $booking->backdrop->color_code }}"></span>
                                                    @endif
                                                @elseif ($booking->backdrop_selections)
                                                    @php
                                                        $selections = $booking->formatted_backdrop_selections;
                                                    @endphp
                                                    @if(!empty($selections))
                                                        <span class="inline-flex items-center gap-1.5 flex-wrap">
                                                            @foreach($selections as $index => $selection)
                                                                <span class="inline-flex items-center gap-1">
                                                                    @if(isset($selection['color']))
                                                                        <span class="inline-block w-3 h-3 border border-gray-300 rounded-sm" 
                                                                              style="background-color: {{ $selection['color'] }};"></span>
                                                                    @endif
                                                                    <span>{{ $selection['name'] ?? $selection['color'] ?? 'Unknown' }}</span>
                                                                </span>
                                                                @if($index < count($selections) - 1)
                                                                    <span>,</span>
                                                                @endif
                                                            @endforeach
                                                        </span>
                                                    @else
                                                        Custom Selection
                                                    @endif
                                                @endif
                                            </span>
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <!-- Financial Summary Card -->
                <div @class([
                    'bg-card-bg',
                    'border',
                    'border-border-color',
                    'rounded-lg',
                    'shadow-sm',
                    'overflow-hidden',
                ])>

                    <div @class([
                        'bg-gradient-to-r',
                        'from-green-600',
                        'to-green-700',
                        'px-6',
                        'py-4',
                    ])>

                        <h2 @class([
                            'text-xl',
                            'font-semibold',
                            'text-white',
                            'flex',
                            'items-center',
                        ])><svg @class(['w-5', 'h-5', 'mr-2']) fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg> Financial Summary </h2>

                    </div>

                    <div @class(['p-6'])>

                        <div @class(['grid', 'grid-cols-1', 'md:grid-cols-4', 'gap-4'])>
                            @php
                                $addonTotal = 0;
                                if ($booking->hasAddons()) {
                                    if ($booking->relationLoaded('addons') && $booking->addons instanceof \Illuminate\Database\Eloquent\Collection) {
                                        $addonTotal = $booking->addons->sum(function ($addon) {
                                            return $addon->pivot->price * $addon->pivot->quantity;
                                        });
                                    } elseif (is_array($booking->addons)) {
                                        foreach ($booking->addons as $addon) {
                                            $addonTotal += (float)($addon['price'] ?? 0) * (int)($addon['quantity'] ?? 1);
                                        }
                                    }
                                }
                                $totalPaid = $booking->payments->sum('amount');
                                $balance = $booking->total_amount - $totalPaid;
                                $packagePrice = $booking->package ? $booking->package->price : 0;
                            @endphp
                            <div @class(['text-center', 'p-4', 'bg-blue-50', 'rounded-lg'])>

                                <p @class([
                                    'text-xs',
                                    'font-medium',
                                    'text-blue-600',
                                    'uppercase',
                                    'tracking-wider',
                                ])>Package</p>

                                <p @class(['text-lg', 'font-bold', 'text-blue-800'])>₱{{ number_format($packagePrice, 2) }}</p>

                            </div>

                            <div @class(['text-center', 'p-4', 'bg-purple-50', 'rounded-lg'])>

                                <p @class([
                                    'text-xs',
                                    'font-medium',
                                    'text-purple-600',
                                    'uppercase',
                                    'tracking-wider',
                                ])>Addons</p>

                                <p @class(['text-lg', 'font-bold', 'text-purple-800'])>₱{{ number_format($addonTotal, 2) }}</p>

                            </div>

                            <div @class(['text-center', 'p-4', 'bg-gray-50', 'rounded-lg'])>

                                <p @class([
                                    'text-xs',
                                    'font-medium',
                                    'text-gray-600',
                                    'uppercase',
                                    'tracking-wider',
                                ])>Total Amount</p>

                                <p @class(['text-lg', 'font-bold', 'text-gray-800'])>₱{{ number_format($booking->total_amount, 2) }}</p>

                            </div>

                            <div @class([
                                'text-center',
                                'p-4',
                                'rounded-lg',
                                'bg-green-50' => $balance <= 0,
                                'bg-red-50' => $balance > 0,
                            ])>

                                <p @class([
                                    'text-xs',
                                    'font-medium',
                                    'uppercase',
                                    'tracking-wider',
                                    'text-green-600' => $balance <= 0,
                                    'text-red-600' => $balance > 0,
                                ])>Balance</p>

                                <p @class([
                                    'text-lg',
                                    'font-bold',
                                    'text-green-800' => $balance <= 0,
                                    'text-red-800' => $balance > 0,
                                ])>
                                    ₱{{ number_format($balance, 2) }}
                                </p>

                            </div>

                        </div>

                        <div @class(['mt-4', 'pt-4', 'border-t', 'border-border-color'])>

                            <div @class(['flex', 'justify-between', 'items-center', 'text-sm'])>
                                <span @class(['text-secondary-text'])>Amount Paid:</span><span
                                    @class(['font-medium', 'text-primary-text'])>₱{{ number_format($totalPaid, 2) }}</span>
                            </div>

                        </div>

                    </div>

                </div>
                <!-- Addons Details Card -->
                @if ($booking->hasAddons())
                    <div @class([
                        'bg-card-bg',
                        'border',
                        'border-border-color',
                        'rounded-lg',
                        'shadow-sm',
                        'overflow-hidden',
                    ])>

                        <div @class([
                            'bg-gradient-to-r',
                            'from-purple-600',
                            'to-purple-700',
                            'px-6',
                            'py-4',
                        ])>

                            <h2 @class([
                                'text-xl',
                                'font-semibold',
                                'text-white',
                                'flex',
                                'items-center',
                            ])><svg @class(['w-5', 'h-5', 'mr-2']) fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg> Addons & Extras </h2>

                        </div>

                        <div @class(['overflow-x-auto'])>
                            <table @class(['min-w-full', 'divide-y', 'divide-border-color'])>
                                <thead @class(['bg-gray-50'])>
                                    <tr>
                                        <th @class([
                                            'px-6',
                                            'py-3',
                                            'text-left',
                                            'text-xs',
                                            'font-medium',
                                            'text-secondary-text',
                                            'uppercase',
                                            'tracking-wider',
                                        ])>Addon</th>
                                        <th @class([
                                            'px-6',
                                            'py-3',
                                            'text-center',
                                            'text-xs',
                                            'font-medium',
                                            'text-secondary-text',
                                            'uppercase',
                                            'tracking-wider',
                                        ])>Quantity</th>
                                        <th @class([
                                            'px-6',
                                            'py-3',
                                            'text-right',
                                            'text-xs',
                                            'font-medium',
                                            'text-secondary-text',
                                            'uppercase',
                                            'tracking-wider',
                                        ])>Unit Price</th>
                                        <th @class([
                                            'px-6',
                                            'py-3',
                                            'text-right',
                                            'text-xs',
                                            'font-medium',
                                            'text-secondary-text',
                                            'uppercase',
                                            'tracking-wider',
                                        ])>Total</th>
                                    </tr>
                                </thead>
                                <tbody @class(['bg-white', 'divide-y', 'divide-border-color'])>
                                    @php
                                        $addonsToDisplay = [];
                                        if ($booking->relationLoaded('addons') && $booking->addons instanceof \Illuminate\Database\Eloquent\Collection) {
                                            $addonsToDisplay = $booking->addons;
                                        } elseif (is_array($booking->addons)) {
                                            // Convert array data to objects for consistent display
                                            foreach ($booking->addons as $addonData) {
                                                $addon = new stdClass();
                                                $addon->addon_name = $addonData['name'] ?? 'Unknown Addon';
                                                $addon->description = null;
                                                $addon->pivot = new stdClass();
                                                $addon->pivot->quantity = $addonData['quantity'] ?? 1;
                                                $addon->pivot->price = (float)($addonData['price'] ?? 0);
                                                $addonsToDisplay[] = $addon;
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach ($addonsToDisplay as $addon)
                                        <tr @class(['hover:bg-gray-50'])>
                                            <td @class(['px-6', 'py-4', 'whitespace-nowrap'])>
                                                <div @class(['text-sm', 'font-medium', 'text-primary-text'])>
                                                    {{ $addon->addon_name }}
                                                </div>

                                                @if ($addon->description)
                                                    <div @class(['text-xs', 'text-secondary-text'])>
                                                        {{ $addon->description }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td @class(['px-6', 'py-4', 'whitespace-nowrap', 'text-center'])><span @class([
                                                'inline-flex',
                                                'items-center',
                                                'px-2.5',
                                                'py-0.5',
                                                'rounded-full',
                                                'text-xs',
                                                'font-medium',
                                                'bg-blue-100',
                                                'text-blue-800',
                                            ])>
                                                    {{ $addon->pivot->quantity }} </span></td>
                                            <td @class([
                                                'px-6',
                                                'py-4',
                                                'whitespace-nowrap',
                                                'text-right',
                                                'text-sm',
                                                'text-primary-text',
                                            ])>
                                                ₱{{ number_format($addon->pivot->price, 2) }} </td>
                                            <td @class([
                                                'px-6',
                                                'py-4',
                                                'whitespace-nowrap',
                                                'text-right',
                                                'text-sm',
                                                'font-medium',
                                                'text-primary-text',
                                            ])>
                                                ₱{{ number_format($addon->pivot->price * $addon->pivot->quantity, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot @class(['bg-gray-50'])>
                                    <tr>
                                        <td colspan="3" @class([
                                            'px-6',
                                            'py-3',
                                            'text-right',
                                            'text-sm',
                                            'font-medium',
                                            'text-primary-text',
                                        ])> Addons Subtotal: </td>
                                        <td @class([
                                            'px-6',
                                            'py-3',
                                            'text-right',
                                            'text-sm',
                                            'font-bold',
                                            'text-primary-text',
                                        ])> ₱{{ number_format($addonTotal, 2) }} </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>

                @endif
                <!-- Notes Section -->
                @if ($booking->notes)
                    <div @class([
                        'bg-card-bg',
                        'border',
                        'border-border-color',
                        'rounded-lg',
                        'shadow-sm',
                        'overflow-hidden',
                    ])>

                        <div @class([
                            'bg-gradient-to-r',
                            'from-orange-600',
                            'to-orange-700',
                            'px-6',
                            'py-4',
                        ])>

                            <h2 @class([
                                'text-xl',
                                'font-semibold',
                                'text-white',
                                'flex',
                                'items-center',
                            ])><svg @class(['w-5', 'h-5', 'mr-2']) fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg> Notes & Comments </h2>

                        </div>

                        <div @class(['p-6'])>

                            <div @class([
                                'bg-orange-50',
                                'p-4',
                                'rounded-lg',
                                'border',
                                'border-orange-200',
                            ])>

                                <p @class(['text-primary-text', 'whitespace-pre-wrap'])>{{ $booking->notes }}</p>

                            </div>

                        </div>

                    </div>
                @endif
                <!-- Booking Timeline -->
                <div @class([
                    'bg-card-bg',
                    'border',
                    'border-border-color',
                    'rounded-lg',
                    'shadow-sm',
                    'overflow-hidden',
                ])>

                    <div @class([
                        'bg-gradient-to-r',
                        'from-gray-600',
                        'to-gray-700',
                        'px-6',
                        'py-4',
                    ])>

                        <h2 @class([
                            'text-xl',
                            'font-semibold',
                            'text-white',
                            'flex',
                            'items-center',
                        ])><svg @class(['w-5', 'h-5', 'mr-2']) fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg> Booking Timeline </h2>

                    </div>

                    <div @class(['p-6'])>

                        <div @class(['space-y-3'])>

                            <div @class(['flex', 'items-center', 'text-sm'])>

                                <div @class(['w-2', 'h-2', 'bg-blue-500', 'rounded-full', 'mr-3'])>

                                </div>
                                <span @class(['text-secondary-text'])>Created:</span><span
                                    @class(['ml-2', 'font-medium', 'text-primary-text'])>{{ $booking->created_at->format('M d, Y h:i A') }}</span>
                            </div>

                            @if ($booking->updated_at->gt($booking->created_at))
                                <div @class(['flex', 'items-center', 'text-sm'])>

                                    <div @class(['w-2', 'h-2', 'bg-yellow-500', 'rounded-full', 'mr-3'])>

                                    </div>
                                    <span @class(['text-secondary-text'])>Last Updated:</span><span
                                        @class(['ml-2', 'font-medium', 'text-primary-text'])>{{ $booking->updated_at->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif

                            @if ($booking->canceled_at)
                                <div @class(['flex', 'items-center', 'text-sm'])>

                                    <div @class(['w-2', 'h-2', 'bg-red-500', 'rounded-full', 'mr-3'])>

                                    </div>
                                    <span @class(['text-secondary-text'])>Canceled:</span><span
                                        @class(['ml-2', 'font-medium', 'text-primary-text'])>{{ $booking->canceled_at->format('M d, Y h:i A') }}</span>
                                </div>
                            @endif

                        </div>

                    </div>

                </div>

            </div>
            <!-- Right Column - Payments & Actions -->
            <div @class(['xl:col-span-1', 'space-y-6'])>
                
                @include('bookings.partials.payment-summary', ['booking' => $booking, 'paymentSummary' => $paymentSummary])
                
                <!-- Quick Actions -->
                <div @class([
                    'bg-card-bg',
                    'border',
                    'border-border-color',
                    'rounded-lg',
                    'shadow-sm',
                ])>

                    <div @class([
                        'px-4',
                        'py-3',
                        'bg-gray-50',
                        'border-b',
                        'border-border-color',
                    ])>

                        <h3 @class(['text-lg', 'font-semibold', 'text-primary-text'])>Quick Actions</h3>

                    </div>

                    <div @class(['p-4', 'space-y-3'])>
                        <!-- Status Update -->
                        <div>
                            <label @class([
                                'block',
                                'text-xs',
                                'font-medium',
                                'text-secondary-text',
                                'mb-1',
                            ])>Change Status</label>
                            <select id="quick-status-update" @class([
                                'w-full',
                                'text-sm',
                                'px-3',
                                'py-2',
                                'rounded',
                                'border',
                                'border-border-color',
                                'bg-background',
                                'text-primary-text',
                                'focus:outline-none',
                                'focus:ring-2',
                                'focus:ring-accent',
                            ])
                                data-booking-id="{{ $booking->booking_id }}">
                                @foreach (['pending', 'confirmed', 'completed', 'canceled'] as $status)
                                    <option value="{{ $status }}"
                                        {{ $booking->status === $status ? 'selected' : '' }}> {{ ucfirst($status) }}
                                    </option>
                                @endforeach

                            </select>

                        </div>
                        <!-- Action Buttons -->
                        <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="delete-booking-form"
                            @class(['mt-4'])>
                            @csrf @method('DELETE')
                            <button type="submit" @class([
                                'w-full',
                                'flex',
                                'justify-center',
                                'items-center',
                                'bg-red-600',
                                'hover:bg-red-700',
                                'text-white',
                                'px-3',
                                'py-2',
                                'rounded-md',
                                'transition-colors',
                                'text-sm',
                            ])><svg @class(['w-4', 'h-4', 'mr-2'])
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg> Delete Booking </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteBookingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Booking</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this booking? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmDeleteBooking" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                    <button id="cancelDeleteBooking" class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Utility function to show UI alerts
    function showAlert(message, type = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
            type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
            type === 'success' ? 'bg-green-100 border border-green-400 text-green-700' :
            'bg-blue-100 border border-blue-400 text-blue-700'
        } flex items-center justify-between max-w-md`;
        
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'error' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span>${message}</span>
            </div>
            <button class="ml-3 text-current hover:opacity-70" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        
        document.body.appendChild(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Quick status update
        const statusSelect = document.getElementById('quick-status-update');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                const bookingId = this.dataset.bookingId;
                const newStatus = this.value;
                
                console.log('Updating status for booking:', bookingId, 'to', newStatus);
                
                // Send AJAX request to update status
                fetch(`/bookings/${bookingId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success notification
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md z-50';
                        notification.innerHTML = '<p>Status updated successfully!</p>';
                        document.body.appendChild(notification);
                        
                        // Remove notification after 3 seconds
                        setTimeout(() => {
                            notification.remove();
                        }, 3000);
                        
                        // Update status badge in the UI
                        const statusBadge = document.querySelector('.status-badge');
                        if (statusBadge) {
                            // Remove old classes
                            statusBadge.classList.remove(
                                'bg-green-100', 'text-green-800',
                                'bg-yellow-100', 'text-yellow-800',
                                'bg-blue-100', 'text-blue-800',
                                'bg-red-100', 'text-red-800'
                            );
                            
                            // Add new classes based on status
                            if (newStatus === 'confirmed') {
                                statusBadge.classList.add('bg-green-100', 'text-green-800');
                            } else if (newStatus === 'pending') {
                                statusBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                            } else if (newStatus === 'completed') {
                                statusBadge.classList.add('bg-blue-100', 'text-blue-800');
                            } else if (newStatus === 'canceled') {
                                statusBadge.classList.add('bg-red-100', 'text-red-800');
                            }
                            
                            // Update text
                            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        }
                    } else {
                        // Error notification
                        showAlert('Failed to update status: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while updating the status', 'error');
                });
            });
        } 
        
        // Delete booking confirmation modal
        let deleteFormToSubmit = null;
        
        const deleteForm = document.querySelector('.delete-booking-form');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();
                deleteFormToSubmit = this;
                showDeleteModal();
            });
        }
        
        function showDeleteModal() {
            const modal = document.getElementById('deleteBookingModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        
        function hideDeleteModal() {
            const modal = document.getElementById('deleteBookingModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            deleteFormToSubmit = null;
        }
        
        // Confirm delete button
        const confirmDeleteBtn = document.getElementById('confirmDeleteBooking');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (deleteFormToSubmit) {
                    deleteFormToSubmit.submit();
                }
            });
        }
        
        // Cancel delete button
        const cancelDeleteBtn = document.getElementById('cancelDeleteBooking');
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', hideDeleteModal);
        }
        
        // Close modal when clicking outside
        const deleteModal = document.getElementById('deleteBookingModal');
        if (deleteModal) {
            deleteModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });
        }
    });
</script> @endsection
