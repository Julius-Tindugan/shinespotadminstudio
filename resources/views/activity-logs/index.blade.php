@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-primary-text">Activity Logs</h1>
                <p class="text-secondary-text mt-1">Monitor all system activities by staff and admin users</p>
            </div>
            <div class="flex space-x-3">
                <button onclick="exportLogs()" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
                <button onclick="refreshLogs()" class="btn-primary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-card-bg p-6 rounded-lg shadow-subtle">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Today</p>
                        <p class="text-2xl font-semibold text-primary-text" id="today-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-lg shadow-subtle">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">This Week</p>
                        <p class="text-2xl font-semibold text-primary-text" id="week-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-lg shadow-subtle">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">This Month</p>
                        <p class="text-2xl font-semibold text-primary-text" id="month-count">-</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg p-6 rounded-lg shadow-subtle">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Total Records</p>
                        <p class="text-2xl font-semibold text-primary-text">{{ $logs->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-card-bg rounded-lg shadow-subtle mb-6">
            <!-- Filter Header -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-primary-text">Filters & Search</h3>
                    </div>
                    @if(array_filter($filters))
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                            {{ count(array_filter($filters)) }} filter(s) active
                        </span>
                    @endif
                </div>
            </div>

            <form method="GET" action="{{ route('activity-logs.index') }}" id="filterForm" class="p-6">
                <!-- First Row: Dropdowns with improved spacing -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="space-y-2">
                        <label for="user_type" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            User Type
                        </label>
                        <select name="user_type" id="user_type" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Types</option>
                            @foreach($userTypes as $type)
                                <option value="{{ $type }}" {{ ($filters['user_type'] ?? '') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="action" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Action
                        </label>
                        <select name="action" id="action" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ ($filters['action'] ?? '') === $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label for="model_type" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Model Type
                        </label>
                        <select name="model_type" id="model_type" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200" onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Models</option>
                            @foreach($modelTypes as $model)
                                <option value="{{ $model['value'] }}" {{ ($filters['model_type'] ?? '') === $model['value'] ? 'selected' : '' }}>
                                    {{ $model['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Second Row: Date Range with improved spacing -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-2">
                        <label for="start_date" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Start Date
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] ?? '' }}" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200" onchange="document.getElementById('filterForm').submit()">
                    </div>

                    <div class="space-y-2">
                        <label for="end_date" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            End Date
                        </label>
                        <input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] ?? '' }}" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200" onchange="document.getElementById('filterForm').submit()">
                    </div>
                </div>

                <!-- Third Row: Search and Action Buttons with improved layout -->
                <div class="flex flex-col lg:flex-row gap-4 items-end">
                    <div class="flex-1 space-y-2">
                        <label for="search" class="flex items-center text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Activities
                        </label>
                        <div class="relative">
                            <input type="text" name="search" id="search" placeholder="Search by description, action, or user..." value="{{ $filters['search'] ?? '' }}" class="form-input w-full rounded-lg border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50 transition-all duration-200 pl-10">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex gap-3 items-center">
                        <button type="submit" class="btn-primary px-8 py-3 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center whitespace-nowrap">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Apply Filters
                        </button>
                        @if(array_filter($filters))
                            <a href="{{ route('activity-logs.index') }}" class="btn-secondary px-8 py-3 rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 inline-flex items-center whitespace-nowrap">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear All
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Activity Logs Table -->
        <div class="bg-card-bg rounded-lg shadow-subtle overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border">
                    <thead class="bg-header-bg">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                Action
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                IP Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-text">
                                    <div class="flex flex-col">
                                        <span>{{ $log->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-gray-400">{{ $log->created_at->format('H:i:s') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $log->user_type === 'admin' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                                @if($log->user_type === 'admin')
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-primary-text">{{ $log->user_name }}</div>
                                            <div class="text-xs text-secondary-text">{{ ucfirst($log->user_type) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($log->action === 'created') bg-green-100 text-green-800
                                        @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                                        @elseif($log->action === 'deleted') bg-red-100 text-red-800
                                        @elseif($log->action === 'login') bg-purple-100 text-purple-800
                                        @elseif($log->action === 'logout') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-primary-text max-w-md">
                                    <div class="truncate" title="{{ $log->action_description }}">
                                        {{ $log->action_description }}
                                    </div>
                                    @if($log->model_type)
                                        <div class="text-xs text-secondary-text mt-1">
                                            {{ class_basename($log->model_type) }}
                                            @if($log->model_id)
                                                #{{ $log->model_id }}
                                            @endif
                                        </div>
                                    @endif
                                    @if($log->action === 'updated' && $log->changes_summary)
                                        <div class="mt-2 text-xs">
                                            <span class="font-semibold text-blue-600">Changes:</span>
                                            <ul class="ml-2 space-y-1 mt-1">
                                                @foreach(array_slice($log->changes_summary, 0, 3) as $change)
                                                    <li class="text-gray-600">
                                                        <span class="font-medium">{{ $change['field'] }}:</span>
                                                        <span class="text-red-600">{{ Str::limit($change['old'], 30) }}</span>
                                                        →
                                                        <span class="text-green-600">{{ Str::limit($change['new'], 30) }}</span>
                                                    </li>
                                                @endforeach
                                                @if(count($log->changes_summary) > 3)
                                                    <li class="text-gray-400 italic">+{{ count($log->changes_summary) - 3 }} more changes</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-text">
                                    {{ $log->ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-text">
                                    <a href="{{ route('activity-logs.show', $log->id) }}" class="text-accent hover:text-accent-dark">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-secondary-text">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-lg font-medium text-gray-400">No activity logs found</p>
                                        <p class="text-sm text-gray-400 mt-1">Try adjusting your filters to see more results</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="bg-header-bg px-6 py-3">
                    {{ $logs->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load statistics
    function loadStats() {
        fetch('{{ route("activity-logs.stats") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('today-count').textContent = data.today_total;
                document.getElementById('week-count').textContent = data.week_total;
                document.getElementById('month-count').textContent = data.month_total;
            })
            .catch(error => console.error('Error loading stats:', error));
    }

    // Export logs
    function exportLogs() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = '{{ route("activity-logs.export") }}?' + params.toString();
    }

    // Refresh logs
    function refreshLogs() {
        window.location.reload();
    }

    // Auto-refresh every 30 seconds
    setInterval(loadStats, 30000);

    // Load stats on page load
    document.addEventListener('DOMContentLoaded', loadStats);
</script>
@endpush
@endsection