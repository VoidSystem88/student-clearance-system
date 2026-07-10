@extends('layouts.admin')

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
    <button onclick="openAddModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg mb-4 hover:bg-green-700 transition flex items-center gap-2">
        <i class="fas fa-plus-circle"></i> Add New Reminder
    </button>
    
    <!-- ============ TABS ============ -->
    <div class="bg-white rounded-xl shadow-sm">
        <!-- Tab Buttons -->
        <div class="border-b border-gray-200 px-4 pt-3 flex flex-wrap gap-1 overflow-x-auto">
            @php
                $activeReminders = $reminders->where('is_active', true);
                $inactiveReminders = $reminders->where('is_active', false);
            @endphp
            <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 transition whitespace-nowrap active" 
                    data-tab="active-tab"
                    style="color: #3b82f6; border-bottom-color: #3b82f6;">
                <i class="fas fa-check-circle text-green-500 mr-1"></i> Active
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $activeReminders->count() }}</span>
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap"
                    data-tab="inactive-tab">
                <i class="fas fa-times-circle text-red-500 mr-1"></i> Inactive
                <span class="bg-red-100 text-red-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $inactiveReminders->count() }}</span>
            </button>
            <button class="tab-btn px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition whitespace-nowrap"
                    data-tab="all-tab">
                <i class="fas fa-list text-blue-500 mr-1"></i> All
                <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full text-xs ml-1">{{ $reminders->count() }}</span>
            </button>
        </div>

        <!-- Tab Contents -->
        <div class="p-4">
            {{-- ACTIVE TAB --}}
            <div id="active-tab" class="tab-content">
                @if($activeReminders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Message</th>
                                <th class="px-4 py-3">Target</th>
                                <th class="px-4 py-3">Department</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($activeReminders as $reminder)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $reminder->id }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $reminder->title }}</td>
                                <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">{{ Str::limit($reminder->message, 60) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($reminder->target_role == 'staff') bg-blue-100 text-blue-700
                                        @elseif($reminder->target_role == 'officer') bg-purple-100 text-purple-700
                                        @elseif($reminder->target_role == 'student') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($reminder->target_role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $reminder->department->name ?? 'All' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $reminder->start_date ? $reminder->start_date->format('M d, Y') : 'Now' }}
                                    -
                                    {{ $reminder->end_date ? $reminder->end_date->format('M d, Y') : 'No end' }}
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="toggleActive({{ $reminder->id }})" class="cursor-pointer">
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <button onclick="openEditModal({{ $reminder->id }})" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-yellow-600 transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $reminder->id }}, '{{ addslashes($reminder->title) }}')" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-red-700 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $reminder->id }}" method="POST" action="{{ route('admin.reminder.destroy', $reminder->id) }}" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                    <p>No active reminders</p>
                </div>
                @endif
            </div>

            {{-- INACTIVE TAB --}}
            <div id="inactive-tab" class="tab-content hidden">
                @if($inactiveReminders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Message</th>
                                <th class="px-4 py-3">Target</th>
                                <th class="px-4 py-3">Department</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($inactiveReminders as $reminder)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $reminder->id }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $reminder->title }}</td>
                                <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">{{ Str::limit($reminder->message, 60) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($reminder->target_role == 'staff') bg-blue-100 text-blue-700
                                        @elseif($reminder->target_role == 'officer') bg-purple-100 text-purple-700
                                        @elseif($reminder->target_role == 'student') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($reminder->target_role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $reminder->department->name ?? 'All' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $reminder->start_date ? $reminder->start_date->format('M d, Y') : 'Now' }}
                                    -
                                    {{ $reminder->end_date ? $reminder->end_date->format('M d, Y') : 'No end' }}
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="toggleActive({{ $reminder->id }})" class="cursor-pointer">
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <button onclick="openEditModal({{ $reminder->id }})" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-yellow-600 transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $reminder->id }}, '{{ addslashes($reminder->title) }}')" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-red-700 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $reminder->id }}" method="POST" action="{{ route('admin.reminder.destroy', $reminder->id) }}" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                    <p>No inactive reminders</p>
                </div>
                @endif
            </div>

            {{-- ALL TAB --}}
            <div id="all-tab" class="tab-content hidden">
                @if($reminders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Message</th>
                                <th class="px-4 py-3">Target</th>
                                <th class="px-4 py-3">Department</th>
                                <th class="px-4 py-3">Duration</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($reminders as $reminder)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $reminder->id }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-800">{{ $reminder->title }}</td>
                                <td class="px-4 py-3 text-gray-600 max-w-[200px] truncate">{{ Str::limit($reminder->message, 60) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($reminder->target_role == 'staff') bg-blue-100 text-blue-700
                                        @elseif($reminder->target_role == 'officer') bg-purple-100 text-purple-700
                                        @elseif($reminder->target_role == 'student') bg-green-100 text-green-700
                                        @else bg-gray-100 text-gray-700 @endif">
                                        {{ ucfirst($reminder->target_role) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $reminder->department->name ?? 'All' }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">
                                    {{ $reminder->start_date ? $reminder->start_date->format('M d, Y') : 'Now' }}
                                    -
                                    {{ $reminder->end_date ? $reminder->end_date->format('M d, Y') : 'No end' }}
                                </td>
                                <td class="px-4 py-3">
                                    <button onclick="toggleActive({{ $reminder->id }})" class="cursor-pointer">
                                        @if($reminder->is_active)
                                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-check-circle mr-1"></i> Active
                                        </span>
                                        @else
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-medium">
                                            <i class="fas fa-times-circle mr-1"></i> Inactive
                                        </span>
                                        @endif
                                    </button>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-1">
                                        <button onclick="openEditModal({{ $reminder->id }})" class="bg-yellow-500 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-yellow-600 transition">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $reminder->id }}, '{{ addslashes($reminder->title) }}')" class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-red-700 transition">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $reminder->id }}" method="POST" action="{{ route('admin.reminder.destroy', $reminder->id) }}" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                    <p>No reminders found</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Pagination -->
    @if(method_exists($reminders, 'links'))
        <div class="mt-4">
            {{ $reminders->links() }}
        </div>
    @endif
