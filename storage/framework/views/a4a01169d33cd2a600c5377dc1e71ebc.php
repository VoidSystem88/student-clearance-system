<?php $__env->startSection('title', 'My Profile'); ?>
<?php $__env->startSection('header', 'My Profile'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 gradient-banner rounded-xl mx-4 mt-4">
    <h3 class="font-semibold text-gray-800 dark:text-white">
        <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 mr-2"></i> Personal Information
    </h3>
    <p class="text-sm text-gray-500 dark:text-gray-400">View and update your account information</p>
</div>
    
    <div class="p-5">
        <?php if(session('success')): ?>
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-3 rounded mb-4">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-3 rounded mb-4">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('student.profile.update')); ?>">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Student ID (Readonly) -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Student ID</label>
                    <input type="text" value="<?php echo e($student->student_id); ?>" 
                           class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                           readonly disabled>
                </div>
                
                <!-- Account ID (Readonly) -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Account ID</label>
                    <input type="text" value="<?php echo e($student->account_id); ?>" 
                           class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                           readonly disabled>
                </div>
                
                <!-- First Name -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="first_name" value="<?php echo e(old('first_name', $student->first_name)); ?>" 
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                    <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <!-- Last Name -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo e(old('last_name', $student->last_name)); ?>" 
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                    <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <!-- Email -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $student->email)); ?>" 
                           class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                
                <!-- Course - READONLY -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Course</label>
                    <input type="text" value="<?php echo e($student->course); ?>" 
                           class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                           readonly disabled>
                    <input type="hidden" name="course" value="<?php echo e($student->course); ?>">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Course cannot be changed. Please contact admin if you need to update your course.
                    </p>
                </div>
                
                <!-- Year Level - READONLY -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Year Level</label>
                    <input type="text" value="<?php echo e($student->year_level); ?>" 
                           class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                           readonly disabled>
                    <input type="hidden" name="year_level" value="<?php echo e($student->year_level); ?>">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Year level cannot be changed. Please contact admin if you need to update your year level.
                    </p>
                </div>
                
                <!-- New Password -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">New Password (optional)</label>
                    <div class="relative">
                        <input type="password" name="password" id="password_field" 
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 pr-10 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <button type="button" onclick="togglePassword('password_field', 'passIcon')" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            <i id="passIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Confirm Password -->
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confirm_password_field" 
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 pr-10 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <button type="button" onclick="togglePassword('confirm_password_field', 'confirmIcon')" 
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                            <i id="confirmIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-2"></i> Update Profile
                </button>
                <a href="<?php echo e(route('student.dashboard')); ?>" class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-5 py-2 rounded-lg text-sm hover:bg-gray-400 dark:hover:bg-gray-500 transition inline-block">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/profile.blade.php ENDPATH**/ ?>