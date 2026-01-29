/**
 * Shows booking details in a popover when an event is clicked
 */
function showBookingDetailsPopover(event) {
    // Create popover element
    const popover = document.createElement('div');
    popover.id = 'booking-popover';
    popover.className = 'fixed z-50 bg-card-bg dark:bg-dark-card-bg shadow-xl rounded-lg p-4 w-80 border border-border-color dark:border-dark-border-color';

    // Get event data
    const title = event.title;
    const start = event.start ? event.start.toLocaleString() : 'N/A';
    const end = event.end ? event.end.toLocaleString() : 'N/A';
    const client = event.extendedProps?.client || 'Unknown Client';
    const packageName = event.extendedProps?.package || 'Standard Session';
    const status = event.extendedProps?.status || 'pending';
    const phone = event.extendedProps?.phone || 'No phone provided';

    // Status color
    let statusColor = '';
    switch(status.toLowerCase()) {
        case 'confirmed':
            statusColor = 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
            break;
        case 'pending':
            statusColor = 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
            break;
        case 'cancelled':
            statusColor = 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
            break;
        default:
            statusColor = 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
    }

    // Set popover content
    popover.innerHTML = `
        <div class="relative">
            <button id="close-popover" class="absolute top-0 right-0 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <h3 class="font-bold text-lg mb-2">${title}</h3>
            <div class="mb-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColor}">
                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                </span>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Start: ${start}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>End: ${end}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Client: ${client}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Package: ${packageName}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span>${phone}</span>
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-2">
                <button class="px-3 py-1 text-xs bg-background dark:bg-dark-background border border-border-color dark:border-dark-border-color rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                    Edit
                </button>
                <button class="px-3 py-1 text-xs bg-red-500 text-white rounded hover:bg-red-600">
                    Cancel Booking
                </button>
            </div>
        </div>
    `;

    // Position the popover near the event
    const mouseX = event.jsEvent.pageX;
    const mouseY = event.jsEvent.pageY;

    document.body.appendChild(popover);

    // Position popover
    const popoverWidth = popover.offsetWidth;
    const popoverHeight = popover.offsetHeight;
    const windowWidth = window.innerWidth;
    const windowHeight = window.innerHeight;

    // Adjust position to keep popover within viewport
    let leftPos = mouseX + 10;
    let topPos = mouseY + 10;

    // Adjust horizontally if needed
    if (leftPos + popoverWidth > windowWidth - 10) {
        leftPos = mouseX - popoverWidth - 10;
    }

    // Adjust vertically if needed
    if (topPos + popoverHeight > windowHeight - 10) {
        topPos = mouseY - popoverHeight - 10;
    }

    // Set final position
    popover.style.left = `${leftPos}px`;
    popover.style.top = `${topPos}px`;

    // Handle close button click
    document.getElementById('close-popover').addEventListener('click', () => {
        document.body.removeChild(popover);
    });

    // Close on outside click
    document.addEventListener('click', function closePopover(e) {
        if (!popover.contains(e.target) && e.target !== popover) {
            if (document.body.contains(popover)) {
                document.body.removeChild(popover);
            }
            document.removeEventListener('click', closePopover);
        }
    });
}

/**
 * Shows a new booking form when a date is clicked
 */
function showNewBookingForm(date) {
    // Format the date for the form
    const formattedDate = date.toISOString().split('T')[0];

    // In a real app, you might open a full booking form modal here
    // For this example, we'll show a toast
    showToast(`Creating new booking for ${new Date(formattedDate).toLocaleDateString()}`, 'info');

    // You could also open a modal with a booking form pre-filled with the selected date
}

/**
 * Shows a toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed bottom-4 right-4 p-4 rounded-lg text-white shadow-lg transform translate-y-0 opacity-0 transition-all duration-300 z-50`;

    // Set background color based on type
    switch(type) {
        case 'success':
            toast.classList.add('bg-green-500');
            break;
        case 'error':
            toast.classList.add('bg-red-500');
            break;
        case 'warning':
            toast.classList.add('bg-yellow-500');
            break;
        default:
            toast.classList.add('bg-blue-500');
            break;
    }

    toast.innerHTML = message;
    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-0');
        toast.classList.add('translate-y-[-1rem]', 'opacity-100');
    }, 10);

    // Animate out and remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('translate-y-[-1rem]', 'opacity-100');
        toast.classList.add('translate-y-0', 'opacity-0');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

// Export these functions
export { showBookingDetailsPopover, showNewBookingForm, showToast };
