/**
 * Booking Reference Copy Functionality
 * Handles copying booking reference numbers to clipboard with visual feedback
 */

(function() {
    'use strict';
    
    // Main function to attach copy handlers
    function attachCopyReferenceHandlers() {
        const copyElements = document.querySelectorAll('.copy-reference');
        console.log('[Copy Reference] Attaching handlers to', copyElements.length, 'elements');
        
        copyElements.forEach(function(el) {
            // Clone element to remove old event listeners
            const newEl = el.cloneNode(true);
            if (el.parentNode) {
                el.parentNode.replaceChild(newEl, el);
            }
            
            // Add click event listener
            newEl.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const reference = this.getAttribute('data-reference');
                console.log('[Copy Reference] Clicked, reference:', reference);
                
                if (!reference) {
                    console.warn('[Copy Reference] No reference attribute found');
                    return;
                }
                
                copyToClipboard(this, reference);
            });
        });
    }
    
    // Copy text to clipboard
    function copyToClipboard(element, text) {
        // Try modern Clipboard API first
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text)
                .then(function() {
                    console.log('[Copy Reference] Successfully copied:', text);
                    showSuccess(element);
                })
                .catch(function(err) {
                    console.error('[Copy Reference] Clipboard API failed:', err);
                    fallbackCopy(element, text);
                });
        } else {
            // Use fallback for older browsers
            fallbackCopy(element, text);
        }
    }
    
    // Fallback copy method using textarea
    function fallbackCopy(element, text) {
        try {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-9999px';
            textArea.style.top = '-9999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            
            if (successful) {
                console.log('[Copy Reference] Fallback copy successful:', text);
                showSuccess(element);
            } else {
                throw new Error('execCommand failed');
            }
        } catch (err) {
            console.error('[Copy Reference] Fallback copy failed:', err);
            showError(element);
        }
    }
    
    // Show success feedback
    function showSuccess(element) {
        const originalContent = element.innerHTML;
        element.innerHTML = '<span class="text-green-500 flex items-center font-semibold"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!</span>';
        element.classList.add('pointer-events-none');
        
        setTimeout(function() {
            element.innerHTML = originalContent;
            element.classList.remove('pointer-events-none');
        }, 2000);
    }
    
    // Show error feedback
    function showError(element) {
        const originalContent = element.innerHTML;
        element.innerHTML = '<span class="text-red-500 flex items-center font-semibold"><svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>Failed</span>';
        
        setTimeout(function() {
            element.innerHTML = originalContent;
        }, 2000);
    }
    
    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', attachCopyReferenceHandlers);
    } else {
        attachCopyReferenceHandlers();
    }
    
    // Make function globally available for re-attachment after AJAX
    window.attachCopyReferenceHandlers = attachCopyReferenceHandlers;
    
    console.log('[Copy Reference] Script loaded and initialized');
})();
