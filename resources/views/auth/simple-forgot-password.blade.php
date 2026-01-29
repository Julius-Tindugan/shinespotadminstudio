@extends('layouts.auth')

@section('content')
<div class="space-y-6">
    <!-- Logo and Title -->
    <div class="text-center">
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.svg') }}" alt="Shine Spot Studio Logo" class="w-32 h-32">
        </div>
        <h1 class="mt-4 text-2xl font-bold text-primary-text">Reset Your Password</h1>
        <p class="mt-1 text-sm text-secondary-text">Enter your email address to receive a password reset link</p>
    </div>
    
    <!-- Reset Form -->
    <div class="bg-card-bg p-8 rounded-lg shadow-sm border border-border-color">
        @if (session('status'))
        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            {{ session('status') }}
        </div>
        @endif
        
        @if ($errors->any())
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif
        
        <form method="POST" action="{{ route('simple.password.email') }}" id="resetForm">
            @csrf
            
            <!-- Email Field -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-primary-text mb-2">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-input-border rounded-lg bg-input-bg text-primary-text placeholder-placeholder-text focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-colors" 
                    placeholder="Enter your email address"
                    required
                    autocomplete="email"
                >
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-accent hover:bg-accent-hover text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center"
                >
                    <svg id="submit-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span id="submit-text">Send Reset Link</span>
                </button>
            </div>
            
            <!-- Back to Login -->
            <div class="text-center mt-6">
                <a href="{{ route('login') }}" class="text-sm font-medium text-accent hover:text-accent-hover transition-colors">
                    ← Back to Login
                </a>
            </div>
        </form>
    </div>
    
    <!-- Footer -->
    <div class="text-center text-xs text-secondary-text">
        <p>&copy; 2025 Shine Spot Studio. All rights reserved.</p>
    </div>
</div>

<script>
document.getElementById('resetForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submit-spinner');
    const text = document.getElementById('submit-text');
    
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    text.textContent = 'Sending...';
});
</script>
@endsection