</div>

<!-- Add/Edit Modal -->
<div id="reminderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="modalTitle" class="text-xl font-bold">
                <i class="fas fa-bell mr-2 text-blue-600"></i> Add Reminder
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
        </div>
        <form id="reminderForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="methodField" value="POST">
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 font-medium">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
            </div>
            
            <div class="mb-3">
                <label class="block text-gray-700 mb-1 font-medium">Message <span class="text-red-500">*</span></label>
                <textarea name="message" id="message" rows="4" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Target Role</label>
                    <select name="target_role" id="target_role" class="w-full border rounded-lg px-3 py-2">
                        <option value="both">Both Staff & Officers</option>
                        <option value="staff">Department Staff Only</option>
                        <option value="officer">Officers Only</option>
                        <option value="student">Students Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Department</label>
                    <select name="department_id" id="department_id" class="w-full border rounded-lg px-3 py-2">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 mb-1 font-medium">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1 font-medium">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4 text-blue-600 rounded mr-2" checked>
                    <span class="text-gray-700 font-medium">Active</span>
                </label>
            </div>
            
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.style.color = '#6b7280';
                b.style.borderBottomColor = 'transparent';
            });
            this.style.color = '#3b82f6';
            this.style.borderBottomColor = '#3b82f6';
            
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(this.dataset.tab)?.classList.remove('hidden');
        });
    });

    function confirmDelete(id, title) {
        Swal.fire({
            title: 'Delete Reminder?',
            text: `Are you sure you want to delete "${title}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then(r => { if (r.isConfirmed) document.getElementById(`delete-form-${id}`).submit(); });
    }

    function toggleActive(id) {
        fetch(`/admin/reminders/${id}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json'
            }
        }).then(r => r.json()).then(d => { if (d.success) location.reload(); });
    }

    function openAddModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle mr-2 text-green-600"></i> Add Reminder';
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
            .then(r => r.json())
            .then(data => {
                document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-2 text-yellow-600"></i> Edit Reminder';
                document.getElementById('reminderForm').action = `/admin/reminders/${id}`;
                document.getElementById('methodField').value = 'PUT';
                document.getElementById('title').value = data.title || '';
                document.getElementById('message').value = data.message || '';
                document.getElementById('target_role').value = data.target_role || 'both';
                document.getElementById('department_id').value = data.department_id || '';
                document.getElementById('start_date').value = data.start_date || '';
                document.getElementById('end_date').value = data.end_date || '';
                document.getElementById('is_active').checked = data.is_active == 1;
                document.getElementById('reminderModal').classList.remove('hidden');
                document.getElementById('reminderModal').classList.add('flex');
                document.body.style.overflow = 'hidden';
            })
            .catch(() => Swal.fire('Error', 'Failed to load data', 'error'));
    }

    function closeModal() {
        document.getElementById('reminderModal').classList.add('hidden');
        document.getElementById('reminderModal').classList.remove('flex');
        document.body.style.overflow = '';
    }

    document.getElementById('reminderModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>
@endsection