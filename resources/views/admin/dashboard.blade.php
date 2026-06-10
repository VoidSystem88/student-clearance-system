@extends('layouts.admin')

@section('title', 'Dashboard - Admin')
@section('header', 'Admin Dashboard')

@section('content')
<!-- Stats Cards - 3 per row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <!-- Total Students -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-blue-500 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalStudents ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">Registered accounts</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-500"></i>
            </div>
        </div>
    </div>

    <!-- Cleared Students -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Cleared Students</p>
                <p class="text-2xl font-bold text-green-600">{{ $clearedStudents ?? 0 }}</p>
                @php
                    $clearanceRate = ($totalStudents ?? 0) > 0 ? round((($clearedStudents ?? 0) / ($totalStudents ?? 0)) * 100, 1) : 0;
                @endphp
                <p class="text-xs text-gray-400 mt-1">{{ $clearanceRate }}% completion rate</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
        </div>
    </div>

    <!-- Departments -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-purple-500 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Departments</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalDepartments ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">Active departments</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- Total Requests -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-orange-500 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Requests</p>
                <p class="text-2xl font-bold text-gray-800">{{ $totalRequests ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">Clearance submissions</p>
            </div>
            <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-orange-500"></i>
            </div>
        </div>
    </div>

    <!-- Approved Requests -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-green-500 hover:shadow-md transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Approved Requests</p>
                <p class="text-2xl font-bold text-green-600">{{ $approvedRequests ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">Cleared applications</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-thumbs-up text-green-500"></i>
            </div>
        </div>
    </div>

    <!-- Rejected Requests -->
    <div class="bg-white rounded-xl shadow-sm p-4 border-l-4 border-red-500 hover:shadow-md transition">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-gray-500 text-sm">Rejected Requests</p>
                <p class="text-2xl font-bold text-red-600">{{ $rejectedRequests ?? 0 }}</p>
                <p class="text-xs text-gray-400 mt-1">Returned applications</p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-500"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============ SECRET IP LOGS BUTTON ============ -->


<!-- Secret IP Logs Modal -->
<div id="secretIpModal" class="fixed inset-0 bg-black bg-opacity-95 hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-xl max-w-5xl w-full max-h-[85vh] overflow-hidden border border-gray-700 shadow-2xl">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700 bg-gray-800">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-network-wired text-blue-400"></i>
                Secret IP Logs
                <span class="text-xs text-gray-400 bg-gray-700 px-2 py-1 rounded-full ml-2">Admin Only </span>
                           
                            <div class="flex flex-wrap gap-3 mb-6">
    <a href="{{ url('/admin/ip-logs') }}" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition shadow-sm">
        <i class="fas fa-network-wired"></i> 
        IP Logs
    </a>
    <a href="{{ url('/admin/visitor-tracking') }}" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition shadow-sm">
        <i class="fas fa-chart-line"></i> 
        Visitor Tracking
    </a>
    <a href="{{ url('/admin/sync-tracking-logs') }}" 
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition shadow-sm">
        <i class="fas fa-sync-alt"></i> 
        Sync Logs
    </a>
</div>
            </h3>
            <button onclick="closeSecretIpModal()" class="text-gray-400 hover:text-white text-2xl transition">&times;</button>
        </div>
        <div class="p-4 overflow-auto" style="max-height: calc(85vh - 70px);">
            <div id="secretIpContent" class="text-green-400 font-mono text-sm">
                <div class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-400"></i>
                    <p class="mt-3 text-gray-400">Loading IP logs...</p>
                </div>
            </div>
            <div class="mt-4 flex gap-3 justify-end border-t border-gray-700 pt-4">
                <button onclick="refreshSecretIpLogs()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <a href="{{ url('/admin/download-ips') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-download"></i> Download CSV
                </a>
                <button onclick="closeSecretIpModal()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cleared Students List Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-white">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-user-graduate text-green-600"></i>
                    Cleared Students
                </h3>
                <p class="text-sm text-gray-500 mt-1">Students who have completed all clearance requirements</p>
            </div>
            <a href="{{ route('admin.students') }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center gap-1">
                View All <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $clearedStudentsList = $students->where('is_cleared', true);
                @endphp
                
                @forelse($clearedStudentsList as $student)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <span class="text-sm font-mono text-gray-700">{{ $student->student_id }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-check text-green-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</p>
                                <p class="text-xs text-gray-400">ID: {{ $student->account_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-sm text-gray-600">{{ $student->course ?? 'N/A' }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-sm text-gray-600">{{ $student->year_level ?? 'N/A' }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="text-sm text-gray-500">{{ $student->email }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1 text-xs"></i> Cleared
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-user-graduate text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-400 font-medium">No cleared students yet</p>
                            <p class="text-xs text-gray-300 mt-1">Students will appear here once they complete all requirements</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($clearedStudentsList->count() > 0)
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
        <div class="flex justify-between items-center text-xs text-gray-500">
            <span>Showing {{ $clearedStudentsList->count() }} of {{ $clearedStudents }} cleared students</span>
            <i class="fas fa-shield-alt"></i>
        </div>
    </div>
    @endif
</div>

<script>
// ============ SECRET IP LOGS FUNCTION ============
let secretClickCount = 0;
let secretClickTimer = null;
let konamiSequence = [];
const konamiCode = ['1', '2', '7', '9', '4', '4', '1', '0', '0', '0', '5', '4'];
// Method 1: Click 5 times on the secret button
document.getElementById('secretIpBtn')?.addEventListener('click', function() {
    secretClickCount++;
    
    // Visual feedback
    this.style.transform = 'scale(0.9)';
    setTimeout(() => {
        this.style.transform = '';
    }, 150);
    
    if (secretClickTimer) clearTimeout(secretClickTimer);
    
    if (secretClickCount >= 5) {
        openSecretIpModal();
        secretClickCount = 0;
    }
    
    secretClickTimer = setTimeout(() => {
        secretClickCount = 0;
    }, 2000);
});

// Method 2: Konami Code (↑ ↑ ↓ ↓ ← → ← → B A)
document.addEventListener('keydown', function(e) {
    konamiSequence.push(e.key);
    if (konamiSequence.length > konamiCode.length) konamiSequence.shift();
    
    if (JSON.stringify(konamiSequence) === JSON.stringify(konamiCode)) {
        openSecretIpModal();
        konamiSequence = [];
    }
});

function openSecretIpModal() {
    const modal = document.getElementById('secretIpModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    loadSecretIpLogs();
    document.body.style.overflow = 'hidden';
}

function closeSecretIpModal() {
    const modal = document.getElementById('secretIpModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function loadSecretIpLogs() {
    const content = document.getElementById('secretIpContent');
    content.innerHTML = '<div class="text-center py-12"><i class="fas fa-spinner fa-spin text-3xl text-blue-400"></i><p class="mt-3 text-gray-400">Loading IP logs...</p></div>';
    
    fetch('{{ url("/admin/ip-logs") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const preElements = doc.querySelectorAll('pre');
        
        if (preElements.length > 0) {
            let logsHtml = '';
            preElements.forEach(pre => {
                logsHtml += pre.outerHTML;
            });
            content.innerHTML = logsHtml;
        } else {
            content.innerHTML = '<div class="text-yellow-400 text-center py-12"><i class="fas fa-inbox text-4xl mb-3"></i><p>No IP logs yet.</p><p class="text-sm mt-2">Visit <code class="bg-gray-800 px-2 py-1 rounded">/my-ip</code> to generate logs.</p></div>';
        }
    })
    .catch(err => {
        content.innerHTML = '<div class="text-red-400 text-center py-12"><i class="fas fa-exclamation-triangle text-4xl mb-3"></i><p>Error loading logs: ' + err.message + '</p></div>';
    });
}

function refreshSecretIpLogs() {
    loadSecretIpLogs();
}
</script>

<style>
    /* Pulse animation for secret button indicator */
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }
</style>
@endsection