@extends('layouts.staff')

@section('title', 'Staff Dashboard')
@section('header', 'Clearance Management')

@section('content')
<style>
    /* Dark mode CSS variables for dashboard */
    :root {
        --card-bg: #ffffff;
        --text-primary: #1f2937;
        --text-secondary: #6b7280;
        --border-color: #e5e7eb;
        --stat-blue-bg: #dbeafe;
        --stat-blue-text: #1e40af;
        --stat-yellow-bg: #fef3c7;
        --stat-yellow-text: #92400e;
        --stat-green-bg: #d1fae5;
        --stat-green-text: #065f46;
        --stat-red-bg: #fee2e2;
        --stat-red-text: #991b1b;
        --stat-purple-bg: #f3e8ff;
        --stat-purple-text: #6b21a8;
        --table-header-bg: #f9fafb;
        --table-hover-bg: #f3f4f6;
        --badge-blue-bg: #dbeafe;
        --badge-blue-text: #1e40af;
        --badge-green-bg: #d1fae5;
        --badge-green-text: #065f46;
        --badge-yellow-bg: #fef3c7;
        --badge-yellow-text: #92400e;
        --badge-purple-bg: #f3e8ff;
        --badge-purple-text: #6b21a8;
        --modal-bg: #ffffff;
        --input-bg: #ffffff;
        --input-border: #e5e7eb;
    }

    body.dark-mode {
        --card-bg: #1f2937;
        --text-primary: #f9fafb;
        --text-secondary: #9ca3af;
        --border-color: #374151;
        --stat-blue-bg: #1e3a5f;
        --stat-blue-text: #60a5fa;
        --stat-yellow-bg: #713f12;
        --stat-yellow-text: #fde047;
        --stat-green-bg: #064e3b;
        --stat-green-text: #4ade80;
        --stat-red-bg: #7f1d1d;
        --stat-red-text: #fca5a5;
        --stat-purple-bg: #4c1d95;
        --stat-purple-text: #c084fc;
        --table-header-bg: #374151;
        --table-hover-bg: #374151;
        --badge-blue-bg: #1e3a5f;
        --badge-blue-text: #93c5fd;
        --badge-green-bg: #064e3b;
        --badge-green-text: #86efac;
        --badge-yellow-bg: #713f12;
        --badge-yellow-text: #fde047;
        --badge-purple-bg: #4c1d95;
        --badge-purple-text: #d8b4fe;
        --modal-bg: #1f2937;
        --input-bg: #374151;
        --input-border: #4b5563;
    }

    /* Stats Cards */
    .stat-card {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    .stat-card .stat-label {
        color: var(--text-secondary);
    }
    .stat-card .stat-value-blue { color: var(--stat-blue-text); }
    .stat-card .stat-value-yellow { color: var(--stat-yellow-text); }
    .stat-card .stat-value-green { color: var(--stat-green-text); }
    .stat-card .stat-value-red { color: var(--stat-red-text); }
    .stat-card .stat-value-purple { color: var(--stat-purple-text); }
    .stat-icon-blue { background-color: var(--stat-blue-bg); }
    .stat-icon-yellow { background-color: var(--stat-yellow-bg); }
    .stat-icon-green { background-color: var(--stat-green-bg); }
    .stat-icon-red { background-color: var(--stat-red-bg); }
    .stat-icon-purple { background-color: var(--stat-purple-bg); }
    .stat-icon-blue i, .stat-icon-yellow i, .stat-icon-green i, .stat-icon-red i, .stat-icon-purple i {
        color: var(--stat-blue-text);
    }

    /* Tables */
    .data-table {
        background-color: var(--card-bg);
        border-color: var(--border-color);
    }
    .data-table thead th {
        background-color: var(--table-header-bg);
        color: var(--text-secondary);
        border-bottom-color: var(--border-color);
    }
    .data-table tbody td {
        color: var(--text-primary);
        border-bottom-color: var(--border-color);
    }
    .data-table tbody tr:hover td {
        background-color: var(--table-hover-bg);
    }

    /* Badges */
    .badge-blue { background-color: var(--badge-blue-bg); color: var(--badge-blue-text); }
    .badge-green { background-color: var(--badge-green-bg); color: var(--badge-green-text); }
    .badge-yellow { background-color: var(--badge-yellow-bg); color: var(--badge-yellow-text); }
    .badge-purple { background-color: var(--badge-purple-bg); color: var(--badge-purple-text); }
    .badge-gray { background-color: var(--border-color); color: var(--text-secondary); }

    /* Tabs */
    .tab-btn {
        transition: all 0.2s ease;
    }
    .tab-btn.active {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    body.dark-mode .tab-btn:not(.active) {
        background-color: #374151 !important;
        color: #9ca3af !important;
    }
    body.dark-mode .tab-btn:not(.active):hover {
        background-color: #4b5563 !important;
        color: #e5e7eb !important;
    }

    /* Modals */
    .modal-content {
        background-color: var(--modal-bg);
    }
    .modal-content input, .modal-content select, .modal-content textarea {
        background-color: var(--input-bg);
        border-color: var(--input-border);
        color: var(--text-primary);
    }

    /* Section headers */
    .section-header {
        background-color: var(--table-header-bg);
        border-bottom-color: var(--border-color);
    }
    .section-header h3 {
        color: var(--text-primary);
    }
    
    /* Tab panes */
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }
</style>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="stat-card rounded-xl p-4 shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label text-sm">Total Students</p>
                <p class="stat-value-blue text-2xl font-bold">{{ $totalStudents ?? 0 }}</p>
            </div>
            <div class="stat-icon-blue w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-lg"></i>
            </div>
        </div>
    </div>
    <div class="stat-card rounded-xl p-4 shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label text-sm">Pending</p>
                <p class="stat-value-yellow text-2xl font-bold">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="stat-icon-yellow w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-lg"></i>
            </div>
        </div>
    </div>
    <div class="stat-card rounded-xl p-4 shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label text-sm">Approved</p>
                <p class="stat-value-green text-2xl font-bold">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="stat-icon-green w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-lg"></i>
            </div>
        </div>
    </div>
    <div class="stat-card rounded-xl p-4 shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label text-sm">Rejected</p>
                <p class="stat-value-red text-2xl font-bold">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <div class="stat-icon-red w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-lg"></i>
            </div>
        </div>
    </div>
    <div class="stat-card rounded-xl p-4 shadow-sm border">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-label text-sm">Reports</p>
                <p class="stat-value-purple text-2xl font-bold">{{ $exportsCount ?? 0 }}</p>
            </div>
            <div class="stat-icon-purple w-10 h-10 rounded-full flex items-center justify-center">
                <i class="fas fa-file-csv text-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="mb-4">
    <div class="border-b" style="border-color: var(--border-color);">
        <nav class="flex gap-2 flex-wrap" aria-label="Tabs">
            <button onclick="switchTab('queue')" id="tabQueueBtn" class="tab-btn active px-5 py-2.5 text-sm font-medium rounded-t-lg bg-blue-600 text-white transition">
                <i class="fas fa-clock mr-2"></i> Queue <span class="ml-1">({{ $pendingCount ?? 0 }})</span>
            </button>
            <button onclick="switchTab('approved')" id="tabApprovedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-check-circle mr-2"></i> Approved <span class="ml-1">({{ $approvedCount ?? 0 }})</span>
            </button>
            <button onclick="switchTab('rejected')" id="tabRejectedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-times-circle mr-2"></i> Rejected <span class="ml-1">({{ $rejectedCount ?? 0 }})</span>
            </button>
            <button onclick="switchTab('requirements')" id="tabRequirementsBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-list-check mr-2"></i> Requirements
            </button>
            <button onclick="switchTab('reports')" id="tabReportsBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-chart-line mr-2"></i> Reports <span class="ml-1">({{ $exportsCount ?? 0 }})</span>
            </button>
            <button onclick="switchTab('verified')" id="tabVerifiedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-check-double mr-2"></i> Verified List <span class="ml-1">({{ $verifiedCount ?? 0 }})</span>
            </button>
        </nav>
    </div>
</div>

<!-- ============ QUEUE TAB ============ -->
<div id="queueTab" class="tab-pane active">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <h3 class="font-semibold">
                <i class="fas fa-clock mr-2"></i> Pending Clearance Requests
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Year Level</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Submitted</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests ?? [] as $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-sm">{{ $request->student->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ $request->student->first_name ?? '' }} {{ $request->student->last_name ?? '' }}</div>
                            <div class="text-xs" style="color: var(--text-secondary);">{{ $request->student->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($request->student->year_level == '1st Year') badge-blue
                                @elseif($request->student->year_level == '2nd Year') badge-green
                                @elseif($request->student->year_level == '3rd Year') badge-yellow
                                @else badge-purple @endif">
                                <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                {{ $request->student->year_level ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm">{{ $request->submitted_at ? $request->submitted_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <button onclick="openApproveModal({{ $request->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                            <button onclick="openRejectModal({{ $request->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition ml-2">
                                <i class="fas fa-times mr-1"></i> Reject
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No pending requests</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ APPROVED TAB ============ -->
<div id="approvedTab" class="tab-pane hidden">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <h3 class="font-semibold">
                <i class="fas fa-check-circle mr-2"></i> Approved Requests
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Year Level</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedRequests ?? [] as $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-sm">{{ $request->student->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ $request->student->first_name ?? '' }} {{ $request->student->last_name ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($request->student->year_level == '1st Year') badge-blue
                                @elseif($request->student->year_level == '2nd Year') badge-green
                                @elseif($request->student->year_level == '3rd Year') badge-yellow
                                @else badge-purple @endif">
                                <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                {{ $request->student->year_level ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm">{{ $request->processed_at ? $request->processed_at->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No approved requests</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ REJECTED TAB ============ -->
<div id="rejectedTab" class="tab-pane hidden">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <h3 class="font-semibold">
                <i class="fas fa-times-circle mr-2"></i> Rejected Requests
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Year Level</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rejectedRequests ?? [] as $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-sm">{{ $request->student->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium">{{ $request->student->first_name ?? '' }} {{ $request->student->last_name ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($request->student->year_level == '1st Year') badge-blue
                                @elseif($request->student->year_level == '2nd Year') badge-green
                                @elseif($request->student->year_level == '3rd Year') badge-yellow
                                @else badge-purple @endif">
                                <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                {{ $request->student->year_level ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm" style="color: var(--danger-text);">{{ $request->remarks ?? 'No reason' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No rejected requests</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ REQUIREMENTS TAB ============ -->
<div id="requirementsTab" class="tab-pane hidden">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <h3 class="font-semibold">
                    <i class="fas fa-list-check mr-2"></i> Requirements by Year Level
                </h3>
                <div class="flex gap-2">
                    <select id="yearLevelFilter" class="border rounded-lg px-3 py-1 text-sm" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);" onchange="filterRequirements()">
                        <option value="all">All Year Levels</option>
                        @php $assignedYears = $department->getAssignedYearLevels(); @endphp
                        @foreach($assignedYears as $year)
                            <option value="{{ $year }}"><i class="fas fa-graduation-cap"></i> {{ $year }}</option>
                        @endforeach
                    </select>
                    <button onclick="openAddRequirementModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-plus mr-1"></i> Add Requirement
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Year Level</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Requirement</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Required</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="requirementsTableBody">
                    @php
                        $allRequirements = \App\Models\DepartmentYearRequirement::where('department_id', $department->id)
                            ->orderBy('year_level')
                            ->orderBy('sort_order')
                            ->get();
                    @endphp
                    
                    @forelse($allRequirements as $req)
                    <tr class="hover:bg-gray-50 transition requirement-row" data-year="{{ $req->year_level }}">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($req->year_level == '1st Year') badge-blue
                                @elseif($req->year_level == '2nd Year') badge-green
                                @elseif($req->year_level == '3rd Year') badge-yellow
                                @else badge-purple @endif">
                                <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                {{ $req->year_level }}
                            </span>
                        </td>
                        <td class="px-5 py-3">{{ $req->requirement_name }}</td>
                        <td class="px-5 py-3">
                            @if($req->is_required)
                                <span class="text-red-600 dark:text-red-400 text-sm"><i class="fas fa-exclamation-circle"></i> Required</span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 text-sm"><i class="fas fa-check-circle"></i> Optional</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($req->is_active)
                                <span class="text-green-600 dark:text-green-400 text-sm"><i class="fas fa-circle"></i> Active</span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-sm"><i class="fas fa-circle"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <button onclick="editRequirement({{ $req->id }}, '{{ addslashes($req->requirement_name) }}', {{ $req->is_required ? 'true' : 'false' }}, {{ $req->is_active ? 'true' : 'false' }}, '{{ $req->year_level }}')" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteRequirement({{ $req->id }}, '{{ addslashes($req->requirement_name) }}')" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition ml-2">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="noRequirementsRow">
                        <td colspan="5" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No requirements yet. Click "Add Requirement" to create one.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ REPORTS TAB ============ -->
<div id="reportsTab" class="tab-pane hidden">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <h3 class="font-semibold">
                <i class="fas fa-chart-line mr-2"></i> Generated Reports
            </h3>
        </div>
        <div class="overflow-x-auto">
            @if(isset($exports) && $exports->count() > 0)
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Event Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Records</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exports as $export)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3">{{ $export->event_name }}</td>
                        <td class="px-5 py-3">{{ $export->total_records }}</td>
                        <td class="px-5 py-3 text-sm">{{ \Carbon\Carbon::parse($export->export_date)->format('M d, Y') }}</td>
                        <td class="px-5 py-3">
                            <button onclick="downloadReport({{ $export->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button onclick="importReport({{ $export->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition ml-2">
                                <i class="fas fa-upload"></i> Import
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No reports available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @else
            <div class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                <i class="fas fa-chart-line text-4xl mb-2 opacity-50"></i>
                <p>No reports available.</p>
                <p class="text-xs mt-2">Reports appear here when officers export verified student lists.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ============ VERIFIED LIST TAB ============ -->
<div id="verifiedTab" class="tab-pane hidden">
    <div class="data-table rounded-xl shadow-sm border overflow-hidden">
        <div class="section-header px-5 py-4 border-b">
            <div class="flex justify-between items-center flex-wrap gap-3">
                <h3 class="font-semibold">
                    <i class="fas fa-check-double mr-2"></i> Verified Student List
                </h3>
                <div class="flex gap-2">
                    <select id="verifiedYearFilter" class="border rounded-lg px-3 py-1 text-sm" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);" onchange="filterVerifiedList()">
                        <option value="all">All Year Levels</option>
                        @foreach($assignedYears as $year)
                            <option value="{{ $year }}"><i class="fas fa-graduation-cap"></i> {{ $year }}</option>
                        @endforeach
                    </select>
                    <button onclick="openUploadCSVModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm transition">
                        <i class="fas fa-upload mr-1"></i> Upload CSV
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full data-table">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Year Level</th>
                    </tr>
                </thead>
                <tbody id="verifiedTableBody">
                    @forelse($verifiedStudents ?? [] as $verified)
                    <tr class="hover:bg-gray-50 transition verified-row" data-year="{{ $verified['year_level'] ?? 'N/A' }}">
                        <td class="px-5 py-3 font-mono text-sm">{{ $verified['student_id'] ?? 'N/A' }}</td>
                        <td class="px-5 py-3">{{ $verified['student_name'] ?? 'N/A' }}</td>
                        <td class="px-5 py-3">{{ $verified['course'] ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if(($verified['year_level'] ?? '') == '1st Year') badge-blue
                                @elseif(($verified['year_level'] ?? '') == '2nd Year') badge-green
                                @elseif(($verified['year_level'] ?? '') == '3rd Year') badge-yellow
                                @else badge-purple @endif">
                                <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                {{ $verified['year_level'] ?? 'N/A' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr id="noVerifiedRow">
                        <td colspan="4" class="px-5 py-8 text-center" style="color: var(--text-secondary);">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>No verified students</p>
                            <button onclick="openUploadCSVModal()" class="mt-2 text-blue-600 hover:underline">Upload CSV to add students</button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ MODALS ============ -->

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-content rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-green-600 dark:text-green-400 mb-4">
            <i class="fas fa-check-circle mr-2"></i> Approve Clearance
        </h3>
        <p id="approveMessage" class="mb-4" style="color: var(--text-secondary);"></p>
        <div class="flex justify-end gap-2">
            <button onclick="closeApproveModal()" class="px-4 py-2 border rounded-lg text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700" style="border-color: var(--border-color); color: var(--text-primary);">Cancel</button>
            <form id="approveForm" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">Confirm Approve</button>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-content rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">
            <i class="fas fa-times-circle mr-2"></i> Reject Clearance
        </h3>
        <p id="rejectMessage" class="mb-2" style="color: var(--text-secondary);"></p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <textarea name="remarks" class="w-full border rounded-lg p-2 focus:ring-2 focus:ring-red-500" rows="3" placeholder="Reason for rejection" required style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border rounded-lg text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700" style="border-color: var(--border-color); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Requirement Modal -->
<div id="addRequirementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-content rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-blue-600 dark:text-blue-400 mb-4">
            <i class="fas fa-plus-circle mr-2"></i> Add Requirement
        </h3>
        <form id="addRequirementForm" method="POST" action="{{ route('staff.year-requirements.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium" style="color: var(--text-primary);">Year Level <span class="text-red-500">*</span></label>
                <select name="year_level" id="req_year_level" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);">
                    @foreach($assignedYears as $year)
                        <option value="{{ $year }}"><i class="fas fa-graduation-cap"></i> {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium" style="color: var(--text-primary);">Requirement Name <span class="text-red-500">*</span></label>
                <input type="text" name="requirement_name" id="req_name" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);" placeholder="e.g., Clearance Form, Good Moral Certificate">
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" checked class="w-4 h-4">
                    <span class="text-sm" style="color: var(--text-primary);">This requirement is <strong>required</strong></span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddRequirementModal()" class="px-4 py-2 border rounded-lg text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700" style="border-color: var(--border-color); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">Add Requirement</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Requirement Modal -->
<div id="editRequirementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-content rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mb-4">
            <i class="fas fa-edit mr-2"></i> Edit Requirement
        </h3>
        <form id="editRequirementForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium" style="color: var(--text-primary);">Year Level</label>
                <input type="text" id="edit_year_level" disabled class="w-full border rounded-lg px-3 py-2 bg-gray-100 dark:bg-gray-600" style="color: var(--text-secondary);">
                <input type="hidden" name="year_level" id="edit_year_level_hidden">
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium" style="color: var(--text-primary);">Requirement Name <span class="text-red-500">*</span></label>
                <input type="text" name="requirement_name" id="edit_req_name" required class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);">
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" id="edit_is_required" value="1" class="w-4 h-4">
                    <span class="text-sm" style="color: var(--text-primary);">This requirement is <strong>required</strong></span>
                </label>
            </div>
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4">
                    <span class="text-sm" style="color: var(--text-primary);">Active (visible to students)</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditRequirementModal()" class="px-4 py-2 border rounded-lg text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700" style="border-color: var(--border-color); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-700 transition">Update Requirement</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload CSV Modal -->
<div id="uploadCSVModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="modal-content rounded-xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold text-green-600 dark:text-green-400 mb-4">
            <i class="fas fa-upload mr-2"></i> Upload CSV
        </h3>
        <p class="text-sm mb-4" style="color: var(--text-secondary);">
            CSV format: <strong>Student ID, Name, Course, Year Level</strong><br>
            <span class="text-xs opacity-75">Example: 2023-00123,Juan Dela Cruz,BSIT,1st Year</span>
        </p>
        <form id="uploadCSVForm" method="POST" action="{{ route('staff.verified.upload-csv') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <input type="file" name="csv_file" accept=".csv,.txt" required class="w-full border rounded-lg p-2" style="background-color: var(--input-bg); border-color: var(--input-border); color: var(--text-primary);">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUploadCSVModal()" class="px-4 py-2 border rounded-lg text-sm transition hover:bg-gray-100 dark:hover:bg-gray-700" style="border-color: var(--border-color); color: var(--text-primary);">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">Upload</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ TAB SWITCHING ============
    function switchTab(tabName) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });
        
        // Remove active class from all tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        
        // Show selected tab pane
        const selectedPane = document.getElementById(tabName + 'Tab');
        if (selectedPane) {
            selectedPane.classList.remove('hidden');
            selectedPane.classList.add('active');
        }
        
        // Add active class to selected button
        const selectedBtn = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Btn');
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-gray-200', 'text-gray-700');
            selectedBtn.classList.add('active', 'bg-blue-600', 'text-white');
        }
    }
    
    // ============ APPROVE/REJECT ============
    function openApproveModal(id) {
        document.getElementById('approveForm').action = '/staff/approve/' + id;
        document.getElementById('approveModal').classList.remove('hidden');
        document.getElementById('approveModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
        document.getElementById('approveModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    function openRejectModal(id) {
        document.getElementById('rejectForm').action = '/staff/reject/' + id;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // ============ REQUIREMENTS ============
    function openAddRequirementModal() {
        document.getElementById('addRequirementModal').classList.remove('hidden');
        document.getElementById('addRequirementModal').classList.add('flex');
        document.getElementById('addRequirementForm').reset();
        document.body.style.overflow = 'hidden';
    }
    function closeAddRequirementModal() {
        document.getElementById('addRequirementModal').classList.add('hidden');
        document.getElementById('addRequirementModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function editRequirement(id, name, isRequired, isActive, yearLevel) {
        document.getElementById('editRequirementForm').action = '/staff/year-requirements/' + id;
        document.getElementById('edit_req_name').value = name;
        document.getElementById('edit_year_level').value = yearLevel;
        document.getElementById('edit_year_level_hidden').value = yearLevel;
        document.getElementById('edit_is_required').checked = isRequired;
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editRequirementModal').classList.remove('hidden');
        document.getElementById('editRequirementModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeEditRequirementModal() {
        document.getElementById('editRequirementModal').classList.add('hidden');
        document.getElementById('editRequirementModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function deleteRequirement(id, name) {
        Swal.fire({
            title: 'Delete Requirement?',
            text: 'Are you sure you want to delete "' + name + '"?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/staff/year-requirements/' + id;
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                let method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function filterRequirements() {
        const filter = document.getElementById('yearLevelFilter').value;
        const rows = document.querySelectorAll('.requirement-row');
        let hasVisible = false;
        
        rows.forEach(row => {
            if (filter === 'all' || row.dataset.year === filter) {
                row.style.display = '';
                hasVisible = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        const noResultRow = document.getElementById('noRequirementsRow');
        if (noResultRow) {
            if (!hasVisible && filter !== 'all') {
                noResultRow.style.display = '';
                noResultRow.innerHTML = '<td colspan="5" class="px-5 py-8 text-center" style="color: var(--text-secondary);"><i class="fas fa-filter text-4xl mb-2 opacity-50"></i><p>No requirements for ' + filter + '</p></td>';
            } else if (filter === 'all' && rows.length === 0) {
                noResultRow.style.display = '';
            } else {
                noResultRow.style.display = 'none';
            }
        }
    }
    
    function filterVerifiedList() {
        const filter = document.getElementById('verifiedYearFilter').value;
        const rows = document.querySelectorAll('.verified-row');
        let hasVisible = false;
        
        rows.forEach(row => {
            if (filter === 'all' || row.dataset.year === filter) {
                row.style.display = '';
                hasVisible = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        const noResultRow = document.getElementById('noVerifiedRow');
        if (noResultRow) {
            if (!hasVisible && filter !== 'all') {
                noResultRow.style.display = '';
                noResultRow.innerHTML = '<td colspan="4" class="px-5 py-8 text-center" style="color: var(--text-secondary);"><i class="fas fa-filter text-4xl mb-2 opacity-50"></i><p>No verified students for ' + filter + '</p><button onclick="openUploadCSVModal()" class="mt-2 text-blue-600 hover:underline">Upload CSV to add students</button></td>';
            } else if (filter === 'all' && rows.length === 0) {
                noResultRow.style.display = '';
            } else {
                noResultRow.style.display = 'none';
            }
        }
    }
    
    // ============ REPORTS ============
    function downloadReport(id) {
        window.location.href = '/staff/download-export/' + id;
    }
    function importReport(id) {
        Swal.fire({
            title: 'Import Report?',
            text: 'This will replace the current verified list.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, import',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/staff/import-report', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ export_id: id })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                      } else {
                          Swal.fire('Error!', data.message, 'error');
                      }
                  });
            }
        });
    }
    
    // ============ UPLOAD CSV ============
    function openUploadCSVModal() {
        document.getElementById('uploadCSVModal').classList.remove('hidden');
        document.getElementById('uploadCSVModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeUploadCSVModal() {
        document.getElementById('uploadCSVModal').classList.add('hidden');
        document.getElementById('uploadCSVModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // Close modals when clicking outside
    document.getElementById('approveModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeApproveModal(); });
    document.getElementById('rejectModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeRejectModal(); });
    document.getElementById('addRequirementModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeAddRequirementModal(); });
    document.getElementById('editRequirementModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeEditRequirementModal(); });
    document.getElementById('uploadCSVModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeUploadCSVModal(); });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeApproveModal();
            closeRejectModal();
            closeAddRequirementModal();
            closeEditRequirementModal();
            closeUploadCSVModal();
        }
    });
// ============ TAB PERSISTENCE ============
// Get the last active tab from localStorage
const lastActiveTab = localStorage.getItem('staff_active_tab') || 'queue';

// Function to save current tab
function saveCurrentTab(tabName) {
    localStorage.setItem('staff_active_tab', tabName);
}

// Modified switchTab function with persistence
function switchTab(tabName, save = true) {
    // Hide all tab panes
    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.add('hidden');
        pane.classList.remove('active');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    
    // Show selected tab pane
    const selectedPane = document.getElementById(tabName + 'Tab');
    if (selectedPane) {
        selectedPane.classList.remove('hidden');
        selectedPane.classList.add('active');
    }
    
    // Add active class to selected button
    const selectedBtn = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Btn');
    if (selectedBtn) {
        selectedBtn.classList.remove('bg-gray-200', 'text-gray-700');
        selectedBtn.classList.add('active', 'bg-blue-600', 'text-white');
    }
    
    // Save to localStorage
    if (save) {
        saveCurrentTab(tabName);
    }
}

// Restore last active tab on page load
if (lastActiveTab) {
    // Make sure the tab exists
    const tabExists = document.getElementById(lastActiveTab + 'Tab') !== null;
    if (tabExists) {
        switchTab(lastActiveTab, false);
    }
}
</script>
@endsection