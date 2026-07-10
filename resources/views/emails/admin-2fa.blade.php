<!DOCTYPE html>
<html>
<head>
    <title>Admin 2FA Verification</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: #3b82f6; padding: 20px; text-align: center;">
            <h1 style="color: white;">🔐 Admin 2FA Verification</h1>
        </div>
        
        <div style="background: #f3f4f6; padding: 20px;">
            <p>Hello <strong>{{ $user->name ?? $user->email }}</strong>,</p>
            <p>Your verification code is:</p>
            <div style="background: white; padding: 15px; text-align: center; font-size: 32px; letter-spacing: 5px; font-weight: bold;">
                {{ $code }}
            </div>
            <p>This code will expire in 5 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>
        
        <div style="text-align: center; font-size: 12px; color: #666;">
            <p>© {{ date('Y') }} Clearance System</p>
        </div>
    </div>
</body>
</html>