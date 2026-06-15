@extends('layouts.officer')

@section('title', 'Officer Dashboard')
@section('header', 'Officer Dashboard')

@section('content')
<style>
    /* Dark mode styles para sa buong page na ito */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .bg-gray-50,
    body.dark-mode .bg-gray-100 {
        background-color: #1f2937 !important;
    }
    body.dark-mode .bg-blue-50 {
        background-color: #1e3a5f !important;
    }
    body.dark-mode .bg-green-50 {
        background-color: #064e3b !important;
    }
    body.dark-mode .bg-yellow-50 {
        background-color: #713f12 !important;
    }
    body.dark-mode .bg-red-50 {
        background-color: #7f1d1d !important;
    }
    body.dark-mode .bg-purple-50 {
        background-color: #4c1d95 !important;
    }
    body.dark-mode .text-gray-500,
    body.dark-mode .text-gray-600,
    body.dark-mode .text-gray-700,
    body.dark-mode .text-gray-800 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-400 {
        color: #9ca3af !important;
    }
    body.dark-mode .border-gray-100,
    body.dark-mode .border-gray-200,
    body.dark-mode .border-gray-300 {
        border-color: #374151 !important;
    }
    body.dark-mode .divide-gray-100 > div {
        border-color: #374151 !important;
    }
    body.dark-mode .student-list-item {
        border-bottom-color: #374151 !important;
    }
    body.dark-mode .verified-list-item {
        border-bottom-color: #374151 !important;
    }
    body.dark-mode .mobile-tab {
        background-color: #374151 !important;
        color: #e5e7eb !important;
    }
    body.dark-mode .mobile-tab.active {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    body.dark-mode .student-panel {
        background-color: #1f2937 !important;
        border-left-color: #374151 !important;
    }
    body.dark-mode .slide-arrow {
        background-color: #3b82f6 !important;
    }
    body.dark-mode .student-name {
        color: #e5e7eb !important;
    }
    body.dark-mode .student-detail {
        color: #9ca3af !important;
    }
    body.dark-mode .remove-btn {
        color: #ef4444 !important;
    }
    body.dark-mode .remove-btn:hover {
        color: #f87171 !important;
    }
    
    /* Export button style */
    .export-btn {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        transition: all 0.3s ease;
    }
    .export-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    }
    body.dark-mode .export-btn {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
    }
    
    /* Mobile Slide-out Panel Styles */
    .student-panel {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        max-width: 400px;
        height: 100%;
        background: white;
        box-shadow: -5px 0 30px rgba(0,0,0,0.3);
        z-index: 1000;
        transition: right 0.3s ease-in-out;
        overflow-y: auto;
        border-left: 1px solid #e5e7eb;
    }
    
    .student-panel.open {
        right: 0;
    }
    
    .panel-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 999;
        display: none;
    }
    
    .panel-overlay.show {
        display: block;
    }
    
    .slide-arrow {
        position: fixed;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: #3b82f6;
        color: white;
        width: 36px;
        height: 70px;
        border-radius: 20px 0 0 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 998;
        box-shadow: -2px 2px 8px rgba(0,0,0,0.15);
        transition: all 0.2s;
    }
    
    .slide-arrow:hover {
        background: #2563eb;
        width: 42px;
    }
    
    .slide-arrow i {
        font-size: 18px;
    }
    
    /* Hide arrow on desktop */
    @media (min-width: 768px) {
        .slide-arrow, .panel-overlay {
            display: none !important;
        }
        .desktop-layout {
            display: block;
        }
        .mobile-layout {
            display: none;
        }
    }
    
    @media (max-width: 767px) {
        .desktop-layout {
            display: none;
        }
        .mobile-layout {
            display: block;
        }
        
        /* Tabs for mobile */
        .mobile-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 16px;
        }
        .mobile-tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .mobile-tab.active {
            background: #3b82f6;
            color: white;
        }
        
        /* List items */
        .student-list-item {
            padding: 14px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .student-info {
            flex: 1;
        }
        .student-name {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
        }
        .student-detail {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px;
        }
        .verify-btn-sm {
            padding: 6px 14px;
            font-size: 12px;
            border-radius: 20px;
        }
        
        /* Verified list item */
        .verified-list-item {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .remove-btn {
            color: #ef4444;
            font-size: 13px;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        /* Hide tabs content */
        .mobile-tab-content {
            display: none;
        }
        .mobile-tab-content.active {
            display: block;
        }
        
        /* Export button sa mobile */
        .export-mobile-btn {
            margin-bottom: 16px;
        }
    }
</style>

<!-- Desktop Layout -->
<div class="desktop-layout">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Students</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $students->count() ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Verified</p>
                    <p class="text-2xl font-bold text-green-600">{{ $verifiedCount ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Not Verified</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ ($students->count() ?? 0) - ($verifiedCount ?? 0) }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- EXPORT BUTTON - Desktop -->
    <div class="mb-6 flex justify-end">
        <button onclick="exportVerifiedToCSV()" class="export-btn text-white px-5 py-2.5 rounded-lg shadow-md flex items-center gap-2">
            <i class="fas fa-file-csv text-lg"></i>
            <span>Export Verified to CSV</span>
        </button>
    </div>

    <!-- Tabs Navigation Desktop -->
    <div class="mb-4">
        <div class="border-b border-gray-200">
            <nav class="flex gap-2 flex-wrap" aria-label="Tabs">
                <button onclick="switchTab('all')" id="tabAllBtn" class="tab-btn active px-5 py-2.5 text-sm font-medium rounded-t-lg bg-blue-600 text-white transition">
                    <i class="fas fa-users mr-2"></i> All Students ({{ $students->count() ?? 0 }})
                </button>
                <button onclick="switchTab('verified')" id="tabVerifiedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                    <i class="fas fa-check-double mr-2"></i> Verified List ({{ $verifiedCount ?? 0 }})
                </button>
            </nav>
        </div>
    </div>

    <!-- ALL STUDENTS TAB Desktop -->
    <div id="allTab" class="tab-pane active">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-colors">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white transition-colors">
                <div class="flex flex-wrap gap-3 justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            <i class="fas fa-users text-blue-600 mr-2"></i> All Students
                        </h3>
                        <p class="text-sm text-gray-500">Search and verify students</p>
                    </div>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="searchStudentInput" placeholder="Search..." 
                               class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white text-gray-800">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        @foreach($students as $student)
                        @php $isVerified = in_array($student->student_id, $verifiedStudentIds ?? []); @endphp
                        <tr class="student-row border-b border-gray-100" data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}" data-student-id="{{ $student->student_id }}">
                            <td class="px-5 py-3 font-mono text-sm text-gray-700">{{ $student->student_id }}</td>
                            <td class="px-5 py-3 text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                            <td class="px-5 py-3 text-gray-600">{{ $student->course }}</td>
                            <td class="px-5 py-3">
                                @if($isVerified)
                                    <span class="text-green-600 text-sm"><i class="fas fa-check-circle"></i> Verified</span>
                                @else
                                    <span class="text-gray-500 text-sm">Not Verified</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if(!$isVerified)
                                <button onclick="verifyStudent('{{ $student->student_id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                        class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                                    Verify
                                </button>
                                @endif
                            </td>
                         تضيفلها
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VERIFIED TAB Desktop -->
    <div id="verifiedTab" class="tab-pane hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden transition-colors">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 transition-colors">
                <div class="flex flex-wrap gap-3 justify-between items-center">
                    <h3 class="font-semibold text-gray-800"><i class="fas fa-check-double text-green-600 mr-2"></i> Verified Students</h3>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text" id="searchVerifiedInput" placeholder="Search..." class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 bg-white text-gray-800">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                        </tr>
                    </thead>
                    <tbody id="verifiedTableBody">
                        @foreach($verifiedStudents as $verified)
                        <tr class="border-b border-gray-100">
                            <td class="px-5 py-3 font-mono text-sm text-gray-700">{{ $verified->student_id }}</td>
                            <td class="px-5 py-3 text-gray-800">{{ $verified->student_name }}</td>
                            <td class="px-5 py-3">
                                <button onclick="removeVerified({{ $verified->id }})" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                                    Remove
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============ MOBILE LAYOUT ============ -->
<div class="mobile-layout">
    <!-- EXPORT BUTTON - Mobile -->
    <div class="export-mobile-btn">
        <button onclick="exportVerifiedToCSV()" class="export-btn text-white px-4 py-2.5 rounded-lg shadow-md flex items-center justify-center gap-2 w-full">
            <i class="fas fa-file-csv text-lg"></i>
            <span>Export Verified to CSV</span>
        </button>
    </div>
    
    <!-- Mobile Tabs -->
    <div class="mobile-tabs">
        <div id="mobileTabAll" class="mobile-tab active" onclick="switchMobileTab('all')">
            <i class="fas fa-users mr-1"></i> All Students
        </div>
        <div id="mobileTabVerified" class="mobile-tab" onclick="switchMobileTab('verified')">
            <i class="fas fa-check-double mr-1"></i> Verified
        </div>
    </div>
    
    <!-- Mobile Tab Content: All Students -->
    <div id="mobileAllContent" class="mobile-tab-content active">
        <div class="bg-white rounded-xl shadow-sm transition-colors">
            <div class="p-3 border-b border-gray-100">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" id="mobileSearchInput" placeholder="Search student..." 
                           class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white text-gray-800">
                </div>
            </div>
            <div id="mobileStudentsList" class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                @foreach($students as $student)
                @php $isVerified = in_array($student->student_id, $verifiedStudentIds ?? []); @endphp
                <div class="student-list-item student-mobile-item" 
                     data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}"
                     data-student-id="{{ $student->student_id }}">
                    <div class="student-info">
                        <div class="student-name">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div class="student-detail">{{ $student->student_id }} • {{ $student->course }}</div>
                    </div>
                    <div>
                        @if($isVerified)
                            <span class="text-green-600 text-xs"><i class="fas fa-check-circle"></i> Verified</span>
                        @else
                            <button onclick="verifyStudentMobile('{{ $student->student_id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                    class="verify-btn-sm bg-green-600 text-white px-3 py-1 rounded-full text-xs hover:bg-green-700">
                                Verify
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Mobile Tab Content: Verified List -->
    <div id="mobileVerifiedContent" class="mobile-tab-content">
        <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 transition-colors">
            <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-800 bg-green-50 rounded-t-xl transition-colors">
                <i class="fas fa-check-double text-green-600 mr-2"></i> Verified Students 
                <span class="text-sm text-gray-500">({{ $verifiedCount ?? 0 }})</span>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($verifiedStudents as $verified)
                <div class="verified-list-item hover:bg-gray-50 transition-colors">
                    <div>
                        <div class="font-medium text-gray-800">{{ $verified->student_name }}</div>
                        <div class="text-xs text-gray-500">{{ $verified->student_id }}</div>
                    </div>
                    <button onclick="removeVerified({{ $verified->id }})" class="remove-btn hover:text-red-700">
                        <i class="fas fa-trash-alt mr-1"></i> Remove
                    </button>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                    <p class="text-sm">No verified students yet</p>
                    <p class="text-xs mt-1">Click Verify from All Students tab</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Slide-out Arrow Button -->
<div class="slide-arrow" onclick="openStatsPanel()">
    <i class="fas fa-chevron-left"></i>
</div>

<!-- Panel Overlay -->
<div id="panelOverlay" class="panel-overlay" onclick="closeStatsPanel()"></div>

<!-- Slide-out Stats Panel (Cards) -->
<div id="statsPanel" class="student-panel">
    <div class="sticky top-0 bg-blue-600 text-white p-4 flex justify-between items-center">
        <h3 class="font-semibold"><i class="fas fa-chart-line mr-2"></i> Statistics</h3>
        <button onclick="closeStatsPanel()" class="text-white text-2xl">&times;</button>
    </div>
    
    <div class="p-4 space-y-4">
        <div class="bg-blue-50 rounded-xl p-4 text-center transition-colors">
            <p class="text-gray-500 text-sm">Total Students</p>
            <p class="text-3xl font-bold text-blue-600">{{ $students->count() ?? 0 }}</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center transition-colors">
            <p class="text-gray-500 text-sm">Verified</p>
            <p class="text-3xl font-bold text-green-600">{{ $verifiedCount ?? 0 }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 text-center transition-colors">
            <p class="text-gray-500 text-sm">Not Verified</p>
            <p class="text-3xl font-bold text-yellow-600">{{ ($students->count() ?? 0) - ($verifiedCount ?? 0) }}</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ EXPORT TO CSV FUNCTION ============
   function exportVerifiedToCSV() {
    Swal.fire({
        title: 'Export Verified Students?',
        text: 'This will generate a CSV report and save it for department staff to download.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8b5cf6',
        confirmButtonText: 'Yes, Generate Report!',
        cancelButtonText: 'Cancel',
        input: 'text',
        inputPlaceholder: 'Event name (e.g., Annual Summit 2024)',
        inputLabel: 'Event Name (optional)'
    }).then((result) => {
        if (result.isConfirmed) {
            const eventName = result.value || 'General_Verification';
            
            Swal.fire({
                title: 'Generating Report...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            // Gamitin ang direct URL
            fetch('/officer/export-csv', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ event_name: eventName })
            })
            .then(response => {
                console.log('Status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Response text:', text);
                        throw new Error('HTTP ' + response.status + ': ' + text.substring(0, 200));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Report Generated!',
                        text: data.message,
                        confirmButtonColor: '#8b5cf6'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Error', 
                    text: error.message 
                });
            });
        }
    });
}
    
    // ============ TAB SWITCHING (Desktop) ============
    function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });
        document.getElementById(tabName + 'Tab').classList.remove('hidden');
        document.getElementById(tabName + 'Tab').classList.add('active');
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        const activeBtn = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Btn');
        activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
    }
    
    // ============ MOBILE TAB SWITCHING ============
    function switchMobileTab(tab) {
        document.getElementById('mobileTabAll').classList.toggle('active', tab === 'all');
        document.getElementById('mobileTabVerified').classList.toggle('active', tab === 'verified');
        
        document.getElementById('mobileAllContent').classList.toggle('active', tab === 'all');
        document.getElementById('mobileVerifiedContent').classList.toggle('active', tab === 'verified');
    }
    
    // ============ STATS PANEL FUNCTIONS ============
    function openStatsPanel() {
        document.getElementById('statsPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function closeStatsPanel() {
        document.getElementById('statsPanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('show');
        document.body.style.overflow = '';
    }
    
    // ============ VERIFY STUDENT (Mobile) ============
    function verifyStudentMobile(studentId, studentName) {
        Swal.fire({
            title: 'Verify Student?',
            html: `Mark <strong>${studentName}</strong> as verified?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'Yes, Verify!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Verifying...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch('{{ route("officer.verify.student") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ student_id: studentId, student_name: studentName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Verified!', text: data.message, timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error' }));
            }
        });
    }
    
    // ============ VERIFY STUDENT (Desktop) ============
    function verifyStudent(studentId, studentName) {
        Swal.fire({
            title: 'Verify Student?',
            html: `Mark <strong>${studentName}</strong> as verified?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#22c55e',
            confirmButtonText: 'Yes, Verify!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Verifying...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch('{{ route("officer.verify.student") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ student_id: studentId, student_name: studentName })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Verified!', text: data.message, timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error' }));
            }
        });
    }
    
    // ============ REMOVE VERIFIED STUDENT ============
    function removeVerified(id) {
        Swal.fire({
            title: 'Remove from Verified List?',
            text: 'This student will no longer be automatically approved.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, remove!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch('/officer/verified/' + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Removed!', text: data.message, timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(() => Swal.fire({ icon: 'error', title: 'Network Error' }));
            }
        });
    }
    
    // ============ SEARCH FUNCTIONS ============
    document.getElementById('searchStudentInput')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        let rows = document.querySelectorAll('#studentsTableBody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
    
    document.getElementById('searchVerifiedInput')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        let rows = document.querySelectorAll('#verifiedTableBody tr');
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
    
    document.getElementById('mobileSearchInput')?.addEventListener('keyup', function() {
        let term = this.value.toLowerCase();
        let items = document.querySelectorAll('.student-mobile-item');
        items.forEach(item => {
            let text = item.getAttribute('data-name') + ' ' + item.getAttribute('data-student-id');
            item.style.display = text.includes(term) ? '' : 'none';
        });
    });
    
    // Close panel on escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeStatsPanel();
    });
</script>
@endsection