@extends('layouts.auth')

@section('content')
<div class="space-y-6" > 
    <!-- {{-- Logo and Title --}} --> 
    <div class="text-center" > 
        <div class="flex justify-center" > 
            <img src="{{ asset('images/logo.svg') }}" alt="Shine Spot Studio Logo" class="w-32 h-32" > 
            <img src="{{ asset('images/logo-dark.svg') }}" alt="Shine Spot Studio Logo" class="w-32 h-32 hidden" > 
        </div> 
        <h1 class="mt-4 text-2xl font-bold text-primary-text" >Welcome Back</h1> 
        <p class="mt-1 text-sm text-secondary-text" >Sign in to access your admin dashboard</p> 
    </div> 
    
    <!-- {{-- Login Form --}} --> 
    <div class="bg-card-bg p-8 rounded-lg shadow-sm border border-border-color" > 
        @if (session('error')) 
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert"> {{ session('error') }} </div> 
        @endif 
        @if (session('success')) 
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert"> {{ session('success') }} </div> 
        @endif 
        
        <!-- {{-- Enhanced Error Message Container --}} -->
        <div id="error-message" class="hidden mb-4 rounded-lg border-l-4 shadow-sm transition-all duration-300 ease-in-out" role="alert" aria-live="assertive" aria-atomic="true" tabindex="0" aria-describedby="error-description">
            <div class="p-4">
                <div class="flex items-start">
                    <div id="error-icon" class="flex-shrink-0 w-6 h-6 mr-3 mt-0.5" aria-hidden="true"></div>
                    <div class="flex-1">
                        <div id="error-title" class="font-semibold text-sm mb-1" role="heading" aria-level="3"></div>
                        <div id="error-description" class="text-sm opacity-90"></div>
                        <div id="error-actions" class="mt-3 hidden" role="group" aria-label="Error recovery actions">
                            <!-- Action buttons will be inserted here -->
                        </div>
                    </div>
                    <button id="error-dismiss" type="button" class="flex-shrink-0 ml-3 p-1 rounded-md hover:bg-black hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-offset-1 transition-colors" aria-label="Dismiss error message">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- {{-- Enhanced Lockout Message Container --}} -->
        <div id="lockout-message" class="hidden mb-4 rounded-lg border-l-4 border-orange-400 bg-orange-50 shadow-sm transition-all duration-300 ease-in-out" role="alert" aria-live="assertive" aria-atomic="true" tabindex="0" aria-describedby="lockout-text">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 mr-3 mt-0.5 text-orange-500" aria-hidden="true">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-orange-800 mb-1" role="heading" aria-level="3">Account Temporarily Locked</div>
                        <div id="lockout-text" class="text-sm text-orange-700 opacity-90">
                            Account temporarily locked. Please wait <span id="countdown" class="font-mono font-bold" aria-label="countdown timer">30</span> seconds.
                        </div>
                        <div class="mt-3 text-xs text-orange-600">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            This security measure protects your account from unauthorized access.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- {{-- Success Message Container --}} -->
        <div id="success-message" class="hidden mb-4 rounded-lg border-l-4 border-green-400 bg-green-50 shadow-sm transition-all duration-300 ease-in-out" role="status" aria-live="polite" aria-atomic="true" tabindex="0">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-6 h-6 mr-3 mt-0.5 text-green-500" aria-hidden="true">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-sm text-green-800 mb-1" role="heading" aria-level="3">Success!</div>
                        <div id="success-text" class="text-sm text-green-700 opacity-90"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <form id="login-form" method="POST" action="{{ route('login.authenticate') }}" class="space-y-6"> 
            @csrf 
            
            <!-- {{-- Username Field --}} --> 
            <div> 
                <label for="username" class="block text-sm font-medium text-secondary-text mb-1" >Username</label> 
                <div class="relative" > 
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" > 
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> 
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path> 
                        </svg> 
                    </div> 
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="pl-10 block w-full rounded-md border-border-color bg-background shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-20 transition duration-200 text-primary-text @error('username') border-red-500 @enderror" 
                           required 
                           autofocus 
                           value="{{ old('username') }}"
                           placeholder="Enter your username" 
                           autocomplete="username"> 
                </div>
                @error('username')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="username-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div> 
            
            <!-- {{-- Password Field --}} --> 
            <div> 
                <div class="flex items-center justify-between mb-1" > 
                    <label for="password" class="block text-sm font-medium text-secondary-text" >Password</label> 
                </div> 
                <div class="relative" > 
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" > 
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> 
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path> 
                        </svg> 
                    </div> 
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="pl-10 pr-14 block w-full rounded-md border-border-color bg-background shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-20 transition duration-200 text-primary-text @error('password') border-red-500 @enderror" 
                           required
                           placeholder="Enter your password" > 
                    <button type="button" 
                            id="password-toggle-btn"
                            class="absolute inset-y-0 right-0 flex items-center justify-center w-12 h-full cursor-pointer rounded-r-md transition-all duration-300 ease-in-out hover:bg-gradient-to-r hover:from-accent/5 hover:to-accent/10 focus:outline-none focus:ring-2 focus:ring-accent/20 focus:ring-offset-2 group active:scale-95" 
                            onclick="console.log('Button clicked!'); togglePasswordVisibility();"
                            title="Show password"
                            aria-label="Show password">
                        <svg id="eye-open" class="h-5 w-5 text-gray-400 group-hover:text-accent group-hover:scale-110 transition-all duration-300 ease-in-out transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <div id="password-error" class="hidden mt-1 text-sm text-red-600"></div>
            </div> 
            
            <!-- {{-- Remember Me --}} --> 
            <div class="flex items-center" > 
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-accent focus:ring-accent border-border-color rounded bg-background" > 
                <label for="remember" class="ml-2 block text-sm text-secondary-text" > Remember me </label> 
            </div> 
            
            <!-- {{-- reCAPTCHA --}} -->
            <div class="flex justify-center">
                {!! NoCaptcha::renderJs() !!}
                {!! NoCaptcha::display() !!}
            </div>
            
            <!-- {{-- Submit Button --}} --> 
            <div> 
                <button type="submit" id="submit-btn" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-accent" > 
                    <svg id="login-icon" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg> 
                    <svg id="loading-icon" class="hidden w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="submit-text">Sign In</span>
                </button> 
            </div> 
            
            <div class="flex flex-col sm:flex-row justify-center items-center space-y-3 sm:space-y-0 pt-4" > 
                <div class="text-center sm:text-left" > 
                    <a href="{{ route('simple.password.request') }}" class="text-sm font-medium text-accent hover:text-accent-hover transition-colors" >Forgot Password?</a> 
                </div> 
            </div> 
        </form> 
    </div> 
    
    <!-- {{-- Footer --}} --> 
    <div class="text-center text-xs text-secondary-text" > 
        <p>&copy; 2025 Shine Spot Studio. All rights reserved.</p> 
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Initializing login form');
    
    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const loginIcon = document.getElementById('login-icon');
    const loadingIcon = document.getElementById('loading-icon');
    const errorMessage = document.getElementById('error-message');
    
    // Debug: Check if all elements are found
    console.log('Form elements found:', {
        form: !!form,
        submitBtn: !!submitBtn,
        submitText: !!submitText,
        loginIcon: !!loginIcon,
        loadingIcon: !!loadingIcon,
        errorMessage: !!errorMessage
    });
    
    // WORKING BUTTON HANDLER - Simplified and reliable
    if (submitBtn && form) {
        submitBtn.addEventListener('click', function(e) {
            console.log('Button clicked - starting login process');
            e.preventDefault(); // Prevent default form submission
            
            // Show immediate feedback
            if (submitText) submitText.textContent = 'Processing...';
            if (loginIcon) loginIcon.classList.add('hidden');
            if (loadingIcon) loadingIcon.classList.remove('hidden');
            submitBtn.disabled = true;
            
            // Get form values
            const usernameInputLocal = document.getElementById('username');
            const passwordInputLocal = document.getElementById('password');
            
            const username = usernameInputLocal ? usernameInputLocal.value.trim() : '';
            const password = passwordInputLocal ? passwordInputLocal.value : '';
            
            console.log('Form data:', { username: !!username, password: !!password });
            
            // Basic validation
            if (!username || !password) {
                const errorConfig = {
                    type: 'validation',
                    title: 'Required Fields Missing',
                    icon: 'general',
                    field: null,
                    severity: 'warning',
                    actions: []
                };
                showError('Please fill in all required fields to continue.', errorConfig);
                resetButton();
                return;
            }
            
            // Make AJAX request
            const formData = new FormData(form);
            
            fetch('{{ route("login.authenticate") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response received:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Login response:', data);
                
                if (data.success) {
                    console.log('Login successful, redirecting to:', data.redirect);
                    if (submitText) submitText.textContent = 'Success! Redirecting...';
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 500);
                } else {
                    console.log('Login failed:', data.message);
                    const errorConfig = {
                        type: 'authentication',
                        title: 'Login Failed',
                        icon: 'general',
                        field: null,
                        severity: 'error',
                        actions: ['forgot_password']
                    };
                    showError(data.message || 'Login failed. Please check your credentials and try again.', errorConfig);
                    resetButton();
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                console.log('AJAX failed, falling back to regular form submission');
                
                resetButton();
                
                // Show an error message and fall back to normal form submission
                const errorConfig = {
                    type: 'system',
                    title: 'Connection Error',
                    icon: 'system',
                    field: null,
                    severity: 'warning',
                    actions: ['retry']
                };
                showError('Network error occurred. Attempting alternative login method...', errorConfig);
                
                // Wait a moment to show the error, then fallback
                setTimeout(() => {
                    // Create a new form element to bypass the AJAX handler
                    const fallbackForm = document.createElement('form');
                    fallbackForm.method = 'POST';
                    fallbackForm.action = form.action;
                    
                    // Copy all form data
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        fallbackForm.appendChild(input);
                    }
                    
                    document.body.appendChild(fallbackForm);
                    fallbackForm.submit();
                }, 1500);
            });
        });
        
        // Function to reset button state
        function resetButton() {
            if (submitText) submitText.textContent = 'Sign In';
            if (loginIcon) loginIcon.classList.remove('hidden');
            if (loadingIcon) loadingIcon.classList.add('hidden');
            submitBtn.disabled = false;
        }
        
        console.log('Working login handler added successfully');
    } else {
        console.error('Form or submit button not found!');
    }
    const lockoutMessage = document.getElementById('lockout-message');
    const countdownElement = document.getElementById('countdown');
    const usernameInput = document.getElementById('username');
    const passwordInput = document.getElementById('password');
    const errorDismissButton = document.getElementById('error-dismiss');
    
    let countdownTimer = null;
    let lockoutCheckTimer = null;
    let hasSubmitted = false; // Track if form has been submitted
    let userInteractions = {
        username: false,
        password: false
    };
    
    // Track user interactions
    usernameInput.addEventListener('input', function() {
        userInteractions.username = true;
        hideFieldError(usernameInput, document.getElementById('username-error'));
    });
    
    passwordInput.addEventListener('input', function() {
        userInteractions.password = true;
        hideFieldError(passwordInput, document.getElementById('password-error'));
    });
    
    // Real-time form validation (only after user interaction or form submission)
    usernameInput.addEventListener('blur', function() {
        if (hasSubmitted || userInteractions.username) {
            validateUsername();
        }
        checkLockoutStatus();
    });
    
    passwordInput.addEventListener('blur', function() {
        if (hasSubmitted || userInteractions.password) {
            validatePassword();
        }
    });
    
    // Handle form submission via button click and form submit event
    function handleLoginSubmit(e) {
        console.log('handleLoginSubmit called');
        
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        // Immediate visual feedback
        if (submitBtn) {
            submitBtn.disabled = true;
            console.log('Button disabled');
        }
        
        // Check if already processing
        if (submitBtn && submitBtn.disabled && submitBtn.textContent.includes('...')) {
            console.log('Already processing, aborting');
            return false;
        }
        
        console.log('Setting loading state');
        try {
            setLoading(true);
        } catch (error) {
            console.error('Error setting loading state:', error);
        }
        
        // Mark that form submission has been attempted
        hasSubmitted = true;
        
        // Simple validation check
        const usernameValue = usernameInput ? usernameInput.value.trim() : '';
        const passwordValue = passwordInput ? passwordInput.value : '';
        
        console.log('Form values:', { username: !!usernameValue, password: !!passwordValue });
        
        // Basic validation
        if (!usernameValue || !passwordValue) {
            console.log('Basic validation failed');
            setLoading(false);
            const errorConfig = {
                type: 'validation',
                title: 'Required Fields Missing',
                icon: 'general',
                field: null,
                severity: 'warning',
                actions: []
            };
            showError('Please fill in all required fields to continue.', errorConfig);
            return false;
        }
        
        console.log('Starting AJAX login request');
        try {
            hideMessages();
        } catch (error) {
            console.error('Error hiding messages:', error);
        }
        
        const formData = new FormData(form);
        
        fetch('{{ route("login.authenticate") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Login response received:', data);
            setLoading(false);
            
            if (data.success) {
                console.log('Login successful, redirecting to:', data.redirect);
                showSuccess('Login successful! Redirecting...');
                // Immediate redirect with minimal delay
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 500);
            } else if (data.locked) {
                showLockout(data.lockout_seconds, data.attempts);
                startCountdown(data.lockout_seconds);
            } else {
                // Show specific error message based on error type
                handleAuthenticationError(data);
            }
        })
        .catch(error => {
            setLoading(false);
            console.error('Login error:', error);
            
            // Use proper config for network/system errors
            const systemErrorConfig = {
                type: 'system',
                title: 'Connection Error',
                icon: 'system',
                field: null,
                severity: 'error',
                actions: ['retry', 'contact_support']
            };
            
            showError('An unexpected error occurred. Please check your internet connection and try again.', systemErrorConfig);
        });
    }
    
    // NOTE: Using simplified handler above instead of complex one
    // Complex handler commented out to avoid conflicts
    
    function validateForm() {
        let isValid = true;
        
        isValid = validateUsername() && isValid;
        isValid = validatePassword() && isValid;
        
        return isValid;
    }
    
    function validateUsername() {
        const username = usernameInput.value.trim();
        const usernameError = document.getElementById('username-error');
        
        if (!username) {
            showFieldError(usernameInput, usernameError, 'Username is required');
            return false;
        }
        
        if (username.length < 3) {
            showFieldError(usernameInput, usernameError, 'Username must be at least 3 characters');
            return false;
        }
        
        hideFieldError(usernameInput, usernameError);
        return true;
    }
    
    function validatePassword() {
        const password = passwordInput.value;
        const passwordError = document.getElementById('password-error');
        
        if (!password) {
            showFieldError(passwordInput, passwordError, 'Password is required');
            return false;
        }
        
        if (password.length < 1) {
            showFieldError(passwordInput, passwordError, 'Please enter your password');
            return false;
        }
        
        hideFieldError(passwordInput, passwordError);
        return true;
    }
    
    function showFieldError(field, errorElement, message) {
        field.classList.add('border-red-500');
        field.classList.remove('border-border-color');
        errorElement.textContent = message;
        errorElement.classList.remove('hidden');
    }
    
    function hideFieldError(field, errorElement) {
        field.classList.remove('border-red-500');
        field.classList.add('border-border-color');
        errorElement.classList.add('hidden');
    }
    
    function checkLockoutStatus() {
        const username = usernameInput.value;
        if (!username) return;
        
        fetch(`{{ route("login.lockout.status") }}?username=${encodeURIComponent(username)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.locked) {
                showLockout(data.lockout_seconds, data.attempts);
                startCountdown(data.lockout_seconds);
            } else {
                hideLockout();
            }
        })
        .catch(error => {
            console.error('Lockout status check error:', error);
        });
    }
    
    function setLoading(loading) {
        submitBtn.disabled = loading;
        if (loading) {
            submitText.textContent = 'Signing In...';
            loginIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
        } else {
            submitText.textContent = 'Sign In';
            loginIcon.classList.remove('hidden');
            loadingIcon.classList.add('hidden');
        }
    }
    
    function handleAuthenticationError(data) {
        const errorType = data.error_type || 'unknown';
        const message = data.message || 'Login failed. Please check your credentials.';
        const attempts = data.attempts || 0;
        
        // Define error configurations
        const errorConfigs = {
            'email_not_found': {
                type: 'validation',
                title: 'Username Not Found',
                icon: 'email',
                field: 'username',
                severity: 'error',
                actions: ['forgot_password']
            },
            'invalid_password': {
                type: 'authentication', 
                title: 'Incorrect Password',
                icon: 'password',
                field: 'password',
                severity: 'error',
                actions: ['forgot_password']
            },
            'account_inactive': {
                type: 'account',
                title: 'Account Inactive',
                icon: 'account',
                field: null,
                severity: 'warning',
                actions: ['contact_support']
            },
            'system_error': {
                type: 'system',
                title: 'System Error',
                icon: 'system',
                field: null,
                severity: 'error',
                actions: ['retry', 'contact_support']
            },
            'authentication_failed': {
                type: 'authentication',
                title: 'Authentication Failed',
                icon: 'general',
                field: null,
                severity: 'error',
                actions: ['forgot_password']
            },
            'invalid_email_format': {
                type: 'validation',
                title: 'Invalid Username',
                icon: 'email',
                field: 'username',
                severity: 'error',
                actions: []
            },
            'missing_password': {
                type: 'validation',
                title: 'Password Required',
                icon: 'password', 
                field: 'password',
                severity: 'error',
                actions: []
            },
            'captcha_required': {
                type: 'validation',
                title: 'Security Verification Required',
                icon: 'general',
                field: null,
                severity: 'warning',
                actions: []
            }
        };
        
        const config = errorConfigs[errorType] || {
            type: 'general',
            title: 'Login Error',
            icon: 'general',
            field: null,
            severity: 'error',
            actions: ['retry']
        };
        
        showError(message, config, attempts);
        
        if (config.field) {
            highlightField(config.field);
        }
    }
    }
    
    function showError(message, config, attempts = 0) {
        const errorIcon = document.getElementById('error-icon');
        const errorTitle = document.getElementById('error-title');
        const errorDescription = document.getElementById('error-description');
        const errorActions = document.getElementById('error-actions');
        
        if (!errorIcon || !errorTitle || !errorDescription || !errorActions) {
            console.error('Error: Required error UI elements not found in DOM');
            return;
        }
        
        // Define visual themes
        const themes = {
            error: {
                border: 'border-red-400',
                bg: 'bg-red-50',
                titleColor: 'text-red-800',
                textColor: 'text-red-700',
                iconColor: 'text-red-500'
            },
            warning: {
                border: 'border-orange-400', 
                bg: 'bg-orange-50',
                titleColor: 'text-orange-800',
                textColor: 'text-orange-700',
                iconColor: 'text-orange-500'
            },
            info: {
                border: 'border-blue-400',
                bg: 'bg-blue-50', 
                titleColor: 'text-blue-800',
                textColor: 'text-blue-700',
                iconColor: 'text-blue-500'
            }
        };
        
        // Define icons
        const icons = {
            email: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>',
            password: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path></svg>',
            user_type: '<svg fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>',
            account: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zM8 6a2 2 0 114 0v1H8V6z" clip-rule="evenodd"></path></svg>',
            system: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>',
            general: '<svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
        };
        
        // Apply theme
        const theme = themes[config.severity] || themes.error;
        errorMessage.className = `mb-4 rounded-lg border-l-4 shadow-sm transition-all duration-300 ease-in-out ${theme.border} ${theme.bg}`;
        
        // Set icon
        errorIcon.innerHTML = icons[config.icon] || icons.general;
        errorIcon.className = `flex-shrink-0 w-6 h-6 mr-3 mt-0.5 ${theme.iconColor}`;
        
        // Set title
        errorTitle.textContent = config.title;
        errorTitle.className = `font-semibold text-sm mb-1 ${theme.titleColor}`;
        
        // Set description with attempt counter
        let description = message;
        if (attempts > 1 && attempts < 5) {
            description += ` (Attempt ${attempts} of 5)`;
        }
        errorDescription.textContent = description;
        errorDescription.className = `text-sm ${theme.textColor} opacity-90`;
        
        // Create action buttons
        errorActions.innerHTML = '';
        errorActions.classList.add('hidden');
        
        if (config.actions && config.actions.length > 0) {
            errorActions.classList.remove('hidden');
            
            config.actions.forEach(action => {
                const actionButton = createActionButton(action, theme);
                if (actionButton) {
                    errorActions.appendChild(actionButton);
                }
            });
        }
        
        // Show warning for multiple attempts
        if (attempts > 0 && attempts < 5) {
            const warningDiv = document.createElement('div');
            warningDiv.className = `mt-2 text-xs ${theme.textColor} opacity-75`;
            warningDiv.innerHTML = `
                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                Account will be locked for ${25 + (5 * (attempts + 1))} seconds after ${5 - attempts} more failed ${5 - attempts === 1 ? 'attempt' : 'attempts'}.
            `;
            errorDescription.parentNode.appendChild(warningDiv);
        }
        
        // Show error
        errorMessage.classList.remove('hidden');
        errorMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Set focus for screen readers
        errorMessage.focus();
    }
    
    function createActionButton(action, theme) {
        const actions = {
            'forgot_password': {
                text: 'Reset Password',
                href: '{{ route("simple.password.request") }}',
                icon: '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-6 6c-2.072 0-3.927-.822-5.275-2.156M15 7l-3.5 3.5M15 7l3.5 3.5M9 10a2 2 0 00-2 2m-4 0a6 6 0 006 6c2.072 0 3.927-.822 5.275-2.156"></path></svg>'
            },
            'contact_support': {
                text: 'Contact Support',
                href: 'mailto:support@shinespotbookings.com',
                icon: '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>'
            },
            'retry': {
                text: 'Try Again',
                action: 'retry',
                icon: '<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>'
            }
        };
        
        const actionConfig = actions[action];
        if (!actionConfig) return null;
        
        const button = document.createElement(actionConfig.href ? 'a' : 'button');
        button.className = `inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md transition-colors mr-2 ${theme.titleColor} hover:bg-black hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-current`;
        button.innerHTML = actionConfig.icon + actionConfig.text;
        
        if (actionConfig.href) {
            button.href = actionConfig.href;
            if (actionConfig.href.startsWith('mailto:')) {
                button.target = '_blank';
                button.rel = 'noopener noreferrer';
            }
        } else if (actionConfig.action === 'retry') {
            button.onclick = function() {
                hideMessages();
                form.querySelector('input[type="password"]').focus();
            };
        }
        
        return button;
    }
    
    function highlightField(fieldType) {
        // Remove previous highlights
        usernameInput.classList.remove('border-red-500', 'ring-red-500');
        passwordInput.classList.remove('border-red-500', 'ring-red-500');
        
        // Add highlight to specific field
        let targetField = null;
        switch (fieldType) {
            case 'email':
            case 'username':
                targetField = usernameInput;
                break;
            case 'password':
                targetField = passwordInput;
                break;
        }
        
        if (targetField) {
            targetField.classList.add('border-red-500', 'ring-1', 'ring-red-500');
            targetField.focus();
            
            // Remove highlight after user interaction
            const removeHighlight = () => {
                targetField.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
                targetField.removeEventListener('focus', removeHighlight);
                targetField.removeEventListener('input', removeHighlight);
                targetField.removeEventListener('change', removeHighlight);
            };
            
            targetField.addEventListener('focus', removeHighlight);
            targetField.addEventListener('input', removeHighlight);
            targetField.addEventListener('change', removeHighlight);
        }
    }
    
    function showSuccess(message) {
        const successMessage = document.getElementById('success-message');
        const successText = document.getElementById('success-text');
        
        successText.textContent = message;
        successMessage.classList.remove('hidden');
        successMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function showLockout(seconds, attempts) {
        const lockoutText = document.getElementById('lockout-text');
        
        // Create more informative lockout message
        let messageText = '';
        if (attempts >= 5) {
            messageText = `Account locked due to ${attempts} failed login attempts. Please wait <span id="countdown" class="font-mono font-bold">${seconds}</span> seconds before trying again.`;
        } else {
            messageText = `Account temporarily locked after ${attempts} failed attempt${attempts !== 1 ? 's' : ''}. Please wait <span id="countdown" class="font-mono font-bold">${seconds}</span> seconds.`;
        }
        
        lockoutText.innerHTML = messageText;
        lockoutMessage.classList.remove('hidden');
        disableForm(true);
        lockoutMessage.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        
        // Set focus for screen readers
        lockoutMessage.focus();
    }
    
    function hideLockout() {
        lockoutMessage.classList.add('hidden');
        disableForm(false);
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }
    
    function hideMessages() {
        errorMessage.classList.add('hidden');
        lockoutMessage.classList.add('hidden');
        document.getElementById('success-message').classList.add('hidden');
    }
    
    function disableForm(disabled) {
        submitBtn.disabled = disabled;
        usernameInput.disabled = disabled;
        passwordInput.disabled = disabled;
        
        if (disabled) {
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Please Wait...';
        } else {
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitText.textContent = 'Sign In';
        }
    }
    
    function startCountdown(seconds) {
        if (countdownTimer) {
            clearInterval(countdownTimer);
        }
        
        let timeLeft = seconds;
        const countdownDisplay = document.getElementById('countdown');
        
        countdownTimer = setInterval(() => {
            timeLeft--;
            if (countdownDisplay) {
                countdownDisplay.textContent = timeLeft;
            }
            
            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                hideLockout();
            }
        }, 1000);
    }
    
    // Error dismiss functionality
    if (errorDismissButton) {
        errorDismissButton.addEventListener('click', function() {
            errorMessage.classList.add('hidden');
            // Return focus to the relevant form field or first form element
            if (hasSubmitted) {
                usernameInput.focus();
            }
        });
        
        // Keyboard support for error dismiss
        errorDismissButton.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    }
    
    // Keyboard navigation support for message containers
    [errorMessage, lockoutMessage, document.getElementById('success-message')].forEach(container => {
        if (container) {
            container.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !container.classList.contains('hidden')) {
                    if (container === errorMessage && errorDismissButton) {
                        errorDismissButton.click();
                    }
                }
            });
        }
    });
    
    // Check lockout status on page load
    setTimeout(checkLockoutStatus, 500);
    
    // Add event listener for password toggle button as backup
    const passwordToggleBtn = document.getElementById('password-toggle-btn');
    if (passwordToggleBtn) {
        passwordToggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            togglePasswordVisibility();
        });
        
        // Add keyboard support
        passwordToggleBtn.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                togglePasswordVisibility();
            }
        });
    }
});

// Password visibility toggle function
function togglePasswordVisibility() {
    console.log('Toggle password visibility called');
    
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('password-eye-icon');
    const toggleBtn = document.getElementById('password-toggle-btn');
    
    if (!passwordInput || !eyeIcon) {
        console.error('Password input or eye icon not found', {
            passwordInput: !!passwordInput,
            eyeIcon: !!eyeIcon
        });
        return;
    }
    
    console.log('Current password input type:', passwordInput.type);
    
    if (passwordInput.type === 'password') {
        // Show password as text
        passwordInput.type = 'text';
        console.log('Changed to text input - password now visible');
        
        // Change to "eye with slash" icon (indicating password is visible, click to hide)
        eyeIcon.outerHTML = `
            <svg id="password-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
            </svg>
        `;
        
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Hide password');
            toggleBtn.setAttribute('aria-label', 'Hide password');
        }
    } else {
        // Hide password (show as dots)
        passwordInput.type = 'password';
        console.log('Changed to password input - password now hidden');
        
        // Change to "open eye" icon (indicating password is hidden, click to show)
        eyeIcon.outerHTML = `
            <svg id="password-eye-icon" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        `;
        
        if (toggleBtn) {
            toggleBtn.setAttribute('title', 'Show password');
            toggleBtn.setAttribute('aria-label', 'Show password');
        }
    }
    
    console.log('New password input type:', passwordInput.type);
    
    // Re-add event listeners after changing outerHTML
    const newEyeIcon = document.getElementById('password-eye-icon');
    if (newEyeIcon && toggleBtn) {
        // Remove old event listeners and add new ones
        toggleBtn.onclick = togglePasswordVisibility;
    }
    
    // Maintain focus on password input after toggle
    setTimeout(() => {
        passwordInput.focus();
        // Move cursor to end of input
        passwordInput.setSelectionRange(passwordInput.value.length, passwordInput.value.length);
    }, 10);
}
</script>

<!-- Enhanced Password Toggle with Improved UI -->
<script>
// Enhanced Password visibility toggle function with beautiful animations
function togglePasswordVisibility() {
    console.log('🎨 Enhanced Toggle function called');
    
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('password-toggle-btn');
    const eyeIcon = document.getElementById('eye-open');
    
    if (!passwordInput) {
        console.error('❌ Password input not found');
        return;
    }
    
    console.log('📋 Current type:', passwordInput.type);
    
    // Add beautiful button press animation
    if (toggleBtn) {
        toggleBtn.style.transform = 'scale(0.92)';
        toggleBtn.style.boxShadow = 'inset 0 2px 4px rgba(0,0,0,0.1)';
        
        setTimeout(() => {
            toggleBtn.style.transform = 'scale(1)';
            toggleBtn.style.boxShadow = '';
        }, 150);
        
        // Add ripple effect
        const ripple = document.createElement('div');
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(var(--accent), 0.3);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
            left: 50%;
            top: 50%;
            width: 20px;
            height: 20px;
            margin-left: -10px;
            margin-top: -10px;
        `;
        
        toggleBtn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 600);
    }
    
    // Toggle the input type with enhanced visual feedback
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        console.log('👁️ Password is now VISIBLE');
        
        // Enhanced visual feedback for showing password
        passwordInput.style.cssText = `
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-color: hsl(var(--accent));
            box-shadow: 0 0 0 3px rgba(var(--accent), 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
            transform: scale(1.01);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        // Icon animation
        if (eyeIcon) {
            eyeIcon.style.cssText = `
                color: hsl(var(--accent));
                transform: scale(1.2) rotate(10deg);
                filter: drop-shadow(0 0 6px rgba(var(--accent), 0.4));
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            `;
        }
        
        // Update button attributes
        if (toggleBtn) {
            toggleBtn.title = 'Hide password';
            toggleBtn.setAttribute('aria-label', 'Hide password');
            toggleBtn.style.background = 'linear-gradient(135deg, rgba(var(--accent), 0.1) 0%, rgba(var(--accent), 0.15) 100%)';
        }
        
    } else {
        passwordInput.type = 'password';
        console.log('🙈 Password is now HIDDEN');
        
        // Enhanced visual feedback for hiding password
        passwordInput.style.cssText = `
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: hsl(var(--warning));
            box-shadow: 0 0 0 3px rgba(var(--warning), 0.1), 0 1px 3px rgba(0, 0, 0, 0.1);
            transform: scale(1.01);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        
        // Icon animation
        if (eyeIcon) {
            eyeIcon.style.cssText = `
                color: #64748b;
                transform: scale(1) rotate(0deg);
                filter: none;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            `;
        }
        
        // Update button attributes
        if (toggleBtn) {
            toggleBtn.title = 'Show password';
            toggleBtn.setAttribute('aria-label', 'Show password');
            toggleBtn.style.background = '';
        }
    }
    
    // Reset styles after animation
    setTimeout(() => {
        passwordInput.style.cssText = '';
        if (eyeIcon && passwordInput.type === 'password') {
            eyeIcon.style.cssText = '';
        }
        if (toggleBtn && passwordInput.type === 'password') {
            toggleBtn.style.background = '';
        }
    }, 1200);
    
    // Keep focus on the input with smooth transition
    passwordInput.focus();
    
    // Subtle success feedback
    if (passwordInput.value.length > 0) {
        const feedback = document.createElement('div');
        feedback.style.cssText = `
            position: absolute;
            right: -120px;
            top: 50%;
            transform: translateY(-50%);
            background: hsl(var(--accent));
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            animation: fadeInOut 2s ease-in-out;
            pointer-events: none;
            z-index: 1000;
        `;
        feedback.textContent = passwordInput.type === 'text' ? '👁️ Visible' : '🔒 Hidden';
        
        passwordInput.parentElement.style.position = 'relative';
        passwordInput.parentElement.appendChild(feedback);
        setTimeout(() => feedback.remove(), 2000);
    }
    
    console.log('✨ Enhanced toggle complete, new type:', passwordInput.type);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    @keyframes fadeInOut {
        0%, 100% { opacity: 0; transform: translateY(-50%) translateX(10px); }
        20%, 80% { opacity: 1; transform: translateY(-50%) translateX(0); }
    }
    
    #password-toggle-btn:hover {
        background: linear-gradient(135deg, rgba(var(--accent), 0.05) 0%, rgba(var(--accent), 0.1) 100%);
        transform: scale(1.02);
    }
    
    #eye-open:hover {
        color: hsl(var(--accent)) !important;
        transform: scale(1.1) !important;
        filter: drop-shadow(0 0 4px rgba(var(--accent), 0.3)) !important;
    }
`;
document.head.appendChild(style);

// Ensure function is available globally
window.togglePasswordVisibility = togglePasswordVisibility;
console.log('🎨 Enhanced password toggle with beautiful UI loaded and ready!');
</script>

@endsection
