<?php $__env->startSection('title', 'Maintenance Access'); ?>

<?php $__env->startSection('header', 'Secure Access Required'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-red-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Maintenance Control</h2>
            <p class="text-gray-500 mt-2">This area is restricted. Please enter the access password.</p>
        </div>
        
        <?php if(session('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>
        
        <form method="GET" action="<?php echo e(route('support.maintenance')); ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-1"></i> Access Password
                </label>
                <input type="password" name="mpass" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-red-500 focus:border-red-500"
                    placeholder="Enter maintenance password" required autofocus>
            </div>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-unlock-alt"></i> Access Maintenance Panel
            </button>
        </form>
        
        <div class="mt-6 text-center text-xs text-gray-400">
            <i class="fas fa-info-circle"></i>
            Contact system administrator if you don't have access.
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.support', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/support/maintenance-password.blade.php ENDPATH**/ ?>