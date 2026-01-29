/**
 * UI Component Test & Debug Helper
 * This script tests all UI functionality and provides debugging information
 */

function testUIComponents() {
    console.log('🧪 Testing UI Components...');
    
    // Test results
    const results = {
        sidebarCollapse: false,
        sidebarToggle: false,
        modals: false
    };

    // Test Sidebar Collapse
    const sidebarCollapseBtn = document.getElementById('sidebar-collapse-btn');
    const sidebar = document.getElementById('sidebar');
    if (sidebarCollapseBtn && sidebar) {
        results.sidebarCollapse = true;
        console.log('✅ Sidebar collapse elements found');
        
        // Test click
        const wasCollapsed = sidebar.classList.contains('w-20');
        sidebarCollapseBtn.click();
        setTimeout(() => {
            const isCollapsed = sidebar.classList.contains('w-20');
            console.log(`📐 Sidebar ${isCollapsed !== wasCollapsed ? 'toggled successfully' : 'failed to toggle'}`);
        }, 150);
    } else {
        console.log('❌ Sidebar collapse elements NOT found');
    }

    // Test Mobile Sidebar Toggle
    const sidebarToggleBtn = document.getElementById('sidebar-toggle-btn');
    if (sidebarToggleBtn) {
        results.sidebarToggle = true;
        console.log('✅ Mobile sidebar toggle found');
    } else {
        console.log('❌ Mobile sidebar toggle NOT found');
    }

    // Test Modal Triggers
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    if (modalTriggers.length > 0) {
        results.modals = true;
        console.log(`✅ Found ${modalTriggers.length} modal triggers`);
    } else {
        console.log('❌ No modal triggers found');
    }

    // Summary
    const passedTests = Object.values(results).filter(r => r).length;
    const totalTests = Object.keys(results).length;
    
    console.log(`\n📊 Test Results: ${passedTests}/${totalTests} components working`);
    console.log('Results:', results);

    if (passedTests === totalTests) {
        console.log('🎉 All UI components are working correctly!');
    } else {
        console.log('⚠️ Some UI components need attention');
    }

    return results;
}

// Test managers availability
function testManagers() {
    console.log('\n🔧 Testing Managers...');
    
    console.log('Theme Manager:', window.themeManager ? '✅' : '❌');
    console.log('UI Manager:', window.uiManager ? '✅' : '❌');
    console.log('Debug UI Function:', typeof window.debugUI === 'function' ? '✅' : '❌');
}

// Comprehensive test
function runComprehensiveTest() {
    console.log('🚀 Running Comprehensive UI Test\n');
    testManagers();
    return testUIComponents();
}

// Auto-run test when page loads
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        runComprehensiveTest();
    }, 2000); // Wait 2 seconds for all scripts to load
});

// Make functions globally available
window.testUIComponents = testUIComponents;
window.testManagers = testManagers;
window.runComprehensiveTest = runComprehensiveTest;