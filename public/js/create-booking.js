document.addEventListener('DOMContentLoaded', function() {
    // Package selection handling
    const packageSelect = document.getElementById('package_id');
    const packageDetails = document.getElementById('package-details');
    const packagePrice = document.getElementById('package-price');
    const packageInclusions = document.getElementById('package-inclusions');
    const packageFreeItems = document.getElementById('package-free-items');
    
    // Get the addons section element
    const addonsSection = document.getElementById('addons-section');
    
    packageSelect.addEventListener('change', function() {
        const packageId = this.value;
        
        // Reset fields
        packageInclusions.innerHTML = '';
        packageFreeItems.innerHTML = '';
        
        // Reset all addon checkboxes when package changes
        document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
            checkbox.checked = false;
            // Also reset the quantities
            const qtyInput = checkbox.closest('.addon-item').querySelector('.addon-qty');
            if (qtyInput) qtyInput.value = 1;
        });
        
        if (packageId) {
            // Show package details section
            packageDetails.classList.remove('hidden');
            
            // Get the selected package price
            const selectedOption = this.options[this.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price || 0);
            
            // Update price display
            packagePrice.textContent = `₱${price.toFixed(2)}`;
            
            // Fetch package inclusions, free items, and package addons via AJAX
            fetch(`/api/packages/${packageId}/details`)
                .then(response => response.json())
                .then(data => {
                    // Update inclusions with enhanced display
                    if (data.inclusions && data.inclusions.length > 0) {
                        data.inclusions.forEach(inclusion => {
                            const li = document.createElement('li');
                            li.className = 'flex items-center';
                            li.innerHTML = `
                                <svg class="w-3 h-3 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                ${inclusion.inclusion_text}
                            `;
                            packageInclusions.appendChild(li);
                        });
                    } else {
                        packageInclusions.innerHTML = '<li class="text-gray-500 italic">No inclusions available for this package</li>';
                    }
                    
                    // Update free items with enhanced display
                    if (data.freeItems && data.freeItems.length > 0) {
                        data.freeItems.forEach(freeItem => {
                            const li = document.createElement('li');
                            li.className = 'flex items-center';
                            li.innerHTML = `
                                <svg class="w-3 h-3 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                ${freeItem.free_item_text}
                            `;
                            packageFreeItems.appendChild(li);
                        });
                    } else {
                        packageFreeItems.innerHTML = '<li class="text-gray-500 italic">No free items included with this package</li>';
                    }
                    
                    // Handle package addons if available
                    if (data.addons && data.addons.length > 0) {
                        // Pre-check addons that come with the package
                        data.addons.forEach(addon => {
                            const addonCheckbox = document.getElementById(`addon-${addon.addon_id}`);
                            if (addonCheckbox) {
                                addonCheckbox.checked = true;
                                
                                // Enable quantity controls
                                const addonItem = addonCheckbox.closest('.addon-item');
                                const qtyInput = addonItem.querySelector('.addon-qty');
                                const decreaseBtn = addonItem.querySelector('.qty-decrease');
                                const increaseBtn = addonItem.querySelector('.qty-increase');
                                
                                qtyInput.removeAttribute('disabled');
                                decreaseBtn.removeAttribute('disabled');
                                increaseBtn.removeAttribute('disabled');
                                
                                // Set the quantity if provided
                                if (addon.quantity) {
                                    qtyInput.value = addon.quantity;
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching package details:', error);
                    packageInclusions.innerHTML = '<li class="text-red-500">Error loading package inclusions</li>';
                    packageFreeItems.innerHTML = '<li class="text-red-500">Error loading package free items</li>';
                });
        } else {
            // Hide package details section if no package selected
            packageDetails.classList.add('hidden');
            
            document.getElementById('total_amount').value = '0.00';
        }
    });
        
    // Backdrop dropdown handling
    const backdropSelect = document.getElementById('backdrop_id');
    const customBackdropField = document.getElementById('custom-backdrop-field');
    const customBackdropInput = document.getElementById('custom_backdrop');
    
    if (backdropSelect) {
        backdropSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customBackdropField.classList.remove('hidden');
                customBackdropInput.focus();
            } else {
                customBackdropField.classList.add('hidden');
            }
        });
    }
    
    // Addon quantity buttons
    document.querySelectorAll('.addon-item').forEach(item => {
        const checkbox = item.querySelector('.addon-checkbox');
        const qtyInput = item.querySelector('.addon-qty');
        const decreaseBtn = item.querySelector('.qty-decrease');
        const increaseBtn = item.querySelector('.qty-increase');
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function() {
                const currentQty = parseInt(qtyInput.value);
                if (currentQty > 1) {
                    qtyInput.value = currentQty - 1;
                }
            });
        }
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', function() {
                const currentQty = parseInt(qtyInput.value);
                qtyInput.value = currentQty + 1;
            });
        }
        
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                // Enable/disable the quantity controls based on checkbox state
                if (this.checked) {
                    qtyInput.removeAttribute('disabled');
                    decreaseBtn.removeAttribute('disabled');
                    increaseBtn.removeAttribute('disabled');
                } else {
                    qtyInput.setAttribute('disabled', 'disabled');
                    decreaseBtn.setAttribute('disabled', 'disabled');
                    increaseBtn.setAttribute('disabled', 'disabled');
                }
            });
        }
    });
        
    // Initialize addon checkboxes state on page load
    document.querySelectorAll('.addon-checkbox').forEach(checkbox => {
        const qtyInput = checkbox.closest('.addon-item').querySelector('.addon-qty');
        const decreaseBtn = checkbox.closest('.addon-item').querySelector('.qty-decrease');
        const increaseBtn = checkbox.closest('.addon-item').querySelector('.qty-increase');
        
        // Initially disable quantity controls if checkbox is unchecked
        if (!checkbox.checked) {
            qtyInput.setAttribute('disabled', 'disabled');
            decreaseBtn.setAttribute('disabled', 'disabled');
            increaseBtn.setAttribute('disabled', 'disabled');
        }
    });
    
    // Initialize package details if a package is already selected
    if (packageSelect && packageSelect.value) {
        packageSelect.dispatchEvent(new Event('change'));
    }
    
    // Initialize backdrop field
    if (backdropSelect && backdropSelect.value === 'custom') {
        customBackdropField.classList.remove('hidden');
    }
        
    // Modal handling
    const modal = document.getElementById('booking-confirmation-modal');
    const validationModal = document.getElementById('validation-error-modal');
    const previewBtn = document.getElementById('preview-booking-btn');
    const closeModalBtn = document.getElementById('close-modal');
    const closeValidationModalBtn = document.getElementById('close-validation-modal');
    const cancelBookingBtn = document.getElementById('cancel-booking');
    const confirmBookingBtn = document.getElementById('confirm-booking');
    const form = document.querySelector('form');
    
    // Preview booking button handler
    if (previewBtn) {
        previewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Validate only the actual required fields that exist and are visible
            const invalidFields = [];
            let firstInvalidField = null;
            
            // Define the specific required fields that actually exist in the form
            const requiredFieldsToCheck = [
                { id: 'client_first_name', label: 'First Name' },
                { id: 'client_last_name', label: 'Last Name' },
                { id: 'status', label: 'Status' },
                { id: 'booking_date', label: 'Date' },
                { id: 'start_time', label: 'Start Time' },
                { id: 'end_time', label: 'End Time' },
                { id: 'package_id', label: 'Package' }
            ];
            
            requiredFieldsToCheck.forEach(fieldInfo => {
                const field = document.getElementById(fieldInfo.id);
                
                // Skip if field doesn't exist
                if (!field) {
                    console.warn(`Required field not found: ${fieldInfo.id}`);
                    return;
                }
                
                // Skip if field is hidden
                if (field.type === 'hidden' || field.style.display === 'none' || field.offsetParent === null) {
                    console.log(`Skipping hidden field: ${fieldInfo.id}`);
                    return;
                }
                
                let isFieldValid = true;
                
                // Check if field has value
                if (!field.value || field.value.trim() === '') {
                    isFieldValid = false;
                } else if (field.type === 'email' && !field.value.includes('@')) {
                    isFieldValid = false;
                } else if (field.tagName.toLowerCase() === 'select' && field.value === '') {
                    isFieldValid = false;
                }
                
                console.log(`Field ${fieldInfo.id}: value="${field.value}", valid=${isFieldValid}`);
                
                if (!isFieldValid) {
                    field.classList.add('border-red-500');
                    invalidFields.push({
                        field: field,
                        label: fieldInfo.label
                    });
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (invalidFields.length > 0) {
                // Show validation error modal if it exists, otherwise use alert
                if (validationModal) {
                    showValidationErrorModal(invalidFields, firstInvalidField);
                } else {
                    // Fallback to alert if validation modal doesn't exist
                    const errorMessages = invalidFields.map(item => item.label).join(', ');
                    alert(`Please fill in the following required fields: ${errorMessages}`);
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                    }
                }
                return;
            }
            
            // Populate modal with form data
            if (modal) {
                populateModal();
                modal.classList.remove('hidden');
            }
        });
    }
        
    // Close modal handlers
    if (closeModalBtn && modal) {
        closeModalBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    // Close validation modal handler with enhanced animation
    if (closeValidationModalBtn && validationModal) {
        closeValidationModalBtn.addEventListener('click', function() {
            hideValidationModal();
        });
    }
    
    if (cancelBookingBtn && modal) {
        cancelBookingBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    }
    
    // Confirm booking handler
    if (confirmBookingBtn && form) {
        confirmBookingBtn.addEventListener('click', function() {
            // Submit the form
            form.submit();
        });
    }
    
    // Click outside modal to close
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    }
    
    // Click outside validation modal to close with animation
    if (validationModal) {
        validationModal.addEventListener('click', function(e) {
            if (e.target === validationModal) {
                hideValidationModal();
            }
        });
    }
        
    // Function to show validation error modal with enhanced animations
    function showValidationErrorModal(invalidFields, firstInvalidField) {
        if (!validationModal || !closeValidationModalBtn) {
            return; // Exit if validation modal doesn't exist
        }
        
        const errorList = document.getElementById('validation-error-list');
        if (errorList) {
            errorList.innerHTML = '';
            
            invalidFields.forEach((item, index) => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <div class="flex items-center text-red-700 dark:text-red-300">
                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">${item.label}</span>
                    </div>
                `;
                li.className = 'flex items-center py-1';
                li.style.animationDelay = `${0.1 + index * 0.1}s`;
                errorList.appendChild(li);
            });
        }
        
        // Add form validation state
        document.body.classList.add('form-validating');
        
        // Show the modal with animation
        validationModal.classList.remove('hidden');
        const modalContent = validationModal.querySelector('.validation-modal-enter');
        if (modalContent) {
            modalContent.classList.remove('validation-modal-exit');
        }
        
        // Focus the close button for accessibility
        setTimeout(() => {
            closeValidationModalBtn.focus();
        }, 300);
        
        // Focus the first invalid field when modal is closed
        const focusHandler = function() {
            if (firstInvalidField) {
                setTimeout(() => {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 200);
            }
            closeValidationModalBtn.removeEventListener('click', focusHandler);
        };
        closeValidationModalBtn.addEventListener('click', focusHandler);
    }
    
    // Function to hide validation modal with exit animation
    function hideValidationModal() {
        if (!validationModal) return;
        
        const modalContent = validationModal.querySelector('.validation-modal-enter');
        if (modalContent) {
            modalContent.classList.add('validation-modal-exit');
        }
        
        // Remove form validation state
        document.body.classList.remove('form-validating');
        
        // Hide modal after animation
        setTimeout(() => {
            validationModal.classList.add('hidden');
            if (modalContent) {
                modalContent.classList.remove('validation-modal-exit');
            }
        }, 300);
    }
    
    // Handle ESC key for modals with enhanced animations
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (validationModal && !validationModal.classList.contains('hidden')) {
                hideValidationModal();
            } else if (modal && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        }
    });
    
    function populateModal() {
        if (!modal) return;
        
        // Client information
        const firstName = document.getElementById('client_first_name');
        const lastName = document.getElementById('client_last_name');
        const email = document.getElementById('client_email');
        const phone = document.getElementById('client_phone');
        
        const modalClientName = document.getElementById('modal-client-name');
        const modalClientEmail = document.getElementById('modal-client-email');
        const modalClientPhone = document.getElementById('modal-client-phone');
        
        if (modalClientName && firstName && lastName) {
            modalClientName.textContent = `${firstName.value} ${lastName.value}`;
        }
        if (modalClientEmail && email) {
            modalClientEmail.textContent = email.value || 'Not provided';
        }
        if (modalClientPhone && phone) {
            modalClientPhone.textContent = phone.value || 'Not provided';
        }
        
        // Session details
        const date = document.getElementById('booking_date');
        const startTime = document.getElementById('start_time');
        const endTime = document.getElementById('end_time');
        const venue = document.getElementById('venue');
        
        const modalBookingDate = document.getElementById('modal-booking-date');
        const modalBookingTime = document.getElementById('modal-booking-time');
        const modalVenue = document.getElementById('modal-venue');
        
        if (modalBookingDate && date) {
            modalBookingDate.textContent = new Date(date.value).toLocaleDateString();
        }
        if (modalBookingTime && startTime && endTime) {
            modalBookingTime.textContent = `${startTime.value} - ${endTime.value}`;
        }
        if (modalVenue && venue) {
            modalVenue.textContent = venue.value || 'Not specified';
        }
        
        // Package information
        const packageSelect = document.getElementById('package_id');
        const modalPackageName = document.getElementById('modal-package-name');
        const modalPackagePrice = document.getElementById('modal-package-price');
        
        let packagePrice = 0;
        if (packageSelect && packageSelect.selectedIndex > 0) {
            const selectedPackageOption = packageSelect.options[packageSelect.selectedIndex];
            const packageName = selectedPackageOption.text || 'No package selected';
            packagePrice = parseFloat(selectedPackageOption.dataset.price || 0);
            
            if (modalPackageName) modalPackageName.textContent = packageName;
            if (modalPackagePrice) modalPackagePrice.textContent = `₱${packagePrice.toFixed(2)}`;
        }
        
        // Selected addons
        const selectedAddons = document.querySelectorAll('.addon-checkbox:checked');
        const modalAddonsSection = document.getElementById('modal-addons-section');
        const modalAddonsList = document.getElementById('modal-addons-list');
        const modalBreakdownAddons = document.getElementById('modal-breakdown-addons');
        
        let addonsTotal = 0;
        if (selectedAddons.length > 0 && modalAddonsList) {
            if (modalAddonsSection) modalAddonsSection.classList.remove('hidden');
            modalAddonsList.innerHTML = '';
            
            selectedAddons.forEach(checkbox => {
                const addonId = checkbox.value;
                const addonItem = checkbox.closest('.addon-item');
                const addonLabel = addonItem.querySelector('label');
                const addonName = addonLabel ? addonLabel.textContent.trim() : 'Unknown addon';
                const qtyInput = document.getElementById(`addon-qty-${addonId}`);
                const quantity = qtyInput ? parseInt(qtyInput.value || 1) : 1;
                
                // Extract price
                const priceElement = addonItem.querySelector('.text-accent');
                let price = 0;
                if (priceElement) {
                    const priceText = priceElement.textContent.trim();
                    const priceMatch = priceText.match(/[\d,]+(\.\d+)?/);
                    if (priceMatch) {
                        price = parseFloat(priceMatch[0].replace(/,/g, ''));
                    }
                }
                
                const itemTotal = price * quantity;
                addonsTotal += itemTotal;
                
                const addonDiv = document.createElement('div');
                addonDiv.className = 'flex justify-between items-center py-1';
                addonDiv.innerHTML = `
                    <span>${addonName} (${quantity}x)</span>
                    <span class="font-medium">₱${itemTotal.toFixed(2)}</span>
                `;
                modalAddonsList.appendChild(addonDiv);
            });
        } else {
            if (modalAddonsSection) modalAddonsSection.classList.add('hidden');
        }
        
        // Price breakdown
        const modalBreakdownPackage = document.getElementById('modal-breakdown-package');
        const modalTotalAmount = document.getElementById('modal-total-amount');
        
        if (modalBreakdownPackage) {
            modalBreakdownPackage.textContent = `₱${packagePrice.toFixed(2)}`;
        }
        if (modalBreakdownAddons) {
            modalBreakdownAddons.textContent = `₱${addonsTotal.toFixed(2)}`;
        }
        
        const totalAmount = packagePrice + addonsTotal;
        if (modalTotalAmount) {
            modalTotalAmount.textContent = `₱${totalAmount.toFixed(2)}`;
        }
        
        // Update hidden form fields for backend
        const totalAmountInput = document.getElementById('total_amount');
        if (totalAmountInput) {
            totalAmountInput.value = totalAmount.toFixed(2);
        }
    }
});

