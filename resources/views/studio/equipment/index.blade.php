
@extends('layouts.app')
@section('title', 'Equipment Management')
@section('content')
    <div class="container px-6 py-8 mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-primary-text mb-2">Equipment Management</h1>
                <p class="text-secondary-text">Manage your studio equipment inventory</p>
            </div>
            <a href="{{ route('equipment.create') }}" class="inline-flex items-center px-5 py-2.5 bg-accent hover:bg-accent-dark text-white rounded-lg font-medium transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Add Equipment
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-lg shadow-sm flex items-start" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-lg shadow-sm flex items-start" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-xl p-5 mb-6 border border-gray-100">
            <form action="{{ route('equipment.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="type" class="block text-sm font-semibold text-primary-text mb-2">Type</label>
                    <select id="type" name="type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all">
                        <option value="">All Types</option>
                        <option value="Camera" {{ request('type') == 'Camera' ? 'selected' : '' }}>Camera</option>
                        <option value="Lens" {{ request('type') == 'Lens' ? 'selected' : '' }}>Lens</option>
                        <option value="Lighting" {{ request('type') == 'Lighting' ? 'selected' : '' }}>Lighting</option>
                        <option value="Lighting Modifier" {{ request('type') == 'Lighting Modifier' ? 'selected' : '' }}>Lighting Modifier</option>
                        <option value="Prop" {{ request('type') == 'Prop' ? 'selected' : '' }}>Prop</option>
                        <option value="Accessory" {{ request('type') == 'Accessory' ? 'selected' : '' }}>Accessory</option>
                        <option value="Support" {{ request('type') == 'Support' ? 'selected' : '' }}>Support</option>
                        <option value="Background" {{ request('type') == 'Background' ? 'selected' : '' }}>Background</option>
                        <option value="Other" {{ request('type') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="availability" class="block text-sm font-semibold text-primary-text mb-2">Availability</label>
                    <select id="availability" name="availability" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all">
                        <option value="">All</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-primary-text mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent bg-white text-primary-text transition-all">
                        <option value="">All</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-accent hover:bg-accent-dark text-white rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Equipment Table -->
        <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Equipment</th>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Type</th>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Quantity</th>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Condition</th>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Status</th>
                            <th class="py-4 px-6 text-left text-xs font-bold text-secondary-text uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($equipment as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-4">
                                        @if($item->image)
                                            <div class="h-12 w-12 flex-shrink-0 rounded-lg overflow-hidden ring-2 ring-gray-200">
                                                <img class="h-12 w-12 object-cover" src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                                            </div>
                                        @else
                                            <div class="h-12 w-12 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                        @endif

                                        <div>
                                            <div class="text-sm font-medium text-primary-text">{{ $item->name }}</div>
                                            <div class="text-xs text-secondary-text">{{ Str::limit($item->description, 30) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-sm text-primary-text">{{ $item->type }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-3 py-1 text-sm font-semibold text-primary-text bg-gray-100 rounded-lg">{{ $item->quantity }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full 
                                        {{ $item->condition == 'Excellent' ? 'bg-green-100 text-green-800 ring-1 ring-green-600/20' : '' }}
                                        {{ $item->condition == 'Good' ? 'bg-blue-100 text-blue-800 ring-1 ring-blue-600/20' : '' }}
                                        {{ $item->condition == 'Fair' ? 'bg-yellow-100 text-yellow-800 ring-1 ring-yellow-600/20' : '' }}
                                        {{ $item->condition == 'Poor' ? 'bg-orange-100 text-orange-800 ring-1 ring-orange-600/20' : '' }}
                                        {{ $item->condition == 'Needs Repair' ? 'bg-red-100 text-red-800 ring-1 ring-red-600/20' : '' }}">
                                        {{ $item->condition }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-2">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800 ring-1 ring-green-600/20' : 'bg-red-100 text-red-800 ring-1 ring-red-600/20' }}">
                                            <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $item->is_active ? 'bg-green-600' : 'bg-red-600' }}"></span>
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $item->is_available ? 'bg-blue-100 text-blue-800 ring-1 ring-blue-600/20' : 'bg-gray-100 text-gray-800 ring-1 ring-gray-600/20' }}">
                                            {{ $item->is_available ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('equipment.show', $item->equipment_id) }}" class="text-accent hover:text-accent-dark transition-colors" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('equipment.edit', $item->equipment_id) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button type="button" onclick="openDeleteModal({{ $item->equipment_id }}, '{{ addslashes($item->name) }}')" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 px-6 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-secondary-text text-base mb-2">No equipment found</p>
                                        <a href="{{ route('equipment.create') }}" class="text-accent hover:text-accent-dark font-medium">
                                            Add your first equipment
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $equipment->links() }}
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-center text-primary-text mb-2">Delete Equipment</h3>
                <p class="text-sm text-center text-secondary-text mb-6">
                    Are you sure you want to delete <span id="deleteItemName" class="font-semibold text-primary-text"></span>? This action cannot be undone.
                </p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function openDeleteModal(equipmentId, equipmentName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const itemName = document.getElementById('deleteItemName');
    
    form.action = `/studio/equipment/${equipmentId}`;
    itemName.textContent = equipmentName;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
    }
});

// Close modal on background click
document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush