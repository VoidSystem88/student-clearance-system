@extends('layouts.admin')

@section('title', 'Manage Students')
@section('header', 'Manage Students')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
        </div>
    @endif
    
    <!-- Search and Filter Bar -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" placeholder="Search by name, student ID, or email..." 
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <select id="courseFilter" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Courses</option>
                    <option value="BSIT">BSIT</option>
                    <option value="BSCS">BSCS</option>
                    <option value="BSIS">BSIS</option>
                    <option value="BSBA-FM">BSBA Financial Management</option>
                    <option value="BSHM">BS Hospitality Management</option>
                    <option value="BEEd">BEEd</option>
                    <option value="BSEd">BSEd</option>
                    <option value="BSCrim">BS Criminology</option>
                </select>
                <button id="resetFilterBtn" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>
    
    <!-- Add Student Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition">
        <i class="fas fa-user-plus mr-2"></i> Add New Student
    </button>
    
    <!-- Students Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Student ID</th>
                    <th class="p-3 text-left">Account ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Course</th>
                    <th class="p-3 text-left">Year</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="studentsTableBody">
                @forelse($students as $student)
                <tr class="border-b hover:bg-gray-50 student-row" data-name="{{ strtolower($student->first_name . ' ' . $student->last_name) }}" 
                    data-student-id="{{ $student->student_id }}" data-email="{{ strtolower($student->email) }}" data-course="{{ $student->course }}">
                    <td class="p-3">{{ $student->id }}</td>
                    <td class="p-3 font-mono text-sm">{{ $student->student_id }}</td>
                    <td class="p-3 font-mono text-sm">{{ $student->account_id ?? 'N/A' }}</td>
                    <td class="p-3 font-semibold">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td class="p-3">{{ $student->email }}</td>
                    <td class="p-3">{{ $student->course }}</td>
                    <td class="p-3">{{ $student->year_level ?? 'N/A' }}</td>
                    <td class="p-3">
                        @if($student->is_active)
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">Active</span>
                        @else
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-xs">Inactive</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2 flex-wrap">
                            <button onclick="openEditModal({{ $student->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="confirmArchive({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                    class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                <i class="fas fa-archive"></i> Archive
                            </button>
                            <button onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                    class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition">
                                <i class="fas fa-trash-alt"></i> Delete
                            </button>
                        </div>
                        <form id="archive-form-{{ $student->id }}" method="POST" action="{{ route('admin.student.archive', $student->id) }}" class="hidden">
                            @csrf
                        </form>
                        <form id="delete-form-{{ $student->id }}" method="POST" action="{{ route('admin.student.destroy', $student->id) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="p-4 text-center text-gray-500">
                        <i class="fas fa-users text-3xl mb-2 text-gray-300"></i>
                        <p>No students found</p>
                        <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:underline">Add your first student</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($students, 'links'))
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    @endif
</div>

<!-- Add/Edit Modal -->
<div id="studentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Add Student</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="studentForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Student ID</label>
                <input type="text" name="student_id" id="student_id" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="text-xs text-gray-500 mt-1">Format: YYYY-XXXXX (e.g., 2023-00123)</p>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1">Course</label>
                    <select name="course" id="course" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIS">BSIS</option>
                        <option value="BSBA-FM">BSBA Financial Management</option>
                        <option value="BSHM">BS Hospitality Management</option>
                        <option value="BEEd">BEEd</option>
                        <option value="BSEd">BSEd</option>
                        <option value="BSCrim">BS Criminology</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Year Level</label>
                    <select name="year_level" id="year_level" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>
            </div>
            <div class="mb-3" id="passwordField">
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500">Leave blank to keep current password when editing</p>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Search and Filter Functionality
    function filterStudents() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const courseFilter = document.getElementById('courseFilter').value;
        const rows = document.querySelectorAll('.student-row');
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const studentId = row.getAttribute('data-student-id') || '';
            const email = row.getAttribute('data-email') || '';
            const course = row.getAttribute('data-course') || '';
            
            const matchesSearch = name.includes(searchTerm) || 
                                  studentId.includes(searchTerm) || 
                                  email.includes(searchTerm);
            const matchesCourse = courseFilter === '' || course === courseFilter;
            
            if (matchesSearch && matchesCourse) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterStudents);
    document.getElementById('courseFilter')?.addEventListener('change', filterStudents);
    document.getElementById('resetFilterBtn')?.addEventListener('click', function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('courseFilter').value = '';
        filterStudents();
    });
    
    // ============ ARCHIVE FUNCTION ============
    function confirmArchive(id, name) {
        Swal.fire({
            title: 'Archive Student?',
            html: `Are you sure you want to archive <strong>${name}</strong>?<br><br>This student can be restored later.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, archive',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`archive-form-${id}`).submit();
            }
        });
    }
    
    // ============ PERMANENT DELETE FUNCTION ============
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'PERMANENTLY DELETE?',
            html: `Are you sure you want to <strong class="text-red-600">PERMANENTLY DELETE</strong> ${name}?<br><br><span class="text-red-600">This action CANNOT be undone!</span>`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, permanently delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
    
    // ============ MODAL FUNCTIONS ============
    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = 'Add Student';
        document.getElementById('studentForm').action = '{{ route("admin.student.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('student_id').value = '';
        document.getElementById('first_name').value = '';
        document.getElementById('last_name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('course').value = 'BSIT';
        document.getElementById('year_level').value = '1st Year';
        document.getElementById('password').value = '';
        document.getElementById('password').required = true;
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('studentModal').classList.remove('hidden');
        document.getElementById('studentModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id) {
        fetch(`/admin/students/${id}/edit-data`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = 'Edit Student';
                document.getElementById('studentForm').action = `/admin/students/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('student_id').value = data.student_id;
                document.getElementById('first_name').value = data.first_name;
                document.getElementById('last_name').value = data.last_name;
                document.getElementById('email').value = data.email;
                document.getElementById('course').value = data.course;
                document.getElementById('year_level').value = data.year_level || '1st Year';
                document.getElementById('password').value = '';
                document.getElementById('password').required = false;
                document.getElementById('passwordField').style.display = 'block';
                document.getElementById('studentModal').classList.remove('hidden');
                document.getElementById('studentModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load student data', 'error');
            });
    }
    
    function closeModal() {
        document.getElementById('studentModal').classList.add('hidden');
        document.getElementById('studentModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('studentModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection