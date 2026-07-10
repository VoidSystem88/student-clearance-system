<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Feedback Response - Void Clearance System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .feedback-box { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #8b5cf6; }
        .response-box { background: #e8f4f8; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #10b981; }
        .button { display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .status-approved { color: #10b981; }
        .status-rejected { color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Feedback Response</h1>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>
            
            <p>Thank you for your feedback. The support team has reviewed and responded to your concern.</p>
            
            <div class="feedback-box">
                <strong>📋 Your Feedback:</strong><br>
                <p style="margin-top: 8px;">{{ $feedback->message }}</p>
                <small>Rating: ⭐{{ $feedback->rating }}/5 | Category: {{ ucfirst($feedback->category) }}</small>
            </div>
            
            <div class="response-box">
                <strong>💬 Admin Response:</strong><br>
                <p style="margin-top: 8px;">{{ $admin_response }}</p>
            </div>
            
            <div class="status-{{ $feedback->status == 'reviewed' ? 'approved' : 'rejected' }}">
                <strong>Status:</strong> {{ ucfirst($feedback->status) }}
            </div>
            
            <p style="margin-top: 20px;">You can view the full conversation in your Feedback page.</p>
            
            <div style="text-align: center;">
                <a href="{{ url('/student/feedback') }}" class="button">View Feedback</a>
            </div>
        </div>
        <div class="footer">
            <p>Void Clearance System - Student Portal</p>
            <p>© {{ date('Y') }} All rights reserved.</p>
        </div>
    </div>
</body>
</html>