<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Student Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
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
        .student-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .student-details p {
            margin: 8px 0;
        }
        .label {
            font-weight: bold;
            color: #333;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎓 New Student Registered</h1>
        </div>
        
        <div class="content">
            <p>Dear Admin,</p>
            <p>A new student has registered on the platform. Here are the details:</p>
            
            <div class="student-details">
                <p><span class="label">Full Name:</span> {{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</p>
                <p><span class="label">Student ID:</span> {{ $student->student_id ?? 'N/A' }}</p>
                <p><span class="label">Email:</span> {{ $student->email ?? 'N/A' }}</p>
                <p><span class="label">Course:</span> {{ $student->course ?? 'N/A' }}</p>
                <p><span class="label">Year Level:</span> {{ $student->year_level ?? 'N/A' }}</p>
                <p><span class="label">Registered:</span> {{ now()->format('F d, Y h:i A') }}</p>
            </div>
            
            <p>Please log in to the admin panel to view more details and manage student accounts.</p>
            
            <div style="text-align: center;">
                <a href="https://tccstudentclearance.gt.tc/admin/students" class="button">View All Students</a>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from the Clearance System.</p>
            <p>© {{ date('Y') }} TCC Student Clearance System</p>
        </div>
    </div>
</body>
</html>