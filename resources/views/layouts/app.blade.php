<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class=""><!-- {{-- The 'dark' class will be toggled here --}} -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ rtrim(config('app.url'), '/') }}">
    <title>Admin Dashboard - Shine Spot Studio</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Prevent Flash of Unstyled Content (FOUC) - Hide page until CSS loads -->
    <style>
        /* Hide page content initially to prevent FOUC */
        html { 
            visibility: hidden; 
            opacity: 0; 
        }
        /* Ensure page becomes visible once CSS is loaded */
        html.css-loaded { 
            visibility: visible !important; 
            opacity: 1 !important; 
            transition: opacity 0.1s ease-in; 
        }
        /* Fallback: Show content after 300ms even if CSS hasn't loaded */
        @keyframes showPage { 
            to { 
                visibility: visible; 
                opacity: 1; 
            } 
        }
        html { 
            animation: showPage 0s 0.3s forwards; 
        }
        /* Critical styles to prevent layout shift */
        body { 
            margin: 0; 
            min-height: 100vh; 
            background-color: #f8fafc; 
        }
        /* Hide SVG icons initially to prevent file-like appearance */
        svg { 
            visibility: inherit; 
        }
    </style>
    
    <!-- {{-- Google Fonts: Inter and Poppins --}} -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"><!-- {{-- Alpine.js for interactive components --}} -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- {{-- Vite for CSS and JS --}} --> @vite(['resources/css/app.css', 'resources/css/dashboard-responsive.css', 'resources/js/app.js'])
    
    <!-- Mark CSS as loaded once Vite assets are processed -->
    <script>
        // Show page immediately when CSS is ready
        (function() {
            // Check if stylesheets are loaded
            function checkCSSLoaded() {
                var sheets = document.styleSheets;
                for (var i = 0; i < sheets.length; i++) {
                    try {
                        if (sheets[i].cssRules && sheets[i].cssRules.length > 0) {
                            document.documentElement.classList.add('css-loaded');
                            return true;
                        }
                    } catch (e) {
                        // Cross-origin stylesheet, skip
                    }
                }
                return false;
            }
            
            // Try immediately
            if (document.readyState !== 'loading') {
                checkCSSLoaded();
            }
            
            // Also check on DOMContentLoaded as fallback
            document.addEventListener('DOMContentLoaded', function() {
                document.documentElement.classList.add('css-loaded');
            });
            
            // Immediate check after a micro-task
            setTimeout(function() {
                if (!document.documentElement.classList.contains('css-loaded')) {
                    checkCSSLoaded();
                }
            }, 0);
        })();
    </script>
    
    <!-- UI Manager for component functionality -->
    <script src="{{ asset('js/ui-manager.js') }}"></script>
    <!-- Global Toast Notification System -->
    <script src="{{ asset('js/toast.js') }}"></script>
    @if (config('app.debug', false) && request()->get('debug_ui') === 'true')
        <!-- UI component test for debugging - only load when debug_ui=true is passed -->
        <script src="{{ asset('js/ui-test.js') }}"></script>
        <!-- UI validation script for testing - only load when debug_ui=true is passed -->
        <script src="{{ asset('js/ui-validation.js') }}"></script>
    @endif
    
    <!-- Stack for page-specific styles -->
    @stack('styles')

</head>

<body class="bg-background font-sans text-primary-text antialiased overflow-x-hidden">
    <div id="app" class="flex h-screen overflow-hidden relative">
        <!-- {{-- Sidebar --}} --> @include('layouts.partials.sidebar')
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            <!-- {{-- Header --}} --> @include('layouts.partials.header') <!-- {{-- Main Content --}} -->
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="p-4 sm:p-6 md:p-8 lg:p-10">
                    @yield('content')
                </div>
            </main>
        </div>

    </div>
    <!-- {{-- Modals --}} -->
    {{-- Only include existing modal files --}}
    @include('modals.new-booking')
    @include('modals.report-generation')
    @include('modals.logout-confirmation')

    {{-- Commented out until files are created --}}
    {{-- @include('modals.view-booking') --}}
    {{-- @include('modals.settings') --}}
    {{-- @include('modals.booking-management') --}}
    {{-- @include('modals.calendar-management') --}}
    {{-- @include('modals.studio-management') --}}
    {{-- @include('modals.finance-management') --}}

    <!-- {{-- Toast Container --}} -->
    @include('layouts.partials.toast') <!-- Stack for page-specific scripts --> @stack('scripts')
    <!-- Yield for page-specific scripts --> @yield('scripts')
</body>

</html>
