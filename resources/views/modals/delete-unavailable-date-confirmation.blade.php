<!-- Delete Unavailable Date Confirmation Modal -->
<div id="delete-unavailable-date-modal" class="fixed inset-0 flex items-center justify-center hidden z-50">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="modal-content bg-card-bg rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all opacity-0 scale-95 relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-border-color">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-primary-text">Remove Unavailable Date</h3>
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
                Are you sure you want to remove this unavailable date?
            </p>
            <div id="delete-date-info" class="mt-4 p-4 bg-background rounded-lg border border-border-color">
                <div class="text-sm text-secondary-text mb-1">Date Range:</div>
                <div id="delete-date-range" class="font-medium text-primary-text mb-2"></div>
                <div id="delete-date-reason-container" class="hidden">
                    <div class="text-sm text-secondary-text mb-1">Reason:</div>
                    <div id="delete-date-reason" class="font-medium text-primary-text"></div>
                </div>
            </div>
            <p class="text-sm text-secondary-text mt-4">
                This will make the date(s) available for booking again.
            </p>
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-border-color">
            <button type="button" class="modal-close px-4 py-2 text-sm font-medium text-secondary-text hover:text-primary-text bg-white light:bg-gray-100 border border-border-color rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300">
                Cancel
            </button>
            <button type="button" id="confirm-delete-unavailable-date" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 shadow-sm">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Yes, Remove Date
                </span>
            </button>
        </div>
    </div>
</div>
