@extends('layouts.app')

@section('title', 'Privacy Policy - Shine Spot Studio')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="fw-bold">Privacy Policy</h1>
                <p class="text-muted">Last updated: {{ date('F j, Y') }}</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h2 class="h4 mb-4">1. Introduction</h2>
                    <p class="mb-4">Shine Spot Studio ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our photography services. By using our services, you consent to the data practices described in this policy.</p>

                    <h2 class="h4 mb-4">2. Information We Collect</h2>
                    <h3 class="h6 mb-3">2.1 Personal Information</h3>
                    <p class="mb-2">We may collect personally identifiable information that you voluntarily provide, including:</p>
                    <ul class="mb-3">
                        <li>Full name</li>
                        <li>Email address</li>
                        <li>Phone number</li>
                        <li>Billing and payment information</li>
                        <li>Special requests or notes</li>
                        <li>Event location details (for event photography)</li>
                    </ul>

                    <h3 class="h6 mb-3">2.2 Usage Information</h3>
                    <p class="mb-2">We automatically collect information about your interaction with our website:</p>
                    <ul class="mb-3">
                        <li>IP address and device information</li>
                        <li>Browser type and version</li>
                        <li>Pages visited and time spent</li>
                        <li>Referring website</li>
                        <li>Date and time of visits</li>
                    </ul>

                    <h3 class="h6 mb-3">2.3 Cookies and Tracking Technologies</h3>
                    <ul class="mb-4">
                        <li>We use cookies to enhance your browsing experience</li>
                        <li>Session cookies for booking functionality</li>
                        <li>Analytics cookies to improve our services</li>
                        <li>You can disable cookies in your browser settings</li>
                    </ul>

                    <h2 class="h4 mb-4">3. How We Use Your Information</h2>
                    <h3 class="h6 mb-3">3.1 Service Provision</h3>
                    <ul class="mb-3">
                        <li>Process and manage your bookings</li>
                        <li>Communicate about your appointments</li>
                        <li>Provide customer support</li>
                        <li>Deliver photos and services</li>
                        <li>Process payments</li>
                    </ul>

                    <h3 class="h6 mb-3">3.2 Business Operations</h3>
                    <ul class="mb-3">
                        <li>Improve our website and services</li>
                        <li>Send booking confirmations and reminders</li>
                        <li>Conduct internal analytics</li>
                        <li>Comply with legal obligations</li>
                    </ul>

                    <h3 class="h6 mb-3">3.3 Marketing Communications</h3>
                    <ul class="mb-4">
                        <li>Send promotional offers (with your consent)</li>
                        <li>Share studio news and updates</li>
                        <li>Notify about new services or packages</li>
                        <li>You may opt out at any time</li>
                    </ul>

                    <h2 class="h4 mb-4">4. Information Sharing and Disclosure</h2>
                    <h3 class="h6 mb-3">4.1 We Do Not Sell Your Information</h3>
                    <p class="mb-3">Shine Spot Studio does not sell, trade, or rent your personal information to third parties.</p>

                    <h3 class="h6 mb-3">4.2 Limited Sharing</h3>
                    <p class="mb-2">We may share your information only in these circumstances:</p>
                    <ul class="mb-3">
                        <li><strong>Service Providers:</strong> Payment processors, cloud storage, email services</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights</li>
                        <li><strong>Business Transfers:</strong> In the event of merger or acquisition</li>
                        <li><strong>Emergency Situations:</strong> To protect health and safety</li>
                    </ul>

                    <h3 class="h6 mb-3">4.3 Photo Usage</h3>
                    <ul class="mb-4">
                        <li>Photos may be used for marketing with client consent</li>
                        <li>We will ask permission before using your photos publicly</li>
                        <li>You may request removal of your photos from our marketing materials</li>
                    </ul>

                    <h2 class="h4 mb-4">5. Data Security</h2>
                    <h3 class="h6 mb-3">5.1 Security Measures</h3>
                    <ul class="mb-3">
                        <li>SSL encryption for data transmission</li>
                        <li>Secure servers and databases</li>
                        <li>Regular security updates and monitoring</li>
                        <li>Limited access to personal information</li>
                        <li>Staff training on data protection</li>
                    </ul>

                    <h3 class="h6 mb-3">5.2 Data Retention</h3>
                    <ul class="mb-4">
                        <li>Personal data retained only as long as necessary</li>
                        <li>Booking information kept for business records (7 years)</li>
                        <li>Photos stored securely with backup systems</li>
                        <li>Inactive accounts may be archived after 2 years</li>
                    </ul>

                    <h2 class="h4 mb-4">6. Your Privacy Rights</h2>
                    <h3 class="h6 mb-3">6.1 Access and Control</h3>
                    <p class="mb-2">You have the right to:</p>
                    <ul class="mb-3">
                        <li>Access your personal information we hold</li>
                        <li>Request correction of inaccurate data</li>
                        <li>Request deletion of your personal data</li>
                        <li>Opt out of marketing communications</li>
                        <li>Request data portability</li>
                    </ul>

                    <h3 class="h6 mb-3">6.2 How to Exercise Your Rights</h3>
                    <p class="mb-4">To exercise any of these rights, please contact us using the information provided at the end of this policy. We will respond to your request within 30 days.</p>

                    <h2 class="h4 mb-4">7. Third-Party Services</h2>
                    <h3 class="h6 mb-3">7.1 Payment Processing</h3>
                    <ul class="mb-3">
                        <li>We use secure third-party payment processors</li>
                        <li>Payment data is handled according to industry standards</li>
                        <li>We do not store complete credit card information</li>
                    </ul>

                    <h3 class="h6 mb-3">7.2 Analytics and Marketing</h3>
                    <ul class="mb-4">
                        <li>Google Analytics for website usage statistics</li>
                        <li>Social media platforms for marketing</li>
                        <li>Email service providers for communications</li>
                        <li>These services have their own privacy policies</li>
                    </ul>

                    <h2 class="h4 mb-4">8. International Data Transfers</h2>
                    <p class="mb-4">Your information may be transferred to and processed in countries other than the Philippines. We ensure appropriate safeguards are in place to protect your data according to this privacy policy.</p>

                    <h2 class="h4 mb-4">9. Children's Privacy</h2>
                    <p class="mb-4">Our services are not directed to children under 13. We do not knowingly collect personal information from children under 13. If we become aware that we have collected such information, we will take steps to delete it promptly.</p>

                    <h2 class="h4 mb-4">10. Changes to Privacy Policy</h2>
                    <p class="mb-4">We may update this Privacy Policy from time to time. We will notify you of any material changes by posting the new policy on our website. Changes become effective immediately upon posting.</p>

                    <h2 class="h4 mb-4">11. Data Protection Officer</h2>
                    <p class="mb-4">For questions about data protection and privacy, you may contact our designated privacy officer at privacy@shinespotph.com.</p>

                    <h2 class="h4 mb-4">12. Contact Information</h2>
                    <p class="mb-2">If you have questions about this Privacy Policy or our data practices, please contact us:</p>
                    <ul class="mb-4">
                        <li><strong>Email:</strong> privacy@shinespotph.com</li>
                        <li><strong>General Inquiries:</strong> info@shinespotph.com</li>
                        <li><strong>Phone:</strong> +63 917 123 4567</li>
                        <li><strong>Address:</strong> Shine Spot Studio, Metro Manila, Philippines</li>
                    </ul>

                    <div class="text-center mt-5 pt-4 border-top">
                        <p class="text-muted mb-0">Your privacy is important to us. Thank you for trusting Shine Spot Studio with your personal information.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
