@extends('layouts.support')

@section('title', 'Manage Students')
@section('header', 'Manage Students')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-users text-blue-600 mr-2"></i> Student List
            </h3>
            <p class="text-sm text-gray-500">Reset passwords, reset Account IDs, or manage account status</p>
        </div>
        
        <!-- Search Box -->
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" id="searchStudent" placeholder="Search by name, student ID, or email..." 
                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
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
                @foreach($students ?? [] as $student)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ $student->student_id }}</td>
                    <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ $student->account_id }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $student->course_year ?? $student->course . ' - ' . $student->year_level }}</td>
                    <td class="px-5 py-3">
                        @if($student->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="fas fa-circle text-xs"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                <i class="fas fa-circle text-xs"></i> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap gap-2">
                            <button onclick="openEditModal({{ $student->id }})" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="resetPassword({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                <i class="fas fa-key"></i> Reset PW
                            </button>
                            <button onclick="resetAccountId({{ $student->id }})" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                                <i class="fas fa-id-card"></i> Reset ID
                            </button>
                            <button onclick="toggleActive({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', '{{ $student->is_active ? 'active' : 'inactive' }}')" 
                                    class="inline-flex items-center gap-1 px-3 py-1.5 {{ $student->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm rounded-lg">
                                <i class="fas {{ $student->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                {{ $student->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Student Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Edit Student Information</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
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
                    <optgroup label="College of Computer Studies">
                        <option value="BSIT-1st Year">BSIT - 1st Year</option>
                        <option value="BSIT-2nd Year">BSIT - 2nd Year</option>
                        <option value="BSIT-3rd Year">BSIT - 3rd Year</option>
                        <option value="BSIT-4th Year">BSIT - 4th Year</option>
                        <option value="BSCS-1st Year">BSCS - 1st Year</option>
                        <option value="BSCS-2nd Year">BSCS - 2nd Year</option>
                        <option value="BSCS-3rd Year">BSCS - 3rd Year</option>
                        <option value="BSCS-4th Year">BSCS - 4th Year</option>
                        <option value="BSIS-1st Year">BSIS - 1st Year</option>
                        <option value="BSIS-2nd Year">BSIS - 2nd Year</option>
                        <option value="BSIS-3rd Year">BSIS - 3rd Year</option>
                        <option value="BSIS-4th Year">BSIS - 4th Year</option>
                    </optgroup>
                    <optgroup label="College of Business and Accountancy">
                        <option value="BSBA-FM-1st Year">BSBA Financial Management - 1st Year</option>
                        <option value="BSBA-FM-2nd Year">BSBA Financial Management - 2nd Year</option>
                        <option value="BSBA-FM-3rd Year">BSBA Financial Management - 3rd Year</option>
                        <option value="BSBA-FM-4th Year">BSBA Financial Management - 4th Year</option>
                        <option value="BSHM-1st Year">BS Hospitality Management - 1st Year</option>
                        <option value="BSHM-2nd Year">BS Hospitality Management - 2nd Year</option>
                        <option value="BSHM-3rd Year">BS Hospitality Management - 3rd Year</option>
                        <option value="BSHM-4th Year">BS Hospitality Management - 4th Year</option>
                    </optgroup>
                    <optgroup label="College of Education">
                        <option value="BEEd-1st Year">BEEd - 1st Year</option>
                        <option value="BEEd-2nd Year">BEEd - 2nd Year</option>
                        <option value="BEEd-3rd Year">BEEd - 3rd Year</option>
                        <option value="BEEd-4th Year">BEEd - 4th Year</option>
                        <option value="BSEd-English-1st Year">BSEd English - 1st Year</option>
                        <option value="BSEd-English-2nd Year">BSEd English - 2nd Year</option>
                        <option value="BSEd-English-3rd Year">BSEd English - 3rd Year</option>
                        <option value="BSEd-English-4th Year">BSEd English - 4th Year</option>
                    </optgroup>
                    <optgroup label="College of Criminology">
                        <option value="BSCrim-1st Year">BS Criminology - 1st Year</option>
                        <option value="BSCrim-2nd Year">BS Criminology - 2nd Year</option>
                        <option value="BSCrim-3rd Year">BS Criminology - 3rd Year</option>
                        <option value="BSCrim-4th Year">BS Criminology - 4th Year</option>
                    </optgroup>
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
<!-- Tracker -->
<img src="{{ url('/track.gif') }}" width="1" height="1" style="display: none;">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Search functionality for Students
    document.getElementById('searchStudent')?.addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        let rows = document.querySelectorAll('#studentsTableBody tr');
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
                let tbody = document.getElementById('studentsTableBody');
                let tr = document.createElement('tr');
                tr.id = 'noResultRow';
                tr.innerHTML = `<td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                    <p>No students found for "<span id="searchTermDisplay"></span>"</p>
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
    
    function openEditModal(id) {
        fetch('/support/students/' + id + '/edit')
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_first_name').value = data.first_name;
                document.getElementById('edit_last_name').value = data.last_name;
                document.getElementById('edit_email').value = data.email;
                document.getElementById('edit_course_year').value = data.course_year;
                document.getElementById('editForm').action = '/support/students/' + id;
                document.getElementById('editModal').classList.remove('hidden');
                document.getElementById('editModal').classList.add('flex');
            });
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }
    
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
                form.action = '{{ route("support.reset.password") }}';
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
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
                form.action = '{{ route("support.reset.accountid") }}';
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
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
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection