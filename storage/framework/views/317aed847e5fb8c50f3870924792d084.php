<?php $__env->startSection('title', 'Send Report'); ?>
<?php $__env->startSection('header', 'Send Report'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* ============ FORCE DARK MODE ============ */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .border-gray-200 {
        border-color: #374151 !important;
    }
    body.dark-mode .border-gray-300 {
        border-color: #4b5563 !important;
    }
    body.dark-mode .text-gray-800 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-700 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-600 {
        color: #9ca3af !important;
    }
    body.dark-mode .text-gray-500 {
        color: #9ca3af !important;
    }
    body.dark-mode .text-gray-400 {
        color: #6b7280 !important;
    }
    body.dark-mode .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
    }
    body.dark-mode .divide-gray-100 {
        border-color: #374151 !important;
    }
    body.dark-mode .bg-gray-50 {
        background-color: #1f2937 !important;
    }
    body.dark-mode .bg-gray-100 {
        background-color: #374151 !important;
    }
    
    /* ============ DARK MODE SPECIFIC COLORS ============ */
    body.dark-mode .bg-green-50 {
        background-color: #064e3b !important;
    }
    body.dark-mode .text-green-600 {
        color: #86efac !important;
    }
    body.dark-mode .bg-blue-50 {
        background-color: #1e3a5f !important;
    }
    body.dark-mode .text-blue-600 {
        color: #60a5fa !important;
    }
    body.dark-mode .bg-yellow-50 {
        background-color: #78350f !important;
    }
    body.dark-mode .text-yellow-700 {
        color: #fcd34d !important;
    }
    body.dark-mode .bg-gray-200 {
        background-color: #374151 !important;
    }
    body.dark-mode .text-gray-700 {
        color: #e5e7eb !important;
    }
    body.dark-mode .hover\:bg-gray-300:hover {
        background-color: #4b5563 !important;
    }
    body.dark-mode .bg-red-50 {
        background-color: #7f1d1d !important;
    }
    body.dark-mode .text-red-700 {
        color: #fca5a5 !important;
    }
    
    /* ============ STATUS BADGES ============ */
    .badge-sent {
        background: #dcfce7;
        color: #166534;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-failed {
        background: #fee2e2;
        color: #991b1b;
    }
    
    body.dark-mode .badge-sent {
        background: #065f46 !important;
        color: #86efac !important;
    }
    body.dark-mode .badge-pending {
        background: #78350f !important;
        color: #fcd34d !important;
    }
    body.dark-mode .badge-failed {
        background: #7f1d1d !important;
        color: #fca5a5 !important;
    }
    
    /* ============ SPINNER ============ */
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* ============ UPLOAD BOX ============ */
    .upload-box {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .upload-box:hover {
        border-color: #3b82f6;
        background: #f8fafc;
    }
    body.dark-mode .upload-box {
        border-color: #4b5563 !important;
    }
    body.dark-mode .upload-box:hover {
        border-color: #60a5fa !important;
        background: #1e293b !important;
    }
    .upload-box.dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    body.dark-mode .upload-box.dragover {
        border-color: #60a5fa !important;
        background: #1e3a5f !important;
    }
    
    /* ============ TABLE ROWS ============ */
    .table-row:hover {
        background-color: #f8fafc;
    }
    body.dark-mode .table-row:hover {
        background-color: #1e293b !important;
    }
    
    /* ============ DISABLED INPUT ============ */
    .input-disabled {
        background-color: #f3f4f6 !important;
        color: #6b7280 !important;
        cursor: not-allowed !important;
        border-color: #e5e7eb !important;
    }
    body.dark-mode .input-disabled {
        background-color: #374151 !important;
        color: #9ca3af !important;
        border-color: #4b5563 !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    
    
    
    
    <?php if(session('success')): ?>
    <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4">
        <i class="fas fa-check-circle mr-2"></i> <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4">
        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    
    
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-paper-plane text-green-600 mr-2"></i> Send Report to Department
            </h3>
            <p class="text-sm text-gray-600">Generate and send verified students report directly to your department</p>
        </div>
        
        <div class="p-6">
            
            <div class="bg-blue-50 p-4 rounded-lg mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Verified Students Ready to Send</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo e($verifiedCount ?? 0); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="<?php echo e(route('officer.send.report')); ?>" enctype="multipart/form-data" id="sendReportForm">
                <?php echo csrf_field(); ?>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building mr-1"></i> Department
                    </label>
                    <div class="relative">
                        <input type="text" 
                               value="<?php echo e($department->name ?? 'N/A'); ?>" 
                               class="w-full p-2.5 border border-gray-300 rounded-lg input-disabled pr-10" 
                               disabled>
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock text-xs"></i>
                        </div>
                    </div>
                    <input type="hidden" name="department_id" value="<?php echo e($department->id ?? ''); ?>">
                    <p class="text-xs text-gray-400 mt-1">
                        <i class="fas fa-info-circle"></i> Reports are automatically sent to your assigned department (cannot be changed)
                    </p>
                </div>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-1"></i> Report Title
                    </label>
                    <input type="text" name="report_title" id="reportTitle" 
                           placeholder="e.g., Verified Students - Graduation 2024"
                           class="w-full p-2.5 border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500">
                </div>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt mr-1"></i> Event Name
                    </label>
                    <input type="text" name="event_name" id="eventName" 
                           placeholder="e.g., Graduation 2024, Orientation"
                           class="w-full p-2.5 border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500">
                </div>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-1"></i> Additional Notes (Optional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Any additional information for the department..."
                              class="w-full p-2.5 border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-paperclip mr-1"></i> Attach Additional File (Optional)
                    </label>
                    <div class="upload-box" id="uploadBox">
                        <input type="file" name="attachment" id="fileInput" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium text-blue-600">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-500 mt-1">PDF, DOC, XLS (Max 5MB)</p>
                        <div id="filePreview" class="hidden mt-3">
                            <div class="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file text-blue-500"></i>
                                    <span id="fileName" class="text-sm text-gray-700"></span>
                                    <span id="fileSize" class="text-xs text-gray-500"></span>
                                </div>
                                <button type="button" id="removeFileBtn" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="bg-yellow-50 p-4 rounded-lg mb-6">
                    <p class="text-sm text-yellow-700 flex items-start gap-2">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <span>The report will be sent directly to <strong><?php echo e($department->name ?? 'your department'); ?></strong>. 
                        A copy will also be saved in your reports history.</span>
                    </p>
                </div>
                
                
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('officer.dashboard')); ?>" 
                       class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-center hover:bg-gray-300 transition font-medium">
                        Cancel
                    </a>
                    <button type="submit" id="sendReportBtn" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg transition font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Send Report
                        <span id="sendSpinner" class="hidden"><span class="spinner"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    
    
    
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h4 class="font-semibold text-gray-800">
                <i class="fas fa-history text-gray-500 mr-2"></i> Sent Reports History
            </h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $reports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="table-row transition">
                        <td class="px-4 py-3 text-gray-500"><?php echo e(\Carbon\Carbon::parse($report->created_at)->format('M d, Y h:i A')); ?></td>
                        <td class="px-4 py-3 text-gray-800"><?php echo e($report->report_title ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($report->department_name ?? $department->name ?? 'N/A'); ?></td>
                        <td class="px-4 py-3 text-gray-600"><?php echo e($report->total_students ?? 0); ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                <?php if($report->status == 'sent'): ?> badge-sent
                                <?php elseif($report->status == 'pending'): ?> badge-pending
                                <?php else: ?> badge-failed <?php endif; ?>">
                                <i class="fas 
                                    <?php if($report->status == 'sent'): ?> fa-check-circle
                                    <?php elseif($report->status == 'pending'): ?> fa-clock
                                    <?php else: ?> fa-times-circle <?php endif; ?> text-xs"></i>
                                <?php echo e(ucfirst($report->status)); ?>

                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
                            <p>No reports sent yet</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // ============ FILE UPLOAD HANDLING ============
    const uploadBox = document.getElementById('uploadBox');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFileBtn');
    
    uploadBox?.addEventListener('click', () => fileInput?.click());
    
    uploadBox?.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadBox?.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadBox?.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFile(e.dataTransfer.files[0]);
        }
    });
    
    fileInput?.addEventListener('change', function() {
        if (this.files.length) {
            handleFile(this.files[0]);
        }
    });
    
    function handleFile(file) {
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            Swal.fire('Error', 'File too large! Maximum size is 5MB.', 'error');
            fileInput.value = '';
            return;
        }
        
        fileName.textContent = file.name;
        const sizeKB = (file.size / 1024).toFixed(2);
        fileSize.textContent = `(${sizeKB} KB)`;
        filePreview.classList.remove('hidden');
    }
    
    removeFileBtn?.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
    });
    
    // ============ FORM SUBMIT ============
    document.getElementById('sendReportForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('sendReportBtn');
        btn.disabled = true;
        document.getElementById('sendSpinner').classList.remove('hidden');
        btn.querySelector('i').classList.add('hidden');
        btn.querySelector('span:not(#sendSpinner)').textContent = 'Sending...';
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.officer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/officer/send-report.blade.php ENDPATH**/ ?>