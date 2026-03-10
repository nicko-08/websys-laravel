<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }

        .container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #128a43 0%, #0d6b35 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 18px;
            color: #128a43;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message {
            margin-bottom: 25px;
            line-height: 1.8;
        }

        .cta-button {
            display: inline-block;
            background: #128a43;
            color: white;
            text-decoration: none;
            padding: 15px 35px;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }

        .cta-button:hover {
            background: #0d6b35;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #128a43;
            padding: 15px;
            margin: 25px 0;
            font-size: 14px;
        }

        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            font-size: 14px;
        }

        .security-notice {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 25px 0;
            font-size: 13px;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .footer-logo {
            margin-bottom: 15px;
        }

        .divider {
            border-top: 1px solid #e9ecef;
            margin: 30px 0;
        }

        .alternative-link {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-size: 12px;
            word-break: break-all;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ eAlloc System</h1>
            <p>Government Budget Management Platform</p>
        </div>

        <div class="content">
            <div class="greeting">Welcome, {{ $user->name }}!</div>

            <div class="message">
                <p>You have been granted access to the <strong>eAlloc Budget Management System</strong> by your system
                    administrator.</p>

                <p>To begin using the platform, you need to activate your account and set your secure password.</p>
            </div>

            <div class="info-box">
                <strong>📧 Your Email:</strong> {{ $user->email }}<br>
                <strong>👤 Assigned Role:</strong> {{ ucwords(str_replace('-', ' ', $user->role)) }}<br>
                <strong>🏢 Organization:</strong> {{ config('app.name') }}
            </div>

            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ $activationUrl }}" class="cta-button">
                    🔐 Activate Account & Set Password
                </a>
            </div>

            <div class="warning-box">
                <strong>⏰ Important:</strong> This activation link will expire in <strong>24 hours</strong>
                ({{ $expiresAt }}).
            </div>

            <div class="alternative-link">
                <strong>Alternative:</strong> If the button doesn't work, copy and paste this link into your
                browser:<br>
                <span style="color: #128a43;">{{ $activationUrl }}</span>
            </div>

            <div class="divider"></div>

            <h3 style="color: #128a43; font-size: 16px; margin-bottom: 15px;">What happens next?</h3>
            <ol style="padding-left: 20px; line-height: 1.8;">
                <li>Click the activation link above</li>
                <li>Set a strong, secure password</li>
                <li>Your account will be activated automatically</li>
                <li>Log in to eAlloc and start managing budgets</li>
            </ol>

            <div class="security-notice">
                <strong>🔒 Security Notice:</strong><br>
                • Never share your password with anyone<br>
                • eAlloc administrators will never ask for your password<br>
                • If you didn't request this account, please contact support immediately<br>
                • Each activation link can only be used once
            </div>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #666;">
                <strong>Need help?</strong><br>
                Contact your system administrator or email
                <a href="mailto:support@ealloc.gov.ph" style="color: #128a43;">support@ealloc.gov.ph</a>
            </p>
        </div>

        <div class="footer">
            <div class="footer-logo">
                <strong style="font-size: 14px; color: #128a43;">eAlloc System</strong>
            </div>
            <p style="margin: 5px 0;">Government Budget Management Platform</p>
            <p style="margin: 15px 0 5px;">
                Promoting transparency and accountability in public fund management
            </p>
            <p style="margin: 5px 0; color: #999;">
                © {{ date('Y') }} eAlloc. All rights reserved.
            </p>
            <p style="margin: 15px 0 0; font-size: 11px; color: #999;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>

</html>
