<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Clearance Slip</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: white;
            padding: 20px;
            position: relative;
            font-size: 12px;
        }

        .logo{
            position: fixed;
            top: 45px;
            left: 510px;
            transform: translate(-50%, -50%);
            opacity: 1;
            z-index: 0;
            text-align: center;
            
        }
        .logo img {
            width: 45px;
            height: auto;
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 0;
            text-align: center;
        }
        .watermark img {
            width: 400px;
            height: auto;
        }
        .content {
            position: relative;
            z-index: 1;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
        }
        .header p {
            font-size: 11px;
            margin-top: 5px;
        }
        .student-info {
            margin-bottom: 20px;
            font-size: 11px;
            border: 1px solid #000;
            padding: 10px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 3px 5px;
        }
        .dept-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        .dept-table th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .dept-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            width: 120px;
            height: 120px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .signature {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-left {
            text-align: center;
            width: 30%;
        }
        .signature-right {
            text-align: center;
            width: 30%;
            margin-left: auto;
        }
        .signature-left hr, .signature-right hr {
            width: 100%;
            margin-bottom: 5px;
        }
        
        .qr-fallback {
            padding: 8px;
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 4px;
            display: inline-block;
        }
        .qr-fallback p {
            font-size: 9px;
            word-break: break-all;
            color: #666;
        }
    </style>
</head>
<body>

<div class="content">
    <!-- Header -->
    <div class="header">
        <h1>CLEARANCE SLIP</h1>
        <p>Void Student Clearance</p>
        <p>{{ now()->format('F d, Y') }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td width="20%"><strong>Student Name:</strong></td>
                <td width="30%">{{ $student->first_name }} {{ $student->last_name }}</td>
                <td width="20%"><strong>Student ID:</strong></td>
                <td width="30%">{{ $student->student_id }}</td>
            </tr>
            <tr>
                <td><strong>Course:</strong></td>
                <td>{{ $student->course }}</td>
                <td><strong>Account ID:</strong></td>
                <td>{{ $student->account_id }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td colspan="3">{{ $student->email }}</td>
            </tr>
        </table>
    </div>

    <!-- Departments Cleared Table -->
    <table class="dept-table">
        <thead>
            <tr>
                <th width="60%">Department</th>
                <th width="40%">Date Approved</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clearanceRequests as $request)
            <tr>
                <td>{{ $request->department->name }}</td>
                <td>{{ $request->processed_at ? $request->processed_at->format('F d, Y') : $request->updated_at->format('F d, Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- QR Code Section -->
    <div class="qr-code">
        @if(isset($qrCodeBase64) && $qrCodeBase64 && !empty($qrCodeBase64))
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
            <p style="font-size: 9px; margin-top: 5px;">Scan QR code to verify clearance</p>
        @else
            <div class="qr-fallback">
                <p><strong>Verification Code:</strong></p>
                <p>{{ $student->clearance_token ?? 'N/A' }}</p>
                <p style="font-size: 8px; margin-top: 5px;">Present this code to verify your clearance</p>
            </div>
        @endif
    </div>

    <!-- Signatures -->
    <div class="signature">
        <div class="signature-left">
            <hr>
            <p>Student Signature</p>
        </div>
        <div class="signature-right">
            <hr>
            <p>Registrar's Signature</p>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>This clearance slip is electronically generated and valid without signature.</p>
        <p>Generated on: {{ now()->format('F d, Y h:i A') }}</p>
    </div>
</div>

</body>
</html>