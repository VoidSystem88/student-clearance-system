<?php $__env->startSection('title', 'Manage Students'); ?>
<?php $__env->startSection('header', 'Manage Students'); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <svg class="w-5 h-5 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Student List
            </h3>
            <p class="text-sm text-gray-500">Manage student accounts, reset passwords, and update year levels</p>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="searchStudent" placeholder="Search by name, student ID, or email..." 
                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
    
    <!-- ============ BULK YEAR LEVEL UPDATE SECTION ============ -->
    <div class="mx-5 my-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
            </svg>
            <h4 class="font-semibold text-gray-800">Bulk Year Level Update</h4>
            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Irreversible
            </span>
        </div>
        <p class="text-xs text-gray-500 mb-3">Update multiple students' year level at once. This action cannot be undone.</p>
        
        <form method="POST" action="<?php echo e(route('support.bulk.year.update')); ?>" id="bulkUpdateForm">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs text-gray-500 font-medium">Selection Type</label>
                    <select name="selection_type" id="selectionType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="all">📋 All Students</option>
                        <option value="by_course">📚 By Course Only</option>
                        <option value="selected">☑️ Selected Students Only</option>
                    </select>
                </div>
                <div id="courseSelectDiv" style="display: none;">
                    <label class="block text-xs text-gray-500 font-medium">Filter by Course</label>
                    <select name="course" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white">
                        <option value="">-- All Courses --</option>
                        <?php $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->code); ?>"><?php echo e($course->code); ?> - <?php echo e($course->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-medium">From Year Level</label>
                    <select name="from_year" id="fromYear" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white" required>
                        <option value="">-- Select --</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-medium">To Year Level</label>
                    <select name="to_year" id="toYear" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white" required>
                        <option value="">-- Select --</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                        <option value="Graduated">🎓 Graduated</option>
                    </select>
                </div>
                <button type="submit" id="bulkUpdateBtn" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                    Update Year Level
                </button>
            </div>
            
            <!-- ============ SELECTED STUDENTS SECTION (may search at separate list) ============ -->
            <div id="selectedStudentsDiv" style="display: none;" class="mt-4 p-4 bg-white rounded-lg border border-gray-200">
                <div class="flex justify-between items-center mb-3 flex-wrap gap-2">
                    <p class="text-sm font-medium text-gray-700 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Select students to update:
                    </p>
                    <div class="relative">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="selectedStudentSearch" placeholder="Search students by name or ID..." 
                               class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm w-64">
                    </div>
                </div>
                
                <!-- Select All / Clear All Buttons -->
                <div class="flex gap-2 mb-3">
                    <button type="button" id="selectAllStudents" class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Select All
                    </button>
                    <button type="button" id="clearAllStudents" class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Clear All
                    </button>
                </div>
                
                <!-- Students List with Scroll -->
                <div class="border rounded-lg max-h-64 overflow-y-auto bg-gray-50">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 sticky top-0">
                            <tr>
                                <th class="px-3 py-2 text-left w-10">
                                    <input type="checkbox" id="headerCheckbox" class="rounded">
                                </th>
                                <th class="px-3 py-2 text-left">Student Name</th>
                                <th class="px-3 py-2 text-left">Student ID</th>
                                <th class="px-3 py-2 text-left">Course</th>
                                <th class="px-3 py-2 text-left">Current Year</th>
                            </tr>
                        </thead>
                        <tbody id="selectedStudentsTableBody">
                            <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="student-row border-b hover:bg-gray-100" data-name="<?php echo e(strtolower($student->first_name . ' ' . $student->last_name)); ?>" data-id="<?php echo e($student->id); ?>" data-student-id="<?php echo e($student->student_id); ?>">
                                <td class="px-3 py-2">
                                    <input type="checkbox" name="student_ids[]" value="<?php echo e($student->id); ?>" class="student-checkbox rounded">
                                </td>
                                <td class="px-3 py-2"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                <td class="px-3 py-2 font-mono text-xs"><?php echo e($student->student_id); ?></td>
                                <td class="px-3 py-2"><?php echo e($student->course); ?></td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs"><?php echo e($student->year_level); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Selected Count Display -->
                <div class="mt-3 text-sm text-gray-600 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span id="selectedCount">0</span> student(s) selected
                </div>
            </div>
        </form>
    </div>
    
    <!-- Students Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="studentsTable">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Account ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course & Year</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="studentsTableBody">
                <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono text-sm text-gray-600"><?php echo e($student->student_id); ?></td>
                    <td class="px-5 py-3 font-mono text-sm text-gray-600"><?php echo e($student->account_id); ?></td>
                    <td class="px-5 py-3 text-gray-800"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($student->course_year ?? $student->course . ' - ' . $student->year_level); ?></td>
                    <td class="px-5 py-3">
                        <?php if($student->is_active): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <svg class="w-2 h-2 fill-current" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Active
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <svg class="w-2 h-2 fill-current" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Inactive
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="openEditModal(<?php echo e($student->id); ?>)" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                            <button onclick="resetPassword(<?php echo e($student->id); ?>, '<?php echo e(addslashes($student->first_name . ' ' . $student->last_name)); ?>')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Reset PW
                            </button>
                            <button onclick="resetAccountId(<?php echo e($student->id); ?>)" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                </svg>
                                Reset ID
                            </button>
                            <button onclick="toggleActive(<?php echo e($student->id); ?>, '<?php echo e(addslashes($student->first_name . ' ' . $student->last_name)); ?>', '<?php echo e($student->is_active ? 'active' : 'inactive'); ?>')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 <?php echo e($student->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'); ?> text-white text-sm rounded-lg">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <?php if($student->is_active): ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                    <?php else: ?>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    <?php endif; ?>
                                </svg>
                                <?php echo e($student->is_active ? 'Deactivate' : 'Activate'); ?>

                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Edit Student Information</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="editForm" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Email</label>
                <input type="email" name="email" id="edit_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Course & Year</label>
                <select name="course_year" id="edit_course_year" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">-- Select Course & Year --</option>
                    <?php $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <optgroup label="<?php echo e($course->code); ?> - <?php echo e($course->name); ?>">
                            <option value="<?php echo e($course->code); ?> - 1st Year"><?php echo e($course->code); ?> - 1st Year</option>
                            <option value="<?php echo e($course->code); ?> - 2nd Year"><?php echo e($course->code); ?> - 2nd Year</option>
                            <option value="<?php echo e($course->code); ?> - 3rd Year"><?php echo e($course->code); ?> - 3rd Year</option>
                            <option value="<?php echo e($course->code); ?> - 4th Year"><?php echo e($course->code); ?> - 4th Year</option>
                        </optgroup>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">New Password (optional)</label>
                <input type="password" name="password" id="edit_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Leave blank to keep current">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ BULK UPDATE CONFIRMATION ============
    document.getElementById('bulkUpdateForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fromYear = document.getElementById('fromYear').value;
        const toYear = document.getElementById('toYear').value;
        const selectionType = document.getElementById('selectionType').value;
        
        let message = '';
        let studentCount = 0;
        
        if (selectionType === 'selected') {
            const selectedCount = document.querySelectorAll('#selectedStudentsTableBody .student-checkbox:checked').length;
            if (selectedCount === 0) {
                Swal.fire('Error', 'Please select at least one student.', 'error');
                return;
            }
            message = `Are you sure you want to update ${selectedCount} selected student(s) from ${fromYear} to ${toYear}?`;
        } else if (selectionType === 'by_course') {
            const course = document.querySelector('select[name="course"]').value;
            message = `Are you sure you want to update ALL ${fromYear} students in ${course || 'all courses'} to ${toYear}?`;
        } else {
            message = `⚠️ WARNING: This will update ALL ${fromYear} students in the system to ${toYear}. This action cannot be undone. Are you absolutely sure?`;
        }
        
        Swal.fire({
            title: 'Confirm Bulk Update',
            html: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, update them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
    
    // ============ SELECTED STUDENTS SEARCH & FILTER ============
    const selectedSearchInput = document.getElementById('selectedStudentSearch');
    const studentRows = document.querySelectorAll('#selectedStudentsTableBody .student-row');
    
    if (selectedSearchInput) {
        selectedSearchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            studentRows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const studentId = row.getAttribute('data-student-id') || '';
                if (name.includes(searchTerm) || studentId.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Update selected count
    function updateSelectedCount() {
        const checkboxes = document.querySelectorAll('#selectedStudentsTableBody .student-checkbox:checked');
        const count = checkboxes.length;
        const selectedCountSpan = document.getElementById('selectedCount');
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        
        // Update header checkbox
        const allCheckboxes = document.querySelectorAll('#selectedStudentsTableBody .student-checkbox');
        const headerCheckbox = document.getElementById('headerCheckbox');
        if (headerCheckbox) {
            const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
            headerCheckbox.checked = allChecked;
            headerCheckbox.indeterminate = !allChecked && Array.from(allCheckboxes).some(cb => cb.checked);
        }
    }
    
    // Select All functionality
    const selectAllBtn = document.getElementById('selectAllStudents');
    const clearAllBtn = document.getElementById('clearAllStudents');
    const headerCheckbox = document.getElementById('headerCheckbox');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const visibleRows = document.querySelectorAll('#selectedStudentsTableBody .student-row:not([style*="display: none"])');
            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.student-checkbox');
                if (checkbox) checkbox.checked = true;
            });
            updateSelectedCount();
        });
    }
    
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('#selectedStudentsTableBody .student-checkbox');
            checkboxes.forEach(cb => cb.checked = false);
            updateSelectedCount();
        });
    }
    
    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            const visibleRows = document.querySelectorAll('#selectedStudentsTableBody .student-row:not([style*="display: none"])');
            visibleRows.forEach(row => {
                const checkbox = row.querySelector('.student-checkbox');
                if (checkbox) checkbox.checked = isChecked;
            });
            updateSelectedCount();
        });
    }
    
    // Update count when checkbox changes
    document.querySelectorAll('#selectedStudentsTableBody .student-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    
    // Initial count
    updateSelectedCount();
    
    // ============ SELECTION TYPE TOGGLE ============
    document.getElementById('selectionType')?.addEventListener('change', function() {
        const courseDiv = document.getElementById('courseSelectDiv');
        const selectedDiv = document.getElementById('selectedStudentsDiv');
        
        if (this.value === 'by_course') {
            courseDiv.style.display = 'block';
            selectedDiv.style.display = 'none';
        } else if (this.value === 'selected') {
            courseDiv.style.display = 'none';
            selectedDiv.style.display = 'block';
            if (selectedSearchInput) selectedSearchInput.value = '';
            studentRows.forEach(row => row.style.display = '');
            updateSelectedCount();
        } else {
            courseDiv.style.display = 'none';
            selectedDiv.style.display = 'none';
        }
    });
    
    // ============ SEARCH FUNCTIONALITY ============
    document.getElementById('searchStudent')?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#studentsTableBody tr');
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
                let tbody = document.getElementById('studentsTableBody');
                let tr = document.createElement('tr');
                tr.id = 'noResultRow';
                tr.innerHTML = `<td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <p>No students found</p>
                                </td>`;
                tbody.appendChild(tr);
                noResultRow = tr;
            }
            noResultRow.style.display = '';
        } else if (noResultRow) {
            noResultRow.style.display = 'none';
        }
    });
    
    // ============ EDIT MODAL FUNCTIONS ============
