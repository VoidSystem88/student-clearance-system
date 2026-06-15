<?php $__env->startSection('title', 'Request Assistance'); ?>
<?php $__env->startSection('header', 'Request Assistance'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Request Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400 mr-2"></i> Submit Assistance Request
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Fill out the form below and our support team will assist you.</p>
                </div>
                
                <div class="p-5">
                    <?php if(session('success')): ?>
                        <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-3 rounded mb-4">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if($errors->any()): ?>
                        <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo e(route('student.assistance.store')); ?>" enctype="multipart/form-data" id="assistanceForm">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Request Type Dropdown -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Request Type <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button type="button" id="requestTypeButton" 
                                        class="w-full px-3 py-2 text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-between">
                                    <span id="selectedRequestType" class="flex items-center gap-2">
                                        <i class="fas fa-question-circle text-gray-500"></i>
                                        <span> Select Request Type </span>
                                    </span>
                                    <i class="fas fa-chevron-down text-gray-500"></i>
                                </button>
                                
                                <div id="requestTypeDropdown" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="password_reset">
                                        <i class="fas fa-key text-blue-500 w-5"></i>
                                        <span>Password Reset</span>
                                    </div>
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="account_id_reset">
                                        <i class="fas fa-id-card text-purple-500 w-5"></i>
                                        <span>Account ID Reset</span>
                                    </div>
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="login_issue">
                                        <i class="fas fa-sign-in-alt text-red-500 w-5"></i>
                                        <span>Login Issue</span>
                                    </div>
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="otp_issue">
                                        <i class="fas fa-envelope text-yellow-500 w-5"></i>
                                        <span>OTP Not Receiving</span>
                                    </div>
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="clearance_issue">
                                        <i class="fas fa-file-alt text-orange-500 w-5"></i>
                                        <span>Clearance Problem</span>
                                    </div>
                                    <div class="dropdown-item px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer flex items-center gap-2" data-value="other">
                                        <i class="fas fa-question-circle text-gray-500 w-5"></i>
                                        <span>Other Concern</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="request_type" id="request_type" required>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="4" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Please provide detailed information about your concern..." required></textarea>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <i class="fas fa-info-circle mr-1"></i> Minimum 10 characters. Include any relevant details.
                            </p>
                        </div>
                        
                        <!-- File Upload -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">
                                <i class="fas fa-paperclip mr-1"></i> Attachment (Optional)
                            </label>
                            <div class="relative">
                                <input type="file" name="attachment" id="attachment" 
                                       class="hidden"
                                       accept=".jpg,.jpeg,.png,.gif,.pdf">
                                
                                <div id="fileUploadArea" 
                                     class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-all duration-200 bg-gray-50 dark:bg-gray-700/50">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 dark:text-gray-500"></i>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium text-blue-600 dark:text-blue-400">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            JPG, PNG, GIF, PDF (Max 5MB)
                                        </p>
                                    </div>
                                </div>
                                
                                <div id="filePreviewArea" class="hidden mt-3">
                                    <div class="flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i id="fileIcon" class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p id="fileName" class="text-sm font-medium text-gray-800 dark:text-white truncate"></p>
                                                <p id="fileSize" class="text-xs text-gray-500 dark:text-gray-400"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="removeFileBtn" 
                                                class="flex-shrink-0 w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-200 shadow-lg ml-3 active:scale-95"
                                                title="Remove file">
                                            <i class="fas fa-times text-base"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="fileSizeWarning" class="hidden text-red-500 text-xs mt-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <span></span>
                            </div>
                        </div>
                        
                        <!-- Submit Button - FULL WIDTH ON MOBILE, CENTERED ON DESKTOP -->
                        <div class="submit-button-container">
                            <button type="submit" id="submitBtn" 
                                class="submit-btn w-full md:w-auto md:px-8 py-2 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i> 
                                <span id="submitBtnText">Submit Request</span>
                                <span id="submitBtnSpinner" class="hidden"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Recent Requests -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-20">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-history text-blue-600 dark:text-blue-400 mr-2"></i> Recent Requests
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $requests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-sm font-medium text-gray-800 dark:text-white flex items-center gap-1">
                                <?php if($req->request_type == 'password_reset'): ?>
                                    <i class="fas fa-key text-blue-500"></i>
                                <?php elseif($req->request_type == 'account_id_reset'): ?>
                                    <i class="fas fa-id-card text-purple-500"></i>
                                <?php elseif($req->request_type == 'login_issue'): ?>
                                    <i class="fas fa-sign-in-alt text-red-500"></i>
                                <?php elseif($req->request_type == 'otp_issue'): ?>
                                    <i class="fas fa-envelope text-yellow-500"></i>
                                <?php elseif($req->request_type == 'clearance_issue'): ?>
                                    <i class="fas fa-file-alt text-orange-500"></i>
                                <?php else: ?>
                                    <i class="fas fa-question-circle text-gray-500"></i>
                                <?php endif; ?>
                                <?php echo e(ucfirst(str_replace('_', ' ', $req->request_type))); ?>

                            </span>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                <?php if($req->status == 'pending'): ?> bg-yellow-100 dark:bg-yellow-300/30 text-yellow-700 dark:text-yellow-500
                                <?php elseif($req->status == 'in_progress'): ?> bg-blue-100 dark:bg-blue-300/30 text-blue-700 dark:text-blue-500
                                <?php elseif($req->status == 'resolved'): ?> bg-green-100 dark:bg-green-300/30 text-green-700 dark:text-green-500
                                <?php else: ?> bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 <?php endif; ?>">
                                <i class="fas 
                                    <?php if($req->status == 'pending'): ?> fa-clock
                                    <?php elseif($req->status == 'in_progress'): ?> fa-spinner fa-pulse
                                    <?php elseif($req->status == 'resolved'): ?> fa-check-circle
                                    <?php else: ?> fa-times-circle <?php endif; ?> text-xs"></i>
                                <?php echo e(ucfirst($req->status)); ?>

                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?php echo e($req->created_at->format('M d, Y h:i A')); ?>

                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2"><?php echo e(Str::limit($req->description, 80)); ?></p>
                        
                        <?php if($req->attachment_path): ?>
                            <a href="<?php echo e(url('/file/' . basename($req->attachment_path))); ?>" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                                <i class="fas fa-paperclip mr-1"></i> View Attachment
                            </a>
                        <?php endif; ?>
                        
                        <?php if($req->response_message): ?>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <i class="fas fa-reply mr-1"></i> <?php echo e(Str::limit($req->response_message, 60)); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                        <p>No requests yet</p>
                        <p class="text-xs mt-1">Submit your first request above</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    #requestTypeDropdown {
        transition: all 0.2s ease;
    }
    
    .dropdown-item {
        transition: all 0.2s ease;
    }
    
    /* Submit Button Styles - Full width on mobile, auto on desktop */
    .submit-button-container {
        margin-top: 1rem;
        display: flex;
        justify-content: center;
    }
    
    .submit-btn {
        min-width: 160px;
    }
    
    @media (max-width: 768px) {
        .submit-button-container {
            width: 100%;
        }
        .submit-btn {
            width: 100%;
            padding: 10px 16px;
        }
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .fa-spin {
        animation: spin 1s linear infinite;
    }
</style>

<script>
    // ============ REQUEST TYPE DROPDOWN ============
    const requestTypeButton = document.getElementById('requestTypeButton');
    const requestTypeDropdown = document.getElementById('requestTypeDropdown');
    const selectedRequestType = document.getElementById('selectedRequestType');
    const requestTypeInput = document.getElementById('request_type');

    if (requestTypeButton) {
        requestTypeButton.addEventListener('click', function(e) {
            e.stopPropagation();
            requestTypeDropdown.classList.toggle('hidden');
        });
    }

    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const icon = this.querySelector('i').cloneNode(true);
            const text = this.querySelector('span').textContent;
            
            selectedRequestType.innerHTML = '';
            selectedRequestType.appendChild(icon);
            selectedRequestType.appendChild(document.createTextNode(' ' + text));
            
            requestTypeInput.value = value;
            requestTypeDropdown.classList.add('hidden');
            
            document.querySelectorAll('.dropdown-item').forEach(i => {
                i.classList.remove('bg-blue-50', 'dark:bg-blue-900/30');
            });
            this.classList.add('bg-blue-50', 'dark:bg-blue-900/30');
        });
    });

    document.addEventListener('click', function(e) {
        if (requestTypeButton && requestTypeDropdown && !requestTypeButton.contains(e.target) && !requestTypeDropdown.contains(e.target)) {
            requestTypeDropdown.classList.add('hidden');
        }
    });

    // ============ TOAST FUNCTION ============
    function showToastMessage(message, type) {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-5 right-5 z-50 px-4 py-2 rounded-lg text-white text-sm ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    }

    // ============ FILE UPLOAD HANDLING ============
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('attachment');
    const filePreviewArea = document.getElementById('filePreviewArea');
    const fileNameSpan = document.getElementById('fileName');
    const fileSizeSpan = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFileBtn');
    const fileIcon = document.getElementById('fileIcon');
    const fileSizeWarning = document.getElementById('fileSizeWarning');
    const maxSize = 5 * 1024 * 1024;

    function updateFilePreview(file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
        
        if (!validTypes.includes(file.type)) {
            showToastMessage('Invalid file type. Please upload JPG, PNG, GIF, or PDF only.', 'error');
            fileInput.value = '';
            return false;
        }
        
        if (file.size > maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            showToastMessage('File too large: ' + fileSizeMB + 'MB. Maximum is 5MB.', 'error');
            fileInput.value = '';
            return false;
        }
        
        if (file.type.startsWith('image/')) {
            fileIcon.className = 'fas fa-image text-blue-600 dark:text-blue-400 text-lg';
        } else if (file.type === 'application/pdf') {
            fileIcon.className = 'fas fa-file-pdf text-red-600 dark:text-red-400 text-lg';
        } else {
            fileIcon.className = 'fas fa-file-alt text-blue-600 dark:text-blue-400 text-lg';
        }
        
        fileNameSpan.textContent = file.name;
        const fileSizeKB = (file.size / 1024).toFixed(2);
        fileSizeSpan.textContent = fileSizeKB + ' KB';
        
        fileUploadArea.classList.add('hidden');
        filePreviewArea.classList.remove('hidden');
        if (fileSizeWarning) fileSizeWarning.classList.add('hidden');
        
        showToastMessage('File ready: ' + fileSizeKB + ' KB', 'success');
        return true;
    }

    if (fileUploadArea) {
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        });
        
        fileUploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
        });
        
        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFilePreview(e.dataTransfer.files[0]);
            }
        });
    }

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length) {
                updateFilePreview(this.files[0]);
            }
        });
    }

    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function() {
            fileInput.value = '';
            filePreviewArea.classList.add('hidden');
            fileUploadArea.classList.remove('hidden');
            if (fileSizeWarning) fileSizeWarning.classList.add('hidden');
        });
    }

    // ============ FORM VALIDATION & SUBMIT ============
    const assistanceForm = document.getElementById('assistanceForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const submitBtnSpinner = document.getElementById('submitBtnSpinner');
    const descriptionField = document.getElementById('description');

    function validateForm() {
        const requestType = requestTypeInput.value;
        if (!requestType) {
            showToastMessage('Please select a request type.', 'error');
            return false;
        }
        
        const description = descriptionField ? descriptionField.value.trim() : '';
        if (description.length < 10) {
            showToastMessage('Please provide a detailed description (minimum 10 characters).', 'error');
            return false;
        }
        
        if (fileInput.files && fileInput.files.length) {
            const file = fileInput.files[0];
            if (file.size > maxSize) {
                showToastMessage('File too large. Maximum size is 5MB.', 'error');
                return false;
            }
        }
        
        return true;
    }

    // PREVENT DOUBLE SUBMIT
    if (assistanceForm && submitBtn) {
        assistanceForm.addEventListener('submit', function(e) {
            if (submitBtn.disabled) {
                e.preventDefault();
                return false;
            }
            
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Submitting...';
            submitBtnSpinner.classList.remove('hidden');
            
            // Allow form to submit normally - the button will stay disabled
            // The form will redirect, so no need to re-enable
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/assistance.blade.php ENDPATH**/ ?>