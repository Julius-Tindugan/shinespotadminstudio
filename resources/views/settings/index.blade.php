@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-primary-text">System Settings</h1>
                <p class="text-secondary-text mt-2">Manage system configuration, user accounts, and security settings</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button type="button" 
                        id="open-account-settings-btn"
                        onclick="if(typeof openAdminAccountModal === 'function') { openAdminAccountModal(); } else { console.error('Function not loaded'); alert('Please wait for page to fully load and try again.'); }" 
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-accent to-accent-hover text-white font-semibold rounded-lg hover:shadow-lg hover:scale-105 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    My Account Settings
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-card-bg rounded-lg p-6 shadow-subtle">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Total Users</p>
                        <p class="text-2xl font-bold text-primary-text">{{ $userStats['total_users'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg rounded-lg p-6 shadow-subtle">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Active Admins</p>
                        <p class="text-2xl font-bold text-primary-text">{{ $userStats['active_admins'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg rounded-lg p-6 shadow-subtle">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Active Staff</p>
                        <p class="text-2xl font-bold text-primary-text">{{ $userStats['active_staff'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-card-bg rounded-lg p-6 shadow-subtle">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-secondary-text">Locked Accounts</p>
                        <p class="text-2xl font-bold text-primary-text">{{ $userStats['locked_admins'] + $userStats['locked_staff'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <div class="bg-card-bg rounded-lg shadow-subtle">
            <div class="border-b border-border-color">
                <nav class="-mb-px flex">
                    <button type="button" 
                            class="tab-button active border-b-2 border-accent text-accent py-4 px-6 font-medium text-sm" 
                            data-tab="users">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        User Management
                    </button>
                    <button type="button" 
                            class="tab-button border-b-2 border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 py-4 px-6 font-medium text-sm" 
                            data-tab="system">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        System Settings
                    </button>
                    <button type="button" 
                            class="tab-button border-b-2 border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 py-4 px-6 font-medium text-sm" 
                            data-tab="payment">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Payment Settings
                    </button>
                    <button type="button" 
                            class="tab-button border-b-2 border-transparent text-secondary-text hover:text-primary-text hover:border-gray-300 py-4 px-6 font-medium text-sm" 
                            data-tab="security">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        Security Settings
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- User Management Tab -->
                <div id="users-tab" class="tab-content">
                    @include('settings.tabs.users')
                </div>

                <!-- System Settings Tab -->
                <div id="system-tab" class="tab-content hidden">
                    @include('settings.tabs.system')
                </div>

                <!-- Payment Settings Tab -->
                <div id="payment-tab" class="tab-content hidden">
                    @include('settings.tabs.payment')
                </div>

                <!-- Security Settings Tab -->
                <div id="security-tab" class="tab-content hidden">
                    @include('settings.tabs.security')
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals -->
@include('settings.modals.create-user')
@include('settings.modals.edit-user')
@include('settings.modals.security-logs')
@include('settings.modals.reset-password')
@include('settings.modals.confirmation')
@include('settings.modals.admin-account')

@endsection

@push('scripts')
<script src="{{ secure_asset('js/settings.js') }}"></script>
<!-- Inline fallback for admin account modal in case external script fails -->
<script>
// Ensure functions are globally available even if settings.js has issues
if (typeof openAdminAccountModal !== 'function') {
    window.openAdminAccountModal = async function() {
        console.log('Opening admin account modal...');
        const modal = document.getElementById('admin-account-modal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Load current admin account data
            try {
                const response = await fetch('/settings/account', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('current-admin-username').textContent = result.user.username;
                    document.getElementById('password-changed-at').textContent = result.user.password_changed_at;
                    document.getElementById('new_username').value = result.user.username;
                }
            } catch (error) {
                console.error('Error loading admin account data:', error);
            }
        } else {
            console.error('Admin account modal not found!');
            alert('Modal not found. Please refresh the page.');
        }
    };
}

if (typeof closeAdminAccountModal !== 'function') {
    window.closeAdminAccountModal = function() {
        const modal = document.getElementById('admin-account-modal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };
}

if (typeof switchAccountTab !== 'function') {
    window.switchAccountTab = function(tabName) {
        document.querySelectorAll('.account-tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-accent', 'text-accent');
            btn.classList.add('border-transparent', 'text-secondary-text');
        });
        
        const activeBtn = document.getElementById(tabName + '-tab-btn');
        if (activeBtn) {
            activeBtn.classList.add('active', 'border-accent', 'text-accent');
            activeBtn.classList.remove('border-transparent', 'text-secondary-text');
        }
        
        document.querySelectorAll('.account-tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        const activeTab = document.getElementById(tabName + '-tab');
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }
    };
}

if (typeof togglePasswordVisibility !== 'function') {
    window.togglePasswordVisibility = function(inputId, eyeIconId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    };
}

// Debug: Log when script loads
console.log('Settings page scripts loaded. openAdminAccountModal available:', typeof openAdminAccountModal === 'function');
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/export-modal.css') }}">
<style>
/* Tab button active state */
.tab-button.active {
    color: var(--accent-color, #c9a227);
    border-bottom-color: var(--accent-color, #c9a227);
}

/* Tab content hidden state */
.tab-content.hidden {
    display: none !important;
}

/* Status badges - using plain CSS instead of @apply */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.125rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    line-height: 1rem;
    font-weight: 500;
}

.status-active {
    background-color: #dcfce7;
    color: #166534;
}

.status-inactive {
    background-color: #fee2e2;
    color: #991b1b;
}

.status-locked {
    background-color: #fef3c7;
    color: #92400e;
}

/* Role badges - using plain CSS instead of @apply */
.role-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    line-height: 1rem;
    font-weight: 500;
    background-color: #dbeafe;
    color: #1e40af;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/export-report.js') }}"></script>
@endpush

<!-- Include Export Modal -->
@include('components.export-modal')
