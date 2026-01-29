/**
 * Booking status management functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Attach status change listeners to all booking status selects
    attachStatusChangeListeners();

    // Function to attach event listeners to status select elements
    function attachStatusChangeListeners() {
        // Get all status selectors
        const statusSelects = document.querySelectorAll('.booking-status-select');
        
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const selectElement = this;
                const bookingId = selectElement.dataset.bookingId;
                const newStatus = selectElement.value;
                const originalStatus = selectElement.dataset.originalStatus;
                
                // If no change, do nothing
                if (newStatus === originalStatus) {
                    return;
                }
                
                // Add visual feedback that something is happening
                selectElement.classList.add('opacity-50');
                
                // Debounce to prevent multiple rapid requests
                setTimeout(() => {
                    // Prepare CSRF token
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    
                    // Make the AJAX request to update status
                    fetch(`/bookings/${bookingId}/status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            status: newStatus
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Remove loading indicator
                        selectElement.classList.remove('opacity-50');
                        
                        if (data.success) {
                            // Update the status select style
                            updateStatusSelectClass(selectElement, newStatus);
                            
                            // Update the data-original-status attribute
                            selectElement.setAttribute('data-original-status', newStatus);
                            
                            // Update the parent row data-status attribute for filtering
                            const tableRow = selectElement.closest('tr');
                            if (tableRow) {
                                tableRow.setAttribute('data-status', newStatus);
                            }
                            
                            // Remove any existing notifications
                            const existingNotifications = document.querySelectorAll('.status-update-notification');
                            existingNotifications.forEach(notification => notification.remove());
                            
                            // Add a temporary success message
                            const notificationContainer = document.createElement('div');
                            notificationContainer.classList.add('fixed', 'bottom-4', 'right-4', 'bg-green-100', 'border', 'border-green-400', 'text-green-700', 'px-4', 'py-3', 'rounded-lg', 'shadow-lg', 'z-50', 'status-update-notification');
                            notificationContainer.innerHTML = `
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Status updated to <strong>${data.new_status}</strong> successfully!</span>
                                </div>
                            `;
                            document.body.appendChild(notificationContainer);
                            
                            // Remove the notification after 3 seconds
                            setTimeout(() => {
                                notificationContainer.remove();
                            }, 3000);
                            
                            // Dispatch custom event for booking-status-changed
                            window.dispatchEvent(new CustomEvent('booking-status-changed', {
                                detail: {
                                    bookingId: data.booking_id,
                                    newStatus: data.new_status,
                                    oldStatus: data.old_status
                                }
                            }));
                        } else {
                            // Revert to original status on error
                            selectElement.value = originalStatus;
                            updateStatusSelectClass(selectElement, originalStatus);
                            alert('Failed to update status: ' + data.message);
                        }
                    })
                    .catch(error => {
                        // Remove loading indicator
                        selectElement.classList.remove('opacity-50');
                        
                        console.error('Error:', error);
                        // Revert to original status on error
                        selectElement.value = originalStatus;
                        updateStatusSelectClass(selectElement, originalStatus);
                        alert('An error occurred while updating the status. Please try again.');
                    });
                }, 300); // 300ms delay to prevent rapid-fire requests
            });
        });
    }
    
    // Update the status select styling based on the current status
    function updateStatusSelectClass(select, status) {
        // Remove all existing status classes
        select.classList.remove(
            'bg-green-100', 'text-green-800', 'bg-yellow-100', 'text-yellow-800',
            'bg-blue-100', 'text-blue-800', 'bg-red-100', 'text-red-800',
            'dark:bg-green-800', 'dark:text-green-100', 'dark:bg-yellow-800', 
            'dark:text-yellow-100', 'dark:bg-blue-800', 'dark:text-blue-100',
            'dark:bg-red-800', 'dark:text-red-100'
        );
        
        // Add appropriate status classes
        switch(status) {
            case 'confirmed':
                select.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-800', 'dark:text-green-100');
                break;
            case 'pending':
                select.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-800', 'dark:text-yellow-100');
                break;
            case 'completed':
                select.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-800', 'dark:text-blue-100');
                break;
            case 'canceled':
                select.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-800', 'dark:text-red-100');
                break;
        }
    }

    // Implement a robust refreshBookingsList function
    function refreshBookingsList() {
        const currentStatusFilter = document.getElementById('statusFilter')?.value || '';
        const currentPageUrl = new URL(window.location.href);
        
        // Preserve any existing query parameters like search term
        const params = new URLSearchParams(currentPageUrl.search);
        
        // Only add status filter if it's not 'all'
        if (currentStatusFilter && currentStatusFilter !== 'all') {
            params.set('status', currentStatusFilter);
        } else {
            params.delete('status');
        }
        
        // Update URL without refreshing page
        currentPageUrl.search = params.toString();
        window.history.pushState({}, '', currentPageUrl.toString());
        
        // Get CSRF token for the request
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
        // Show loading indicator
        const bookingsList = document.querySelector('#bookings-list');
        if (bookingsList) {
            bookingsList.classList.add('opacity-50');
            
            // Make AJAX request to refresh the bookings list
            fetch(currentPageUrl.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    bookingsList.innerHTML = data.html;
                    
                    // Re-attach event listeners to new elements
                    attachStatusChangeListeners();
                }
                bookingsList.classList.remove('opacity-50');
            })
            .catch(error => {
                console.error('Error refreshing bookings list:', error);
                bookingsList.classList.remove('opacity-50');
            });
        }
    }
    
    // Add event listener for status filter changes
    document.addEventListener('DOMContentLoaded', function() {
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                refreshBookingsList();
            });
        }
    });

    // Make these functions globally available
    window.attachStatusChangeListeners = attachStatusChangeListeners;
    window.updateStatusSelectClass = updateStatusSelectClass;
    window.refreshBookingsList = refreshBookingsList;
});
