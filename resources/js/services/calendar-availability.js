/**
 * Calendar Availability Module
 * 
 * Handles checking and displaying availability for booking dates
 */

/**
 * Check if a specific date is available for booking
 * @param {string} date - ISO format date string (YYYY-MM-DD)
 * @returns {Promise} - Promise resolving to availability data
 */
async function checkDateAvailability(date) {
    try {
        const response = await fetch(`/api/calendar/check-availability?date=${date}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error checking availability:', error);
        throw error;
    }
}

/**
 * Get all unavailable dates within a range (for calendar rendering)
 * @param {string} startDate - Start date in ISO format (YYYY-MM-DD)
 * @param {string} endDate - End date in ISO format (YYYY-MM-DD)
 * @returns {Promise} - Promise resolving to array of unavailable dates
 */
async function getUnavailableDates(startDate, endDate) {
    try {
        const response = await fetch(`/api/calendar/unavailable-dates?start=${startDate}&end=${endDate}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error getting unavailable dates:', error);
        throw error;
    }
}

/**
 * Get business hours for a specific day of week
 * @param {number} dayOfWeek - Day of week (0 = Sunday, 1 = Monday, etc.)
 * @returns {Promise} - Promise resolving to business hour data
 */
async function getBusinessHours(dayOfWeek) {
    try {
        const response = await fetch(`/api/calendar/business-hours?day=${dayOfWeek}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error getting business hours:', error);
        throw error;
    }
}

/**
 * Initialize a date picker with availability checking
 * @param {HTMLElement} element - The date picker input element
 * @param {object} options - Configuration options
 */
function initAvailabilityDatePicker(element, options = {}) {
    // Assuming you have a date picker library like flatpickr
    // This is a simplified example
    const datePicker = flatpickr(element, {
        minDate: "today",
        disable: [
            function(date) {
                // Initially, assume all dates are available
                return false;
            }
        ],
        onMonthChange: async function(selectedDates, dateStr, instance) {
            const currentMonth = instance.currentMonth;
            const currentYear = instance.currentYear;
            
            // Get first and last day of month
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            
            const startDate = firstDay.toISOString().split('T')[0];
            const endDate = lastDay.toISOString().split('T')[0];
            
            // Get unavailable dates for this month
            const unavailableDates = await getUnavailableDates(startDate, endDate);
            
            // Update the disabled dates
            instance.set('disable', [
                function(date) {
                    const dateString = date.toISOString().split('T')[0];
                    
                    // Check if date is in unavailable dates
                    if (unavailableDates.includes(dateString)) {
                        return true;
                    }
                    
                    // Check if day of week is closed
                    const dayOfWeek = date.getDay();
                    const businessHours = options.businessHours || [];
                    const dayData = businessHours.find(h => h.day_of_week === dayOfWeek);
                    
                    if (!dayData || dayData.is_closed) {
                        return true;
                    }
                    
                    return false;
                }
            ]);
        },
        ...options
    });
    
    return datePicker;
}

/**
 * Render a calendar with availability highlighting
 * @param {HTMLElement} element - The container element for the calendar
 * @param {object} options - Configuration options
 */
function renderAvailabilityCalendar(element, options = {}) {
    // Create a calendar using FullCalendar or similar library
    const calendar = new FullCalendar.Calendar(element, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        events: function(info, successCallback, failureCallback) {
            const start = info.start.toISOString().split('T')[0];
            const end = info.end.toISOString().split('T')[0];
            
            // Get unavailable dates
            getUnavailableDates(start, end)
                .then(unavailableDates => {
                    const events = unavailableDates.map(date => ({
                        start: date,
                        display: 'background',
                        backgroundColor: '#ffcccc',
                        classNames: ['unavailable-date'],
                    }));
                    
                    successCallback(events);
                })
                .catch(error => {
                    failureCallback(error);
                });
        },
        dateClick: function(info) {
            if (options.onDateClick) {
                // Check if date is available before calling handler
                checkDateAvailability(info.dateStr)
                    .then(data => {
                        if (data.available) {
                            options.onDateClick(info.dateStr, data);
                        } else {
                            // Show message that date is unavailable
                            showUnavailableMessage(data.reason);
                        }
                    });
            }
        },
        ...options
    });
    
    calendar.render();
    return calendar;
}

/**
 * Display a message when date is unavailable
 * @param {string} reason - Reason for unavailability
 */
function showUnavailableMessage(reason) {
    const message = document.createElement('div');
    message.className = 'unavailable-message';
    message.innerHTML = `
        <div class="p-3 bg-red-100 text-red-800 rounded-lg mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>This date is not available for booking. ${reason || ''}</span>
            </div>
        </div>
    `;
    
    // Insert at top of booking form or other appropriate place
    const container = document.querySelector('.booking-form-container');
    if (container) {
        container.insertBefore(message, container.firstChild);
        
        // Remove message after a few seconds
        setTimeout(() => {
            if (container.contains(message)) {
                container.removeChild(message);
            }
        }, 5000);
    }
}

// Export functions for use in other modules
export {
    checkDateAvailability,
    getUnavailableDates,
    getBusinessHours,
    initAvailabilityDatePicker,
    renderAvailabilityCalendar
};
