@extends('layouts.staff')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- STATS CARDS -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Total Requests</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ ($pendingCount ?? 0) + ($approvedCount ?? 0) + ($rejectedCount ?? 0) }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Approved</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $approvedCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm">Rejected</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- ============ TABS ============ -->
<div class="bg-white rounded-xl shadow-sm">
    <!-- Tab Buttons -->
    <div class="border-b border-gray-200 px-4 pt-3 flex flex-wrap gap-1 overflow-x-auto">
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap active" 
                data-tab="pending-tab"
                style="color: #3b82f6; border-bottom-color: #3b82f6;">
            <i class="fas fa-clock text-yellow-500 mr-1"></i> Pending
            <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $pendingCount ?? 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap"
                data-tab="approved-tab">
            <i class="fas fa-check-circle text-green-500 mr-1"></i> Approved
            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $approvedCount ?? 0 }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap"
                data-tab="verified-tab">
            <i class="fas fa-clipboard-check text-blue-500 mr-1"></i> Verified by Officer
            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ ($verifiedStudents ?? collect([]))->count() }}</span>
        </button>
        <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap"
                data-tab="rejected-tab">
            <i class="fas fa-times-circle text-red-500 mr-1"></i> Rejected
            <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $rejectedCount ?? 0 }}</span>
        </button>
    </div>

    <!-- Tab Contents -->
    <div class="p-4">
        {{-- PENDING TAB --}}
        <div id="pending-tab" class="tab-content">
            @if(($pendingRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Course</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Submitted</th>
                                <th class="px-4 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pendingRequests as $request)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $request->student->first_name ?? 'Unknown' }} {{ $request->student->last_name ?? '' }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->student->email ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $request->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $request->student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $request->student->year_level ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $request->submitted_at ? $request->submitted_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('staff.approve', $request->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $request->id }})" 
                                                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                            <i class="fas fa-times mr-1"></i> Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-inbox text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No pending requests</p>
                    <p class="text-sm">All caught up! 🎉</p>
                </div>
            @endif
        </div>

        {{-- APPROVED TAB --}}
        <div id="approved-tab" class="tab-content hidden">
            @if(($approvedRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Course</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Approved Date</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($approvedRequests as $request)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $request->student->first_name ?? 'Unknown' }} {{ $request->student->last_name ?? '' }}</div>
                                    <div class="text-xs text-gray-500">{{ $request->student->email ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $request->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $request->student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $request->student->year_level ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $request->updated_at ? $request->updated_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-check-circle text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No approved requests yet</p>
                </div>
            @endif
        </div>

        {{-- VERIFIED BY OFFICER TAB --}}
        <div id="verified-tab" class="tab-content hidden">
            @if(($verifiedStudents ?? collect([]))->count() > 0)
                <div class="mb-4 flex flex-wrap justify-between items-center gap-2">
                    <p class="text-sm text-gray-500">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                        Students verified by Officer (imported from CSV)
                    </p>
                    <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                        <i class="fas fa-users mr-1"></i> Total: {{ $verifiedStudents->count() }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3 w-12">#</th>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Course</th>
                                <th class="px-4 py-3">Year</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($verifiedStudents as $index => $student)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $student->year_level ?? 'N/A' }}</td>
                                <td class="px-4 py-3">
                                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                        <i class="fas fa-clipboard-check mr-1"></i> Verified
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-file-import text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No verified students</p>
                    <p class="text-sm">Import CSV from Officer to see verified students here</p>
                </div>
            @endif
        </div>

        {{-- REJECTED TAB --}}
        <div id="rejected-tab" class="tab-content hidden">
            @if(($rejectedRequests ?? collect([]))->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">Student</th>
                                <th class="px-4 py-3">Student ID</th>
                                <th class="px-4 py-3">Course</th>
                                <th class="px-4 py-3">Rejected Date</th>
                                <th class="px-4 py-3">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rejectedRequests as $request)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-800">{{ $request->student->first_name ?? 'Unknown' }} {{ $request->student->last_name ?? '' }}</div>
                                </td>
                                <td class="px-4 py-3 font-mono text-gray-600">{{ $request->student->student_id ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $request->student->course ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">{{ $request->updated_at ? $request->updated_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-3 text-xs text-red-600">{{ $request->remarks ?? 'No remarks' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-times-circle text-5xl mb-3 text-gray-300"></i>
                    <p class="text-lg font-medium">No rejected requests</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- REJECT MODAL -->
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-red-600">
                <i class="fas fa-times-circle mr-2"></i> Reject Request
            </h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Reason for rejection</label>
                <textarea name="remarks" rows="4" 
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" 
                    required placeholder="Please provide a reason..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRejectModal()" 
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                    <i class="fas fa-times mr-1"></i> Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============ TAB SWITCHING ============
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active from all buttons
            tabButtons.forEach(btn => {
                btn.style.color = '#6b7280';
                btn.style.borderBottomColor = 'transparent';
                btn.classList.remove('active');
            });
            
            // Set active on clicked button
            this.style.color = '#3b82f6';
            this.style.borderBottomColor = '#3b82f6';
            this.classList.add('active');
            
            // Hide all tabs, show target
            tabContents.forEach(content => content.classList.add('hidden'));
            const targetContent = document.getElementById(targetTab);
            if (targetContent) targetContent.classList.remove('hidden');
        });
    });

    // ============ REJECT MODAL ============
    function showRejectModal(requestId) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/staff/reject/${requestId}`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('rejectModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeRejectModal();
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeRejectModal();
    });
</script>
@endpush