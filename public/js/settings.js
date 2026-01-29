/**
 * Settings Management JavaScript
 * Handles all settings-related functionality
 */

/**
 * Get the base URL from meta tag or default to empty string
 * This ensures proper URL resolution on both localhost and production
 */
function getBaseUrl() {
    const baseUrlMeta = document.querySelector('meta[name="base-url"]');
    if (baseUrlMeta) {
        return baseUrlMeta.getAttribute('content') || '';
    }
    // Fallback: use current origin
    return window.location.origin;
}

/**
 * Build a full URL from a relative path
 */
function buildUrl(path) {
    const baseUrl = getBaseUrl();
    // Ensure path starts with /
    const normalizedPath = path.startsWith('/') ? path : '/' + path;
    return baseUrl + normalizedPath;
}

// Toggle API key visibility
function toggleApiKeyVisibility() {
    const apiKeyInput = document.getElementById('xendit_api_key');
    const showIcon = document.getElementById('show-api-key-icon');
    const hideIcon = document.getElementById('hide-api-key-icon');
    
    if (apiKeyInput.type === 'password') {
        apiKeyInput.type = 'text';
        showIcon.classList.add('hidden');
        hideIcon.classList.remove('hidden');
    } else {
        apiKeyInput.type = 'password';
        showIcon.classList.remove('hidden');
        hideIcon.classList.add('hidden');
    }
}

// Global notification function

document.addEventListener('DOMContentLoaded', function() {
    initializeSettingsTabs();
    initializeUserSearch();
    initializeForms();
});

// Tab Management
function initializeSettingsTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Update button states
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-accent', 'text-accent');
                btn.classList.add('border-transparent', 'text-secondary-text');
            });
            
            this.classList.add('active', 'border-accent', 'text-accent');
            this.classList.remove('border-transparent', 'text-secondary-text');
            
            // Update content visibility
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            document.getElementById(tabName + '-tab').classList.remove('hidden');
        });
    });
}

// User Search and Filtering
function initializeUserSearch() {
    const searchInput = document.getElementById('user-search');
    const statusFilter = document.getElementById('user-status-filter');

    if (searchInput) {
        searchInput.addEventListener('input', filterUsers);
    }
    if (statusFilter) {
        statusFilter.addEventListener('change', filterUsers);
    }
}

