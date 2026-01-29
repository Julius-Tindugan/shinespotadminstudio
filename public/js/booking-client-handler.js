/**
 * Client information handling for bookings
 * This script enhances the booking forms to support direct client information
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a booking form page
    const clientSelect = document.getElementById('client_id');
    const clientFirstNameInput = document.getElementById('client_first_name');
    const clientLastNameInput = document.getElementById('client_last_name');
    const clientEmailInput = document.getElementById('client_email');
    const clientPhoneInput = document.getElementById('client_phone');
    
    if (!clientSelect || !clientFirstNameInput) return;
    
    // Function to toggle direct client information fields
    function toggleClientFields() {
        const clientId = clientSelect.value;
        const fieldsContainer = document.getElementById('direct-client-fields');
        
        if (!clientId) {
            // Enable direct input fields
            clientFirstNameInput.removeAttribute('disabled');
            clientLastNameInput.removeAttribute('disabled');
            clientEmailInput.removeAttribute('disabled');
            clientPhoneInput.removeAttribute('disabled');
            
            // Make first and last name required
            clientFirstNameInput.setAttribute('required', 'required');
            clientLastNameInput.setAttribute('required', 'required');
            
            // Show fields container
            if (fieldsContainer) fieldsContainer.classList.remove('hidden');
        } else {
            // Disable direct input fields
            clientFirstNameInput.setAttribute('disabled', 'disabled');
            clientLastNameInput.setAttribute('disabled', 'disabled');
            clientEmailInput.setAttribute('disabled', 'disabled');
            clientPhoneInput.setAttribute('disabled', 'disabled');
            
            // Remove required
            clientFirstNameInput.removeAttribute('required');
            clientLastNameInput.removeAttribute('required');
            
            // Hide fields container
            if (fieldsContainer) fieldsContainer.classList.add('hidden');
        }
    }
    
    // Initialize field state
    toggleClientFields();
    
    // Add event listener to client select
    clientSelect.addEventListener('change', function() {
        // When a client is selected, populate the fields with their info
        const clientId = this.value;
        
        if (clientId) {
            // Fetch client details via API
            fetch(`/api/clients/${clientId}`)
                .then(response => response.json())
                .then(client => {
                    clientFirstNameInput.value = client.first_name || '';
                    clientLastNameInput.value = client.last_name || '';
                    clientEmailInput.value = client.email || '';
                    clientPhoneInput.value = client.phone || '';
                })
                .catch(error => {
                    console.error('Error fetching client data:', error);
                });
        } else {
            // Clear fields if no client selected
            clientFirstNameInput.value = '';
            clientLastNameInput.value = '';
            clientEmailInput.value = '';
            clientPhoneInput.value = '';
        }
        
        // Toggle field states
        toggleClientFields();
    });
});