<!DOCTYPE html>
<html>
<head>
    <title>New Report Received</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #16a34a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 20px; border-radius: 0 0 8px 8px; }
        .info { background: white; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .footer { text-align: center; font-size: 12px; color: #6b7280; margin-top: 20px; }
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>📋 New Report Received</h2>
        </div>
        <div class="content">
            <p>Dear <strong>{{ $staff->name ?? 'Staff' }}</strong>,</p>
            
            <p>A new verified students report has been sent to your department.</p>
            
            <div class="info">
                <p><strong>📌 Report:</strong> {{ $report_title }}</p>
                <p><strong>🏢 Department:</strong> {{ $department->name ?? 'N/A' }}</p>
                <p><strong>👤 Sent by:</strong> {{ $officer->name ?? 'Officer' }}</p>
                <p><strong>📅 Event:</strong> {{ $event_name }}</p>
                <p><strong>👥 Total Students:</strong> <span class="badge">{{ $total_students }}</span></p>
                @if($notes)
                    <p><strong>📝 Notes:</strong> {{ $notes }}</p>
                @endif
            </div>
            
            <p>Please log in to the system to view the full report.</p>
            
            <p style="margin-top: 20px;">
                <a href="{{ url('/staff/reports/' . $report_id) }}" 
                   style="background: #16a34a; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    View Report →
                </a>
            </p>
        </div>
        <div class="footer">
            <p>This is an automated notification from the Clearance System.</p>
            <p>&copy; {{ date('Y') }} Clearance System</p>
        </div>
    </div>
</body>
</html>