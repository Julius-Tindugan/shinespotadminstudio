<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Link - {{ $booking->booking_reference }}</title>
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2d3748;
            background-color: #f7fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a1a;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(255,255,255,0.3);
        }
        .header p {
            margin: 8px 0 0;
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
        }
        .content {
            padding: 35px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 20px;
            font-weight: 600;
        }
        .message {
            font-size: 15px;
            color: #4a5568;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        .payment-info {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            border-left: 4px solid #FFD700;
            padding: 25px;
            margin: 25px 0;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(255, 215, 0, 0.15);
        }
        .payment-info .label {
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .payment-info .value {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .payment-button {
            display: block;
            width: 100%;
            max-width: 400px;
            margin: 30px auto;
            padding: 18px 32px;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            color: #FFD700;
            text-decoration: none;
            text-align: center;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }
        .payment-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        .payment-link {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            word-break: break-all;
            font-size: 13px;
            color: #4a5568;
            margin: 20px 0;
        }
        .instructions {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            border-left: 4px solid #FFD700;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .instructions h3 {
            margin: 0 0 12px;
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 700;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #4a5568;
        }
        .instructions li {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #333333;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #CCCCCC;
        }
        .footer strong {
            color: #FFD700;
        }
        .note {
            background: linear-gradient(135deg, #FFF5F5 0%, #FEE2E2 100%);
            border-left: 4px solid #EF4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-size: 14px;
            color: #991B1B;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💳 Payment Request</h1>
            <p>Shine Spot Studio</p>
        </div>

        <div class="content">
            <div class="greeting">
                Hello {{ $booking->client_first_name }} {{ $booking->client_last_name }},
            </div>

            <div class="message">
                Thank you for choosing Shine Spot Studio! We're excited to capture your special moments.
            </div>

            <div class="payment-info">
                <div class="label">Booking Reference</div>
                <div class="value">{{ $booking->booking_reference }}</div>
                
                <div class="label">Amount Due</div>
                <div class="value">₱{{ number_format($amount, 2) }}</div>
                
                <div class="label">Payment Method</div>
                <div class="value">GCash via Xendit</div>
            </div>

            <div class="message">
                Please click the button below to complete your payment securely through Xendit:
            </div>

            <a href="{{ $paymentUrl }}" class="payment-button">
                🔒 Pay Now with GCash
            </a>

            <div class="message" style="text-align: center; font-size: 13px; color: #718096;">
                Or copy and paste this link into your browser:
            </div>
            
            <div class="payment-link">
                {{ $paymentUrl }}
            </div>

            <div class="instructions">
                <h3>📝 Payment Instructions:</h3>
                <ol>
                    <li>Click the "Pay Now with GCash" button above</li>
                    <li>You will be redirected to Xendit's secure payment page</li>
                    <li>Select GCash as your payment method</li>
                    <li>Complete the payment through your GCash account</li>
                    <li>You will receive a confirmation once payment is successful</li>
                </ol>
            </div>

            <div class="note">
                <strong>⏰ Important:</strong> This payment link will expire in 24 hours. Please complete your payment before the link expires to secure your booking.
            </div>
        </div>

        <div class="footer">
            <p><strong style="font-size: 15px;">✨ Shine Spot Studio ✨</strong></p>
            <p style="margin-top: 10px;">📧 Email: shinespotstudio@gmail.com</p>
            <p>📱 Phone: +63 0991 690 5443</p>
            <p style="margin-top: 15px; font-size: 12px; color: #999999;">
                This is an automated email. Please do not reply to this message.
            </p>
        </div>
    </div>
</body>
</html>
