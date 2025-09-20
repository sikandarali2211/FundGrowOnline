<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Verification Code - FundGrow Online</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #3bd17a;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 10px;
        }
        .otp-code {
            background: linear-gradient(135deg, #3bd17a, #00d4aa);
            color: white;
            font-size: 32px;
            font-weight: bold;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            letter-spacing: 5px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }
        .security-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #3bd17a;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px 0;
        }
        .warning-icon {
            color: #f39c12;
            font-size: 20px;
            margin-right: 10px;
        }
        .success-icon {
            color: #27ae60;
            font-size: 20px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1 style="color: #3bd17a; margin: 0;">🔐 FundGrow Online</h1>
            <h2 style="color: #333; margin: 10px 0;">Security Verification Code</h2>
        </div>

        <p>Hello {{ $userName ?: 'Valued User' }},</p>

        <p>You have requested a security verification code for your FundGrow Online account. Please use the code below to complete your verification:</p>

        <div class="otp-code">
            {{ $otp }}
        </div>

        <div class="security-notice">
            <span class="warning-icon">⚠️</span>
            <strong>Important Security Information:</strong>
            <ul style="margin: 10px 0;">
                <li>This code will expire in <strong>10 minutes</strong></li>
                <li>Never share this code with anyone</li>
                <li>FundGrow Online will never ask for this code via phone or email</li>
                <li>If you didn't request this code, please ignore this email</li>
            </ul>
        </div>

        <p>If you're having trouble with the code above, you can also:</p>
        <ul>
            <li>Check your WhatsApp messages for the same code</li>
            <li>Request a new code if this one has expired</li>
            <li>Contact our support team if you continue to have issues</li>
        </ul>

        <div class="footer">
            <p><strong>Need Help?</strong></p>
            <p>If you have any questions or concerns, please contact our support team.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">
            <p style="font-size: 12px; color: #999;">
                © {{ date('Y') }} FundGrow Online. All rights reserved.<br>
                This email was sent to you because you requested a security verification code.
            </p>
        </div>
    </div>
</body>
</html>
