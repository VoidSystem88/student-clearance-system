<?php $__env->startSection('title', 'Notifications'); ?>
<?php $__env->startSection('header', 'All Notifications'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-bell text-blue-600 mr-2"></i> All Notifications
        </h3>
        <form method="POST" action="<?php echo e(route('admin.notifications.mark-all-read')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-check-double mr-1"></i> Mark all as read
            </button>
        </form>
    </div>
    
    <div class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="p-4 hover:bg-gray-50 transition <?php echo e(!$notification->is_read ? 'bg-blue-50' : ''); ?>">
            <div class="flex gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center
                    <?php if($notification->type == 'new_student'): ?> bg-green-100 text-green-600
                    <?php elseif($notification->type == 'new_request'): ?> bg-yellow-100 text-yellow-600
                    <?php else: ?> bg-blue-100 text-blue-600 <?php endif; ?>">
                    <?php if($notification->type == 'new_student'): ?>
                        <i class="fas fa-user-graduate"></i>
                    <?php elseif($notification->type == 'new_request'): ?>
                        <i class="fas fa-ticket-alt"></i>
                    <?php else: ?>
                        <i class="fas fa-bell"></i>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium text-gray-800"><?php echo e($notification->title); ?></h4>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($notification->message); ?></p>
                            <p class="text-xs text-gray-400 mt-2"><?php echo e($notification->created_at->diffForHumans()); ?></p>
                        </div>
                        <?php if(!$notification->is_read): ?>
                            <form method="POST" action="<?php echo e(route('admin.notifications.mark-read', $notification->id)); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">Mark as read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="p-8 text-center text-gray-500">
            <i class="fas fa-bell-slash text-4xl mb-2 text-gray-300"></i>
            <p>No notifications yet</p>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="px-5 py-4 border-t border-gray-100">
        <?php echo e($notifications->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/notifications.blade.php ENDPATH**/ ?>