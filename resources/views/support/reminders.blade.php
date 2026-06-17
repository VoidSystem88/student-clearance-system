@extends('layouts.support')

@section('title', 'Manage Reminders')
@section('header', 'Manage Reminders')

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
    
    <!-- Add Reminder Button -->
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded mb-4 hover:bg-green-700 transition flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> Add New Reminder
    </button>
    
    <!-- Reminders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">ID</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Title</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Target</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Department</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Dates</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="p-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reminders as $reminder)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3 text-sm">{{ $reminder->id }}</td>
                    <td class="p-3 font-semibold text-gray-800">{{ $reminder->title }}</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium 
                            @if($reminder->target_role == 'staff') bg-blue-100 text-blue-700
                            @elseif($reminder->target_role == 'officer') bg-purple-100 text-purple-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($reminder->target_role) }}
                        </span>
                    </td>
                    <td class="p-3 text-sm">{{ $reminder->department->name ?? 'All' }}</td>
                    <td class="p-3 text-sm">
                        {{ $reminder->start_date ? \Carbon\Carbon::parse($reminder->start_date)->format('M d, Y') : 'Always' }}
                        -
                        {{ $reminder->end_date ? \Carbon\Carbon::parse($reminder->end_date)->format('M d, Y') : 'Always' }}
                    </td>
                    <td class="p-3">
                        @if($reminder->is_active)
                            @php
                                $now = now();
                                $isExpired = $reminder->end_date && $now->gt($reminder->end_date);
                                $isScheduled = $reminder->start_date && $now->lt($reminder->start_date);
                            @endphp
                            @if($isExpired)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Expired</span>
                            @elseif($isScheduled)
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Scheduled</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                            @endif
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="flex gap-1">
                            <button onclick="openEditModal({{ $reminder->id }})" class="bg-yellow-500 text-white px-3 py-1 rounded text-sm hover:bg-yellow-600 transition">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="toggleReminder({{ $reminder->id }}, '{{ addslashes($reminder->title) }}', {{ $reminder->is_active ? 'true' : 'false' }})" 
                                    class="inline-flex items-center gap-1 {{ $reminder->is_active ? 'bg-gray-500 hover:bg-gray-600' : 'bg-green-500 hover:bg-green-600' }} text-white px-3 py-1 rounded text-sm transition">
                                <i class="fas {{ $reminder->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                {{ $reminder->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button onclick="confirmDelete({{ $reminder->id }}, '{{ addslashes($reminder->title) }}')" 
                                    class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                        <form id="delete-form-{{ $reminder->id }}" method="POST" action="{{ route('support.reminder.destroy', $reminder->id) }}" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl mb-2 text-gray-300"></i>
                        <p>No reminders found</p>
                        <button onclick="openAddModal()" class="mt-2 text-blue-600 hover:underline">Create your first reminder</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($reminders, 'links'))
        <div class="mt-4">
            {{ $reminders->links() }}
        </div>
    @endif
</div>

<!-- Add/Edit Reminder Modal -->
<div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">
                <i class="fas fa-plus-circle mr-2 text-green-600"></i> Add Reminder
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="reminderForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Message <span class="text-red-500">*</span></label>
                <textarea name="message" id="message" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Target Role <span class="text-red-500">*</span></label>
                <select name="target_role" id="target_role" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="both">Both Staff & Officers</option>
                    <option value="staff">Department Staff Only</option>
                    <option value="officer">Officers Only</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 text-sm font-medium">Department (Optional)</label>
                <select name="department_id" id="department_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1 text-sm font-medium">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 text-sm font-medium">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4" checked>
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Save</button>
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
    
    function toggleReminder(id, title, isActive) {
        const action = isActive ? 'deactivate' : 'activate';
        Swal.fire({
            title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} Reminder?`,
            text: `Are you sure you want to ${action} "${title}"?`,
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
                form.action = `/support/reminders/${id}/toggle`;
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle mr-2 text-green-600"></i> Add Reminder';
        document.getElementById('reminderForm').action = '{{ route("support.reminder.store") }}';
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
        fetch(`/support/reminders/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-2 text-yellow-600"></i> Edit Reminder';
                document.getElementById('reminderForm').action = `/support/reminders/${id}`;
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
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endsection