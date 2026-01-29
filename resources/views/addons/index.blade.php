
@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto" >

        <div class="flex justify-between items-center mb-6" >

            <h1 class="text-2xl font-bold text-primary-text" >Addon Management</h1>
            <a href="{{ route('addons.create') }}" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-md transition-colors duration-150 flex items-center" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add New Addon </a>
            </div>
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center justify-between" role="alert">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="close-alert ml-4" aria-label="Close"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

                    </div>

                @endif
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center justify-between" role="alert">
                        <span>{{ session('error') }}</span>
                        <button type="button" class="close-alert ml-4" aria-label="Close"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

                        </div>

                    @endif

                    <div class="bg-card-bg rounded-lg shadow-sm overflow-hidden" >

                        <div class="overflow-x-auto" >
                            <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Addon Name</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Price</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Description</th><th scope="col" class="px-6 py-3 text-center text-xs font-medium text-secondary-text uppercase tracking-wider" >Status</th><th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider" >Actions</th></tr></thead><tbody class="bg-white divide-y divide-border-color" id="addons-table-body"> @forelse($addons as $addon) <tr><td class="px-6 py-4 whitespace-nowrap" ><a href="{{ route('addons.show', $addon->addon_id) }}" class="text-sm font-medium text-accent hover:text-accent-dark" >{{ $addon->addon_name }}</a></td><td class="px-6 py-4 whitespace-nowrap" >
                                <div class="text-sm text-secondary-text" >
                                    ₱{{ number_format($addon->addon_price, 2) }}
                                </div>
                            </td><td class="px-6 py-4" >
                                <div class="text-sm text-secondary-text truncate max-w-xs" >
                                    {{ $addon->description ?: 'No description' }}
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap text-center" ><span class="{{ $addon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 text-xs rounded-full" > {{ $addon->is_active ? 'Active' : 'Inactive' }} </span></td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" >
                                <div class="flex justify-end space-x-2" >
                                    <a href="{{ route('addons.show', $addon->addon_id) }}" class="text-accent hover:text-accent-dark" title="View"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></a><a href="{{ route('addons.edit', $addon->addon_id) }}" class="text-blue-600 hover:text-blue-800" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>
                                            <form action="{{ route('addons.toggle-status', $addon->
                                                addon_id) }}" method="POST" class="inline" > @csrf
                                                <button type="submit" class="{{ $addon->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}" title="{{ $addon->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($addon->is_active) <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    @endif
                                                </button>

                                            </form>

                                            <form action="{{ route('addons.destroy', $addon->
                                                addon_id) }}" method="POST" class="inline delete-form" > @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                                                </form>

                                            </div>
                                        </td></tr> @empty <tr><td colspan="3" class="px-6 py-4 text-center text-secondary-text" >No addons found</td></tr> @endforelse </tbody></table>
                                    </div>

                                </div>

                                <div class="mt-6" >
                                    <a href="{{ route('packages.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150 flex items-center inline-flex" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Packages </a>
                                    </div>

                                </div>

                                @push('scripts') <script> document.addEventListener('DOMContentLoaded', function() { // Close alert messages document.querySelectorAll('.close-alert').forEach(button => { button.addEventListener('click', function() { this.closest('div[role="alert"]').remove(); }); }); // Confirm deletion document.querySelectorAll('.delete-form').forEach(form => { form.addEventListener('submit', function(e) { e.preventDefault(); if (confirm('Are you sure you want to delete this addon? This action cannot be undone.')) { this.submit(); } }); }); }); </script>
                                @endpush
                            @endsection