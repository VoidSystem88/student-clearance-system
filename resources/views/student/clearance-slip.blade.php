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
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: white;
            padding: 40px 30px;
            position: relative;
            min-height: 100vh;
        }
        /* Malaking Watermark Logo sa Likod */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.08;
            z-index: 0;
            text-align: center;
            width: 100%;
            pointer-events: none;
        }
        .watermark img {
            width: 450px;
            height: auto;
            max-width: 80%;
        }
        /* Main Content - nasa taas ng watermark */
        .content {
            position: relative;
            z-index: 1;
        }
        /* Clean Header - walang label, pong may border */
        .header {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 15px;
            border-bottom: 3px solid #1a56db;
        }
        .header h1 {
            font-size: 28px;
            letter-spacing: 2px;
            color: #1e3a8a;
            font-weight: 700;
            margin: 0;
        }
        /* Info Cards */
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }
        .info-card {
            flex: 1;
            min-width: 200px;
            background: #f8fafc;
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #1a56db;
        }
        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }
        /* Section Titles */
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #8e1616;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        /* Tables */
        .dept-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 13px;
        }
        .dept-table th {
            background-color: #f1f5f9;
            text-align: left;
            padding: 12px 12px;
            font-weight: 600;
            color: #334155;
            border-bottom: 2px solid #e2e8f0;
        }
        .dept-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }
        .dept-table tr:hover td {
            background-color: #f8fafc;
        }
        /* QR Code */
        .qr-section {
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background: #f8fafc;
            border-radius: 16px;
        }
        .qr-section img {
            width: 140px;
            height: 140px;
            margin-bottom: 8px;
        }
        .qr-label {
            font-size: 11px;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <!-- Malaking Watermark Logo sa Likod -->
    <div class="watermark">
        <img src="data:image/png;base64,{{ $logoBase64 ?? '' }}" alt="TCC Logo">
    </div>

    <div class="content">
        <!-- Header - Clean, walang label -->
        <div class="header">
            <h1>CLEARANCE SLIP</h1>
        </div>

        <!-- Student Information as Cards -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">ACCOUNT ID</div>
                <div class="info-value">{{ $student->account_id }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">STUDENT ID</div>
                <div class="info-value">{{ $student->student_id }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">FULL NAME</div>
                <div class="info-value">{{ $student->first_name }} {{ $student->last_name }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">COURSE</div>
                <div class="info-value">{{ $student->course }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">EMAIL</div>
                <div class="info-value">{{ $student->email }}</div>
            </div>
            <div class="info-card">
                <div class="info-label">DATE CLEARED</div>
                <div class="info-value">{{ now()->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Departments Cleared -->
        <div class="section-title">Departments Cleared</div>
        <table class="dept-table">
            <thead>
                <tr>
                    <th>Department</th>
                    <th>Date Approved</th>
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

        @if(isset($qrCodeBase64) && $qrCodeBase64)
        <div class="qr-section">
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
            <div class="qr-label">Scan to verify authenticity</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Electronically generated clearance slip • Valid without signature</p>
            <p>System Generated • {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
</body>
</html>