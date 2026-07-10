<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Clearance Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: {{ $status === 'approved' ? '#22c55e' : '#ef4444' }};
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            background: {{ $status === 'approved' ? '#22c55e' : '#ef4444' }};
            color: white;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .footer {
            background: #f3f4f6;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }
        .info-box {
            background: #f3f4f6;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Clearance Status Update</h1>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>
            
            <p>Your clearance request for <strong>{{ $department }}</strong> has been:</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <span class="status-badge">
                    {{ $status === 'approved' ? '✅ APPROVED' : '❌ REJECTED' }}
                </span>
            </div>
            
            @if($status === 'rejected' && $remarks)
            <div class="info-box">
                <strong>📝 Reason for rejection:</strong><br>
                {{ $remarks }}
            </div>
            @endif
            
            <div class="info-box">
                <strong>📋 Summary:</strong><br>
                • Department: {{ $department }}<br>
                • Status: {{ ucfirst($status) }}<br>
                • Date: {{ now()->format('F d, Y h:i A') }}
            </div>
            
            <p>Please log in to the system to view more details about your clearance status.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/login') }}" class="button">Login to System</a>
            </div>
            
            <p style="margin-top: 15px; font-size: 13px; color: #6b7280;">
                ⚠️ This is an automated notification. Please do not reply to this email.
            </p>
        </div>
        
        <div class="footer">
            <p>Student Clearance Tracking and Management System</p>
            <p>© {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</body>
</html>