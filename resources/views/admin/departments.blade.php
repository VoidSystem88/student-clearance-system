@extends('layouts.admin')

@section('title', 'Manage Departments')
@section('header', 'Manage Departments')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
        </div>
    @endif
    
    <!-- Add Department Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> Add New Department
    </button>
    
    <!-- Departments Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Department</th>
                    <th class="p-3 text-left">Handler</th>
                    <th class="p-3 text-left">Staff Email</th>
                    <th class="p-3 text-left">Assigned Year Levels</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $dept->id }}</td>
                    
                    <!-- Department Column -->
                    <td class="p-3">
                        <div class="font-semibold text-gray-800">{{ $dept->name }}</div>
                    </td>
                    
                    <!-- Handler Column -->
                    <td class="p-3">
                        @if($dept->handler_name)
                            <div class="text-sm font-medium text-gray-800">{{ $dept->handler_name }}</div>
                        @else
                            <span class="text-gray-400 text-sm">—</span>
                        @endif
                    </td>
                    
                    <!-- Staff Email Column -->
                    <td class="p-3 text-gray-600">{{ $dept->staff_email ?? '-' }}</td>
                    
                    <!-- Assigned Year Levels Column -->
                    <td class="p-3">
                        @php
                            $assignedYears = $dept->getAssignedYearLevels();
                        @endphp
                        @if(count($assignedYears) > 0)
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($assignedYears as $year)
                                    @php
                                        $yearIcon = match($year) {
                                            '1st Year' => 'fa-graduation-cap',
                                            '2nd Year' => 'fa-book-open',
                                            '3rd Year' => 'fa-chalkboard-user',
                                            '4th Year' => 'fa-user-graduate',
                                            default => 'fa-calendar-alt'
                                        };
                                        $yearColor = match($year) {
                                            '1st Year' => 'bg-blue-100 text-blue-800',
                                            '2nd Year' => 'bg-green-100 text-green-800',
                                            '3rd Year' => 'bg-yellow-100 text-yellow-800',
                                            '4th Year' => 'bg-purple-100 text-purple-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium {{ $yearColor }}">
                                        <i class="fas {{ $yearIcon }} text-xs"></i>
                                        {{ $year }}
                                    </span>
                                @endforeach
                            </div>
                            <button onclick="openYearModal({{ $dept->id }}, '{{ addslashes($dept->name) }}', {{ json_encode($assignedYears) }})" 
                                    class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 transition">
                                <i class="fas fa-edit"></i> Edit Years
                            </button>
                        @else
                            <span class="text-gray-400 text-sm">No years assigned</span>
                            <button onclick="openYearModal({{ $dept->id }}, '{{ addslashes($dept->name) }}', [])" 
                                    class="inline-flex items-center gap-1 text-xs text-blue-600 ml-2 hover:text-blue-800 transition">
                                <i class="fas fa-plus-circle"></i> Assign Years
                            </button>
                        @endif
                    </td>
                    
                    <!-- Status Column -->
                    <td class="p-3">
                        @if($dept->is_active)
                            <span class="inline-flex items-center gap-1 bg-green-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-check-circle text-xs"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 bg-red-500 text-white px-2 py-1 rounded text-xs">
                                <i class="fas fa-times-circle text-xs"></i> Inactive
                            </span>
                        @endif
                    </td>
                    
                    <!-- Actions Column -->
                    <td class="p-3">
                        <div class="flex gap-1">
                            <button onclick="openEditModal({{ $dept->id }})" class="inline-flex items-center gap-1 bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="confirmDeleteDept({{ $dept->id }}, '{{ addslashes($dept->name) }}')" 
                                    class="inline-flex items-center gap-1 bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                        <form id="delete-dept-form-{{ $dept->id }}" method="POST" action="{{ route('admin.department.destroy', $dept->id) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        <i class="fas fa-building text-4xl mb-2 text-gray-300"></i>
                        <p>No departments found</p>
                        <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:underline">Create your first department</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($departments, 'links'))
        <div class="mt-4">
            {{ $departments->links() }}
        </div>
    @endif
</div>

<!-- Add/Edit Department Modal -->
<div id="deptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">
                <i class="fas fa-building mr-2 text-blue-600"></i> Add Department
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="deptForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <!-- Department Name -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-tag text-gray-500 mr-1"></i> Department Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <!-- Handler Name -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-user text-gray-500 mr-1"></i> Handler Name
                </label>
                <input type="text" name="handler_name" id="handler_name" 
                       class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="e.g., James Raid">
                <p class="text-xs text-gray-500 mt-1">Person in charge of this department</p>
            </div>
            
            <!-- Staff Email -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-envelope text-gray-500 mr-1"></i> Staff Email
                </label>
                <input type="email" name="staff_email" id="staff_email" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Email ng department staff (auto-create account)</p>
            </div>
            
            <!-- Password -->
            <div class="mb-3" id="passwordField">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-lock text-gray-500 mr-1"></i> Password
                </label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500">Leave blank to keep current password when editing</p>
            </div>
            
            <!-- Status -->
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">
                    <i class="fas fa-toggle-on text-gray-500 mr-1"></i> Status
                </label>
                <select name="is_active" id="is_active" class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            
            <div class="flex justify-end gap-2 pt-2 border-t mt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Department
                </button>
            </div>
        </form>
    </div>
</div>

<!-- YEAR ASSIGNMENT MODAL -->
<div id="yearModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="yearModalTitle" class="text-xl font-bold">
                <i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Assign Year Levels
            </h3>
            <button onclick="closeYearModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="yearForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="department_id" id="year_dept_id">
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-3 font-medium">
                    <i class="fas fa-users text-gray-500 mr-1"></i> Select which year levels need to clear this department:
                </label>
                <div class="space-y-2 border rounded-lg p-3 bg-gray-50">
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition">
                        <input type="checkbox" name="years[]" value="1st Year" class="year-checkbox w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <i class="fas fa-graduation-cap text-blue-500 w-5"></i>
                        <div class="flex-1">
                            <span class="font-medium text-gray-800">1st Year</span>
                            <p class="text-xs text-gray-500">Freshmen students</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition">
                        <input type="checkbox" name="years[]" value="2nd Year" class="year-checkbox w-4 h-4 text-green-600 rounded focus:ring-green-500">
                        <i class="fas fa-book-open text-green-500 w-5"></i>
                        <div class="flex-1">
                            <span class="font-medium text-gray-800">2nd Year</span>
                            <p class="text-xs text-gray-500">Sophomore students</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition">
                        <input type="checkbox" name="years[]" value="3rd Year" class="year-checkbox w-4 h-4 text-yellow-600 rounded focus:ring-yellow-500">
                        <i class="fas fa-chalkboard-user text-yellow-500 w-5"></i>
                        <div class="flex-1">
                            <span class="font-medium text-gray-800">3rd Year</span>
                            <p class="text-xs text-gray-500">Junior students</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-lg transition">
                        <input type="checkbox" name="years[]" value="4th Year" class="year-checkbox w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                        <i class="fas fa-user-graduate text-purple-500 w-5"></i>
                        <div class="flex-1">
                            <span class="font-medium text-gray-800">4th Year</span>
                            <p class="text-xs text-gray-500">Senior students</p>
                        </div>
                    </label>
                </div>
                <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                    <span class="text-xs text-blue-700"><strong>Note:</strong> Only students from selected year levels will see this department in their clearance.</span>
                </div>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeYearModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition flex items-center gap-1">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition flex items-center gap-1">
                    <i class="fas fa-save"></i> Save Assignment
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteDept(id, name) {
    Swal.fire({
        title: 'PERMANENTLY DELETE Department?',
        html: `Are you sure you want to permanently delete <strong class="text-red-600">"${name}"</strong>?<br><br>
               <span class="text-sm text-red-500">⚠️ This will also delete:</span><br>
               • All clearance requests for this department<br>
               • All year assignments<br>
               • All requirements for this department<br><br>
               <strong>This action CANNOT be undone!</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, permanently delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-dept-form-${id}`).submit();
        }
    });
}

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-building mr-2 text-blue-600"></i> Add Department';
        document.getElementById('deptForm').action = '{{ route("admin.department.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('name').value = '';
        document.getElementById('handler_name').value = '';
        document.getElementById('staff_email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password').required = true;
        document.getElementById('is_active').value = '1';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('deptModal').classList.remove('hidden');
        document.getElementById('deptModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id) {
    console.log('Fetching department ID:', id);
    
    fetch(`/admin/departments/${id}/edit-data`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Department data received:', data);
        
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-2 text-yellow-600"></i> Edit Department';
        document.getElementById('deptForm').action = `/admin/departments/${id}`;
        document.getElementById('methodField').value = 'PUT';
        document.getElementById('name').value = data.name || '';
        // ALISIN ANG DESCRIPTION - wala ito sa modal
        // document.getElementById('description').value = data.description || '';
        document.getElementById('handler_name').value = data.handler_name || '';
        document.getElementById('staff_email').value = data.staff_email || '';
        document.getElementById('password').value = '';
        document.getElementById('password').required = false;
        document.getElementById('is_active').value = data.is_active ? '1' : '0';
        document.getElementById('passwordField').style.display = 'block';
        
        document.getElementById('deptModal').classList.remove('hidden');
        document.getElementById('deptModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    })
    .catch(error => {
        console.error('Error details:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load department data: ' + error.message,
            icon: 'error'
        });
    });
}
    
    function closeModal() {
        document.getElementById('deptModal').classList.add('hidden');
        document.getElementById('deptModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // ============ YEAR ASSIGNMENT FUNCTIONS ============
    
    function openYearModal(deptId, deptName, assignedYears) {
        document.getElementById('yearModalTitle').innerHTML = `<i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Assign Year Levels - ${deptName}`;
        document.getElementById('year_dept_id').value = deptId;
        document.getElementById('yearForm').action = `/admin/departments/${deptId}/assign-years`;
        
        // Reset all checkboxes first
        document.querySelectorAll('.year-checkbox').forEach(cb => {
            cb.checked = false;
        });
        
        // Check the ones that are assigned
        assignedYears.forEach(year => {
            document.querySelectorAll('.year-checkbox').forEach(cb => {
                if (cb.value === year) {
                    cb.checked = true;
                }
            });
        });
        
        document.getElementById('yearModal').classList.remove('hidden');
        document.getElementById('yearModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeYearModal() {
        document.getElementById('yearModal').classList.add('hidden');
        document.getElementById('yearModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    // Close modals when clicking outside
    document.getElementById('deptModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    
    document.getElementById('yearModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeYearModal();
    });

    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeYearModal();
        }
    });
</script>
@endsection