<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Shine Spot Password</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);">
                    
                    <!-- Header with Shine Spot Branding -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); padding: 50px 20px; text-align: center; position: relative; overflow: hidden;">
                            <!-- Logo/Icon Area -->
                            <div style="background-color: rgba(0,0,0,0.15); width: 90px; height: 90px; border-radius: 50%; margin: 0 auto 25px; display: inline-block; line-height: 90px; font-size: 42px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                ✨
                            </div>
                            <h1 style="margin: 0; color: #1a1a1a; font-size: 32px; font-weight: 800; letter-spacing: -1px; text-shadow: 0 2px 4px rgba(255,255,255,0.3);">
                                Shine Spot
                            </h1>
                            <p style="margin: 8px 0 0; color: #1a1a1a; font-size: 18px; font-weight: 500;">
                                Password Reset Request
                            </p>
                            <!-- Decorative shine effect -->
                            <div style="position: absolute; top: 20px; right: 20px; width: 30px; height: 30px; background: radial-gradient(circle, rgba(255,255,255,0.4) 0%, transparent 70%); border-radius: 50%;"></div>
                            <div style="position: absolute; bottom: 30px; left: 30px; width: 20px; height: 20px; background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%); border-radius: 50%;"></div>
                        </td>
                    </tr>
                    
                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 0;">
                            
                            <!-- Greeting Section -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 45px 45px 0; text-align: center;">
                                        <h2 style="margin: 0; font-size: 24px; color: #1a1a1a; font-weight: 700; line-height: 1.3;">
                                            Hello {{ $userName }}! 👋
                                        </h2>
                                        <p style="margin: 15px 0 0; font-size: 16px; color: #666666; font-weight: 500;">
                                            Let's get you back into your account
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Message Section -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 30px 45px;">
                                        <div style="background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%); border-radius: 12px; padding: 30px; text-align: center; border: 2px solid #FFD700;">
                                            <p style="margin: 0; font-size: 16px; color: #1a1a1a; line-height: 1.7;">
                                                We received a request to reset your password for your <strong style="color: #FFA500;">Shine Spot</strong> {{ $userType }} account. Click the button below to create a new secure password.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Reset Button Section -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 0 45px 45px;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="border-radius: 12px; background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3); transition: transform 0.2s ease;">
                                                    <a href="{{ $resetUrl }}" style="display: inline-block; padding: 20px 45px; color: #FFD700; text-decoration: none; font-weight: 700; font-size: 16px; letter-spacing: 0.5px; text-transform: uppercase;">
                                                        🔒 Reset My Password
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin: 20px 0 0; font-size: 14px; color: #666666;">
                                            This link expires in <strong>{{ $expirationTime }}</strong> ⏰
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Divider with shine effect -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 0 45px;">
                                        <div style="height: 2px; background: linear-gradient(90deg, transparent 0%, #f59e0b 50%, transparent 100%); margin: 25px 0;"></div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Alternative Link Section -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 25px 45px;">
                                        <div style="background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%); border: 2px solid #FFD700; border-radius: 12px; padding: 25px; text-align: center;">
                                            <p style="margin: 0 0 15px; font-size: 15px; color: #1a1a1a; font-weight: 600;">
                                                🔗 Button not working? Copy this link:
                                            </p>
                                            <div style="background-color: #ffffff; border: 2px solid #FFA500; border-radius: 8px; padding: 15px; word-break: break-all; font-family: 'Courier New', monospace; font-size: 12px; color: #1a1a1a; font-weight: 500;">
                                                {{ $resetUrl }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Security Warning with Shine Spot styling -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 25px 45px 45px;">
                                        <div style="background: linear-gradient(135deg, #FFF5F5 0%, #FEE2E2 100%); border: 2px solid #EF4444; border-radius: 12px; padding: 25px;">
                                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                <tr>
                                                    <td>
                                                        <h3 style="margin: 0 0 15px; color: #DC2626; font-size: 18px; font-weight: 700;">
                                                            🛡️ Security Notice
                                                        </h3>
                                                        <p style="margin: 0; color: #991B1B; font-size: 15px; line-height: 1.6; font-weight: 500;">
                                                            If you didn't request this password reset, please ignore this email. Your account remains secure and no changes will be made. For extra security, consider enabling two-factor authentication in your Shine Spot admin panel.
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Help Section -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 0 45px 50px; text-align: center;">
                                        <div style="background: linear-gradient(135deg, #FFFAEB 0%, #FEF3C7 100%); border-radius: 12px; padding: 25px; border: 2px solid #FFD700;">
                                            <p style="margin: 0; font-size: 15px; color: #1a1a1a; line-height: 1.6; font-weight: 500;">
                                                Need help? Our Shine Spot support team is here for you! ✨<br>
                                                <a href="mailto:shinespotstudio@gmail.com" style="color: #FFA500; text-decoration: none; font-weight: 700;">shinespotstudio@gmail.com</a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Security Info -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="padding: 0 45px 30px;">
                                        <div style="background: #F9FAFB; border: 2px solid #E5E7EB; border-radius: 12px; padding: 20px; text-align: center;">
                                            <p style="margin: 0; font-size: 13px; color: #374151; line-height: 1.5;">
                                                <strong>🔐 Security Details:</strong><br>
                                                Request from IP: {{ $ipAddress }}<br>
                                                Time: {{ $requestTime }}<br>
                                                User Agent: {{ $userAgent }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                    <!-- Footer with Shine Spot Branding -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a1a 0%, #000000 100%); padding: 40px 45px; text-align: center;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td>
                                        <!-- Shine Spot Logo/Brand -->
                                        <div style="margin-bottom: 20px;">
                                            <span style="font-size: 24px;">✨</span>
                                            <h4 style="display: inline; margin: 0 0 0 10px; color: #FFD700; font-size: 20px; font-weight: 800; letter-spacing: 1px;">
                                                SHINE SPOT
                                            </h4>
                                        </div>
                                        <p style="margin: 0 0 15px; color: #CCCCCC; font-size: 15px; font-weight: 500;">
                                            Admin Security System
                                        </p>
                                        <p style="margin: 0 0 20px; color: #999999; font-size: 14px;">
                                            This is an automated security email. Please do not reply.
                                        </p>
                                        <div style="border-top: 2px solid #333333; margin: 25px 0; padding-top: 25px;">
                                            <p style="margin: 0; color: #999999; font-size: 13px; line-height: 1.5;">
                                                © {{ date('Y') }} Shine Spot Admin System. All rights reserved.<br>
                                                Making admin management shine brighter ✨<br>
                                                You're receiving this because you requested a password reset.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>