@extends('layouts.staff')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.15); }
    .tab-btn { transition: all 0.3s ease; cursor: pointer; }
    .tab-btn:hover { color: #374151; }
    .tab-btn.active { color: #3b82f6; border-bottom-color: #3b82f6; }
    .report-card { transition: all 0.3s ease; }
    .report-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1); }
    .btn-approve:hover, .btn-reject:hover, .btn-delete:hover, .btn-export:hover, .btn-edit:hover { transform: scale(1.02); }
    .table-row:hover { background-color: #f8fafc; }
    #photoModal img { transition: transform 0.3s ease; }
    #photoModal img:hover { transform: scale(1.02); }
</style>
@endpush

@section('content')

{{-- STATS CARDS --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex justify-between items-start">
            <div><p class="text-gray-500 text-sm">Total Requests</p><p class="text-2xl font-bold text-blue-600 mt-1">{{ ($pendingCount ?? 0) + ($approvedCount ?? 0) + ($rejectedCount ?? 0) }}</p></div>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center"><i class="fas fa-file-alt text-blue-600"></i></div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex justify-between items-start">
            <div><p class="text-gray-500 text-sm">Pending</p><p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingCount ?? 0 }}</p></div>
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center"><i class="fas fa-clock text-yellow-600"></i></div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex justify-between items-start">
            <div><p class="text-gray-500 text-sm">Approved</p><p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedCount ?? 0 }}</p></div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-check-circle text-green-600"></i></div>
        </div>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <div class="flex justify-between items-start">
            <div><p class="text-gray-500 text-sm">Rejected</p><p class="text-2xl font-bold text-red-600 mt-1">{{ $rejectedCount ?? 0 }}</p></div>
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-times-circle text-red-600"></i></div>
        </div>
    </div>
</div>

