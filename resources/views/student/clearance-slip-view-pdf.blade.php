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
            background: #e5e7eb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Courier New', Courier, monospace;
        }
        
        /* BOND PAPER SIZE - Fixed width */
        .clearance-slip {
            width: 8.5in;
            min-height: 11in;
            background: white;
            margin: 0 auto;
            padding: 0.5in 0.5in;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-size: 11pt;
        }
        
        @media screen {
            .clearance-slip {
                width: 100%;
                max-width: 8.5in;
                margin: 0 auto;
            }
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            .clearance-slip {
                width: 100%;
                padding: 0.5in;
                box-shadow: none;
            }
            .no-print {
                display: none !important;
            }
        }
        
        .content {
            position: relative;
            z-index: 1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 0;
        }
        .header p {
            font-size: 10pt;
            margin-top: 5px;
        }
        
        .student-info {
            margin-bottom: 20px;
            font-size: 10pt;
            border: 1px solid #000;
            padding: 10px;
        }
        .student-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .student-info td {
            padding: 5px 8px;
        }
        
        .dept-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
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
            width: 100px;
            height: 100px;
        }
        
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-left, .signature-right {
            text-align: center;
            width: 40%;
        }
        .signature-left hr, .signature-right hr {
            width: 100%;
            margin-bottom: 5px;
            border: none;
            border-top: 1px solid #000;
        }
        
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .print-btn-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12pt;
            font-family: Arial, sans-serif;
        }
        .print-btn:hover {
            background: #2563eb;
        }
        .back-btn {
            margin-left: 10px;
            background: #6b7280;
            color: white;
            text-decoration: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-size: 12pt;
            display: inline-block;
        }
        .back-btn:hover {
            background: #4b5563;
        }
        
        /* QR Code Debug - ipakita ang URL kung walang QR */
        .qr-fallback {
            padding: 8px;
            background: #f8f9fa;
            border: 1px dashed #ccc;
            border-radius: 4px;
            display: inline-block;
            max-width: 250px;
        }
        .qr-fallback p {
            font-size: 8pt;
            word-break: break-all;
            color: #666;
        }
    </style>
</head>
<body>

<div class="no-print print-btn-container">
    <button onclick="window.print()" class="print-btn">
        🖨️ Print / Save as PDF
    </button>
    <a href="{{ route('student.clearance') }}" class="back-btn">
        ← Back
    </a>
</div>

<div class="clearance-slip">
    <div class="content">
        <!-- Header -->
        <div class="header">
            <h1>CLEARANCE SLIP</h1>
            <p>Void Student Clearance System</p>
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

        <!-- QR Code - Professional Version (walang "FULLY CLEARED" text) -->
        <div class="qr-code">
            @if(isset($qrCodeBase64) && $qrCodeBase64 && !empty($qrCodeBase64))
                <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Verification QR Code">
                <p style="font-size: 8pt; margin-top: 5px; color: #555;">Scan to verify authenticity</p>
            @else
                <div class="qr-fallback">
                    <p><strong>Verification Code:</strong></p>
                    <p>{{ $student->clearance_token ?? 'N/A' }}</p>
                    <p style="font-size: 7pt; margin-top: 5px;">Present this code to verify your clearance</p>
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
</div>

</body>
</html>