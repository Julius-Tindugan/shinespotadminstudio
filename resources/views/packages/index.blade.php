
@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto" >

        <div class="flex justify-between items-center mb-6" >

            <h1 class="text-2xl font-bold text-primary-text" >Package Management</h1>
            <a href="{{ route('packages.create') }}" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-md transition-colors duration-150 flex items-center" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add New Package </a>
        </div>

        <!-- Featured Packages Summary -->
        @php
            $featuredCount = App\Models\Package::getFeaturedCount();
            $maxLimit = App\Models\Package::getMaxFeaturedLimit();
            $featuredPackages = App\Models\Package::featured()->active()->get();
        @endphp
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-blue-900">Featured Packages</h3>
                    <p class="text-sm text-blue-700">
                        {{ $featuredCount }}/{{ $maxLimit }} featured packages used
                        @if($featuredCount >= $maxLimit)
                            <span class="text-red-600 font-medium">(Maximum reached)</span>
                        @endif
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-bold text-blue-900">{{ $featuredCount }}</span>
                    <span class="text-sm text-blue-700">/{{ $maxLimit }}</span>
                </div>
            </div>
            @if($featuredPackages->count() > 0)
                <div class="mt-3">
                    <p class="text-sm text-blue-700 mb-2">Currently featured:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($featuredPackages as $featured)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $featured->title }}
                                <form action="{{ route('packages.toggle-featured', $featured->package_id) }}" method="POST" class="inline ml-1">
                                    @csrf
                                    <button type="submit" class="ml-1 text-blue-600 hover:text-blue-800" title="Remove from featured">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </form>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
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
                    <!-- Package Category Filter -->
                        <div class="mb-6 flex items-center space-x-4" >
                            <div>
                                <label for="category-filter" class="text-sm font-medium text-secondary-text mr-3" >Filter by Category:</label>
                                <select id="category-filter" class="bg-white border border-border-color rounded-md px-3 py-2 focus:outline-none focus:ring-accent focus:border-accent" >
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status-filter" class="text-sm font-medium text-secondary-text mr-3" >Filter by Status:</label>
                                <select id="status-filter" class="bg-white border border-border-color rounded-md px-3 py-2 focus:outline-none focus:ring-accent focus:border-accent" >
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <button id="reset-filters" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150">
                                    Reset Filters
                                </button>
                            </div>
                        </div>

                        <div class="bg-card-bg rounded-lg shadow-sm overflow-hidden" >

                            <div class="overflow-x-auto" >
                                <table class="min-w-full divide-y divide-border-color" ><thead class="bg-gray-50" ><tr><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Title</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Category</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Price</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Duration</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Capacity</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Status</th><th scope="col" class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider" >Featured</th><th scope="col" class="px-6 py-3 text-right text-xs font-medium text-secondary-text uppercase tracking-wider" >Actions</th></tr></thead><tbody class="bg-white divide-y divide-border-color" id="packages-table-body"> @forelse($packages as $package) <tr class="package-row" data-category="{{ $package->category_id }}" data-status="{{ $package->is_active ? 'active' : 'inactive' }}"><td class="px-6 py-4 whitespace-nowrap" ><a href="{{ route('packages.show', $package->package_id) }}" class="text-sm font-medium text-accent hover:text-accent-dark" >{{ $package->title }}</a>
                                <div class="text-xs text-gray-500 truncate max-w-xs" >
                                    {{ Str::limit($package->description, 50) }}
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                <div class="text-sm text-secondary-text" >
                                    @if($package->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $package->category->color_code }}20; color: {{ $package->category->color_code }};">
                                            {{ $package->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">No Category</span>
                                    @endif
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                <div class="text-sm text-secondary-text" >
                                    ₱{{ number_format($package->price, 2) }}
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                <div class="text-sm text-secondary-text" >
                                    @if($package->duration_hours)
                                        {{ $package->formatted_duration }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                <div class="text-sm text-secondary-text" >
                                    @if($package->max_capacity)
                                        {{ $package->max_capacity }} {{ $package->max_capacity > 1 ? 'ppl' : 'person' }}
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </div>
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                @if($package->is_active) <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800" >Active</span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" >Inactive</span>
                                @endif
                            </td><td class="px-6 py-4 whitespace-nowrap" >
                                @if($package->is_featured) 
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800" >Featured</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" >Standard</span>
                                @endif
                            </td><td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" >
                                <div class="flex justify-end space-x-2" >
                                    <a href="{{ route('packages.show', $package->package_id) }}" class="text-accent hover:text-accent-dark" title="View"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></a><a href="{{ route('packages.edit', $package->package_id) }}" class="text-blue-600 hover:text-blue-800" title="Edit"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></a>
                                            
                                            <form action="{{ route('packages.toggle-featured', $package->package_id) }}" method="POST" class="inline" title="{{ $package->is_featured ? 'Remove from Featured' : 'Make Featured' }}"> 
                                                @csrf
                                                <button type="submit" 
                                                        class="{{ $package->is_featured ? 'text-yellow-600 hover:text-yellow-800' : 'text-gray-600 hover:text-gray-800' }}"
                                                        @if(!$package->is_featured && App\Models\Package::getFeaturedCount() >= App\Models\Package::getMaxFeaturedLimit()) disabled title="Cannot feature: Maximum limit reached" @endif>
                                                    @if($package->is_featured) 
                                                        <svg class="w-5 h-5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                    @endif
                                                </button>
                                            </form>

                                            <form action="{{ route('packages.toggle-status', $package->
                                                package_id) }}" method="POST" class="inline" > @csrf
                                                <button type="submit" class="{{ $package->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }}" title="{{ $package->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($package->is_active) <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    @endif
                                                </button>

                                            </form>

                                            <form action="{{ route('packages.destroy', $package->
                                                package_id) }}" method="POST" class="inline delete-form" > @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                                                </form>

                                            </div>
                                        </td></tr> @empty <tr><td colspan="8" class="px-6 py-4 text-center text-secondary-text" >No packages found</td></tr> @endforelse </tbody></table>
                                    </div>

                                </div>

                                <!-- Pagination Links -->
                                @if($packages->hasPages())
                                <div class="mt-6">
                                    {{ $packages->links() }}
                                </div>
                                @endif

                                <div class="mt-6 flex justify-between items-center" >
                                    <a href="{{ route('addons.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150 flex items-center" ><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Manage Addons </a>
                                    </div>

                                </div>

                                </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Delete Package</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to delete this package? This action cannot be undone.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                    <button id="cancelDelete" class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

                                @push('scripts') <script> document.addEventListener('DOMContentLoaded', function() { 
    // Get filter elements and rows
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const packageRows = document.querySelectorAll('.package-row');
    
    // Function to filter rows based on current filter values
    function filterRows() {
        const selectedCategory = categoryFilter.value;
        const selectedStatus = statusFilter.value;
        
        packageRows.forEach(row => {
            const rowCategory = row.getAttribute('data-category');
            const rowStatus = row.getAttribute('data-status');
            
            let showRow = true;
            
            // Check category filter
            if (selectedCategory && rowCategory !== selectedCategory) {
                showRow = false;
            }
            
            // Check status filter
            if (selectedStatus && rowStatus !== selectedStatus) {
                showRow = false;
            }
            
            // Show or hide row
            if (showRow) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }
    
    // Add event listeners for filters
    categoryFilter.addEventListener('change', filterRows);
    statusFilter.addEventListener('change', filterRows);
    
    // Reset filters button
    const resetFiltersBtn = document.getElementById('reset-filters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            categoryFilter.value = '';
            statusFilter.value = '';
            filterRows();
        });
    }
    
    // Close alert messages
    document.querySelectorAll('.close-alert').forEach(button => { 
        button.addEventListener('click', function() { 
            this.closest('div[role="alert"]').remove(); 
        }); 
    }); 
    
    // Confirm deletion with modal
    let deleteFormToSubmit = null;
    
    document.querySelectorAll('.delete-form').forEach(form => { 
        form.addEventListener('submit', function(e) { 
            e.preventDefault(); 
            deleteFormToSubmit = this;
            showDeleteModal();
        }); 
    }); 
    
    function showDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }
    
    function hideDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        deleteFormToSubmit = null;
    }
    
    // Confirm delete button
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function() {
            if (deleteFormToSubmit) {
                deleteFormToSubmit.submit();
            }
        });
    }
    
    // Cancel delete button
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', hideDeleteModal);
    }
    
    // Close modal when clicking outside
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    }
}); </script>
                                @endpush
                            @endsection