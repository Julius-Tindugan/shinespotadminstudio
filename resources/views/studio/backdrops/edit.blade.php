@extends('layouts.app')
@section('title', 'Edit Backdrop')
@section('content')
    <div class="container px-6 py-8 mx-auto max-w-4xl">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-primary-text mb-2">Edit Backdrop</h1>
                <p class="text-secondary-text">Update backdrop information</p>
            </div>
            <a href="{{ route('backdrops.index') }}" class="inline-flex items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                Back to List
            </a>
        </div>

        <div class="bg-white shadow-sm rounded-xl p-8 border border-gray-100">
            <form action="{{ route('backdrops.update', $backdrop->backdrop_id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Form Errors -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-semibold">Please correct the following errors:</p>
                                <ul class="mt-2 list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-primary-text mb-4 pb-2 border-b border-gray-200">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Backdrop Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-primary-text mb-2">
                                Backdrop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $backdrop->name) }}" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all"
                                placeholder="Enter backdrop name">
                        </div>

                        <!-- Color Code -->
                        <div>
                            <label for="color_code" class="block text-sm font-semibold text-primary-text mb-2">
                                Color Code
                            </label>
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <input type="color" id="color_picker" value="{{ old('color_code', $backdrop->color_code) }}" 
                                        class="h-11 w-16 border border-gray-300 rounded-lg cursor-pointer">
                                </div>
                                <input type="text" name="color_code" id="color_code" value="{{ old('color_code', $backdrop->color_code) }}" 
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
                        placeholder="Enter backdrop description (optional)">{{ old('description', $backdrop->description) }}</textarea>
                </div>

                <!-- Status Section -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" 
                            class="h-5 w-5 text-accent focus:ring-accent focus:ring-2 border-gray-300 rounded cursor-pointer"
                            {{ old('is_active', $backdrop->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-3">
                            <span class="text-sm font-semibold text-primary-text">Active Status</span>
                            <p class="text-xs text-secondary-text mt-0.5">When inactive, this backdrop won't be available for new bookings</p>
                        </label>
                    </div>
                </div>

                <!-- Current Booking Warning -->
                @if ($activeBookings > 0)
                    <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <p class="font-semibold">Active Bookings Alert</p>
                                <p class="mt-1 text-sm">This backdrop is currently used in {{ $activeBookings }} active booking(s). Changes may affect existing bookings.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('backdrops.index') }}" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-dark text-white rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                        Update Backdrop
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const colorPicker = document.getElementById('color_picker');
                const colorInput = document.getElementById('color_code');

                if (colorPicker && colorInput) {
                    // Update text input when color picker changes
                    colorPicker.addEventListener('input', function() {
                        colorInput.value = this.value.toUpperCase();
                    });

                    // Also listen to 'change' event for better compatibility
                    colorPicker.addEventListener('change', function() {
                        colorInput.value = this.value.toUpperCase();
                    });

                    // Update color picker when text input changes
                    colorInput.addEventListener('input', function() {
                        // Only update if it's a valid hex color
                        if (/^#([0-9A-F]{3}){1,2}$/i.test(this.value)) {
                            colorPicker.value = this.value;
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
