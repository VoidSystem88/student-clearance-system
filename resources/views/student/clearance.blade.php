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
        <p class="text-sm text-gray-500">Submit requirements to each department</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Department</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($departments ?? [] as $department)
                    @php
                        $request = $clearanceRequests->get($department->id) ?? null;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $department->name }}</td>
                        <td class="px-5 py-3">
    @if($request && $request->status == 'approved')
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
            <i class="fas fa-check-circle"></i> Approved
        </span>
    @elseif($request && $request->status == 'rejected')
        <div>
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                <i class="fas fa-times-circle"></i> Rejected
            </span>
            @if($request->remarks)
                <div class="mt-2 p-2 bg-red-50 rounded-lg text-xs text-red-600">
                    <i class="fas fa-comment-dots mr-1"></i> 
                    <strong>Reason:</strong> {{ $request->remarks }}
                </div>
            @endif
        </div>
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
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request ? $request->submitted_at->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex flex-col gap-2">
                                <!-- View Requirements Button -->
                                <button onclick="viewRequirements({{ $department->id }}, '{{ addslashes($department->name) }}')" 
                                        class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition">
                                    <i class="fas fa-list-check"></i> View Requirements
                                </button>
                                
                                <!-- Submit Button -->
                                @if(!$request || $request->status == 'rejected')
                                <button onclick="showSubmitForm({{ $department->id }}, '{{ addslashes($department->name) }}')" 
                                        class="inline-flex items-center justify-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-upload"></i> Submit
                                </button>
                                @elseif($request && $request->status == 'pending')
                                    <span class="text-gray-400 text-sm text-center">Waiting for review...</span>
                                @elseif($request && $request->status == 'approved')
                                    <span class="text-green-600 text-sm text-center"><i class="fas fa-check-circle"></i> Completed</span>
                                @endif
                            </div>
                        </td>
                    <tr>
                @empty
                <tr>
                    <td colspan="4" class="px-5 py-8 text-center text-gray-500">No departments available</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Submit Modal with Request Message -->
