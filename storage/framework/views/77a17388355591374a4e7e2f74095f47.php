<?php $__env->startSection('title', 'Export CSV'); ?>
<?php $__env->startSection('header', 'Export CSV'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Force dark mode */
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
    body.dark-mode .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
    }
    
    body.dark-mode .bg-green-100 {
        background-color: #064e3b !important;
    }
    body.dark-mode .text-green-600 {
        color: #86efac !important;
    }
    body.dark-mode .bg-gray-200 {
        background-color: #374151 !important;
    }
    body.dark-mode .text-gray-700 {
        color: #e5e7eb !important;
    }
    body.dark-mode .bg-blue-50 {
        background-color: #1e3a5f !important;
    }
    body.dark-mode .text-blue-700 {
        color: #60a5fa !important;
    }
    
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
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-file-csv text-2xl text-green-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Export Verified Students</h3>
            <p class="text-sm text-gray-500">Download verified students list as CSV file</p>
        </div>
        
        <form method="POST" action="<?php echo e(route('officer.export.csv')); ?>" id="exportForm">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Name (Optional)</label>
                <input type="text" name="event_name" id="eventName" 
                       placeholder="e.g., Graduation 2024"
                       class="w-full p-2.5 border border-gray-300 rounded-lg bg-white text-gray-800 focus:ring-2 focus:ring-green-500">
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg mb-4">
                <p class="text-sm text-blue-700 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> 
                    <strong><?php echo e($verifiedCount ?? 0); ?></strong> verified students will be exported.
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="<?php echo e(route('officer.dashboard')); ?>" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-center hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
                <button type="submit" id="exportBtn" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg transition font-medium">
                    <i class="fas fa-file-csv mr-2"></i> Export
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('exportForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('exportBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Exporting...';
        // ✅ Allow form to submit normally for file download
        // No need to prevent default - this will download the file
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.officer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/officer/export.blade.php ENDPATH**/ ?>