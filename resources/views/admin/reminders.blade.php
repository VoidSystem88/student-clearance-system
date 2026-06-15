@extends('layouts.admin')

@section('title', 'Manage Reminders')
@section('header', 'Manage Reminders')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Add Reminder Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700">
        <i class="fas fa-plus mr-2"></i> Add Reminder
    </button>
    
    <!-- Reminders Table -->
    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-200">
                <tr>
                    <th class="p-3 text-left">ID</th>
                    <th class="p-3 text-left">Title</th>
                    <th class="p-3 text-left">Target</th>
                    <th class="p-3 text-left">Department</th>
                    <th class="p-3 text-left">Dates</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reminders as $reminder)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $reminder->id }}</td>
                    <td class="p-3 font-semibold">{{ $reminder->title }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            @if($reminder->target_role == 'staff') bg-blue-100 text-blue-700
                            @elseif($reminder->target_role == 'officer') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ $reminder->target_text }}
                        </span>
                    </td>
                    <td class="p-3">{{ $reminder->department->name ?? 'All' }}</td>
                    <td class="p-3 text-sm">
                        {{ $reminder->start_date ? $reminder->start_date->format('M d, Y') : 'Always' }}
                        -
                        {{ $reminder->end_date ? $reminder->end_date->format('M d, Y') : 'Always' }}
                    </td>
                    <td class="p-3">{!! $reminder->status_badge !!}</td>
                    <td class="p-3">
                        <button onclick="openEditModal({{ $reminder->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="confirmDelete({{ $reminder->id }}, '{{ addslashes($reminder->title) }}')" 
                                class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                        <form id="delete-form-{{ $reminder->id }}" method="POST" action="{{ route('admin.reminder.destroy', $reminder->id) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">No reminders found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-4">
        {{ $reminders->links() }}
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">Add Reminder</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="reminderForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Title</label>
                <input type="text" name="title" id="title" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Message</label>
                <textarea name="message" id="message" rows="3" class="w-full border rounded px-3 py-2" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Target Role</label>
                <select name="target_role" id="target_role" class="w-full border rounded px-3 py-2">
                    <option value="both">Both Staff & Officers</option>
                    <option value="staff">Department Staff Only</option>
                    <option value="officer">Officers Only</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1">Department (Optional)</label>
                <select name="department_id" id="department_id" class="w-full border rounded px-3 py-2">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="w-full border rounded px-3 py-2">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="mr-2" checked>
                    <span>Active</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Delete Reminder?',
            text: `Are you sure you want to delete "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = 'Add Reminder';
        document.getElementById('reminderForm').action = '{{ route("admin.reminder.store") }}';
        document.getElementById('methodField').value = 'POST';
        document.getElementById('title').value = '';
        document.getElementById('message').value = '';
        document.getElementById('target_role').value = 'both';
        document.getElementById('department_id').value = '';
        document.getElementById('start_date').value = '';
        document.getElementById('end_date').value = '';
        document.getElementById('is_active').checked = true;
        document.getElementById('reminderModal').classList.remove('hidden');
        document.getElementById('reminderModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function openEditModal(id) {
        fetch(`/admin/reminders/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = 'Edit Reminder';
                document.getElementById('reminderForm').action = `/admin/reminders/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('title').value = data.title;
                document.getElementById('message').value = data.message;
                document.getElementById('target_role').value = data.target_role;
                document.getElementById('department_id').value = data.department_id || '';
                document.getElementById('start_date').value = data.start_date || '';
                document.getElementById('end_date').value = data.end_date || '';
                document.getElementById('is_active').checked = data.is_active == 1;
                document.getElementById('reminderModal').classList.remove('hidden');
                document.getElementById('reminderModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load reminder data', 'error');
            });
    }
    
    function closeModal() {
        document.getElementById('reminderModal').classList.add('hidden');
        document.getElementById('reminderModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // Close modal when clicking outside
    document.getElementById('reminderModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection