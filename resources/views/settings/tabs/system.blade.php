<!-- System Settings Tab Content -->
<div class="space-y-6">
    <form id="system-settings-form" class="space-y-6">
        @csrf
        
        <!-- General Settings -->
        <div class="bg-white rounded-lg border border-border-color p-6 mb-4">
            <h3 class="text-lg font-medium text-primary-text mb-4">General Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="system_name" class="block text-sm font-medium text-primary-text mb-2">
                        System Name
                    </label>
                    <input type="text" 
                           id="system_name" 
                           name="settings[system_name]" 
                           value="{{ \App\Models\SystemSetting::getValue('system_name', 'Shine Spot Studio Admin') }}"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                </div>
                
                <div>
                    <label for="timezone" class="block text-sm font-medium text-primary-text mb-2">
                        Timezone
                    </label>
                    <select id="timezone" 
                            name="settings[timezone]" 
                            class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                        <option value="Asia/Manila" selected>Asia/Manila (UTC+8)</option>
                        <option value="UTC">UTC (UTC+0)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Booking Settings -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Booking Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="booking_advance_days" class="block text-sm font-medium text-primary-text mb-2">
                        Advance Booking Days
                    </label>
                    <input type="number" 
                           id="booking_advance_days" 
                           name="settings[booking_advance_days]" 
                           value="{{ \App\Models\SystemSetting::getValue('booking_advance_days', 30) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">How many days in advance bookings can be made</p>
                </div>
                
                <div>
                    <label for="cancellation_hours" class="block text-sm font-medium text-primary-text mb-2">
                        Cancellation Window (Hours)
                    </label>
                    <input type="number" 
                           id="cancellation_hours" 
                           name="settings[cancellation_hours]" 
                           value="{{ \App\Models\SystemSetting::getValue('cancellation_hours', 24) }}"
                           min="1"
                           class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                    <p class="text-xs text-secondary-text mt-1">Minimum hours before booking can be cancelled</p>
                </div>
            </div>
        </div>
        
        <!-- Notification Settings -->
        <div class="bg-white rounded-lg border border-border-color p-6">
            <h3 class="text-lg font-medium text-primary-text mb-4">Notification Settings</h3>
            
            <div class="space-y-3">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[email_notifications]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('email_notifications', true) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Email Notifications</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[sms_notifications]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('sms_notifications', false) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">SMS Notifications</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="settings[booking_reminders]" 
                           value="1"
                           {{ \App\Models\SystemSetting::getValue('booking_reminders', true) ? 'checked' : '' }}
                           class="rounded border-border-color text-accent focus:ring-accent">
                    <span class="ml-2 text-sm text-primary-text">Automatic Booking Reminders</span>
                </label>
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="btn btn-primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Save Settings
            </button>
        </div>
    </form>
</div>