function filterUsers() {
    const searchTerm = document.getElementById('user-search').value.toLowerCase();
    const statusFilter = document.getElementById('user-status-filter').value;
    
    const userRows = document.querySelectorAll('.user-row');
    let visibleCount = 0;

    userRows.forEach(row => {
        const searchText = row.getAttribute('data-search-text');
        const userStatus = row.getAttribute('data-user-status');
        
        const matchesSearch = !searchTerm || searchText.includes(searchTerm);
        const matchesStatus = !statusFilter || userStatus === statusFilter;
        
        if (matchesSearch && matchesStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide empty state
    const emptyState = document.getElementById('no-users-found');
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

// Form Initialization
function initializeForms() {
    // Create staff form
    const createStaffForm = document.getElementById('create-staff-form');
    if (createStaffForm) {
        createStaffForm.addEventListener('submit', handleCreateStaff);
    }

    // Edit staff form
    const editStaffForm = document.getElementById('edit-staff-form');
    if (editStaffForm) {
        editStaffForm.addEventListener('submit', handleEditStaff);
    }

    // Reset password form
    const resetPasswordForm = document.getElementById('reset-password-form');
    if (resetPasswordForm) {
        resetPasswordForm.addEventListener('submit', handleResetPassword);
    }

    // System settings form
    const systemSettingsForm = document.getElementById('system-settings-form');
    if (systemSettingsForm) {
        systemSettingsForm.addEventListener('submit', handleSystemSettings);
    }

    // Security settings form
    const securitySettingsForm = document.getElementById('security-settings-form');
    if (securitySettingsForm) {
        securitySettingsForm.addEventListener('submit', handleSecuritySettings);
    }

    // Payment configuration form
    const paymentConfigForm = document.getElementById('payment-configuration-form');
    if (paymentConfigForm) {
        paymentConfigForm.addEventListener('submit', handlePaymentConfiguration);
    }
}

// Modal Functions
function openCreateStaffModal() {
    const modal = document.getElementById('create-staff-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeCreateStaffModal() {
    const modal = document.getElementById('create-staff-modal');
    const form = document.getElementById('create-staff-form');
    
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    if (form) {
        form.reset();
    }
}

function closeEditStaffModal() {
    const modal = document.getElementById('edit-staff-modal');
    const form = document.getElementById('edit-staff-form');
    
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    if (form) {
        form.reset();
    }
}

function closeSecurityLogsModal() {
    const modal = document.getElementById('security-logs-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

// Staff Management Functions
async function handleCreateStaff(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoadingState(form);
        
        const response = await fetch('/settings/users/staff', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
            closeCreateStaffModal();
            location.reload();
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error creating staff:', error);
        showNotification('error', 'An error occurred while creating the staff member.');
    } finally {
        hideLoadingState(form);
    }
}

async function editStaff(staffId) {
    try {
        // Fetch staff data
        const response = await fetch(`/settings/users/staff/${staffId}`);
        const staffData = await response.json();

        if (staffData.success) {
            const staff = staffData.user;
            
            // Populate form
            document.getElementById('edit-staff-id').value = staffId;
            document.getElementById('edit_username').value = staff.username || '';
            document.getElementById('edit_first_name').value = staff.first_name || '';
            document.getElementById('edit_last_name').value = staff.last_name || '';
            document.getElementById('edit_email').value = staff.email || '';
            document.getElementById('edit_phone').value = staff.phone || '';

            // Show modal
            const modal = document.getElementById('edit-staff-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        } else {
            showNotification('error', 'Failed to load staff data.');
        }
    } catch (error) {
        console.error('Error loading staff data:', error);
        showNotification('error', 'An error occurred while loading staff data.');
    }
}

async function handleEditStaff(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const staffId = formData.get('staff_id');
    
    // Add _method field for Laravel to recognize PUT request
    formData.append('_method', 'PUT');
    
    try {
        showLoadingState(form);
        
        const response = await fetch(`/settings/users/staff/${staffId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
            closeEditStaffModal();
            location.reload();
        } else {
            // Display validation errors if present
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat().join('<br>');
                showNotification('error', errorMessages);
            } else {
                showNotification('error', result.message || 'Failed to update staff member.');
            }
        }
    } catch (error) {
        console.error('Error updating staff:', error);
        showNotification('error', 'An error occurred while updating the staff member. Please try again.');
    } finally {
        hideLoadingState(form);
    }
}

async function toggleStaffStatus(staffId) {
    // Show confirmation modal instead of browser confirm
    showConfirmationModal({
        title: 'Change Staff Status',
        message: 'Are you sure you want to change this staff member\'s status? This will affect their access to the system.',
        type: 'warning',
        confirmText: 'Change Status',
        onConfirm: async () => {
            try {
                const response = await fetch(`/settings/users/staff/${staffId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('success', result.message);
                    location.reload();
                } else {
                    showNotification('error', result.message);
                }
            } catch (error) {
                console.error('Error toggling staff status:', error);
                showNotification('error', 'An error occurred while updating staff status.');
            }
        }
    });
}

async function resetStaffPassword(staffId) {
    try {
        // Fetch staff data to display in modal
        const response = await fetch(`/settings/users/staff/${staffId}`);
        const staffData = await response.json();

        if (staffData.success) {
            const staff = staffData.user;
            
            // Populate modal
            document.getElementById('reset-staff-id').value = staffId;
            document.getElementById('reset-staff-display').textContent = `${staff.first_name} ${staff.last_name} (${staff.email})`;
            
            // Show modal
            const modal = document.getElementById('reset-password-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        } else {
            showNotification('error', 'Failed to load staff data.');
        }
    } catch (error) {
        console.error('Error loading staff data:', error);
        showNotification('error', 'An error occurred while loading staff data.');
    }
}

function closeResetPasswordModal() {
    const modal = document.getElementById('reset-password-modal');
    const form = document.getElementById('reset-password-form');
    
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
    if (form) {
        form.reset();
    }
}

async function handleResetPassword(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const staffId = formData.get('staff_id');
    
    // Validate passwords match
    const password = formData.get('password');
    const confirmPassword = formData.get('password_confirmation');
    
    if (password !== confirmPassword) {
        showNotification('error', 'Passwords do not match.');
        return;
    }
    
    if (password.length < 8) {
        showNotification('error', 'Password must be at least 8 characters long.');
        return;
    }

    try {
        showLoadingState(form);
        
        const response = await fetch(`/settings/users/staff/${staffId}/reset-password`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                password: password,
                password_confirmation: confirmPassword
            })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
            closeResetPasswordModal();
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error resetting password:', error);
        showNotification('error', 'An error occurred while resetting the password.');
    } finally {
        hideLoadingState(form);
    }
}

async function unlockStaff(staffId) {
    // Show confirmation modal instead of browser confirm
    showConfirmationModal({
        title: 'Unlock Staff Account',
        message: 'Are you sure you want to unlock this staff account? This will reset failed login attempts and allow the staff member to access the system again.',
        type: 'success',
        confirmText: 'Unlock Account',
        onConfirm: async () => {
            try {
                const response = await fetch(`/settings/users/staff/${staffId}/unlock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('success', result.message);
                    location.reload();
                } else {
                    showNotification('error', result.message);
                }
            } catch (error) {
                console.error('Error unlocking staff:', error);
                showNotification('error', 'An error occurred while unlocking the staff member.');
            }
        }
    });
}

async function showStaffSecurityLogs(staffId) {
    try {
        const response = await fetch(`/settings/users/staff/${staffId}/security-logs`);
        const result = await response.json();

        if (result.success) {
            const logs = result.logs;
            
            // Populate modal with data
            document.getElementById('security-user-name').textContent = logs.full_name || '-';
            document.getElementById('security-user-email').textContent = logs.email || '-';
            document.getElementById('security-user-type').textContent = 'Staff';
            document.getElementById('security-user-status').textContent = logs.is_locked ? 'Locked' : 'Active';
            
            document.getElementById('security-last-login').textContent = logs.last_login ? new Date(logs.last_login).toLocaleString() : 'Never';
            document.getElementById('security-failed-attempts').textContent = logs.failed_attempts || '0';
            document.getElementById('security-password-changed').textContent = logs.password_changed_at ? new Date(logs.password_changed_at).toLocaleString() : 'Never';
            
            document.getElementById('security-is-locked').textContent = logs.is_locked ? 'Yes' : 'No';
            document.getElementById('security-force-password-change').textContent = logs.force_password_change ? 'Yes' : 'No';
            document.getElementById('security-last-ip').textContent = logs.last_login_ip || 'Unknown';
            
            // Handle locked until
            const lockedUntilRow = document.getElementById('locked-until-row');
            const unlockBtn = document.getElementById('unlock-user-btn');
            
            if (logs.is_locked && logs.locked_until) {
                lockedUntilRow.style.display = 'flex';
                document.getElementById('security-locked-until').textContent = new Date(logs.locked_until).toLocaleString();
                unlockBtn.style.display = 'inline-block';
                unlockBtn.setAttribute('data-staff-id', staffId);
            } else {
                lockedUntilRow.style.display = 'none';
                unlockBtn.style.display = 'none';
            }

            // Show modal
            const modal = document.getElementById('security-logs-modal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            showNotification('error', 'Failed to load security logs.');
        }
    } catch (error) {
        console.error('Error loading security logs:', error);
        showNotification('error', 'An error occurred while loading security logs.');
    }
}

function unlockStaffFromModal() {
    const btn = document.getElementById('unlock-user-btn');
    const staffId = btn.getAttribute('data-staff-id');
    
    unlockStaff(staffId).then(() => {
        closeSecurityLogsModal();
    });
}

// Payment Integration
async function togglePaymentIntegration(enabled) {
    try {
        const response = await fetch('/settings/payment/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ enabled })
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
            
            // Update UI
            document.getElementById('payment-status-text').textContent = enabled ? 'Enabled' : 'Disabled';
            const settingsForm = document.getElementById('payment-settings-form');
            if (settingsForm) {
                settingsForm.style.opacity = enabled ? '1' : '0.5';
                settingsForm.style.pointerEvents = enabled ? 'auto' : 'none';
            }
            
            // Update GCash method card toggle and appearance
            const gcashToggle = document.getElementById('gcash-method-toggle');
            const gcashCard = document.getElementById('gcash-method-card');
            if (gcashToggle) {
                gcashToggle.checked = enabled;
            }
            if (gcashCard) {
                gcashCard.style.opacity = enabled ? '1' : '0.5';
            }
        } else {
            showNotification('error', result.message);
            // Revert toggle
            document.getElementById('payment-integration-toggle').checked = !enabled;
        }
    } catch (error) {
        console.error('Error toggling payment integration:', error);
        showNotification('error', 'An error occurred while updating payment integration.');
        // Revert toggle
        document.getElementById('payment-integration-toggle').checked = !enabled;
    }
}

async function handlePaymentConfiguration(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoadingState(form);
        
        const response = await fetch('/settings/payment/configure', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error saving payment configuration:', error);
        showNotification('error', 'An error occurred while saving payment configuration.');
    } finally {
        hideLoadingState(form);
    }
}

async function testPaymentConnection() {
    try {
        // Get the API key from the input field
        const apiKeyInput = document.getElementById('xendit_api_key');
        const apiKey = apiKeyInput ? apiKeyInput.value.trim() : '';
        
        if (!apiKey) {
            showNotification('error', 'Please enter an API key before testing the connection.');
            return;
        }
        
        console.log('Testing API key:', apiKey.substring(0, 20) + '... (length: ' + apiKey.length + ')');
        
        // Show loading state
        const testButton = event.target;
        const originalText = testButton.innerHTML;
        testButton.disabled = true;
        testButton.innerHTML = `
            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Testing Connection...
        `;

        const response = await fetch('/settings/payment/test-connection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                api_key: apiKey
            })
        });

        const result = await response.json();

        // Restore button state
        testButton.disabled = false;
        testButton.innerHTML = originalText;

        if (result.success) {
            console.log('Connection successful:', result);
            let message = result.message || 'Payment connection test successful!';
            if (result.data && result.data.mode) {
                message += ' (Mode: ' + result.data.mode + ')';
            }
            showNotification('success', message);
        } else {
            console.error('Connection failed:', result);
            
            // Show detailed error information
            let errorMessage = result.message || 'Payment connection test failed.';
            
            // If there's debug info, log it to console
            if (result.debug_info) {
                console.error('Debug info:', result.debug_info);
                // You can optionally show this in the UI for troubleshooting
                if (result.debug_info.suggestion) {
                    errorMessage += '\n\nSuggestion: ' + result.debug_info.suggestion;
                }
            }
            
            showNotification('error', errorMessage);
        }
    } catch (error) {
        console.error('Error testing payment connection:', error);
        showNotification('error', 'Failed to connect to API. Network error or server is unreachable.');
        
        // Restore button state if error occurs
        if (event && event.target) {
            event.target.disabled = false;
            event.target.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Test Connection
            `;
        }
    }
}

// Settings Forms
async function handleSystemSettings(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoadingState(form);
        
        const response = await fetch('/settings/system', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error saving system settings:', error);
        showNotification('error', 'An error occurred while saving system settings.');
    } finally {
        hideLoadingState(form);
    }
}

async function handleSecuritySettings(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoadingState(form);
        
        const response = await fetch('/settings/security', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showNotification('success', result.message);
        } else {
            showNotification('error', result.message);
        }
    } catch (error) {
        console.error('Error saving security settings:', error);
        showNotification('error', 'An error occurred while saving security settings.');
    } finally {
        hideLoadingState(form);
    }
}

// Utility Functions
function showLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
    }
}

function hideLoadingState(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = submitButton.getAttribute('data-original-text') || 'Save';
    }
}

function showNotification(type, message) {
    // Use the global toast notification system
    if (typeof window.showToast === 'function') {
        window.showToast(message, type, 5000);
    } else {
        // Fallback in case toast.js hasn't loaded
        console.warn('Toast system not available, using fallback notification');
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// ===========================================
// Admin Account Settings Functions
// ===========================================

/**
 * Open the admin account settings modal
 */
async function openAdminAccountModal() {
    const modal = document.getElementById('admin-account-modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Load current admin account data
        await loadAdminAccountData();
        
        // Initialize password strength checker
        initPasswordStrengthChecker();
    }
}

/**
 * Close the admin account settings modal
 */
function closeAdminAccountModal() {
    const modal = document.getElementById('admin-account-modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset forms
        const usernameForm = document.getElementById('change-username-form');
        const passwordForm = document.getElementById('change-password-form');
        if (usernameForm) usernameForm.reset();
        if (passwordForm) passwordForm.reset();
        
        // Reset to username tab
        switchAccountTab('username');
        
        // Reset password strength indicator
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        if (strengthBar) strengthBar.style.width = '0%';
        if (strengthText) strengthText.textContent = 'Enter a password to see strength';
    }
}

/**
 * Load the current admin's account data
 */
async function loadAdminAccountData() {
    try {
        console.log('Loading admin account data from:', buildUrl('/settings/account'));
        
        const response = await fetch(buildUrl('/settings/account'), {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        console.log('Load account response status:', response.status);
        
        // Check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Load account failed:', response.status, errorText);
            showNotification('error', 'Failed to load account info: ' + response.status);
            return;
        }
        
        const result = await response.json();
        console.log('Load account result:', result);
        
        if (result.success) {
            document.getElementById('current-admin-username').textContent = result.user.username;
            document.getElementById('password-changed-at').textContent = result.user.password_changed_at;
            document.getElementById('new_username').value = result.user.username;
        } else {
            showNotification('error', result.message || 'Failed to load account information.');
        }
    } catch (error) {
        console.error('Error loading admin account data:', error);
        showNotification('error', 'An error occurred while loading account information: ' + error.message);
    }
}

/**
 * Switch between account settings tabs
 */
function switchAccountTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.account-tab-btn').forEach(btn => {
        btn.classList.remove('active', 'border-accent', 'text-accent');
        btn.classList.add('border-transparent', 'text-secondary-text');
    });
    
    const activeBtn = document.getElementById(tabName + '-tab-btn');
    if (activeBtn) {
        activeBtn.classList.add('active', 'border-accent', 'text-accent');
        activeBtn.classList.remove('border-transparent', 'text-secondary-text');
    }
    
    // Update tab contents
    document.querySelectorAll('.account-tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    const activeTab = document.getElementById(tabName + '-tab');
    if (activeTab) {
        activeTab.classList.remove('hidden');
    }
}

/**
 * Toggle password visibility
 */
function togglePasswordVisibility(inputId, eyeIconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeIconId);
    
    if (!input || !icon) return;
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
}

/**
 * Initialize password strength checker
 */
function initPasswordStrengthChecker() {
    const passwordInput = document.getElementById('admin_new_password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
}

/**
 * Check password strength and update indicator
 */
function checkPasswordStrength(password) {
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    
    if (!strengthBar || !strengthText) return;
    
    let strength = 0;
    let feedback = [];
    
    // Length check
    if (password.length >= 8) strength += 25;
    else feedback.push('at least 8 characters');
    
    // Uppercase check
    if (/[A-Z]/.test(password)) strength += 25;
    else feedback.push('uppercase letter');
    
    // Number check
    if (/[0-9]/.test(password)) strength += 25;
    else feedback.push('number');
    
    // Special character check
    if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 25;
    else feedback.push('special character');
    
    // Update bar
    strengthBar.style.width = strength + '%';
    
    // Update color and text
    if (strength <= 25) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
        strengthText.textContent = 'Weak - Add ' + feedback.join(', ');
        strengthText.className = 'text-xs text-red-500 mt-1';
    } else if (strength <= 50) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
        strengthText.textContent = 'Fair - Add ' + feedback.join(', ');
        strengthText.className = 'text-xs text-orange-500 mt-1';
    } else if (strength <= 75) {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
        strengthText.textContent = 'Good - Consider adding ' + feedback.join(', ');
        strengthText.className = 'text-xs text-yellow-600 mt-1';
    } else {
        strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
        strengthText.textContent = 'Strong password!';
        strengthText.className = 'text-xs text-green-500 mt-1';
    }
}

/**
 * Handle username change form submission
 */
async function handleChangeUsername(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    try {
        showLoadingState(form);
        
        console.log('Submitting username change to:', buildUrl('/settings/account/username'));
        
        const response = await fetch(buildUrl('/settings/account/username'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                username: formData.get('username'),
                current_password: formData.get('current_password'),
                _method: 'PUT'
            })
        });
        
        console.log('Username change response status:', response.status);
        
        // Check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Username change failed:', response.status, errorText);
            
            // Try to parse as JSON if possible
            try {
                const errorJson = JSON.parse(errorText);
                showNotification('error', errorJson.message || 'Server error: ' + response.status);
            } catch (e) {
                showNotification('error', 'Server error (' + response.status + '): ' + (errorText.substring(0, 100) || 'Unknown error'));
            }
            return;
        }
        
        const result = await response.json();
        console.log('Username change result:', result);
        
        if (result.success) {
            showNotification('success', result.message);
            // Update the displayed username
            document.getElementById('current-admin-username').textContent = result.new_username;
            form.reset();
            document.getElementById('new_username').value = result.new_username;
        } else {
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat().join('. ');
                showNotification('error', errorMessages);
            } else {
                showNotification('error', result.message);
            }
        }
    } catch (error) {
        console.error('Error updating username:', error);
        showNotification('error', 'An error occurred while updating username.');
    } finally {
        hideLoadingState(form);
    }
}

