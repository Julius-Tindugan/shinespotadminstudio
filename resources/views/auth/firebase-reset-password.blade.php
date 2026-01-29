<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Reset Password' }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .password-requirements {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
            background: #f9fafb;
            padding: 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid #e5e7eb;
        }
        .password-requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        .password-requirement:last-child {
            margin-bottom: 0;
        }
        .password-requirement.valid {
            color: #10b981;
        }
        .password-requirement.invalid {
            color: #ef4444;
        }
        .password-requirement .icon {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            font-weight: bold;
        }
        .password-strength-meter {
            height: 6px;
            background-color: #e5e7eb;
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }
        .password-strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 3px;
        }
        .strength-weak { background-color: #ef4444; width: 25%; }
        .strength-fair { background-color: #f59e0b; width: 50%; }
        .strength-good { background-color: #3b82f6; width: 75%; }
        .strength-strong { background-color: #10b981; width: 100%; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m0 0a2 2 0 00-2-2m2 2v3a2 2 0 01-2 2h-9a2 2 0 01-2-2V9a2 2 0 012-2h9a2 2 0 012 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h1>
                <p class="text-sm text-gray-600">
                    @if(isset($email) && $email)
                        Enter your new password for <strong>{{ $email }}</strong>
                    @else
                        Enter your new password below
                    @endif
                </p>
                <div class="mt-3 text-xs text-orange-700 bg-orange-50 p-3 rounded-lg border border-orange-200">
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        This link will expire in 15 minutes for security
                    </div>
                </div>
            </div>

            <!-- Error Messages -->
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Main Form -->
            <form method="POST" action="{{ route('firebase.password.update') }}" id="reset-password-form" novalidate>
                @csrf
                <input type="hidden" name="oobCode" value="{{ $oobCode }}">
                
                <!-- New Password Field -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               autocomplete="new-password"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-500 @enderror"
                               placeholder="Enter your new password">
                        <button type="button" 
                                id="toggle-password" 
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition duration-200">
                            <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Password Strength Meter -->
                    <div class="password-strength-meter">
                        <div id="strength-fill" class="password-strength-fill"></div>
                    </div>
                    <div id="strength-text" class="text-xs mt-1 text-gray-500"></div>
                    
                    <!-- Password Requirements -->
                    <div id="password-requirements" class="password-requirements" style="display: none;">
                        <div class="text-xs font-medium text-gray-700 mb-2">Password Requirements:</div>
                        <div class="password-requirement" id="req-length">
                            <span class="icon">✗</span>
                            At least 8 characters long
                        </div>
                        <div class="password-requirement" id="req-lowercase">
                            <span class="icon">✗</span>
                            Contains lowercase letter
                        </div>
                        <div class="password-requirement" id="req-uppercase">
                            <span class="icon">✗</span>
                            Contains uppercase letter
                        </div>
                        <div class="password-requirement" id="req-number">
                            <span class="icon">✗</span>
                            Contains number
                        </div>
                        <div class="password-requirement" id="req-special">
                            <span class="icon">✗</span>
                            Contains special character (@$!%*?&)
                        </div>
                    </div>
                    
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        Confirm New Password
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password_confirmation') border-red-500 @enderror"
                           placeholder="Confirm your new password">
                    <div id="password-match" class="text-xs mt-2" style="display: none;"></div>
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- reCAPTCHA -->
                <div class="mb-6 flex justify-center">
                    <div class="g-recaptcha" 
                         data-sitekey="{{ config('captcha.sitekey') }}"
                         data-callback="enableSubmitButton"
                         data-expired-callback="disableSubmitButton"></div>
                    @error('g-recaptcha-response')
                        <p class="mt-2 text-sm text-red-600 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="mb-6">
                    <button type="submit" 
                            id="submit-button"
                            disabled
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 shadow-md">
                        Reset Password
                    </button>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 transition duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Login
                    </a>
                </div>
            </form>
        </div>

        <!-- Security Notice -->
        <div class="text-center text-xs text-gray-500 mt-6 px-4">
            <p class="flex items-center justify-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                Secure password reset protected by Firebase Authentication
            </p>
            <p class="mt-1">
                Protected by reCAPTCHA and subject to the Google 
                <a href="https://policies.google.com/privacy" class="text-blue-600 hover:text-blue-800">Privacy Policy</a> 
                and 
                <a href="https://policies.google.com/terms" class="text-blue-600 hover:text-blue-800">Terms of Service</a>.
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const confirmField = document.getElementById('password_confirmation');
            const toggleButton = document.getElementById('toggle-password');
            const requirementsDiv = document.getElementById('password-requirements');
            const submitButton = document.getElementById('submit-button');
            const strengthFill = document.getElementById('strength-fill');
            const strengthText = document.getElementById('strength-text');
            const passwordMatch = document.getElementById('password-match');

            let recaptchaValid = false;

            // reCAPTCHA functions
            window.enableSubmitButton = function() {
                recaptchaValid = true;
                updateSubmitButton();
            };
            
            window.disableSubmitButton = function() {
                recaptchaValid = false;
                updateSubmitButton();
            };

            // Password visibility toggle
            toggleButton.addEventListener('click', function() {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);
                
                // Update icon
                const eyeIcon = document.getElementById('eye-icon');
                if (type === 'text') {
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.87 6.87m11.263 11.263l-2.132-2.132m2.132 2.132L21 21" />';
                } else {
                    eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                }
            });

            // Password validation and requirements checking
            passwordField.addEventListener('input', function() {
                const password = this.value;
                
                if (password.length > 0) {
                    requirementsDiv.style.display = 'block';
                } else {
                    requirementsDiv.style.display = 'none';
                    strengthFill.className = 'password-strength-fill';
                    strengthText.textContent = '';
                    updateSubmitButton();
                    return;
                }

                validatePassword(password);
                updateStrengthMeter(password);
                checkPasswordMatch();
                updateSubmitButton();
            });

            // Password confirmation checking
            confirmField.addEventListener('input', function() {
                checkPasswordMatch();
                updateSubmitButton();
            });

            function validatePassword(password) {
                const requirements = {
                    'req-length': password.length >= 8,
                    'req-lowercase': /[a-z]/.test(password),
                    'req-uppercase': /[A-Z]/.test(password),
                    'req-number': /\d/.test(password),
                    'req-special': /[@$!%*?&]/.test(password)
                };

                for (const [id, isValid] of Object.entries(requirements)) {
                    const element = document.getElementById(id);
                    if (isValid) {
                        element.classList.add('valid');
                        element.classList.remove('invalid');
                        element.querySelector('.icon').textContent = '✓';
                    } else {
                        element.classList.add('invalid');
                        element.classList.remove('valid');
                        element.querySelector('.icon').textContent = '✗';
                    }
                }

                return Object.values(requirements).every(Boolean);
            }

            function updateStrengthMeter(password) {
                const requirements = {
                    length: password.length >= 8,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[@$!%*?&]/.test(password)
                };

                const validCount = Object.values(requirements).filter(Boolean).length;
                strengthFill.className = 'password-strength-fill';
                
                if (validCount < 2) {
                    strengthFill.classList.add('strength-weak');
                    strengthText.textContent = 'Weak password';
                    strengthText.className = 'text-xs mt-1 text-red-600';
                } else if (validCount < 4) {
                    strengthFill.classList.add('strength-fair');
                    strengthText.textContent = 'Fair password';
                    strengthText.className = 'text-xs mt-1 text-orange-600';
                } else if (validCount < 5) {
                    strengthFill.classList.add('strength-good');
                    strengthText.textContent = 'Good password';
                    strengthText.className = 'text-xs mt-1 text-blue-600';
                } else {
                    strengthFill.classList.add('strength-strong');
                    strengthText.textContent = 'Strong password';
                    strengthText.className = 'text-xs mt-1 text-green-600';
                }
            }

            function checkPasswordMatch() {
                const password = passwordField.value;
                const confirm = confirmField.value;
                
                if (confirm.length > 0) {
                    passwordMatch.style.display = 'block';
                    if (password === confirm) {
                        passwordMatch.textContent = '✓ Passwords match';
                        passwordMatch.className = 'text-xs mt-2 text-green-600';
                    } else {
                        passwordMatch.textContent = '✗ Passwords do not match';
                        passwordMatch.className = 'text-xs mt-2 text-red-600';
                    }
                } else {
                    passwordMatch.style.display = 'none';
                }
            }

            function updateSubmitButton() {
                const password = passwordField.value;
                const confirmPassword = confirmField.value;
                
                const isValidPassword = validatePassword(password);
                const passwordsMatch = password === confirmPassword && password.length > 0;
                
                submitButton.disabled = !(recaptchaValid && isValidPassword && passwordsMatch);
            }

            // Form submission
            document.getElementById('reset-password-form').addEventListener('submit', function(e) {
                submitButton.disabled = true;
                submitButton.textContent = 'Resetting Password...';
                
                // Re-enable after 10 seconds in case of error
                setTimeout(function() {
                    if (submitButton.textContent === 'Resetting Password...') {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Reset Password';
                    }
                }, 10000);
            });

            // Security: Clear password fields on page unload
            window.addEventListener('beforeunload', function() {
                passwordField.value = '';
                confirmField.value = '';
            });

            // Security: Prevent form resubmission
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }

            // Auto-expire warning (after 10 minutes)
            setTimeout(function() {
                if (document.body.contains(submitButton)) {
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-orange-100 border border-orange-400 text-orange-700 px-4 py-3 rounded-lg shadow-lg z-50';
                    notification.innerHTML = '<div class="flex items-center"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>This reset link will expire soon. Please complete the process quickly.</div>';
                    document.body.appendChild(notification);
                    
                    // Remove notification after 5 seconds
                    setTimeout(() => notification.remove(), 5000);
                }
            }, 10 * 60 * 1000); // 10 minutes
        });
    </script>
</body>
</html>
