<?php $__env->startSection('title', 'Request Assistance'); ?>
<?php $__env->startSection('header', 'Request Assistance'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ✅ ASSISTANCE ACTION BUTTONS - ALWAYS VISIBLE, MOBILE FRIENDLY */
    .assistance-actions {
        display: flex;
        gap: 8px;
        opacity: 1 !important;
        visibility: visible !important;
        margin-top: 8px;
    }

    .assistance-edit-btn,
    .assistance-delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        min-width: 44px;
        min-height: 44px;
        flex: 1;
    }

    .assistance-edit-btn {
        background: #eff6ff;
        color: #2563eb;
        border: 1px solid #bfdbfe;
    }
    .assistance-edit-btn:hover, .assistance-edit-btn:active {
        background: #2563eb;
        color: white;
    }

    .assistance-delete-btn {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .assistance-delete-btn:hover, .assistance-delete-btn:active {
        background: #dc2626;
        color: white;
    }

    body.dark .assistance-edit-btn {
        background: #1e3a5f;
        color: #60a5fa;
        border-color: #1e40af;
    }
    body.dark .assistance-edit-btn:hover {
        background: #2563eb;
        color: white;
    }

    body.dark .assistance-delete-btn {
        background: #7f1d1d;
        color: #fca5a5;
        border-color: #991b1b;
    }
    body.dark .assistance-delete-btn:hover {
        background: #dc2626;
        color: white;
    }

    @media (max-width: 640px) {
        .assistance-edit-btn,
        .assistance-delete-btn {
            padding: 12px 18px;
            font-size: 14px;
            min-width: 48px;
            min-height: 48px;
            border-radius: 12px;
        }
        .assistance-item {
            padding: 16px 10px !important;
        }
        .assistance-actions {
            gap: 10px;
        }
    }

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
    
    .submit-btn {
        min-width: 160px;
    }
    
    @media (max-width: 768px) {
        .submit-btn {
            width: 100%;
            padding: 10px 16px;
        }
        .form-actions {
            flex-direction: column;
            width: 100%;
        }
        .form-actions .btn-cancel {
            order: 2;
        }
        .form-actions .btn-submit {
            order: 1;
        }
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .fa-spin {
        animation: spin 1s linear infinite;
    }

    /* ✅ EDIT MODE HIGHLIGHT */
    .editing-mode {
        ring: 2px solid #3b82f6;
        ring-offset: 2px;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Request Form -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-300">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
                    <h3 class="font-semibold text-gray-800 dark:text-white">
                        <i class="fas fa-paper-plane text-blue-600 dark:text-blue-400 mr-2"></i> 
                        <span id="formTitle">Submit Assistance Request</span>
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400" id="formSubtitle">Fill out the form below and our support team will assist you.</p>
                </div>
                
                <div class="p-5">
                    <div id="alertMessage" class="hidden rounded-lg p-3 mb-4"></div>
                    
                    <form id="assistanceForm" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <input type="hidden" name="edit_id" id="edit_id" value="">
                        <input type="hidden" name="remove_attachment" id="remove_attachment" value="0">
                        
                        <!-- Request Type Dropdown -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Request Type <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <button type="button" id="requestTypeButton" 
                                        class="w-full px-3 py-3 text-left border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-between">
                                    <span id="selectedRequestType" class="flex items-center gap-2">
                                        <i class="fas fa-question-circle text-gray-500"></i>
                                        <span>Select Request Type</span>
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
                            <p id="request_type_error" class="text-red-500 text-xs mt-1 hidden">Please select a request type</p>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-4">
                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Description <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="4" class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="Please provide detailed information about your concern..." required></textarea>
                            <p id="description_error" class="text-red-500 text-xs mt-1 hidden">Description must be at least 10 characters</p>
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
                                
                                <!-- Existing File Info (for edit mode) -->
                                <div id="existingFileInfo" class="hidden mt-3">
                                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-paperclip text-blue-600 dark:text-blue-400 text-xl"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p id="existingFileName" class="text-sm font-medium text-gray-800 dark:text-white truncate"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Current attachment</p>
                                            </div>
                                        </div>
                                        <button type="button" id="removeExistingFileBtn" 
                                                class="flex-shrink-0 w-10 h-10 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-all duration-200 shadow-lg ml-3 active:scale-95"
                                                title="Remove existing file">
                                            <i class="fas fa-times text-base"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div id="fileSizeWarning" class="hidden text-red-500 text-xs mt-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <span></span>
                            </div>
                        </div>
                        
                        <!-- Submit & Cancel Buttons -->
                        <div class="form-actions flex flex-wrap gap-2 mt-6">
                            <button type="button" id="cancelEditBtn" 
                                class="btn-cancel hidden px-5 py-3 rounded-lg text-sm font-medium transition bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 flex-1 md:flex-none">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </button>
                            <button type="submit" id="submitBtn" 
                                class="btn-submit flex-1 md:flex-none px-5 py-3 rounded-lg text-sm font-medium transition-all duration-200 bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
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
                        <i class="fas fa-history text-blue-600 dark:text-blue-400 mr-2"></i> Your Requests
                    </h3>
                </div>
                <div id="requestsList" class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $requests ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="assistance-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="<?php echo e($req->id); ?>">
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

                            <?php if($req->is_edited ?? false): ?>
                                <span class="text-blue-500 ml-2"><i class="fas fa-edit"></i> Edited</span>
                            <?php endif; ?>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2 assistance-description"><?php echo e(Str::limit($req->description, 80)); ?></p>
                        
                        <?php if($req->attachment_path): ?>
                            <a href="<?php echo e(route('student.assistance.attachment', $req->id)); ?>" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block">
                                <i class="fas fa-paperclip mr-1"></i> View Attachment
                            </a>
                        <?php endif; ?>
                        
                        <?php if($req->response_message): ?>
                            <p class="text-xs text-blue-600 dark:text-blue-400 mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                <i class="fas fa-reply mr-1"></i> <?php echo e(Str::limit($req->response_message, 60)); ?>

                            </p>
                        <?php endif; ?>
                        
                        <!-- ✅ ALWAYS VISIBLE EDIT/DELETE BUTTONS (only for pending requests) -->
                        <?php if($req->status == 'pending'): ?>
                        <div class="assistance-actions">
                            <button onclick="editRequest(<?php echo e($req->id); ?>)" 
                                class="assistance-edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteRequest(<?php echo e($req->id); ?>)" 
                                class="assistance-delete-btn">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                        <?php else: ?>
                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                            <i class="fas fa-lock mr-1"></i> Cannot edit/delete - Request is <?php echo e($req->status); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="p-8 text-center text-gray-500 dark:text-gray-400" id="emptyRequests">
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

<script>
// ============ REQUEST TYPE DROPDOWN ============
const requestTypeButton = document.getElementById('requestTypeButton');
const requestTypeDropdown = document.getElementById('requestTypeDropdown');
const selectedRequestType = document.getElementById('selectedRequestType');
const requestTypeInput = document.getElementById('request_type');
let currentRequestType = '';

function setRequestType(value) {
    const option = document.querySelector(`.dropdown-item[data-value="${value}"]`);
    if (option) {
        const icon = option.querySelector('i').cloneNode(true);
        const text = option.querySelector('span').textContent;
        selectedRequestType.innerHTML = '';
        selectedRequestType.appendChild(icon);
        selectedRequestType.appendChild(document.createTextNode(' ' + text));
        requestTypeInput.value = value;
        currentRequestType = value;
        document.getElementById('request_type_error').classList.add('hidden');
        
        document.querySelectorAll('.dropdown-item').forEach(i => {
            i.classList.remove('bg-blue-50', 'dark:bg-blue-900/30');
        });
        option.classList.add('bg-blue-50', 'dark:bg-blue-900/30');
    }
}

if (requestTypeButton) {
    requestTypeButton.addEventListener('click', function(e) {
        e.stopPropagation();
        requestTypeDropdown.classList.toggle('hidden');
    });
}

document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function() {
        setRequestType(this.getAttribute('data-value'));
        requestTypeDropdown.classList.add('hidden');
    });
});

