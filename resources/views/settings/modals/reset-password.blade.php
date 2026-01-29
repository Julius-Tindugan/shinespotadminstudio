<!-- Reset Staff Password Modal -->
<div id="reset-password-modal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]" onclick="if(event.target === this) closeResetPasswordModal()">
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="modal-content relative w-11/12 md:w-1/2 lg:w-1/3 max-w-lg bg-card-bg rounded-xl shadow-2xl my-8">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-accent to-accent-hover text-white px-6 py-5 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Reset Password</h3>
                        <p class="text-sm text-white/80 mt-0.5">Set a new password for staff member</p>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200" onclick="closeResetPasswordModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
            <!-- Warning Banner -->
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800 mb-1">Important Notice</p>
                        <p class="text-sm text-amber-700">
                            The staff member will be required to change their password upon next login for security purposes.
                        </p>
                    </div>
                </div>
            </div>

            <form id="reset-password-form" class="space-y-5">
                @csrf
                <input type="hidden" id="reset-staff-id" name="staff_id" value="">
                
                <!-- Staff Info Card -->
                <div class="bg-gradient-to-br from-accent/5 to-accent/10 border-2 border-accent/20 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-accent/20 rounded-lg p-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-secondary-text font-medium">Staff Member</p>
                            <p id="reset-staff-display" class="text-sm font-bold text-primary-text"></p>
                        </div>
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="space-y-4">
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-primary-text mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="new_password" 
                                   name="password" 
                                   required
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <button type="button" onclick="toggleResetPasswordVisibility('new_password', 'new-password-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                <svg id="new-password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <label for="confirm_password" class="block text-sm font-medium text-primary-text mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="confirm_password" 
                                   name="password_confirmation" 
                                   required
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <button type="button" onclick="toggleResetPasswordVisibility('confirm_password', 'confirm-password-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                <svg id="confirm-password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                </div>

                <!-- Security Info -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-r-lg">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-xs font-medium text-blue-800">Security Best Practice</p>
                            <p class="text-xs text-blue-700 mt-1">Use a strong password with a mix of letters, numbers, and symbols.</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-border-color">
            <button type="button" 
                    class="px-5 py-2.5 border-2 border-border-color text-secondary-text font-medium rounded-lg hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200" 
                    onclick="closeResetPasswordModal()">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </span>
            </button>
            <button type="submit" 
                    form="reset-password-form"
                    class="px-6 py-2.5 bg-gradient-to-r from-accent to-accent-hover text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                    Reset Password
                </span>
            </button>
        </div>
        </div>
    </div>
</div>

<script>
function toggleResetPasswordVisibility(inputId, eyeIconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(eyeIconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
    }
}
</script>