<?php $__env->startSection('title', 'Clearance Status'); ?>
<?php $__env->startSection('header', 'Clearance Status'); ?>

<?php $__env->startSection('content'); ?>
<!-- Summary Cards - Responsive Grid -->
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="bg-green-50 rounded-xl p-3 border border-green-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-600 text-xs">Approved</p>
                <p class="text-xl font-bold text-green-700"><?php echo e($approvedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-check-circle text-green-500 text-lg"></i>
        </div>
    </div>
    <div class="bg-yellow-50 rounded-xl p-3 border border-yellow-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-600 text-xs">Pending</p>
                <p class="text-xl font-bold text-yellow-700"><?php echo e($pendingCount ?? 0); ?></p>
            </div>
            <i class="fas fa-clock text-yellow-500 text-lg"></i>
        </div>
    </div>
    <div class="bg-red-50 rounded-xl p-3 border border-red-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-600 text-xs">Rejected</p>
                <p class="text-xl font-bold text-red-700"><?php echo e($rejectedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-times-circle text-red-500 text-lg"></i>
        </div>
    </div>
    <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-xs">Not Submitted</p>
                <p class="text-xl font-bold text-gray-700"><?php echo e($notSubmittedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-hourglass-start text-gray-500 text-lg"></i>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-2">
        <span class="font-semibold text-gray-700 text-sm">Overall Progress</span>
        <span class="text-xs text-gray-500"><?php echo e($approvedCount ?? 0); ?>/<?php echo e($totalDepartments ?? 0); ?> cleared</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="bg-green-500 h-2 rounded-full transition-all" style="width: <?php echo e(isset($totalDepartments) && $totalDepartments > 0 ? ($approvedCount ?? 0) / $totalDepartments * 100 : 0); ?>%"></div>
    </div>
    
    <?php if(isset($isFullyCleared) && $isFullyCleared): ?>
    <div class="mt-4 grid grid-cols-2 gap-3">
        <a href="<?php echo e(route('student.clearance.view-pdf')); ?>" target="_blank" 
           class="flex items-center justify-center gap-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-xs font-medium">
            <i class="fas fa-eye text-xs"></i> View Slip
        </a>
        <a href="<?php echo e(route('student.clearance.print')); ?>" 
           class="flex items-center justify-center gap-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition text-xs font-medium">
            <i class="fas fa-download text-xs"></i> Download PDF
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Departments Cards (Mobile-Friendly Card Layout) -->
<div class="space-y-3">
    <?php $__empty_1 = true; $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $request = $clearanceRequests->get($department->id) ?? null;
            $status = $request ? $request->status : 'not_submitted';
        ?>
        
        <!-- Department Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="px-4 py-3 border-b border-gray-100 <?php echo e($status == 'approved' ? 'bg-green-50' : ($status == 'rejected' ? 'bg-red-50' : ($status == 'pending' ? 'bg-yellow-50' : 'bg-gray-50'))); ?>">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo e($department->name); ?></h4>
                        <?php if($department->handler_name): ?>
                            <div class="text-xs text-gray-500 mt-0.5"><?php echo e($department->handler_name); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="ml-2">
                        <?php if($status == 'approved'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle text-xs"></i> Approved
                            </span>
                        <?php elseif($status == 'rejected'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle text-xs"></i> Rejected
                            </span>
                        <?php elseif($status == 'pending'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock text-xs"></i> Pending
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                <i class="fas fa-hourglass-start text-xs"></i> Not Submitted
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="px-4 py-3 space-y-3">
                <!-- Submitted Date -->
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500">Submitted Date:</span>
                    <span class="text-gray-700"><?php echo e($request ? $request->submitted_at->format('M d, Y') : '—'); ?></span>
                </div>
                
                <!-- Rejection Reason (if rejected) -->
                <?php if($status == 'rejected' && $request->remarks): ?>
                    <div class="bg-red-50 rounded-lg p-2">
                        <p class="text-xs text-red-600">
                            <i class="fas fa-comment-dots mr-1"></i> 
                            <strong>Reason:</strong> <?php echo e($request->remarks); ?>

                        </p>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="flex gap-2 pt-1">
                    <!-- View Requirements Button -->
                    <button onclick="viewRequirements(<?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')" 
                            class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-list-check text-xs"></i> Requirements
                    </button>
                    
                    <!-- Submit / Action Button -->
                    <?php if(!$request || $status == 'rejected'): ?>
                        <button onclick="showSubmitForm(<?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')" 
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-upload text-xs"></i> Submit
                        </button>
                    <?php elseif($status == 'pending'): ?>
                        <button onclick="cancelRequest(<?php echo e($request->id); ?>)" 
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times-circle text-xs"></i> Cancel
                        </button>
                    <?php elseif($status == 'approved'): ?>
                        <span class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-green-100 text-green-700 text-xs rounded-lg">
                            <i class="fas fa-check-circle text-xs"></i> Completed
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-white rounded-xl p-8 text-center text-gray-500">
            <i class="fas fa-building text-4xl mb-2 text-gray-300"></i>
            <p>No departments available</p>
        </div>
    <?php endif; ?>
</div>

<!-- Submit Modal -->
<div id="submitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 w-full max-w-md max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-bold">
                <i class="fas fa-paper-plane text-blue-600 mr-2"></i> Submit Clearance
            </h3>
            <button onclick="hideSubmitForm()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="clearanceForm" method="POST" action="<?php echo e(route('student.clearance.submit')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="department_id" id="departmentId">
            
            <!-- Upload Method Toggle -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Choose Upload Method</label>
                <div class="flex gap-2">
                    <button type="button" onclick="toggleMethod('file')" id="fileMethodBtn" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium transition bg-blue-600 text-white">
                        <i class="fas fa-folder-open mr-1"></i> File
                    </button>
                    <button type="button" onclick="toggleMethod('camera')" id="cameraMethodBtn" class="flex-1 py-2 px-2 rounded-lg text-xs font-medium transition bg-gray-200 text-gray-700">
                        <i class="fas fa-camera mr-1"></i> Camera
                    </button>
                </div>
            </div>
            
            <!-- File Upload Method -->
            <div id="fileMethod" class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Upload Proof/Receipt</label>
                <input type="file" name="attachment" id="attachment" class="hidden" accept=".jpg,.jpeg,.png" onchange="validateFileSize(this)">
                <button type="button" onclick="document.getElementById('attachment').click()" class="w-full py-2 px-3 rounded-lg text-sm font-medium bg-blue-600 text-white">
                    <i class="fas fa-folder-open mr-2"></i> Choose File
                </button>
                <div id="selectedFileName" class="text-xs text-gray-500 mt-1 hidden"></div>
                <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG (Max 5MB)</p>
                <div id="fileSizeWarning" class="hidden text-red-500 text-xs mt-1"></div>
            </div>
            
            <!-- Camera Method -->
            <div id="cameraMethod" class="mb-3 hidden">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Take Photo</label>
                <div id="cameraContainer">
                    <div class="bg-gray-100 rounded-lg overflow-hidden">
                        <video id="video" class="w-full h-auto" autoplay playsinline></video>
                    </div>
                    <div class="flex gap-2 mt-2">
                        <button type="button" onclick="switchCamera()" class="flex-1 py-2 bg-gray-600 text-white rounded-lg text-xs">
                            <i class="fas fa-sync-alt mr-1"></i> Switch
                        </button>
                        <button type="button" onclick="capturePhoto()" class="flex-1 py-2 bg-blue-600 text-white rounded-lg text-xs">
                            <i class="fas fa-camera mr-1"></i> Capture
                        </button>
                    </div>
                </div>
                <canvas id="canvas" class="hidden"></canvas>
                <div id="previewContainer" class="hidden mt-2">
                    <img id="previewImage" src="" class="w-full rounded-lg border-2 border-green-500/50 mb-2">
                    <div class="flex gap-2">
                        <button type="button" onclick="retakePhoto()" class="flex-1 py-2 bg-yellow-600 text-white rounded-lg text-xs">
                            <i class="fas fa-redo mr-1"></i> Retake
                        </button>
                        <button type="button" onclick="usePhoto()" class="flex-1 py-2 bg-green-600 text-white rounded-lg text-xs">
                            <i class="fas fa-check mr-1"></i> Use
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Request Message -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">
                    <i class="fas fa-comment-dots mr-1"></i> Request Message (Optional)
                </label>
                <textarea name="request_message" id="request_message" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                          placeholder="Add a message if needed..."></textarea>
            </div>
            
            <!-- Buttons -->
            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="confirmCancel()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" id="submitBtn" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane mr-1"></i> Submit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Requirements Modal -->
<div id="requirementsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 w-full max-w-md">
        <div class="flex justify-between items-center mb-3">
            <h3 id="requirementsModalTitle" class="text-lg font-bold text-gray-800">
                <i class="fas fa-list-check text-blue-600 mr-2"></i> Requirements
            </h3>
            <button onclick="closeRequirementsModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div id="requirementsList" class="space-y-2 max-h-80 overflow-y-auto">
            <div class="text-center py-6 text-gray-500 text-sm">
                <i class="fas fa-spinner fa-spin"></i> Loading...
            </div>
        </div>
        
        <div class="mt-3 flex justify-end">
            <button onclick="closeRequirementsModal()" class="px-3 py-1.5 bg-gray-300 rounded-lg text-xs hover:bg-gray-400 transition">Close</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ TOAST NOTIFICATION ============
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed top-16 right-3 left-3 z-50 px-3 py-2 rounded-lg shadow-lg text-white text-sm ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} transition-all duration-300`;
        toast.innerHTML = '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + ' mr-2"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // ============ CANCEL PENDING REQUEST ============
    function cancelRequest(requestId) {
        Swal.fire({
            title: 'Cancel Request?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                
                fetch('/student/clearance/' + requestId + '/cancel', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Cancelled!', text: data.message, timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                    }
                })
                .catch(error => Swal.fire({ icon: 'error', title: 'Network Error' }));
            }
        });
    }
    
    // ============ CANCEL BEFORE SUBMIT ============
    function confirmCancel() {
        Swal.fire({
            title: 'Cancel Submission?',
            text: 'Your uploaded file will be discarded.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('attachment').value = '';
                document.getElementById('selectedFileName')?.classList.add('hidden');
                capturedImageData = null;
                hideSubmitForm();
                showToast('Submission cancelled', 'info');
            }
        });
    }
    
    // ============ REQUIREMENTS MODAL ============
    function viewRequirements(departmentId, departmentName) {
    const studentYear = window.VoidUserData?.yearLevel || '1st Year';
    
    document.getElementById('requirementsModalTitle').innerHTML = '<i class="fas fa-list-check text-blue-600 mr-2"></i> ' + departmentName + ' (' + studentYear + ')';
    const container = document.getElementById('requirementsList');
    container.innerHTML = '<div class="text-center py-6 text-gray-500"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    fetch('/api/departments/' + departmentId + '/requirements?year_level=' + encodeURIComponent(studentYear))
    .then(response => response.json())
    .then(data => {
                if (data.requirements && data.requirements.length > 0) {
                    let html = '<div class="space-y-2">';
                    data.requirements.forEach(req => {
                        html += `<div class="flex items-start gap-2 p-2 bg-gray-50 rounded-lg">
                                    <i class="fas fa-check-circle text-green-500 text-xs mt-0.5"></i>
                                    <div class="flex-1">
                                        <p class="text-gray-800 text-sm">${escapeHtml(req.requirement_name)}</p>
                                        ${req.is_required ? '<span class="text-xs text-red-500">Required</span>' : '<span class="text-xs text-gray-400">Optional</span>'}
                                    </div>
                                </div>`;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="text-center py-6 text-gray-500"><i class="fas fa-inbox text-3xl mb-2"></i><p class="text-sm">No specific requirements.</p></div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<div class="text-center py-6 text-red-500"><i class="fas fa-exclamation-triangle text-3xl mb-2"></i><p class="text-sm">Failed to load.</p></div>';
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
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ============ FILE VALIDATION ============
    function validateFileSize(input) {
        const file = input.files[0];
        const warningDiv = document.getElementById('fileSizeWarning');
        const maxSize = 5 * 1024 * 1024;
        
        if (file && file.size > maxSize) {
            warningDiv.innerHTML = 'File too large. Max 5MB.';
            warningDiv.classList.remove('hidden');
            input.value = '';
            return false;
        } else {
            warningDiv.classList.add('hidden');
            if (file) showToast('File ready: ' + (file.size / 1024).toFixed(0) + ' KB', 'success');
            return true;
        }
    }
    
    document.getElementById('attachment')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        const fileDiv = document.getElementById('selectedFileName');
        if (fileName) {
            fileDiv.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> ' + fileName;
            fileDiv.classList.remove('hidden');
        } else {
            fileDiv.classList.add('hidden');
        }
    });
    
    // ============ MODAL CONTROLS ============
    function showSubmitForm(departmentId, departmentName) {
        document.getElementById('departmentId').value = departmentId;
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-paper-plane text-blue-600 mr-2"></i> ' + departmentName;
        document.getElementById('submitModal').style.display = 'flex';
        document.getElementById('submitModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        toggleMethod('file');
        document.getElementById('request_message').value = '';
        document.getElementById('attachment').value = '';
        document.getElementById('selectedFileName')?.classList.add('hidden');
        capturedImageData = null;
    }
    
    function hideSubmitForm() {
        stopCamera();
        document.getElementById('submitModal').style.display = 'none';
        document.getElementById('submitModal').classList.add('hidden');
        document.body.style.overflow = '';
        document.getElementById('clearanceForm').reset();
        capturedImageData = null;
        document.getElementById('fileSizeWarning')?.classList.add('hidden');
        document.getElementById('selectedFileName')?.classList.add('hidden');
    }
    
    // ============ UPLOAD METHOD TOGGLE ============
    let currentMethod = 'file';
    
    function toggleMethod(method) {
        currentMethod = method;
        const fileMethod = document.getElementById('fileMethod');
        const cameraMethod = document.getElementById('cameraMethod');
        const fileBtn = document.getElementById('fileMethodBtn');
        const cameraBtn = document.getElementById('cameraMethodBtn');
        
        if (method === 'file') {
            fileMethod.classList.remove('hidden');
            cameraMethod.classList.add('hidden');
            fileBtn.classList.remove('bg-gray-200', 'text-gray-700');
            fileBtn.classList.add('bg-blue-600', 'text-white');
            cameraBtn.classList.remove('bg-blue-600', 'text-white');
            cameraBtn.classList.add('bg-gray-200', 'text-gray-700');
            stopCamera();
        } else {
            fileMethod.classList.add('hidden');
            cameraMethod.classList.remove('hidden');
            cameraBtn.classList.remove('bg-gray-200', 'text-gray-700');
            cameraBtn.classList.add('bg-blue-600', 'text-white');
            fileBtn.classList.remove('bg-blue-600', 'text-white');
            fileBtn.classList.add('bg-gray-200', 'text-gray-700');
            startCamera();
        }
    }
    
    // ============ CAMERA FUNCTIONS ============
    let video = null, canvas = null, stream = null, currentCamera = 'user', capturedImageData = null;
    
    async function startCamera() {
        video = document.getElementById('video');
        canvas = document.getElementById('canvas');
        try {
            if (stream) stopCamera();
            const constraints = { video: { facingMode: { exact: currentCamera }, width: { ideal: 1280 }, height: { ideal: 720 } } };
            try { stream = await navigator.mediaDevices.getUserMedia(constraints); } 
            catch (err) { stream = await navigator.mediaDevices.getUserMedia({ video: true }); }
            if (video) { video.srcObject = stream; video.onloadedmetadata = () => video.play(); }
        } catch (err) {
            showToast('Cannot access camera. Use file upload.', 'error');
            toggleMethod('file');
        }
    }
    
    function stopCamera() {
        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
        if (video) video.srcObject = null;
    }
    
    function switchCamera() { currentCamera = currentCamera === 'user' ? 'environment' : 'user'; startCamera(); }
    
    function capturePhoto() {
        if (!video || !canvas) return;
        const context = canvas.getContext('2d');
        let width = video.videoWidth, height = video.videoHeight;
        const max = 800;
        if (width > max) { height = (height * max) / width; width = max; }
        if (height > max) { width = (width * max) / height; height = max; }
        canvas.width = width; canvas.height = height;
        context.drawImage(video, 0, 0, width, height);
        canvas.toBlob(blob => {
            if (blob.size > 2 * 1024 * 1024) {
                canvas.toBlob(compressed => {
                    capturedImageData = compressed;
                    document.getElementById('previewImage').src = URL.createObjectURL(compressed);
                    showToast('Photo captured!', 'success');
                    document.getElementById('cameraContainer').classList.add('hidden');
                    document.getElementById('previewContainer').classList.remove('hidden');
                    stopCamera();
                }, 'image/jpeg', 0.5);
            } else {
                capturedImageData = blob;
                document.getElementById('previewImage').src = URL.createObjectURL(blob);
                showToast('Photo captured!', 'success');
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
        const file = new File([capturedImageData], 'camera_capture.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        document.getElementById('attachment').files = dataTransfer.files;
        document.getElementById('selectedFileName').innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> Photo captured';
        document.getElementById('selectedFileName').classList.remove('hidden');
        toggleMethod('file');
    }
    
    // ============ FORM SUBMIT ============
    document.getElementById('clearanceForm')?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('attachment');
        if (fileInput.files && fileInput.files.length > 0 && fileInput.files[0].size > 5 * 1024 * 1024) {
            e.preventDefault();
            showToast('File too large. Maximum 5MB.', 'error');
            return false;
        }
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
    });
    
    // ============ CLOSE MODALS ============
    document.getElementById('submitModal')?.addEventListener('click', e => { if (e.target === this) hideSubmitForm(); });
    document.getElementById('requirementsModal')?.addEventListener('click', e => { if (e.target === this) closeRequirementsModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { hideSubmitForm(); closeRequirementsModal(); } });
    window.addEventListener('beforeunload', () => stopCamera());
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/clearance.blade.php ENDPATH**/ ?>