<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 text-center">
            @if($mode === 'full')
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-skull-crosswalk text-red-600 text-5xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-red-600 mb-2">System Offline</h1>
            @else
                <div class="w-24 h-24 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tools text-yellow-600 text-5xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-yellow-600 mb-2">Under Maintenance</h1>
            @endif
            
            <p class="text-gray-600 mb-6">{{ $message }}</p>
            
            @if(isset($end_time) && $end_time)
                <div class="bg-gray-100 rounded-lg p-3 mb-6">
                    <p class="text-sm text-gray-500">Expected to be back:</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($end_time)->format('F d, Y g:i A') }}</p>
                </div>
            @endif
            
            <div class="border-t pt-4">
                <p class="text-xs text-gray-400">Clearance System Support Team</p>
                <p class="text-xs text-gray-400 mt-1">
                    <i class="fas fa-envelope mr-1"></i> support@clearance.com
                </p>
            </div>
            
            <button onclick="location.reload()" class="mt-4 text-blue-600 text-sm hover:underline">
                <i class="fas fa-sync-alt mr-1"></i> Check Status
            </button>
        </div>
    </div>
</body>
</html>