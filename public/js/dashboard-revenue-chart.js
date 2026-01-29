/**
 * Dashboard Revenue Analytics Chart
 * Handles revenue chart initialization, updates, and real-time data fetching
 */

let revenueChart = null;
let currentPeriod = 'daily';
let realtimeUpdateEnabled = true;

const periodTitles = {
    'daily': 'Last 30 Days',
    'weekly': 'Last 12 Weeks',
    'monthly': 'Last 12 Months',
    'yearly': 'Last 5 Years'
};

// Format currency function
function formatCurrency(value) {
    return '₱' + parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Check if data is empty (all zeros)
function isDataEmpty(data) {
    return Array.isArray(data) && data.every(item => item === 0);
}

// Get gradient for chart background
function createGradient(ctx, startColor, endColor) {
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, startColor);
    gradient.addColorStop(1, endColor);
    return gradient;
}

// Initialize the revenue chart
function initializeRevenueChart(initialData, initialLabels, initialStats) {
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        console.log('Initializing chart with data:', initialData);
        console.log('Initializing chart with labels:', initialLabels);

        // Check if we have data
        if (isDataEmpty(initialData)) {
            document.getElementById('no-data-message').classList.remove('hidden');
        }

        const startColor = 'rgba(212, 175, 55, 0.3)';
        const endColor = 'rgba(212, 175, 55, 0.05)';
        const gradient = createGradient(ctx.getContext('2d'), startColor, endColor);

        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: initialLabels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: initialData,
                    backgroundColor: gradient,
                    borderColor: '#D4AF37',
                    borderWidth: 2,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    hoverBackgroundColor: '#C09A2E'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return formatCurrency(value);
                            },
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                family: "'Inter', sans-serif"
                            },
                            color: '#6B7280',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
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
                                return 'Revenue: ' + formatCurrency(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });

        // Update stats with initial data if available
        if (initialStats) {
            updateRevenueStats(initialStats);
        }

        updateLastUpdatedTime();
    }
}

// Update revenue chart and stats with real-time data
function updateRevenueChart(period) {
    const loadingEl = document.getElementById('revenue-loading');
    loadingEl.classList.remove('hidden');
    document.getElementById('no-data-message').classList.add('hidden');

    // Update period buttons
    document.querySelectorAll('.period-selector').forEach(btn => {
        if (btn.dataset.period === period) {
            btn.className = 'period-selector px-4 py-2 text-xs font-semibold rounded-md transition-all duration-200 bg-accent text-white shadow-sm';
        } else {
            btn.className = 'period-selector px-4 py-2 text-xs font-semibold rounded-md hover:bg-gray-200 transition-all duration-200 text-secondary-text';
        }
    });

    document.getElementById('period-title').textContent = periodTitles[period] ? `- ${periodTitles[period]}` : '';

    const timestamp = new Date().getTime();

    fetch(`/api/revenue?period=${period}&_=${timestamp}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Server responded with status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Revenue data received:', data);

            if (isDataEmpty(data.data)) {
                document.getElementById('no-data-message').classList.remove('hidden');
            } else {
                document.getElementById('no-data-message').classList.add('hidden');
            }

            revenueChart.data.labels = data.labels;
            revenueChart.data.datasets[0].data = data.data;
            revenueChart.data.datasets[0].label = `${period.charAt(0).toUpperCase() + period.slice(1)} Revenue`;

            // Apply different bar styles based on period
            if (period === 'yearly') {
                revenueChart.data.datasets[0].barPercentage = 0.5;
                revenueChart.data.datasets[0].borderRadius = 6;
            } else if (period === 'monthly') {
                revenueChart.data.datasets[0].barPercentage = 0.6;
                revenueChart.data.datasets[0].borderRadius = 5;
            } else {
                revenueChart.data.datasets[0].barPercentage = 0.8;
                revenueChart.data.datasets[0].borderRadius = 4;
            }

            revenueChart.update('active');

            if (data.stats) {
                updateRevenueStats(data.stats);
            }

            updateLastUpdatedTime();

            setTimeout(() => {
                loadingEl.classList.add('hidden');
            }, 300);
        })
        .catch(error => {
            console.error('Error fetching revenue data:', error);
            loadingEl.classList.add('hidden');
            showErrorNotification('Failed to load revenue data. Please try again.');
        });
}

// Show error notification
function showErrorNotification(message) {
    const notificationEl = document.createElement('div');
    notificationEl.className = 'fixed bottom-6 right-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-5 py-4 rounded-lg shadow-xl z-50 max-w-md animate-fade-in';
    notificationEl.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 flex-shrink-0 text-red-400 hover:text-red-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    document.body.appendChild(notificationEl);

    setTimeout(() => {
        notificationEl.style.opacity = '0';
        notificationEl.style.transition = 'opacity 0.5s ease-out';
        setTimeout(() => {
            if (notificationEl.parentNode) {
                document.body.removeChild(notificationEl);
            }
        }, 500);
    }, 5000);
}

// Update revenue statistics with animation
function updateRevenueStats(stats) {
    animateValue('avg-revenue', stats.average);
    animateValue('max-revenue', stats.max);
    animateValue('min-revenue', stats.min);
}

// Animate value changes
function animateValue(elementId, newValue) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const oldValue = parseFloat(element.textContent.replace(/[₱,]/g, '')) || 0;
    const duration = 1000;
    const frameDuration = 1000 / 60;
    const totalFrames = Math.round(duration / frameDuration);
    let frame = 0;

    const animate = () => {
        frame++;
        const progress = frame / totalFrames;
        const currentValue = oldValue + (newValue - oldValue) * progress;
        element.textContent = formatCurrency(currentValue);

        if (frame < totalFrames) {
            requestAnimationFrame(animate);
        }
    };

    requestAnimationFrame(animate);
}

// Update last updated time
function updateLastUpdatedTime() {
    const element = document.getElementById('last-updated');
    if (element) {
        element.textContent = 'Last updated: ' + new Date().toLocaleTimeString();
    }
}

// Initialize revenue chart on page load
function initRevenueChartModule(initialData, initialLabels, initialStats) {
    // Set up real-time indicator in the UI
    const lastUpdatedEl = document.getElementById('last-updated');
    if (lastUpdatedEl) {
        lastUpdatedEl.innerHTML = '<span class="inline-flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-1"></span> Real-time updates enabled</span>';
    }

    // Initialize chart
    initializeRevenueChart(initialData, initialLabels, initialStats);

    // Add event listeners for period buttons
    document.querySelectorAll('.period-selector').forEach(btn => {
        btn.addEventListener('click', () => {
            const period = btn.dataset.period;
            if (period && period !== currentPeriod) {
                currentPeriod = period;
                updateRevenueChart(period);
            }
        });
    });

    // Set up periodic refresh (every 60 seconds)
    setInterval(() => {
        if (revenueChart && document.visibilityState === 'visible') {
            updateRevenueChart(currentPeriod);
        }
    }, 60000);

    // Add visibility change listener
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && revenueChart) {
            updateRevenueChart(currentPeriod);
        }
    });

    // Add window resize handler
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            if (revenueChart) {
                revenueChart.resize();
            }
        }, 250);
    });
}
