<!-- Create Staff Modal -->
<div id="create-staff-modal" class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden z-[9999]" onclick="if(event.target === this) closeCreateStaffModal()">
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="modal-content relative w-11/12 md:w-2/3 lg:w-1/2 max-w-2xl bg-card-bg rounded-xl shadow-2xl my-8">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-accent to-accent-hover text-white px-6 py-5 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 rounded-lg p-2 backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Create New Staff Member</h3>
                        <p class="text-sm text-white/80 mt-0.5">Add a new member to your team</p>
                    </div>
                </div>
                <button type="button" class="text-white/80 hover:text-white hover:bg-white/20 rounded-lg p-2 transition-all duration-200" onclick="closeCreateStaffModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="create-staff-form" class="p-6 space-y-6 max-h-[calc(100vh-200px)] overflow-y-auto">
            @csrf

            <!-- Personal Information Section -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 pb-2 border-b border-border-color">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-primary-text uppercase tracking-wide">Personal Information</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-primary-text mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   required
                                   placeholder="John"
                                   class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-primary-text mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   required
                                   placeholder="Doe"
                                   class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Information Section -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 pb-2 border-b border-border-color">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-primary-text uppercase tracking-wide">Account Information</h4>
                </div>
                
                <div>
                    <label for="username" class="block text-sm font-medium text-primary-text mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                               id="username" 
                               name="username" 
                               required
                               pattern="[a-zA-Z0-9_]+"
                               minlength="3"
                               maxlength="50"
                               placeholder="john_doe"
                               class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                        <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <p class="text-xs text-secondary-text mt-1.5 flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Only letters, numbers, and underscores allowed (3-50 characters)
                    </p>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-primary-text mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required
                               placeholder="john.doe@example.com"
                               class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                        <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-primary-text mb-2">
                        Phone Number <span class="text-secondary-text text-xs">(Optional)</span>
                    </label>
                    <div class="relative">
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               placeholder="+63-975-543-1234"
                               class="w-full px-4 py-2.5 pl-10 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                        <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="space-y-4">
                <div class="flex items-center space-x-2 pb-2 border-b border-border-color">
                    <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-primary-text uppercase tracking-wide">Security</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-primary-text mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <button type="button" onclick="togglePasswordVisibility('password', 'password-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-primary-text mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required
                                   minlength="8"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-2.5 pl-10 pr-12 border-2 border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200 hover:border-accent/50">
                            <svg class="w-5 h-5 text-secondary-text absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation', 'password-confirm-eye')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-text hover:text-primary-text transition-colors duration-200 focus:outline-none">
                                <svg id="password-confirm-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-3 rounded-r-lg">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-xs font-medium text-blue-800">Password Requirements:</p>
                            <p class="text-xs text-blue-700 mt-1">Minimum 8 characters. Use a strong, unique password.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end space-x-3 border-t border-border-color">
            <button type="button" 
                    class="px-5 py-2.5 border-2 border-border-color text-secondary-text font-medium rounded-lg hover:bg-gray-100 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200" 
                    onclick="closeCreateStaffModal()">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </span>
            </button>
            <button type="submit" 
                    form="create-staff-form"
                    class="px-6 py-2.5 bg-gradient-to-r from-accent to-accent-hover text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Staff Member
                </span>
            </button>
        </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, eyeIconId) {
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