
@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto" >

        <div class="flex items-center mb-6" >
            <a href="{{ route('addons.index') }}" class="flex items-center text-accent hover:text-accent-dark transition-colors duration-200 mr-4" ><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Addons </a>
                <h1 class="text-2xl font-bold text-primary-text" >Create New Addon</h1>

            </div>

            <div class="bg-card-bg rounded-lg shadow-sm p-6" >

                <form action="{{ route('addons.store') }}" method="POST">
                    @csrf
                    <div class="mb-6" >
                        <label for="addon_name" class="block text-sm font-medium text-primary-text mb-1" >Addon Name <span class="text-red-500" >*</span></label>
                        <input type="text" id="addon_name" name="addon_name" value="{{ old('addon_name') }}" required class="w-full px-4 py-2 border @error('addon_name') border-red-500
                        @else
                        border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="e.g. Additional Hour">
                        @error('addon_name')
                        <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6" >
                    <label for="addon_price" class="block text-sm font-medium text-primary-text mb-1" >Price (PHP) <span class="text-red-500" >*</span></label>
                    <div class="relative" >

                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" >
                            <span class="text-gray-500" >₱</span>
                        </div>

                        <input type="number" id="addon_price" name="addon_price" value="{{ old('addon_price') }}" required min="0" step="0.01" class="w-full pl-8 pr-4 py-2 border @error('addon_price') border-red-500
                        @else
                        border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="0.00">

                    </div>
                    @error('addon_price')
                    <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6" >
                <label for="description" class="block text-sm font-medium text-primary-text mb-1" >Description</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border @error('description') border-red-500
                @else
                border-border-color @enderror rounded-md focus:outline-none focus:ring-accent focus:border-accent" placeholder="Enter addon description">{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600" >{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center mb-6" >

            <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded" >
                <label for="is_active" class="ml-2 block text-sm text-primary-text" >Active</label>
            </div>

            <div class="flex justify-end space-x-3" >
                <a href="{{ route('addons.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md transition-colors duration-150" > Cancel </a>
                <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-md transition-colors duration-150" > Create Addon </button>

            </div>

        </form>

    </div>

</div>
@endsection