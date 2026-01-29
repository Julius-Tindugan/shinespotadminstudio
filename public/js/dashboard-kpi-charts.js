/**
 * Dashboard KPI Charts
 * Handles initialization and rendering of pie/doughnut charts for KPIs
 */

// KPI Pie Charts Initialization
function initializeKpiCharts() {
    // Revenue Breakdown Pie Chart
    const revenueBreakdownCtx = document.getElementById('revenueBreakdownChart');
    if (revenueBreakdownCtx) {
        const paidAmount = parseFloat(revenueBreakdownCtx.dataset.paidAmount || 0);
        const refundedAmount = parseFloat(revenueBreakdownCtx.dataset.refundedAmount || 0);
        
        // Check if there's any revenue data to display
        if (paidAmount === 0 && refundedAmount === 0) {
            // Display fallback message when no revenue data
            const canvas = revenueBreakdownCtx;
            const container = canvas.parentElement;
            
            // Hide the canvas
            canvas.style.display = 'none';
            
            // Create fallback message element
            const fallbackDiv = document.createElement('div');
            fallbackDiv.className = 'revenue-chart-fallback';
            fallbackDiv.style.cssText = 'display: flex; align-items: center; justify-content: center; height: 120px; text-align: center; color: #9ca3af; font-size: 0.875rem;';
            fallbackDiv.innerHTML = `
                <div>
                    <svg class="mx-auto mb-2" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <p>No revenue data yet</p>
                </div>
            `;
            
            // Insert fallback message after canvas
            container.insertBefore(fallbackDiv, canvas.nextSibling);
        } else {
            // Display chart as normal
            new Chart(revenueBreakdownCtx, {
                type: 'doughnut',
                data: {
                    labels: refundedAmount > 0 ? ['Paid Bookings', 'Refunded'] : ['Paid Bookings'],
                    datasets: [{
                        data: refundedAmount > 0 ? [paidAmount, refundedAmount] : [paidAmount],
                        backgroundColor: refundedAmount > 0 ? 
                            ['rgba(34, 197, 94, 0.8)', 'rgba(251, 146, 60, 0.8)'] :
                            ['rgba(34, 197, 94, 0.8)'],
                        borderColor: refundedAmount > 0 ?
                            ['rgba(34, 197, 94, 1)', 'rgba(251, 146, 60, 1)'] :
                            ['rgba(34, 197, 94, 1)'],
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ₱${value.toLocaleString('en-US', {minimumFractionDigits: 2})} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    }

    // Booking Status Pie Chart
    const bookingStatusCtx = document.getElementById('bookingStatusChart');
    if (bookingStatusCtx) {
        const statusData = [];
        const statusLabels = [];
        const statusColors = [];
        const statusBorderColors = [];
        
        const pending = parseInt(bookingStatusCtx.dataset.pending || 0);
        const confirmed = parseInt(bookingStatusCtx.dataset.confirmed || 0);
        const completed = parseInt(bookingStatusCtx.dataset.completed || 0);
        const cancelled = parseInt(bookingStatusCtx.dataset.cancelled || 0);
        
        if (pending > 0) {
            statusData.push(pending);
            statusLabels.push('Pending');
            statusColors.push('rgba(234, 179, 8, 0.8)');
            statusBorderColors.push('rgba(234, 179, 8, 1)');
        }
        if (confirmed > 0) {
            statusData.push(confirmed);
            statusLabels.push('Confirmed');
            statusColors.push('rgba(59, 130, 246, 0.8)');
            statusBorderColors.push('rgba(59, 130, 246, 1)');
        }
        if (completed > 0) {
            statusData.push(completed);
            statusLabels.push('Completed');
            statusColors.push('rgba(34, 197, 94, 0.8)');
            statusBorderColors.push('rgba(34, 197, 94, 1)');
        }
        if (cancelled > 0) {
            statusData.push(cancelled);
            statusLabels.push('Cancelled');
            statusColors.push('rgba(239, 68, 68, 0.8)');
            statusBorderColors.push('rgba(239, 68, 68, 1)');
        }
        
        if (statusData.length > 0) {
            new Chart(bookingStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusColors,
                        borderColor: statusBorderColors,
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    }

    // Booking Performance Pie Chart
    const bookingPerformanceCtx = document.getElementById('bookingPerformanceChart');
    if (bookingPerformanceCtx) {
        const performanceData = [];
        const performanceLabels = [];
        const performanceColors = [];
        const performanceBorderColors = [];
        
        const completedCount = parseInt(bookingPerformanceCtx.dataset.completed || 0);
        const activeCount = parseInt(bookingPerformanceCtx.dataset.active || 0);
        const cancelledCount = parseInt(bookingPerformanceCtx.dataset.cancelled || 0);
        
        if (completedCount > 0) {
            performanceData.push(completedCount);
            performanceLabels.push('Completed');
            performanceColors.push('rgba(34, 197, 94, 0.8)');
            performanceBorderColors.push('rgba(34, 197, 94, 1)');
        }
        if (activeCount > 0) {
            performanceData.push(activeCount);
            performanceLabels.push('Active');
            performanceColors.push('rgba(59, 130, 246, 0.8)');
            performanceBorderColors.push('rgba(59, 130, 246, 1)');
        }
        if (cancelledCount > 0) {
            performanceData.push(cancelledCount);
            performanceLabels.push('Cancelled');
            performanceColors.push('rgba(239, 68, 68, 0.8)');
            performanceBorderColors.push('rgba(239, 68, 68, 1)');
        }
        
        if (performanceData.length > 0) {
            new Chart(bookingPerformanceCtx, {
                type: 'doughnut',
                data: {
                    labels: performanceLabels,
                    datasets: [{
                        data: performanceData,
                        backgroundColor: performanceColors,
                        borderColor: performanceBorderColors,
                        borderWidth: 2,
                        hoverOffset: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        },
                        datalabels: {
                            display: false
                        }
                    },
                    cutout: '65%',
                    animation: {
                        animateRotate: true,
                        animateScale: true,
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        }
    }
}
