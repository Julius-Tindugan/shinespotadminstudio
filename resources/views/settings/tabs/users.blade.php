<!-- User Management Tab Content -->
<div class="space-y-6">
    <!-- Add Staff Button -->
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-primary-text">Staff Management</h3>
        <button type="button" 
                class="btn btn-primary" 
                onclick="openCreateStaffModal()">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Staff Member
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input type="text" 
                   id="user-search" 
                   placeholder="Search staff by name or email..." 
                   class="w-full px-4 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
        </div>
        <div class="flex gap-2">
            <select id="user-status-filter" class="px-4 py-2 border border-border-color rounded-lg focus:ring-2 focus:ring-accent focus:border-accent">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="locked">Locked</option>
            </select>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-border-color">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Staff Member</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Roles</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-secondary-text uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody id="users-table-body" class="bg-white divide-y divide-border-color">
                @foreach($users['staff'] as $user)
                <tr class="user-row" 
                    data-user-status="{{ $user['is_active'] ? 'active' : ($user['is_locked'] ? 'locked' : 'inactive') }}"
                    data-search-text="{{ strtolower($user['full_name'] . ' ' . $user['email'] . ' ' . ($user['username'] ?? '')) }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <div class="h-10 w-10 rounded-full bg-accent text-white flex items-center justify-center">
                                    {{ strtoupper(substr($user['full_name'], 0, 2)) }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-primary-text">{{ $user['full_name'] }}</div>
                                <div class="text-sm text-secondary-text">{{ '@' . ($user['username'] ?? 'N/A') }}</div>
                                <div class="text-xs text-secondary-text">{{ $user['email'] }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-text">
                        {{ $user['phone'] ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user['is_locked'])
                            <span class="status-badge status-locked">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                                </svg>
                                Locked
                            </span>
                        @elseif($user['is_active'])
                            <span class="status-badge status-active">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Active
                            </span>
                        @else
                            <span class="status-badge status-inactive">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap">
                            @foreach($user['roles'] as $role)
                                <span class="role-badge">{{ $role }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary-text">
                        @if($user['last_login'])
                            {{ \Carbon\Carbon::parse($user['last_login'])->diffForHumans() }}
                        @else
                            Never
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    class="text-accent hover:text-accent-hover" 
                                    onclick="editStaff({{ $user['id'] }})"
                                    title="Edit Staff">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            
                            @if($user['is_locked'])
                                <button type="button" 
                                        class="text-green-600 hover:text-green-700" 
                                        onclick="unlockStaff({{ $user['id'] }})"
                                        title="Unlock Account">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            @else
                                <button type="button" 
                                        class="text-yellow-600 hover:text-yellow-700" 
                                        onclick="toggleStaffStatus({{ $user['id'] }})"
                                        title="{{ $user['is_active'] ? 'Deactivate' : 'Activate' }} Staff">
                                    @if($user['is_active'])
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </button>
                            @endif
                            
                            <button type="button" 
                                    class="text-blue-600 hover:text-blue-700" 
                                    onclick="showStaffSecurityLogs({{ $user['id'] }})"
                                    title="Security Logs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            
                            <button type="button" 
                                    class="text-red-600 hover:text-red-700" 
                                    onclick="resetStaffPassword({{ $user['id'] }})"
                                    title="Reset Password">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 14H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Empty State -->
    <div id="no-users-found" class="hidden text-center py-8">
        <svg class="mx-auto h-12 w-12 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-primary-text">No staff members found</h3>
        <p class="mt-1 text-sm text-secondary-text">Try adjusting your search or filter criteria, or add a new staff member.</p>
    </div>
</div>