document.addEventListener('click', function(e) {
    if (requestTypeButton && requestTypeDropdown && !requestTypeButton.contains(e.target) && !requestTypeDropdown.contains(e.target)) {
        requestTypeDropdown.classList.add('hidden');
    }
});

// ============ TOAST FUNCTION ============
function showAlert(message, type) {
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i> ${message}`;
    alertMessage.className = `rounded-lg p-3 mb-4 ${type === 'success' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 border-l-4 border-green-500' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-l-4 border-red-500'}`;
    alertMessage.classList.remove('hidden');
    setTimeout(() => alertMessage.classList.add('hidden'), 5000);
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
const existingFileInfo = document.getElementById('existingFileInfo');
const existingFileName = document.getElementById('existingFileName');
const removeExistingFileBtn = document.getElementById('removeExistingFileBtn');
const removeAttachmentInput = document.getElementById('remove_attachment');
const maxSize = 5 * 1024 * 1024;
let editingId = null;
let hasExistingFile = false;

function updateFilePreview(file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'];
    
    if (!validTypes.includes(file.type)) {
        showAlert('Invalid file type. Please upload JPG, PNG, GIF, or PDF only.', 'error');
        fileInput.value = '';
        return false;
    }
    
    if (file.size > maxSize) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        showAlert('File too large: ' + fileSizeMB + 'MB. Maximum is 5MB.', 'error');
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
    if (existingFileInfo) existingFileInfo.classList.add('hidden');
    if (fileSizeWarning) fileSizeWarning.classList.add('hidden');
    
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
        // Show existing file info if there was one
        if (hasExistingFile && existingFileInfo) {
            existingFileInfo.classList.remove('hidden');
        }
    });
}

if (removeExistingFileBtn) {
    removeExistingFileBtn.addEventListener('click', function() {
        // Mark that we want to remove the existing file
        removeAttachmentInput.value = '1';
        existingFileInfo.classList.add('hidden');
        hasExistingFile = false;
        fileUploadArea.classList.remove('hidden');
        showAlert('Existing attachment will be removed upon update.', 'info');
    });
}

// ============ EDIT REQUEST ============
function editRequest(id) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Show loading
    showAlert('Loading request data...', 'info');
    
    fetch(`/student/assistance/${id}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const req = data.request;
            editingId = req.id;
            
            // Update form
            document.getElementById('formTitle').textContent = 'Edit Assistance Request';
            document.getElementById('formSubtitle').textContent = 'Update your request details';
            document.getElementById('edit_id').value = req.id;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('submitBtnText').textContent = 'Update Request';
            document.getElementById('cancelEditBtn').classList.remove('hidden');
            
            // Set request type
            setRequestType(req.request_type);
            
            // Set description
            document.getElementById('description').value = req.description;
            
            // Handle existing file
            if (req.attachment_path && req.attachment_original_name) {
                hasExistingFile = true;
                existingFileName.textContent = req.attachment_original_name;
                existingFileInfo.classList.remove('hidden');
                fileUploadArea.classList.add('hidden');
                filePreviewArea.classList.add('hidden');
                removeAttachmentInput.value = '0';
            } else {
                hasExistingFile = false;
                existingFileInfo.classList.add('hidden');
                fileUploadArea.classList.remove('hidden');
                filePreviewArea.classList.add('hidden');
            }
            
            // Highlight form
            const formContainer = document.getElementById('assistanceForm').closest('.bg-white');
            if (formContainer) {
                formContainer.classList.add('ring-2', 'ring-blue-500', 'ring-offset-2');
                setTimeout(() => {
                    formContainer.classList.remove('ring-2', 'ring-blue-500', 'ring-offset-2');
                }, 3000);
            }
            
            // Scroll to form
            document.getElementById('assistanceForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Clear any previous alerts
            document.getElementById('alertMessage').classList.add('hidden');
            
        } else {
            showAlert(data.message || 'Error loading request', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Network error. Please try again.', 'error');
    });
}

