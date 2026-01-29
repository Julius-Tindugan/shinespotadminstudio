<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Code</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 0;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a1a;
            padding: 40px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(255,255,255,0.3);
        }

        .content {
            padding: 35px 30px;
            background-color: #ffffff;
        }

        .content p {
            margin: 0 0 15px;
            color: #4a5568;
            font-size: 15px;
            line-height: 1.7;
        }

        .footer {
            text-align: center;
            padding: 25px 30px;
            font-size: 13px;
            color: #CCCCCC;
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
        }

        .footer p {
            margin: 5px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            letter-spacing: 8px;
            text-align: center;
            padding: 25px;
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            border-radius: 12px;
            margin: 30px 0;
            border: 3px solid #FFD700;
            color: #1a1a1a;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }

        .warning {
            background: linear-gradient(135deg, #FFF5F5 0%, #FEE2E2 100%);
            border-left: 4px solid #EF4444;
            color: #991B1B;
            font-size: 14px;
            padding: 15px 20px;
            margin: 20px 0;
            border-radius: 8px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="font-size: 42px; margin-bottom: 15px;">✨</div>
            <h1>Verification Code</h1>
            <p style="margin: 5px 0 0; font-size: 15px; opacity: 0.9;">Shine Spot Studio</p>
        </div>
        <div class="content">
            <p style="font-size: 16px; font-weight: 600; color: #1a1a1a; margin-bottom: 10px;">Hello {{ $booking->client_first_name }},</p>
            <p>You are attempting to access your booking with Shine Spot Studio. To verify your identity, please
                use the verification code below:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="text-align: center; font-weight: 600; color: #4a5568;">⏰ This code will expire in <strong style="color: #FFA500;">10 minutes</strong> and can only be used once.</p>
            <div class="warning">
                <strong>⚠️ Security Notice:</strong> If you did not request this code, please ignore this message and contact us immediately.
            </div>
            <p>Thank you for choosing Shine Spot Studio.</p>
            <p style="margin-top: 20px;"><strong>Best regards,</strong><br> Shine Spot Studio Team</p>
        </div>
        <div class="footer">
            <p><strong style="color: #FFD700; font-size: 15px;">✨ Shine Spot Studio ✨</strong></p>
            <p style="margin-top: 10px;">&copy; {{ date('Y') }} Shine Spot Studio. All rights reserved.</p>
            <p style="margin-top: 8px; color: #999999;">Address: 2nd Floor, CGN Building, Barangay San Pedro, Santo Tomas, Philippines</p>
            <p style="color: #999999;">Phone: 0991 690 5443 | Email: shinespotstudio@gmail.com</p>
        </div>
    </div>
</body>

</html>
