/**
 * This script adds better logging and debugging for the booking status update functionality
 */

// Add logging for booking status changes
window.addEventListener('booking-status-changed', function(event) {
    console.log('Booking status changed:', event.detail);
    
    // Update UI if needed
    if (event.detail && event.detail.bookingId) {
        const row = document.querySelector(`tr[data-booking-id="${event.detail.bookingId}"]`);
        if (row) {
            // Update the row's data-status attribute
            row.setAttribute('data-status', event.detail.newStatus);
            
            // Highlight the row briefly
            row.classList.add('bg-green-50', 'dark:bg-green-900/20');
            setTimeout(() => {
                row.classList.remove('bg-green-50', 'dark:bg-green-900/20');
            }, 2000);
            
            // Handle status-based filtering
            const currentStatusFilter = document.getElementById('statusFilter')?.value || '';
            if (currentStatusFilter && currentStatusFilter !== 'all' && currentStatusFilter !== event.detail.newStatus) {
                // If filtering is active and this row no longer matches, we should refresh the list
                if (window.refreshBookingsList) {
                    window.refreshBookingsList();
                }
            }
        }
    }
});

// Debugging helper for monitoring AJAX requests
(function() {
    const originalFetch = window.fetch;
    window.fetch = function() {
        const url = arguments[0];
        if (typeof url === 'string' && url.includes('/bookings/') && url.includes('/status')) {
            console.log('Booking status update request:', arguments);
        }
        return originalFetch.apply(this, arguments);
    };
})();

// Add filter function to show only bookings with selected status
document.addEventListener('DOMContentLoaded', function() {
    // Check if we have a status filter in place
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) {
        // Create a status filter if it doesn't exist
        const filterContainer = document.querySelector('.booking-filters');
        if (filterContainer) {
            const statusFilterHTML = `
                <div class="ml-4">
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="statusFilter" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>
            `;
            filterContainer.insertAdjacentHTML('beforeend', statusFilterHTML);
            
            // Add event listener to the newly created filter
            document.getElementById('statusFilter').addEventListener('change', function() {
                if (window.refreshBookingsList) {
                    window.refreshBookingsList();
                }
            });
        }
    }
});
