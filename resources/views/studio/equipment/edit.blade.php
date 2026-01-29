
@extends('layouts.app')
@section('title', 'Edit Equipment')
    @section('content')
        <div class="container px-6 py-8 mx-auto" >

            <div class="flex justify-between items-center mb-6" >

                <h1 class="text-2xl font-semibold text-primary-text" > Edit Equipment </h1>

                <div class="flex space-x-2" >
                    <a href="{{ route('equipment.show', $equipment->equipment_id) }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg> View Details </a><a href="{{ route('equipment.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg flex items-center" ><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg> Back to List </a>
                        </div>

                    </div>

                    <div class="bg-white shadow-md rounded-lg p-6" >

                        <form action="{{ route('equipment.update', $equipment->
                            equipment_id) }}" method="POST" enctype="multipart/form-data"> @csrf @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" >
                                <!-- Name -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-secondary-text mb-1" > Name <span class="text-red-500" >*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $equipment->
                                            name) }}" required class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" > @error('name')
                                            <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Type -->
                                        <div>
                                            <label for="type" class="block text-sm font-medium text-secondary-text mb-1" > Type <span class="text-red-500" >*</span></label>
                                            <select name="type" id="type" required class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" >
                                                <option value="">Select Type</option>
                                                @foreach($equipmentTypes as $type) <option value="{{ $type }}" {{ old('type', $equipment->type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach

                                            </select>
                                            @error('type')
                                            <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                        @enderror
                                    </div>

                                </div>

                                <div class="mb-6" >
                                    <!-- Description --><label for="description" class="block text-sm font-medium text-secondary-text mb-1" > Description </label>
                                    <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" >{{ old('description', $equipment->description) }}</textarea>
                                    @error('description')
                                    <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" >
                                <!-- Image -->
                                    <div>
                                        <label for="image" class="block text-sm font-medium text-secondary-text mb-1" > Image </label>
                                        @if($equipment->image)
                                            <div class="mb-2" >
                                                <img src="{{ asset('storage/' . $equipment->image) }}" alt="{{ $equipment->name }}" class="h-32 w-auto rounded" >
                                                <p class="text-xs text-secondary-text mt-1" > Current image. Upload a new image to replace it. </p>

                                            </div>

                                        @endif

                                        <input type="file" name="image" id="image" class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" accept="image/*">

                                            <p class="text-xs text-secondary-text mt-1" > Accepted formats: JPG, PNG, GIF. Max size: 2MB </p>
                                            @error('image')
                                            <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <!-- Quantity -->
                                        <div>
                                            <label for="quantity" class="block text-sm font-medium text-secondary-text mb-1" > Quantity <span class="text-red-500" >*</span></label>
                                            <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $equipment->
                                                quantity) }}" min="0" required class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" > @error('quantity')
                                                <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6" >
                                        <!-- Cost -->
                                            <div>
                                                <label for="cost" class="block text-sm font-medium text-secondary-text mb-1" > Cost (PHP) </label>
                                                <input type="number" name="cost" id="cost" value="{{ old('cost', $equipment->
                                                    cost) }}" min="0" step="0.01" class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" > @error('cost')
                                                    <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <!-- Purchase Date -->
                                                <div>
                                                    <label for="purchase_date" class="block text-sm font-medium text-secondary-text mb-1" > Purchase Date </label>
                                                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $equipment->
                                                        purchase_date ? $equipment->purchase_date->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" > @error('purchase_date')
                                                        <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                                    @enderror
                                                </div>

                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" >
                                                <!-- Condition -->
                                                    <div>
                                                        <label for="condition" class="block text-sm font-medium text-secondary-text mb-1" > Condition <span class="text-red-500" >*</span></label>
                                                        <select name="condition" id="condition" required class="w-full px-3 py-2 border border-border-color rounded-md focus:outline-none focus:ring-2 focus:ring-accent bg-background text-primary-text" >
                                                            <option value="">Select Condition</option><option value="Excellent" {{ old('condition', $equipment->condition) == 'Excellent' ? 'selected' : '' }}>Excellent</option><option value="Good" {{ old('condition', $equipment->condition) == 'Good' ? 'selected' : '' }}>Good</option><option value="Fair" {{ old('condition', $equipment->condition) == 'Fair' ? 'selected' : '' }}>Fair</option><option value="Poor" {{ old('condition', $equipment->condition) == 'Poor' ? 'selected' : '' }}>Poor</option><option value="Needs Repair" {{ old('condition', $equipment->condition) == 'Needs Repair' ? 'selected' : '' }}>Needs Repair</option>
                                                        </select>
                                                        @error('condition')
                                                        <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <!-- Is Available -->
                                                    <div class="flex items-center mt-6" >

                                                        <input type="checkbox" name="is_available" id="is_available" class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded" {{ old('is_available', $equipment->
                                                            is_available) ? 'checked' : '' }}><label for="is_available" class="ml-2 block text-sm text-primary-text" > Available for Bookings </label> @error('is_available')
                                                            <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <!-- Is Active -->
                                                        <div class="flex items-center mt-6" >

                                                            <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded" {{ old('is_active', $equipment->
                                                                is_active) ? 'checked' : '' }}><label for="is_active" class="ml-2 block text-sm text-primary-text" > Active </label> @error('is_active')
                                                                <p class="text-red-500 text-xs mt-1" >{{ $message }}</p>
                                                            @enderror
                                                        </div>

                                                    </div>

                                                    <div class="flex justify-end mt-8" >

                                                        <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg" > Update Equipment </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    @endsection