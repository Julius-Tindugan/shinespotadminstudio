
@extends('layouts.app')
@section('content')
    <div class="max-w-4xl mx-auto" >

        <div class="flex items-center mb-6" >
            <a href="{{ route('addons.index') }}" class="flex items-center text-accent hover:text-accent-dark transition-colors duration-200 mr-4" ><svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg> Back to Addons </a>
                <h1 class="text-2xl font-bold text-primary-text" >Addon Details</h1>

            </div>

            <div class="bg-card-bg rounded-lg shadow-sm overflow-hidden" >

                <div class="p-6" >

                    <div class="flex justify-between items-start" >

                        <h2 class="text-xl font-bold text-primary-text" >{{ $addon->addon_name }}</h2>
                        <span class="{{ $addon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} px-2 py-1 text-xs font-semibold rounded-full" > {{ $addon->is_active ? 'Active' : 'Inactive' }} </span>
                    </div>

                    <div class="mt-4" >

                        <h3 class="text-sm font-medium text-secondary-text" >Price</h3>

                        <p class="mt-1 text-primary-text" >₱{{ number_format($addon->addon_price, 2) }}</p>

                    </div>

                    @if($addon->description)
                        <div class="mt-4" >

                            <h3 class="text-sm font-medium text-secondary-text" >Description</h3>

                            <p class="mt-1 text-primary-text" >{{ $addon->description }}</p>

                        </div>

                    @endif

                    <div class="mt-6" >

                        <h3 class="text-sm font-medium text-secondary-text mb-2" >Available in Packages</h3>

                        @if($addon->packages->count() > 0) <ul class="list-disc pl-5 space-y-1" >
                            @foreach($addon->packages as $package) <li class="text-primary-text" ><a href="{{ route('packages.show', $package->package_id) }}" class="text-accent hover:underline" > {{ $package->title }} ({{ $package->package_type }}) </a></li>
                            @endforeach
                        </ul>
                        @else

                        <p class="text-secondary-text italic" >This addon is not associated with any packages yet.</p>

                    @endif

                </div>

                <div class="mt-6 border-t border-border-color pt-4" >

                    <div class="text-sm text-secondary-text" >

                        <div>
                            Created: {{ $addon->created_at->format('F d, Y \a\t h:i A') }}
                        </div>

                        <div>
                            Last Updated: {{ $addon->updated_at->format('F d, Y \a\t h:i A') }}
                        </div>

                    </div>

                </div>

                <div class="mt-6 flex justify-end space-x-3" >
                    <a href="{{ route('addons.edit', $addon->addon_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors duration-150" > Edit Addon </a>
                    <form action="{{ route('addons.destroy', $addon->
                        addon_id) }}" method="POST" class="inline delete-form" > @csrf @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-150" > Delete Addon </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

    @push('scripts') <script> document.addEventListener('DOMContentLoaded', function() { // Confirm deletion document.querySelector('.delete-form').addEventListener('submit', function(e) { e.preventDefault(); if (confirm('Are you sure you want to delete this addon? This action cannot be undone.')) { this.submit(); } }); }); </script>
    @endpush
@endsection