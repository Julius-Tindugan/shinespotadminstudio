<!-- Admin Account Settings Modal -->
<div id="admin-account-modal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]" onclick="if(event.target === this) closeAdminAccountModal()">
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="modal-content relative w-11/12 md:w-2/3 lg:w-1/2 max-w-2xl bg-card-bg rounded-xl shadow-2xl my-8">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-accent to-accent-hover text-white px-6 py-5 rounded-t-xl">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Account Settings</h3>
                            <p class="text-sm text-white/80 mt-0.5">Manage your administrator account credentials</p>
                        </div>
                    </div>
                    <button type="button" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200" onclick="closeAdminAccountModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6">
                <!-- Current Account Info Card -->
                <div class="bg-gradient-to-br from-accent/5 to-accent/10 border-2 border-accent/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="bg-accent/20 rounded-lg p-2">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-secondary-text font-medium">Current Username</p>
                                <p id="current-admin-username" class="text-sm font-bold text-primary-text">Loading...</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-secondary-text font-medium">Password Last Changed</p>
                            <p id="password-changed-at" class="text-sm text-primary-text">Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="border-b border-border-color">
                    <nav class="-mb-px flex space-x-4">
                        <button type="button" 
                                id="username-tab-btn"
                                class="account-tab-btn active border-b-2 border-accent text-accent pb-3 px-1 font-medium text-sm" 
                                onclick="switchAccountTab('username')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Change Username
                        </button>
                        <button type="button" 
                                id="password-tab-btn"
                                class="account-tab-btn border-b-2 border-transparent text-secondary-text hover:text-primary-text pb-3 px-1 font-medium text-sm" 
                                onclick="switchAccountTab('password')">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            Change Password
                        </button>
                    </nav>
                </div>

                <!-- Username Change Form -->
                <div id="username-tab" class="account-tab-content">
                    <form id="change-username-form" class="space-y-4" onsubmit="return handleUsernameFormSubmit(event);">
                        @csrf
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg mb-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Username Requirements</p>
                                    <p class="text-xs text-blue-700 mt-1">Username must be 3-50 characters. Allowed: letters, numbers, and special characters (!@#$%^&*_)</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="new_username" class="block text-sm font-medium text-primary-text mb-2">
                                New Username <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="new_username" 
                                       name="username" 
                                       required
                                       minlength="3"
                                       maxlength="50"
                                       pattern="[a-zA-Z0-9_!@#$%^&*]+"
                                       placeholder="Enter new username"
                                       class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                                <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        
                        <div>
                            <label for="username_current_password" class="block text-sm font-medium text-primary-text mb-2">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="username_current_password" 
                                       name="current_password" 
                                       required
                                       placeholder="Enter your current password"
                                       class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                                <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" onclick="togglePasswordVisibility('username_current_password', 'username-pwd-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                    <svg id="username-pwd-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-secondary-text mt-1.5">Required to confirm your identity</p>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-accent to-accent-hover text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Update Username
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Change Form -->
                <div id="password-tab" class="account-tab-content hidden">
                    <form id="change-password-form" class="space-y-4" onsubmit="return handlePasswordFormSubmit(event);">
                        @csrf
                        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg mb-4">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-amber-800">Security Notice</p>
                                    <p class="text-xs text-amber-700 mt-1">Password must be at least 8 characters and different from your current password.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="password_current_password" class="block text-sm font-medium text-primary-text mb-2">
                                Current Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password_current_password" 
                                       name="current_password" 
                                       required
                                       placeholder="Enter your current password"
                                       class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                                <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <button type="button" onclick="togglePasswordVisibility('password_current_password', 'current-pwd-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                    <svg id="current-pwd-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-primary-text mb-2">
                                New Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="admin_new_password" 
                                       name="password" 
                                       required
                                       minlength="8"
                                       placeholder="Enter new password"
                                       oninput="updatePasswordStrengthUI(this.value)"
                                       class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                                <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                <button type="button" onclick="togglePasswordVisibility('admin_new_password', 'new-pwd-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                    <svg id="new-pwd-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-secondary-text mt-1.5 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Minimum 8 characters required
                            </p>
                        </div>
                        
                        <div>
                            <label for="confirm_new_password" class="block text-sm font-medium text-primary-text mb-2">
                                Confirm New Password <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="confirm_new_password" 
                                       name="password_confirmation" 
                                       required
                                       minlength="8"
                                       placeholder="Confirm new password"
                                       class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                                <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <button type="button" onclick="togglePasswordVisibility('confirm_new_password', 'confirm-pwd-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                    <svg id="confirm-pwd-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-secondary-text mt-1.5 flex items-center">
                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Re-enter the password to confirm
                            </p>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs font-medium text-secondary-text mb-2">Password Strength</p>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="password-strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%;"></div>
                            </div>
                            <p id="password-strength-text" class="text-xs text-secondary-text mt-1">Enter a password to see strength</p>
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-accent to-accent-hover text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Update Password
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end border-t border-border-color">
                <button type="button" 
                        class="px-5 py-2.5 border-2 border-border-color text-secondary-text font-medium rounded-lg hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200" 
                        onclick="closeAdminAccountModal()">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Inline JavaScript for form handlers - ensures they work even if external JS has issues --}}
