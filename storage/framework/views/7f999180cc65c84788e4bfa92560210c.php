<?php $__env->startSection('title', 'Clearance Status'); ?>
<?php $__env->startSection('header', 'Clearance Status'); ?>

<?php $__env->startSection('content'); ?>
<!-- Summary Cards -->
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="rounded-xl p-3 border" style="background: rgba(34,197,94,0.1); border-color: rgba(34,197,94,0.2);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs" style="color: #16a34a;">Approved</p>
                <p class="text-xl font-bold" style="color: #15803d;"><?php echo e($approvedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-check-circle text-lg" style="color: #22c55e;"></i>
        </div>
    </div>
    <div class="rounded-xl p-3 border" style="background: rgba(234,179,8,0.1); border-color: rgba(234,179,8,0.2);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs" style="color: #ca8a04;">Pending</p>
                <p class="text-xl font-bold" style="color: #a16207;"><?php echo e($pendingCount ?? 0); ?></p>
            </div>
            <i class="fas fa-clock text-lg" style="color: #eab308;"></i>
        </div>
    </div>
    <div class="rounded-xl p-3 border" style="background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs" style="color: #dc2626;">Rejected</p>
                <p class="text-xl font-bold" style="color: #b91c1c;"><?php echo e($rejectedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-times-circle text-lg" style="color: #ef4444;"></i>
        </div>
    </div>
    <div class="rounded-xl p-3 border" style="background: rgba(107,114,128,0.1); border-color: rgba(107,114,128,0.2);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs" style="color: #6b7280;">Not Submitted</p>
                <p class="text-xl font-bold" style="color: #4b5563;"><?php echo e($notSubmittedCount ?? 0); ?></p>
            </div>
            <i class="fas fa-hourglass-start text-lg" style="color: #6b7280;"></i>
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
        <a href="<?php echo e(route('student.clearance.view-pdf')); ?>" target="_blank" class="flex items-center justify-center gap-1 bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 transition text-xs font-medium"><i class="fas fa-eye text-xs"></i> View Slip</a>
        <a href="<?php echo e(route('student.clearance.print')); ?>" class="flex items-center justify-center gap-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition text-xs font-medium"><i class="fas fa-download text-xs"></i> Download PDF</a>
    </div>
    <?php endif; ?>
</div>

<!-- Departments Cards -->
<div class="space-y-3">
    <?php $__empty_1 = true; $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $department): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $request = $clearanceRequests->get($department->id) ?? null;
            $status = $request ? $request->status : 'not_submitted';
            
            $statusColors = [
                'approved' => ['bg' => 'rgba(34,197,94,0.1)', 'text' => '#16a34a', 'badgeBg' => 'rgba(34,197,94,0.15)', 'badgeText' => '#15803d'],
                'rejected' => ['bg' => 'rgba(239,68,68,0.1)', 'text' => '#dc2626', 'badgeBg' => 'rgba(239,68,68,0.15)', 'badgeText' => '#b91c1c'],
                'pending' => ['bg' => 'rgba(234,179,8,0.1)', 'text' => '#ca8a04', 'badgeBg' => 'rgba(234,179,8,0.15)', 'badgeText' => '#a16207'],
                'not_submitted' => ['bg' => 'rgba(107,114,128,0.05)', 'text' => '#6b7280', 'badgeBg' => 'rgba(107,114,128,0.1)', 'badgeText' => '#4b5563'],
            ];
            $c = $statusColors[$status] ?? $statusColors['not_submitted'];
        ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100" style="background: <?php echo e($c['bg']); ?>;">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm"><?php echo e($department->name); ?></h4>
                        <?php if($department->handler_name): ?><div class="text-xs text-gray-500 mt-0.5"><?php echo e($department->handler_name); ?></div><?php endif; ?>
                    </div>
                    <div class="ml-2">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium" style="background: <?php echo e($c['badgeBg']); ?>; color: <?php echo e($c['badgeText']); ?>;">
                            <?php if($status == 'approved'): ?><i class="fas fa-check-circle text-xs"></i> Approved
                            <?php elseif($status == 'rejected'): ?><i class="fas fa-times-circle text-xs"></i> Rejected
                            <?php elseif($status == 'pending'): ?><i class="fas fa-clock text-xs"></i> Pending
                            <?php else: ?><i class="fas fa-hourglass-start text-xs"></i> Not Submitted
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="px-4 py-3 space-y-3">
                <div class="flex justify-between items-center text-xs"><span class="text-gray-500">Submitted Date:</span><span class="text-gray-700"><?php echo e($request ? $request->submitted_at->format('M d, Y') : '—'); ?></span></div>
                
                <?php if($status == 'rejected' && $request->remarks): ?>
                <div class="rounded-lg p-2" style="background: rgba(239,68,68,0.1);">
                    <p class="text-xs" style="color: #dc2626;"><i class="fas fa-comment-dots mr-1"></i><strong>Reason:</strong> <?php echo e($request->remarks); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="flex gap-2 pt-1">
                    <button onclick="viewRequirements(<?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-gray-600 text-white text-xs rounded-lg hover:bg-gray-700 transition"><i class="fas fa-list-check text-xs"></i> Requirements</button>
                    
                    <?php if(!$request || $status == 'rejected'): ?>
                        <button onclick="showSubmitForm(<?php echo e($department->id); ?>, '<?php echo e(addslashes($department->name)); ?>')" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition"><i class="fas fa-paper-plane text-xs"></i> Submit</button>
                    <?php elseif($status == 'pending'): ?>
                        <button onclick="cancelRequest(<?php echo e($request->id); ?>)" class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700 transition"><i class="fas fa-times-circle text-xs"></i> Cancel</button>
                    <?php elseif($status == 'approved'): ?>
                        <span class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-lg text-xs" style="background: rgba(34,197,94,0.15); color: #15803d;"><i class="fas fa-check-circle text-xs"></i> Completed</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="bg-white rounded-xl p-8 text-center text-gray-500"><i class="fas fa-building text-4xl mb-2 text-gray-300"></i><p>No departments available</p></div>
    <?php endif; ?>
</div>

<!-- Submit Modal -->
<div id="submitModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 w-full max-w-md max-h-[85vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800"><i class="fas fa-paper-plane text-blue-600 mr-2"></i> Submit Clearance</h3>
            <button onclick="hideSubmitForm()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <div class="rounded-lg p-3 mb-4" style="background: rgba(234,179,8,0.1); border: 1px solid rgba(234,179,8,0.3);">
            <p class="text-xs" style="color: #a16207;"><i class="fas fa-info-circle mr-1"></i> At least <strong>check the box</strong> OR <strong>upload a photo</strong> to enable submit.</p>
        </div>
        
        <div class="mb-4">
            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition">
                <input type="checkbox" id="onTheList" class="w-4 h-4 text-green-600 rounded focus:ring-green-500" onchange="document.getElementById('onTheListInput').value = this.checked ? '1' : '0'; checkSubmitEligibility();">
                <div><span class="text-sm font-medium text-gray-800">I'm on the verified list</span><p class="text-xs text-gray-500">Check if your name is listed</p></div>
            </label>
        </div>
        
        <form id="clearanceForm" method="POST" action="<?php echo e(route('student.clearance.submit')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="department_id" id="departmentId">
            <input type="hidden" name="on_the_list" id="onTheListInput" value="0">
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Upload Proof (Optional)</label>
                <input type="file" name="attachment" id="attachment" class="hidden" accept=".jpg,.jpeg,.png" onchange="validateFileSize(this); showFileName(this); checkSubmitEligibility();">
                <button type="button" onclick="document.getElementById('attachment').click()" class="w-full py-2 px-3 rounded-lg text-sm font-medium bg-blue-600 text-white"><i class="fas fa-folder-open mr-2"></i> Choose File</button>
                <div id="selectedFileName" class="text-xs text-gray-500 mt-1 hidden"></div>
                <p class="text-xs text-gray-400 mt-1">JPG, PNG (Max 5MB) - Optional</p>
                <div id="fileSizeWarning" class="hidden text-red-500 text-xs mt-1"></div>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium"><i class="fas fa-comment-dots mr-1"></i> Message (Optional)</label>
                <textarea name="request_message" id="request_message" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" placeholder="Add a message..."></textarea>
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t border-gray-200">
                <button type="button" onclick="confirmCancel()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" id="submitBtn" disabled class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"><i class="fas fa-paper-plane mr-1"></i> Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- Requirements Modal -->
<div id="requirementsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-5 w-full max-w-md">
        <div class="flex justify-between items-center mb-3"><h3 id="requirementsModalTitle" class="text-lg font-bold text-gray-800"><i class="fas fa-list-check text-blue-600 mr-2"></i> Requirements</h3><button onclick="closeRequirementsModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button></div>
        <div id="requirementsList" class="space-y-2 max-h-80 overflow-y-auto"><div class="text-center py-6 text-gray-500 text-sm"><i class="fas fa-spinner fa-spin"></i> Loading...</div></div>
        <div class="mt-3 flex justify-end"><button onclick="closeRequirementsModal()" class="px-3 py-1.5 bg-gray-300 rounded-lg text-xs hover:bg-gray-400 transition text-gray-700">Close</button></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showToast(m, t) {
        const toast = document.createElement('div');
        toast.className = `fixed top-16 right-3 left-3 z-50 px-3 py-2 rounded-lg shadow-lg text-white text-sm transition-all duration-300`;
        toast.style.background = t === 'success' ? '#22c55e' : '#ef4444';
        toast.innerHTML = '<i class="fas ' + (t === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + ' mr-2"></i>' + m;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
    }
    
    function cancelRequest(id) {
        Swal.fire({ title: 'Cancel Request?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes', cancelButtonText: 'No' }).then(r => {
            if (r.isConfirmed) {
                Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                fetch('/student/clearance/' + id + '/cancel', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json' } })
                .then(r => r.json()).then(d => {
                    if (d.success) Swal.fire({ icon: 'success', title: 'Cancelled!', timer: 1500, showConfirmButton: false }).then(() => location.reload());
                    else Swal.fire({ icon: 'error', title: 'Error', text: d.message });
                });
            }
        });
    }
    
    function confirmCancel() {
        Swal.fire({ title: 'Cancel?', text: 'Your input will be discarded.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes', cancelButtonText: 'No' }).then(r => { if (r.isConfirmed) hideSubmitForm(); });
    }
    
    function viewRequirements(did, dname) {
        const yr = window.VoidUserData?.yearLevel || '1st Year';
        document.getElementById('requirementsModalTitle').innerHTML = '<i class="fas fa-list-check text-blue-600 mr-2"></i>' + dname + ' (' + yr + ')';
        document.getElementById('requirementsList').innerHTML = '<div class="text-center py-6"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
        fetch('/api/departments/' + did + '/requirements?year_level=' + encodeURIComponent(yr)).then(r => r.json()).then(d => {
            if (d.requirements && d.requirements.length > 0) {
                let h = '<div class="space-y-2">';
                d.requirements.forEach(r => h += `<div class="flex items-start gap-2 p-2 bg-gray-50 rounded-lg"><i class="fas fa-check-circle text-green-500 text-xs mt-0.5"></i><div class="flex-1"><p class="text-gray-800 text-sm">${r.requirement_name}</p>${r.is_required ? '<span class="text-xs text-red-500">Required</span>' : '<span class="text-xs text-gray-400">Optional</span>'}</div></div>`);
                document.getElementById('requirementsList').innerHTML = h + '</div>';
            } else document.getElementById('requirementsList').innerHTML = '<div class="text-center py-6 text-gray-500"><i class="fas fa-inbox text-3xl mb-2"></i><p class="text-sm">No requirements.</p></div>';
        }).catch(() => document.getElementById('requirementsList').innerHTML = '<div class="text-center py-6 text-red-500">Failed to load.</div>');
        document.getElementById('requirementsModal').classList.remove('hidden'); document.getElementById('requirementsModal').classList.add('flex'); document.body.style.overflow = 'hidden';
    }
    
    function closeRequirementsModal() { document.getElementById('requirementsModal').classList.add('hidden'); document.getElementById('requirementsModal').classList.remove('flex'); document.body.style.overflow = ''; }
    
    function validateFileSize(input) {
        const w = document.getElementById('fileSizeWarning');
        if (input.files[0] && input.files[0].size > 5 * 1024 * 1024) { w.innerHTML = 'File too large. Max 5MB.'; w.classList.remove('hidden'); input.value = ''; checkSubmitEligibility(); return false; }
        else { w.classList.add('hidden'); return true; }
    }
    
    function showFileName(input) {
        const d = document.getElementById('selectedFileName');
        if (input.files[0]) { d.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i>' + input.files[0].name; d.classList.remove('hidden'); }
        else d.classList.add('hidden');
    }
    
    function checkSubmitEligibility() {
        const isChecked = document.getElementById('onTheList').checked;
        const hasFile = document.getElementById('attachment').files && document.getElementById('attachment').files.length > 0;
        document.getElementById('submitBtn').disabled = !(isChecked || hasFile);
    }
    
    function showSubmitForm(did, dname) {
        document.getElementById('departmentId').value = did;
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-paper-plane text-blue-600 mr-2"></i>' + dname;
        document.getElementById('onTheList').checked = false;
        document.getElementById('onTheListInput').value = '0';
        document.getElementById('attachment').value = '';
        document.getElementById('selectedFileName').classList.add('hidden');
        document.getElementById('fileSizeWarning').classList.add('hidden');
        document.getElementById('request_message').value = '';
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-paper-plane mr-1"></i> Submit';
        document.getElementById('submitModal').classList.remove('hidden');
        document.getElementById('submitModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function hideSubmitForm() {
        document.getElementById('submitModal').classList.add('hidden');
        document.getElementById('submitModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    document.getElementById('clearanceForm')?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('attachment');
        if (fileInput.files && fileInput.files.length > 0 && fileInput.files[0].size > 5 * 1024 * 1024) {
            e.preventDefault(); showToast('File too large. Max 5MB.', 'error'); return false;
        }
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
    });
    
    document.getElementById('submitModal')?.addEventListener('click', e => { if (e.target === this) hideSubmitForm(); });
    document.getElementById('requirementsModal')?.addEventListener('click', e => { if (e.target === this) closeRequirementsModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') { hideSubmitForm(); closeRequirementsModal(); } });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/clearance.blade.php ENDPATH**/ ?>