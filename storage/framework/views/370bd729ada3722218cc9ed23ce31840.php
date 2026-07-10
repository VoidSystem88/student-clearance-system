<?php $__env->startSection('title', 'Reminders'); ?>
<?php $__env->startSection('header', 'Reminders'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/30">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-bullhorn text-blue-600 dark:text-blue-400 mr-2"></i> Announcements & Reminders
            </h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ann): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                <div class="flex gap-3">
                    <!-- Icon based on type with dark mode support -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 
                        <?php if($ann->type == 'info'): ?> bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400
                        <?php elseif($ann->type == 'warning'): ?> bg-yellow-100 dark:bg-yellow-900/50 text-yellow-600 dark:text-yellow-400
                        <?php elseif($ann->type == 'success'): ?> bg-green-100 dark:bg-green-900/50 text-green-600 dark:text-green-400
                        <?php else: ?> bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 <?php endif; ?>">
                        <i class="fas 
                            <?php if($ann->type == 'info'): ?> fa-info-circle
                            <?php elseif($ann->type == 'warning'): ?> fa-exclamation-triangle
                            <?php elseif($ann->type == 'success'): ?> fa-check-circle
                            <?php else: ?> fa-times-circle <?php endif; ?> text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start flex-wrap gap-1">
                            <h4 class="font-semibold text-gray-800 dark:text-gray-200"><?php echo e($ann->title); ?></h4>
                            <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($ann->created_at->format('M d, Y')); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1"><?php echo e($ann->content); ?></p>
                        <?php if($ann->start_date || $ann->end_date): ?>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                <i class="far fa-calendar-alt mr-1"></i>
                                <?php echo e($ann->start_date ? \Carbon\Carbon::parse($ann->start_date)->format('M d, Y') : 'Always'); ?> - 
                                <?php echo e($ann->end_date ? \Carbon\Carbon::parse($ann->end_date)->format('M d, Y') : 'Always'); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <i class="far fa-newspaper text-4xl mb-2 text-gray-300 dark:text-gray-600"></i>
                <p>No announcements at this time</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/reminders.blade.php ENDPATH**/ ?>