<?php $__env->startSection('title', 'Manage Announcements'); ?>
<?php $__env->startSection('header', 'Manage Announcements'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Add Announcement Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition">
        <i class="fas fa-plus mr-2"></i> Add Announcement
    </button>
    
    <!-- Announcements Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Title</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Content</th>
                    <th class="p-3 text-left">Dates</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3"><?php echo e($ann->id); ?></td>
                    <td class="p-3 font-semibold"><?php echo e($ann->title); ?></td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded text-xs text-white
                            <?php echo e($ann->type == 'info' ? 'bg-blue-500' : ''); ?>

                            <?php echo e($ann->type == 'warning' ? 'bg-yellow-500' : ''); ?>

                            <?php echo e($ann->type == 'success' ? 'bg-green-500' : ''); ?>

                            <?php echo e($ann->type == 'danger' ? 'bg-red-500' : ''); ?>">
                            <i class="fas 
                                <?php echo e($ann->type == 'info' ? 'fa-info-circle' : ''); ?>

                                <?php echo e($ann->type == 'warning' ? 'fa-exclamation-triangle' : ''); ?>

                                <?php echo e($ann->type == 'success' ? 'fa-check-circle' : ''); ?>

                                <?php echo e($ann->type == 'danger' ? 'fa-times-circle' : ''); ?> mr-1"></i>
                            <?php echo e(ucfirst($ann->type)); ?>

                        </span>
                    </td>
                    <td class="p-3 max-w-xs"><?php echo e(Str::limit($ann->content, 50)); ?></td>
                    <td class="p-3 text-sm">
                        <?php if($ann->start_date || $ann->end_date): ?>
                            <?php echo e($ann->start_date ? $ann->start_date->format('M d, Y') : 'Always'); ?>

                            -
                            <?php echo e($ann->end_date ? $ann->end_date->format('M d, Y') : 'Always'); ?>

                        <?php else: ?>
                            <span class="text-gray-400">Always</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <?php if($ann->is_active): ?>
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Active</span>
                        <?php else: ?>
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <a href="<?php echo e(route('admin.announcement.edit', $ann->id)); ?>" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm inline-block hover:bg-yellow-600 transition">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        
                        <form method="POST" action="<?php echo e(route('admin.announcement.destroy', $ann->id)); ?>" class="inline" onsubmit="return confirm('Delete this announcement?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-sm hover:bg-red-700 transition">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                        
                        <form method="POST" action="<?php echo e(route('admin.announcement.toggle', $ann->id)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="bg-gray-500 text-white px-2 py-1 rounded text-sm hover:bg-gray-600 transition">
                                <?php echo e($ann->is_active ? 'Deactivate' : 'Activate'); ?>

                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        <i class="fas fa-bullhorn text-3xl mb-2 text-gray-300"></i>
                        <p>No announcements found</p>
                        <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:underline">Create your first announcement</button>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if(method_exists($announcements, 'links')): ?>
        <div class="mt-4">
            <?php echo e($announcements->links()); ?>

        </div>
    <?php endif; ?>
</div>

<!-- Add Modal -->
<div id="annModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i> Add Announcement
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="annForm" method="POST" action="<?php echo e(route('admin.announcement.store')); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-heading text-gray-500 mr-1"></i> Title
                </label>
                <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-tag text-gray-500 mr-1"></i> Type
                </label>
                <select name="type" id="type" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="info"><i class="fas fa-info-circle"></i> Info</option>
                    <option value="warning"><i class="fas fa-exclamation-triangle"></i> Warning</option>
                    <option value="success"><i class="fas fa-check-circle"></i> Success</option>
                    <option value="danger"><i class="fas fa-times-circle"></i> Danger</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-file-alt text-gray-500 mr-1"></i> Content
                </label>
                <textarea name="content" id="content" rows="4" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1">
                        <i class="fas fa-calendar-alt text-gray-500 mr-1"></i> Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">
                        <i class="fas fa-calendar-check text-gray-500 mr-1"></i> End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">
                    <i class="fas fa-times mr-1"></i> Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle text-green-600 mr-2"></i> Add Announcement';
        document.getElementById('annForm').reset();
        document.getElementById('annModal').classList.remove('hidden');
        document.getElementById('annModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal() {
        document.getElementById('annModal').classList.add('hidden');
        document.getElementById('annModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('annModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/announcements.blade.php ENDPATH**/ ?>