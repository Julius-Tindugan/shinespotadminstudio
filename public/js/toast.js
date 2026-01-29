/**
 * Global Toast Notification System
 * Provides consistent toast notifications across all management systems
 */

(function() {
    'use strict';

    /**
     * Show a toast notification
     * @param {string} message - The toast message (or title if second param is message)
     * @param {string} type - The toast type: success, error, warning, info
     * @param {number} duration - How long to show the toast in milliseconds (default: 5000)
     */
    window.showToast = function(message, type = 'success', duration = 5000) {
        const container = document.getElementById('toast-container');
        if (!container) {
            console.warn('Toast container not found');
            return;
        }

        const template = document.getElementById('toast-template');
        if (!template) {
            console.warn('Toast template not found');
            return;
        }

        // Clone the template
        const toast = template.content.cloneNode(true).querySelector('.toast');
        if (!toast) {
            console.warn('Toast element not found in template');
            return;
        }

        // Determine if message is title+message or just message
        let title, toastMessage;
        
        // If message contains a separator or is very long, split it
        if (typeof message === 'object' && message.title && message.message) {
            title = message.title;
            toastMessage = message.message;
        } else {
            // Use type as title and message as content
            const typeLabels = {
                'success': 'Success',
                'error': 'Error',
                'warning': 'Warning',
                'info': 'Information'
            };
            title = typeLabels[type] || 'Notification';
            toastMessage = message;
        }

        // Set toast content
        const titleElement = toast.querySelector('.toast-title');
        const messageElement = toast.querySelector('.toast-message');
        
        if (titleElement) titleElement.textContent = title;
        if (messageElement) messageElement.textContent = toastMessage;

        // Set icon based on toast type
        const iconElement = toast.querySelector('.toast-icon');
        if (iconElement) {
            const iconSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            iconSvg.setAttribute('class', 'w-6 h-6');
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
                    iconPath.setAttribute('d', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z');
                    break;
                case 'error':
                    iconElement.classList.add('text-red-500');
                    iconPath.setAttribute('d', 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z');
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
            iconElement.innerHTML = '';
            iconElement.appendChild(iconSvg);
        }

        // Set up progress bar
        const progressBar = toast.querySelector('.toast-progress');
        if (progressBar) {
            // Animation for progress bar
            progressBar.style.transition = `width ${duration}ms linear`;
            progressBar.style.width = '100%';

            // Start animation after a brief delay
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

        // Store timeout ID on the toast element
        toast._timeoutId = timeoutId;

        return toast;
    };

    /**
     * Remove a toast from the DOM
     * @param {HTMLElement} toast - The toast element to remove
     */
    function removeToast(toast) {
        if (!toast) return;

        // Clear any pending timeout
        if (toast._timeoutId) {
            clearTimeout(toast._timeoutId);
        }

        // Animate out
        toast.classList.remove('opacity-100', 'translate-x-0');
        toast.classList.add('opacity-0', 'translate-x-full');

        // Remove from DOM after animation
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }

    /**
     * Initialize toast system and check for session flash messages
     */
    function initializeToastSystem() {
        // Check for Laravel session flash messages
        const flashMessages = document.querySelectorAll('[data-flash-message]');
        
        flashMessages.forEach(function(element) {
            const message = element.getAttribute('data-flash-message');
            const type = element.getAttribute('data-flash-type') || 'info';
            
            if (message) {
                // Show toast after a brief delay to ensure DOM is ready
                setTimeout(() => {
                    showToast(message, type);
                }, 100);
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeToastSystem);
    } else {
        initializeToastSystem();
    }

    // Export for global access
    window.removeToast = removeToast;
    window.initializeToastSystem = initializeToastSystem;
})();
