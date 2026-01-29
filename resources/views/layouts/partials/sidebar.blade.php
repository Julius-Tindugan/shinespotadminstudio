<aside id="sidebar" class="flex-shrink-0 w-64 bg-card-bg border-r border-border-color flex flex-col transition-all duration-300 ease-in-out shadow-subtle fixed inset-y-0 left-0 -translate-x-full z-50 lg:relative lg:inset-auto lg:translate-x-0 lg:z-20">
    <div class="h-16 flex items-center justify-between px-4 flex-shrink-0 border-b border-border-color">
        <div class="flex items-center w-full">
            <div class="flex items-center relative">
                <!-- Logo container only visible in expanded mode -->
                <div id="sidebar-expanded-logo" class="flex-shrink-0">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-9 w-auto mr-2">
                    <img src="{{ asset('images/logo-dark.svg') }}" alt="Logo" class="h-9 w-auto mr-2 hidden">
                </div>
                <!-- Empty placeholder for collapsed state -->
                <div id="sidebar-collapsed-logo" class="hidden w-0 flex-shrink-0"></div>
                <!-- Text with proper margin to not overlay logo -->
                <span id="sidebar-user-type" class="font-bold text-xl sidebar-text ml-1 transition-all duration-300 ease-in-out">
                    {{ Session::get('user_type') === 'admin' ? 'ADMIN' : 'STAFF' }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <!-- Close button for mobile -->
            <button id="sidebar-close-btn" class="flex-shrink-0 p-2 rounded-md lg:hidden text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <!-- Collapse button for desktop -->
            <button id="sidebar-collapse-btn" class="flex-shrink-0 p-2 -mr-2 rounded-md hidden lg:flex text-secondary-text hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200">
                <svg id="collapse-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <svg id="expand-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar overscroll-contain">
        <div class="px-4 mb-2">
            <h3 class="text-xs font-semibold text-secondary-text uppercase tracking-wider sidebar-text">Main</h3>
        </div>
        
        <!-- Dashboard - accessible to all authenticated users -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('dashboard') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('dashboard') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>

        @php
            $userType = Session::get('user_type');
            $userRoles = $userType === 'admin' 
                ? Session::get('admin_roles', [])
                : Session::get('staff_roles', []);
            $userRolesLower = array_map('strtolower', $userRoles);
            $isAdmin = $userType === 'admin';
            $isStaff = $userType === 'staff';
        @endphp

        <!-- MAIN SECTIONS - Available to Admin and Staff -->
        @if($isAdmin || $isStaff)
           

            <!-- Calendar -->
            <a href="{{ route('calendar.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('calendar.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('calendar.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="sidebar-text">Calendar</span>
            </a>

            <!-- Bookings -->
            <a href="{{ route('bookings.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('bookings.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('bookings.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="sidebar-text">Bookings</span>
            </a>

            
           

            <div class="px-4 mt-6 mb-2">
                <h3 class="text-xs font-semibold text-secondary-text uppercase tracking-wider sidebar-text py-3">Studio</h3>
            </div>

            <!-- Studio Management -->
            <a href="{{ route('studio.index') }}" class="flex items-center px-4 py-3 rounded-r-lg text-secondary-text hover:bg-gray-50 hover:text-primary-text transition-colors duration-200 relative group border-l-4 {{ request()->routeIs('studio.*') ? 'border-accent text-primary-text bg-gray-50' : 'border-transparent' }} min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 text-secondary-text group-hover:text-primary-text {{ request()->routeIs('studio.*') ? 'text-primary-text' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="sidebar-text">Studio Management</span>
            </a>



            <!-- Packages -->
            <a href="{{ route('packages.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('packages.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('packages.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="sidebar-text">Packages</span>
            </a>
        @endif

        <!-- ADMIN-ONLY SECTIONS -->
        

        <!-- FINANCE SECTION - Available to Admin -->
        @if($isAdmin)
        <div class="px-4 mt-6 mb-2">
            <h3 class="text-xs font-semibold text-secondary-text uppercase tracking-wider sidebar-text py-3">Business</h3>
        </div>

        <a href="{{ route('finance.dashboard') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('finance.dashboard') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('finance.dashboard') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="sidebar-text">Finance Dashboard</span>
        </a>

        <a href="{{ route('finance.payments.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('finance.payments.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('finance.payments.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="sidebar-text">Payments</span>
        </a>

        <a href="{{ route('finance.expenses.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('finance.expenses.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('finance.expenses.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span class="sidebar-text">Expenses</span>
        </a>

        <a href="{{ route('finance.reports') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('finance.reports') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('finance.reports') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <span class="sidebar-text">Financial Reports</span>
        </a>
 
         <!-- System Section - Admin only -->
        

            <!-- Settings -->
            <div class="px-4 mt-6 mb-2">
                <h3 class="text-xs font-semibold text-secondary-text uppercase tracking-wider sidebar-text py-3">System</h3>
            </div>
 <!-- Activity Logs -->
            <a href="{{ route('activity-logs.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('activity-logs.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('activity-logs.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="sidebar-text">Activity Logs</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 rounded-r-lg {{ Request::routeIs('settings.*') ? 'text-primary-text font-medium bg-gray-100 border-l-4 border-accent' : 'text-secondary-text hover:bg-gray-50 hover:text-primary-text border-l-4 border-transparent' }} transition-colors duration-200 relative group min-h-[48px] touch-manipulation">
                <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ Request::routeIs('settings.*') ? 'text-accent' : 'text-secondary-text group-hover:text-primary-text' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="sidebar-text">Settings</span>
            </a>
            
        @endif

    </nav>

    <div class="p-4 border-t border-border-color mt-auto flex-shrink-0">
        <div class="flex items-center sidebar-text min-h-[48px]">
            <div class="h-10 w-10 rounded-full bg-accent text-white flex items-center justify-center text-sm font-semibold flex-shrink-0">
                {{ strtoupper(substr(Session::get('admin_name', Session::get('staff_name', 'U')), 0, 1)) }}
            </div>
            <div class="ml-3 overflow-hidden">
                <p class="text-sm font-medium text-primary-text truncate">
                    {{ Session::get('admin_name', Session::get('staff_name', 'User')) }}
                </p>
                <p class="text-xs text-secondary-text truncate">
                    {{ Session::get('user_type') === 'admin' ? 'Administrator' : 'Staff Member' }}
                </p>
            </div>
        </div>
    </div>
</aside>