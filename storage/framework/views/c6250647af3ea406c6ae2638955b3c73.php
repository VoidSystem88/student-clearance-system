<?php $__env->startSection('title', 'Verified Students'); ?>
<?php $__env->startSection('header', 'Verified Students'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Force dark mode */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .border-gray-200 {
        border-color: #374151 !important;
    }
    body.dark-mode .border-gray-100 {
        border-color: #374151 !important;
    }
    body.dark-mode .text-gray-800 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-600 {
        color: #9ca3af !important;
    }
    body.dark-mode .text-gray-500 {
        color: #9ca3af !important;
    }
    body.dark-mode .bg-gray-50 {
        background-color: #1f2937 !important;
    }
    body.dark-mode .divide-gray-100 {
        border-color: #374151 !important;
    }
    body.dark-mode .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
    }
    
    body.dark-mode .badge-verified {
        background: #065f46 !important;
        color: #86efac !important;
    }
    
    .badge-verified {
        background: #dcfce7;
        color: #166534;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .btn-remove {
        background: #fee2e2;
        color: #dc2626;
        padding: 4px 12px;
        border-radius: 6px;
        font-size: 11px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-remove:hover:not(:disabled) {
        background: #fecaca;
        color: #b91c1c;
    }
    
    body.dark-mode .btn-remove {
        background: #7f1d1d !important;
        color: #fca5a5 !important;
    }
    body.dark-mode .btn-remove:hover:not(:disabled) {
        background: #991b1b !important;
        color: #fecaca !important;
    }
    
    .spinner-dark {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(0,0,0,0.1);
        border-top-color: #2563eb;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    body.dark-mode .spinner-dark {
        border: 2px solid rgba(255,255,255,0.1) !important;
        border-top-color: #60a5fa !important;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .table-row:hover {
        background-color: #f8fafc;
    }
    body.dark-mode .table-row:hover {
        background-color: #1e293b !important;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-green-50">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-check-circle text-green-600 mr-2"></i> Verified Students
            <span class="text-sm font-normal text-gray-500 ml-2">(<?php echo e($verifiedStudents->count()); ?>)</span>
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Verified At</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $verifiedStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="table-row transition">
                    <td class="px-4 py-3 text-gray-500"><?php echo e($loop->iteration); ?></td>
                    <td class="px-4 py-3 font-mono font-medium text-gray-800"><?php echo e($v->student_id); ?></td>
                    <td class="px-4 py-3 text-gray-800">
                        
                        <?php if($v->student): ?>
                            <?php echo e($v->student->first_name ?? ''); ?> <?php echo e($v->student->last_name ?? ''); ?>

                        <?php else: ?>
                            <?php echo e($v->student_name ?? 'N/A'); ?>

                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-gray-500"><?php echo e($v->verified_at ? \Carbon\Carbon::parse($v->verified_at)->format('M d, Y h:i A') : 'N/A'); ?></td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="removeVerified(<?php echo e($v->id); ?>, this)" class="btn-remove">
                            <i class="fas fa-trash-alt"></i> Remove
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>No verified students yet</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
async function removeVerified(id, button) {
    const result = await Swal.fire({
        title: 'Remove Verification?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Remove',
        cancelButtonText: 'Cancel'
    });
    if (!result.isConfirmed) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-dark"></span>';
    
    try {
        const response = await fetch(`/officer/verified/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            await Swal.fire('Removed!', data.message, 'success');
            location.reload();
        } else {
            await Swal.fire('Error!', data.message, 'error');
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    } catch (error) {
        console.error('Error:', error);
        await Swal.fire('Error!', 'Network error. Please try again.', 'error');
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.officer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/officer/verified.blade.php ENDPATH**/ ?>