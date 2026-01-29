/**
 * Export Report Functionality
 * Handles the export modal and report generation
 */

let currentExportType = 'dashboard';

/**
 * Open the export modal
 * @param {string} type - Type of report (dashboard or payment)
 */
function openExportModal(type) {
    console.log('openExportModal() called with type:', type);
    
    currentExportType = type;
    const modal = document.getElementById('exportReportModal');
    const exportTypeSection = document.getElementById('exportTypeSection');
    
    if (!modal) {
        console.error('Export modal not found');
        return;
    }
    
    console.log('Modal found:', modal);
    
    // Reset form
    resetExportForm();
    
    // Set default dates (current month)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    const startDateInput = document.getElementById('exportStartDate');
    const endDateInput = document.getElementById('exportEndDate');
    
    if (startDateInput) startDateInput.valueAsDate = firstDay;
    if (endDateInput) endDateInput.valueAsDate = lastDay;
    
    // Show/hide export type section based on context
    if (type === 'dashboard') {
        exportTypeSection?.classList.remove('hidden');
    } else {
        exportTypeSection?.classList.add('hidden');
    }
    
    // Show modal with animation
    modal.classList.remove('hidden');
    
    // Force reflow to ensure CSS changes take effect
    modal.offsetHeight;
    
    setTimeout(() => {
        modal.classList.add('opacity-100');
        modal.classList.remove('opacity-0');
        const content = modal.querySelector('.modal-content');
        if (content) {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }
    }, 10);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Add event listeners to radio buttons to ensure visual feedback
    setupRadioButtonListeners();
}

/**
 * Close the export modal
 */
function closeExportModal() {
    const modal = document.getElementById('exportReportModal');
    if (!modal) return;
    
    const content = modal.querySelector('.modal-content');
    if (content) {
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
    }
    
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        resetExportForm();
    }, 300);
    
    // Restore body scroll
    document.body.style.overflow = '';
}

/**
 * Reset the export form
 */
function resetExportForm() {
    // Reset format selection to PDF
    const pdfRadio = document.querySelector('input[name="exportFormat"][value="pdf"]');
    if (pdfRadio) {
        pdfRadio.checked = true;
    }
    
    // Hide error and progress
    document.getElementById('exportError').classList.add('hidden');
    document.getElementById('exportProgress').classList.add('hidden');
    
    // Enable export button
    const exportBtn = document.getElementById('exportButton');
    if (exportBtn) {
        exportBtn.disabled = false;
        exportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * Confirm and trigger export
 */
function confirmExport() {
    console.log('confirmExport() called');
    
    const format = document.querySelector('input[name="exportFormat"]:checked')?.value;
    const startDate = document.getElementById('exportStartDate')?.value;
    const endDate = document.getElementById('exportEndDate')?.value;
    
    console.log('Selected format:', format);
    console.log('Date range:', startDate, 'to', endDate);
    
    if (!format) {
        console.error('No format selected');
        showExportError('Please select an export format');
        return;
    }
    
    // Validate date range if provided
    if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (start > end) {
            showExportError('Start date must be before end date');
            return;
        }
        
        // Check if date range is too large (more than 1 year)
        const oneYear = 365 * 24 * 60 * 60 * 1000;
        if (end - start > oneYear) {
            showExportError('Date range cannot exceed one year');
            return;
        }
    }
    
    // Show progress
    showExportProgress();
    
    // Prepare form data
    const formData = new FormData();
    formData.append('format', format);
    if (startDate) formData.append('start_date', startDate);
    if (endDate) formData.append('end_date', endDate);
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showExportError('Security token not found. Please refresh the page.');
        hideExportProgress();
        return;
    }
    
    // Determine endpoint based on export type
    const endpoint = currentExportType === 'dashboard' 
        ? '/reports/export/dashboard' 
        : '/reports/export/payment';
    
    // Trigger export
    fetch(endpoint, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json, application/pdf, application/vnd.ms-excel, text/csv',
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || `Export failed (${response.status})`);
            }).catch(jsonError => {
                // If response is not JSON, use status text
                throw new Error(response.statusText || 'Export failed');
            });
        }
        
        // Get filename from Content-Disposition header or create default
        const contentDisposition = response.headers.get('Content-Disposition');
        let filename = `${currentExportType}-report-${new Date().toISOString().split('T')[0]}.${format}`;
        
        if (contentDisposition) {
            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(contentDisposition);
            if (matches && matches[1]) {
                filename = matches[1].replace(/['"]/g, '');
            }
        }
        
        return response.blob().then(blob => ({ blob, filename }));
    })
    .then(({ blob, filename }) => {
        // Check if blob is valid
        if (!blob || blob.size === 0) {
            throw new Error('Export file is empty. No data available for the selected period.');
        }
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        
        // Cleanup
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        // Show success message
        showSuccessMessage('Report exported successfully! Check your downloads.');
        
        // Close modal after short delay
        setTimeout(() => {
            closeExportModal();
        }, 1500);
    })
    .catch(error => {
        console.error('Export error:', error);
        showExportError(error.message || 'Failed to export report. Please try again.');
    })
    .finally(() => {
        hideExportProgress();
    });
}