<div id="submitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4" style="display: none;">
    <div class="bg-white rounded-xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Submit Clearance Request</h3>
            <button onclick="hideSubmitForm()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="clearanceForm" method="POST" action="{{ route('student.clearance.submit') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="department_id" id="departmentId">
            
            <!-- Upload Method Toggle -->
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 font-medium">Choose Upload Method</label>
                <div class="flex gap-3">
                    <button type="button" onclick="toggleMethod('file')" id="fileMethodBtn" class="flex-1 py-2 px-3 rounded-lg font-medium transition-all duration-200 bg-blue-600 text-white">
                        <i class="fas fa-folder-open mr-2"></i> Upload File
                    </button>
                    <button type="button" onclick="toggleMethod('camera')" id="cameraMethodBtn" class="flex-1 py-2 px-3 rounded-lg font-medium transition-all duration-200 bg-gray-200 text-gray-700">
                        <i class="fas fa-camera mr-2"></i> Take Photo
                    </button>
                </div>
            </div>
            
            <!-- File Upload Method -->
            <div id="fileMethod" class="mb-4">
                <label class="block text-gray-700 mb-2">Upload Proof/Receipt</label>
                
                <!-- Hidden file input -->
                <input type="file" name="attachment" id="attachment" class="hidden" accept=".pdf,.jpg,.png,.jpeg" onchange="validateFileSize(this)">
                
                <!-- Button na trigger ng file upload -->
                <button type="button" onclick="document.getElementById('attachment').click()" class="w-full py-2 px-3 rounded-lg font-medium transition-all duration-200 bg-blue-600 text-white">
                    <i class="fas fa-folder-open mr-2"></i> Choose File
                </button>
                
                <!-- Display selected file name -->
                <div id="selectedFileName" class="text-sm text-gray-500 mt-2 hidden"></div>
                
                <p class="text-xs text-gray-500 mt-1">Accepted: PDF, JPG, PNG (Max 5MB)</p>
                <div id="fileSizeWarning" class="hidden text-red-500 text-xs mt-1"></div>
            </div>
            
            <!-- Camera Method -->
            <div id="cameraMethod" class="mb-4 hidden">
                <label class="block text-gray-700 mb-2">Take Photo</label>
                
                <div id="cameraContainer">
                    <div class="bg-gray-100 rounded-lg overflow-hidden">
                        <video id="video" class="w-full h-auto" autoplay playsinline></video>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button type="button" onclick="switchCamera()" class="flex-1 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                            <i class="fas fa-sync-alt mr-2"></i> Switch Camera
                        </button>
                        <button type="button" onclick="capturePhoto()" class="flex-1 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            <i class="fas fa-camera mr-2"></i> Capture
                        </button>
                    </div>
                </div>
                
                <canvas id="canvas" class="hidden"></canvas>
                
                <div id="previewContainer" class="hidden mt-3">
                    <label class="block text-gray-700 mb-2">Preview:</label>
                    <img id="previewImage" src="" class="w-full rounded-lg border-2 border-green-500/50 mb-3">
                    <div class="flex gap-2">
                        <button type="button" onclick="retakePhoto()" class="flex-1 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-redo mr-2"></i> Retake
                        </button>
                        <button type="button" onclick="usePhoto()" class="flex-1 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i> Use This Photo
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- ============ REQUEST MESSAGE SECTION (BAGO) ============ -->
            <div class="mb-4 pt-2">
                <label class="block text-gray-700 mb-2 font-medium">
                    <i class="fas fa-comment-dots mr-1"></i> Request Message (Optional)
                </label>
                <textarea name="request_message" id="request_message" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                          placeholder="e.g., Wala po akong proof pero confident ako na kasama ako sa list. Salamat po!"></textarea>
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i> 
                    You can add a message if you don't have proof of clearance. Staff will review your request.
                </p>
            </div>
            
            <div class="flex justify-end gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="hideSubmitForm()" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</button>
                <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Requirements Modal -->
<div id="requirementsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="requirementsModalTitle" class="text-xl font-bold text-gray-800">
                <i class="fas fa-list-check text-blue-600 mr-2"></i> Department Requirements
            </h3>
            <button onclick="closeRequirementsModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div id="requirementsList" class="space-y-2 max-h-96 overflow-y-auto">
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        </div>
        
        <div class="mt-4 flex justify-end">
            <button onclick="closeRequirementsModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm hover:bg-gray-400 transition">Close</button>
        </div>
    </div>
</div>

<script>
    // ============ TOAST NOTIFICATION ============
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed top-20 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-all duration-300`;
        toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + ' mr-2"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // ============ REQUIREMENTS MODAL FUNCTIONS ============
    function viewRequirements(departmentId, departmentName) {
        document.getElementById('requirementsModalTitle').innerHTML = 
            `<i class="fas fa-list-check text-blue-600 mr-2"></i> ${departmentName} Requirements`;
        
        const container = document.getElementById('requirementsList');
        container.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-spinner fa-spin"></i> Loading requirements...</div>';
        
        fetch(`/api/departments/${departmentId}/requirements`)
            .then(response => response.json())
            .then(data => {
                if(data.requirements && data.requirements.length > 0) {
                    let html = '<ul class="space-y-2">';
                    data.requirements.forEach(req => {
                        html += `
                            <li class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center mt-0.5 flex-shrink-0">
                                    <i class="fas fa-check text-blue-600 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-800 font-medium">${escapeHtml(req.requirement_name)}</p>
                                    ${req.is_required ? 
                                        '<span class="text-xs text-red-500 inline-flex items-center gap-1 mt-1"><i class="fas fa-exclamation-circle"></i> Required</span>' : 
                                        '<span class="text-xs text-gray-400 inline-flex items-center gap-1 mt-1"><i class="fas fa-check-circle"></i> Optional</span>'}
                                </div>
                            </li>
                        `;
                    });
                    html += '</ul>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i><p>No specific requirements for this department.</p><p class="text-sm mt-1">Please contact the department directly.</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-4xl mb-2"></i><p>Failed to load requirements.</p><p class="text-sm">Please try again later.</p></div>';
            });
        
        document.getElementById('requirementsModal').classList.remove('hidden');
        document.getElementById('requirementsModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeRequirementsModal() {
        document.getElementById('requirementsModal').classList.add('hidden');
        document.getElementById('requirementsModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    function escapeHtml(text) {
        if(!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ============ FILE SIZE VALIDATION ============
    function validateFileSize(input) {
        const file = input.files[0];
        const warningDiv = document.getElementById('fileSizeWarning');
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (file && file.size > maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            warningDiv.innerHTML = '❌ File too large: ' + fileSizeMB + 'MB. Maximum is 5MB.';
            warningDiv.classList.remove('hidden');
            input.value = '';
            return false;
        } else {
            warningDiv.classList.add('hidden');
            if (file) {
                const fileSizeKB = (file.size / 1024).toFixed(2);
                showToast('File ready: ' + fileSizeKB + ' KB', 'success');
            }
            return true;
        }
    }
    
    // Show selected file name
    document.getElementById('attachment')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileDiv = document.getElementById('selectedFileName');
        
        if (fileName) {
            fileDiv.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> Selected: ' + fileName;
            fileDiv.classList.remove('hidden');
        } else {
            fileDiv.classList.add('hidden');
        }
    });
    
    // ============ MODAL FUNCTIONS ============
    function showSubmitForm(departmentId, departmentName) {
        document.getElementById('departmentId').value = departmentId;
        document.getElementById('modalTitle').innerHTML = 'Submit to ' + departmentName;
        document.getElementById('submitModal').style.display = 'flex';
        document.getElementById('submitModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Reset to file method
        toggleMethod('file');
        // Clear request message
        document.getElementById('request_message').value = '';
    }
    
    function hideSubmitForm() {
        stopCamera();
        document.getElementById('submitModal').style.display = 'none';
        document.getElementById('submitModal').classList.add('hidden');
        document.body.style.overflow = '';
        // Clear form
        document.getElementById('clearanceForm').reset();
        capturedImageData = null;
        document.getElementById('fileSizeWarning')?.classList.add('hidden');
        document.getElementById('selectedFileName')?.classList.add('hidden');
    }
    
    // ============ TOGGLE METHOD ============
    let currentMethod = 'file';
    
    function toggleMethod(method) {
        currentMethod = method;
        
        if (method === 'file') {
            document.getElementById('fileMethod').classList.remove('hidden');
            document.getElementById('cameraMethod').classList.add('hidden');
            document.getElementById('fileMethodBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('fileMethodBtn').classList.add('bg-blue-600', 'text-white');
            document.getElementById('cameraMethodBtn').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('cameraMethodBtn').classList.add('bg-gray-200', 'text-gray-700');
            stopCamera();
        } else {
            document.getElementById('fileMethod').classList.add('hidden');
            document.getElementById('cameraMethod').classList.remove('hidden');
            document.getElementById('cameraMethodBtn').classList.remove('bg-gray-200', 'text-gray-700');
            document.getElementById('cameraMethodBtn').classList.add('bg-blue-600', 'text-white');
            document.getElementById('fileMethodBtn').classList.remove('bg-blue-600', 'text-white');
            document.getElementById('fileMethodBtn').classList.add('bg-gray-200', 'text-gray-700');
            startCamera();
        }
    }
    
    // ============ CAMERA FUNCTIONS ============
    let video = null;
    let canvas = null;
    let stream = null;
    let currentCamera = 'user';
    let capturedImageData = null;
    
    async function startCamera() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        
        try {
            if (stream) stopCamera();
            
            const constraints = {
                video: { 
                    facingMode: { exact: currentCamera },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };
            
            try {
                stream = await navigator.mediaDevices.getUserMedia(constraints);
            } catch (err) {
                const fallbackConstraints = { video: true };
                stream = await navigator.mediaDevices.getUserMedia(fallbackConstraints);
            }
            
            if (video) {
                video.srcObject = stream;
                video.onloadedmetadata = () => video.play();
            }
        } catch (err) {
            console.error('Camera error:', err);
            showToast('Cannot access camera. Please use file upload instead.', 'error');
            toggleMethod('file');
        }
    }
    
    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        if (video) video.srcObject = null;
    }
    
    function switchCamera() {
        currentCamera = currentCamera === 'user' ? 'environment' : 'user';
        startCamera();
    }
    
    function capturePhoto() {
        if (!video || !canvas) return;
        
        const context = canvas.getContext('2d');
        
        const maxWidth = 1024;
        const maxHeight = 1024;
        let width = video.videoWidth;
        let height = video.videoHeight;
        
        if (width > maxWidth) {
            height = (height * maxWidth) / width;
            width = maxWidth;
        }
        if (height > maxHeight) {
            width = (width * maxHeight) / height;
            height = maxHeight;
        }
        
        canvas.width = width;
        canvas.height = height;
        context.drawImage(video, 0, 0, width, height);
        
        canvas.toBlob(function(blob) {
            if (blob.size > 2 * 1024 * 1024) {
                canvas.toBlob(function(compressedBlob) {
                    capturedImageData = compressedBlob;
                    const url = URL.createObjectURL(compressedBlob);
                    const previewImg = document.getElementById('previewImage');
                    previewImg.src = url;
                    
                    const fileSizeKB = (compressedBlob.size / 1024).toFixed(2);
                    showToast(`Photo captured: ${fileSizeKB} KB`, 'success');
                    
                    document.getElementById('cameraContainer').classList.add('hidden');
                    document.getElementById('previewContainer').classList.remove('hidden');
                    stopCamera();
                }, 'image/jpeg', 0.5);
            } else {
                capturedImageData = blob;
                const url = URL.createObjectURL(blob);
                const previewImg = document.getElementById('previewImage');
                previewImg.src = url;
                
                const fileSizeKB = (blob.size / 1024).toFixed(2);
                showToast(`Photo captured: ${fileSizeKB} KB`, 'success');
                
                document.getElementById('cameraContainer').classList.add('hidden');
                document.getElementById('previewContainer').classList.remove('hidden');
                stopCamera();
            }
        }, 'image/jpeg', 0.7);
    }
    
    function retakePhoto() {
        document.getElementById('cameraContainer').classList.remove('hidden');
        document.getElementById('previewContainer').classList.add('hidden');
        capturedImageData = null;
        startCamera();
    }
    
    function usePhoto() {
        if (!capturedImageData) return;
        
        if (capturedImageData.size > 5 * 1024 * 1024) {
            showToast('Photo is too large (>5MB). Please retake with better lighting.', 'error');
            return;
        }
        
        const filename = 'camera_capture_' + Date.now() + '.jpg';
        const file = new File([capturedImageData], filename, { type: 'image/jpeg' });
        
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        
        const fileInput = document.getElementById('attachment');
        fileInput.files = dataTransfer.files;
        
        const fileSizeKB = (file.size / 1024).toFixed(2);
        showToast(`Photo ready to submit! (${fileSizeKB} KB)`, 'success');
        
        // Ipakita ang filename
        const fileDiv = document.getElementById('selectedFileName');
        fileDiv.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> Selected: ' + filename;
        fileDiv.classList.remove('hidden');
        
        toggleMethod('file');
    }
    
    // ============ FORM SUBMIT ============
    document.getElementById('clearanceForm')?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('attachment');
        const submitBtn = document.getElementById('submitBtn');
        
        // Optional lang ang file at request message — kahit wala, puwedeng mag-submit
        // Pero kung may file, i-validate ang size
        
        if (fileInput.files && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024;
            
            if (file.size > maxSize) {
                e.preventDefault();
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                showToast(`File too large: ${fileSizeMB}MB. Maximum is 5MB.`, 'error');
                return false;
            }
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
    });
    
    // ============ CLOSE MODALS ============
    document.getElementById('submitModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            hideSubmitForm();
        }
    });
    
    document.getElementById('requirementsModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRequirementsModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideSubmitForm();
            closeRequirementsModal();
        }
    });
    
    window.addEventListener('beforeunload', function() {
        stopCamera();
    });
</script>
@endsection