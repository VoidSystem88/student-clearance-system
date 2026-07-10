@extends('layouts.app')

@section('title', 'Clearance Status')
@section('header', 'Clearance Status')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-600 text-sm">Approved</p>
                <p class="text-2xl font-bold text-green-700">{{ $approvedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-600 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount ?? 0 }}</p>
            </div>
            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-600 text-sm">Rejected</p>
                <p class="text-2xl font-bold text-red-700">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-times-circle text-red-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Not Submitted</p>
                <p class="text-2xl font-bold text-gray-700">{{ $notSubmittedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-hourglass-start text-gray-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-2">
        <span class="font-semibold text-gray-700">Overall Progress</span>
        <span class="text-sm text-gray-500">{{ $approvedCount ?? 0 }}/{{ $totalDepartments ?? 0 }} cleared</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ isset($totalDepartments) && $totalDepartments > 0 ? ($approvedCount ?? 0) / $totalDepartments * 100 : 0 }}%"></div>
    </div>
    @if(isset($isFullyCleared) && $isFullyCleared)
        <div class="mt-4 text-center">
            <a href="{{ route('student.clearance.print') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-download"></i> Download Clearance Slip
            </a>
        </div>
    @endif
</div>

<!-- Departments Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Department Clearance Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Department</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Processed Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Remarks</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($departments ?? [] as $department)
                    @php
                        $request = $clearanceRequests->get($department->id) ?? null;
                        $needsPhoto = $request && $request->status == 'pending' && !$request->attachment_path;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $department->name }}</td>
                        <td class="px-5 py-3">
                            @if($request && $request->status == 'approved')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @elseif($request && $request->status == 'rejected')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @elseif($needsPhoto)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                    <i class="fas fa-camera"></i> Needs Photo
                                </span>
                            @elseif($request && $request->status == 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-hourglass-start"></i> Not Submitted
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request && $request->submitted_at ? $request->submitted_at->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request && $request->processed_at ? $request->processed_at->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">
                            @if($needsPhoto)
                                <span class="text-orange-600 text-xs">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Photo proof required
                                </span>
                            @else
                                {{ $request ? ($request->remarks ?? '—') : '—' }}
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            @if(!$request || ($request && $request->status == 'rejected'))
                                <button onclick="confirmSubmit({{ $department->id }}, '{{ addslashes($department->name) }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs hover:bg-blue-700 transition">
                                    <i class="fas fa-paper-plane"></i> Submit
                                </button>
                            @elseif($needsPhoto)
                                <button onclick="uploadPhotoProof({{ $department->id }}, {{ $request->id }}, '{{ addslashes($department->name) }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-500 text-white rounded-lg text-xs hover:bg-orange-600 transition animate-pulse">
                                    <i class="fas fa-camera"></i> Upload Photo
                                </button>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                            <i class="fas fa-building text-4xl mb-2 text-gray-300"></i>
                            <p>No departments available</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ CONFIRMATION MODAL - "Are you in the list?" -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-sm shadow-2xl">
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-clipboard-check text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Confirm Submission</h3>
            <p class="text-sm text-gray-500 mt-1" id="confirmDeptName"></p>
        </div>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-3 mb-4">
            <p class="text-sm text-yellow-700 dark:text-yellow-400">
                <i class="fas fa-info-circle mr-1"></i> 
                You can submit without photo proof. However, if you're <strong>not on the verified list</strong>, 
                your request will remain pending until you upload a photo.
            </p>
        </div>
        
        <div class="space-y-2">
            <button onclick="submitNow({{ isset($department) ? $department->id : 0 }}, true)" 
                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-check-circle"></i> Yes, I'm in the event list
            </button>
            <button onclick="submitNow({{ isset($department) ? $department->id : 0 }}, false)" 
                class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane"></i> Submit without photo
            </button>
            <button onclick="closeConfirmModal()" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- ✅ UPLOAD PHOTO MODAL -->
<div id="uploadPhotoModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-md shadow-2xl">
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/50 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-camera text-orange-600 text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Upload Photo Proof</h3>
            <p class="text-sm text-gray-500 mt-1" id="uploadDeptName"></p>
        </div>
        
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-3 mb-4">
            <p class="text-sm text-red-700 dark:text-red-400">
                <i class="fas fa-exclamation-circle mr-1"></i> 
                Your request needs photo proof for verification. Please upload a photo showing your attendance.
            </p>
        </div>
        
        <form id="uploadPhotoForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="request_id" id="uploadRequestId">
            
            <div class="mb-4">
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-orange-500 transition cursor-pointer" 
                     onclick="document.getElementById('photoInput').click()">
                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-500">Click to upload photo</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 5MB)</p>
                    <input type="file" name="attachment" id="photoInput" class="hidden" accept="image/*" onchange="previewUploadPhoto(this)">
                </div>
                <div id="uploadPhotoPreview" class="mt-2 hidden">
                    <img id="uploadPreviewImg" src="" class="w-full h-40 object-cover rounded-lg">
                </div>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t dark:border-gray-700">
                <button type="button" onclick="closeUploadModal()" 
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="submit" id="uploadSubmitBtn" disabled
                    class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center gap-2">
                    <i class="fas fa-upload"></i> Upload & Submit
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let currentDeptId = null;
    let currentDeptName = '';
    
    // ============ CONFIRM MODAL ============
    function confirmSubmit(deptId, deptName) {
        currentDeptId = deptId;
        currentDeptName = deptName;
        document.getElementById('confirmDeptName').textContent = 'Submit clearance for ' + deptName + '?';
        document.getElementById('confirmModal').classList.remove('hidden');
        document.getElementById('confirmModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        document.getElementById('confirmModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // ============ SUBMIT ============
    async function submitNow(deptId, inEventList) {
        closeConfirmModal();
        
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('department_id', currentDeptId);
        formData.append('in_event_list', inEventList ? '1' : '0');
        
        Swal.fire({
            title: 'Submitting...',
            text: 'Please wait',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        try {
            const response = await fetch('{{ route("student.clearance.submit") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.auto_approved) {
                    Swal.fire({
                        title: '🎉 Auto-Approved!',
                        text: 'You were on the verified list. Your clearance has been automatically approved!',
                        icon: 'success',
                        confirmButtonColor: '#22c55e'
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        title: '✅ Submitted!',
                        html: 'Your request has been submitted.<br><br><strong class="text-orange-600">⚠️ Please upload photo proof</strong> for faster verification.',
                        icon: 'info',
                        confirmButtonColor: '#3b82f6',
                        confirmButtonText: 'Upload Photo Now'
                    }).then((result) => {
                        if (result.isConfirmed && data.request_id) {
                            uploadPhotoProof(currentDeptId, data.request_id, currentDeptName);
                        } else {
                            location.reload();
                        }
                    });
                }
            } else {
                Swal.fire({ title: 'Error', text: data.message || 'Submission failed.', icon: 'error' });
            }
        } catch (error) {
            Swal.fire({ title: 'Error', text: 'Network error. Please try again.', icon: 'error' });
        }
    }
    
    // ============ UPLOAD PHOTO MODAL ============
    function uploadPhotoProof(deptId, requestId, deptName) {
        document.getElementById('uploadDeptName').textContent = 'Upload photo for ' + deptName;
        document.getElementById('uploadRequestId').value = requestId;
        document.getElementById('photoInput').value = '';
        document.getElementById('uploadPhotoPreview').classList.add('hidden');
        document.getElementById('uploadSubmitBtn').disabled = true;
        
        document.getElementById('uploadPhotoModal').classList.remove('hidden');
        document.getElementById('uploadPhotoModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function previewUploadPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('uploadPreviewImg').src = e.target.result;
                document.getElementById('uploadPhotoPreview').classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
            document.getElementById('uploadSubmitBtn').disabled = false;
        }
    }
    
    function closeUploadModal() {
        document.getElementById('uploadPhotoModal').classList.add('hidden');
        document.getElementById('uploadPhotoModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // ============ UPLOAD PHOTO FORM SUBMIT ============
    document.getElementById('uploadPhotoForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const requestId = document.getElementById('uploadRequestId').value;
        
        Swal.fire({
            title: 'Uploading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        
        try {
            const response = await fetch(`/student/clearance/${requestId}/add-photo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    title: 'Photo Uploaded!',
                    text: 'Your photo proof has been submitted for verification.',
                    icon: 'success',
                    confirmButtonColor: '#3b82f6'
                }).then(() => location.reload());
            } else {
                Swal.fire({ title: 'Error', text: data.message || 'Upload failed.', icon: 'error' });
            }
        } catch (error) {
            Swal.fire({ title: 'Error', text: 'Network error.', icon: 'error' });
        }
    });
    
    // Close modals
    document.getElementById('confirmModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeConfirmModal();
    });
    document.getElementById('uploadPhotoModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeUploadModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeConfirmModal(); closeUploadModal(); }
    });
</script>
@endsection