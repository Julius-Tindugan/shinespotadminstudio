/**
 * UI Components Manager
 * Handles all interactive UI functionality including dropdowns, sidebar, modals, etc.
 */

class UIManager {
    constructor() {
        this.isInitialized = false;
        this.components = {};
        console.log('🚀 UI Manager initializing...');
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeComponents();
            });
        } else {
            this.initializeComponents();
        }

        // Retry initialization for dynamic content
        this.retryInitialization();
    }

    initializeComponents() {
        console.log('🔧 Initializing UI components...');
        
        // Initialize each component with error handling
        try { this.initializeSidebarCollapse(); } catch (e) { console.error('Sidebar collapse init failed:', e); }
        try { this.initializeSidebarToggle(); } catch (e) { console.error('Sidebar toggle init failed:', e); }
        try { this.initializeModalHandlers(); } catch (e) { console.error('Modal handlers init failed:', e); }
        try { this.initializeSearchFunctionality(); } catch (e) { console.error('Search init failed:', e); }
        try { this.initializeTabs(); } catch (e) { console.error('Tabs init failed:', e); }
        try { this.initializeUserTypeDisplay(); } catch (e) { console.error('User type display init failed:', e); }
        
        // Check if we have at least some components working
        const workingComponents = Object.keys(this.components).length;
        if (workingComponents > 0) {
            this.isInitialized = true;
            console.log(`✅ UI Manager initialized with ${workingComponents} components working`);
        } else {
            console.warn('⚠️ UI Manager initialized but no components are working');
        }
    }

    retryInitialization() {
        let attempts = 0;
        const maxAttempts = 30; // Increased attempts for better reliability
        const retryInterval = setInterval(() => {
            attempts++;
            
            if (this.isInitialized || attempts >= maxAttempts) {
                clearInterval(retryInterval);
                if (!this.isInitialized && attempts >= maxAttempts) {
                    console.warn('⚠️ UI Manager initialization timed out, some components may not work');
                    // Force initialization with whatever elements we can find
                    this.forceInitialization();
                }
                return;
            }
            
            // Check if essential elements exist
            const essentialElements = [
                'sidebar-collapse-btn',
                'sidebar-toggle-btn'
            ];
            
            const existingElements = essentialElements.filter(id => document.getElementById(id));
            
            if (existingElements.length > 0) {
                console.log(`🔄 Retrying UI initialization (attempt ${attempts}) - found ${existingElements.length}/${essentialElements.length} elements`);
                this.initializeComponents();
                
                // Continue retrying until we get most essential elements or timeout
                if (existingElements.length >= 2) { // At least 2 out of 3 elements
                    clearInterval(retryInterval);
                }
            }
        }, 50); // Faster retry interval for better responsiveness
    }

    forceInitialization() {
        console.log('🔄 Force initializing UI components...');
        // Initialize whatever we can find
        try { this.initializeSidebarCollapse(); } catch (e) { console.error('Sidebar collapse force init failed:', e); }
        try { this.initializeSidebarToggle(); } catch (e) { console.error('Sidebar toggle force init failed:', e); }
        
        const workingComponents = Object.keys(this.components).length;
        this.isInitialized = workingComponents > 0;
        console.log(`✅ Force initialization completed with ${workingComponents} components`);
    }

    initializeSidebarCollapse() {
        const sidebar = document.getElementById('sidebar');
        const collapseBtn = document.getElementById('sidebar-collapse-btn');
        const collapseIcon = document.getElementById('collapse-icon');
        const expandIcon = document.getElementById('expand-icon');
        
        if (!sidebar || !collapseBtn || !collapseIcon || !expandIcon) {
            console.warn('⚠️ Sidebar collapse elements not found');
            return;
        }

        // Remove existing event listeners by cloning and replacing
        const newCollapseBtn = this.cloneAndReplace(collapseBtn);

        // Initialize from localStorage ONLY on desktop
        if (window.innerWidth >= 1024) {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (sidebarCollapsed) {
                this.collapseSidebar(sidebar, collapseIcon, expandIcon);
            }
        }

        newCollapseBtn.addEventListener('click', (e) => {
            e.preventDefault();
            console.log('📐 Sidebar collapse clicked');
            
            const isCollapsed = sidebar.classList.contains('w-20');
            
            if (isCollapsed) {
                this.expandSidebar(sidebar, collapseIcon, expandIcon);
            } else {
                this.collapseSidebar(sidebar, collapseIcon, expandIcon);
            }
            
            // Save state
            localStorage.setItem('sidebarCollapsed', (!isCollapsed).toString());
        });

        // Mark button as having event listener
        newCollapseBtn.setAttribute('data-has-listener', 'true');

        this.components.sidebarCollapse = { 
            sidebar, 
            btn: newCollapseBtn, 
            collapseIcon, 
            expandIcon 
        };
        console.log('✅ Sidebar collapse initialized');
    }

    collapseSidebar(sidebar, collapseIcon, expandIcon) {
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-20');
        collapseIcon.classList.add('hidden');
        expandIcon.classList.remove('hidden');
        
        // Hide text elements
        document.querySelectorAll('.sidebar-text').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Handle logo visibility
        const expandedLogo = document.getElementById('sidebar-expanded-logo');
        const collapsedLogo = document.getElementById('sidebar-collapsed-logo');
        
        if (expandedLogo) expandedLogo.classList.add('hidden');
        if (collapsedLogo) collapsedLogo.classList.remove('hidden');
    }

    expandSidebar(sidebar, collapseIcon, expandIcon) {
        sidebar.classList.remove('w-20');
        sidebar.classList.add('w-64');
        collapseIcon.classList.remove('hidden');
        expandIcon.classList.add('hidden');
        
        // Show text elements
        document.querySelectorAll('.sidebar-text').forEach(el => {
            el.classList.remove('hidden');
        });
        
        // Handle logo visibility
        const expandedLogo = document.getElementById('sidebar-expanded-logo');
        const collapsedLogo = document.getElementById('sidebar-collapsed-logo');
        
        if (expandedLogo) expandedLogo.classList.remove('hidden');
        if (collapsedLogo) collapsedLogo.classList.add('hidden');
    }

    initializeSidebarToggle() {
        const toggleBtn = document.getElementById('sidebar-toggle-btn');
        const closeBtn = document.getElementById('sidebar-close-btn');
        const sidebar = document.getElementById('sidebar');
        
        if (!toggleBtn || !sidebar) {
            console.warn('⚠️ Sidebar toggle elements not found');
            return;
        }

        // Remove existing event listeners by cloning and replacing
        const newToggleBtn = this.cloneAndReplace(toggleBtn);
        
        // Handle close button if it exists
        let newCloseBtn = null;
        if (closeBtn) {
            newCloseBtn = this.cloneAndReplace(closeBtn);
        }

        // Toggle button click handler
        newToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            console.log('📱 Mobile sidebar toggle clicked');
            
            const isOpen = sidebar.classList.contains('mobile-open') || !sidebar.classList.contains('-translate-x-full');
            
            if (isOpen) {
                this.hideMobileSidebar(sidebar);
            } else {
                this.showMobileSidebar(sidebar);
            }
        });

        // Close button click handler
        if (newCloseBtn) {
            newCloseBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('✖️ Mobile sidebar close clicked');
                this.hideMobileSidebar(sidebar);
            });
        }

        // Mark buttons as having event listeners
        newToggleBtn.setAttribute('data-has-listener', 'true');
        if (newCloseBtn) {
            newCloseBtn.setAttribute('data-has-listener', 'true');
        }

        // Close sidebar when clicking on navigation links (mobile only)
        if (window.innerWidth < 1024) {
            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    // Small delay to allow navigation to start
                    setTimeout(() => {
                        this.hideMobileSidebar(sidebar);
                    }, 150);
                });
            });
        }

        // Initialize swipe gestures for mobile
        this.initializeSwipeGestures(sidebar);

        this.components.sidebarToggle = { 
            toggleBtn: newToggleBtn, 
            closeBtn: newCloseBtn,
            sidebar 
        };
        console.log('✅ Sidebar toggle initialized');
    }

    initializeSwipeGestures(sidebar) {
        if (!sidebar) return;

        let touchStartX = 0;
        let touchEndX = 0;
        let touchStartY = 0;
        let touchEndY = 0;
        const swipeThreshold = 50; // Minimum swipe distance
        const edgeThreshold = 20; // Touch start area from edge

        // Handle swipe to open from left edge
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            
            // Only trigger if touch starts from left edge and sidebar is closed
            if (window.innerWidth < 1024 && 
                touchStartX < edgeThreshold && 
                sidebar.classList.contains('-translate-x-full')) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchmove', (e) => {
            if (window.innerWidth >= 1024) return; // Desktop
            
            const touchX = e.touches[0].clientX;
            
            // If swiping from left edge, show preview
            if (touchStartX < edgeThreshold && touchX > touchStartX + 10) {
                e.preventDefault();
            }
        }, { passive: false });

        document.addEventListener('touchend', (e) => {
            if (window.innerWidth >= 1024) return; // Desktop only on mobile
            
            touchEndX = e.changedTouches[0].clientX;
            touchEndY = e.changedTouches[0].clientY;
            
            const swipeDistanceX = touchEndX - touchStartX;
            const swipeDistanceY = Math.abs(touchEndY - touchStartY);
            
            // Ensure horizontal swipe (not vertical scroll)
            if (swipeDistanceY < 50) {
                // Swipe right from left edge to open
                if (touchStartX < edgeThreshold && 
                    swipeDistanceX > swipeThreshold &&
                    sidebar.classList.contains('-translate-x-full')) {
                    this.showMobileSidebar(sidebar);
                }
                
                // Swipe left to close when sidebar is open
                if (!sidebar.classList.contains('-translate-x-full') && 
                    swipeDistanceX < -swipeThreshold) {
                    this.hideMobileSidebar(sidebar);
                }
            }
        });

        console.log('✅ Swipe gestures initialized');
    }

    showMobileSidebar(sidebar) {
        // Remove any existing backdrop first
        const existingBackdrop = document.getElementById('mobile-sidebar-backdrop');
        if (existingBackdrop) {
            existingBackdrop.remove();
        }
        
        // Create backdrop with z-40 (lower than sidebar's z-50)
        const backdrop = document.createElement('div');
        backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300 opacity-0 lg:hidden';
        backdrop.id = 'mobile-sidebar-backdrop';
        document.body.appendChild(backdrop);

        // Prevent body scroll on mobile
        document.body.classList.add('sidebar-open');

        // Show sidebar - ensure it's properly positioned
        sidebar.classList.remove('-translate-x-full');
        sidebar.classList.add('mobile-open', 'translate-x-0');

        // Animate backdrop
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
        });

        // Close on backdrop click
        backdrop.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.hideMobileSidebar(sidebar);
        });

        // Close on ESC key
        const escHandler = (e) => {
            if (e.key === 'Escape') {
                this.hideMobileSidebar(sidebar);
                document.removeEventListener('keydown', escHandler);
            }
        };
        document.addEventListener('keydown', escHandler);
    }

    hideMobileSidebar(sidebar) {
        const backdrop = document.getElementById('mobile-sidebar-backdrop');
        
        // Hide sidebar
        sidebar.classList.remove('mobile-open', 'translate-x-0');
        sidebar.classList.add('-translate-x-full');
        
        // Restore body scroll
        document.body.classList.remove('sidebar-open');
        
        if (backdrop) {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            
            setTimeout(() => {
                if (backdrop && backdrop.parentNode) {
                    backdrop.remove();
                }
            }, 300);
        }
    }

    initializeModalHandlers() {
        // Modal triggers
        document.addEventListener('click', (e) => {
            const modalTrigger = e.target.closest('[data-modal-target]');
            if (modalTrigger) {
                e.preventDefault();
                const modalId = modalTrigger.dataset.modalTarget;
                console.log(`🔍 Opening modal: ${modalId}`);
                this.openModal(modalId);
            }
        });

        // Modal close buttons
        document.addEventListener('click', (e) => {
            const closeBtn = e.target.closest('.modal-close');
            if (closeBtn) {
                const modal = closeBtn.closest('.fixed.inset-0');
                if (modal) {
                    console.log(`✖️ Closing modal: ${modal.id}`);
                    this.closeModal(modal.id);
                }
            }
        });

        // Close modal on backdrop click
        document.querySelectorAll('.fixed.inset-0').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeModal(modal.id);
                }
            });
        });

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.fixed.inset-0:not(.hidden)');
                if (openModal) {
                    this.closeModal(openModal.id);
                }
            }
        });

        console.log('✅ Modal handlers initialized');
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`❌ Modal not found: ${modalId}`);
            return;
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.querySelector('.modal-content, .transform')?.classList.remove('opacity-0', 'scale-95');
            modal.querySelector('.modal-content, .transform')?.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const content = modal.querySelector('.modal-content, .transform');
        if (content) {
            content.classList.add('opacity-0', 'scale-95');
            content.classList.remove('opacity-100', 'scale-100');
        }

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    initializeSearchFunctionality() {
        const searchInput = document.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                // Add search functionality here
                console.log('🔍 Search query:', e.target.value);
            });
            console.log('✅ Search functionality initialized');
        }
    }

    initializeTabs() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        if (tabButtons.length === 0) return;

        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                console.log(`📋 Tab clicked: ${button.dataset.tab}`);
                
                // Deactivate all tabs
                tabButtons.forEach(btn => {
                    btn.classList.remove('active-tab', 'text-accent', 'border-accent', 'border-b-2');
                    btn.classList.add('text-secondary-text', 'dark:text-dark-secondary-text');
                });

                // Activate clicked tab
                button.classList.add('active-tab', 'text-accent', 'border-accent', 'border-b-2');
                button.classList.remove('text-secondary-text', 'dark:text-dark-secondary-text');

                // Hide all contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });

                // Show selected content
                const selectedContent = document.getElementById(button.dataset.tab);
                if (selectedContent) {
                    selectedContent.classList.remove('hidden');
                }
            });
        });

        console.log('✅ Tab functionality initialized');
    }

    /**
     * Initialize dynamic user type display functionality
     */
    initializeUserTypeDisplay() {
        const userTypeElement = document.getElementById('sidebar-user-type');
        
        if (!userTypeElement) {
            console.warn('⚠️ Sidebar user type element not found');
            return;
        }

        // Store reference for potential future updates
        this.components.userTypeDisplay = userTypeElement;
        console.log('✅ User type display initialized');
    }

    /**
     * Update sidebar user type text with smooth transition
     * @param {string} userType - Either 'admin' or 'staff'
     */
    updateUserType(userType) {
        const userTypeElement = document.getElementById('sidebar-user-type');
        
        if (!userTypeElement) {
            console.warn('⚠️ Cannot update user type - element not found');
            return;
        }

        const newText = userType === 'admin' ? 'ADMIN' : 'STAFF';
        
        // Add fade out effect
        userTypeElement.style.opacity = '0';
        userTypeElement.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            userTypeElement.textContent = newText;
            // Fade back in
            userTypeElement.style.opacity = '1';
            userTypeElement.style.transform = 'scale(1)';
            
            console.log(`✅ User type updated to: ${newText}`);
        }, 150);
    }

    /**
     * Get current user type from the sidebar display
     * @returns {string} Current user type ('admin' or 'staff')
     */
    getCurrentUserType() {
        const userTypeElement = document.getElementById('sidebar-user-type');
        
        if (!userTypeElement) {
            console.warn('⚠️ Cannot get user type - element not found');
            return null;
        }

        const currentText = userTypeElement.textContent.trim();
        return currentText === 'ADMIN' ? 'admin' : 'staff';
    }

    // Utility function to clone and replace elements (removes all event listeners)
    cloneAndReplace(element) {
        if (!element) return null;
        const newElement = element.cloneNode(true);
        element.parentNode.replaceChild(newElement, element);
        return newElement;
    }

    // Debug function
    debugComponents() {
        console.log('🔧 UI Components Debug Info:');
        console.log('Components:', this.components);
        console.log('Initialized:', this.isInitialized);
        
        const elements = [
            'sidebar-collapse-btn',
            'sidebar-toggle-btn'
        ];
        
        elements.forEach(id => {
            const element = document.getElementById(id);
            console.log(`${element ? '✅' : '❌'} ${id}:`, element);
        });
    }

    // Enhanced debug methods
    debugStatus() {
        console.log('🔍 UI Manager Status:');
        console.log(`- Initialized: ${this.isInitialized}`);
        console.log(`- Components: ${Object.keys(this.components).length}`);
        console.log('- Working components:', Object.keys(this.components));
        
        // Check for missing elements
        const expectedElements = [
            'sidebar-collapse-btn',
            'sidebar-toggle-btn',
            'sidebar'
        ];
        
        expectedElements.forEach(elementId => {
            const el = document.getElementById(elementId);
            console.log(`- ${elementId}: ${el ? '✅ Found' : '❌ Missing'}`);
        });
        
        // Test event handlers
        this.testEventHandlers();
    }

    testEventHandlers() {
        console.log('🧪 Testing event handlers...');
        
        // Test sidebar collapse
        const collapseBtn = document.getElementById('sidebar-collapse-btn');
        if (collapseBtn) {
            const hasEventListeners = collapseBtn.onclick !== null || 
                                    collapseBtn.getAttribute('data-has-listener') === 'true';
            console.log(`- Sidebar collapse button: ${hasEventListeners ? '✅ Has handler' : '❌ No handler'}`);
        }
    }

    // Manual button testing function
    testButtons() {
        console.log('🔍 Manual button testing...');
        
        // Test sidebar collapse
        setTimeout(() => {
            const collapseBtn = document.getElementById('sidebar-collapse-btn');
            if (collapseBtn) {
                console.log('Testing sidebar collapse...');
                const sidebar = document.getElementById('sidebar');
                const initialWidth = sidebar ? sidebar.classList.contains('w-64') : false;
                collapseBtn.click();
                setTimeout(() => {
                    const newWidth = sidebar ? sidebar.classList.contains('w-20') : false;
                    console.log(`Sidebar collapse working: ${initialWidth !== newWidth ? '✅' : '❌'}`);
                    if (newWidth) {
                        collapseBtn.click(); // Restore state
                    }
                }, 100);
            }
        }, 600);
    }
}

// Initialize UI Manager
let uiManager;

function initializeUIManager() {
    if (!uiManager) {
        uiManager = new UIManager();
        
        // Make globally available for debugging
        window.uiManager = uiManager;
        window.debugUI = () => uiManager.debugComponents();
        window.debugUIStatus = () => uiManager.debugStatus();
        window.testUI = () => uiManager.testButtons();
        
        // Add user type update functions for global access
        window.updateSidebarUserType = (userType) => uiManager.updateUserType(userType);
        window.getCurrentSidebarUserType = () => uiManager.getCurrentUserType();
        
        // Add a simple function to reinitialize if needed
        window.reinitializeUI = () => {
            console.log('🔄 Reinitializing UI Manager...');
            uiManager.initializeComponents();
        };
    }
}

// Initialize immediately
initializeUIManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UIManager;
}