// ============ CANCEL EDIT ============
function cancelEdit() {
    editingId = null;
    document.getElementById('formTitle').textContent = 'Submit Assistance Request';
    document.getElementById('formSubtitle').textContent = 'Fill out the form below and our support team will assist you.';
    document.getElementById('edit_id').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('submitBtnText').textContent = 'Submit Request';
    document.getElementById('cancelEditBtn').classList.add('hidden');
    
    // Reset form
    document.getElementById('description').value = '';
    setRequestType('');
    currentRequestType = '';
    selectedRequestType.innerHTML = '<i class="fas fa-question-circle text-gray-500"></i><span>Select Request Type</span>';
    requestTypeInput.value = '';
    
    // Reset file upload
    fileInput.value = '';
    filePreviewArea.classList.add('hidden');
    existingFileInfo.classList.add('hidden');
    fileUploadArea.classList.remove('hidden');
    removeAttachmentInput.value = '0';
    hasExistingFile = false;
    
    document.getElementById('alertMessage').classList.add('hidden');
}

document.getElementById('cancelEditBtn').addEventListener('click', cancelEdit);

// ============ DELETE REQUEST ============
async function deleteRequest(id) {
    const result = await Swal.fire({
        title: 'Delete Request?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    });
    
    if (result.isConfirmed) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(`/student/assistance/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            if (data.success) {
                const item = document.querySelector(`.assistance-item[data-id="${id}"]`);
                if (item) item.remove();
                
                if (document.querySelectorAll('.assistance-item').length === 0) {
                    document.getElementById('requestsList').innerHTML = `
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400" id="emptyRequests">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                            <p>No requests yet</p>
                            <p class="text-xs mt-1">Submit your first request above</p>
                        </div>`;
                }
                
                if (editingId === id) cancelEdit();
                showAlert('Request deleted successfully!', 'success');
            } else {
                showAlert(data.message || 'Failed to delete', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('Network error. Please try again.', 'error');
        }
    }
}

// ============ VALIDATE FORM ============
function validateForm() {
    let isValid = true;
    
    if (!requestTypeInput.value) {
        document.getElementById('request_type_error').classList.remove('hidden');
        isValid = false;
    } else {
        document.getElementById('request_type_error').classList.add('hidden');
    }
    
    const description = document.getElementById('description').value.trim();
    if (description.length < 10) {
        document.getElementById('description_error').classList.remove('hidden');
        isValid = false;
    } else {
        document.getElementById('description_error').classList.add('hidden');
    }
    
    return isValid;
}

// ============ FORM SUBMIT ============
const form = document.getElementById('assistanceForm');
const submitBtn = document.getElementById('submitBtn');

form.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!validateForm()) {
        showAlert('Please fix the errors above.', 'error');
        return;
    }
    
    if (submitBtn.disabled) return;
    
    submitBtn.disabled = true;
    document.getElementById('submitBtnText').textContent = editingId ? 'Updating...' : 'Submitting...';
    document.getElementById('submitBtnSpinner').classList.remove('hidden');
    
    const formData = new FormData(form);
    const url = editingId ? `/student/assistance/${editingId}` : '/student/assistance';
    if (editingId) formData.append('_method', 'PUT');
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        showAlert('Session expired. Please refresh the page.', 'error');
        setTimeout(() => location.reload(), 2000);
        submitBtn.disabled = false;
        document.getElementById('submitBtnText').textContent = editingId ? 'Update Request' : 'Submit Request';
        document.getElementById('submitBtnSpinner').classList.add('hidden');
        return;
    }
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: formData
        });
        
        if (response.redirected) {
            showAlert('Session expired. Refreshing page...', 'error');
            setTimeout(() => location.reload(), 1500);
            return;
        }
        
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            showAlert('Your session has expired. Please refresh the page.', 'error');
            setTimeout(() => location.reload(), 2000);
            return;
        }
        
        const data = await response.json();
        
        if (data.success) {
            if (editingId) {
                updateRequestInList(editingId, data.request);
                cancelEdit();
                showAlert('Request updated successfully!', 'success');
            } else {
                // Reset form
                document.getElementById('description').value = '';
                setRequestType('');
                currentRequestType = '';
                selectedRequestType.innerHTML = '<i class="fas fa-question-circle text-gray-500"></i><span>Select Request Type</span>';
                requestTypeInput.value = '';
                
                // Reset file upload
                fileInput.value = '';
                filePreviewArea.classList.add('hidden');
                fileUploadArea.classList.remove('hidden');
                existingFileInfo.classList.add('hidden');
                removeAttachmentInput.value = '0';
                
                showAlert(data.message || 'Request submitted!', 'success');
                if (data.request) addRequestToList(data.request);
            }
        } else {
            showAlert(data.message || 'Something went wrong', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('Connection error. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        document.getElementById('submitBtnText').textContent = editingId ? 'Update Request' : 'Submit Request';
        document.getElementById('submitBtnSpinner').classList.add('hidden');
    }
});

// ============ UPDATE REQUEST IN LIST ============
function updateRequestInList(id, request) {
    const item = document.querySelector(`.assistance-item[data-id="${id}"]`);
    if (!item) return;
    
    // Update status badge
    const statusBadge = item.querySelector('.inline-flex.items-center.gap-1.px-2.py-0.5.rounded-full');
    if (statusBadge) {
        const statusMap = {
            'pending': { bg: 'bg-yellow-100 dark:bg-yellow-300/30 text-yellow-700 dark:text-yellow-500', icon: 'fa-clock' },
            'in_progress': { bg: 'bg-blue-100 dark:bg-blue-300/30 text-blue-700 dark:text-blue-500', icon: 'fa-spinner fa-pulse' },
            'resolved': { bg: 'bg-green-100 dark:bg-green-300/30 text-green-700 dark:text-green-500', icon: 'fa-check-circle' },
            'closed': { bg: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', icon: 'fa-times-circle' }
        };
        const status = request.status || 'pending';
        const s = statusMap[status] || statusMap.pending;
        statusBadge.className = `inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ${s.bg}`;
        statusBadge.innerHTML = `<i class="fas ${s.icon} text-xs"></i> ${status.charAt(0).toUpperCase() + status.slice(1)}`;
    }
    
    // Update description
    const descEl = item.querySelector('.assistance-description');
    if (descEl) {
        descEl.textContent = request.description.length > 80 ? request.description.substring(0, 80) + '...' : request.description;
    }
    
    // Update edited status
    const dateEl = item.querySelector('.text-xs.text-gray-500');
    if (dateEl) {
        const editedSpan = dateEl.querySelector('.text-blue-500');
        if (!editedSpan) {
            const dateText = dateEl.textContent.trim();
            dateEl.innerHTML = dateText + ' <span class="text-blue-500 ml-2"><i class="fas fa-edit"></i> Edited</span>';
        }
    }
}

// ============ ADD REQUEST TO LIST ============
function addRequestToList(request) {
    const container = document.getElementById('requestsList');
    const emptyDiv = document.getElementById('emptyRequests');
    if (emptyDiv) emptyDiv.remove();
    
    const date = new Date();
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const formattedTime = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    const typeLabels = {
        'password_reset': 'Password Reset',
        'account_id_reset': 'Account ID Reset',
        'login_issue': 'Login Issue',
        'otp_issue': 'OTP Not Receiving',
        'clearance_issue': 'Clearance Problem',
        'other': 'Other Concern'
    };
    
    const typeIcons = {
        'password_reset': 'fa-key text-blue-500',
        'account_id_reset': 'fa-id-card text-purple-500',
        'login_issue': 'fa-sign-in-alt text-red-500',
        'otp_issue': 'fa-envelope text-yellow-500',
        'clearance_issue': 'fa-file-alt text-orange-500',
        'other': 'fa-question-circle text-gray-500'
    };
    
    const typeLabel = typeLabels[request.request_type] || request.request_type;
    const typeIcon = typeIcons[request.request_type] || 'fa-question-circle text-gray-500';
    
    const newHtml = `
        <div class="assistance-item p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition" data-id="${request.id}">
            <div class="flex justify-between items-start mb-1">
                <span class="text-sm font-medium text-gray-800 dark:text-white flex items-center gap-1">
                    <i class="fas ${typeIcon}"></i>
                    ${typeLabel}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-300/30 text-yellow-700 dark:text-yellow-500">
                    <i class="fas fa-clock text-xs"></i> Pending
                </span>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                <i class="far fa-calendar-alt mr-1"></i>
                ${formattedDate} ${formattedTime}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 line-clamp-2 assistance-description">${escapeHtml(request.description.substring(0, 80) + (request.description.length > 80 ? '...' : ''))}</p>
            
            ${request.attachment_path ? `<a href="/student/assistance/attachment/${request.id}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-2 inline-block"><i class="fas fa-paperclip mr-1"></i> View Attachment</a>` : ''}
            
            <div class="assistance-actions">
                <button onclick="editRequest(${request.id})" class="assistance-edit-btn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deleteRequest(${request.id})" class="assistance-delete-btn">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>`;
    
    container.insertAdjacentHTML('afterbegin', newHtml);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/assistance.blade.php ENDPATH**/ ?>