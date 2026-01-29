<!-- Payment Settings Tab Content -->
<div class="space-y-6">
    <div class="bg-white rounded-lg border border-border-color p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-medium text-primary-text">Payment Integration</h3>
                <p class="text-sm text-secondary-text mt-1">Configure Xendit payment integration for GCash transactions</p>
            </div>
            <div class="flex items-center">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           id="payment-integration-toggle" 
                           class="sr-only peer" 
                           {{ \App\Models\SystemSetting::getValue('payment_integration_enabled', false) ? 'checked' : '' }}
                           onchange="togglePaymentIntegration(this.checked)">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent"></div>
                </label>
                <span class="ml-3 text-sm font-medium text-primary-text" id="payment-status-text">
                    {{ \App\Models\SystemSetting::getValue('payment_integration_enabled', false) ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
        </div>

        <div id="payment-settings-form" class="{{ \App\Models\SystemSetting::getValue('payment_integration_enabled', false) ? '' : 'opacity-50 pointer-events-none' }}">
            <form id="payment-configuration-form" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="xendit_api_key" class="block text-sm font-medium text-primary-text mb-2">
                            Xendit Secret API Key
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="xendit_api_key" 
                                   name="xendit_api_key" 
                                   value="{{ \App\Models\SystemSetting::getValue('xendit_api_key', '') }}"
                                   class="w-full px-3 py-2.5 pr-12 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent bg-white"
                                   placeholder="xnd_development_... or xnd_production_..."
                                   required>
                            <button type="button" 
                                    onclick="toggleApiKeyVisibility()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 z-10 text-secondary-text hover:text-primary-text focus:outline-none"
                                    tabindex="-1">
                                <svg id="show-api-key-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="hide-api-key-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-xs text-blue-800 font-medium mb-1">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                How to find your Secret API Key:
                            </p>
                            <ol class="text-xs text-blue-700 ml-5 list-decimal space-y-1">
                                <li>Login to <a href="https://dashboard.xendit.co/" target="_blank" class="underline font-medium">Xendit Dashboard</a></li>
                                <li>Go to <strong>Settings → Developers → API Keys</strong></li>
                                <li>Copy the <strong>Secret Key</strong> (NOT the Public Key)</li>
                                <li>Use <strong>Test mode</strong> key for testing, <strong>Live mode</strong> for production</li>
                            </ol>
                        </div>
                    </div>

                    <div>
                        <label for="webhook_url" class="block text-sm font-medium text-primary-text mb-2">
                            Webhook URL
                        </label>
                        <input type="url" 
                               id="webhook_url" 
                               name="webhook_url" 
                               value="{{ url('/webhook/xendit') }}"
                               readonly
                               class="w-full px-3 py-2 border border-border-color rounded-lg bg-gray-50"
                               placeholder="Webhook URL">
                        <p class="text-xs text-secondary-text mt-1">Configure this URL in your Xendit dashboard</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="payment_methods" class="block text-sm font-medium text-primary-text mb-2">
                            Enabled Payment Methods
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="payment_methods[]" 
                                       value="gcash" 
                                       checked
                                       class="rounded border-border-color text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-primary-text">GCash</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="payment_methods[]" 
                                       value="grabpay"
                                       class="rounded border-border-color text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-primary-text">GrabPay</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="payment_methods[]" 
                                       value="paymaya"
                                       class="rounded border-border-color text-accent focus:ring-accent">
                                <span class="ml-2 text-sm text-primary-text">PayMaya</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="currency" class="block text-sm font-medium text-primary-text mb-2">
                            Currency
                        </label>
                        <select id="currency" 
                                name="currency" 
                                class="w-full px-3 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                            <option value="PHP" selected>PHP - Philippine Peso</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 border-t border-border-color">
                    <button type="submit" 
                            class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Payment Settings
                    </button>
                    <button type="button" 
                            class="btn btn-secondary ml-3"
                            onclick="testPaymentConnection()">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Test Connection
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Payment Method Configuration -->
    <div class="bg-white rounded-lg border border-border-color p-6">
        <h3 class="text-lg font-medium text-primary-text mb-4">Payment Method Settings</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- GCash Settings -->
            <div class="border border-border-color rounded-lg p-4" id="gcash-method-card">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">G</span>
                        </div>
                        <span class="ml-2 font-medium text-primary-text">GCash</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               id="gcash-method-toggle"
                               class="sr-only peer" 
                               {{ \App\Models\SystemSetting::getValue('payment_integration_enabled', false) ? 'checked' : '' }}
                               disabled>
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-accent"></div>
                    </label>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Min Amount:</span>
                        <span class="text-primary-text">₱1.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Max Amount:</span>
                        <span class="text-primary-text">₱50,000.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-secondary-text">Processing Fee:</span>
                        <span class="text-primary-text">2.9% + ₱15</span>
                    </div>
                </div>
            </div>

            <!-- GrabPay Settings -->
            <div class="border border-border-color rounded-lg p-4 opacity-50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">GP</span>
                        </div>
                        <span class="ml-2 font-medium text-primary-text">GrabPay</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-accent"></div>
                    </label>
                </div>
                <div class="text-xs text-secondary-text">
                    Coming Soon
                </div>
            </div>

            <!-- PayMaya Settings -->
            <div class="border border-border-color rounded-lg p-4 opacity-50">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xs font-bold">PM</span>
                        </div>
                        <span class="ml-2 font-medium text-primary-text">PayMaya</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-accent/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-accent"></div>
                    </label>
                </div>
                <div class="text-xs text-secondary-text">
                    Coming Soon
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Set initial state for GCash method card based on payment integration status
document.addEventListener('DOMContentLoaded', function() {
    const paymentIntegrationEnabled = {{ \App\Models\SystemSetting::getValue('payment_integration_enabled', false) ? 'true' : 'false' }};
    const gcashCard = document.getElementById('gcash-method-card');
    
    if (gcashCard) {
        gcashCard.style.opacity = paymentIntegrationEnabled ? '1' : '0.5';
    }
});

// Toggle API key visibility function (in case settings.js hasn't loaded it)
if (typeof toggleApiKeyVisibility === 'undefined') {
    function toggleApiKeyVisibility() {
        const apiKeyInput = document.getElementById('xendit_api_key');
        const showIcon = document.getElementById('show-api-key-icon');
        const hideIcon = document.getElementById('hide-api-key-icon');
        
        if (apiKeyInput && showIcon && hideIcon) {
            if (apiKeyInput.type === 'password') {
                apiKeyInput.type = 'text';
                showIcon.classList.add('hidden');
                hideIcon.classList.remove('hidden');
            } else {
                apiKeyInput.type = 'password';
                showIcon.classList.remove('hidden');
                hideIcon.classList.add('hidden');
            }
        }
    }
}
</script>
