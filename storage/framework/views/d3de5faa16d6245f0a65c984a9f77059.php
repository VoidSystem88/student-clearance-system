<?php $__env->startSection('title', 'Manage Courses'); ?>
<?php $__env->startSection('header', 'Manage Courses'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto">
    <?php if(session('success')): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    <?php endif; ?>
    
    <?php if(session('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?></span>
            <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
        </div>
    <?php endif; ?>
    
    <!-- Add Course Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> Add New Course
    </button>
    
    <!-- Courses Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">Code</th>
                    <th class="p-3 text-left">Course Name</th>
                    <th class="p-3 text-left">Department</th>
                    <th class="p-3 text-left">Duration</th>
                    <th class="p-3 text-left">Students</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">
                        <span class="font-mono font-semibold text-blue-600"><?php echo e($course->code); ?></span>
                    </td>
                    <td class="p-3 font-semibold"><?php echo e($course->name); ?></td>
                    <td class="p-3 text-gray-600"><?php echo e($course->department ?? '—'); ?></td>
                    <td class="p-3 text-gray-600"><?php echo e($course->duration ?? '—'); ?></td>
                    <td class="p-3">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <i class="fas fa-users"></i> <?php echo e($course->student_count); ?>

                        </span>
                    </td>
                    <td class="p-3">
                        <?php if($course->is_active): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-times-circle"></i> Inactive
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-1">
                            <button onclick="openEditModal(<?php echo e($course->id); ?>)" 
                                    class="inline-flex items-center gap-1 bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="toggleCourseStatus(<?php echo e($course->id); ?>, '<?php echo e(addslashes($course->code)); ?>', <?php echo e($course->is_active ? 'true' : 'false'); ?>)" 
                                    class="inline-flex items-center gap-1 <?php echo e($course->is_active ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-500 hover:bg-green-600'); ?> text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas <?php echo e($course->is_active ? 'fa-ban' : 'fa-check'); ?>"></i>
                                <?php echo e($course->is_active ? 'Deactivate' : 'Activate'); ?>

                            </button>
                            <button onclick="confirmDeleteCourse(<?php echo e($course->id); ?>, '<?php echo e(addslashes($course->code)); ?>', <?php echo e($course->student_count); ?>)" 
                                    class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                        <form id="delete-course-form-<?php echo e($course->id); ?>" method="POST" action="<?php echo e(route('admin.course.destroy', $course->id)); ?>" class="hidden">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        <i class="fas fa-book-open text-4xl mb-2 text-gray-300"></i>
                        <p>No courses found</p>
                        <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:underline">Add your first course</button>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if(method_exists($courses, 'links')): ?>
        <div class="mt-4">
            <?php echo e($courses->links()); ?>

        </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Course Modal -->
<div id="courseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">
                <i class="fas fa-plus-circle text-green-600 mr-2"></i> Add Course
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="courseForm" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-code text-gray-500 mr-1"></i> Course Code <span class="text-red-500">*</span>
                </label>
                <input type="text" name="code" id="code" 
                       class="w-full border rounded px-3 py-2 uppercase focus:ring-2 focus:ring-blue-500" 
                       placeholder="e.g., BSIT, BSCS, BSHM" required>
                <p class="text-xs text-gray-400 mt-1">Unique code (will be auto-capitalized)</p>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-book text-gray-500 mr-1"></i> Course Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" 
                       class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" 
                       placeholder="e.g., Bachelor of Science in Information Technology" required>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-building text-gray-500 mr-1"></i> Department/College
                </label>
                <select name="department" id="department" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Department --</option>
                    <option value="College of Computer Studies">College of Computer Studies</option>
                    <option value="College of Business and Accountancy">College of Business and Accountancy</option>
                    <option value="College of Education">College of Education</option>
                    <option value="College of Criminology">College of Criminology</option>
                    <option value="College of Hospitality Management">College of Hospitality Management</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-clock text-gray-500 mr-1"></i> Duration
                </label>
                <select name="duration" id="duration" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Duration --</option>
                    <option value="4 Years">4 Years</option>
                    <option value="3 Years">3 Years</option>
                    <option value="2 Years">2 Years</option>
                    <option value="1 Year">1 Year</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-align-left text-gray-500 mr-1"></i> Description
                </label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                          placeholder="Course description (optional)"></textarea>
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t mt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Course
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle text-green-600 mr-2"></i> Add New Course';
        document.getElementById('courseForm').action = '<?php echo e(route("admin.course.store")); ?>';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('code').value = '';
        document.getElementById('name').value = '';
        document.getElementById('department').value = '';
        document.getElementById('duration').value = '';
        document.getElementById('description').value = '';
        document.getElementById('courseModal').classList.remove('hidden');
        document.getElementById('courseModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id) {
        fetch(`/admin/courses/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit text-yellow-600 mr-2"></i> Edit Course';
                document.getElementById('courseForm').action = `/admin/courses/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('code').value = data.code;
                document.getElementById('name').value = data.name;
                document.getElementById('department').value = data.department || '';
                document.getElementById('duration').value = data.duration || '';
                document.getElementById('description').value = data.description || '';
                document.getElementById('courseModal').classList.remove('hidden');
                document.getElementById('courseModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load course data', 'error');
            });
    }
    
    function closeModal() {
        document.getElementById('courseModal').classList.add('hidden');
        document.getElementById('courseModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function confirmDeleteCourse(id, code, studentCount) {
        let message = `Are you sure you want to delete course "${code}"?`;
        if (studentCount > 0) {
            message = `Cannot delete "${code}" because ${studentCount} student(s) are enrolled in this course.`;
            Swal.fire({
                title: 'Cannot Delete',
                text: message,
                icon: 'warning',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        Swal.fire({
            title: 'Delete Course?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-course-form-${id}`).submit();
            }
        });
    }
    
    function toggleCourseStatus(id, code, isActive) {
        const action = isActive ? 'deactivate' : 'activate';
        Swal.fire({
            title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} Course?`,
            text: `Are you sure you want to ${action} "${code}"?`,
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
                form.action = `/admin/courses/${id}/toggle`;
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
    
    // Close modal when clicking outside
    document.getElementById('courseModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/admin/courses.blade.php ENDPATH**/ ?>