/**
 * Dashboard Main Initialization
 * Coordinates all dashboard modules and handles initialization
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize KPI Pie Charts
    if (typeof initializeKpiCharts === 'function') {
        initializeKpiCharts();
    }
    
    // Initialize Revenue Chart Module
    if (typeof initRevenueChartModule === 'function') {
        // Get initial data from data attributes or window object
        const revenueChartCanvas = document.getElementById('revenueChart');
        if (revenueChartCanvas) {
            const initialData = window.dashboardData?.dailyRevenue?.data || [];
            const initialLabels = window.dashboardData?.dailyRevenue?.labels || [];
            const initialStats = window.dashboardData?.dailyRevenue?.stats || null;
            
            initRevenueChartModule(initialData, initialLabels, initialStats);
        }
    }
    
    // Initialize Activity Logs Module (Admin only)
    if (typeof initActivityLogsModule === 'function') {
        const isAdmin = window.dashboardData?.isAdmin || false;
        initActivityLogsModule(isAdmin);
    }
});
