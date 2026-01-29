<!-- {{-- Toast Container --}} -->
<div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-3">
    <!-- {{-- Toasts will be dynamically added here via JavaScript --}} -->
</div>

<!-- {{-- Hidden flash message data for JavaScript --}} -->
@if(session('success'))
    <div data-flash-message="{{ session('success') }}" data-flash-type="success" style="display: none;"></div>
@endif

@if(session('error'))
    <div data-flash-message="{{ session('error') }}" data-flash-type="error" style="display: none;"></div>
@endif

@if(session('warning'))
    <div data-flash-message="{{ session('warning') }}" data-flash-type="warning" style="display: none;"></div>
@endif

@if(session('info'))
    <div data-flash-message="{{ session('info') }}" data-flash-type="info" style="display: none;"></div>
@endif

<!-- {{-- Toast Template (hidden, will be cloned by JavaScript) --}} -->
<template id="toast-template">
    <div class="toast bg-card-bg border border-border-color rounded-lg shadow-lg p-4 flex items-start w-80 transform transition-all duration-300 opacity-0 translate-x-full">
        <div class="toast-icon flex-shrink-0 w-6 h-6 mr-3"></div>
        
        <div class="flex-1 pr-2">
            <h4 class="toast-title font-medium text-sm"></h4>
            <p class="toast-message text-secondary-text text-xs mt-1"></p>
        </div>
        
        <button class="toast-close flex-shrink-0 p-1 rounded-full hover:bg-gray-100 focus:outline-none">
            <svg class="w-4 h-4 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        
        <div class="toast-progress absolute bottom-0 left-0 h-1 bg-accent rounded-bl-lg" style="width: 100%;"></div>
    </div>
</template>
