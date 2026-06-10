@extends('layouts.admin')  

@section('title', 'Manage Departments')
@section('header', 'Manage Departments')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    
    <!-- Add Department Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i> Add New Department
    </button>
    
    <!-- Departments Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Description</th>
                    <th class="p-3 text-left">Staff Email</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $dept->id }}</td>
                    <td class="p-3 font-semibold">{{ $dept->name }}</td>
                    <td class="p-3">{{ $dept->description ?? '-' }}</td>
                    <td class="p-3">{{ $dept->staff_email ?? '-' }}</td>
                    <td class="p-3">
                        @if($dept->is_active)
                            <span class="bg-green-500 text-white px-2 py-1 rounded text-sm">Active</span>
                        @else
                            <span class="bg-red-500 text-white px-2 py-1 rounded text-sm">Inactive</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <button onclick="openEditModal({{ $dept->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="confirmDeleteDept({{ $dept->id }}, '{{ addslashes($dept->name) }}')" 
                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <form id="delete-dept-form-{{ $dept->id }}" method="POST" action="{{ route('admin.department.destroy', $dept->id) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">No departments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $departments->links() }}
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="deptModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-96">
        <h3 id="modalTitle" class="text-xl font-bold mb-4">Add Department</h3>
        <form id="deptForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Department Name</label>
                <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" class="w-full border rounded px-3 py-2" rows="2"></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Staff Email</label>
                <input type="email" name="staff_email" id="staff_email" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Email ng department staff (optional)</p>
            </div>
            <div class="mb-3" id="passwordField">
                <label class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2">
                <p class="text-xs text-gray-500">Leave blank to keep current password when editing</p>
            </div>
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Status</label>
                <select name="is_active" id="is_active" class="w-full border rounded px-3 py-2">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDeleteDept(id, name) {
        Swal.fire({
            title: 'Delete Department?',
            text: `Are you sure you want to delete "${name}"? This will also delete all related clearance requests.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-dept-form-${id}`).submit();
            }
        });
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = 'Add Department';
        document.getElementById('deptForm').action = '{{ route("admin.department.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('staff_email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('password').required = true;
        document.getElementById('is_active').value = '1';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('deptModal').classList.remove('hidden');
        document.getElementById('deptModal').classList.add('flex');
    }
    
    function openEditModal(id) {
    console.log('Fetching department ID:', id);
    
    fetch(`/admin/departments/${id}/edit-data`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Department data received:', data);
            
            document.getElementById('modalTitle').innerHTML = 'Edit Department';
            document.getElementById('deptForm').action = `/admin/departments/${id}`;
            document.getElementById('methodField').value = 'PUT';
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description || '';
            document.getElementById('staff_email').value = data.staff_email || '';
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('is_active').value = data.is_active ? '1' : '0';
            document.getElementById('passwordField').style.display = 'block';
            document.getElementById('deptModal').classList.remove('hidden');
            document.getElementById('deptModal').classList.add('flex');
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
    }
</script>
@endsection