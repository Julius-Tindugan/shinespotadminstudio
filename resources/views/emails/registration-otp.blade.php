<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Verification Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            padding: 40px 30px;
            text-align: center;
            color: #1a1a1a;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(255,255,255,0.3);
        }
        .header p {
            margin: 8px 0 0;
            font-size: 15px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .otp-container {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
            border: 3px solid #FFD700;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }
        .otp-label {
            font-size: 14px;
            color: #4a5568;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            color: #1a1a1a;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .expiration {
            font-size: 13px;
            color: #666666;
            margin-top: 15px;
        }
        .warning-box {
            background: linear-gradient(135deg, #FFF5F5 0%, #FEE2E2 100%);
            border-left: 4px solid #EF4444;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .warning-box p {
            margin: 0;
            color: #991B1B;
            font-size: 14px;
            line-height: 1.5;
        }
        .security-tips {
            background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            border: 2px solid #FFD700;
        }
        .security-tips h3 {
            margin: 0 0 12px;
            font-size: 16px;
            color: #1a1a1a;
        }
        .security-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #4a5568;
            font-size: 14px;
            line-height: 1.8;
        }
        .footer {
            background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%);
            padding: 25px 30px;
            text-align: center;
            color: #CCCCCC;
            font-size: 13px;
            border-top: 1px solid #333333;
        }
        .footer p {
            margin: 5px 0;
        }
        .footer a {
            color: #FFD700;
            text-decoration: none;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div style="font-size: 42px; margin-bottom: 10px;">✨</div>
            <h1>Shine Spot Studio</h1>
            <p>Registration Verification</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hello {{ $userName }},
            </div>

            <div class="message">
                Thank you for registering as a <strong>{{ $userType }}</strong> with Shine Spot Studio! 
                To complete your registration, please use the verification code below:
            </div>

            <!-- OTP Code Display -->
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="expiration">
                    ⏱️ This code will expire in <strong>{{ $expirationMinutes }} minutes</strong>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="warning-box">
                <p>
                    <strong>⚠️ Important:</strong> If you didn't request this verification code, 
                    please ignore this email or contact our support team immediately.
                </p>
            </div>

            <!-- Security Tips -->
            <div class="security-tips">
                <h3>🔒 Security Tips</h3>
                <ul>
                    <li>Never share your verification code with anyone</li>
                    <li>Shine Spot Studio will never ask for your code via phone or chat</li>
                    <li>This code is single-use and will expire after verification</li>
                    <li>Ensure you're on our official registration page before entering the code</li>
                </ul>
            </div>

            <div class="message">
                If you have any questions or need assistance, feel free to reach out to our support team.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong style="color: #FFD700; font-size: 15px;">✨ Shine Spot Studio ✨</strong></p>
            <p style="margin-top: 10px;">Professional Photography Services</p>
            <p style="margin-top: 15px; color: #999999;">
                This is an automated message. Please do not reply to this email.
            </p>
            <p style="margin-top: 10px; color: #999999;">
                © {{ date('Y') }} Shine Spot Studio. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
