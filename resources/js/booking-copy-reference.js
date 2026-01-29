/**
 * Booking Reference Copy Functionality
 * Handles copying booking reference numbers to clipboard
 */

export function initializeCopyReferenceHandlers() {
    attachCopyReferenceHandlers();
}

export function attachCopyReferenceHandlers() {
    const copyElements = document.querySelectorAll('.copy-reference');
    console.log('Attaching copy handlers to', copyElements.length, 'elements');
    
    copyElements.forEach(el => {
        // Remove old event listeners by cloning the element
        const newEl = el.cloneNode(true);
        el.parentNode.replaceChild(newEl, el);
        
        // Add click event listener
        newEl.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const reference = this.getAttribute('data-reference');
            console.log('Attempting to copy reference:', reference);
            
            if (!reference) {
                console.warn('No reference attribute found on element');
                return;
            }
            
            // Try modern Clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                copyWithClipboardAPI(this, reference);
            } else {
                // Fallback for older browsers or non-HTTPS
                copyWithFallback(this, reference);
            }
        });
    });
}

function copyWithClipboardAPI(element, text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            console.log('Successfully copied:', text);
            showCopySuccess(element);
        })
        .catch(err => {
            console.error('Clipboard API failed:', err);
            copyWithFallback(element, text);
        });
}

function copyWithFallback(element, text) {
    try {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        const successful = document.execCommand('copy');
        document.body.removeChild(textArea);
        
        if (successful) {
            console.log('Successfully copied with fallback:', text);
            showCopySuccess(element);
        } else {
            throw new Error('execCommand copy failed');
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showCopyError(element);
    }
}

function showCopySuccess(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="text-green-500 flex items-center font-semibold">✓ Copied!</span>';
    
    setTimeout(() => {
        element.innerHTML = originalContent;
    }, 2000);
}

function showCopyError(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = '<span class="text-red-500 flex items-center font-semibold">✗ Failed</span>';
    
    setTimeout(() => {
        element.innerHTML = originalContent;
    }, 2000);
    
    // Try to show alert if available
    if (typeof window.showAlert === 'function') {
        window.showAlert('Failed to copy to clipboard', 'error');
    }
}

// Make it available globally for inline scripts
window.attachCopyReferenceHandlers = attachCopyReferenceHandlers;
