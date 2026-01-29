
@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto" >

        <div class="flex items-center mb-6" >
            <a href="{{ route('packages.index') }}" class="flex items-center text-accent hover:text-accent-dark transition-colors duration-200 mr-4" ><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Packages </a>
                
           
            </div>
 <div><h1 class="text-2xl font-bold text-primary-text" >Package Details</h1></div>
                
            <div class="bg-card-bg rounded-lg shadow-sm overflow-hidden" >

                <div class="p-6" >

                    <div class="flex flex-col md:flex-row" >

        @if($package->hasImage())
            <div class="md:w-1/3 mb-6 md:mb-0 md:mr-6" >

                <div class="rounded-md overflow-hidden border border-border-color" >
                    @if($package->image_data)
                        <img src="{{ $package->image_url }}" alt="{{ $package->title }}" class="w-full h-auto" >
                    @endif
                </div>

            </div>

        @endif                        <div class="md:flex-1" >

                            <div class="flex justify-between items-start" >

                                <h2 class="text-xl font-bold text-primary-text" >{{ $package->title }}</h2>
                                <span class="{{ $package->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 text-xs font-semibold rounded-full" > {{ $package->is_active ? 'Active' : 'Inactive' }} </span>
                            </div>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4" >

                                <div>

                                    <h3 class="text-sm font-medium text-secondary-text" >Package Type</h3>

                                    <p class="mt-1 text-primary-text" >{{ $package->package_type }}</p>

                                </div>

                                <div>

                                    <h3 class="text-sm font-medium text-secondary-text" >Price</h3>

                                    <p class="mt-1 text-primary-text" >₱{{ number_format($package->price, 2) }}</p>

                                </div>

                                @if($package->duration_hours)
                                <div>
                                    <h3 class="text-sm font-medium text-secondary-text" >Duration</h3>
                                    <p class="mt-1 text-primary-text" >{{ $package->formatted_duration }}</p>
                                </div>
                                @endif

                                @if($package->max_capacity)
                                <div>
                                    <h3 class="text-sm font-medium text-secondary-text" >Capacity</h3>
                                    <p class="mt-1 text-primary-text" >{{ $package->max_capacity }} {{ $package->max_capacity > 1 ? 'people' : 'person' }}</p>
                                </div>
                                @endif

                            </div>

                            @if($package->description)
                                <div class="mt-4" >

                                    <h3 class="text-sm font-medium text-secondary-text" >Description</h3>

                                    <p class="mt-1 text-primary-text" >{{ $package->description }}</p>

                                </div>

                            @endif

                        </div>

                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6" >

                        <div>

                            <h3 class="text-sm font-medium text-secondary-text mb-2" >Package Inclusions</h3>

                            @if($package->inclusions->count() > 0) <ul class="list-disc pl-5 space-y-1" >
                                @foreach($package->inclusions as $inclusion) <li class="text-primary-text" >{{ $inclusion->inclusion_text }}</li>
                                @endforeach
                            </ul>
                            @else

                            <p class="text-secondary-text italic" >No inclusions specified</p>

                        @endif

                    </div>

                    <div>

                        <h3 class="text-sm font-medium text-secondary-text mb-2" >Free Items</h3>

                        @if($package->freeItems->count() > 0) <ul class="list-disc pl-5 space-y-1" >
                            @foreach($package->freeItems as $freeItem) <li class="text-primary-text" >{{ $freeItem->free_item_text }}</li>
                            @endforeach
                        </ul>
                        @else

                        <p class="text-secondary-text italic" >No free items specified</p>

                    @endif

                </div>

                <div>

                    <h3 class="text-sm font-medium text-secondary-text mb-2" >Available Addons</h3>

                    @if($package->addons->count() > 0) <ul class="list-disc pl-5 space-y-1" >
                        @foreach($package->addons as $addon) <li class="text-primary-text" > {{ $addon->addon_name }} <span class="text-xs text-secondary-text ml-1" >(₱{{ number_format($addon->addon_price, 2) }})</span></li>
                        @endforeach
                    </ul>
                    @else

                    <p class="text-secondary-text italic" >No addons available for this package</p>

                @endif

            </div>

        </div>

        <div class="mt-6 border-t border-border-color pt-4" >

            <div class="text-sm text-secondary-text" >

                <div>
                    Created: {{ $package->created_at->format('F d, Y \a\t h:i A') }}
                </div>

                <div>
                    Last Updated: {{ $package->updated_at->format('F d, Y \a\t h:i A') }}
                </div>

            </div>

        </div>

        <div class="mt-6 flex justify-end space-x-3" >
            <a href="{{ route('packages.edit', $package->package_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors duration-150" > Edit Package </a>
            <form action="{{ route('packages.destroy', $package->
                package_id) }}" method="POST" class="inline delete-form" > @csrf @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-150" > Delete Package </button>

            </form>

        </div>

    </div>

</div>

</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
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
    // Confirm deletion with modal
    let deleteFormToSubmit = null;
    
    const deleteForm = document.querySelector('.delete-form');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) { 
            e.preventDefault(); 
            deleteFormToSubmit = this;
            showDeleteModal();
        }); 
    }
    
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