<script>
    // Get base URL for API calls
    function getApiBaseUrl() {
        const meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.getAttribute('content').replace(/\/$/, '') : '';
    }
    
    // Toast notification system
    function showAccountToast(type, message) {
        // Remove any existing toasts
        const existingToasts = document.querySelectorAll('.account-toast');
        existingToasts.forEach(toast => toast.remove());
        
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('account-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'account-toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-[10000] flex flex-col gap-2';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'account-toast transform translate-x-full opacity-0 transition-all duration-300 ease-out';
        
        // Set colors and icon based on type
        let bgColor, borderColor, iconColor, icon;
        switch(type) {
            case 'success':
                bgColor = 'bg-green-50';
                borderColor = 'border-green-500';
                iconColor = 'text-green-500';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                break;
            case 'error':
                bgColor = 'bg-red-50';
                borderColor = 'border-red-500';
                iconColor = 'text-red-500';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                break;
            case 'warning':
                bgColor = 'bg-amber-50';
                borderColor = 'border-amber-500';
                iconColor = 'text-amber-500';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
                break;
            default:
                bgColor = 'bg-blue-50';
                borderColor = 'border-blue-500';
                iconColor = 'text-blue-500';
                icon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
        }
        
        toast.innerHTML = `
            <div class="flex items-start gap-3 ${bgColor} border-l-4 ${borderColor} rounded-lg shadow-lg p-4 min-w-[320px] max-w-md">
                <svg class="w-6 h-6 ${iconColor} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${icon}
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${type === 'success' ? 'Success!' : type === 'error' ? 'Error' : type === 'warning' ? 'Warning' : 'Info'}</p>
                    <p class="text-sm text-gray-600 mt-1">${message}</p>
                </div>
                <button onclick="this.closest('.account-toast').remove()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Animate in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });
        
        // Auto-remove after delay
        const duration = type === 'success' ? 4000 : 6000;
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
    
    // Handle password form submission
    async function handlePasswordFormSubmit(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Get form values
        const currentPassword = form.querySelector('[name="current_password"]').value;
        const password = form.querySelector('[name="password"]').value;
        const passwordConfirmation = form.querySelector('[name="password_confirmation"]').value;
        
        // Validate
        if (password !== passwordConfirmation) {
            showAccountToast('error', 'Passwords do not match. Please ensure both password fields are identical.');
            return false;
        }
        
        if (password.length < 8) {
            showAccountToast('error', 'Password must be at least 8 characters long.');
            return false;
        }
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Updating...';
        
        try {
            const apiUrl = getApiBaseUrl() + '/settings/account/password';
            console.log('Submitting password change to:', apiUrl);
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    password: password,
                    password_confirmation: passwordConfirmation,
                    _method: 'PUT'
                })
            });
            
            console.log('Response status:', response.status);
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse response as JSON:', e);
                showAccountToast('error', 'Server returned an invalid response. Please try again later.');
                return false;
            }
            
            if (result.success) {
                showAccountToast('success', 'Password updated successfully! Please use your new password for future logins.');
                form.reset();
                document.getElementById('password-changed-at').textContent = 'Just now';
                // Reset password strength indicator
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength-text');
                if (strengthBar) strengthBar.style.width = '0%';
                if (strengthText) strengthText.textContent = 'Enter a password to see strength';
                setTimeout(() => closeAdminAccountModal(), 1500);
            } else {
                if (result.errors) {
                    const errors = Object.values(result.errors).flat().join(' ');
                    showAccountToast('error', errors);
                } else {
                    showAccountToast('error', result.message || 'Failed to update password. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error updating password:', error);
            showAccountToast('error', 'A network error occurred. Please check your connection and try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
        
        return false;
    }
    
    // Handle username form submission
    async function handleUsernameFormSubmit(event) {
        event.preventDefault();
        event.stopPropagation();
        
        const form = event.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Get form values
        const username = form.querySelector('[name="username"]').value;
        const currentPassword = form.querySelector('[name="current_password"]').value;
        
        // Validate
        if (username.length < 3 || username.length > 50) {
            showAccountToast('error', 'Username must be between 3 and 50 characters.');
            return false;
        }
        
        // Show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Updating...';
        
        try {
            const apiUrl = getApiBaseUrl() + '/settings/account/username';
            console.log('Submitting username change to:', apiUrl);
            
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    username: username,
                    current_password: currentPassword,
                    _method: 'PUT'
                })
            });
            
            console.log('Response status:', response.status);
            
            const responseText = await response.text();
            console.log('Response text:', responseText);
            
            let result;
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse response as JSON:', e);
                showAccountToast('error', 'Server returned an invalid response. Please try again later.');
                return false;
            }
            
            if (result.success) {
                showAccountToast('success', 'Username updated successfully!');
                document.getElementById('current-admin-username').textContent = result.new_username || username;
                form.querySelector('[name="username"]').value = result.new_username || username;
                form.querySelector('[name="current_password"]').value = '';
                setTimeout(() => closeAdminAccountModal(), 1500);
            } else {
                if (result.errors) {
                    const errors = Object.values(result.errors).flat().join(' ');
                    showAccountToast('error', errors);
                } else {
                    showAccountToast('error', result.message || 'Failed to update username. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error updating username:', error);
            showAccountToast('error', 'A network error occurred. Please check your connection and try again.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
        
        return false;
    }
    
    // Toggle password visibility function
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input && icon) {
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    }
    
    // Password strength checker
    function checkPasswordStrength(password) {
        let strength = 0;
        const feedback = [];
        
        // Length checks
        if (password.length >= 8) strength += 20;
        if (password.length >= 12) strength += 10;
        if (password.length >= 16) strength += 10;
        
        // Character type checks
        if (/[a-z]/.test(password)) {
            strength += 15;
        } else {
            feedback.push('lowercase letters');
        }
        
        if (/[A-Z]/.test(password)) {
            strength += 15;
        } else {
            feedback.push('uppercase letters');
        }
        
        if (/[0-9]/.test(password)) {
            strength += 15;
        } else {
            feedback.push('numbers');
        }
        
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
            strength += 15;
        } else {
            feedback.push('special characters');
        }
        
        return { strength: Math.min(strength, 100), feedback };
    }
    
    function updatePasswordStrengthUI(password) {
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');
        
        if (!strengthBar || !strengthText) return;
        
        if (!password || password.length === 0) {
            strengthBar.style.width = '0%';
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-gray-300';
            strengthText.textContent = 'Enter a password to see strength';
            strengthText.className = 'text-xs text-secondary-text mt-1';
            return;
        }
        
        const { strength, feedback } = checkPasswordStrength(password);
        
        // Update progress bar width
        strengthBar.style.width = strength + '%';
        
        // Update colors and text based on strength
        if (strength < 30) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
            strengthText.textContent = 'Weak - Add ' + feedback.join(', ');
            strengthText.className = 'text-xs text-red-500 mt-1';
        } else if (strength < 50) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
            strengthText.textContent = 'Fair - Add ' + feedback.join(', ');
            strengthText.className = 'text-xs text-orange-500 mt-1';
        } else if (strength < 75) {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
            if (feedback.length > 0) {
                strengthText.textContent = 'Good - Consider adding ' + feedback.join(', ');
            } else {
                strengthText.textContent = 'Good - Consider a longer password';
            }
            strengthText.className = 'text-xs text-yellow-600 mt-1';
        } else {
            strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
            strengthText.textContent = 'Strong password!';
            strengthText.className = 'text-xs text-green-500 mt-1';
        }
    }
    
    // Initialize password strength checker when modal opens
    function initPasswordStrengthChecker() {
        const passwordInput = document.getElementById('admin_new_password');
        if (passwordInput) {
            // Remove any existing listener first
            passwordInput.removeEventListener('input', handlePasswordInput);
            // Add new listener
            passwordInput.addEventListener('input', handlePasswordInput);
            // Check initial value
            updatePasswordStrengthUI(passwordInput.value);
        }
    }
    
    function handlePasswordInput(e) {
        updatePasswordStrengthUI(e.target.value);
    }
    
    // Tab switching function
    function switchAccountTab(tabName) {
        // Update tab buttons
        const usernameBtn = document.getElementById('username-tab-btn');
        const passwordBtn = document.getElementById('password-tab-btn');
        const usernameTab = document.getElementById('username-tab');
        const passwordTab = document.getElementById('password-tab');
        
        if (!usernameBtn || !passwordBtn || !usernameTab || !passwordTab) {
            console.error('Tab elements not found');
            return;
        }
        
        if (tabName === 'username') {
            // Activate username tab
            usernameBtn.classList.add('active', 'border-accent', 'text-accent');
            usernameBtn.classList.remove('border-transparent', 'text-secondary-text');
            passwordBtn.classList.remove('active', 'border-accent', 'text-accent');
            passwordBtn.classList.add('border-transparent', 'text-secondary-text');
            
            usernameTab.classList.remove('hidden');
            passwordTab.classList.add('hidden');
        } else if (tabName === 'password') {
            // Activate password tab
            passwordBtn.classList.add('active', 'border-accent', 'text-accent');
            passwordBtn.classList.remove('border-transparent', 'text-secondary-text');
            usernameBtn.classList.remove('active', 'border-accent', 'text-accent');
            usernameBtn.classList.add('border-transparent', 'text-secondary-text');
            
            passwordTab.classList.remove('hidden');
            usernameTab.classList.add('hidden');
            
            // Initialize password strength checker when switching to password tab
            setTimeout(initPasswordStrengthChecker, 50);
        }
    }
    
    // Make switchAccountTab available globally
    window.switchAccountTab = switchAccountTab;
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initPasswordStrengthChecker();
    });
    
    console.log('Admin account modal inline scripts loaded successfully');
</script>