{{-- TABS --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="border-b border-gray-200 px-4 pt-3 flex flex-wrap gap-1 overflow-x-auto">
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap active" data-tab="pending-tab" style="color: #3b82f6; border-bottom-color: #3b82f6;">
            <i class="fas fa-clock text-yellow-500 mr-1"></i> Pending <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $pendingCount ?? 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap" data-tab="approved-tab">
            <i class="fas fa-check-circle text-green-500 mr-1"></i> Approved <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $approvedCount ?? 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap" data-tab="reports-tab">
            <i class="fas fa-file-alt text-purple-500 mr-1"></i> Reports <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ isset($allReports) ? $allReports->count() : 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap" data-tab="requirements-tab">
            <i class="fas fa-list-check text-blue-500 mr-1"></i> Requirements <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ isset($requirements) ? $requirements->count() : 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap" data-tab="rejected-tab">
            <i class="fas fa-times-circle text-red-500 mr-1"></i> Rejected <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $rejectedCount ?? 0 }}</span>
        </button>
    </div>

    <div class="p-4">

        {{-- PENDING TAB --}}
        <div id="pending-tab" class="tab-content">
            @if(($pendingRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            <tr><th class="px-4 py-3">Student</th><th class="px-4 py-3">Student ID</th><th class="px-4 py-3">Course/Year</th><th class="px-4 py-3">Submitted</th><th class="px-4 py-3">Photo Proof</th><th class="px-4 py-3">Action</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pendingRequests as $req)
                            <tr class="table-row transition">
                                <td class="px-4 py-3"><div class="font-medium text-gray-800">{{ $req->student->first_name ?? 'Unknown' }} {{ $req->student->last_name ?? '' }}</div><div class="text-xs text-gray-500">{{ $req->student->email ?? '' }}</div></td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $req->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3"><span class="text-gray-600">{{ $req->student->course ?? 'N/A' }}</span><br><span class="text-xs text-gray-400">{{ $req->student->year_level ?? 'N/A' }}</span></td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->submitted_at ? $req->submitted_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    @if($req->attachment_path)
                                        @php
                                            $path = $req->attachment_path;
                                            // Remove 'public/' prefix if exists
                                            $path = str_replace('public/', '', $path);
                                            // Check if already a full URL
                                            if (!Str::startsWith($path, 'http')) {
                                                $path = asset('storage/' . $path);
                                            }
                                        @endphp
                                        <button onclick="viewPhoto('{{ $path }}')" class="text-blue-600 hover:text-blue-800 transition">
                                            <i class="fas fa-image text-lg"></i><span class="text-xs block">View</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-orange-500"><i class="fas fa-exclamation-triangle"></i> No photo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('staff.approve', $req->id) }}" class="inline">@csrf<button type="submit" class="btn-approve bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium"><i class="fas fa-check mr-1"></i> Approve</button></form>
                                        <button onclick="showRejectModal({{ $req->id }})" class="btn-reject bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium"><i class="fas fa-times mr-1"></i> Reject</button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500"><i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i><p class="text-lg font-medium">No pending requests</p></div>
            @endif
        </div>

        {{-- APPROVED TAB --}}
        <div id="approved-tab" class="tab-content hidden">
            @if(($approvedRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            <tr><th class="px-4 py-3">Student</th><th class="px-4 py-3">Student ID</th><th class="px-4 py-3">Course</th><th class="px-4 py-3">Year</th><th class="px-4 py-3">Approved Date</th><th class="px-4 py-3">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($approvedRequests as $req)
                            <tr class="table-row transition">
                                <td class="px-4 py-3"><div class="font-medium text-gray-800">{{ $req->student->first_name ?? 'Unknown' }} {{ $req->student->last_name ?? '' }}</div><div class="text-xs text-gray-500">{{ $req->student->email ?? '' }}</div></td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $req->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $req->student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $req->student->year_level ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->updated_at ? $req->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3"><span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium"><i class="fas fa-check-circle mr-1"></i> Approved</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500"><i class="fas fa-check-circle text-5xl mb-3 text-gray-300"></i><p class="text-lg font-medium">No approved requests yet</p></div>
            @endif
        </div>

        {{-- REPORTS TAB --}}
        <div id="reports-tab" class="tab-content hidden">
            @if(isset($allReports) && $allReports->count() > 0)
                <div class="mb-4 flex flex-wrap justify-between items-center gap-2"><p class="text-sm text-gray-500"><i class="fas fa-info-circle text-purple-500 mr-1"></i> Reports sent by officers</p><span class="text-xs bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-medium"><i class="fas fa-file mr-1"></i> Total: {{ $allReports->count() }}</span></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($allReports as $report)
                    <div class="report-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-200 flex justify-between items-start @if($report->status == 'pending') bg-yellow-50 @elseif($report->status == 'approved') bg-green-50 @else bg-red-50 @endif">
                            <div class="flex-1 min-w-0"><h4 class="font-semibold text-gray-800 truncate">{{ $report->report_title ?? 'Untitled Report' }}</h4><p class="text-xs text-gray-500"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y h:i A') }}</p></div>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium @if($report->status == 'pending') bg-yellow-200 text-yellow-800 @elseif($report->status == 'approved') bg-green-200 text-green-800 @else bg-red-200 text-red-800 @endif"><i class="fas @if($report->status == 'pending') fa-clock @elseif($report->status == 'approved') fa-check-circle @else fa-times-circle @endif text-xs"></i> {{ ucfirst($report->status) }}</span>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div><p class="text-xs text-gray-500">Event</p><p class="font-medium text-gray-800">{{ $report->event_name ?? 'N/A' }}</p></div>
                                <div><p class="text-xs text-gray-500">Students</p><p class="font-medium text-gray-800">{{ $report->total_students ?? 0 }}</p></div>
                                <div class="col-span-2"><p class="text-xs text-gray-500">Sent By</p>@php $officer = App\Models\User::find($report->officer_id); @endphp<p class="font-medium text-gray-800">{{ $officer->name ?? 'Unknown Officer' }}</p></div>
                                @if($report->notes)<div class="col-span-2"><p class="text-xs text-gray-500">Notes</p><p class="text-sm text-gray-600">{{ Str::limit($report->notes, 80) }}</p></div>@endif
                            </div>
                            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap gap-2">
                                @if($report->status == 'pending')
                                    <button onclick="approveReport({{ $report->id }})" class="btn-approve bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1"><i class="fas fa-check"></i> Verify & Approve</button>
                                    <button onclick="rejectReport({{ $report->id }})" class="btn-reject bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1"><i class="fas fa-times"></i> Reject</button>
                                @endif
                                @if($report->status == 'approved')
                                    <button onclick="exportReport({{ $report->id }})" class="btn-export bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1"><i class="fas fa-file-csv"></i> Extract CSV</button>
                                @endif
                                <button onclick="deleteReport({{ $report->id }})" class="btn-delete bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1"><i class="fas fa-trash-alt"></i> Delete</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-gray-500"><i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i><p class="text-lg font-medium">No reports received</p></div>
            @endif
        </div>

        {{-- REQUIREMENTS TAB --}}
        <div id="requirements-tab" class="tab-content hidden">
            <div class="mb-4 flex flex-wrap justify-between items-center gap-2">
                <p class="text-sm text-gray-500"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Manage department requirements for each year level</p>
                <button onclick="openAddRequirementModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2"><i class="fas fa-plus-circle"></i> Add Requirement</button>
            </div>
            @if(isset($requirements) && $requirements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            <tr><th class="px-4 py-3">Year Level</th><th class="px-4 py-3">Requirement</th><th class="px-4 py-3">Required</th><th class="px-4 py-3">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($requirements as $req)
                            <tr class="table-row transition">
                                <td class="px-4 py-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ $req->year_level }}</span></td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $req->requirement_name }}</td>
                                <td class="px-4 py-3">@if($req->is_required)<span class="text-green-600"><i class="fas fa-check-circle"></i> Required</span>@else<span class="text-gray-400">Optional</span>@endif</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button onclick="editRequirement({{ $req->id }}, '{{ addslashes($req->requirement_name) }}', '{{ $req->year_level }}', {{ $req->is_required }})" class="btn-edit bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium"><i class="fas fa-edit"></i></button>
                                        <button onclick="deleteRequirement({{ $req->id }})" class="btn-delete bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500"><i class="fas fa-list-check text-5xl mb-3 text-gray-300"></i><p class="text-lg font-medium">No requirements set</p></div>
            @endif
        </div>

        {{-- REJECTED TAB --}}
        <div id="rejected-tab" class="tab-content hidden">
            @if(($rejectedRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                            <tr><th class="px-4 py-3">Student</th><th class="px-4 py-3">Student ID</th><th class="px-4 py-3">Course</th><th class="px-4 py-3">Rejected Date</th><th class="px-4 py-3">Remarks</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rejectedRequests as $req)
                            <tr class="table-row transition">
                                <td class="px-4 py-3"><div class="font-medium text-gray-800">{{ $req->student->first_name ?? 'Unknown' }} {{ $req->student->last_name ?? '' }}</div></td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $req->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $req->student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $req->updated_at ? $req->updated_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-red-600">{{ $req->remarks ?? 'No remarks' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500"><i class="fas fa-times-circle text-5xl mb-3 text-gray-300"></i><p class="text-lg font-medium">No rejected requests</p></div>
            @endif
        </div>
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-4"><h3 class="text-xl font-bold text-red-600"><i class="fas fa-times-circle mr-2"></i> Reject Request</h3><button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button></div>
        <form id="rejectForm" method="POST">@csrf
            <div class="mb-4"><label class="block text-gray-700 text-sm font-medium mb-2">Reason for rejection</label><textarea name="remarks" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 outline-none" required placeholder="Please provide a reason..."></textarea></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="closeRejectModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</button><button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition"><i class="fas fa-times mr-1"></i> Reject</button></div>
        </form>
    </div>
</div>

{{-- PHOTO VIEW MODAL --}}
<div id="photoModal" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50 p-4">
    <div class="relative max-w-2xl w-full">
        <button onclick="closePhotoModal()" class="absolute -top-10 right-0 text-white text-3xl hover:text-gray-300">&times;</button>
        <img id="photoModalImg" src="" class="w-full rounded-xl shadow-2xl max-h-[80vh] object-contain bg-black">
        <div class="absolute bottom-4 left-0 right-0 text-center"><a id="photoDownloadLink" href="" download class="inline-flex items-center gap-2 bg-white text-gray-800 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition"><i class="fas fa-download"></i> Download</a></div>
    </div>
</div>

{{-- REQUIREMENT MODAL --}}
<div id="requirementModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-4"><h3 id="reqModalTitle" class="text-xl font-bold text-blue-600"><i class="fas fa-list-check mr-2"></i> Add Requirement</h3><button onclick="closeRequirementModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button></div>
        <form id="requirementForm">@csrf<input type="hidden" id="reqId">
            <div class="mb-3"><label class="block text-gray-700 text-sm font-medium mb-1">Year Level</label><select id="reqYearLevel" class="w-full border border-gray-300 rounded-lg px-3 py-2" required><option value="1st Year">1st Year</option><option value="2nd Year">2nd Year</option><option value="3rd Year">3rd Year</option><option value="4th Year">4th Year</option></select></div>
            <div class="mb-3"><label class="block text-gray-700 text-sm font-medium mb-1">Requirement Name</label><input type="text" id="reqName" class="w-full border border-gray-300 rounded-lg px-3 py-2" required placeholder="e.g., Photocopy of ID"></div>
            <div class="mb-4"><label class="flex items-center gap-2"><input type="checkbox" id="reqIsRequired" class="w-4 h-4 text-blue-600 rounded" checked> <span class="text-sm text-gray-700">Required</span></label></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="closeRequirementModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</button><button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition"><i class="fas fa-save mr-1"></i> Save</button></div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            tabButtons.forEach(btn => { btn.style.color = ''; btn.style.borderBottomColor = ''; btn.classList.remove('active'); });
            this.style.color = '#3b82f6'; this.style.borderBottomColor = '#3b82f6'; this.classList.add('active');
            tabContents.forEach(content => content.classList.add('hidden'));
            document.getElementById(targetTab)?.classList.remove('hidden');
        });
    });

    function getCsrfToken() { return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'); }

    function showRejectModal(id) { document.getElementById('rejectForm').action = `/staff/reject/${id}`; document.getElementById('rejectModal').classList.remove('hidden'); document.getElementById('rejectModal').classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function closeRejectModal() { document.getElementById('rejectModal').classList.add('hidden'); document.getElementById('rejectModal').classList.remove('flex'); document.body.style.overflow = ''; }

    function viewPhoto(url) { document.getElementById('photoModalImg').src = url; document.getElementById('photoDownloadLink').href = url; document.getElementById('photoModal').classList.remove('hidden'); document.getElementById('photoModal').classList.add('flex'); document.body.style.overflow = 'hidden'; }
    function closePhotoModal() { document.getElementById('photoModal').classList.add('hidden'); document.getElementById('photoModal').classList.remove('flex'); document.body.style.overflow = ''; }

    function openAddRequirementModal() {
        document.getElementById('reqModalTitle').innerHTML = '<i class="fas fa-plus-circle mr-2 text-blue-600"></i> Add Requirement';
        document.getElementById('reqId').value = '';
        document.getElementById('reqName').value = '';
        document.getElementById('reqYearLevel').value = '1st Year';
        document.getElementById('reqIsRequired').checked = true;
        document.getElementById('requirementForm').action = '{{ route("staff.requirements.store") }}';
        document.getElementById('requirementModal').classList.remove('hidden');
        document.getElementById('requirementModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function editRequirement(id, name, yearLevel, isRequired) {
        document.getElementById('reqModalTitle').innerHTML = '<i class="fas fa-edit mr-2 text-yellow-600"></i> Edit Requirement';
        document.getElementById('reqId').value = id;
        document.getElementById('reqName').value = name;
        document.getElementById('reqYearLevel').value = yearLevel;
        document.getElementById('reqIsRequired').checked = isRequired == 1;
        document.getElementById('requirementForm').action = `/staff/requirements/${id}`;
        document.getElementById('requirementModal').classList.remove('hidden');
        document.getElementById('requirementModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeRequirementModal() { document.getElementById('requirementModal').classList.add('hidden'); document.getElementById('requirementModal').classList.remove('flex'); document.body.style.overflow = ''; }
    
    document.getElementById('requirementForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('reqId').value;
        const url = id ? `/staff/requirements/${id}` : '{{ route("staff.requirements.store") }}';
        const formData = new FormData();
        formData.append('_token', getCsrfToken());
        formData.append('requirement_name', document.getElementById('reqName').value);
        formData.append('year_level', document.getElementById('reqYearLevel').value);
        formData.append('is_required', document.getElementById('reqIsRequired').checked ? 1 : 0);
        if (id) formData.append('_method', 'PUT');
        
        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' }, body: formData })
        .then(r => r.json()).then(d => {
            if (d.success) { closeRequirementModal(); Swal.fire({ icon: 'success', title: 'Saved!', timer: 1500 }).then(() => location.reload()); }
            else Swal.fire({ icon: 'error', title: 'Error', text: d.message });
        }).catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.' }));
    });

    function deleteRequirement(id) {
        Swal.fire({ title: 'Delete?', text: 'Cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes' }).then(r => {
            if (r.isConfirmed) {
                fetch(`/staff/requirements/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { if (d.success) Swal.fire({ icon: 'success', title: 'Deleted!', timer: 1500 }).then(() => location.reload()); });
            }
        });
    }

    function approveReport(id) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/staff/reports/${id}/approve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: getCsrfToken() }) })
        .then(r => r.json()).then(d => { if (d.success) Swal.fire({ icon: 'success', title: 'Approved!', timer: 2000 }).then(() => location.reload()); });
    }
    function rejectReport(id) {
        Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/staff/reports/${id}/reject`, { method: 'POST', headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ _token: getCsrfToken() }) })
        .then(r => r.json()).then(d => { if (d.success) Swal.fire({ icon: 'success', title: 'Rejected!', timer: 2000 }).then(() => location.reload()); });
    }
    function deleteReport(id) {
        Swal.fire({ title: 'Delete?', text: 'Cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes' }).then(r => {
            if (r.isConfirmed) {
                fetch(`/staff/reports/${id}/delete`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' } })
                .then(r => r.json()).then(d => { if (d.success) Swal.fire({ icon: 'success', title: 'Deleted!', timer: 2000 }).then(() => location.reload()); });
            }
        });
    }
    function exportReport(id) {
        Swal.fire({ title: 'Exporting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/staff/reports/${id}/export`, { method: 'GET', headers: { 'X-CSRF-TOKEN': getCsrfToken() } })
        .then(r => r.blob()).then(blob => {
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'report.csv'; a.click();
            Swal.fire({ icon: 'success', title: 'Exported!', timer: 1500 });
        });
    }

    document.getElementById('rejectModal')?.addEventListener('click', function(e) { if (e.target === this) closeRejectModal(); });
    document.getElementById('photoModal')?.addEventListener('click', function(e) { if (e.target === this) closePhotoModal(); });
    document.getElementById('requirementModal')?.addEventListener('click', function(e) { if (e.target === this) closeRequirementModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') { closeRejectModal(); closePhotoModal(); closeRequirementModal(); } });
</script>
@endpush