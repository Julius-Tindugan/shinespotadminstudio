
@extends('layouts.app')
@section('title', 'Studio Management')
@section('content')
    <div class="container px-6 py-8 mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-primary-text mb-2">Studio Management</h1>
            <p class="text-secondary-text">Manage your studio backdrops, equipment, and inventory</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Backdrop Stats -->
            <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-accent/30">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-primary-text">Backdrops</h2>
                    <div class="p-3 bg-accent/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-secondary-text">Total Backdrops</span>
                        <span class="text-2xl font-bold text-primary-text">{{ $totalBackdrops }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-secondary-text">Active Backdrops</span>
                        <span class="text-xl font-semibold text-green-600">{{ $activeBackdrops }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('backdrops.index') }}" class="group flex items-center justify-between text-sm font-medium text-accent hover:text-accent-dark transition-colors">
                        <span>Manage Backdrops</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Equipment Stats -->
            <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-accent/30">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-primary-text">Equipment</h2>
                    <div class="p-3 bg-accent/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-secondary-text">Total Equipment</span>
                        <span class="text-2xl font-bold text-primary-text">{{ $totalEquipment }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-secondary-text">Active Equipment</span>
                        <span class="text-xl font-semibold text-green-600">{{ $activeEquipment }}</span>
                    </div>

                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-secondary-text">Available Now</span>
                        <span class="text-xl font-semibold text-blue-600">{{ $availableEquipment }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('equipment.index') }}" class="group flex items-center justify-between text-sm font-medium text-accent hover:text-accent-dark transition-colors">
                        <span>Manage Equipment</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:border-accent/30">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-primary-text">Quick Actions</h2>
                    <div class="p-3 bg-accent/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('backdrops.create') }}" class="group flex items-center justify-between p-3 rounded-lg hover:bg-accent/5 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-accent/10 rounded-lg mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-primary-text group-hover:text-accent transition-colors">Add New Backdrop</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-secondary-text group-hover:text-accent transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="{{ route('equipment.create') }}" class="group flex items-center justify-between p-3 rounded-lg hover:bg-accent/5 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-accent/10 rounded-lg mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-primary-text group-hover:text-accent transition-colors">Add New Equipment</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-secondary-text group-hover:text-accent transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="{{ route('bookings.create') }}" class="group flex items-center justify-between p-3 rounded-lg hover:bg-accent/5 transition-all duration-200">
                        <div class="flex items-center">
                            <div class="p-2 bg-accent/10 rounded-lg mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-primary-text group-hover:text-accent transition-colors">Create New Booking</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-secondary-text group-hover:text-accent transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Equipment by Type Chart & Recent Bookings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-primary-text">Equipment by Type</h2>
                    <div class="p-2 bg-accent/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>

                <div class="h-64">
                    <canvas id="equipmentTypeChart"></canvas>
                </div>
            </div>

            <!-- Recent Bookings with Studio Items -->
            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-primary-text">Recent Bookings</h2>
                    <div class="p-2 bg-accent/10 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                @if($recentBookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Date</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Client</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Studio Items</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-secondary-text uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($recentBookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="py-3 px-4 text-sm text-primary-text whitespace-nowrap">
                                            {{ $booking->booking_date->format('M d, Y') }}
                                        </td>
                                        <td class="py-3 px-4 text-sm text-primary-text">
                                            {{ $booking->client_name }}
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <div class="flex flex-wrap gap-1">
                                                @if($booking->backdrop)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/10 text-accent">
                                                        Backdrop: {{ $booking->backdrop->name }}
                                                    </span>
                                                @endif

                                                @if($booking->equipment->count() > 0)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        Equipment: {{ $booking->equipment->count() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <a href="{{ route('bookings.show', $booking->booking_id) }}" class="text-accent hover:text-accent-dark font-medium transition-colors">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-secondary-text text-sm">No bookings with studio items found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
                @endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Equipment by Type Chart
    const ctx = document.getElementById('equipmentTypeChart').getContext('2d');
    const equipmentData = @json($equipmentByType);
    const labels = equipmentData.map(item => item.type);
    const data = equipmentData.map(item => item.count);
    
    // Generate modern gradient colors
    const colors = [
        '#3B82F6', '#8B5CF6', '#EC4899', '#F59E0B', 
        '#10B981', '#6366F1', '#F97316', '#14B8A6'
    ];
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        color: document.documentElement.classList.contains('dark') ? '#CBD5E0' : '#4A5568',
                        font: {
                            size: 12,
                            family: "'Inter', sans-serif"
                        },
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1
                }
            }
        }
    });
});
</script>
@endpush
