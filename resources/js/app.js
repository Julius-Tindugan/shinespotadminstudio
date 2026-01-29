import { Chart } from 'chart.js/auto';
// FullCalendar imports
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import { showBookingDetailsPopover, showNewBookingForm, showToast } from './calendar-functions';
import './booking-status';
import './booking-debug';
import { getServiceDetails, getServicesByCategory } from './services/services';
import './services/booking-services';

// Make service functions available globally
window.serviceUtils = {
    getServiceDetails,
    getServicesByCategory
};

document.addEventListener('DOMContentLoaded', () => {

    // Login Form Functionality
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const submitButton = loginForm.querySelector('button[type="submit"]');

        // Simple form validation
        loginForm.addEventListener('submit', (e) => {
           
        });
    }

    function clearInputError(input) {
        const parentDiv = input.closest('div').parentElement;
        const errorElement = parentDiv.querySelector('.error-message');

        input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');

        if (errorElement) {
            errorElement.remove();
        }
    }

    // --- Core UI Functionality ---

        // Remaining app-specific functionality that doesn't conflict with UI Manager

    // Activity Feed Tabs with smooth transitions

    // Quick Actions panel (replaces notifications)
    const quickActionsBtn = document.getElementById('quick-actions-btn');
    const quickActionsPanel = document.getElementById('quick-actions-panel');

    function showQuickActions() {
        quickActionsPanel.classList.remove('hidden');
        setTimeout(() => {
            quickActionsPanel.classList.remove('opacity-0', 'translate-y-2');
            quickActionsPanel.classList.add('opacity-100', 'translate-y-0');
            quickActionsBtn.setAttribute('aria-expanded', 'true');
        }, 10);
    }

    function hideQuickActions() {
        quickActionsPanel.classList.add('opacity-0', 'translate-y-2');
        quickActionsPanel.classList.remove('opacity-100', 'translate-y-0');
        quickActionsBtn.setAttribute('aria-expanded', 'false');
        setTimeout(() => {
            quickActionsPanel.classList.add('hidden');
        }, 200);
    }

    quickActionsBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (!quickActionsPanel || quickActionsPanel.classList.contains('hidden')) {
            showQuickActions();
        } else {
            hideQuickActions();
        }
    });

    // Close quick actions when clicking outside
    document.addEventListener('click', (e) => {
        if (quickActionsPanel && !quickActionsPanel.contains(e.target) && e.target !== quickActionsBtn) {
            if (!quickActionsPanel.classList.contains('hidden')) hideQuickActions();
        }
    });

    // Close quick actions with ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            if (quickActionsPanel && !quickActionsPanel.classList.contains('hidden')) hideQuickActions();
        }
    });

    // Activity Feed Tabs with smooth transitions
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Deactivate all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active-tab', 'text-accent', 'border-accent', 'border-b-2');
                btn.classList.add('text-secondary-text', 'dark:text-dark-secondary-text');
            });

            // Activate clicked button
            button.classList.add('active-tab', 'text-accent', 'border-accent', 'border-b-2');
            button.classList.remove('text-secondary-text', 'dark:text-dark-secondary-text');

            // Hide all tab contents with fade effect
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('animate-fade-in');
            });

            // Show selected tab content with fade effect
            const selectedContent = document.getElementById(button.dataset.tab);
            selectedContent.classList.remove('hidden');
            setTimeout(() => {
                selectedContent.classList.add('animate-fade-in');
            }, 10);
        });
    });

    // --- Modal Management ---
    // Listen for clicks on any element with data-modal-target attribute
    document.addEventListener('click', (e) => {
        const modalTrigger = e.target.closest('[data-modal-target]');
        if (modalTrigger) {
            e.preventDefault();
            const modalId = modalTrigger.dataset.modalTarget;
            openModal(modalId);

            // Special handling for specific modals
            if (modalId === 'view-booking-modal' && modalTrigger.dataset.bookingId) {
                // In a real app, you'd load booking details from the backend
                updateBookingModalContent(modalTrigger.dataset.bookingId);
            } else if (modalId === 'client-modal' && modalTrigger.dataset.clientId) {
                // In a real app, you'd load client details from the backend
                updateClientModalContent(modalTrigger.dataset.clientId);
            } else if (modalId === 'task-modal' && modalTrigger.dataset.taskId) {
                // In a real app, you'd load task details from the backend
                updateTaskModalContent(modalTrigger.dataset.taskId);
            }
        }
    });

    // Close modal when clicking on modal-close buttons
    document.addEventListener('click', (e) => {
        const closeBtn = e.target.closest('.modal-close');
        if (closeBtn) {
            const modal = closeBtn.closest('.fixed.inset-0');
            if (modal) {
                closeModal(modal.id);
            }
        }
    });

    // Close modal on backdrop click
    document.querySelectorAll('.fixed.inset-0').forEach(modal => {
        modal.addEventListener('click', event => {
            if (event.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    // Close modal on ESC key
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.fixed.inset-0:not(.hidden)');
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });

    /**
     * Open a modal by ID
     */
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Show the modal
        modal.classList.remove('hidden');

        // Animate the modal content
        setTimeout(() => {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.classList.remove('opacity-0', 'scale-95');
                modalContent.classList.add('opacity-100', 'scale-100');
            }
        }, 10);

        // Prevent body scrolling
        document.body.classList.add('overflow-hidden');

        // Initialize specific modal functionality
        switch(modalId) {
            case 'settings-modal':
                initSettingsTabs();
                break;
            case 'calendar-modal':
                initializeCalendar();
                break;
            case 'task-modal':
                // Initialize task modal functionality if needed
                break;
        }
    }

    /**
     * Close a modal by ID
     */
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Animate the modal content
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.classList.remove('opacity-100', 'scale-100');
            modalContent.classList.add('opacity-0', 'scale-95');
        }

        // Hide the modal after animation
        setTimeout(() => {
            modal.classList.add('hidden');

            // Allow body scrolling again
            document.body.classList.remove('overflow-hidden');
        }, 200);
    }

    /**
     * Initialize tabs in settings modal
     */
    function initSettingsTabs() {
        document.querySelectorAll('.settings-tabs button').forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.getAttribute('data-tab');

                // Deactivate all tabs
                document.querySelectorAll('.settings-tabs button').forEach(t => {
                    t.classList.remove('bg-background', 'dark:bg-dark-background');
                    t.classList.add('text-secondary-text', 'dark:text-dark-secondary-text', 'hover:text-primary-text', 'dark:hover:text-dark-primary-text', 'hover:bg-background', 'dark:hover:bg-dark-background');
                    t.querySelector('svg').classList.remove('text-accent');
                    t.querySelector('svg').classList.add('text-secondary-text', 'dark:text-dark-secondary-text');
                });

                // Activate current tab
                tab.classList.remove('text-secondary-text', 'dark:text-dark-secondary-text', 'hover:text-primary-text', 'dark:hover:text-dark-primary-text', 'hover:bg-background', 'dark:hover:bg-dark-background');
                tab.classList.add('bg-background', 'dark:bg-dark-background');
                tab.querySelector('svg').classList.remove('text-secondary-text', 'dark:text-dark-secondary-text');
                tab.querySelector('svg').classList.add('text-accent');

                // Hide all panels
                document.querySelectorAll('.settings-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });

                // Show current panel
                document.getElementById(`${target}-panel`).classList.remove('hidden');
            });
        });
    }

    /**
     * Update booking modal content with data from the server
     * In a real app, this would fetch data from an API
     */
    function updateBookingModalContent(bookingId) {
        console.log(`Loading booking data for ID: ${bookingId}`);
        // This is where you would make an API request
        // For demo purposes, we'll just update some elements directly
        document.getElementById('booking-client-name').textContent = 'Sarah Johnson';
        document.getElementById('booking-date').textContent = 'August 25, 2025';
    }

    /**
     * Update client modal content with data from the server
     * In a real app, this would fetch data from an API
     */
    function updateClientModalContent(clientId) {
        console.log(`Loading client data for ID: ${clientId}`);
        // This is where you would make an API request
    }

    /**
     * Update task modal content with data from the server
     * In a real app, this would fetch data from an API
     */
    function updateTaskModalContent(taskId) {
        console.log(`Loading task data for ID: ${taskId}`);
        // This is where you would make an API request
    };

    // --- Toast Notifications System ---
    /**
     * Show a toast notification
     * @param {string} title - The toast title
     * @param {string} message - The toast message
     * @param {string} type - The toast type: success, error, warning, info
     * @param {number} duration - How long to show the toast in milliseconds
     */
    const showToast = (title, message, type = 'success', duration = 5000) => {
        const container = document.getElementById('toast-container');
        if (!container) return;

        // Use our toast template
        const template = document.getElementById('toast-template');
        if (!template) return;

        // Clone the template
        const toast = template.content.cloneNode(true).querySelector('.toast');

        // Set toast content
        toast.querySelector('.toast-title').textContent = title;
        toast.querySelector('.toast-message').textContent = message;

        // Set icon based on toast type
        const iconElement = toast.querySelector('.toast-icon');
        if (iconElement) {
            const iconSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            iconSvg.setAttribute('class', 'w-5 h-5');
            iconSvg.setAttribute('fill', 'none');
            iconSvg.setAttribute('stroke', 'currentColor');
            iconSvg.setAttribute('viewBox', '0 0 24 24');

            const iconPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            iconPath.setAttribute('stroke-linecap', 'round');
            iconPath.setAttribute('stroke-linejoin', 'round');
            iconPath.setAttribute('stroke-width', '2');

            switch (type) {
                case 'success':
                    iconElement.classList.add('text-green-500');
                    iconPath.setAttribute('d', 'M5 13l4 4L19 7');
                    break;
                case 'error':
                    iconElement.classList.add('text-red-500');
                    iconPath.setAttribute('d', 'M6 18L18 6M6 6l12 12');
                    break;
                case 'warning':
                    iconElement.classList.add('text-yellow-500');
                    iconPath.setAttribute('d', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z');
                    break;
                case 'info':
                    iconElement.classList.add('text-blue-500');
                    iconPath.setAttribute('d', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z');
                    break;
            }

            iconSvg.appendChild(iconPath);
            iconElement.appendChild(iconSvg);
        }

        // Set up progress bar
        const progressBar = toast.querySelector('.toast-progress');
        if (progressBar) {
            // Animation for progress bar
            progressBar.style.transition = `width ${duration}ms linear`;

            // Wait for DOM to be updated
            setTimeout(() => {
                progressBar.style.width = '0%';
            }, 10);
        }

        // Add to container
        container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('opacity-0', 'translate-x-full');
            toast.classList.add('opacity-100', 'translate-x-0');
        }, 10);

        // Set up close button
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                removeToast(toast);
            });
        }

        // Auto-remove after duration
        const timeoutId = setTimeout(() => {
            removeToast(toast);
        }, duration);

        // Store timeout ID on the toast element so we can clear it if needed
        toast._timeoutId = timeoutId;

        // Return the toast element in case we want to manipulate it later
        return toast;
    };

    const removeToast = (toast) => {
        if (!toast) return;

        // Clear any existing timeout
        if (toast._timeoutId) {
            clearTimeout(toast._timeoutId);
        }

        // Animate out
        toast.classList.remove('opacity-100', 'translate-x-0');
        toast.classList.add('opacity-0', 'translate-x-full');

        // Remove from DOM after animation completes
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    };

    // Function to load booking details from server
    const loadBookingDetails = (bookingId) => {
        // In a real application, this would be an API call
        // For now, we'll simulate loading different data based on the ID

        const bookings = {
            '1': {
                client: {
                    name: 'Sarah Johnson',
                    email: 'sarahjohnson@example.com',
                    phone: '+63 912 345 6789'
                },
                package: 'Wedding Photography Package',
                addons: ['Extra Prints', 'Photo Album'],
                date: '2025-08-25',
                timeSlot: '10:00 AM - 12:00 PM',
                payment: {
                    amount: '₱25,000',
                    status: 'Paid',
                    method: 'Credit Card'
                },
                status: 'Confirmed',
                notes: 'Client prefers black and white shots for portrait series.'
            },
            '2': {
                client: {
                    name: 'Michael Chen',
                    email: 'michaelchen@example.com',
                    phone: '+63 917 123 4567'
                },
                package: 'Family Photoshoot',
                addons: ['Digital Files'],
                date: '2025-08-25',
                timeSlot: '02:00 PM - 03:00 PM',
                payment: {
                    amount: '₱8,500',
                    status: 'Pending',
                    method: 'Bank Transfer'
                },
                status: 'Pending',
                notes: 'Family of 5 with young children.'
            },
            '3': {
                client: {
                    name: 'Lisa Brown',
                    email: 'lisabrown@example.com',
                    phone: '+63 919 876 5432'
                },
                package: 'Maternity Session',
                addons: ['Makeup Service', 'Digital Files'],
                date: '2025-08-27',
                timeSlot: '03:30 PM - 04:30 PM',
                payment: {
                    amount: '₱12,000',
                    status: 'Paid (50%)',
                    method: 'GCash'
                },
                status: 'Confirmed',
                notes: 'Prefers outdoor setting with natural lighting.'
            },
            '4': {
                client: {
                    name: 'David Wilson',
                    email: 'davidwilson@example.com',
                    phone: '+63 918 765 4321'
                },
                package: 'Corporate Headshots',
                addons: ['Rush Processing', 'LinkedIn Optimization'],
                date: '2025-08-29',
                timeSlot: '11:00 AM - 11:30 AM',
                payment: {
                    amount: '₱5,500',
                    status: 'Completed',
                    method: 'Company Account'
                },
                status: 'Completed',
                notes: 'For company website and marketing materials.'
            }
        };

        // Get the booking data
        const booking = bookings[bookingId];

        if (booking) {
            // Fill the booking details modal with this data
            const modal = document.getElementById('view-booking-modal');
            if (!modal) return;

            // Update client info
            modal.querySelector('#booking-client-name').textContent = booking.client.name;
            modal.querySelector('#booking-client-email').textContent = booking.client.email;
            modal.querySelector('#booking-client-phone').textContent = booking.client.phone;

            // Update booking details
            modal.querySelector('#booking-package').textContent = booking.package;
            modal.querySelector('#booking-date').textContent = booking.date;
            modal.querySelector('#booking-time').textContent = booking.timeSlot;

            // Update payment info
            modal.querySelector('#booking-payment-amount').textContent = booking.payment.amount;
            modal.querySelector('#booking-payment-status').textContent = booking.payment.status;
            modal.querySelector('#booking-payment-method').textContent = booking.payment.method;

            // Update status
            const statusElement = modal.querySelector('#booking-status');
            statusElement.textContent = booking.status;

            // Set status color
            statusElement.className = 'px-2 py-1 text-xs rounded-full';
            switch (booking.status) {
                case 'Confirmed':
                    statusElement.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-400');
                    break;
                case 'Pending':
                    statusElement.classList.add('bg-yellow-100', 'text-yellow-800', 'dark:bg-yellow-900/30', 'dark:text-yellow-400');
                    break;
                case 'Completed':
                    statusElement.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-400');
                    break;
                case 'Cancelled':
                    statusElement.classList.add('bg-red-100', 'text-red-800', 'dark:bg-red-900/30', 'dark:text-red-400');
                    break;
            }

            // Update addons list
            const addonsList = modal.querySelector('#booking-addons');
            addonsList.innerHTML = '';
            booking.addons.forEach(addon => {
                const li = document.createElement('li');
                li.className = 'flex items-center';
                li.innerHTML = `
                    <svg class="w-3 h-3 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    ${addon}
                `;
                addonsList.appendChild(li);
            });

            // Update notes
            modal.querySelector('#booking-notes').textContent = booking.notes;
        }
    };

    // --- Chart.js: Revenue Analytics ---
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        // Generate dates for the last 30 days
        const getDates = () => {
            const dates = [];
            for (let i = 30; i > 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                dates.push(d.getDate() + '/' + (d.getMonth() + 1));
            }
            return dates;
        };

        // Generate realistic looking revenue data
        const generateRevenueData = () => {
            const baseValue = 15000;
            const variance = 10000;
            const trend = 100; // Slight upward trend

            return Array.from({ length: 30 }, (_, i) => {
                // Weekend spike
                const dayOfWeek = (new Date().getDay() + i) % 7;
                const weekendBonus = (dayOfWeek === 0 || dayOfWeek === 6) ? variance * 0.5 : 0;

                // Random variance
                const randomVariance = (Math.random() - 0.5) * variance;

                // Upward trend + base value + random variance + weekend bonus
                return Math.max(0, baseValue + (i * trend) + randomVariance + weekendBonus);
            });
        };

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: getDates(),
                datasets: [{
                    label: 'Daily Revenue (₱)',
                    data: generateRevenueData(),
                    backgroundColor: '#D4AF37',
                    borderColor: '#C09A2E',
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverBackgroundColor: '#C09A2E',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    // --- FullCalendar.js: Calendar Management ---
    const initializeCalendar = () => {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            // Get disabled dates from local storage or initialize empty array
            let disabledDates = JSON.parse(localStorage.getItem('studio_disabled_dates') || '[]');

            // Initialize calendar with all features
            const calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                height: '100%',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [ // Sample Data
                    {
                        title: 'Wedding - S. Johnson',
                        start: '2025-08-25T10:00:00',
                        end: '2025-08-25T12:00:00',
                        backgroundColor: '#34D399',
                        borderColor: '#34D399',
                        extendedProps: {
                            client: 'Sarah Johnson',
                            package: 'Wedding Photography',
                            status: 'confirmed',
                            phone: '+63 912 345 6789'
                        }
                    },
                    {
                        title: 'Maternity - L. Brown',
                        start: '2025-08-27T15:30:00',
                        end: '2025-08-27T16:30:00',
                        backgroundColor: '#34D399',
                        borderColor: '#34D399',
                        extendedProps: {
                            client: 'Lisa Brown',
                            package: 'Maternity Session',
                            status: 'confirmed',
                            phone: '+63 919 876 5432'
                        }
                    },
                    {
                        title: 'Family Shoot - M. Chen',
                        start: '2025-08-25T14:00:00',
                        end: '2025-08-25T15:00:00',
                        backgroundColor: '#FBBF24',
                        borderColor: '#FBBF24',
                        extendedProps: {
                            client: 'Michael Chen',
                            package: 'Family Photoshoot',
                            status: 'pending',
                            phone: '+63 917 123 4567'
                        }
                    },
                    // Add disabled dates as events with special styling
                    ...disabledDates.map(date => ({
                        title: date.reason || 'Unavailable',
                        start: date.startDate,
                        end: date.endDate ? new Date(new Date(date.endDate).getTime() + 86400000).toISOString().split('T')[0] : undefined, // Add a day to make it inclusive
                        display: 'background',
                        backgroundColor: 'rgba(244, 63, 94, 0.4)',  // Red with transparency
                        classNames: ['unavailable-date'],
                        extendedProps: {
                            type: 'unavailable',
                            reason: date.reason || 'Studio Unavailable'
                        }
                    }))
                ],
                editable: true,
                eventClick: function(info) {
                    // Don't show booking details for unavailable dates
                    if (info.event.extendedProps.type === 'unavailable') {
                        return;
                    }

                    // Show booking details
                    showBookingDetailsPopover(info.event);
                },
                eventDrop: function(info) {
                    // Don't allow dragging unavailable dates
                    if (info.event.extendedProps.type === 'unavailable') {
                        info.revert();
                        return;
                    }

                    // Update booking date/time
                    const event = info.event;
                    showToast(`${event.title} rescheduled to ${event.start.toLocaleDateString()}`, 'info');
                },
                dateClick: function(info) {
                    // Check if date is unavailable
                    const isUnavailable = disabledDates.some(date => {
                        const clickedDate = info.date;
                        const startDate = new Date(date.startDate);
                        const endDate = date.endDate ? new Date(date.endDate) : new Date(date.startDate);

                        // Set time to 00:00:00 for accurate date comparison
                        clickedDate.setHours(0,0,0,0);
                        startDate.setHours(0,0,0,0);
                        endDate.setHours(0,0,0,0);

                        return clickedDate >= startDate && clickedDate <= endDate;
                    });

                    if (isUnavailable) {
                        showToast('This date is marked as unavailable', 'error');
                        return;
                    }

                    // Create new booking
                    showNewBookingForm(info.date);
                }
            });

            calendar.render();

            // Initialize unavailable date management
            initDisabledDatesManager(calendar, disabledDates);
        }
    };

    /**
     * Initialize the disabled dates manager
     */
    function initDisabledDatesManager(calendar, disabledDates) {
        // Reference UI elements
        const startDateInput = document.getElementById('disable-date-start');
        const endDateInput = document.getElementById('disable-date-end');
        const reasonInput = document.getElementById('unavailable-reason');
        const addButton = document.getElementById('add-disabled-date');
        const disabledDatesList = document.getElementById('disabled-dates-list');

        // Set initial dates to today and tomorrow
        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        startDateInput.valueAsDate = today;
        endDateInput.valueAsDate = tomorrow;

        // Initialize the list of disabled dates
        updateDisabledDatesList();

        // Add event listener for the add button
        addButton.addEventListener('click', () => {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const reason = reasonInput.value.trim();

            if (!startDate) {
                showToast('Please select a start date', 'error');
                return;
            }

            // Validate dates
            if (endDate && new Date(endDate) < new Date(startDate)) {
                showToast('End date cannot be before start date', 'error');
                return;
            }

            // Add to disabled dates
            const newDisabledDate = {
                id: Date.now().toString(),
                startDate: startDate,
                endDate: endDate || startDate,  // If no end date, use start date
                reason: reason
            };

            disabledDates.push(newDisabledDate);

            // Save to localStorage
            localStorage.setItem('studio_disabled_dates', JSON.stringify(disabledDates));

            // Clear inputs
            reasonInput.value = '';

            // Update the calendar
            calendar.getEvents().forEach(event => {
                if (event.extendedProps.type === 'unavailable') {
                    event.remove();
                }
            });

            // Add the new unavailable dates to the calendar
            disabledDates.forEach(date => {
                const endDateTime = new Date(new Date(date.endDate).getTime() + 86400000); // Add a day to make it inclusive
                calendar.addEvent({
                    title: date.reason || 'Unavailable',
                    start: date.startDate,
                    end: endDateTime.toISOString().split('T')[0],
                    display: 'background',
                    backgroundColor: 'rgba(244, 63, 94, 0.4)',
                    classNames: ['unavailable-date'],
                    extendedProps: {
                        type: 'unavailable',
                        reason: date.reason || 'Studio Unavailable'
                    }
                });
            });

            // Update the list of disabled dates
            updateDisabledDatesList();

            // Show success toast
            showToast('Date successfully marked as unavailable', 'success');
        });

        // Function to update the disabled dates list
        function updateDisabledDatesList() {
            disabledDatesList.innerHTML = '';

            if (disabledDates.length === 0) {
                disabledDatesList.innerHTML = `
                    <div class="text-secondary-text dark:text-dark-secondary-text text-center py-2">
                        No dates marked as unavailable
                    </div>
                `;
                return;
            }

            disabledDates.forEach(date => {
                const dateRangeText = date.startDate === date.endDate ?
                    new Date(date.startDate).toLocaleDateString() :
                    `${new Date(date.startDate).toLocaleDateString()} - ${new Date(date.endDate).toLocaleDateString()}`;

                const dateItem = document.createElement('div');
                dateItem.className = 'p-2 bg-card-bg dark:bg-dark-card-bg border border-border-color dark:border-dark-border-color rounded-md flex justify-between items-center';
                dateItem.innerHTML = `
                    <div>
                        <div class="font-medium">${dateRangeText}</div>
                        ${date.reason ? `<div class="text-xs text-secondary-text dark:text-dark-secondary-text">${date.reason}</div>` : ''}
                    </div>
                    <button class="text-red-500 hover:text-red-600" data-date-id="${date.id}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                `;
                disabledDatesList.appendChild(dateItem);

                // Add event listener to delete button
                dateItem.querySelector('button').addEventListener('click', (e) => {
                    const dateId = e.currentTarget.dataset.dateId;
                    disabledDates = disabledDates.filter(d => d.id !== dateId);

                    // Save to localStorage
                    localStorage.setItem('studio_disabled_dates', JSON.stringify(disabledDates));

                    // Update the calendar
                    calendar.getEvents().forEach(event => {
                        if (event.extendedProps.type === 'unavailable') {
                            event.remove();
                        }
                    });

                    // Add the updated unavailable dates to the calendar
                    disabledDates.forEach(date => {
                        const endDateTime = new Date(new Date(date.endDate).getTime() + 86400000); // Add a day to make it inclusive
                        calendar.addEvent({
                            title: date.reason || 'Unavailable',
                            start: date.startDate,
                            end: endDateTime.toISOString().split('T')[0],
                            display: 'background',
                            backgroundColor: 'rgba(244, 63, 94, 0.4)',
                            classNames: ['unavailable-date'],
                            extendedProps: {
                                type: 'unavailable',
                                reason: date.reason || 'Studio Unavailable'
                            }
                        });
                    });

                    // Update the list
                    updateDisabledDatesList();

                    // Show success toast
                    showToast('Date removed from unavailable dates', 'success');
                });
            });
        }
    }

    // --- Kanban Board Drag and Drop ---
    const initializeKanban = () => {
        const draggables = document.querySelectorAll('[draggable="true"]');
        const columns = document.querySelectorAll('.kanban-column');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                draggable.classList.add('dragging', 'opacity-50');
            });

            draggable.addEventListener('dragend', () => {
                draggable.classList.remove('dragging', 'opacity-50');
                // Show toast notification when task is moved
                const columnName = draggable.closest('.kanban-column').dataset.status;
                const taskName = draggable.querySelector('.task-name')?.textContent || 'Task';

                showToast(`"${taskName}" moved to ${columnName}`, 'success');
            });
        });

        columns.forEach(column => {
            column.addEventListener('dragover', e => {
                e.preventDefault();
                const afterElement = getDragAfterElement(column, e.clientY);
                const draggable = document.querySelector('.dragging');
                if (draggable) {
                    if (afterElement == null) {
                        column.appendChild(draggable);
                    } else {
                        column.insertBefore(draggable, afterElement);
                    }
                }
            });

            // Highlight column when dragging over
            column.addEventListener('dragenter', () => {
                column.classList.add('bg-gray-50', 'dark:bg-gray-800/50');
            });

            column.addEventListener('dragleave', () => {
                column.classList.remove('bg-gray-50', 'dark:bg-gray-800/50');
            });

            column.addEventListener('drop', () => {
                column.classList.remove('bg-gray-50', 'dark:bg-gray-800/50');
            });
        });
    }

    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('[draggable="true"]:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    // Initialize Kanban if needed
    const kanbanBoard = document.querySelector('.kanban-board');
    if (kanbanBoard) {
        initializeKanban();
    }
});
