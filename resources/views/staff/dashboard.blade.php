@extends('layouts.staff')

@section('title', 'Staff Dashboard')
@section('header', 'Clearance Management')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <p class="text-2xl font-bold text-blue-600">{{ $totalStudents ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Approved</p>
                <p class="text-2xl font-bold text-green-600">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Rejected</p>
                <p class="text-2xl font-bold text-red-600">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="mb-4">
    <div class="border-b border-gray-200">
        <nav class="flex gap-2 flex-wrap" aria-label="Tabs">
            <button onclick="switchTab('queue')" id="tabQueueBtn" class="tab-btn active px-5 py-2.5 text-sm font-medium rounded-t-lg bg-blue-600 text-white transition">
                <i class="fas fa-clock mr-2"></i> Queue ({{ $pendingCount ?? 0 }})
            </button>
            <button onclick="switchTab('approved')" id="tabApprovedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-check-circle mr-2"></i> Approved ({{ $approvedCount ?? 0 }})
            </button>
            <button onclick="switchTab('rejected')" id="tabRejectedBtn" class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-times-circle mr-2"></i> Rejected ({{ $rejectedCount ?? 0 }})
            </button>
            <button onclick="switchTab('requirements')" id="tabRequirementsBtn" 
                    class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-list-check mr-2"></i> Requirements ({{ optional($department)->requirements->count() ?? 0 }})
            </button>
            <button onclick="switchTab('verified')" id="tabVerifiedBtn" 
                    class="tab-btn px-5 py-2.5 text-sm font-medium rounded-t-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition">
                <i class="fas fa-check-double mr-2"></i> Verified List ({{ $verifiedCount ?? 0 }})
            </button>
        </nav>
    </div>
</div>

<!-- ============ QUEUE TAB ============ -->
<div id="queueTab" class="tab-pane active">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i> Pending Clearance Requests
                </h3>
                <p class="text-sm text-gray-500">Review and process student clearance submissions</p>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchQueue" placeholder="Search by student name, ID, or course..." 
                       class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="queueTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Request Message</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Attachment</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="queueTableBody">
                    @forelse($pendingRequests ?? [] as $index => $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ optional($request->student)->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800">{{ optional($request->student)->first_name ?? '' }} {{ optional($request->student)->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ optional($request->student)->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ optional($request->student)->course_year ?? ((optional($request->student)->course ?? '') . ' - ' . (optional($request->student)->year_level ?? '')) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request->submitted_at ? $request->submitted_at->format('M d, Y h:i A') : 'N/A' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">
                            @if($request->request_message)
                                <button onclick="showMessageModal('{{ addslashes($request->request_message) }}', '{{ addslashes(optional($request->student)->first_name ?? '') }} {{ addslashes(optional($request->student)->last_name ?? '') }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-comment-dots"></i> View Message
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($request->attachment_path)
                                @php
                                    $filename = basename($request->attachment_path);
                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                    $isPdf = $extension == 'pdf';
                                    $fileUrl = url('/file/' . $filename);
                                @endphp
                                @if($isImage)
                                    <button onclick="openImageViewer('{{ $fileUrl }}', '{{ addslashes((optional($request->student)->first_name ?? '') . ' ' . (optional($request->student)->last_name ?? '')) }}', '{{ addslashes($filename) }}')" 
                                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                        <i class="fas fa-image"></i> View Image
                                    </button>
                                @elseif($isPdf)
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                        <i class="fas fa-file-pdf"></i> View PDF
                                    </a>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                        <i class="fas fa-file"></i> Download
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">No file</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock"></i> Pending
                            </span>
                            @if($request->verified_list_match)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 ml-1">
                                    <i class="fas fa-check-circle"></i> Verified
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <button onclick="openApproveModal({{ $request->id }}, '{{ addslashes((optional($request->student)->first_name ?? '') . ' ' . (optional($request->student)->last_name ?? '')) }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button onclick="openRejectModal({{ $request->id }}, '{{ addslashes((optional($request->student)->first_name ?? '') . ' ' . (optional($request->student)->last_name ?? '')) }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No pending clearance requests</p>
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i> Approved Clearance Requests
                </h3>
                <p class="text-sm text-gray-500">List of approved clearance submissions</p>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchApproved" placeholder="Search by student name, ID, or course..." 
                       class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="approvedTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Processed Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Request Message</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Attachment</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="approvedTableBody">
                    @forelse($approvedRequests ?? [] as $index => $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ optional($request->student)->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800">{{ optional($request->student)->first_name ?? '' }} {{ optional($request->student)->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ optional($request->student)->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ optional($request->student)->course_year ?? ((optional($request->student)->course ?? '') . ' - ' . (optional($request->student)->year_level ?? '')) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request->submitted_at ? $request->submitted_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request->processed_at ? $request->processed_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">
                            @if($request->request_message)
                                <button onclick="showMessageModal('{{ addslashes($request->request_message) }}', '{{ addslashes(optional($request->student)->first_name ?? '') }} {{ addslashes(optional($request->student)->last_name ?? '') }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-comment-dots"></i> View Message
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($request->attachment_path)
                                @php
                                    $filename = basename($request->attachment_path);
                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                    $fileUrl = url('/file/' . $filename);
                                @endphp
                                @if($isImage)
                                    <button onclick="openImageViewer('{{ $fileUrl }}', '{{ addslashes((optional($request->student)->first_name ?? '') . ' ' . (optional($request->student)->last_name ?? '')) }}', '{{ addslashes($filename) }}')" 
                                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                        <i class="fas fa-image"></i> View
                                    </button>
                                @else
                                    <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @endif
                            @else
                                <span class="text-gray-400 text-sm">No file</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Approved
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No approved requests yet</p>
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i> Rejected Clearance Requests
                </h3>
                <p class="text-sm text-gray-500">List of rejected clearance submissions</p>
            </div>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchRejected" placeholder="Search by student name, ID, or course..." 
                       class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-red-500 focus:border-red-500">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="rejectedTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Processed Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Request Message</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Reason</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Attachment</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="rejectedTableBody">
                    @forelse($rejectedRequests ?? [] as $index => $request)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ optional($request->student)->student_id ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <div class="font-medium text-gray-800">{{ optional($request->student)->first_name ?? '' }} {{ optional($request->student)->last_name ?? '' }}</div>
                            <div class="text-xs text-gray-500">{{ optional($request->student)->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">{{ optional($request->student)->course_year ?? ((optional($request->student)->course ?? '') . ' - ' . (optional($request->student)->year_level ?? '')) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request->submitted_at ? $request->submitted_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request->processed_at ? $request->processed_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">
                            @if($request->request_message)
                                <button onclick="showMessageModal('{{ addslashes($request->request_message) }}', '{{ addslashes(optional($request->student)->first_name ?? '') }} {{ addslashes(optional($request->student)->last_name ?? '') }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-comment-dots"></i> View Message
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-red-600 max-w-xs">{{ $request->remarks ?? 'No reason provided' }}</td>
                        <td class="px-5 py-3">
                            @if($request->attachment_path)
                                @php
                                    $filename = basename($request->attachment_path);
                                    $fileUrl = url('/file/' . $filename);
                                @endphp
                                <a href="{{ $fileUrl }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm transition">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">No file</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle"></i> Rejected
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
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
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-list-check text-blue-600 mr-2"></i> Department Requirements
                </h3>
                <p class="text-sm text-gray-500">Manage requirements for your department</p>
            </div>
            <button onclick="openAddRequirementModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus"></i> Add Requirement
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Requirement Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Required?</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="requirementsTableBody">
                    @forelse(optional($department)->requirements ?? [] as $index => $req)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                        <td class="px-5 py-3 text-gray-800">{{ $req->requirement_name }}</td>
                        <td class="px-5 py-3">
                            @if($req->is_required)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-exclamation-circle"></i> Required
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-check-circle"></i> Optional
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if($req->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-circle text-xs"></i> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-circle text-xs"></i> Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex gap-2">
                                <button onclick="openEditRequirementModal({{ $req->id }}, '{{ addslashes($req->requirement_name) }}', {{ $req->is_required ? 'true' : 'false' }}, {{ $req->is_active ? 'true' : 'false' }})" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button onclick="deleteRequirement({{ $req->id }}, '{{ addslashes($req->requirement_name) }}')" 
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No requirements added yet</p>
                            <button onclick="openAddRequirementModal()" class="mt-2 text-blue-600 hover:underline">
                                Add your first requirement
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ VERIFIED STUDENTS TAB ============ -->
<div id="verifiedTab" class="tab-pane hidden">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    <i class="fas fa-check-double text-green-600 mr-2"></i> Verified Student List
                </h3>
                <p class="text-sm text-gray-500">Students who are pre-verified for clearance</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openUploadVerifiedModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-upload"></i> Upload CSV
                </button>
                <button onclick="openAddVerifiedModal()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i> Add Manually
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Verified By</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($verifiedStudents ?? [] as $verified)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-3 font-mono text-sm">{{ $verified->student_id }}</td>
                        <td class="px-5 py-3 text-gray-800">{{ $verified->student_name }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">
                            {{ $verified->verifier->name ?? 'System' }}
                            @if($verified->verified_by_role == 'officer')
                                <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1 rounded">Officer</span>
                            @else
                                <span class="ml-1 text-xs bg-green-100 text-green-700 px-1 rounded">Staff</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $verified->verified_at ? $verified->verified_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-5 py-3">
                            <button onclick="removeVerified({{ $verified->id }})" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>No verified students yet</p>
                            <p class="text-xs mt-1">Upload CSV or add manually</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-green-600">Approve Clearance</h3>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <p id="approveMessage" class="mb-4 text-gray-600"></p>
        <form id="approveForm" method="POST">
            @csrf
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Confirm Approve</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-red-600">Reject Clearance</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <p id="rejectMessage" class="mb-2 text-gray-600"></p>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Reason for rejection:</label>
                <textarea name="remarks" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500" rows="3" required placeholder="Please specify the reason..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Confirm Reject</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Requirement Modal -->
<div id="addRequirementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Add Requirement
            </h3>
            <button onclick="closeAddRequirementModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="addRequirementForm" method="POST" action="{{ route('staff.requirements.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Requirement Name *</label>
                <input type="text" name="requirement_name" id="req_name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., Clearance Form, Good Moral Certificate, etc.">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" value="1" checked class="w-4 h-4">
                    <span class="text-sm text-gray-700">This requirement is <strong>required</strong> (cannot be skipped)</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddRequirementModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Add Requirement</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Requirement Modal -->
<div id="editRequirementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-edit text-yellow-600 mr-2"></i> Edit Requirement
            </h3>
            <button onclick="closeEditRequirementModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="editRequirementForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="requirement_id" id="edit_req_id">
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Requirement Name *</label>
                <input type="text" name="requirement_name" id="edit_req_name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500">
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_required" id="edit_is_required" value="1" class="w-4 h-4">
                    <span class="text-sm text-gray-700">This requirement is <strong>required</strong></span>
                </label>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4">
                    <span class="text-sm text-gray-700">Active (visible to students)</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditRequirementModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm hover:bg-yellow-700">Update Requirement</button>
            </div>
        </form>
    </div>
</div>

<!-- Upload Verified CSV Modal -->
<div id="uploadVerifiedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-upload text-green-600 mr-2"></i> Upload Verified Students
            </h3>
            <button onclick="closeUploadVerifiedModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">
            Upload a CSV file with columns: <strong>Student ID, Student Name</strong><br>
            <span class="text-xs text-gray-400">Example: 2023-00123,Juan Dela Cruz</span>
        </p>
        
        <form id="uploadVerifiedForm" method="POST" action="{{ route('staff.verified.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUploadVerifiedModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Verified Student Manually Modal -->
<div id="addVerifiedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Add Verified Student
            </h3>
            <button onclick="closeAddVerifiedModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="addVerifiedForm" method="POST" action="{{ route('staff.verified.add') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Student ID *</label>
                <input type="text" name="student_id" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="2023-00123">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Student Name *</label>
                <input type="text" name="student_name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Juan Dela Cruz">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddVerifiedModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Add to Verified List</button>
            </div>
        </form>
    </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-comment-dots text-yellow-600 mr-2"></i> Request Message
            </h3>
            <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div id="messageContent" class="mb-4">
            <p id="messageStudentName" class="text-sm text-gray-500 mb-2"></p>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p id="messageText" class="text-gray-700"></p>
            </div>
        </div>
        <div class="flex justify-end">
            <button onclick="closeMessageModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:bg-gray-700">Close</button>
        </div>
    </div>
</div>

<!-- Image Viewer Modal -->
<div id="imageViewerModal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4">
    <div class="relative max-w-4xl w-full max-h-full">
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
            <div class="flex justify-between items-center px-5 py-3 bg-gray-100 border-b border-gray-200">
                <h3 id="imageViewerTitle" class="font-semibold text-gray-800">
                    <i class="fas fa-image text-blue-600 mr-2"></i> Student Attachment
                </h3>
                <button onclick="closeImageViewer()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div class="p-4 bg-gray-50 flex items-center justify-center" style="min-height: 300px; max-height: 70vh; overflow-y: auto;">
                <div id="imageLoadingSpinner" class="flex flex-col items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                    <p class="text-gray-500 text-sm">Loading image...</p>
                </div>
                <img id="imageViewerSrc" src="" alt="Student Attachment" class="max-w-full max-h-full object-contain rounded-lg hidden">
                <div id="imageErrorMsg" class="hidden text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-3"></i>
                    <p class="text-red-600 font-medium">Failed to load image</p>
                    <p id="imageErrorDetail" class="text-gray-500 text-sm mt-2"></p>
                    <button onclick="retryLoadImage()" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        <i class="fas fa-sync-alt mr-1"></i> Retry
                    </button>
                </div>
            </div>
            <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-end gap-2">
                <a id="downloadImageBtn" href="#" download class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm">
                    <i class="fas fa-download"></i> Download
                </a>
                <button onclick="closeImageViewer()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-100">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.add('hidden');
            pane.classList.remove('active');
        });
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-200', 'text-gray-700');
        });
        const selectedPane = document.getElementById(tabName + 'Tab');
        selectedPane.classList.remove('hidden');
        selectedPane.classList.add('active');
        const selectedBtn = document.getElementById('tab' + tabName.charAt(0).toUpperCase() + tabName.slice(1) + 'Btn');
        selectedBtn.classList.remove('bg-gray-200', 'text-gray-700');
        selectedBtn.classList.add('active', 'bg-blue-600', 'text-white');
    }

    function setupSearch(inputId, tbodyId, colspan) {
        document.getElementById(inputId)?.addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#' + tbodyId + ' tr');
            let hasResults = false;
            rows.forEach(row => {
                if (row.dataset.noResult) return;
                let text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) { row.style.display = ''; hasResults = true; }
                else row.style.display = 'none';
            });
            let noResultRow = document.querySelector('#' + tbodyId + ' tr[data-no-result]');
            if (!hasResults && rows.length > 0 && rows[0].cells) {
                if (!noResultRow) {
                    let tr = document.createElement('tr');
                    tr.dataset.noResult = '1';
                    tr.innerHTML = `<td colspan="${colspan}" class="px-5 py-8 text-center text-gray-500"><i class="fas fa-search text-4xl mb-2 text-gray-300"></i><p>No results found</p></td>`;
                    document.getElementById(tbodyId).appendChild(tr);
                    noResultRow = tr;
                }
                noResultRow.style.display = '';
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        });
    }
    setupSearch('searchQueue', 'queueTableBody', 9);
    setupSearch('searchApproved', 'approvedTableBody', 9);
    setupSearch('searchRejected', 'rejectedTableBody', 10);

    function openApproveModal(id, studentName) {
        document.getElementById('approveMessage').innerHTML = 'Are you sure you want to approve <strong class="text-gray-800">' + studentName + '</strong>\'s clearance?';
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

    function openRejectModal(id, studentName) {
        document.getElementById('rejectMessage').innerHTML = 'Reject <strong class="text-gray-800">' + studentName + '</strong>\'s clearance?';
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

    // Message Modal functions
    function showMessageModal(message, studentName) {
        document.getElementById('messageStudentName').innerHTML = '<i class="fas fa-user mr-1"></i> From: <strong>' + studentName + '</strong>';
        document.getElementById('messageText').innerHTML = message.replace(/\n/g, '<br>');
        document.getElementById('messageModal').classList.remove('hidden');
        document.getElementById('messageModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeMessageModal() {
        document.getElementById('messageModal').classList.add('hidden');
        document.getElementById('messageModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Requirements functions
    function openAddRequirementModal() {
        document.getElementById('addRequirementForm').reset();
        document.getElementById('addRequirementModal').classList.remove('hidden');
        document.getElementById('addRequirementModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddRequirementModal() {
        document.getElementById('addRequirementModal').classList.add('hidden');
        document.getElementById('addRequirementModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function openEditRequirementModal(id, name, isRequired, isActive) {
        document.getElementById('edit_req_id').value = id;
        document.getElementById('edit_req_name').value = name;
        document.getElementById('edit_is_required').checked = isRequired;
        document.getElementById('edit_is_active').checked = isActive;
        document.getElementById('editRequirementForm').action = '/staff/requirements/' + id;
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
            text: `Are you sure you want to delete "${name}"?`,
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
                form.action = '/staff/requirements/' + id;
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

    // Verified Students functions
    function openUploadVerifiedModal() {
        document.getElementById('uploadVerifiedForm').reset();
        document.getElementById('uploadVerifiedModal').classList.remove('hidden');
        document.getElementById('uploadVerifiedModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeUploadVerifiedModal() {
        document.getElementById('uploadVerifiedModal').classList.add('hidden');
        document.getElementById('uploadVerifiedModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function openAddVerifiedModal() {
        document.getElementById('addVerifiedForm').reset();
        document.getElementById('addVerifiedModal').classList.remove('hidden');
        document.getElementById('addVerifiedModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddVerifiedModal() {
        document.getElementById('addVerifiedModal').classList.add('hidden');
        document.getElementById('addVerifiedModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function removeVerified(id) {
        Swal.fire({
            title: 'Remove from Verified List?',
            text: 'This student will no longer be automatically approved for clearance.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/staff/verified/' + id;
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

    let currentImageUrl = '', currentStudentName = '', currentFilename = '', attemptUrls = [];

    function openImageViewer(imageUrl, studentName, filename) {
        currentImageUrl = imageUrl; currentStudentName = studentName; currentFilename = filename;
        const baseUrl = window.location.origin;
        attemptUrls = [imageUrl, baseUrl + '/file/' + filename, baseUrl + '/storage/attachments/' + filename, baseUrl + '/attachments/' + filename];
        const spinner = document.getElementById('imageLoadingSpinner');
        const img = document.getElementById('imageViewerSrc');
        const errorMsg = document.getElementById('imageErrorMsg');
        if (spinner) spinner.classList.remove('hidden');
        if (img) { img.classList.add('hidden'); img.src = ''; }
        if (errorMsg) errorMsg.classList.add('hidden');
        document.getElementById('imageViewerTitle').innerHTML = '<i class="fas fa-image text-blue-600 mr-2"></i> ' + studentName + '\'s Attachment';
        document.getElementById('downloadImageBtn').href = imageUrl;
        document.getElementById('imageViewerModal').classList.remove('hidden');
        document.getElementById('imageViewerModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
        tryLoadImage(0);
    }
    function tryLoadImage(index) {
        if (index >= attemptUrls.length) {
            document.getElementById('imageLoadingSpinner').classList.add('hidden');
            document.getElementById('imageErrorMsg').classList.remove('hidden');
            document.getElementById('imageErrorDetail').innerHTML = 'Tried all possible paths.<br>Filename: ' + currentFilename;
            return;
        }
        const testImg = new Image();
        testImg.onload = function() {
            const img = document.getElementById('imageViewerSrc');
            img.src = attemptUrls[index];
            document.getElementById('imageLoadingSpinner').classList.add('hidden');
            img.classList.remove('hidden');
            document.getElementById('imageErrorMsg').classList.add('hidden');
            document.getElementById('downloadImageBtn').href = attemptUrls[index];
        };
        testImg.onerror = function() { tryLoadImage(index + 1); };
        testImg.src = attemptUrls[index];
    }
    function retryLoadImage() {
        document.getElementById('imageLoadingSpinner').classList.remove('hidden');
        document.getElementById('imageViewerSrc').classList.add('hidden');
        document.getElementById('imageErrorMsg').classList.add('hidden');
        tryLoadImage(0);
    }
    function closeImageViewer() {
        document.getElementById('imageViewerModal').classList.add('hidden');
        document.getElementById('imageViewerModal').classList.remove('flex');
        document.getElementById('imageViewerSrc').src = '';
        document.body.style.overflow = '';
    }

    document.getElementById('approveModal')?.addEventListener('click', function(e) { if (e.target === this) closeApproveModal(); });
    document.getElementById('rejectModal')?.addEventListener('click', function(e) { if (e.target === this) closeRejectModal(); });
    document.getElementById('addRequirementModal')?.addEventListener('click', function(e) { if (e.target === this) closeAddRequirementModal(); });
    document.getElementById('editRequirementModal')?.addEventListener('click', function(e) { if (e.target === this) closeEditRequirementModal(); });
    document.getElementById('uploadVerifiedModal')?.addEventListener('click', function(e) { if (e.target === this) closeUploadVerifiedModal(); });
    document.getElementById('addVerifiedModal')?.addEventListener('click', function(e) { if (e.target === this) closeAddVerifiedModal(); });
    document.getElementById('messageModal')?.addEventListener('click', function(e) { if (e.target === this) closeMessageModal(); });
    document.getElementById('imageViewerModal')?.addEventListener('click', function(e) { if (e.target === this) closeImageViewer(); });
    document.addEventListener('keydown', function(e) { 
        if (e.key === 'Escape') { 
            closeImageViewer(); 
            closeApproveModal(); 
            closeRejectModal(); 
            closeAddRequirementModal(); 
            closeEditRequirementModal(); 
            closeUploadVerifiedModal(); 
            closeAddVerifiedModal(); 
            closeMessageModal();
        } 
    });

    const style = document.createElement('style');
    style.textContent = `@keyframes spin { to { transform: rotate(360deg); } } .animate-spin { animation: spin 0.8s linear infinite; } .tab-btn { transition: all 0.2s ease; }`;
    document.head.appendChild(style);
</script>
@endsection