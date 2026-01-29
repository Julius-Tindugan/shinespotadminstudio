@extends('layouts.app')

@section('title', 'Terms and Conditions - Shine Spot Studio')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="fw-bold">Terms and Conditions</h1>
                <p class="text-muted">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="h4 mb-4">1. Acceptance of Terms</h2>
                    <p class="mb-4">By accessing and using Shine Spot Studio's services, you accept and agree to be bound by the terms and provision of this agreement. If you do not agree to abide by the above, please do not use this service.</p>

                    <h2 class="h4 mb-4">2. Services Description</h2>
                    <p class="mb-2">Shine Spot Studio provides professional photography services including but not limited to:</p>
                    <ul class="mb-4">
                        <li>Self-shoot photography sessions</li>
                        <li>Event photography (weddings, parties, christenings)</li>
                        <li>Studio rentals</li>
                        <li>Photo editing and enhancement services</li>
                        <li>Digital photo delivery</li>
                    </ul>

                    <h2 class="h4 mb-4">3. Booking and Payment</h2>
                    <h3 class="h6 mb-3">3.1 Booking Process</h3>
                    <ul class="mb-3">
                        <li>All bookings must be made through our online platform</li>
                        <li>A valid payment method is required to confirm your booking</li>
                        <li>Booking confirmation will be sent via email</li>
                    </ul>

                    <h3 class="h6 mb-3">3.2 Payment Terms</h3>
                    <ul class="mb-4">
                        <li>Payment is required at the time of booking</li>
                        <li>We accept major credit cards and digital payment methods</li>
                        <li>All prices are in Philippine Pesos (₱)</li>
                        <li>Additional charges may apply for services beyond the package scope</li>
                    </ul>

                    <h2 class="h4 mb-4">4. Cancellation and Refund Policy</h2>
                    <h3 class="h6 mb-3">4.1 Cancellation by Client</h3>
                    <ul class="mb-3">
                        <li><strong>Less than 48 hours from booking creation:</strong> Full refund</li>
                        <li><strong>24-48 hours after booking creation:</strong> No refund</li>
                        <li><strong>No-show:</strong> No refund</li>
                    </ul>

                    <h3 class="h6 mb-3">4.2 Rescheduling</h3>
                    <ul class="mb-4">
                        <li>Sessions may be rescheduled up to 24 hours in advance</li>
                        <li>Rescheduling fees do not apply</li>
                        <li>Subject to studio availability</li>
                    </ul>

                    <h2 class="h4 mb-4">5. Client Responsibilities</h2>
                    <ul class="mb-4">
                        <li>Arrive on time for scheduled sessions</li>
                        <li>Follow studio guidelines and safety protocols</li>
                        <li>Respect studio equipment and property</li>
                        <li>Provide accurate contact information</li>
                        <li>Inform us of any special requirements or concerns</li>
                        <li>Dress appropriately for the session type</li>
                    </ul>

                    <h2 class="h4 mb-4">6. Photo Delivery and Usage Rights</h2>
                    <h3 class="h6 mb-3">6.1 Delivery</h3>
                    <ul class="mb-3">
                        <li>Photos will be delivered within 5-7 business days</li>
                        <li>Delivery method: Google Drive link via email</li>
                        <li>Files will be available for download for 30 days</li>
                    </ul>

                    <h3 class="h6 mb-3">6.2 Usage Rights</h3>
                    <ul class="mb-4">
                        <li>Clients receive full personal usage rights for delivered photos</li>
                        <li>Shine Spot Studio retains copyright and may use photos for marketing purposes</li>
                        <li>Commercial use of photos requires separate licensing agreement</li>
                        <li>Photo editing or alteration by clients may affect quality guarantee</li>
                    </ul>

                    <h2 class="h4 mb-4">7. Studio Policies</h2>
                    <h3 class="h6 mb-3">7.1 Safety and Conduct</h3>
                    <ul class="mb-3">
                        <li>No food or drinks allowed in studio areas</li>
                        <li>Smoking is strictly prohibited</li>
                        <li>Children must be supervised at all times</li>
                        <li>Studio equipment is for staff use only</li>
                    </ul>

                    <h3 class="h6 mb-3">7.2 Liability</h3>
                    <ul class="mb-4">
                        <li>Clients use studio facilities at their own risk</li>
                        <li>Shine Spot Studio is not responsible for personal injury or property damage</li>
                        <li>Insurance coverage recommendations available upon request</li>
                    </ul>

                    <h2 class="h4 mb-4">8. Force Majeure</h2>
                    <p class="mb-4">Shine Spot Studio shall not be liable for any failure or delay in performance due to circumstances beyond our reasonable control, including but not limited to natural disasters, government actions, or public health emergencies. In such cases, we will work with clients to reschedule or provide appropriate remedies.</p>

                    <h2 class="h4 mb-4">9. Limitation of Liability</h2>
                    <p class="mb-4">Our liability is limited to the amount paid for services. We are not responsible for consequential or indirect damages. Our maximum liability shall not exceed the total amount paid by the client for the specific service.</p>

                    <h2 class="h4 mb-4">10. Dispute Resolution</h2>
                    <p class="mb-4">Any disputes arising from these terms shall be resolved through good faith negotiation. If resolution cannot be reached, disputes will be handled according to Philippine law and jurisdiction.</p>

                    <h2 class="h4 mb-4">11. Modifications to Terms</h2>
                    <p class="mb-4">Shine Spot Studio reserves the right to modify these terms at any time. Changes will be effective immediately upon posting on our website. Continued use of our services constitutes acceptance of modified terms.</p>

                    <h2 class="h4 mb-4">12. Contact Information</h2>
                    <p class="mb-2">For questions regarding these Terms and Conditions, please contact us:</p>
                    <ul class="mb-4">
                        <li><strong>Email:</strong> info@shinespotph.com</li>
                        <li><strong>Phone:</strong> +63 917 123 4567</li>
                        <li><strong>Address:</strong> Shine Spot Studio, Metro Manila, Philippines</li>
                    </ul>

                    <div class="text-center mt-5 pt-4 border-top">
                        <p class="text-muted mb-0">Thank you for choosing Shine Spot Studio for your photography needs!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
