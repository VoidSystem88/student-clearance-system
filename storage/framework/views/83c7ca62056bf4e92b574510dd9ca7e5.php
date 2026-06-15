<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-6">
    <div class="bg-white rounded shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Edit Announcement</h1>
            <a href="<?php echo e(route('admin.announcements')); ?>" class="text-blue-600">← Back to Announcements</a>
        </div>
        
        <?php if($errors->any()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p><?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route('admin.announcement.update', $announcement->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Title</label>
                <input type="text" name="title" value="<?php echo e(old('title', $announcement->title)); ?>" 
                       class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="info" <?php echo e($announcement->type == 'info' ? 'selected' : ''); ?>>📘 Info</option>
                    <option value="warning" <?php echo e($announcement->type == 'warning' ? 'selected' : ''); ?>>⚠️ Warning</option>
                    <option value="success" <?php echo e($announcement->type == 'success' ? 'selected' : ''); ?>>✅ Success</option>
                    <option value="danger" <?php echo e($announcement->type == 'danger' ? 'selected' : ''); ?>>🔴 Danger</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Content</label>
                <textarea name="content" rows="5" class="w-full border rounded px-3 py-2" required><?php echo e(old('content', $announcement->content)); ?></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 mb-2">Start Date (Optional)</label>
                    <input type="date" name="start_date" value="<?php echo e($announcement->start_date ? $announcement->start_date->format('Y-m-d') : ''); ?>" 
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">End Date (Optional)</label>
                    <input type="date" name="end_date" value="<?php echo e($announcement->end_date ? $announcement->end_date->format('Y-m-d') : ''); ?>" 
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php echo e($announcement->is_active ? 'checked' : ''); ?> class="mr-2">
                    <span>Active</span>
                </label>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Announcement</button>
                <a href="<?php echo e(route('admin.announcements')); ?>" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/announcements/edit.blade.php ENDPATH**/ ?>