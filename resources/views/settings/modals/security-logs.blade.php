<!-- Security Logs Modal -->
<div id="security-logs-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-primary-text">Security Logs</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeSecurityLogsModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="security-logs-content" class="space-y-4">
            <!-- User Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-medium text-primary-text mb-2">User Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-secondary-text">Name:</span>
                        <span class="text-primary-text font-medium" id="security-user-name">-</span>
                    </div>
                    <div>
                        <span class="text-secondary-text">Email:</span>
                        <span class="text-primary-text font-medium" id="security-user-email">-</span>
                    </div>
                    <div>
                        <span class="text-secondary-text">Type:</span>
                        <span class="text-primary-text font-medium" id="security-user-type">-</span>
                    </div>
                    <div>
                        <span class="text-secondary-text">Status:</span>
                        <span class="text-primary-text font-medium" id="security-user-status">-</span>
                    </div>
                </div>
            </div>

            <!-- Security Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white border border-border-color rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-secondary-text">Last Login</p>
                            <p class="text-lg font-semibold text-primary-text" id="security-last-login">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-border-color rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-secondary-text">Failed Attempts</p>
                            <p class="text-lg font-semibold text-primary-text" id="security-failed-attempts">-</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-border-color rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-secondary-text">Password Changed</p>
                            <p class="text-lg font-semibold text-primary-text" id="security-password-changed">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="bg-white border border-border-color rounded-lg p-4">
                <h4 class="font-medium text-primary-text mb-3">Account Status</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary-text">Account Locked:</span>
                        <span class="text-sm font-medium" id="security-is-locked">-</span>
                    </div>
                    <div class="flex items-center justify-between" id="locked-until-row" style="display: none;">
                        <span class="text-sm text-secondary-text">Locked Until:</span>
                        <span class="text-sm font-medium text-red-600" id="security-locked-until">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary-text">Force Password Change:</span>
                        <span class="text-sm font-medium" id="security-force-password-change">-</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-secondary-text">Last Login IP:</span>
                        <span class="text-sm font-medium text-primary-text" id="security-last-ip">-</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" 
                        class="btn btn-secondary" 
                        onclick="closeSecurityLogsModal()">
                    Close
                </button>
                <button type="button" 
                        class="btn btn-warning"
                        id="unlock-user-btn"
                        onclick="unlockUserFromModal()"
                        style="display: none;">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                    </svg>
                    Unlock Account
                </button>
            </div>
        </div>
    </div>
</div>