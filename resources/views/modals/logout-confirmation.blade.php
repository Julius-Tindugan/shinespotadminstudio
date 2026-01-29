<!-- Logout Confirmation Modal -->
<div id="logout-confirmation-modal" class="fixed inset-0 flex items-center justify-center hidden z-50">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="modal-content bg-card-bg rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all opacity-0 scale-95 relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-border-color">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-primary-text">Confirm Logout</h3>
            </div>
            <button class="modal-close p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 text-secondary-text hover:text-primary-text">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <p class="text-secondary-text mb-2">
                Are you sure you want to log out of your account?
            </p>
            <p class="text-sm text-secondary-text">
                You'll need to sign in again to access the admin dashboard.
            </p>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-border-color ">
            <button type="button" class="modal-close px-4 py-2 text-sm font-medium text-secondary-text hover:text-primary-text bg-white light:bg-gray-100 border border-border-color rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                Cancel
            </button>
            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Yes, Logout
                    </span>
                </button>
            </form>
        </div>
    </div>
</div>
