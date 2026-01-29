/**
 * Dashboard Activity Logs
 * Handles activity logs auto-refresh and UI updates
 */

// Activity Logs Auto-Refresh Functionality
function refreshActivityLogs() {
    const container = document.getElementById('activity-logs-container');
    const lastUpdatedEl = document.getElementById('activity-last-updated');
    
    if (!container) return;

    // Add loading state
    container.style.opacity = '0.6';
    
    fetch('/activity-logs/recent?limit=10')
        .then(response => response.json())
        .then(data => {
            if (data && data.length > 0) {
                updateActivityLogsUI(data);
                if (lastUpdatedEl) {
                    lastUpdatedEl.textContent = 'Updated just now';
                }
            }
            container.style.opacity = '1';
        })
        .catch(error => {
            console.error('Error refreshing activity logs:', error);
            container.style.opacity = '1';
        });
}

function updateActivityLogsUI(activities) {
    const container = document.getElementById('activity-logs-container');
    if (!container) return;

    const actionColors = {
        'created': 'bg-green-100 text-green-600',
        'updated': 'bg-blue-100 text-blue-600',
        'deleted': 'bg-red-100 text-red-600',
        'login': 'bg-purple-100 text-purple-600',
        'logout': 'bg-gray-100 text-gray-600',
        'viewed': 'bg-yellow-100 text-yellow-600',
        'failed_login': 'bg-orange-100 text-orange-600'
    };

    const actionIcons = {
        'created': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>',
        'updated': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>',
        'deleted': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>',
        'login': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>',
        'default': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
    };

    let html = '';
    activities.forEach(activity => {
        const colorClass = actionColors[activity.action] || 'bg-gray-100 text-gray-600';
        const iconPath = actionIcons[activity.action] || actionIcons['default'];
        const modelInfo = activity.model_type ? 
            `<p class="text-xs text-gray-500 mt-1">
                <span class="font-semibold">Model:</span> ${activity.model_type}
                ${activity.model_id ? '#' + activity.model_id : ''}
            </p>` : '';

        html += `
            <div class="flex items-start p-3 rounded-lg hover:bg-gray-50 transition-colors duration-150 border border-gray-100">
                <div class="flex-shrink-0 mr-3">
                    <div class="p-2 rounded-full ${colorClass}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${iconPath}
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                <span class="font-semibold">${activity.user_name || 'System'}</span>
                                <span class="text-gray-600"> ${activity.action}</span>
                            </p>
                            <p class="text-xs text-gray-600 mt-0.5">${activity.description || 'No description'}</p>
                            ${modelInfo}
                        </div>
                        <div class="flex-shrink-0 ml-2">
                            <span class="text-xs text-gray-500">${activity.formatted_time || ''}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Initialize activity logs auto-refresh
function initActivityLogsModule(isAdmin) {
    if (!isAdmin) return;

    // Initial load is already done by server-side rendering
    
    // Set up auto-refresh
    setInterval(() => {
        if (document.visibilityState === 'visible') {
            refreshActivityLogs();
        }
    }, 30000); // 30 seconds

    // Refresh when page becomes visible
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshActivityLogs();
        }
    });
}
