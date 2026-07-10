<?php $__env->startSection('title', 'Feedbacks'); ?>
<?php $__env->startSection('header', 'Student Feedbacks'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-purple-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-star text-purple-600 mr-2"></i> Student Feedbacks
            </h3>
            <p class="text-sm text-gray-500">View and respond to student feedback</p>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchFeedback" placeholder="Search by student name, message, or category..." 
                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full" id="feedbacksTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Rating</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Message</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="feedbacksTableBody">
                <?php $__empty_1 = true; $__currentLoopData = $feedbacks ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm font-mono text-gray-600">#<?php echo e($fb->id); ?></td>
                    <td class="px-5 py-3">
                        <div class="font-medium text-gray-800"><?php echo e($fb->user->first_name ?? ''); ?> <?php echo e($fb->user->last_name ?? ''); ?></div>
                        <div class="text-xs text-gray-500"><?php echo e($fb->user->student_id ?? ''); ?></div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $fb->rating): ?>
                                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                                <?php else: ?>
                                    <i class="far fa-star text-gray-300 text-sm"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            <?php if($fb->category == 'bug'): ?> bg-red-100 text-red-700
                            <?php elseif($fb->category == 'feature'): ?> bg-purple-100 text-purple-700
                            <?php elseif($fb->category == 'improvement'): ?> bg-blue-100 text-blue-700
                            <?php elseif($fb->category == 'experience'): ?> bg-green-100 text-green-700
                            <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
                            <?php echo e(ucfirst($fb->category)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-600 max-w-xs"><?php echo e(Str::limit($fb->message, 50)); ?></td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                            <?php if($fb->status == 'pending'): ?> bg-yellow-100 text-yellow-700
                            <?php elseif($fb->status == 'reviewed'): ?> bg-blue-100 text-blue-700
                            <?php else: ?> bg-green-100 text-green-700 <?php endif; ?>">
                            <i class="fas 
                                <?php if($fb->status == 'pending'): ?> fa-clock
                                <?php elseif($fb->status == 'reviewed'): ?> fa-eye
                                <?php else: ?> fa-check <?php endif; ?> text-xs"></i>
                            <?php echo e(ucfirst($fb->status)); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <button onclick="viewFeedback(this)" 
                                data-id="<?php echo e($fb->id); ?>"
                                data-message="<?php echo e(addslashes($fb->message)); ?>"
                                data-student="<?php echo e(addslashes(($fb->user->first_name ?? '') . ' ' . ($fb->user->last_name ?? ''))); ?>"
                                data-student-id="<?php echo e(addslashes($fb->user->student_id ?? '')); ?>"
                                data-rating="<?php echo e($fb->rating); ?>"
                                data-category="<?php echo e($fb->category); ?>"
                                data-status="<?php echo e($fb->status); ?>"
                                data-response="<?php echo e(addslashes($fb->admin_response)); ?>"
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <?php if($fb->status == 'pending'): ?>
                        <button onclick="respondFeedback(<?php echo e($fb->id); ?>)" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 ml-2">
                            <i class="fas fa-reply"></i> Respond
                        </button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No feedback yet</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- View Feedback Modal -->
<div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Feedback Details</h3>
            <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <div id="viewContent"></div>
        <div class="flex justify-end mt-4">
            <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Close</button>
        </div>
    </div>
</div>

<!-- Respond Feedback Modal -->
<div id="respondModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-green-600">Respond to Feedback</h3>
            <button onclick="closeRespondModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="respondForm" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Admin Response</label>
                <textarea name="admin_response" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2" required placeholder="Type your response here..."></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeRespondModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Send Response</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search functionality for Feedbacks
    document.getElementById('searchFeedback')?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#feedbacksTableBody tr');
        let hasResults = false;
        
        rows.forEach(row => {
            // Skip if it's a no-result row
            if (row.id === 'noResultRow') return;
            
            let text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Show/hide no results message
        let noResultRow = document.getElementById('noResultRow');
        if (!hasResults && rows.length > 0 && rows[0].cells) {
            if (!noResultRow) {
                let tbody = document.getElementById('feedbacksTableBody');
                let tr = document.createElement('tr');
                tr.id = 'noResultRow';
                tr.innerHTML = `<td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                    <p>No feedback found for "<span id="searchTermDisplay"></span>"</p>
                                  </td>`;
                tbody.appendChild(tr);
                noResultRow = tr;
            }
            document.getElementById('searchTermDisplay').innerText = searchTerm;
            noResultRow.style.display = '';
        } else if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    });
    
    // View Feedback function - fixed to properly display messages
    function viewFeedback(button) {
        // Get data from data attributes
        const id = button.getAttribute('data-id');
        const message = button.getAttribute('data-message') || '';
        const student = button.getAttribute('data-student') || '';
        const studentId = button.getAttribute('data-student-id') || '';
        const rating = parseInt(button.getAttribute('data-rating')) || 0;
        const category = button.getAttribute('data-category') || '';
        const status = button.getAttribute('data-status') || '';
        const admin_response = button.getAttribute('data-response') || '';
        
        // Create star display
        let stars = '';
        for(let i = 1; i <= 5; i++) {
            if(i <= rating) {
                stars += '<i class="fas fa-star text-yellow-500"></i>';
            } else {
                stars += '<i class="far fa-star text-gray-300"></i>';
            }
        }
        
        // Escape HTML and preserve line breaks
        const escapedMessage = escapeHtml(message).replace(/\n/g, '<br>');
        const escapedStudent = escapeHtml(student);
        const escapedStudentId = escapeHtml(studentId);
        const escapedResponse = admin_response ? escapeHtml(admin_response).replace(/\n/g, '<br>') : '';
        
        // Get status color and icon
        let statusColor = '';
        let statusIcon = '';
        if(status === 'pending') {
            statusColor = 'text-yellow-700 bg-yellow-50';
            statusIcon = 'fa-clock';
        } else if(status === 'reviewed') {
            statusColor = 'text-blue-700 bg-blue-50';
            statusIcon = 'fa-eye';
        } else {
            statusColor = 'text-green-700 bg-green-50';
            statusIcon = 'fa-check';
        }
        
        document.getElementById('viewContent').innerHTML = `
            <div class="space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-semibold text-gray-900">${escapedStudent}</div>
                        <div class="text-xs text-gray-500">ID: ${escapedStudentId}</div>
                    </div>
                    <div class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium ${statusColor}">
                        <i class="fas ${statusIcon} text-xs"></i>
                        ${status.charAt(0).toUpperCase() + status.slice(1)}
                    </div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-700 mb-1">Rating</div>
                    <div class="flex gap-0.5">${stars}</div>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-700 mb-1">Category</div>
                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium
                        ${category === 'bug' ? 'bg-red-100 text-red-700' : 
                          category === 'feature' ? 'bg-purple-100 text-purple-700' : 
                          category === 'improvement' ? 'bg-blue-100 text-blue-700' : 
                          category === 'experience' ? 'bg-green-100 text-green-700' : 
                          'bg-gray-100 text-gray-700'}">
                        ${category.charAt(0).toUpperCase() + category.slice(1)}
                    </span>
                </div>
                
                <div>
                    <div class="text-sm font-medium text-gray-700 mb-1">Message</div>
                    <div class="text-gray-600 p-3 bg-gray-50 rounded-lg text-sm leading-relaxed">${escapedMessage || '<em class="text-gray-400">No message</em>'}</div>
                </div>
                
                ${escapedResponse ? `
                <div>
                    <div class="text-sm font-medium text-green-700 mb-1">Admin Response</div>
                    <div class="text-green-700 p-3 bg-green-50 rounded-lg text-sm leading-relaxed">${escapedResponse}</div>
                </div>
                ` : ''}
                
                <div class="text-xs text-gray-400 border-t pt-3 mt-2">
                    Feedback ID: #${id}
                </div>
            </div>
        `;
        document.getElementById('viewModal').classList.remove('hidden');
        document.getElementById('viewModal').classList.add('flex');
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function closeViewModal() {
        document.getElementById('viewModal').classList.add('hidden');
        document.getElementById('viewModal').classList.remove('flex');
    }
    
    function respondFeedback(id) {
        document.getElementById('respondForm').action = '/support/feedback/' + id + '/respond';
        document.getElementById('respondModal').classList.remove('hidden');
        document.getElementById('respondModal').classList.add('flex');
    }
    
    function closeRespondModal() {
        document.getElementById('respondModal').classList.add('hidden');
        document.getElementById('respondModal').classList.remove('flex');
    }
    
    // Close modals when clicking outside
    document.getElementById('viewModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewModal();
        }
    });
    
    document.getElementById('respondModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRespondModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.support', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/support/feedbacks.blade.php ENDPATH**/ ?>