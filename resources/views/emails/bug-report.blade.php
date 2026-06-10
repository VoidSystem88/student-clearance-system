<!DOCTYPE html>
<html>
<head>
    <title>New Bug Report</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>🔍 New Bug/Issue Report</h2>
    <p><strong>Type:</strong> {{ ucfirst(str_replace('_', ' ', $type)) }}</p>
    <p><strong>Name:</strong> {{ $report->name ?? 'Not provided' }}</p>
    <p><strong>Email:</strong> {{ $report->email }}</p>
    <p><strong>Student ID:</strong> {{ $report->student_id ?? 'Not provided' }}</p>
    <p><strong>Message:</strong></p>
    <p style="background: #f4f4f4; padding: 10px;">{{ $report->message }}</p>
    <p><strong>Browser:</strong> {{ $report->browser_info }}</p>
    <p><strong>URL:</strong> {{ $report->url }}</p>
    <hr>
    <p>Login to admin panel to respond: {{ url('/admin/bug-reports') }}</p>
</body>
</html>