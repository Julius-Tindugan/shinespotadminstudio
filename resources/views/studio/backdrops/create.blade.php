
@extends('layouts.app')
@section('title', 'Add New Backdrop')
@section('content')
    <div class="container px-6 py-8 mx-auto max-w-4xl">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-primary-text mb-2">Add New Backdrop</h1>
                <p class="text-secondary-text">Create a new backdrop for your studio</p>
            </div>
            <a href="{{ route('backdrops.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Back to Backdrops
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
            <form action="{{ route('backdrops.store') }}" method="POST">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-primary-text mb-4 pb-2 border-b border-gray-200">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-primary-text mb-2">
                                Backdrop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all"
                                placeholder="Enter backdrop name">
                            @error('name')
                                <p class="text-red-500 text-xs mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Color Code -->
                        <div>
                            <label for="color_code" class="block text-sm font-semibold text-primary-text mb-2">
                                Color Code
                            </label>
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <input type="color" id="color_picker" value="{{ old('color_code', '#FFFFFF') }}" 
                                        class="h-11 w-16 border border-gray-300 rounded-lg cursor-pointer">
                                </div>
                                <input type="text" name="color_code" id="color_code" value="{{ old('color_code') }}" 
                                    placeholder="#FFFFFF" 
                                    class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text font-mono transition-all">
                            </div>
                           
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="mb-8">
                    <label for="description" class="block text-sm font-semibold text-primary-text mb-2">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="4" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all resize-none"
                        placeholder="Enter backdrop description (optional)">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status Section -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" 
                            class="h-5 w-5 text-accent focus:ring-accent focus:ring-2 border-gray-300 rounded cursor-pointer" 
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3">
                            <span class="text-sm font-semibold text-primary-text">Active Status</span>
                            <p class="text-xs text-secondary-text mt-0.5">This backdrop will be available for bookings</p>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="text-red-500 text-xs mt-2 flex items-center ml-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('backdrops.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-dark text-white rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        Save Backdrop
                    </button>
                </div>
            </form>
        </div>
    </div>

                        @push('scripts')
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const colorPicker = document.getElementById('color_picker');
                            const colorCode = document.getElementById('color_code');
                            
                            if (colorPicker && colorCode) {
                                // Update text field when color picker changes
                                colorPicker.addEventListener('input', function() {
                                    colorCode.value = this.value.toUpperCase();
                                });
                                
                                // Also listen to 'change' event for better compatibility
                                colorPicker.addEventListener('change', function() {
                                    colorCode.value = this.value.toUpperCase();
                                });
                                
                                // Update color picker when text field changes
                                colorCode.addEventListener('input', function() {
                                    if (this.value.match(/^#[0-9A-F]{6}$/i)) {
                                        colorPicker.value = this.value;
                                    }
                                });
                            }
                        });
                        </script>
                        @endpush
                    @endsection