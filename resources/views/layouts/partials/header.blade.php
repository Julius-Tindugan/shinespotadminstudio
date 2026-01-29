<header class="flex items-center justify-between h-16 px-4 md:px-6 bg-card-bg border-b border-border-color flex-shrink-0 transition-colors duration-200 shadow-subtle z-[5]">
    <div class="flex items-center min-w-0 flex-1">
        <button id="sidebar-toggle-btn" class="p-2 -ml-1 mr-2 rounded-md lg:hidden text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200 flex-shrink-0 touch-manipulation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <div class="hidden md:flex items-center min-w-0">
            <img src="{{ asset('images/logo.svg') }}" alt="Shine Spot Studio Logo" class="h-9 w-auto mr-2.5 flex-shrink-0">
            <h1 class="text-lg font-bold text-primary-text truncate">Shine Spot Studio</h1>
        </div>
        
        <!-- Mobile: Show abbreviated title -->
        <div class="md:hidden flex items-center min-w-0">
            <img src="{{ asset('images/logo.svg') }}" alt="Shine Spot Studio Logo" class="h-8 w-auto mr-2 flex-shrink-0">
            <h1 class="text-base font-bold text-primary-text truncate">Shine Spot</h1>
        </div>
    </div>

   

    <div class="flex items-center space-x-2 md:space-x-4 flex-shrink-0">
        <!-- User Info and Logout -->
        <div class="flex items-center">
            
            <button type="button" data-modal-target="logout-confirmation-modal" class="flex items-center px-2 md:px-3 py-2 text-sm text-red-500 hover:bg-white-100 dark:hover:bg-black-700 rounded-lg transition-colors duration-150 touch-manipulation">
                <svg class="w-4 h-4 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="hidden md:inline">Logout</span>
            </button>
        </div>
    </div>
</header>