/**
 * Show export progress indicator
 */
function showExportProgress() {
    const progress = document.getElementById('exportProgress');
    const error = document.getElementById('exportError');
    const exportBtn = document.getElementById('exportButton');
    
    if (progress) progress.classList.remove('hidden');
    if (error) error.classList.add('hidden');
    if (exportBtn) {
        exportBtn.disabled = true;
        exportBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * Hide export progress indicator
 */
function hideExportProgress() {
    const progress = document.getElementById('exportProgress');
    const exportBtn = document.getElementById('exportButton');
    
    if (progress) progress.classList.add('hidden');
    if (exportBtn) {
        exportBtn.disabled = false;
        exportBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}

/**
 * Show export error message
 * @param {string} message - Error message to display
 */
function showExportError(message) {
    const error = document.getElementById('exportError');
    const errorMessage = document.getElementById('exportErrorMessage');
    const progress = document.getElementById('exportProgress');
    
    if (errorMessage) errorMessage.textContent = message;
    if (error) error.classList.remove('hidden');
    if (progress) progress.classList.add('hidden');
}

/**
 * Show success message
 * @param {string} message - Success message to display
 */
function showSuccessMessage(message) {
    // Create a temporary success notification
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 z-[10000] bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center space-x-3 animate-fade-in';
    notification.innerHTML = `
        <div class="bg-white/20 rounded-lg p-2">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div>
            <span class="font-semibold text-base">${message}</span>
            <p class="text-xs text-white/90 mt-0.5">Your file is ready</p>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        notification.style.transition = 'all 0.3s ease-out';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('exportReportModal');
    if (modal && event.target === modal && !modal.classList.contains('hidden')) {
        closeExportModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('exportReportModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeExportModal();
        }
    }
});

/**
 * Setup radio button event listeners to ensure visual feedback
 * This is a fallback in case CSS peer selectors don't work
 */
function setupRadioButtonListeners() {
    console.log('Setting up radio button listeners...');
    
    // Handle export format radio buttons
    const formatRadios = document.querySelectorAll('input[name="exportFormat"]');
    console.log('Found format radios:', formatRadios.length);
    
    formatRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Format radio changed:', this.value, 'checked:', this.checked);
            
            // Update all visual indicators
            formatRadios.forEach(r => {
                updateFormatVisualIndicator(r);
            });
        });
        
        // Initialize visual state for currently checked radio
        if (radio.checked) {
            console.log('Initializing checked state for:', radio.value);
            updateFormatVisualIndicator(radio);
        }
    });
    
    // Handle export type radio buttons
    const typeRadios = document.querySelectorAll('input[name="exportType"]');
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Type radio changed:', this.value);
            
            // Update visual indicator
            typeRadios.forEach(r => {
                const label = r.closest('label');
                if (label) {
                    const indicator = label.querySelector('.absolute.rounded-full');
                    if (indicator) {
                        if (r.checked) {
                            indicator.style.borderWidth = '6px';
                            indicator.style.borderColor = 'hsl(var(--accent), 1)';
                        } else {
                            indicator.style.borderWidth = '2px';
                            indicator.style.borderColor = '#d1d5db';
                        }
                    }
                }
            });
        });
        
        // Trigger change event for initially checked radio
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
}

/**
 * Update visual indicator for a format radio button
 */
function updateFormatVisualIndicator(radio) {
    const label = radio.closest('label');
    if (!label) return;
    
    const indicator = label.querySelector('.absolute.top-3.right-3');
    const icon = indicator?.querySelector('svg');
    const iconBg = label.querySelector('.w-14');
    
    if (radio.checked) {
        // Determine color based on value
        let color = '#ef4444'; // red for PDF
        if (radio.value === 'excel') color = '#22c55e'; // green
        if (radio.value === 'csv') color = '#3b82f6'; // blue
        
        console.log('Setting checked styles for', radio.value, 'with color', color);
        
        // Apply checked styles
        if (indicator) {
            indicator.style.backgroundColor = color;
            indicator.style.borderColor = color;
        }
        
        // Show checkmark icon
        if (icon) {
            icon.style.opacity = '1';
        }
        
        // Scale icon background
        if (iconBg) {
            iconBg.style.transform = 'scale(1.1)';
        }
    } else {
        // Reset to unchecked state
        if (indicator) {
            indicator.style.backgroundColor = '';
            indicator.style.borderColor = '#d1d5db';
        }
        
        if (icon) {
            icon.style.opacity = '0';
        }
        
        if (iconBg) {
            iconBg.style.transform = 'scale(1)';
        }
    }
}

// Prevent clicking on modal content from closing the modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('exportReportModal');
    if (modal) {
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }
    }
});
