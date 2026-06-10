<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Verification — Void Clearance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #0f172a; }
        .card { background: #1e293b; }
        .badge-valid { background: linear-gradient(135deg, #22c55e, #16a34a); }
        .badge-invalid { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .dept-row:nth-child(even) { background: #0f172a30; }
        @keyframes pulse-green {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.4); }
            50% { box-shadow: 0 0 0 12px rgba(34,197,94,0); }
        }
        .pulse { animation: pulse-green 2s infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg">

        @if($valid)
        {{-- ✅ VALID --}}
        <div class="card rounded-2xl overflow-hidden shadow-2xl">

            {{-- Header --}}
            <div class="badge-valid px-6 py-5 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3 pulse">
                    <i class="fas fa-shield-check text-white text-3xl"></i>
                </div>
                <h1 class="text-white text-2xl font-bold">Clearance Verified</h1>
                <p class="text-green-100 text-sm mt-1">This document is authentic and valid</p>
            </div>

            {{-- Student Info --}}
            <div class="px-6 py-5 border-b border-slate-700">
                <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">Student Information</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Full Name</span>
                        <span class="text-white font-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Student ID</span>
                        <span class="text-white font-mono">{{ $student->student_id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Course</span>
                        <span class="text-white">{{ $student->course ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Year Level</span>
                        <span class="text-white">{{ $student->year_level ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400 text-sm">Date Cleared</span>
                        <span class="text-green-400 font-semibold">
                            {{ $student->cleared_at ? \Carbon\Carbon::parse($student->cleared_at)->format('F d, Y') : now()->format('F d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Approved Departments --}}
            <div class="px-6 py-5 border-b border-slate-700">
                <h2 class="text-slate-400 text-xs font-semibold uppercase tracking-wider mb-3">
                    Cleared Departments
                    <span class="ml-2 bg-green-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $requests->count() }}</span>
                </h2>
                <div class="rounded-xl overflow-hidden border border-slate-700">
                    @foreach($requests as $req)
                    <div class="dept-row flex justify-between items-center px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 bg-green-500 bg-opacity-20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-400 text-xs"></i>
                            </div>
                            <span class="text-white text-sm font-medium">{{ optional($req->department)->name ?? 'N/A' }}</span>
                        </div>
                        <span class="text-slate-400 text-xs">
                            {{ $req->processed_at ? \Carbon\Carbon::parse($req->processed_at)->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-slate-400 text-xs">Verified by Void Clearance System</span>
                </div>
                <span class="text-slate-500 text-xs">{{ now()->format('M d, Y h:i A') }}</span>
            </div>

        </div>

        @else
        {{-- ❌ INVALID --}}
        <div class="card rounded-2xl overflow-hidden shadow-2xl">
            <div class="badge-invalid px-6 py-8 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-times-circle text-white text-3xl"></i>
                </div>
                <h1 class="text-white text-2xl font-bold">Invalid Document</h1>
                <p class="text-red-100 text-sm mt-1">This clearance document could not be verified</p>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-slate-400 text-sm">The QR code you scanned does not match any valid clearance record in our system. The document may be fake, expired, or tampered with.</p>
                <div class="mt-4 p-3 bg-red-900 bg-opacity-30 rounded-xl border border-red-800">
                    <p class="text-red-400 text-xs"><i class="fas fa-exclamation-triangle mr-1"></i> If you believe this is an error, please contact the registrar's office.</p>
                </div>
            </div>
        </div>
        @endif

        <p class="text-center text-slate-600 text-xs mt-4">
            &copy; {{ date('Y') }} Void Clearance System
        </p>
    </div>

</body>
</html>