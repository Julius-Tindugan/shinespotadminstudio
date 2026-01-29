<!-- Security Settings Tab Content -->
<div class="space-y-6">
    <form id="security-settings-form" class="space-y-6">
        @csrf
        
        <!-- Login Security -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Login Security</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="max_login_attempts" class="block text-sm font-medium text-primary-text mb-2">
                        Max Login Attempts
                    </label>
                    <input type="number" 
                           id="max_login_attempts" 
                           name="settings[max_login_attempts]" 
                           value="{{ \App\Models\SystemSetting::getValue('max_login_attempts', 5) }}"
                           min="3"
                           max="10"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">Number of failed attempts before account lockout</p>
                </div>
                
                <div>
                    <label for="account_lockout_duration" class="block text-sm font-medium text-primary-text mb-2">
                        Lockout Duration (Minutes)
                    </label>
                    <input type="number" 
                           id="account_lockout_duration" 
                           name="settings[account_lockout_duration]" 
                           value="{{ \App\Models\SystemSetting::getValue('account_lockout_duration', 30) }}"
                           min="5"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">How long accounts remain locked after max attempts</p>
                </div>
            </div>
        </div>
        
        <!-- Password Policy -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Password Policy</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password_min_length" class="block text-sm font-medium text-primary-text mb-2">
                        Minimum Password Length
                    </label>
                    <input type="number" 
                           id="password_min_length" 
                           name="settings[password_min_length]" 
                           value="{{ \App\Models\SystemSetting::getValue('password_min_length', 8) }}"
                           min="6"
                           max="20"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                </div>
                
                <div>
                    <label for="password_expiry_days" class="block text-sm font-medium text-primary-text mb-2">
                        Password Expiry (Days)
                    </label>
                    <input type="number" 
                           id="password_expiry_days" 
                           name="settings[password_expiry_days]" 
                           value="{{ \App\Models\SystemSetting::getValue('password_expiry_days', 90) }}"
                           min="30"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                   
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[require_uppercase]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('require_uppercase', true) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Require uppercase letters</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[require_lowercase]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('require_lowercase', true) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Require lowercase letters</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[require_numbers]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('require_numbers', true) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Require numbers</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[require_special_chars]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('require_special_chars', false) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Require special characters</span>
                </label>
            </div>
        </div>
        
        <!-- Session Security -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Session Security</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="session_timeout" class="block text-sm font-medium text-primary-text mb-2">
                        Session Timeout (Minutes)
                    </label>
                    <input type="number" 
                           id="session_timeout" 
                           name="settings[session_timeout]" 
                           value="{{ \App\Models\SystemSetting::getValue('session_timeout', 120) }}"
                           min="15"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">Automatic logout after inactivity</p>
                </div>
                
                <div>
                    <label for="concurrent_sessions" class="block text-sm font-medium text-primary-text mb-2">
                        Max Concurrent Sessions
                    </label>
                    <input type="number" 
                           id="concurrent_sessions" 
                           name="settings[concurrent_sessions]" 
                           value="{{ \App\Models\SystemSetting::getValue('concurrent_sessions', 3) }}"
                           min="1"
                           max="10"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">Maximum simultaneous login sessions per user</p>
                </div>
            </div>
        </div>
        
        <!-- Access Control -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Access Control</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="settings[enforce_finance_restriction]" 
                               value="1"
                               {{ \App\Models\SystemSetting::getValue('enforce_finance_restriction', true) ? 'checked' : '' }}
                               class="rounded border-border-color text-accent focus:ring-accent">
                        <span class="ml-2 text-sm text-primary-text">Enforce Finance Access Restriction for Staff</span>
                    </label>
                    <p class="text-xs text-secondary-text mt-1 ml-6">When enabled, staff members cannot access any financial data</p>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="settings[log_all_actions]" 
                               value="1"
                               {{ \App\Models\SystemSetting::getValue('log_all_actions', true) ? 'checked' : '' }}
                               class="rounded border-border-color text-accent focus:ring-accent">
                        <span class="ml-2 text-sm text-primary-text">Log All User Actions</span>
                    </label>
                    <p class="text-xs text-secondary-text mt-1 ml-6">Record all user activities for security auditing</p>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="settings[require_password_change]" 
                               value="1"
                               {{ \App\Models\SystemSetting::getValue('require_password_change', false) ? 'checked' : '' }}
                               class="rounded border-border-color text-accent focus:ring-accent">
                        <span class="ml-2 text-sm text-primary-text">Force Password Change on First Login</span>
                    </label>
                    <p class="text-xs text-secondary-text mt-1 ml-6">New users must change their password on first login</p>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Security Settings
            </button>
        </div>
    </form>
</div>
