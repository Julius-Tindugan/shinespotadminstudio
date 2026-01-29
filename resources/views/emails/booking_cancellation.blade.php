<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Booking Cancellation</title><style> body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; } .container { max-width: 600px; margin: 0 auto; padding: 20px; } .header { background-color: #ef4444; color: white; padding: 20px; text-align: center; } .content { padding: 20px; background-color: #f9fafb; } .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; } .button { display: inline-block; background-color: #6366f1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; margin: 10px 0; } .booking-details { background-color: white; border-radius: 5px; padding: 15px; margin: 20px 0; } .booking-reference { font-size: 18px; font-weight: bold; text-align: center; padding: 10px; background-color: #fee2e2; border-radius: 5px; margin: 10px 0; } .book-again { text-align: center; margin: 20px 0; } </style></head><body>
    <div class="container" >

        <div class="header" >

            <h1>Booking Cancellation</h1>

        </div>

        <div class="content" >

            <p>Hello {{ $booking->client_first_name }},</p>

            <p>Your booking with Shine Photography Studio has been <strong>cancelled</strong>.</p>

            <div class="booking-reference" >
                Booking Reference: {{ $booking->booking_reference }}
            </div>

            <div class="booking-details" >

                <h3>Cancelled Booking Details:</h3>

                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</p>

                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>

                @if($booking->package)
                    <p><strong>Package:</strong> {{ $booking->package->title }}</p>

                @endif

                <p><strong>Status:</strong> Cancelled</p>

                <p><strong>Cancellation Date:</strong> {{ \Carbon\Carbon::parse($booking->canceled_at)->format('F j, Y') }}</p>

            </div>

            @if(isset($booking->notes) && strpos($booking->notes, 'Cancellation reason:') !== false)
                <div class="booking-details" >

                    <h3>Cancellation Reason:</h3>

                    <p>{{ substr($booking->notes, strpos($booking->notes, 'Cancellation reason:') + 20) }}</p>

                </div>

            @endif

            <div class="book-again" >

                <p>Would you like to book another session?</p>
                <a href="https://shinestudio.com/book" class="button" >Book Again</a>
            </div>

            <p>If you have any questions about this cancellation, please don't hesitate to contact us.</p>

            <p>Thank you for considering Shine Photography Studio.</p>

            <p>Best regards,<br> Shine Photography Studio Team</p>

        </div>

        <div class="footer" >

            <p>&copy; {{ date('Y') }} Shine Photography Studio. All rights reserved.</p>

            <p>Address: 2nd Floor, CGN Building, Barangay San Pedro, Santo Tomas, Philippines</p>

            <p>Phone: 0991 690 5443 | Email: shinespotstudio@gmail.com</p>

        </div>

    </div>
</body></html>