/**
 * Handle password change form submission
 */
async function handleChangePassword(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate passwords match
    const password = formData.get('password');
    const confirmPassword = formData.get('password_confirmation');
    
    if (password !== confirmPassword) {
        showNotification('error', 'Passwords do not match.');
        return;
    }
    
    if (password.length < 8) {
        showNotification('error', 'Password must be at least 8 characters long.');
        return;
    }
    
    try {
        showLoadingState(form);
        
        console.log('Submitting password change to:', buildUrl('/settings/account/password'));
        
        const response = await fetch(buildUrl('/settings/account/password'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            body: JSON.stringify({
                current_password: formData.get('current_password'),
                password: password,
                password_confirmation: confirmPassword,
                _method: 'PUT'
            })
        });
        
        console.log('Password change response status:', response.status);
        
        // Check if response is OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Password change failed:', response.status, errorText);
            
            // Try to parse as JSON if possible
            try {
                const errorJson = JSON.parse(errorText);
                showNotification('error', errorJson.message || 'Server error: ' + response.status);
            } catch (e) {
                showNotification('error', 'Server error (' + response.status + '): ' + (errorText.substring(0, 100) || 'Unknown error'));
            }
            return;
        }
        
        const result = await response.json();
        console.log('Password change result:', result);
        
        if (result.success) {
            showNotification('success', result.message);
            form.reset();
            // Update password changed date
            document.getElementById('password-changed-at').textContent = 'Just now';
            // Reset strength indicator
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            if (strengthBar) strengthBar.style.width = '0%';
            if (strengthText) {
                strengthText.textContent = 'Enter a password to see strength';
                strengthText.className = 'text-xs text-secondary-text mt-1';
            }
        } else {
            if (result.errors) {
                const errorMessages = Object.values(result.errors).flat().join('. ');
                showNotification('error', errorMessages);
            } else {
                showNotification('error', result.message);
            }
        }
    } catch (error) {
        console.error('Error updating password:', error);
        showNotification('error', 'An error occurred while updating password.');
    } finally {
        hideLoadingState(form);
    }
}

// Initialize admin account forms when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for admin account forms
    const changeUsernameForm = document.getElementById('change-username-form');
    if (changeUsernameForm) {
        changeUsernameForm.addEventListener('submit', handleChangeUsername);
    }
    
    const changePasswordForm = document.getElementById('change-password-form');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', handleChangePassword);
    }
});