function openEditModal(id) {
    fetch('/support/students/' + id + '/edit')
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_first_name').value = data.first_name;
            document.getElementById('edit_last_name').value = data.last_name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_course_year').value = data.course_year;
            document.getElementById('editForm').action = '/support/students/' + id;
            
            // ✅ UPDATE COURSE DROPDOWN KUNG MAY BAGONG COURSES
            if (data.courses && data.courses.length > 0) {
                const courseSelect = document.getElementById('edit_course_year');
                const currentValue = data.course_year;
                
                // I-clear ang existing options (except ang una)
                while (courseSelect.options.length > 1) {
                    courseSelect.remove(1);
                }
                
                // Idagdag ang mga bagong courses
                data.courses.forEach(course => {
                    // Para sa bawat course, magdagdag ng 4 year levels
                    const years = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
                    years.forEach(year => {
                        const optionValue = course.code + ' - ' + year;
                        const optionText = course.code + ' - ' + year;
                        const option = document.createElement('option');
                        option.value = optionValue;
                        option.textContent = optionText;
                        if (optionValue === currentValue) {
                            option.selected = true;
                        }
                        courseSelect.appendChild(option);
                    });
                });
            }
            
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to load student data', 'error');
        });
}
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }
    
    // ============ RESET FUNCTIONS ============
    function resetPassword(id, name) {
        Swal.fire({
            title: 'Reset Password',
            html: `<div style="text-align: left;"><p>Reset password for <strong>${name}</strong></p><input type="password" id="newPassword" class="swal2-input" placeholder="New Password" value="12345678"></div>`,
            confirmButtonText: 'Reset',
            cancelButtonText: 'Cancel',
            showCancelButton: true,
            preConfirm: () => {
                const password = document.getElementById('newPassword').value;
                if (!password) {
                    Swal.showValidationMessage('Please enter a password');
                    return false;
                }
                return { password: password };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo e(route("support.reset.password")); ?>';
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '<?php echo e(csrf_token()); ?>';
                let studentId = document.createElement('input');
                studentId.type = 'hidden';
                studentId.name = 'student_id';
                studentId.value = id;
                let newPassword = document.createElement('input');
                newPassword.type = 'hidden';
                newPassword.name = 'new_password';
                newPassword.value = result.value.password;
                form.appendChild(csrf);
                form.appendChild(studentId);
                form.appendChild(newPassword);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    function resetAccountId(id) {
        Swal.fire({
            title: 'Reset Account ID?',
            text: 'This will generate a new Account ID for the student. Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, reset it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo e(route("support.reset.accountid")); ?>';
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '<?php echo e(csrf_token()); ?>';
                let studentId = document.createElement('input');
                studentId.type = 'hidden';
                studentId.name = 'student_id';
                studentId.value = id;
                form.appendChild(csrf);
                form.appendChild(studentId);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function toggleActive(id, name, currentStatus) {
        const action = currentStatus === 'active' ? 'deactivate' : 'activate';
        Swal.fire({
            title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} Student?`,
            text: `Are you sure you want to ${action} ${name}'s account?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'deactivate' ? '#d33' : '#3085d6',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${action} it!`,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/support/toggle-active/' + id;
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '<?php echo e(csrf_token()); ?>';
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.support', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/support/students.blade.php ENDPATH**/ ?>