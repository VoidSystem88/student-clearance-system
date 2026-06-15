<!DOCTYPE html>
<html>
<head>
    <title>New Feedback Submitted</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #8b5cf6;">📝 New Feedback Received</h2>
        <p>Dear Admin,</p>
        <p>A new feedback has been submitted by <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>.</p>
        
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 15px 0;">
            <p><strong>Rating:</strong> ⭐{{ $feedback->rating }}/5</p>
            <p><strong>Category:</strong> {{ ucfirst($feedback->category) }}</p>
            <p><strong>Message:</strong> {{ $feedback->message }}</p>
        </div>
        
        <a href="{{ url('/admin/feedbacks') }}" style="background: #8b5cf6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">View Feedback</a>
        
        <p style="margin-top: 20px;">Login to the admin panel to respond.</p>
    </div>
</body>
</html>