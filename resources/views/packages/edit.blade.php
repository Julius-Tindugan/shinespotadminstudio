
@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto" >

        <div class="flex items-center mb-6" >
            <a href="{{ route('packages.index') }}" class="flex items-center text-accent hover:text-accent-dark transition-colors duration-200 mr-4" ><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Packages </a>
               

            </div>
<div>
 <h1 class="text-2xl font-bold text-primary-text my-4" >Edit Package</h1>
</div>
            <!-- Display Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <div class="font-bold">Please fix the following errors:</div>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @if($errors->has('category_id'))
                        <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded">
                            <div class="text-sm text-yellow-800">
                                <strong>Category Issue:</strong> 
                                @if($package->category && !$package->category->is_active)
                                    This package's current category "{{ $package->category->name }}" is inactive. 
                                    Please select an active category or contact an administrator to reactivate the current category.
                                @else
                                    Please select a valid category from the dropdown.
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="bg-card-bg rounded-lg shadow-sm p-6" >

                <form action="{{ route('packages.update-with-image', $package->package_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6" >
                        <label for="title" class="block text-sm font-medium text-primary-text mb-1" >Package Title <span class="text-red-500" >*</span></label>
                        <input type="text" id="title" name="title" value="{{ old('title', $package->
                            title) }}" required class="w-full px-4 py-2 border @error('title') border-red-500
                            @else
                            border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. Premium Wedding Package"> @error('title')
                            <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
                        @enderror
                    </div>

            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-primary-text mb-1">Category <span class="text-red-500">*</span></label>
                <select id="category_id" name="category_id" required 
                        class="w-full px-4 py-2 border @error('category_id') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" {{ old('category_id', $package->category_id) == $category->category_id ? 'selected' : '' }}>
                            {{ $category->name }}{{ !$category->is_active ? ' (Inactive)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($package->category && !$package->category->is_active)
                    <p class="mt-1 text-sm text-yellow-600">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Warning: This package's current category is inactive. Consider selecting an active category.
                    </p>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="price" class="block text-sm font-medium text-primary-text mb-1">Current Price (PHP) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">₱</span>
                        </div>
                        <input type="number" id="price" name="price" value="{{ old('price', $package->price) }}" required min="0" step="0.01" 
                               class="w-full pl-8 pr-4 py-2 border @error('price') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                               placeholder="0.00">
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-primary-text mb-1">
                        Duration
                    </label>
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $package->duration_hours_component ?? 0) }}" min="0" max="48" 
                                   class="w-full px-4 py-2 border @error('duration_hours') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                   placeholder="Hours">
                            <p class="mt-1 text-xs text-secondary-text">Hours (0-48)</p>
                        </div>
                        <div class="flex-1">
                            <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $package->duration_minutes_component ?? 0) }}" min="0" max="59" 
                                   class="w-full px-4 py-2 border @error('duration_minutes') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                   placeholder="Minutes">
                            <p class="mt-1 text-xs text-secondary-text">Minutes (0-59)</p>
                        </div>
                    </div>
                    @error('duration_hours')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="max_capacity" class="block text-sm font-medium text-primary-text mb-1">
                        Person Capacity
                    </label>
                    <input type="number" id="max_capacity" name="max_capacity" value="{{ old('max_capacity', $package->max_capacity) }}" min="1" max="1000" 
                           class="w-full px-4 py-2 border @error('max_capacity') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                           placeholder="e.g. 50">
                    <p class="mt-1 text-xs text-secondary-text">Maximum number of people</p>
                    @error('max_capacity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
            </div>

            <div class="mb-6" >
                <label for="description" class="block text-sm font-medium text-primary-text mb-1" >Description</label>
                <textarea id="description" name="description" rows="4" class="w-full px-4 py-2 border @error('description') border-red-500
                @else
                border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="Enter package description">{{ old('description', $package->description) }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Enhanced Features Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-primary-text mb-4">Enhanced Features</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div>
                    <label for="max_bookings_per_day" class="block text-sm font-medium text-primary-text mb-1">Max Bookings Per Day</label>
                    <input type="number" id="max_bookings_per_day" name="max_bookings_per_day" 
                           value="{{ old('max_bookings_per_day', $package->max_bookings_per_day) }}" 
                           min="1" max="100" 
                           class="w-full px-4 py-2 border @error('max_bookings_per_day') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                           placeholder="e.g. 3">
                    @error('max_bookings_per_day')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input id="is_featured" name="is_featured" type="checkbox" 
                               {{ old('is_featured', $package->is_featured) ? 'checked' : '' }}
                               @if(!$package->is_featured && App\Models\Package::getFeaturedCount() >= App\Models\Package::getMaxFeaturedLimit()) disabled @endif
                               class="focus:ring-accent h-4 w-4 text-accent border-border-color rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_featured" class="font-medium text-primary-text">Featured Package</label>
                        <p class="text-secondary-text">Show prominently in listings</p>
                        @php
                            $featuredCount = App\Models\Package::getFeaturedCount();
                            $maxLimit = App\Models\Package::getMaxFeaturedLimit();
                        @endphp
                        <div class="mt-1 text-xs">
                            <span class="text-secondary-text">
                                {{ $featuredCount }}/{{ $maxLimit }} featured packages used
                            </span>
                            @if(!$package->is_featured && $featuredCount >= $maxLimit)
                                <span class="text-red-600 block">
                                    Maximum reached. Unfeature another package first.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <div class="flex items-center h-5">
                        <input id="is_active" name="is_active" type="checkbox" 
                               {{ old('is_active', $package->is_active) ? 'checked' : '' }}
                               class="focus:ring-accent h-4 w-4 text-accent border-border-color rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-primary-text">Active Package</label>
                        <p class="text-secondary-text">Available for booking</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6" >
            <label for="image" class="block text-sm font-medium text-primary-text mb-1" >Package Image</label>
            @if($package->hasImage())
                <div class="mb-4 flex items-center" id="current-image-section">

                    <div class="w-40 h-40 rounded-md overflow-hidden border border-border-color mr-4" >
                        @if($package->image_data)
                            <img src="{{ $package->image_url }}" alt="{{ $package->title }}" class="w-full h-full object-cover" id="current-image">
                        @endif
                    </div>

                    <div class="flex flex-col space-y-2">

                        <p class="text-sm text-secondary-text" >Current image</p>
                        <button type="button" id="remove-image-btn" class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded-md transition-colors duration-150 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Remove Image
                        </button>
                        <p class="text-xs text-secondary-text mt-1" >Or upload a new image to replace it</p>

                    </div>

                </div>
                <input type="hidden" id="remove_image" name="remove_image" value="0">

            @endif

            <input type="file" id="image" name="image" class="w-full px-4 py-2 border @error('image') border-red-500
            @else
            border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" accept="image/*">

            <p class="mt-1 text-xs text-secondary-text" >Accepted formats: JPG, PNG, GIF, WEBP. Max size: 5MB</p>
            @error('image')
            <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
        @enderror
        <div id="image-preview" class="mt-3 hidden" >

        </div>

    </div>

    <div class="mb-6" >
        <label class="block text-sm font-medium text-primary-text mb-2" >Package Inclusions</label>
        <div id="inclusions-container">
            @forelse($package->inclusions as $inclusion)
            <div class="inclusion-row flex items-center mb-2" >

                <input type="text" name="inclusions[]" value="{{ $inclusion->
                    inclusion_text }}" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 8 Hours Photo Coverage">
                    <button type="button" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700" ><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                    </div>
                    @empty
                    <div class="inclusion-row flex items-center mb-2" >

                        <input type="text" name="inclusions[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 8 Hours Photo Coverage">

                            <button type="button" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700 disabled:opacity-50 disabled:cursor-not-allowed" ><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                            </div>
                        @endforelse
                    </div>

                    <button type="button" id="add-inclusion-btn" class="mt-2 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 rounded-md transition-colors duration-150 flex items-center text-sm" ><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add Inclusion </button>
                        @error('inclusions.*')
                        <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6" >
                    <label class="block text-sm font-medium text-primary-text mb-2" >Package Free Items</label>
                    <div id="free-items-container">
                        @forelse($package->freeItems as $freeItem)
                        <div class="free-item-row flex items-center mb-2" >

                            <input type="text" name="free_items[]" value="{{ $freeItem->
                                free_item_text }}" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 1 Free 16x20 Photo Frame">
                                <button type="button" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700" ><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                                </div>
                                @empty
                                <div class="free-item-row flex items-center mb-2" >

                                    <input type="text" name="free_items[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 1 Free 16x20 Photo Frame">

                                        <button type="button" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700 disabled:opacity-50 disabled:cursor-not-allowed" ><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>

                                        </div>
                                    @endforelse
                                </div>

                                <button type="button" id="add-free-item-btn" class="mt-2 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 rounded-md transition-colors duration-150 flex items-center text-sm" ><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add Free Item </button>
                                    @error('free_items.*')
                                    <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6" >
                                <label class="block text-sm font-medium text-primary-text mb-2" >Available Addons</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" >
                                    @php $packageAddonIds = $package->addons->pluck('addon_id')->toArray(); @endphp
                                    @foreach($addons as $addon)
                                        <div class="flex items-center p-3 border rounded-md hover:bg-gray-50" >

                                            <input type="checkbox" name="addons[]" id="addon_{{ $addon->
                                                addon_id }}" value="{{ $addon->addon_id }}" {{ in_array($addon->addon_id, $packageAddonIds) ? 'checked' : '' }} class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded" ><label for="addon_{{ $addon->addon_id }}" class="ml-2 flex flex-col cursor-pointer" ><span class="text-sm font-medium text-primary-text" >{{ $addon->addon_name }}</span><span class="text-xs text-secondary-text" >₱{{ number_format($addon->addon_price, 2) }}</span></label>
                                            </div>

                                        @endforeach

                                    </div>

                                    @if($addons->isEmpty())
                                        <p class="text-sm text-secondary-text italic mt-2" >No addons available. <a href="{{ route('addons.create') }}" class="text-accent hover:underline" >Create addons first</a>.</p>

                                    @endif

                                </div>

                                <div class="flex items-center mb-6" >

                                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $package->
                                        is_active) ? 'checked' : '' }} class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded" ><label for="is_active" class="ml-2 block text-sm text-primary-text" >Active</label>
                                    </div>

                                    <div class="flex justify-end space-x-3" >
                                        <a href="{{ route('packages.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150" > Cancel </a>
                                        <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-md transition-colors duration-150" > Update Package </button>

                                    </div>

                                </form>

                            </div>

                        </div>

                        @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inclusions dynamic rows
    const inclusionsContainer = document.getElementById('inclusions-container');
    const addInclusionBtn = document.getElementById('add-inclusion-btn');
    
    addInclusionBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'inclusion-row flex items-center mb-2';
        newRow.innerHTML = `
            <input type="text" name="inclusions[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 8 Hours Photo Coverage" required>
            <button type="button" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        inclusionsContainer.appendChild(newRow);
        updateInclusionRemoveButtons();
    });
    
    // Remove inclusion button event
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-inclusion-btn')) {
            const btn = e.target.closest('.remove-inclusion-btn');
            const row = btn.closest('.inclusion-row');
            row.remove();
            updateInclusionRemoveButtons();
        }
    });
    
    // Free Items dynamic rows
    const freeItemsContainer = document.getElementById('free-items-container');
    const addFreeItemBtn = document.getElementById('add-free-item-btn');
    
    addFreeItemBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'free-item-row flex items-center mb-2';
        newRow.innerHTML = `
            <input type="text" name="free_items[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 1 Free 16x20 Photo Frame" required>
            <button type="button" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        freeItemsContainer.appendChild(newRow);
        updateFreeItemRemoveButtons();
    });
    
    // Remove free item button event
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-free-item-btn')) {
            const btn = e.target.closest('.remove-free-item-btn');
            const row = btn.closest('.free-item-row');
            row.remove();
            updateFreeItemRemoveButtons();
        }
    });
    
    // Function to update inclusion remove buttons (disable if only one row)
    function updateInclusionRemoveButtons() {
        const rows = document.querySelectorAll('.inclusion-row');
        if (rows.length === 1) {
            rows[0].querySelector('.remove-inclusion-btn').disabled = true;
            rows[0].querySelector('.remove-inclusion-btn').classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            rows.forEach(row => {
                const btn = row.querySelector('.remove-inclusion-btn');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    }
    
    // Function to update free item remove buttons (disable if only one row)
    function updateFreeItemRemoveButtons() {
        const rows = document.querySelectorAll('.free-item-row');
        if (rows.length === 1) {
            rows[0].querySelector('.remove-free-item-btn').disabled = true;
            rows[0].querySelector('.remove-free-item-btn').classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            rows.forEach(row => {
                const btn = row.querySelector('.remove-free-item-btn');
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }
    }
    
    // Initialize button states
    updateInclusionRemoveButtons();
    updateFreeItemRemoveButtons();
    
    // Utility function to show UI alerts
    function showAlert(message, type = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg ${
            type === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
            type === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' :
            'bg-blue-100 border border-blue-400 text-blue-700'
        } flex items-center justify-between max-w-md`;
        
        alertDiv.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'error' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
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
    
    // Confirmation modal for image removal
    function showImageRemoveConfirmation(callback) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
        modal.innerHTML = `
            <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Remove Image</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to remove this image? This action cannot be undone.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button id="confirmRemove" class="px-4 py-2 bg-yellow-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            Remove
                        </button>
                        <button id="cancelRemove" class="mt-3 px-4 py-2 bg-white text-gray-700 text-base font-medium rounded-md w-full shadow-sm border border-gray-300 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('#confirmRemove').addEventListener('click', () => {
            modal.remove();
            callback();
        });
        
        modal.querySelector('#cancelRemove').addEventListener('click', () => {
            modal.remove();
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
    
    // Handle remove image button
    const removeImageBtn = document.getElementById('remove-image-btn');
    const currentImageSection = document.getElementById('current-image-section');
    const removeImageInput = document.getElementById('remove_image');
    const imageInput = document.getElementById('image');
    
    if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
            showImageRemoveConfirmation(() => {
                // Hide current image section
                if (currentImageSection) {
                    currentImageSection.style.display = 'none';
                }
                
                // Set remove flag
                if (removeImageInput) {
                    removeImageInput.value = '1';
                }
                
                // Clear file input if any file was selected
                if (imageInput) {
                    imageInput.value = '';
                }
                
                // Hide preview if visible and clear replacement confirmation
                const imagePreview = document.getElementById('image-preview');
                if (imagePreview) {
                    imagePreview.classList.add('hidden');
                }
                
                // Remove any existing confirmation messages
                const replacementDiv = document.getElementById('image-replacement-confirmation');
                if (replacementDiv) {
                    replacementDiv.remove();
                }
                
                const existingConfirmation = document.getElementById('image-remove-confirmation');
                if (existingConfirmation) {
                    existingConfirmation.remove();
                }
                
                // Show confirmation message with cancel option
                const confirmationDiv = document.createElement('div');
                confirmationDiv.className = 'mb-2 p-2 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800 flex items-center justify-between';
                confirmationDiv.innerHTML = `
                    <span>⚠️ Image will be removed when you save the package.</span>
                    <button type="button" id="cancel-remove-btn" class="ml-2 px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded">
                        Cancel Remove
                    </button>
                `;
                confirmationDiv.id = 'image-remove-confirmation';
                
                // Insert confirmation before file input
                imageInput.parentNode.insertBefore(confirmationDiv, imageInput);
                
                // Add event listener to cancel button
                const cancelBtn = document.getElementById('cancel-remove-btn');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', function() {
                        // Reset remove flag
                        if (removeImageInput) {
                            removeImageInput.value = '0';
                        }
                        
                        // Show current image section again
                        if (currentImageSection) {
                            currentImageSection.style.display = 'flex';
                        }
                        
                        // Remove confirmation message
                        confirmationDiv.remove();
                    });
                }
            });
        });
    }
    
    // Preview new image
    const imagePreview = document.getElementById('image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // When a new image is selected, we should replace the current image
                // Don't reset the remove flag - let the backend handle prioritization
                
                // Remove confirmation message if it exists
                const confirmationDiv = document.getElementById('image-remove-confirmation');
                if (confirmationDiv) {
                    confirmationDiv.remove();
                }
                
                // Update the confirmation message to indicate replacement
                const replacementDiv = document.createElement('div');
                replacementDiv.className = 'mb-2 p-2 bg-blue-100 border border-blue-300 rounded text-sm text-blue-800';
                replacementDiv.innerHTML = '📷 New image selected. Current image will be replaced when you save.';
                replacementDiv.id = 'image-replacement-confirmation';
                
                // Insert confirmation before file input
                imageInput.parentNode.insertBefore(replacementDiv, imageInput);
                
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showAlert('File size must be less than 5MB', 'error');
                    this.value = '';
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showAlert('Please select a valid image file (JPEG, PNG, JPG, GIF, WEBP)', 'error');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `
                        <div class="relative w-40 h-40 rounded-md overflow-hidden border border-border-color">
                            <img src="${e.target.result}" class="w-full h-full object-cover" alt="New image preview">
                            <button type="button" onclick="removeImagePreview()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                ×
                            </button>
                            <p class="text-xs text-secondary-text mt-1">New image preview</p>
                        </div>
                    `;
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                imagePreview.classList.add('hidden');
                // Remove replacement confirmation if file is cleared
                const replacementDiv = document.getElementById('image-replacement-confirmation');
                if (replacementDiv) {
                    replacementDiv.remove();
                }
            }
        });
    }
});

// Global function to remove image preview
function removeImagePreview() {
    const preview = document.getElementById('image-preview');
    const imageInput = document.getElementById('image');
    const replacementDiv = document.getElementById('image-replacement-confirmation');
    
    if (preview) preview.classList.add('hidden');
    if (imageInput) imageInput.value = '';
    if (replacementDiv) replacementDiv.remove();
    
    // If we had clicked remove image before, restore that state
    const removeImageInput = document.getElementById('remove_image');
    const currentImageSection = document.getElementById('current-image-section');
    
    if (removeImageInput && removeImageInput.value === '1') {
        // Restore the remove confirmation message with cancel option
        const confirmationDiv = document.createElement('div');
        confirmationDiv.className = 'mb-2 p-2 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800 flex items-center justify-between';
        confirmationDiv.innerHTML = `
            <span>⚠️ Image will be removed when you save the package.</span>
            <button type="button" id="cancel-remove-btn" class="ml-2 px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded">
                Cancel Remove
            </button>
        `;
        confirmationDiv.id = 'image-remove-confirmation';
        
        // Insert confirmation before file input
        if (imageInput) {
            imageInput.parentNode.insertBefore(confirmationDiv, imageInput);
        }
        
        // Add event listener to cancel button
        const cancelBtn = document.getElementById('cancel-remove-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                // Reset remove flag
                if (removeImageInput) {
                    removeImageInput.value = '0';
                }
                
                // Show current image section again
                if (currentImageSection) {
                    currentImageSection.style.display = 'flex';
                }
                
                // Remove confirmation message
                confirmationDiv.remove();
            });
        }
        
        // Keep current image hidden if it was marked for removal
        if (currentImageSection) {
            currentImageSection.style.display = 'none';
        }
    }
}

// Global function to toggle day selection
function toggleDaySelection(checkbox) {
    const label = checkbox.closest('.day-checkbox');
    if (checkbox.checked) {
        // Selected state: accent color background
        label.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
        label.classList.add('bg-accent', 'text-white', 'border-accent');
    } else {
        // Unselected state: white background
        label.classList.remove('bg-accent', 'text-white', 'border-accent');
        label.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
    }
}

// Initialize day selection styling on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.day-input').forEach(function(checkbox) {
        toggleDaySelection(checkbox);
    });
});
</script>
@endpush
                                        @endsection