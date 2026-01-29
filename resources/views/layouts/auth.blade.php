<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class=""><!-- {{-- The 'dark' class will be toggled here --}} -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Shine Spot Studio Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">
    
    <!-- Prevent Flash of Unstyled Content (FOUC) -->
    <style>
        html { 
            visibility: hidden; 
            opacity: 0; 
        }
        html.css-loaded { 
            visibility: visible !important; 
            opacity: 1 !important; 
            transition: opacity 0.1s ease-in; 
        }
        @keyframes showPage { 
            to { 
                visibility: visible; 
                opacity: 1; 
            } 
        }
        html { 
            animation: showPage 0s 0.3s forwards; 
        }
        body { 
            margin: 0; 
            min-height: 100vh; 
            background-color: #f8fafc; 
        }
    </style>
    
    <!-- {{-- Google Fonts: Inter and Poppins --}} -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"><!-- {{-- Vite for CSS and JS --}} --> @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Mark CSS as loaded -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.documentElement.classList.add('css-loaded');
        });
    </script>
</head>

<body class="bg-background font-sans text-primary-text antialiased">
    <div id="app" class="min-h-screen flex items-center justify-center p-4">
        {{-- Main Content --}} <main class="relative z-0 focus:outline-none w-full max-w-md"> @yield('content') </main>
    </div>
    <!-- {{-- Toast Container --}} --> @include('layouts.partials.toast')
</body>

</html>
