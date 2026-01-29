<!-- {{-- New Booking Modal --}} -->
    <div id="new-booking-modal" class="fixed inset-0 flex items-center justify-center hidden z-50" >
        <!-- Enhanced modal backdrop with better dark mode support -->
            <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300" >

            </div>

            <div class="modal-content bg-card-bg rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden transform transition-all opacity-0 scale-95 relative z-10 mx-4" >
                <!-- {{-- Modal Header --}} -->
                    <div class="flex items-center justify-between p-6 border-b border-border-color bg-gradient-to-r from-background to-card-bg" >

                        <div class="flex items-center space-x-3" >

                            <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center" >
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>

                                <h3 class="text-xl font-bold text-primary-text" >New Booking</h3>

                            </div>

                            <button class="modal-close p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-accent transition-colors duration-200" ><svg class="w-5 h-5 text-secondary-text" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>

                            </div>
                            <!-- {{-- Modal Body --}} -->
                                <div class="p-6 overflow-y-auto max-h-[60vh]" >

                                    <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}" class="space-y-6" >
                                        @csrf <!-- {{-- Client Information --}} -->
                                            <div>

                                                <h4 class="text-sm font-medium uppercase tracking-wider text-secondary-text mb-4" >Client Information</h4>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >

                                                    <div>
                                                        <label for="client_first_name" class="block text-sm font-medium text-secondary-text mb-1" >First Name <span class="text-danger" >*</span></label>
                                                        <input type="text" id="client_first_name" name="client_first_name" class="form-input" placeholder="Enter first name" required>

                                                        </div>

                                                        <div>
                                                            <label for="client_last_name" class="block text-sm font-medium text-secondary-text mb-1" >Last Name <span class="text-danger" >*</span></label>
                                                            <input type="text" id="client_last_name" name="client_last_name" class="form-input" placeholder="Enter last name" required>

                                                            </div>

                                                            <div>
                                                                <label for="client_email" class="block text-sm font-medium text-secondary-text mb-1" >Email <span class="text-danger" >*</span></label>
                                                                <input type="email" id="client_email" name="client_email" class="form-input" placeholder="Enter email address" required>

                                                                </div>

                                                                <div>
                                                                    <label for="client_phone" class="block text-sm font-medium text-secondary-text mb-1" >Phone Number <span class="text-danger" >*</span></label>
                                                                    <input type="tel" id="client_phone" name="client_phone" class="form-input" placeholder="+63 912 345 6789" required>

                                                                    </div>

                                                                </div>

                                                            </div>
                                                            <!-- {{-- Booking Details --}} -->
                                                                <div>

                                                                    <h4 class="text-sm font-medium uppercase tracking-wider text-secondary-text mb-4" >Booking Details</h4>

                                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >

                                                                        <div>
                                                                            <label for="package_id" class="block text-sm font-medium text-secondary-text mb-1" >Package <span class="text-danger" >*</span></label>
                                                                            <select id="package_id" name="package_id" class="form-select" required>
                                                                                <option value="" disabled selected>Select package</option><!-- Options will be populated dynamically -->
                                                                                </select>

                                                                            </div>

                                                                            <div>
                                                                                <label for="primary_staff_id" class="block text-sm font-medium text-secondary-text mb-1" >Primary Staff</label>
                                                                                <select id="primary_staff_id" name="primary_staff_id" class="form-select">
                                                                                    <option value="" disabled selected>Select staff member</option><!-- Options will be populated dynamically -->
                                                                                    </select>

                                                                                </div>

                                                                                <div>
                                                                                    <label for="booking_date" class="block text-sm font-medium text-secondary-text mb-1" >Date <span class="text-danger" >*</span></label>
                                                                                    <input type="date" id="booking_date" name="booking_date" class="form-input" required>

                                                                                    </div>

                                                                                    <div>
                                                                                        <label for="start_time" class="block text-sm font-medium text-secondary-text mb-1" >Start Time <span class="text-danger" >*</span></label>
                                                                                        <input type="time" id="start_time" name="start_time" class="form-input" required>

                                                                                        </div>

                                                                                        <div>
                                                                                            <label for="end_time" class="block text-sm font-medium text-secondary-text mb-1" >End Time <span class="text-danger" >*</span></label>
                                                                                            <input type="time" id="end_time" name="end_time" class="form-input" required>

                                                                                            </div>

                                                                                            <div class="md:col-span-2" >
                                                                                                <label for="venue" class="block text-sm font-medium text-secondary-text mb-1" >Venue</label>
                                                                                                <input type="text" id="venue" name="venue" class="form-input" placeholder="Enter venue">

                                                                                                </div>

                                                                                                <div>
                                                                                                    <label for="backdrop_id" class="block text-sm font-medium text-secondary-text mb-1" >Backdrop</label>
                                                                                                    <select id="backdrop_id" name="backdrop_id" class="form-select" >
                                                                                                        <option value="">No Backdrop</option><!-- Options will be populated dynamically -->
                                                                                                        </select>

                                                                                                    </div>

                                                                                                    <div>
                                                                                                        <label for="status" class="block text-sm font-medium text-secondary-text mb-1" >Status <span class="text-danger" >*</span></label>
                                                                                                        <select id="status" name="status" class="form-select" required>
                                                                                                            <option value="pending" selected>Pending</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="canceled">Canceled</option>
                                                                                                        </select>

                                                                                                    </div>

                                                                                                    <div class="md:col-span-2" >
                                                                                                        <label for="payment_method_id" class="block text-sm font-medium text-secondary-text mb-1" >Payment Method <span class="text-danger" >*</span></label>
                                                                                                        <select id="payment_method_id" name="payment_method_id" class="form-select" required>
                                                                                                            <option value="" disabled selected>Select payment method</option><!-- Options will be populated dynamically -->
                                                                                                            </select>

                                                                                                        </div>

                                                                                                    </div>

                                                                                                </div>
                                                                                                <!-- {{-- Add-ons --}} -->
                                                                                                    <div>

                                                                                                        <h4 class="text-sm font-medium uppercase tracking-wider text-secondary-text mb-4" >Add-ons</h4>

                                                                                                        <div id="addons-container" class="space-y-3 bg-background p-4 rounded-lg border border-border-color" >
                                                                                                            <!-- Add-ons will be loaded dynamically -->
                                                                                                            </div>

                                                                                                        </div>
                                                                                                        <!-- {{-- Booking Summary --}} -->
                                                                                                            <div>

                                                                                                                <h4 class="text-sm font-medium uppercase tracking-wider text-secondary-text mb-4" >Booking Summary</h4>

                                                                                                                <div class="bg-background p-4 rounded-lg border border-border-color" >

                                                                                                                    <div class="space-y-2" >

                                                                                                                        <div class="flex justify-between" >
                                                                                                                            <span>Package Cost:</span><span id="package-cost">₱0.00</span>
                                                                                                                        </div>

                                                                                                                        <div class="flex justify-between" >
                                                                                                                            <span>Add-ons Cost:</span><span id="addons-cost">₱0.00</span>
                                                                                                                        </div>
                                                                                                                        <hr class="border-border-color" >
                                                                                                                            <div class="flex justify-between font-bold" >
                                                                                                                                <span>Total Amount:</span><span id="total-amount">₱0.00</span>
                                                                                                                            </div>

                                                                                                                        </div>

                                                                                                                    </div>

                                                                                                                </div>
                                                                                                                <!-- {{-- Notes --}} -->
                                                                                                                    <div>

                                                                                                                        <h4 class="text-sm font-medium uppercase tracking-wider text-secondary-text mb-4" >Notes</h4>

                                                                                                                        <textarea id="notes" name="notes" rows="3" class="form-textarea w-full" placeholder="Add any additional notes about this booking"></textarea>

                                                                                                                    </div>

                                                                                                                </form>

                                                                                                            </div>
                                                                                                            <!-- {{-- Modal Footer --}} -->
                                                                                                                <div class="p-6 border-t border-border-color flex flex-wrap justify-end gap-3" >

                                                                                                                    <button class="modal-close btn btn-secondary" > Cancel </button>

                                                                                                                    <button type="submit" form="bookingForm" class="btn btn-primary" ><svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg> Create Booking </button>

                                                                                                                    </div>

                                                                                                                </div>

                                                                                                            </div>
                                                                                                            <script>
                                                                                                                document.addEventListener('DOMContentLoaded', function() { const modal = document.getElementById('new-booking-modal'); const packageSelect = document.getElementById('package_id'); const staffSelect = document.getElementById('primary_staff_id'); const backdropSelect = document.getElementById('backdrop_id'); const addonsContainer = document.getElementById('addons-container'); // Load data when modal is opened function loadModalData() { // Load packages fetch('/api/packages/list') .then(response => response.json()) .then(data => { packageSelect.innerHTML = '<option value="" disabled selected>Select package</option>'; data.forEach(package => { const option = document.createElement('option'); option.value = package.package_id; option.textContent = `${package.title} - ₱${parseFloat(package.price).toLocaleString()}`; option.dataset.price = package.price; packageSelect.appendChild(option); }); }) .catch(error => console.error('Error loading packages:', error)); // Load staff fetch('/api/staff/active') .then(response => response.json()) .then(data => { staffSelect.innerHTML = '<option value="" disabled selected>Select staff member</option>'; data.forEach(staff => { const option = document.createElement('option'); option.value = staff.staff_id; option.textContent = `${staff.first_name} ${staff.last_name}`; staffSelect.appendChild(option); }); }) .catch(error => console.error('Error loading staff:', error)); // Load backdrops fetch('/api/backdrops/list') .then(response => response.json()) .then(data => { backdropSelect.innerHTML = '<option value="">No Backdrop</option>'; data.forEach(backdrop => { const option = document.createElement('option'); option.value = backdrop.backdrop_id; option.textContent = backdrop.name; backdropSelect.appendChild(option); }); }) .catch(error => console.error('Error loading backdrops:', error)); // Load addons fetch('/api/addons/list') .then(response => response.json()) .then(data => { addonsContainer.innerHTML = ''; data.forEach(addon => { const addonDiv = document.createElement('div'); addonDiv.className = 'flex items-center justify-between'; addonDiv.innerHTML = `
                                                                                                                <div class="flex items-center" >

                                                                                                                    <input id="addon-${addon.addon_id}" name="addons[]" value="${addon.addon_id}" type="checkbox" class="form-checkbox addon-checkbox" data-price="${addon.addon_price}">
                                                                                                                        <label for="addon-${addon.addon_id}" class="ml-3 text-sm" >
                                                                                                                            <div class="font-medium" >
                                                                                                                                ${addon.addon_name}
                                                                                                                            </div>

                                                                                                                            <p class="text-secondary-text text-xs" >₱${parseFloat(addon.addon_price).toLocaleString()}</p>
                                                                                                                        </label>
                                                                                                                    </div>

                                                                                                                    <div class="flex items-center" >
                                                                                                                        <label for="addon_qty_${addon.addon_id}" class="text-sm mr-2" >Qty:</label>
                                                                                                                        <input type="number" id="addon_qty_${addon.addon_id}" name="addon_qty[${addon.addon_id}]" min="1" max="99" value="1" class="form-input w-16 text-center addon-qty" disabled>

                                                                                                                        </div>
                                                                                                                        `; addonsContainer.appendChild(addonDiv); }); // Add event listeners for addons document.querySelectorAll('.addon-checkbox').forEach(checkbox => { checkbox.addEventListener('change', function() { const addonId = this.value; const qtyInput = document.getElementById(`addon_qty_${addonId}`); qtyInput.disabled = !this.checked; if (!this.checked) { qtyInput.value = 1; } updateBookingSummary(); }); }); document.querySelectorAll('.addon-qty').forEach(input => { input.addEventListener('change', updateBookingSummary); }); }) .catch(error => console.error('Error loading addons:', error)); // Load payment methods fetch('/api/payment-methods/list') .then(response => response.json()) .then(data => { const paymentMethodSelect = document.getElementById('payment_method_id'); paymentMethodSelect.innerHTML = '<option value="" disabled selected>Select payment method</option>'; data.forEach(method => { const option = document.createElement('option'); option.value = method.method_id; option.textContent = method.name; paymentMethodSelect.appendChild(option); }); }) .catch(error => console.error('Error loading payment methods:', error)); } // Update booking summary function updateBookingSummary() { let packageCost = 0; let addonsCost = 0; // Get package cost const selectedPackage = packageSelect.options[packageSelect.selectedIndex]; if (selectedPackage && selectedPackage.dataset.price) { packageCost = parseFloat(selectedPackage.dataset.price); } // Calculate addons cost document.querySelectorAll('.addon-checkbox:checked').forEach(checkbox => { const addonPrice = parseFloat(checkbox.dataset.price) || 0; const addonId = checkbox.value; const qty = parseInt(document.getElementById(`addon_qty_${addonId}`).value) || 1; addonsCost += addonPrice * qty; }); const totalAmount = packageCost + addonsCost; // Update display document.getElementById('package-cost').textContent = '₱' + packageCost.toLocaleString('en-US', {minimumFractionDigits: 2}); document.getElementById('addons-cost').textContent = '₱' + addonsCost.toLocaleString('en-US', {minimumFractionDigits: 2}); document.getElementById('total-amount').textContent = '₱' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2}); } // Package selection change event packageSelect.addEventListener('change', updateBookingSummary); // Modal trigger event listeners document.querySelectorAll('[data-modal-target="new-booking-modal"]').forEach(trigger => { trigger.addEventListener('click', function(e) { e.preventDefault(); modal.classList.remove('hidden'); setTimeout(() => { modal.querySelector('.modal-content').classList.remove('opacity-0', 'scale-95'); }, 10); loadModalData(); }); }); // Modal close event listeners modal.querySelectorAll('.modal-close').forEach(closeBtn => { closeBtn.addEventListener('click', function() { modal.querySelector('.modal-content').classList.add('opacity-0', 'scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 150); }); }); // Close modal when clicking outside modal.addEventListener('click', function(e) { if (e.target === modal) { modal.querySelector('.modal-content').classList.add('opacity-0', 'scale-95'); setTimeout(() => { modal.classList.add('hidden'); }, 150); } });
                                                                                                                        });
                                                                                                                    </script>
