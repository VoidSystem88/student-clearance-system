<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('header', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<!-- ============ NOTIFICATIONS SECTION ============ -->
<?php
    $unreadNotifications = Auth::user()->notifications()
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->get();
?>

<?php if($unreadNotifications->count() > 0): ?>
<div class="mb-6 space-y-3">
    <?php $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $notifData = json_decode($notif->data, true);
        $icon = $notifData['icon'] ?? 'fa-bell';
        $color = $notifData['color'] ?? 'blue';
        
        $colorClass = match($color) {
            'purple' => 'border-purple-500 bg-purple-50 dark:bg-purple-900/30',
            'yellow' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30',
            'green' => 'border-green-500 bg-green-50 dark:bg-green-900/30',
            'red' => 'border-red-500 bg-red-50 dark:bg-red-900/30',
            default => 'border-blue-500 bg-blue-50 dark:bg-blue-900/30',
        };
        
        $iconColor = match($color) {
            'purple' => 'text-purple-600 dark:text-purple-400',
            'yellow' => 'text-yellow-600 dark:text-yellow-400',
            'green' => 'text-green-600 dark:text-green-400',
            'red' => 'text-red-600 dark:text-red-400',
            default => 'text-blue-600 dark:text-blue-400',
        };
    ?>
    
    <div class="rounded-xl p-4 shadow-sm border-l-4 <?php echo e($colorClass); ?> transition-all hover:shadow-md dark:border-opacity-50">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full <?php echo e(str_replace('border-l-4', '', $colorClass)); ?> flex items-center justify-center flex-shrink-0">
                <i class="fas <?php echo e($icon); ?> <?php echo e($iconColor); ?> text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h4 class="font-semibold text-gray-800 dark:text-white"><?php echo e($notif->title); ?></h4>
                    <span class="text-xs text-gray-400 dark:text-gray-500"><?php echo e($notif->created_at->diffForHumans()); ?></span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1"><?php echo e($notif->message); ?></p>
            </div>
            <button onclick="markAsRead(<?php echo e($notif->id); ?>)" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<!-- Course & Year Display -->
<div class="mb-3">
    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Course & Year</label>
    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200" value="<?php echo e($student->course_year ?? ($student->course . ' - ' . $student->year_level)); ?>" readonly>
</div>

<!-- Welcome Card with Dynamic Message -->
<div class="rounded-xl p-6 mb-6 text-white bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-800 dark:to-blue-950">
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <?php
                $isNewUser = $student->created_at && $student->created_at->diffInDays(now()) < 1;
            ?>
            
            <?php if($isNewUser): ?>
                <h2 class="text-2xl font-bold mb-2">🎉 Welcome, <?php echo e($student->first_name ?? $student->name ?? 'Student'); ?>!</h2>
                <p class="text-blue-100 text-sm">We're excited to have you here! Start your clearance journey today.</p>
            <?php else: ?>
                <h2 class="text-2xl font-bold mb-2">👋 Welcome back, <?php echo e($student->first_name ?? $student->name ?? 'Student'); ?>!</h2>
                <p class="text-blue-100 text-sm">Track your clearance progress here.</p>
            <?php endif; ?>
        </div>
        <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2 text-center">
            <p class="text-xs opacity-80">Account ID</p>
            <p class="font-mono text-sm font-bold"><?php echo e($student->account_id ?? 'N/A'); ?></p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Student ID Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Student ID</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white"><?php echo e($student->student_id ?? 'N/A'); ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-id-card text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Course & Year Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Course & Year</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white"><?php echo e($student->course_year ?? ($student->course . ' - ' . $student->year_level)); ?></p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-graduation-cap text-green-600 dark:text-green-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Progress Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Progress</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white"><?php echo e($approvedCount ?? 0); ?>/<?php echo e($totalDepartments ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Status</p>
                <p class="font-bold text-lg">
                    <?php if(isset($isFullyCleared) && $isFullyCleared): ?>
                        <span class="text-green-600 dark:text-green-400">✓ Cleared</span>
                    <?php else: ?>
                        <span class="text-yellow-600 dark:text-yellow-400">In Progress</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-flag-checkered text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
        <span class="font-semibold text-gray-700 dark:text-gray-300">Overall Clearance Progress</span>
        <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($approvedCount ?? 0); ?> out of <?php echo e($totalDepartments ?? 0); ?> departments cleared</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: <?php echo e(isset($totalDepartments) && $totalDepartments > 0 ? ($approvedCount ?? 0) / $totalDepartments * 100 : 0); ?>%"></div>
    </div>
    <?php if(isset($isFullyCleared) && $isFullyCleared): ?>
        <div class="mt-4 text-center">
            <a href="<?php echo e(route('student.clearance.print')); ?>" class="inline-flex items-center gap-2 bg-green-600 dark:bg-green-700 text-white px-5 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition">
                <i class="fas fa-download"></i>
                <span>Download Clearance Slip</span>
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// I-embed ang user data para magamit ni Void AI
window.VoidUserData = {
    // Basic Info
    id: <?php echo e(Auth::id()); ?>,
    name: "<?php echo e($student->first_name ?? Auth::user()->first_name ?? 'Student'); ?>",
    fullName: "<?php echo e(trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? ''))); ?>",
    studentId: "<?php echo e($student->student_id ?? 'N/A'); ?>",
    accountId: "<?php echo e($student->account_id ?? 'N/A'); ?>",
    email: "<?php echo e(Auth::user()->email ?? ''); ?>",
    
    // Academic Info
    course: "<?php echo e($student->course ?? 'N/A'); ?>",
    yearLevel: "<?php echo e($student->year_level ?? 'N/A'); ?>",
    courseYear: "<?php echo e($student->course_year ?? ($student->course . ' - ' . $student->year_level)); ?>",
    
    // Clearance Progress
    clearedCount: <?php echo e($approvedCount ?? 0); ?>,
    totalDepartments: <?php echo e($totalDepartments ?? 0); ?>,
    isFullyCleared: <?php echo e(isset($isFullyCleared) && $isFullyCleared ? 'true' : 'false'); ?>,
    
    // Pending departments (kung meron)
    pendingDepartments: <?php echo json_encode($pendingDepartments ?? [], 15, 512) ?>,
    
    // User Status
    isNewUser: <?php echo e(isset($isNewUser) && $isNewUser ? 'true' : 'false'); ?>,
    createdAt: "<?php echo e($student->created_at ?? Auth::user()->created_at ?? ''); ?>",
    
    // Role
    role: "<?php echo e(Auth::user()->role ?? 'student'); ?>"
};

console.log('✅ Dashboard: Void AI User Data Loaded');
console.log('👤 User:', window.VoidUserData.name);
console.log('📊 Progress:', window.VoidUserData.clearedCount + '/' + window.VoidUserData.totalDepartments);

// Function to mark notification as read
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              location.reload();
          }
      });
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/student/dashboard.blade.php ENDPATH**/ ?>