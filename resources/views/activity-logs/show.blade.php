@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="{{ route('activity-logs.index') }}" class="text-secondary-text hover:text-primary-text">
                                    Activity Logs
                                </a>
                            </div>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-secondary-text">Log #{{ $log->id }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold text-primary-text mt-2">Activity Log Details</h1>
            </div>
            <a href="{{ route('activity-logs.index') }}" class="btn-secondary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Logs
            </a>
        </div>

        <!-- Main Content -->
        <div class="bg-card-bg rounded-lg shadow-subtle overflow-hidden">
            <!-- Header Info -->
            <div class="px-6 py-4 bg-header-bg border-b border-border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $log->user_type === 'admin' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                            @if($log->user_type === 'admin')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-primary-text">{{ $log->action_description }}</h3>
                            <p class="text-sm text-secondary-text">{{ $log->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full 
                        @if($log->action === 'created') bg-green-100 text-green-800
                        @elseif($log->action === 'updated') bg-blue-100 text-blue-800
                        @elseif($log->action === 'deleted') bg-red-100 text-red-800
                        @elseif($log->action === 'login') bg-purple-100 text-purple-800
                        @elseif($log->action === 'logout') bg-gray-100 text-gray-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($log->action) }}
                    </span>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Information -->
                    <div>
                        <h4 class="text-lg font-medium text-primary-text mb-4">User Information</h4>
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">Name</dt>
                                <dd class="mt-1 text-sm text-primary-text">{{ $log->user_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">Type</dt>
                                <dd class="mt-1 text-sm text-primary-text">{{ ucfirst($log->user_type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">User ID</dt>
                                <dd class="mt-1 text-sm text-primary-text">#{{ $log->user_id }}</dd>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div>
                        <h4 class="text-lg font-medium text-primary-text mb-4">System Information</h4>
                        <div class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">IP Address</dt>
                                <dd class="mt-1 text-sm text-primary-text">{{ $log->ip_address }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">URL</dt>
                                <dd class="mt-1 text-sm text-primary-text break-all">{{ $log->url }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">Timestamp</dt>
                                <dd class="mt-1 text-sm text-primary-text">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                    <span class="text-secondary-text">({{ $log->created_at->diffForHumans() }})</span>
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                @if($log->model_type || $log->model_id)
                <!-- Model Information -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-primary-text mb-4">Affected Model</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($log->model_type)
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">Model Type</dt>
                                <dd class="mt-1 text-sm text-primary-text">{{ class_basename($log->model_type) }}</dd>
                            </div>
                            @endif
                            @if($log->model_id)
                            <div>
                                <dt class="text-sm font-medium text-secondary-text">Model ID</dt>
                                <dd class="mt-1 text-sm text-primary-text">#{{ $log->model_id }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                @if($log->old_values || $log->new_values)
                <!-- Data Changes -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-primary-text mb-4">Data Changes</h4>
                    
                    @if($log->action === 'updated' && $log->changes_summary)
                        <!-- User-Friendly Change Summary -->
                        <div class="mb-6">
                            <h5 class="text-md font-medium text-secondary-text mb-3">Changes Made</h5>
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Old Value</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">→</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Value</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($log->changes_summary as $change)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $change['field'] }}</td>
                                            <td class="px-4 py-3 text-sm text-red-600 bg-red-50">
                                                <div class="max-w-xs overflow-auto">{{ $change['old'] }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <svg class="w-4 h-4 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                                </svg>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-green-600 bg-green-50">
                                                <div class="max-w-xs overflow-auto">{{ $change['new'] }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Raw JSON Data (collapsible) -->
                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-medium text-secondary-text hover:text-primary-text mb-3">
                            View Raw Data (JSON)
                        </summary>
                        @if($log->old_values && $log->new_values)
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-3">
                                <!-- Old Values -->
                                <div>
                                    <h5 class="text-md font-medium text-secondary-text mb-3">Previous Values</h5>
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                        <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-auto max-h-96">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>

                                <!-- New Values -->
                                <div>
                                    <h5 class="text-md font-medium text-secondary-text mb-3">New Values</h5>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-auto max-h-96">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        @elseif($log->new_values)
                            <!-- Only New Values (Create) -->
                            <div class="mt-3">
                                <h5 class="text-md font-medium text-secondary-text mb-3">Created Data</h5>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-auto max-h-96">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @elseif($log->old_values)
                            <!-- Only Old Values (Delete) -->
                            <div class="mt-3">
                                <h5 class="text-md font-medium text-secondary-text mb-3">Deleted Data</h5>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap overflow-auto max-h-96">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        @endif
                    </details>
                </div>
                @endif

                @if($log->user_agent)
                <!-- User Agent -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-primary-text mb-4">Browser Information</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-primary-text break-all">{{ $log->user_agent }}</p>
                    </div>
                </div>
                @endif

                @if($log->description && $log->description !== $log->action_description)
                <!-- Additional Description -->
                <div class="mt-8">
                    <h4 class="text-lg font-medium text-primary-text mb-4">Additional Notes</h4>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-primary-text">{{ $log->description }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection