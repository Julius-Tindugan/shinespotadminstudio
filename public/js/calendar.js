document.addEventListener('DOMContentLoaded', function() {
    // Helper function for showing toast notifications
    // Falls back to console.log if showToast is not available
    const safeShowToast = function(message, type) {
        if (typeof showToast === 'function') {
            safeShowToast(message, type);
        } else {
            console.log(`[${type.toUpperCase()}] ${message}`);
            // Fallback alert for critical errors
            if (type === 'error') {
                console.error(message);
            }
        }
    };
    
    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar-view');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 1.8,
        eventDisplay: 'block',
        displayEventTime: true,
        displayEventEnd: false,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        events: function(info, successCallback, failureCallback) {
            // Note: info.start and info.end contain the date range being viewed
            // We fetch ALL bookings regardless of date range for complete historical view
            fetch('/admin/calendar/bookings')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Validate data structure
                    if (Array.isArray(data)) {
                        console.log(`Calendar: Loaded ${data.length} bookings from server`);
                        
                        // Filter out any invalid events
                        const validEvents = data.filter(event => {
                            const isValid = event.id 
                                && event.start 
                                && event.title 
                                && event.extendedProps 
                                && event.extendedProps.booking_id;
                            
                            if (!isValid) {
                                console.warn('Invalid event filtered out:', event);
                            }
                            return isValid;
                        });
                        
                        console.log(`Calendar: Displaying ${validEvents.length} valid bookings (including past bookings)`);
                        successCallback(validEvents);
                    } else if (data.error) {
                        console.error('Server error loading bookings:', data.message);
                        safeShowToast(data.message || 'Failed to load bookings', 'error');
                        failureCallback(data.error);
                    } else {
                        successCallback([]);
                    }
                })
                .catch(error => {
                    console.error('Error loading bookings:', error);
                    safeShowToast('Failed to load calendar bookings. Please refresh the page.', 'error');
                    failureCallback(error);
                });
        },
        eventSources: [
            {
                url: '/admin/calendar/unavailable-dates',
                failure: function() {
                    console.error('Error loading unavailable dates');
                }
            }
        ],
        businessHours: {
            url: '/admin/calendar/business-hours'
        },
        selectable: true,
        select: function(info) {
            // Handle date selection - show date availability modal
            showDateAvailabilityModal(info.start, info.startStr);
        },
        eventClick: function(info) {
            // Handle event click
            if (info.event.extendedProps && info.event.extendedProps.type === 'booking') {
                showEventDetails(info.event);
            }
        },
        eventDidMount: function(info) {
            // Add tooltip for better UX
            const status = info.event.extendedProps.status;
            if (status) {
                info.el.title = `${info.event.title}\nStatus: ${status.charAt(0).toUpperCase() + status.slice(1)}`;
            }
        },
        datesSet: function() {
            loadUnavailableDatesList();
            loadBusinessHoursSummary();
        }
    });
    
    calendar.render();
    
    // View Type Switcher
    document.getElementById('calendar-view-type').addEventListener('change', function() {
        calendar.changeView(this.value);
    });
    
    // Today Button
    document.getElementById('calendar-today').addEventListener('click', function() {
        calendar.today();
    });
    
    // Show event details in modal
    function showEventDetails(event) {
        // Validate event data
        if (!event || !event.extendedProps) {
            console.error('Invalid event data:', event);
            safeShowToast('Unable to load booking details', 'error');
            return;
        }
        
        const modal = document.getElementById('event-details-modal');
        const content = document.getElementById('event-details-content');
        const title = document.getElementById('event-title');
        const viewBtn = document.getElementById('event-view-btn');
        
        if (!modal || !content || !title || !viewBtn) {
            console.error('Modal elements not found');
            return;
        }
        
        title.textContent = event.title || 'Booking Details';
        
        // Use booking_id from extendedProps to ensure we get the correct ID
        const bookingId = event.extendedProps.booking_id || event.id;
        
        if (!bookingId) {
            console.error('No booking ID found for event:', event);
            safeShowToast('Invalid booking reference', 'error');
            return;
        }
        
        viewBtn.href = `/admin/bookings/${bookingId}`;
        
        const statusBadge = getStatusBadgeClass(event.extendedProps.status);
        const statusText = event.extendedProps.status ? event.extendedProps.status.charAt(0).toUpperCase() + event.extendedProps.status.slice(1) : 'Unknown';
        
        content.innerHTML = `
            <div class="grid grid-cols-1 gap-4">
                <div class="flex justify-between items-center py-2 border-b border-border-color">
                    <div class="text-sm font-semibold text-secondary-text flex items-center">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Type
                    </div>
                    <div class="font-semibold text-primary-text">${event.extendedProps.type || 'Booking'}</div>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-border-color">
                    <div class="text-sm font-semibold text-secondary-text flex items-center">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Date
                    </div>
                    <div class="font-semibold text-primary-text">${formatDate(event.start)}</div>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-border-color">
                    <div class="text-sm font-semibold text-secondary-text flex items-center">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Time
                    </div>
                    <div class="font-semibold text-primary-text">${formatTime(event.start)} - ${formatTime(event.end)}</div>
                </div>
                <div class="flex justify-between items-center py-2">
                    <div class="text-sm font-semibold text-secondary-text flex items-center">
                        <svg class="w-4 h-4 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Status
                    </div>
                    <div>
                        <span class="px-3 py-1.5 text-xs font-bold rounded-lg shadow-sm ${statusBadge}">
                            ${statusText}
                        </span>
                    </div>
                </div>
            </div>
        `;
        
        openModal(modal);
    }
    
    // Get status badge class with improved styling
    function getStatusBadgeClass(status) {
        switch (status ? status.toLowerCase() : '') {
            case 'confirmed':
                return 'bg-emerald-100 text-emerald-800 border border-emerald-300';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 border border-yellow-300';
            case 'cancelled':
                return 'bg-red-100 text-red-800 border border-red-300';
            case 'completed':
                return 'bg-blue-100 text-blue-800 border border-blue-300';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-300';
        }
    }
    
    // Load Unavailable Dates List
    function loadUnavailableDatesList() {
        const container = document.getElementById('unavailable-dates-list');
        const countEl = document.getElementById('unavailable-count');
        
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2"></div>
                <div class="text-sm text-secondary-text">Loading...</div>
            </div>
        `;
        
        fetch('/admin/calendar/unavailable-dates')
            .then(response => response.json())
            .then(data => {
                const uniqueDates = {};
                
                // Group by unavailable_id
                data.forEach(event => {
                    if (event.extendedProps && event.extendedProps.type === 'unavailable') {
                        const id = event.extendedProps.unavailable_id;
                        if (id && !uniqueDates[id]) {
                            uniqueDates[id] = {
                                id: id,
                                title: event.title,
                                dates: []
                            };
                        }
                        if (id) {
                            uniqueDates[id].dates.push(event.start);
                        }
                    }
                });
                
                // Convert to array and sort
                const sortedDates = Object.values(uniqueDates).sort((a, b) => {
                    return new Date(a.dates[0]) - new Date(b.dates[0]);
                });
                
                // Update the count
                countEl.textContent = `${sortedDates.length} date range${sortedDates.length !== 1 ? 's' : ''}`;
                
                // Update the list
                if (sortedDates.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-secondary-text">No unavailable dates set</p>
                            <p class="text-xs text-gray-400 mt-1">All dates are available for booking</p>
                        </div>
                    `;
                    return;
                }
                
                container.innerHTML = '';
                sortedDates.forEach(item => {
                    const dateRange = item.dates.length > 1 
                        ? `${formatDate(item.dates[0])} - ${formatDate(item.dates[item.dates.length - 1])}`
                        : formatDate(item.dates[0]);
                        
                    const div = document.createElement('div');
                    div.className = 'p-3 bg-background rounded border border-border-color hover:border-accent transition-colors mb-3';
                    div.innerHTML = `
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-primary-text">${dateRange}</div>
                                <div class="text-sm text-secondary-text">${item.title}</div>
                            </div>
                            <button class="text-gray-400 hover:text-red-500 transition-colors delete-date" data-id="${item.id}" data-range="${dateRange}" data-reason="${item.title}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    container.appendChild(div);
                });
                
                // Add event listeners to delete buttons
                document.querySelectorAll('.delete-date').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const dateRange = this.getAttribute('data-range');
                        const reason = this.getAttribute('data-reason');
                        
                        // Validate ID before proceeding
                        if (!id || id === 'undefined' || id === 'null') {
                            safeShowToast('Invalid unavailable date ID. Please refresh the page and try again.', 'error');
                            return;
                        }
                        
                        // Show confirmation modal instead of browser confirm
                        showDeleteUnavailableDateModal(id, dateRange, reason);
                    });
                });
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-500">Failed to load unavailable dates</p>
                        <button id="retry-load-dates" class="text-sm text-accent hover:underline mt-2">Retry</button>
                    </div>
                `;
                document.getElementById('retry-load-dates').addEventListener('click', loadUnavailableDatesList);
                console.error('Error:', error);
            });
    }
    
    // Load Business Hours Summary
    function loadBusinessHoursSummary() {
        const container = document.getElementById('business-hours-summary');
        
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2"></div>
                <div class="text-sm text-secondary-text">Loading...</div>
            </div>
        `;
        
        fetch('/api/calendar/all-business-hours')
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                
                const daysOfWeek = [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 
                    'Thursday', 'Friday', 'Saturday'
                ];
                
                // Sort by day of week
                data.sort((a, b) => a.day_of_week - b.day_of_week);
                
                data.forEach(day => {
                    const dayName = daysOfWeek[day.day_of_week];
                    const status = day.is_closed ? 
                        '<span class="text-red-500">Closed</span>' : 
                        `<span class="text-green-600">${formatTime12h(day.open_time)} - ${formatTime12h(day.close_time)}</span>`;
                    
                    const dayEl = document.createElement('div');
                    dayEl.className = 'flex justify-between items-center py-1';
                    dayEl.innerHTML = `
                        <span class="font-medium text-primary-text">${dayName}</span>
                        <span>${status}</span>
                    `;
                    container.appendChild(dayEl);
                });
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-500">Failed to load business hours</p>
                        <button id="retry-load-hours" class="text-sm text-accent hover:underline mt-2">Retry</button>
                    </div>
                `;
                document.getElementById('retry-load-hours').addEventListener('click', loadBusinessHoursSummary);
                console.error('Error:', error);
            });
    }
    
    // Format time (12h)
    function formatTime12h(timeStr) {
        if (!timeStr) return 'N/A';
        const [hours, minutes] = timeStr.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
    
    // Format date to more readable format
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }
    
    // Format time from date
    function formatTime(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    }
    
    // Handle Unavailable Date Form
    document.getElementById('unavailable-date-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const reason = document.getElementById('reason').value;
        
        // Basic validation
        if (!startDate || !endDate) {
            safeShowToast('Please select both start and end dates', 'error');
            return;
        }
        
        if (new Date(startDate) > new Date(endDate)) {
            safeShowToast('End date must be after start date', 'error');
            return;
        }
        
        const formData = { start_date: startDate, end_date: endDate, reason };
        
        // Disable form during submission
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        fetch('/admin/calendar/unavailable-dates', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                });
            }
            return response.json();
        })
        .then(data => {
            // Re-enable form
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            if (data.success) {
                calendar.refetchEvents();
                loadUnavailableDatesList();
                document.getElementById('unavailable-date-form').reset();
                safeShowToast('Dates marked as unavailable successfully', 'success');
            } else {
                safeShowToast(data.message || 'Failed to mark dates as unavailable', 'error');
            }
        })
        .catch(error => {
            // Re-enable form
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            // Handle validation errors
            if (error.errors) {
                const errorMessages = Object.values(error.errors).flat();
                safeShowToast(errorMessages.join('<br>'), 'error');
            } else if (error.message) {
                safeShowToast(error.message, 'error');
            } else {
                safeShowToast('An error occurred. Please try again.', 'error');
            }
            console.error('Error:', error);
        });
    });
    
    // Business Hours Modal
    const businessHoursModal = document.getElementById('business-hours-modal');
    
    document.getElementById('manage-business-hours-btn').addEventListener('click', function() {
        loadBusinessHours();
        openModal(businessHoursModal);
    });
    
    document.getElementById('studio-hours-btn').addEventListener('click', function() {
        loadBusinessHours();
        openModal(businessHoursModal);
    });
    
    // Booking Slots Modal
    const bookingSlotsModal = document.getElementById('booking-slots-modal');
    
    document.getElementById('manage-slots-btn').addEventListener('click', function() {
        openModal(bookingSlotsModal);
    });
    
    // Remove New Booking Button functionality
    
    // Handle modal close buttons
    document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal-container');
            closeModal(modal);
        });
    });
    
    // Load Business Hours
    function loadBusinessHours() {
        const container = document.getElementById('business-hours-container');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2"></div>
                <div class="text-sm">Loading...</div>
            </div>
        `;
        
        fetch('/api/calendar/all-business-hours')
            .then(response => response.json())
            .then(data => {
                container.innerHTML = '';
                
                const daysOfWeek = [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 
                    'Thursday', 'Friday', 'Saturday'
                ];
                
                // Sort by day of week
                data.sort((a, b) => a.day_of_week - b.day_of_week);
                
                // Create the form elements
                data.forEach(day => {
                    const dayEl = document.createElement('div');
                    dayEl.className = 'business-hour-day p-4 border border-border-color rounded mb-3';
                    
                    const openTime = day.open_time ? day.open_time.substring(0, 5) : '09:00';
                    const closeTime = day.close_time ? day.close_time.substring(0, 5) : '17:00';
                    
                    dayEl.innerHTML = `
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-bold text-primary-text">${daysOfWeek[day.day_of_week]}</h3>
                            <label class="switch">
                                <input type="checkbox" name="is_open_${day.day_of_week}" ${!day.is_closed ? 'checked' : ''}>
                                <span class="slider"></span>
                                <span class="ml-2 text-sm text-primary-text">${!day.is_closed ? 'Open' : 'Closed'}</span>
                            </label>
                        </div>
                        
                        <div class="hours-inputs ${day.is_closed ? 'hidden' : 'flex'} space-x-3 flex-wrap sm:flex-nowrap">
                            <div class="w-full sm:w-1/2 mb-2 sm:mb-0">
                                <label class="block text-sm font-medium mb-1 text-primary-text">Open Time</label>
                                <input type="time" name="open_time_${day.day_of_week}" value="${openTime}" class="form-input w-full bg-background text-primary-text border border-border-color" ${day.is_closed ? 'disabled' : ''}>
                            </div>
                            <div class="w-full sm:w-1/2">
                                <label class="block text-sm font-medium mb-1 text-primary-text">Close Time</label>
                                <input type="time" name="close_time_${day.day_of_week}" value="${closeTime}" class="form-input w-full bg-background text-primary-text border border-border-color" ${day.is_closed ? 'disabled' : ''}>
                            </div>
                        </div>
                    `;
                    
                    container.appendChild(dayEl);
                    
                    // Add toggle event for checkbox
                    const checkbox = dayEl.querySelector(`input[name="is_open_${day.day_of_week}"]`);
                    const hoursInputs = dayEl.querySelector('.hours-inputs');
                    const timeInputs = dayEl.querySelectorAll('input[type="time"]');
                    const label = dayEl.querySelector('.slider + span');
                    
                    checkbox.addEventListener('change', function() {
                        const isOpen = this.checked;
                        if (isOpen) {
                            hoursInputs.classList.remove('hidden');
                            hoursInputs.classList.add('flex');
                            timeInputs.forEach(input => input.disabled = false);
                            label.textContent = 'Open';
                        } else {
                            hoursInputs.classList.add('hidden');
                            hoursInputs.classList.remove('flex');
                            timeInputs.forEach(input => input.disabled = true);
                            label.textContent = 'Closed';
                        }
                    });
                });
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="text-center py-4">
                        <p class="text-red-500">Failed to load business hours</p>
                        <button id="retry-load-hours-modal" class="text-sm text-accent hover:underline mt-2">Retry</button>
                    </div>
                `;
                document.getElementById('retry-load-hours-modal').addEventListener('click', loadBusinessHours);
                console.error('Error:', error);
            });
    }
    
    // Handle Business Hours Form
    document.getElementById('business-hours-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const hours = [];
        const saveBtn = document.getElementById('save-hours-btn');
        const originalBtnText = saveBtn.innerHTML;
        
        // Disable button during save
        saveBtn.disabled = true;
        saveBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Saving...
        `;
        
        // Collect all business hours data
        for (let i = 0; i < 7; i++) {
            const isOpen = document.querySelector(`input[name="is_open_${i}"]`).checked;
            const openTime = document.querySelector(`input[name="open_time_${i}"]`).value;
            const closeTime = document.querySelector(`input[name="close_time_${i}"]`).value;
            
            hours.push({
                day_of_week: i,
                is_closed: !isOpen,
                open_time: openTime,
                close_time: closeTime
            });
        }
        
        fetch('/admin/calendar/business-hours', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ hours })
        })
        .then(response => response.json())
        .then(data => {
            // Re-enable button
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
            
            if (data.success) {
                calendar.refetchEvents();
                loadBusinessHoursSummary();
                closeModal(businessHoursModal);
                safeShowToast('Business hours updated successfully', 'success');
            } else {
                safeShowToast(data.message || 'Failed to update business hours', 'error');
            }
        })
        .catch(error => {
            // Re-enable button
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnText;
            
            safeShowToast('An error occurred while updating hours', 'error');
            console.error('Error:', error);
        });
    });
    
    // Handle booking slots
    const slotDate = document.getElementById('slot-date');
    const dateStatus = document.getElementById('date-status');
    const slotsContainer = document.getElementById('slots-container');
    
    slotDate.addEventListener('change', loadBookingSlots);
    document.getElementById('refresh-slots-btn').addEventListener('click', loadBookingSlots);
    
    function loadBookingSlots() {
        const selectedDate = slotDate.value;
        
        if (!selectedDate) {
            slotsContainer.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-secondary-text">Select a date to view available slots</p>
                </div>
            `;
            dateStatus.textContent = 'Select a date to view status';
            return;
        }
        
        slotsContainer.innerHTML = `
            <div class="text-center py-12">
                <div class="animate-spin w-8 h-8 border-4 border-accent border-t-transparent rounded-full mx-auto mb-2"></div>
                <div class="text-sm">Loading slots...</div>
            </div>
        `;
        
        fetch(`/api/calendar/booking-slots?date=${selectedDate}`)
            .then(response => response.json())
            .then(data => {
                if (!data.available) {
                    // Date is unavailable
                    slotsContainer.innerHTML = `
                        <div class="text-center py-12 px-4">
                            <svg class="w-16 h-16 text-red-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-xl font-bold text-red-600 mb-2">Date Not Available</p>
                            <p class="text-secondary-text">${data.message || data.reason || 'This date is not available for booking'}</p>
                        </div>
                    `;
                    
                    dateStatus.innerHTML = `
                        <div class="flex items-center gap-2 px-3 py-2 bg-red-100 text-red-700 rounded-lg border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="font-semibold text-sm">Not Available</span>
                        </div>
                    `;
                    return;
                }
                
                // Date is available - calculate statistics
                const totalSlots = data.slots.length;
                const availableSlots = data.slots.filter(s => s.is_available && (parseInt(s.current_bookings) || 0) < (parseInt(s.max_bookings) || 1)).length;
                const bookedSlots = totalSlots - availableSlots;
                const availabilityPercentage = totalSlots > 0 ? Math.round((availableSlots / totalSlots) * 100) : 0;
                
                // Determine status based on availability
                let statusHTML;
                if (availabilityPercentage === 0) {
                    statusHTML = `
                        <div class="flex items-center gap-2 px-3 py-2 bg-red-100 text-red-700 rounded-lg border border-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <div class="flex flex-col">
                                <span class="font-semibold text-sm">Fully Booked</span>
                                <span class="text-xs opacity-90">0 of ${totalSlots} slots available</span>
                            </div>
                        </div>
                    `;
                } else if (availabilityPercentage <= 30) {
                    statusHTML = `
                        <div class="flex items-center gap-2 px-3 py-2 bg-orange-100 text-orange-700 rounded-lg border border-orange-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="flex flex-col">
                                <span class="font-semibold text-sm">Limited Availability</span>
                                <span class="text-xs opacity-90">${availableSlots} of ${totalSlots} slots available</span>
                            </div>
                        </div>
                    `;
                } else {
                    statusHTML = `
                        <div class="flex items-center gap-2 px-3 py-2 bg-green-100 text-green-700 rounded-lg border border-green-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div class="flex flex-col">
                                <span class="font-semibold text-sm">Available</span>
                                <span class="text-xs opacity-90">${availableSlots} of ${totalSlots} slots available</span>
                            </div>
                        </div>
                    `;
                }
                
                dateStatus.innerHTML = statusHTML;
                
                if (!data.slots || data.slots.length === 0) {
                    slotsContainer.innerHTML = `
                        <div class="text-center py-12 px-4">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-lg font-medium text-primary-text mb-2">No Slots Found</p>
                            <p class="text-secondary-text">No time slots are configured for this date.</p>
                        </div>
                    `;
                    return;
                }
                
                // Display slots
                slotsContainer.innerHTML = '';
                data.slots.forEach(slot => {
                    const slotEl = document.createElement('div');
                    slotEl.className = 'slot-item p-4 border-b last:border-b-0 border-border-color hover:bg-accent/5 transition-all duration-200';
                    
                    // Format time
                    const timeSlot = new Date(`2000-01-01T${slot.time_slot}`);
                    const formattedTime = timeSlot.toLocaleTimeString('en-US', { 
                        hour: 'numeric', 
                        minute: '2-digit',
                        hour12: true
                    });
                    
                    // Determine availability status
                    const currentBookings = parseInt(slot.current_bookings) || 0;
                    const maxBookings = parseInt(slot.max_bookings) || 1;
                    const isAvailable = slot.is_available && currentBookings < maxBookings;
                    
                    // Calculate fill percentage
                    const fillPercentage = maxBookings > 0 ? (currentBookings / maxBookings) * 100 : 0;
                    
                    // Determine status color and text
                    let statusClass, statusBadgeClass, statusText, statusIcon, indicatorClass;
                    
                    if (!isAvailable || currentBookings >= maxBookings) {
                        statusClass = 'text-red-600';
                        statusBadgeClass = 'bg-red-100 text-red-700 border border-red-200';
                        statusText = 'Fully Booked';
                        indicatorClass = 'bg-red-500';
                        statusIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>`;
                    } else if (fillPercentage >= 80) {
                        statusClass = 'text-orange-600';
                        statusBadgeClass = 'bg-orange-100 text-orange-700 border border-orange-200';
                        statusText = 'Limited Slots';
                        indicatorClass = 'bg-orange-500';
                        statusIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>`;
                    } else {
                        statusClass = 'text-green-600';
                        statusBadgeClass = 'bg-green-100 text-green-700 border border-green-200';
                        statusText = 'Available';
                        indicatorClass = 'bg-green-500';
                        statusIcon = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>`;
                    }
                    
                    // Build the slot HTML
                    slotEl.innerHTML = `
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <!-- Status Indicator -->
                                <div class="flex items-center justify-center">
                                    <span class="w-2.5 h-2.5 rounded-full ${indicatorClass} animate-pulse"></span>
                                </div>
                                
                                <!-- Time Information -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold text-primary-text text-base">${formattedTime}</span>
                                    </div>
                                    
                                    <!-- Booking Progress Bar -->
                                    <div class="mt-2">
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="text-secondary-text">Capacity</span>
                                            <span class="font-medium text-primary-text">${currentBookings}/${maxBookings} booked</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 ${
                                                fillPercentage >= 100 ? 'bg-red-500' : 
                                                fillPercentage >= 80 ? 'bg-orange-500' : 
                                                'bg-green-500'
                                            }" style="width: ${Math.min(fillPercentage, 100)}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="ml-4">
                                <span class="${statusBadgeClass} px-3 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap shadow-sm">
                                    ${statusIcon}
                                    ${statusText}
                                </span>
                            </div>
                        </div>
                    `;
                    
                    slotsContainer.appendChild(slotEl);
                });
            })
            .catch(error => {
                slotsContainer.innerHTML = `
                    <div class="text-center py-12">
                        <p class="text-red-500">Failed to load booking slots</p>
                        <button id="retry-load-slots" class="text-sm text-accent hover:underline mt-2">Retry</button>
                    </div>
                `;
                document.getElementById('retry-load-slots').addEventListener('click', loadBookingSlots);
                console.error('Error:', error);
            });
    }
    
    // Modal functions
    function openModal(modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.modal-content').classList.remove('opacity-0', '-translate-y-4');
        }, 10);
    }
    
    function closeModal(modal) {
        const content = modal.querySelector('.modal-content');
        content.classList.add('opacity-0', '-translate-y-4');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    // Note: Toast function is now provided globally via toast.js
    // Use window.safeShowToast(message, type, duration) for notifications
    
    // Function to show delete unavailable date confirmation modal
    function showDeleteUnavailableDateModal(id, dateRange, reason) {
        const modal = document.getElementById('delete-unavailable-date-modal');
        const dateRangeEl = document.getElementById('delete-date-range');
        const reasonEl = document.getElementById('delete-date-reason');
        const reasonContainer = document.getElementById('delete-date-reason-container');
        const confirmBtn = document.getElementById('confirm-delete-unavailable-date');
        
        // Set the date range
        dateRangeEl.textContent = dateRange;
        
        // Set the reason if it exists and is not a default message
        if (reason && reason.trim() && reason !== 'Studio unavailable') {
            reasonEl.textContent = reason;
            reasonContainer.classList.remove('hidden');
        } else {
            reasonContainer.classList.add('hidden');
        }
        
        // Remove any existing click listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add click listener for confirmation
        newConfirmBtn.addEventListener('click', function() {
            // Disable button to prevent double-clicks
            newConfirmBtn.disabled = true;
            newConfirmBtn.innerHTML = `
                <span class="flex items-center">
                    <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Removing...
                </span>
            `;
            
            // Perform the delete operation
            fetch(`/admin/calendar/unavailable-dates/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    closeModal(modal);
                    
                    // Refresh calendar and list
                    calendar.refetchEvents();
                    loadUnavailableDatesList();
                    safeShowToast('Unavailable dates removed successfully', 'success');
                } else {
                    safeShowToast(data.message || 'Failed to remove unavailable dates', 'error');
                }
                
                // Re-enable button
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = `
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Yes, Remove Date
                    </span>
                `;
            })
            .catch(error => {
                safeShowToast('An error occurred while removing dates', 'error');
                console.error('Error:', error);
                
                // Re-enable button
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = `
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Yes, Remove Date
                    </span>
                `;
            });
        });
        
        // Show the modal
        openModal(modal);
    }
    
    // Initialize
    loadUnavailableDatesList();
    loadBusinessHoursSummary();
    
    // Set default date for slot management to today
    const today = new Date();
    slotDate.valueAsDate = today;
    
    // Handle date availability modal functionality
    function showDateAvailabilityModal(date, dateStr) {
        const modal = document.getElementById('date-availability-modal');
        const selectedDateDisplay = document.getElementById('selected-date-display');
        const currentDateStatus = document.getElementById('current-date-status');
        const makeUnavailableSection = document.getElementById('make-unavailable-section');
        const alreadyUnavailableSection = document.getElementById('already-unavailable-section');
        const actionButton = document.getElementById('date-availability-action');
        const actionButtonText = document.getElementById('availability-action-text');
        const unavailableReason = document.getElementById('unavailable-reason');
        const availabilityReason = document.getElementById('availability-reason');
        
        // Reset form
        document.getElementById('date-availability-form').reset();
        
        // Set the selected date display
        selectedDateDisplay.textContent = formatDate(date);
        
        // Show loading status
        currentDateStatus.textContent = 'Checking availability...';
        currentDateStatus.className = 'px-3 py-2 rounded text-center font-medium bg-gray-100';
        
        // First check if the date is already unavailable
        fetch(`/api/calendar/check-availability?date=${dateStr}`)
            .then(response => response.json())
            .then(data => {
                if (data.isAvailable) {
                    // Date is available, show make unavailable option
                    currentDateStatus.textContent = 'Available for booking';
                    currentDateStatus.className = 'px-3 py-2 rounded text-center font-medium bg-green-100 text-green-800';
                    
                    makeUnavailableSection.classList.remove('hidden');
                    alreadyUnavailableSection.classList.add('hidden');
                    
                    actionButton.className = 'btn-red flex items-center justify-center';
                    actionButtonText.textContent = 'Mark as Unavailable';
                    
                    // Set up action button
                    actionButton.onclick = function() {
                        markDateAsUnavailable(dateStr, availabilityReason.value);
                    };
                } else {
                    // Date is unavailable, show make available option
                    currentDateStatus.textContent = 'Currently Unavailable';
                    currentDateStatus.className = 'px-3 py-2 rounded text-center font-medium bg-red-100 text-red-800';
                    
                    makeUnavailableSection.classList.add('hidden');
                    alreadyUnavailableSection.classList.remove('hidden');
                    
                    // Display reason if available
                    unavailableReason.textContent = data.reason || 'This date is marked as unavailable';
                    
                    actionButton.className = 'btn-green flex items-center justify-center';
                    actionButtonText.textContent = 'Make Available Again';
                    
                    // Set up action button with the unavailable date ID
                    actionButton.onclick = function() {
                        makeDataAvailable(data.unavailableId);
                    };
                }
            })
            .catch(error => {
                currentDateStatus.textContent = 'Error checking availability';
                currentDateStatus.className = 'px-3 py-2 rounded text-center font-medium bg-red-100 text-red-800';
                console.error('Error:', error);
            });
        
        openModal(modal);
    }
    
    // Function to mark date as unavailable
    function markDateAsUnavailable(dateStr, reason) {
        const actionButton = document.getElementById('date-availability-action');
        const originalBtnText = actionButton.innerHTML;
        
        actionButton.disabled = true;
        actionButton.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        fetch('/admin/calendar/unavailable-dates', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                start_date: dateStr,
                end_date: dateStr,
                reason: reason
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw err;
                });
            }
            return response.json();
        })
        .then(data => {
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;
            
            if (data.success) {
                // Close modal
                closeModal(document.getElementById('date-availability-modal'));
                
                // Refresh calendar and list
                calendar.refetchEvents();
                loadUnavailableDatesList();
                safeShowToast('Date marked as unavailable successfully', 'success');
            } else {
                safeShowToast(data.message || 'Failed to mark date as unavailable', 'error');
            }
        })
        .catch(error => {
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;
            
            // Handle validation errors
            if (error.errors) {
                const errorMessages = Object.values(error.errors).flat();
                safeShowToast(errorMessages.join('<br>'), 'error');
            } else if (error.message) {
                safeShowToast(error.message, 'error');
            } else {
                safeShowToast('An error occurred. Please try again.', 'error');
            }
            console.error('Error:', error);
        });
    }
    
    // Function to make date available again
    function makeDataAvailable(unavailableId) {
        // Validate ID before proceeding
        if (!unavailableId || unavailableId === 'undefined' || unavailableId === 'null') {
            safeShowToast('Invalid unavailable date ID. Please refresh the page and try again.', 'error');
            return;
        }
        
        const actionButton = document.getElementById('date-availability-action');
        const originalBtnText = actionButton.innerHTML;
        
        actionButton.disabled = true;
        actionButton.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...
        `;
        
        fetch(`/admin/calendar/unavailable-dates/${unavailableId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;
            
            if (data.success) {
                // Close modal
                closeModal(document.getElementById('date-availability-modal'));
                
                // Refresh calendar and list
                calendar.refetchEvents();
                loadUnavailableDatesList();
                safeShowToast('Date is now available for booking', 'success');
            } else {
                safeShowToast(data.message || 'Failed to make date available', 'error');
            }
        })
        .catch(error => {
            actionButton.disabled = false;
            actionButton.innerHTML = originalBtnText;
            safeShowToast('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        });
    }
});
