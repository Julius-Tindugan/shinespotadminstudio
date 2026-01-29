<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Booking Rescheduled</title><style> body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; } .container { max-width: 600px; margin: 0 auto; padding: 20px; } .header { background-color: #8b5cf6; color: white; padding: 20px; text-align: center; } .content { padding: 20px; background-color: #f9fafb; } .footer { text-align: center; padding: 20px; font-size: 12px; color: #6b7280; } .button { display: inline-block; background-color: #6366f1; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; margin: 10px 0; } .booking-details { background-color: white; border-radius: 5px; padding: 15px; margin: 20px 0; } .booking-reference { font-size: 18px; font-weight: bold; text-align: center; padding: 10px; background-color: #ddd6fe; border-radius: 5px; margin: 10px 0; } .manage-links { text-align: center; margin: 20px 0; } .changes { background-color: #f3f4f6; padding: 10px; border-left: 4px solid #8b5cf6; margin: 15px 0; } </style></head><body>
    <div class="container" >

        <div class="header" >

            <h1>Booking Rescheduled</h1>

        </div>

        <div class="content" >

            <p>Hello {{ $booking->client_first_name }},</p>

            <p>Your booking with Shine Photography Studio has been <strong>rescheduled</strong> successfully.</p>

            <div class="booking-reference" >
                Booking Reference: {{ $booking->booking_reference }}
            </div>

            <div class="booking-details" >

                <h3>New Booking Details:</h3>

                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</p>

                <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</p>

                @if($booking->package)
                    <p><strong>Package:</strong> {{ $booking->package->title }}</p>

                @endif

                <p><strong>Status:</strong> Rescheduled</p>

            </div>

            @if(isset($oldValues))
                <div class="changes" >

                    <h3>Previous Details:</h3>

                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($oldValues['booking_date'])->format('l, F j, Y') }}</p>

                    <p><strong>Time:</strong> {{ \Carbon\Carbon::parse($oldValues['start_time'])->format('g:i A') }} - {{ \Carbon\Carbon::parse($oldValues['end_time'])->format('g:i A') }}</p>

                </div>

            @endif

            <div class="manage-links" >

                <p>Need to make further changes to your booking?</p>
                <a href="{{ route('public.bookings.lookup') }}" class="button" >Manage My Booking</a>
            </div>

            <p>To manage your booking, you will need:</p>
            <ul><li>Your booking reference code: <strong>{{ $booking->booking_reference }}</strong></li><li>The email or phone number you used for booking</li></ul>
            <p>If you have any questions, please don't hesitate to contact us.</p>

            <p>We look forward to seeing you!</p>

            <p>Best regards,<br> Shine Photography Studio Team</p>

        </div>

        <div class="footer" >

            <p>&copy; {{ date('Y') }} Shine Photography Studio. All rights reserved.</p>

            <p>Address: 2nd Floor, CGN Building, Barangay San Pedro, Santo Tomas, Philippines</p>

            <p>Phone: 0991 690 5443 | Email: shinespotstudio@gmail.com</p>

        </div>

    </div>
</body></html>