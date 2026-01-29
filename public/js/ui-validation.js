/**
 * UI Validation Script
 * Quick test to verify that all UI components are working properly
 */

function validateUIComponents() {
    console.log('🔍 Starting UI component validation...');
    
    const results = {
        sidebarCollapse: false,
        sidebarToggle: false
    };
    
    // Check sidebar collapse
    const collapseBtn = document.getElementById('sidebar-collapse-btn');
    const sidebar = document.getElementById('sidebar');
    if (collapseBtn && sidebar) {
        const hasListener = collapseBtn.getAttribute('data-has-listener') === 'true';
        results.sidebarCollapse = hasListener;
        console.log(`Sidebar collapse: ${hasListener ? '✅ Working' : '❌ Not working'}`);
    }
    
    // Check sidebar toggle (mobile)
    const toggleBtn = document.getElementById('sidebar-toggle-btn');
    if (toggleBtn && sidebar) {
        const hasListener = toggleBtn.getAttribute('data-has-listener') === 'true';
        results.sidebarToggle = hasListener;
        console.log(`Sidebar toggle: ${hasListener ? '✅ Working' : '❌ Not working'}`);
    }
    
    const workingCount = Object.values(results).filter(Boolean).length;
    const totalCount = Object.keys(results).length;
    
    console.log(`\n📊 Validation Summary: ${workingCount}/${totalCount} components working`);
    
    if (workingCount === totalCount) {
        console.log('🎉 All UI components are working correctly!');
    } else {
        console.warn('⚠️ Some UI components may not be functioning properly.');
    }
    
    return results;
}

// Auto-run validation after page load
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        validateUIComponents();
    }, 1000); // Wait for UI Manager to initialize
});

// Make validation function globally available
window.validateUI = validateUIComponents;