
@extends('layouts.app')
@section('content')
    <div class="max-w-6xl mx-auto">

        <div class="flex items-center mb-6">
            <a href="{{ route('packages.index') }}" class="flex items-center text-accent hover:text-accent-dark transition-colors duration-200 mr-4">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg> 
                Back to Packages 
            </a>
        </div>
        
        <div>
            <h1 class="text-2xl font-bold text-primary-text">Create New Package</h1>
            <p class="mt-1 text-sm text-secondary-text">Create a comprehensive package with inclusions, addons, and pricing details.</p>
        </div>
        
        <div class="bg-card-bg rounded-lg shadow-sm p-6 mt-6">

            <form action="{{ route('packages.store') }}" method="POST" enctype="multipart/form-data" x-data="packageForm()">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Basic Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-primary-text mb-1">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category_id" name="category_id" required 
                                    class="w-full px-4 py-2 border @error('category_id') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-primary-text mb-1">
                                Package Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required 
                                   class="w-full px-4 py-2 border @error('title') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                   placeholder="e.g. Premium Wedding Package">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>


                </div>

                <!-- Pricing Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        Pricing & Details
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="price" class="block text-sm font-medium text-primary-text mb-1">
                                Price (PHP) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">₱</span>
                                </div>
                                <input type="number" id="price" name="price" value="{{ old('price') }}" required min="0" step="0.01" 
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
                                    <input type="number" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', 0) }}" min="0" max="48" 
                                           class="w-full px-4 py-2 border @error('duration_hours') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                           placeholder="Hours">
                                    <p class="mt-1 text-xs text-secondary-text">Hours (0-48)</p>
                                </div>
                                <div class="flex-1">
                                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 0) }}" min="0" max="59" 
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
                            <input type="number" id="max_capacity" name="max_capacity" value="{{ old('max_capacity') }}" min="1" max="1000" 
                                   class="w-full px-4 py-2 border @error('max_capacity') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                   placeholder="e.g. 50">
                            <p class="mt-1 text-xs text-secondary-text">Maximum number of people</p>
                            @error('max_capacity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                        <div>
                            <label for="max_bookings_per_day" class="block text-sm font-medium text-primary-text mb-1">Max Bookings Per Day</label>
                            <input type="number" id="max_bookings_per_day" name="max_bookings_per_day" value="{{ old('max_bookings_per_day') }}" min="1" max="100" 
                                   class="w-full px-4 py-2 border @error('max_bookings_per_day') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                   placeholder="e.g. 3">
                            <p class="mt-1 text-xs text-secondary-text">Daily booking limit</p>
                            @error('max_bookings_per_day')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>



                <!-- Description Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Description & Details
                    </h3>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-primary-text mb-1">Full Description</label>
                        <textarea id="description" name="description" rows="6" 
                                  class="w-full px-4 py-2 border @error('description') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                  placeholder="Enter detailed package description, what's included, terms and conditions, etc.">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Image Upload Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Package Image
                    </h3>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-primary-text mb-1">Package Image</label>
                        <input type="file" id="image" name="image" 
                               class="w-full px-4 py-2 border @error('image') border-red-500 @else border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                               accept="image/*">
                        <p class="mt-1 text-xs text-secondary-text">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 5MB</p>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Inclusions Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Package Inclusions
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">What's Included</label>
                        <div id="inclusions-container">
                            <div class="inclusion-row flex items-center mb-2">
                                <input type="text" name="inclusions[]" 
                                       class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                       placeholder="e.g. 8 Hours Photo Coverage">
                                <button type="button" onclick="removeInclusionRow(this)" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="addInclusion()" 
                                class="mt-2 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 rounded-md transition-colors duration-150 flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg> 
                            Add Inclusion
                        </button>
                        @error('inclusions.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Free Items Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                        </svg>
                        Free Items & Bonuses
                    </h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-primary-text mb-2">Complimentary Items</label>
                        <div id="free-items-container">
                            <div class="free-item-row flex items-center mb-2">
                                <input type="text" name="free_items[]" 
                                       class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" 
                                       placeholder="e.g. 1 Free 16x20 Photo Frame">
                                <button type="button" onclick="removeFreeItemRow(this)" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="addFreeItem()" 
                                class="mt-2 px-4 py-2 bg-gray-100 text-gray-800 hover:bg-gray-200 rounded-md transition-colors duration-150 flex items-center text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Free Item
                        </button>
                        @error('free_items.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Add-ons Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Available Add-ons
                    </h3>
                    
                    @if($addons->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @foreach($addons as $addon)
                                <div class="flex items-center p-3 border rounded-md hover:bg-gray-50">
                                    <input type="checkbox" name="addons[]" id="addon_{{ $addon->addon_id }}" 
                                           value="{{ $addon->addon_id }}" 
                                           class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded">
                                    <label for="addon_{{ $addon->addon_id }}" class="ml-2 flex flex-col cursor-pointer flex-1">
                                        <span class="text-sm font-medium text-primary-text">{{ $addon->addon_name }}</span>
                                        <span class="text-xs text-secondary-text">₱{{ number_format($addon->addon_price, 2) }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-secondary-text italic">
                            No add-ons available. 
                            <a href="{{ route('addons.create') }}" class="text-accent hover:underline">Create add-ons first</a>.
                        </p>
                    @endif
                </div>

                <!-- Settings Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-primary-text mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Package Settings
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_active" name="is_active" value="1" 
                                   {{ old('is_active', 1) ? 'checked' : '' }}
                                   class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded">
                            <label for="is_active" class="ml-2 block text-sm text-primary-text">Active Package</label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1" 
                                   {{ old('is_featured', 0) ? 'checked' : '' }}
                                   @if(App\Models\Package::getFeaturedCount() >= App\Models\Package::getMaxFeaturedLimit()) disabled @endif
                                   class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded">
                            <label for="is_featured" class="ml-2 block text-sm text-primary-text">Featured Package</label>
                            <span class="ml-2 text-xs text-secondary-text">(Highlighted in listings)</span>
                        </div>
                        @php
                            $featuredCount = App\Models\Package::getFeaturedCount();
                            $maxLimit = App\Models\Package::getMaxFeaturedLimit();
                        @endphp
                        <div class="ml-6 text-xs">
                            <span class="text-secondary-text">
                                {{ $featuredCount }}/{{ $maxLimit }} featured packages used
                            </span>
                            @if($featuredCount >= $maxLimit)
                                <span class="text-red-600 block mt-1">
                                    Maximum featured packages reached. Unfeature another package first.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <a href="{{ route('packages.index') }}" 
                       class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-accent hover:bg-accent-dark text-white rounded-md transition-colors duration-150 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Package
                    </button>
                </div>

            </form>
        </div>
    </div>

@push('scripts')
<script>
function packageForm() {
    return {
        inclusions: [''],
        freeItems: [''],
        
        addInclusion() {
            console.log('Adding inclusion - AlpineJS method called');
            const container = document.getElementById('inclusions-container');
            if (container) {
                const newRow = document.createElement('div');
                newRow.className = 'inclusion-row flex items-center mb-2';
                newRow.innerHTML = `
                    <input type="text" name="inclusions[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 8 Hours Photo Coverage">
                    <button type="button" onclick="removeInclusionRow(this)" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(newRow);
                // Call global function instead of this context
                if (typeof updateInclusionRemoveButtons === 'function') {
                    updateInclusionRemoveButtons();
                }
                console.log('New inclusion row added successfully');
            } else {
                console.error('Inclusions container not found');
            }
        },
        
        addFreeItem() {
            console.log('Adding free item - AlpineJS method called');
            const container = document.getElementById('free-items-container');
            if (container) {
                const newRow = document.createElement('div');
                newRow.className = 'free-item-row flex items-center mb-2';
                newRow.innerHTML = `
                    <input type="text" name="free_items[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 1 Free 16x20 Photo Frame">
                    <button type="button" onclick="removeFreeItemRow(this)" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(newRow);
                // Call global function instead of this context
                if (typeof updateFreeItemRemoveButtons === 'function') {
                    updateFreeItemRemoveButtons();
                }
                console.log('New free item row added successfully');
            } else {
                console.error('Free items container not found');
            }
        },
        
        updateInclusionRemoveButtons() {
            // This will be handled by the global function
            if (typeof updateInclusionRemoveButtons === 'function') {
                updateInclusionRemoveButtons();
            }
        },
        
        updateFreeItemRemoveButtons() {
            // This will be handled by the global function  
            if (typeof updateFreeItemRemoveButtons === 'function') {
                updateFreeItemRemoveButtons();
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Starting initialization');
    
    // Initialize day selection styling
    document.querySelectorAll('.day-input').forEach(function(checkbox) {
        toggleDaySelection(checkbox);
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
    
    // Add backup event listeners in case AlpineJS doesn't work
    const addInclusionBtn = document.querySelector('button[onclick*="addInclusion"], button[\\@click*="addInclusion"]');
    const addFreeItemBtn = document.querySelector('button[onclick*="addFreeItem"], button[\\@click*="addFreeItem"]');
    
    if (addInclusionBtn) {
        addInclusionBtn.addEventListener('click', function(e) {
            console.log('Backup inclusion button clicked');
            e.preventDefault();
            
            const container = document.getElementById('inclusions-container');
            if (container) {
                const newRow = document.createElement('div');
                newRow.className = 'inclusion-row flex items-center mb-2';
                newRow.innerHTML = `
                    <input type="text" name="inclusions[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 8 Hours Photo Coverage">
                    <button type="button" onclick="removeInclusionRow(this)" class="remove-inclusion-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(newRow);
                updateInclusionRemoveButtons();
                console.log('Backup: New inclusion row added');
            }
        });
    }
    
    if (addFreeItemBtn) {
        addFreeItemBtn.addEventListener('click', function(e) {
            console.log('Backup free item button clicked');
            e.preventDefault();
            
            const container = document.getElementById('free-items-container');
            if (container) {
                const newRow = document.createElement('div');
                newRow.className = 'free-item-row flex items-center mb-2';
                newRow.innerHTML = `
                    <input type="text" name="free_items[]" class="flex-1 px-4 py-2 border border-border-color rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. 1 Free 16x20 Photo Frame">
                    <button type="button" onclick="removeFreeItemRow(this)" class="remove-free-item-btn ml-2 px-2 py-1 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(newRow);
                updateFreeItemRemoveButtons();
                console.log('Backup: New free item row added');
            }
        });
    }
    
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
    
    // Preview image
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
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
                    // Create preview element if it doesn't exist
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'image-preview';
                        preview.className = 'mt-3';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.innerHTML = `
                        <div class="relative w-40 h-40 rounded-md overflow-hidden border border-border-color">
                            <img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">
                            <button type="button" onclick="removeImagePreview()" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                ×
                            </button>
                        </div>
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form validation before submit
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate inclusions
            const inclusionInputs = document.querySelectorAll('input[name="inclusions[]"]');
            let hasValidInclusion = false;
            inclusionInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValidInclusion = true;
                }
            });
            
            // Validate free items  
            const freeItemInputs = document.querySelectorAll('input[name="free_items[]"]');
            let hasValidFreeItem = false;
            freeItemInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasValidFreeItem = true;
                }
            });
            
            if (!hasValidInclusion && !hasValidFreeItem) {
                e.preventDefault();
                showAlert('Please add at least one inclusion or free item.', 'warning');
                return false;
            }
        });
    }
    
    // Make showAlert available globally
    window.showAlert = showAlert;
});

// Global function to remove image preview
function removeImagePreview() {
    const preview = document.getElementById('image-preview');
    const imageInput = document.getElementById('image');
    if (preview) preview.remove();
    if (imageInput) imageInput.value = '';
}

// Global function to remove inclusion row
function removeInclusionRow(button) {
    const row = button.closest('.inclusion-row');
    const rows = document.querySelectorAll('.inclusion-row');
    if (rows.length > 1) {
        row.remove();
        updateInclusionRemoveButtons();
    } else {
        if (window.showAlert) {
            window.showAlert('At least one inclusion is required.', 'warning');
        }
    }
}

// Global function to remove free item row
function removeFreeItemRow(button) {
    const row = button.closest('.free-item-row');
    const rows = document.querySelectorAll('.free-item-row');
    if (rows.length > 1) {
        row.remove();
        updateFreeItemRemoveButtons();
    } else {
        if (window.showAlert) {
            window.showAlert('At least one free item is required.', 'warning');
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
</script>
@endpush
                            @endsection