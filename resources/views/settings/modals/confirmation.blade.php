<!-- Confirmation Modal -->
<div id="confirmation-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div id="confirmation-icon" class="flex-shrink-0 mr-3">
                    <!-- Icon will be dynamically set -->
                </div>
                <h3 id="confirmation-title" class="text-lg font-semibold text-gray-900">
                    Confirm Action
                </h3>
            </div>
            <button type="button" 
                    onclick="closeConfirmationModal()" 
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <p id="confirmation-message" class="text-sm text-gray-600">
                Are you sure you want to proceed with this action?
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50">
            <button type="button" 
                    onclick="closeConfirmationModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                Cancel
            </button>
            <button type="button" 
                    id="confirmation-confirm-btn"
                    class="px-4 py-2 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
// Confirmation modal functionality
let confirmationCallback = null;

function showConfirmationModal(options) {
    const modal = document.getElementById('confirmation-modal');
    const title = document.getElementById('confirmation-title');
    const message = document.getElementById('confirmation-message');
    const confirmBtn = document.getElementById('confirmation-confirm-btn');
    const icon = document.getElementById('confirmation-icon');
    
    // Set content
    title.textContent = options.title || 'Confirm Action';
    message.textContent = options.message || 'Are you sure you want to proceed?';
    
    // Set button text and color based on type
    const type = options.type || 'default';
    confirmBtn.textContent = options.confirmText || 'Confirm';
    
    // Set colors based on action type
    if (type === 'danger') {
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors';
        icon.innerHTML = `
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        `;
    } else if (type === 'warning') {
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors';
        icon.innerHTML = `
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    } else if (type === 'success') {
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors';
        icon.innerHTML = `
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    } else {
        confirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors';
        icon.innerHTML = `
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        `;
    }
    
    // Store callback
    confirmationCallback = options.onConfirm;
    
    // Show modal
    modal.classList.remove('hidden');
    
    // Focus confirm button
    setTimeout(() => confirmBtn.focus(), 100);
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmation-modal');
    modal.classList.add('hidden');
    confirmationCallback = null;
}

function handleConfirmation() {
    if (confirmationCallback && typeof confirmationCallback === 'function') {
        confirmationCallback();
    }
    closeConfirmationModal();
}

// Attach event listener to confirm button
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmation-confirm-btn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', handleConfirmation);
    }
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('confirmation-modal');
            if (!modal.classList.contains('hidden')) {
                closeConfirmationModal();
            }
        }
    });
});
</script>
