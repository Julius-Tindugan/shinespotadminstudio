@extends('layouts.auth')

@section('content')
<style>
    :root {
        --background: 210 20% 98%;
        --card-bg: 0 0% 100%;
        --primary-text: 220 20% 10%;
        --secondary-text: 220 10% 40%;
        --border-color: 220 13% 91%;
        --accent: 43 74% 49%;
        --accent-hover: 43 90% 45%;
        --danger: 0 72% 51%;
        --success: 142 72% 29%;
        --input-border: 220 13% 91%;
        --input-bg: 0 0% 100%;
    }
    
    .password-requirements {
        font-size: 0.875rem;
        color: hsl(var(--secondary-text));
        margin-top: 0.75rem;
        background: hsl(var(--background));
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid hsl(var(--border-color));
    }
    
    .password-requirement {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }
    
    .password-requirement:last-child {
        margin-bottom: 0;
    }
    
    .password-requirement.valid {
        color: hsl(var(--success));
    }
    
    .password-requirement.invalid {
        color: hsl(var(--secondary-text));
    }
    
    .password-requirement .icon {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.5rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .password-strength-meter {
        height: 4px;
        background-color: hsl(var(--border-color));
        border-radius: 2px;
        margin-top: 0.5rem;
        overflow: hidden;
    }
    
    .password-strength-fill {
        height: 100%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }
    
    .strength-weak { 
        background-color: hsl(var(--danger)); 
        width: 25%; 
    }
    
    .strength-fair { 
        background-color: hsl(38 92% 50%); 
        width: 50%; 
    }
    
    .strength-good { 
        background-color: hsl(var(--accent)); 
        width: 75%; 
    }
    
    .strength-strong { 
        background-color: hsl(var(--success)); 
        width: 100%; 
    }
    
    .shine-icon {
        display: inline-block;
        animation: shine 2s ease-in-out infinite;
    }
    
    @keyframes shine {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>

<div class="space-y-6">
    <!-- Logo and Title -->
    <div class="text-center">
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.svg') }}" alt="Shine Spot Studio Logo" class="w-32 h-32">
        </div>
        <h1 class="mt-4 text-2xl font-bold text-primary-text">Reset Your Password</h1>
        <p class="mt-1 text-sm text-secondary-text">
            Create a new secure password for your <strong class="text-primary-text">{{ ucfirst($userType ?? 'user') }}</strong> account
        </p>
    </div>
    
    <!-- Main Form Card -->
    <div class="bg-card-bg p-8 rounded-lg shadow-sm border border-border-color">
        <!-- Security Badge -->
        <div class="mb-6 flex items-center justify-center p-3 rounded-lg border border-border-color bg-background">
            <svg class="w-4 h-4 mr-2 shine-icon" style="color: hsl(var(--accent));" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-xs font-medium text-secondary-text">Secure password reset via Shine Spot Authentication</span>
        </div>

        <!-- Error Messages -->
        @if (session('error'))
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg border border-red-200" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
        <form method="POST" action="{{ route('custom.password.update', $token) }}" id="reset-password-form" novalidate>
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <!-- Email Display -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-primary-text mb-2">
                    Email Address
                </label>
                <div class="w-full px-4 py-3 border border-border-color rounded-lg bg-background text-primary-text font-medium">
                    {{ $email }}
                </div>
                <p class="text-xs text-secondary-text mt-1">Resetting password for this account</p>
            </div>
            
            <!-- New Password Field -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-primary-text mb-2">
                    New Password
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="new-password"
                           class="w-full px-4 py-3 pr-12 border border-input-border rounded-lg bg-input-bg text-primary-text placeholder-secondary-text focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors @error('password') border-red-500 @enderror"
                           placeholder="Enter your new password">
                    <button type="button" 
                            id="toggle-password" 
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-secondary-text hover:text-primary-text transition-colors">
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
                <div id="strength-text" class="text-xs mt-1 text-secondary-text"></div>
                
                <!-- Password Requirements -->
                <div id="password-requirements" class="password-requirements" style="display: none;">
                    <div class="text-xs font-semibold text-primary-text mb-3">Password must contain:</div>
                    <div class="space-y-1">
                        <div class="password-requirement invalid" id="req-length">
                            <span class="icon">✗</span>
                            <span>At least 8 characters</span>
                        </div>
                        <div class="password-requirement invalid" id="req-lowercase">
                            <span class="icon">✗</span>
                            <span>One lowercase letter (a-z)</span>
                        </div>
                        <div class="password-requirement invalid" id="req-uppercase">
                            <span class="icon">✗</span>
                            <span>One uppercase letter (A-Z)</span>
                        </div>
                        <div class="password-requirement invalid" id="req-number">
                            <span class="icon">✗</span>
                            <span>One number (0-9)</span>
                        </div>
                        <div class="password-requirement invalid" id="req-special">
                            <span class="icon">✗</span>
                            <span>One special character (@$!%*?&)</span>
                        </div>
                    </div>
                </div>
                
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password Field -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-primary-text mb-2">
                    Confirm New Password
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       class="w-full px-4 py-3 border border-input-border rounded-lg bg-input-bg text-primary-text placeholder-secondary-text focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors @error('password_confirmation') border-red-500 @enderror"
                       placeholder="Confirm your new password">
                <div id="password-match" class="text-xs mt-2" style="display: none;"></div>
                @error('password_confirmation')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="mb-6">
                <button type="submit" 
                        id="submit-button"
                        class="w-full bg-accent hover:bg-accent-hover text-white font-semibold py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md flex items-center justify-center"
                        style="background-color: hsl(var(--accent)); color: white;">
                    <svg id="submit-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="submit-text">Reset Password</span>
                </button>
            </div>

            <!-- Back to Login Link -->
            <div class="text-center">
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center text-sm font-medium transition-colors hover:underline"
                   style="color: hsl(var(--accent));">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Login
                </a>
            </div>
        </form>
    </div>
    
    <!-- Footer -->
    <div class="text-center text-xs text-secondary-text">
        <p class="flex items-center justify-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
            </svg>
            Secure password reset protected by Shine Spot Authentication
        </p>
        <p class="mt-2">&copy; {{ date('Y') }} Shine Spot Studio. All rights reserved.</p>
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
        const submitSpinner = document.getElementById('submit-spinner');
        const submitText = document.getElementById('submit-text');

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
                strengthText.style.color = 'hsl(var(--danger))';
            } else if (validCount < 4) {
                strengthFill.classList.add('strength-fair');
                strengthText.textContent = 'Fair password';
                strengthText.style.color = 'hsl(38 92% 50%)';
            } else if (validCount < 5) {
                strengthFill.classList.add('strength-good');
                strengthText.textContent = 'Good password';
                strengthText.style.color = 'hsl(var(--accent))';
            } else {
                strengthFill.classList.add('strength-strong');
                strengthText.textContent = 'Strong password';
                strengthText.style.color = 'hsl(var(--success))';
            }
        }

        function checkPasswordMatch() {
            const password = passwordField.value;
            const confirm = confirmField.value;
            
            if (confirm.length > 0) {
                passwordMatch.style.display = 'block';
                if (password === confirm) {
                    passwordMatch.textContent = '✓ Passwords match';
                    passwordMatch.style.color = 'hsl(var(--success))';
                } else {
                    passwordMatch.textContent = '✗ Passwords do not match';
                    passwordMatch.style.color = 'hsl(var(--danger))';
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
            
            submitButton.disabled = !(isValidPassword && passwordsMatch);
        }

        // Form submission with loading state
        document.getElementById('reset-password-form').addEventListener('submit', function(e) {
            submitButton.disabled = true;
            submitSpinner.classList.remove('hidden');
            submitText.textContent = 'Resetting Password...';
            
            // Re-enable after 10 seconds in case of error
            setTimeout(function() {
                if (submitText.textContent === 'Resetting Password...') {
                    submitButton.disabled = false;
                    submitSpinner.classList.add('hidden');
                    submitText.textContent = 'Reset Password';
                }
            }, 10000);
        });

        // Security: Clear password fields on page unload
        window.addEventListener('beforeunload', function() {
            passwordField.value = '';
            confirmField.value = '';
        });
    });
</script>
@endsection