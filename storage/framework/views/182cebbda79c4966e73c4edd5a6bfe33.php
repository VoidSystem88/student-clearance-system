<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('header', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<!-- Stats Cards - 6 important stats -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <!-- Total Students -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Students</p>
                <p class="text-xl font-bold text-blue-600"><?php echo e($totalStudents ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-blue-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Total Staff -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Staff</p>
                <p class="text-xl font-bold text-purple-600"><?php echo e($totalStaff ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-purple-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Departments -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Depts</p>
                <p class="text-xl font-bold text-green-600"><?php echo e($totalDepartments ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-green-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Total Requests -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Requests</p>
                <p class="text-xl font-bold text-orange-600"><?php echo e($totalRequests ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-alt text-orange-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Pending -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Pending</p>
                <p class="text-xl font-bold text-yellow-600"><?php echo e($pendingRequests ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600 text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Cleared -->
    <div class="bg-white rounded-xl p-3 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs">Cleared</p>
                <p class="text-xl font-bold text-emerald-600"><?php echo e($clearedStudents ?? 0); ?></p>
            </div>
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <!-- Pending -->
    <div class="bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl p-4 text-white">
        <div class="flex justify-between">
            <div>
                <p class="text-white/80 text-sm">Pending Approval</p>
                <p class="text-3xl font-bold"><?php echo e($pendingRequests ?? 0); ?></p>
            </div>
            <i class="fas fa-clock text-4xl text-white/30"></i>
        </div>
    </div>

    <!-- Approved -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 text-white">
        <div class="flex justify-between">
            <div>
                <p class="text-white/80 text-sm">Approved</p>
                <p class="text-3xl font-bold"><?php echo e($approvedRequests ?? 0); ?></p>
            </div>
            <i class="fas fa-check-circle text-4xl text-white/30"></i>
        </div>
    </div>

    <!-- Rejected -->
    <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-xl p-4 text-white">
        <div class="flex justify-between">
            <div>
                <p class="text-white/80 text-sm">Rejected</p>
                <p class="text-3xl font-bold"><?php echo e($rejectedRequests ?? 0); ?></p>
            </div>
            <i class="fas fa-times-circle text-4xl text-white/30"></i>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Recent Requests Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800 text-sm">
                    <i class="fas fa-clock text-blue-600 mr-1"></i> Recent Requests
                </h3>
            </div>
            <a href="<?php echo e(route('admin.clearance-requests')); ?>" class="text-xs text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Student</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Dept</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = ($clearanceRequests ?? [])->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-xs">
                            <?php echo e($request->student->first_name ?? ''); ?> <?php echo e($request->student->last_name ?? ''); ?>

                        </td>
                        <td class="px-3 py-2 text-xs"><?php echo e($request->department->name ?? 'N/A'); ?></td>
                        <td class="px-3 py-2">
                            <?php if($request->status == 'pending'): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">Pending</span>
                            <?php elseif($request->status == 'approved'): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Approved</span>
                            <?php else: ?>
                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">Rejected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-400 text-xs">No requests</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-bolt text-yellow-600 mr-1"></i> Quick Actions
            </h3>
        </div>
        <div class="p-3 grid grid-cols-2 gap-2">
            <a href="<?php echo e(route('admin.students')); ?>" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-3 text-center transition">
                <i class="fas fa-user-plus text-blue-600 text-lg"></i>
                <p class="text-xs text-gray-700 mt-1">Add Student</p>
            </a>
            <a href="<?php echo e(route('admin.staffs')); ?>" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-3 text-center transition">
                <i class="fas fa-user-tie text-purple-600 text-lg"></i>
                <p class="text-xs text-gray-700 mt-1">Add Staff</p>
            </a>
            <a href="<?php echo e(route('admin.departments')); ?>" class="bg-green-50 hover:bg-green-100 rounded-lg p-3 text-center transition">
                <i class="fas fa-building text-green-600 text-lg"></i>
                <p class="text-xs text-gray-700 mt-1">Add Dept</p>
            </a>
            <a href="<?php echo e(route('admin.announcements')); ?>" class="bg-amber-50 hover:bg-amber-100 rounded-lg p-3 text-center transition">
                <i class="fas fa-bullhorn text-amber-600 text-lg"></i>
                <p class="text-xs text-gray-700 mt-1">Announce</p>
            </a>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Department Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-chart-bar text-blue-600 mr-1"></i> Requests by Department
            </h3>
        </div>
        <div class="p-3">
            <canvas id="departmentChart" height="200"></canvas>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-chart-line text-green-600 mr-1"></i> Monthly Submissions
            </h3>
        </div>
        <div class="p-3">
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Status Chart & Activity Logs -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Status Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-chart-pie text-purple-600 mr-1"></i> Overall Status
            </h3>
        </div>
        <div class="p-3">
            <canvas id="statusChart" height="180"></canvas>
            <div class="flex justify-center gap-4 mt-2 text-xs">
                <div class="flex items-center gap-1"><span class="w-2 h-2 bg-yellow-500 rounded-full"></span> Pending: <?php echo e($pendingRequests ?? 0); ?></div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Approved: <?php echo e($approvedRequests ?? 0); ?></div>
                <div class="flex items-center gap-1"><span class="w-2 h-2 bg-red-500 rounded-full"></span> Rejected: <?php echo e($rejectedRequests ?? 0); ?></div>
            </div>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-semibold text-gray-800 text-sm">
                <i class="fas fa-history text-gray-600 mr-1"></i> Recent Activities
            </h3>
            <a href="<?php echo e(route('admin.activity-logs')); ?>" class="text-xs text-blue-600 hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">User</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Action</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Module</th>
                        <th class="px-3 py-2 text-left text-xs text-gray-500">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = ($activityLogs ?? [])->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-xs text-gray-600"><?php echo e(Str::limit($log->user_email ?? 'System', 20)); ?></td>
                        <td class="px-3 py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                <?php if($log->action == 'created'): ?> bg-green-100 text-green-700
                                <?php elseif($log->action == 'updated'): ?> bg-blue-100 text-blue-700
                                <?php elseif($log->action == 'deleted'): ?> bg-red-100 text-red-700
                                <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
                                <?php echo e($log->action); ?>

                            </span>
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-600"><?php echo e($log->module); ?></td>
                        <td class="px-3 py-2 text-xs text-gray-400"><?php echo e($log->created_at ? $log->created_at->diffForHumans() : 'N/A'); ?></td>
                    </td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 text-xs">No activities</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Department Chart
    const deptLabels = <?php echo json_encode($departmentsList ?? [], 15, 512) ?>;
    const pendingData = <?php echo json_encode($pendingPerDept ?? [], 15, 512) ?>;
    const approvedData = <?php echo json_encode($approvedPerDept ?? [], 15, 512) ?>;
    const rejectedData = <?php echo json_encode($rejectedPerDept ?? [], 15, 512) ?>;

    if (document.getElementById('departmentChart')) {
        new Chart(document.getElementById('departmentChart'), {
            type: 'bar',
            data: {
                labels: deptLabels,
                datasets: [
                    { label: 'Pending', data: pendingData, backgroundColor: '#eab308', borderRadius: 4 },
                    { label: 'Approved', data: approvedData, backgroundColor: '#22c55e', borderRadius: 4 },
                    { label: 'Rejected', data: rejectedData, backgroundColor: '#ef4444', borderRadius: 4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    // Monthly Chart
    const months = <?php echo json_encode($months ?? [], 15, 512) ?>;
    const monthlyData = <?php echo json_encode($monthlySubmissions ?? [], 15, 512) ?>;

    if (document.getElementById('monthlyChart')) {
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{ label: 'Submissions', data: monthlyData, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', tension: 0.3, fill: true, pointBackgroundColor: '#22c55e', pointRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });
    }

    // Status Chart
    const pending = <?php echo e($pendingRequests ?? 0); ?>;
    const approved = <?php echo e($approvedRequests ?? 0); ?>;
    const rejected = <?php echo e($rejectedRequests ?? 0); ?>;

    if (document.getElementById('statusChart')) {
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: { labels: ['Pending', 'Approved', 'Rejected'], datasets: [{ data: [pending, approved, rejected], backgroundColor: ['#eab308', '#22c55e', '#ef4444'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } } }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>