<?php $__env->startSection('title', 'Manage Officers'); ?>
<?php $__env->startSection('header', 'Manage Officers'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Add Officer Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition">
        <i class="fas fa-user-plus mr-2"></i> Add New Officer
    </button>
    
    <!-- Officers Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Department</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $officers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $officer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3"><?php echo e($officer->id); ?></td>
                    <td class="p-3 font-semibold"><?php echo e($officer->name); ?></td>
                    <td class="p-3"><?php echo e($officer->email); ?></td>
                    <td class="p-3"><?php echo e($officer->department->name ?? 'N/A'); ?></td>
                    <td class="p-3">
                        <?php if($officer->is_active): ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Active</span>
                        <?php else: ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <button onclick="openEditModal(<?php echo e($officer->id); ?>)" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="confirmDelete(<?php echo e($officer->id); ?>, '<?php echo e(addslashes($officer->name)); ?>')" 
                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <button onclick="toggleStatus(<?php echo e($officer->id); ?>, '<?php echo e(addslashes($officer->name)); ?>', <?php echo e($officer->is_active ? 'true' : 'false'); ?>)" 
                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition">
                            <i class="fas <?php echo e($officer->is_active ? 'fa-ban' : 'fa-check'); ?>"></i>
                            <?php echo e($officer->is_active ? 'Deactivate' : 'Activate'); ?>

                        </button>
                        <form id="delete-form-<?php echo e($officer->id); ?>" method="POST" action="<?php echo e(route('admin.officer.destroy', $officer->id)); ?>" class="hidden">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">No officers found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        <?php echo e($officers->links()); ?>

    </div>
</div>

<!-- Add/Edit Modal -->
<div id="officerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Add Officer</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="officerForm" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="methodField" value="POST">
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Department *</label>
                <select name="department_id" id="department_id" class="w-full border rounded px-3 py-2" required>
                    <option value="">-- Select Department --</option>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-3" id="passwordField">
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500">Leave blank to keep current password when editing</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Delete Officer?',
            text: `Are you sure you want to delete "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
    
    function toggleStatus(id, name, isActive) {
        const action = isActive ? 'deactivate' : 'activate';
        Swal.fire({
            title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} Officer?`,
            text: `Are you sure you want to ${action} "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'deactivate' ? '#d33' : '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${action} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/officers/${id}/toggle-status`;
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '<?php echo e(csrf_token()); ?>';
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = 'Add Officer';
        document.getElementById('officerForm').action = '<?php echo e(route("admin.officer.store")); ?>';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('department_id').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password').required = true;
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('officerModal').classList.remove('hidden');
        document.getElementById('officerModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id) {
        fetch(`/admin/officers/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = 'Edit Officer';
                document.getElementById('officerForm').action = `/admin/officers/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('name').value = data.name;
                document.getElementById('email').value = data.email;
                document.getElementById('department_id').value = data.department_id;
                document.getElementById('password').value = '';
                document.getElementById('password').required = false;
                document.getElementById('passwordField').style.display = 'block';
                document.getElementById('officerModal').classList.remove('hidden');
                document.getElementById('officerModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to load officer data', 'error');
            });
    }
    
    function closeModal() {
        document.getElementById('officerModal').classList.add('hidden');
        document.getElementById('officerModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    document.getElementById('officerModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/officers.blade.php ENDPATH**/ ?>