<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Void Clearance System</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 550px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #1e3c72, #2b4c8a);
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .otp-code {
            background: #f0f4f8;
            padding: 20px;
            text-align: center;
            border-radius: 12px;
            margin: 20px 0;
        }
        .otp-digits {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #2b4c8a;
            font-family: monospace;
        }
        .btn {
            display: inline-block;
            background: #2b4c8a;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
        }
        .footer {
            background: #f0f4f8;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .warning {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <div class="container">
            <div class="header">
                <h1>📧 Email Verification</h1>
                <p style="color: #c8d6e5; margin: 5px 0 0;">Void Clearance System - TCC</p>
            </div>
            
            <div class="content">
                <h2 style="color: #2c3e50; margin-top: 0;">Hello, <?php echo e($name); ?>!</h2>
                <p>Thank you for registering with the <strong>Student Clearance System</strong>.</p>
                <p>To complete your registration, please use the verification code below:</p>
                
                <div class="otp-code">
                    <div class="otp-digits"><?php echo e($otp); ?></div>
                    <p style="margin: 10px 0 0; color: #666;">This code will expire in <strong>10 minutes</strong>.</p>
                </div>
                
                <p>If you did not request this verification, please ignore this email.</p>
                
                <hr style="margin: 25px 0; border: none; border-top: 1px solid #e0e0e0;">
                
                <p style="font-size: 12px; color: #888;">For any concerns, please contact the system administrator at <strong>voidsystem88@gmail.com</strong></p>
            </div>
            
            <div class="footer">
                <p>&copy; <?php echo e(date('Y')); ?> Void Clearance System - Taguig City College. All rights reserved.</p>
                <p>This is an automated message, please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/emails/verification-otp.blade.php ENDPATH**/ ?>