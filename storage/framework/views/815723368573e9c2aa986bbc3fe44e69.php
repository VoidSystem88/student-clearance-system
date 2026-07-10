<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('header', 'Support Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Pending Requests</p>
                <p class="text-2xl font-bold text-yellow-600"><?php echo e($pendingCount ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">In Progress</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo e($inProgressCount ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-spinner text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Resolved</p>
                <p class="text-2xl font-bold text-green-600"><?php echo e($resolvedCount ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Total Students</p>
                <p class="text-2xl font-bold text-purple-600"><?php echo e($totalStudents ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-users text-purple-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">Bug Reports</p>
                <p class="text-2xl font-bold text-red-600"><?php echo e($pendingBugReports ?? 0); ?></p>
            </div>
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bug text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Bug Reports Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-bug text-red-600 mr-2"></i> User Bug Reports
            </h3>
            <p class="text-sm text-gray-500">Issues reported by users who encountered problems</p>
        </div>
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchBugReports" placeholder="Search by name, email, or type..." 
                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-red-500 focus:border-red-500">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="bugReportsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">User</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Issue Type</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Message</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="bugReportsTableBody">
                <?php $__empty_1 = true; $__currentLoopData = $bugReports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm font-mono text-gray-600">#<?php echo e($report->id); ?></td>
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-800"><?php echo e($report->name ?? 'Anonymous'); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($report->email); ?></div>
                        <div class="text-xs text-gray-400"><?php echo e($report->student_id ?? 'No ID'); ?></div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            <?php if($report->type == 'bug'): ?> bg-red-100 text-red-700
                            <?php elseif($report->type == 'login_issue'): ?> bg-yellow-100 text-yellow-700
                            <?php elseif($report->type == 'registration_issue'): ?> bg-orange-100 text-orange-700
                            <?php elseif($report->type == 'clearance_issue'): ?> bg-purple-100 text-purple-700
                            <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
                            <?php echo e(ucfirst(str_replace('_', ' ', $report->type))); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600 max-w-xs"><?php echo e(Str::limit($report->message, 60)); ?></td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            <?php if($report->status == 'pending'): ?> bg-yellow-100 text-yellow-700
                            <?php elseif($report->status == 'reviewed'): ?> bg-blue-100 text-blue-700
                            <?php else: ?> bg-green-100 text-green-700 <?php endif; ?>">
                            <i class="fas 
                                <?php if($report->status == 'pending'): ?> fa-clock
                                <?php elseif($report->status == 'reviewed'): ?> fa-eye
                                <?php else: ?> fa-check <?php endif; ?> text-xs"></i>
                            <?php echo e(ucfirst($report->status)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500"><?php echo e($report->created_at->format('M d, Y h:i A')); ?></td>
                    <td class="px-5 py-3">
                        <button onclick="viewBugReport(<?php echo e($report->id); ?>, '<?php echo e(addslashes($report->message)); ?>', '<?php echo e(addslashes($report->name ?? 'Anonymous')); ?>', '<?php echo e($report->email); ?>', '<?php echo e($report->student_id ?? 'N/A'); ?>', '<?php echo e($report->type); ?>', '<?php echo e($report->status); ?>', '<?php echo e(addslashes($report->admin_response ?? '')); ?>')" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-eye"></i> View
                        </button>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No bug reports yet</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Support Requests Cards (Existing) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="cardsContainer">
    <div class="bg-blue-50 rounded-xl p-6 text-center card-item" data-name="requests support tickets manage view">
        <i class="fas fa-ticket-alt text-blue-600 text-4xl mb-3"></i>
        <h3 class="font-bold text-lg">Support Requests</h3>
        <p class="text-gray-600 text-sm mb-4">View and manage student support tickets</p>
        <a href="<?php echo e(route('support.requests')); ?>" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Go to Requests <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="bg-green-50 rounded-xl p-6 text-center card-item" data-name="students manage reset password accounts">
        <i class="fas fa-users text-green-600 text-4xl mb-3"></i>
        <h3 class="font-bold text-lg">Manage Students</h3>
        <p class="text-gray-600 text-sm mb-4">Reset passwords, manage accounts</p>
        <a href="<?php echo e(route('support.students')); ?>" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Go to Students <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <div class="bg-purple-50 rounded-xl p-6 text-center card-item" data-name="feedbacks respond suggestions comments">
        <i class="fas fa-star text-purple-600 text-4xl mb-3"></i>
        <h3 class="font-bold text-lg">Feedbacks</h3>
        <p class="text-gray-600 text-sm mb-4">View and respond to student feedback</p>
        <a href="<?php echo e(route('support.feedbacks')); ?>" class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
            Go to Feedbacks <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Bug Report View Modal -->
<div id="bugViewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100 bg-red-50 sticky top-0">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-bug text-red-600 mr-2"></i> Bug Report Details
            </h3>
            <button onclick="closeBugViewModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="p-5">
            <div id="bugViewContent" class="mb-4"></div>
            
            <form id="updateBugForm" method="POST" class="mt-4 pt-3 border-t">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="bug_report_id" id="bugReportId">
                <div class="mb-3">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Status</label>
                    <select name="status" id="bugStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500">
                        <option value="pending">Pending</option>
                        <option value="reviewed">Reviewed</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 text-sm font-medium mb-1">Response Message</label>
                    <textarea name="admin_response" id="bugAdminResponse" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500" placeholder="Type your response here..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeBugViewModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">Update Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // ============ SEARCH FOR BUG REPORTS ============
    document.getElementById('searchBugReports')?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#bugReportsTableBody tr');
        let hasResults = false;
        
        rows.forEach(row => {
            if (row.id === 'noResultRow') return;
            let text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        let noResultRow = document.getElementById('noResultRow');
        if (!hasResults && rows.length > 0 && rows[0].cells) {
            if (!noResultRow) {
                let tbody = document.getElementById('bugReportsTableBody');
                let tr = document.createElement('tr');
                tr.id = 'noResultRow';
                tr.innerHTML = `<td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                    <p>No bug reports found</p>
                                </td>`;
                tbody.appendChild(tr);
                noResultRow = tr;
            }
            noResultRow.style.display = '';
        } else if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    });
    
    // ============ VIEW BUG REPORT ============
    let currentBugId = null;
    
    function viewBugReport(id, message, name, email, studentId, type, status, adminResponse) {
        currentBugId = id;
        
        const typeFormatted = type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const statusBadgeClass = status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                 (status === 'reviewed' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700');
        
        document.getElementById('bugViewContent').innerHTML = `
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><strong class="text-gray-700">Name:</strong> <span class="text-gray-600">${escapeHtml(name)}</span></div>
                    <div><strong class="text-gray-700">Email:</strong> <span class="text-gray-600">${escapeHtml(email)}</span></div>
                    <div><strong class="text-gray-700">Student ID:</strong> <span class="text-gray-600">${escapeHtml(studentId)}</span></div>
                    <div><strong class="text-gray-700">Issue Type:</strong> <span class="text-gray-600">${typeFormatted}</span></div>
                </div>
                <div>
                    <strong class="text-gray-700">Message:</strong>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-600 text-sm">${escapeHtml(message)}</div>
                </div>
                ${adminResponse ? `
                <div>
                    <strong class="text-gray-700">Previous Response:</strong>
                    <div class="mt-1 p-3 bg-green-50 rounded-lg text-green-700 text-sm">${escapeHtml(adminResponse)}</div>
                </div>
                ` : ''}
                <div class="pt-2 border-t">
                    <strong class="text-gray-700">Current Status:</strong>
                    <span class="ml-2 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${statusBadgeClass}">
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </span>
                </div>
            </div>
        `;
        
        document.getElementById('bugReportId').value = id;
        document.getElementById('bugStatus').value = status;
        document.getElementById('bugAdminResponse').value = adminResponse || '';
        document.getElementById('updateBugForm').action = '/support/bug-reports/' + id + '/update';
        
        document.getElementById('bugViewModal').classList.remove('hidden');
        document.getElementById('bugViewModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeBugViewModal() {
        document.getElementById('bugViewModal').classList.add('hidden');
        document.getElementById('bugViewModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Close modal when clicking outside
    document.getElementById('bugViewModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeBugViewModal();
    });
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeBugViewModal();
    });
    
    // ============ SEARCH FOR CARDS ============
    const searchInput = document.getElementById('searchCards');
    const cards = document.querySelectorAll('.card-item');
    const cardsContainer = document.getElementById('cardsContainer');
    const noResults = document.getElementById('noResults');

    searchInput?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase().trim();
        let hasResults = false;
        
        cards.forEach(card => {
            const cardName = card.getAttribute('data-name') || '';
            const cardText = card.textContent.toLowerCase();
            
            if (searchTerm === '' || cardName.includes(searchTerm) || cardText.includes(searchTerm)) {
                card.style.display = '';
                hasResults = true;
            } else {
                card.style.display = 'none';
            }
        });
        
        if (!hasResults && searchTerm !== '') {
            noResults.classList.remove('hidden');
            cardsContainer.classList.add('hidden');
        } else {
            noResults.classList.add('hidden');
            cardsContainer.classList.remove('hidden');
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.support', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/support/dashboard.blade.php ENDPATH**/ ?>