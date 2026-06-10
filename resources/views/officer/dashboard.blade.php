@extends('layouts.app')

@section('title', 'Officer Dashboard')
@section('header', 'Officer Dashboard')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
        <div>
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-check-double text-green-600 mr-2"></i> Verified Student List
            </h3>
            <p class="text-sm text-gray-500">Manage verified students for <strong>{{ $department->name ?? 'your department' }}</strong></p>
            <p class="text-xs text-gray-400 mt-1">
                <i class="fas fa-shield-alt mr-1"></i> 
                You can only see student names, course, and year level for privacy protection.
            </p>
        </div>
        <div class="flex gap-2">
            <button onclick="openUploadVerifiedModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-upload"></i> Upload CSV
            </button>
            <button onclick="openAddVerifiedModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus"></i> Add Manually
            </button>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student Name</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Year Level</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Date Added</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($verifiedStudents ?? [] as $index => $verified)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-5 py-3 text-gray-800">{{ $verified->student_name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $verified->course ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $verified->year_level ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-500">{{ $verified->verified_at ? \Carbon\Carbon::parse($verified->verified_at)->format('M d, Y') : 'N/A' }}</td>
                    <td class="px-5 py-3">
                        <button onclick="removeVerified({{ $verified->id }})" 
                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>No verified students yet</p>
                        <p class="text-xs mt-1">Upload CSV or add manually</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upload Verified CSV Modal -->
<div id="uploadVerifiedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-upload text-green-600 mr-2"></i> Upload Verified Students
            </h3>
            <button onclick="closeUploadVerifiedModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <p class="text-sm text-gray-600 mb-4">
            Upload a CSV file with columns: <strong>Student ID, Student Name</strong><br>
            <span class="text-xs text-gray-400">Example: 2023-00123,Juan Dela Cruz</span>
        </p>
        
        <form id="uploadVerifiedForm" method="POST" action="{{ route('officer.verified.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="w-full border border-gray-300 rounded-lg p-2 text-sm">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeUploadVerifiedModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Upload</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Verified Student Manually Modal -->
<div id="addVerifiedModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i> Add Verified Student
            </h3>
            <button onclick="closeAddVerifiedModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        
        <form id="addVerifiedForm" method="POST" action="{{ route('officer.verified.add') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Student ID *</label>
                <input type="text" name="student_id" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="2023-00123">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 mb-2 text-sm font-medium">Student Name *</label>
                <input type="text" name="student_name" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500"
                       placeholder="Juan Dela Cruz">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddVerifiedModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Add to Verified List</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadVerifiedModal() {
        document.getElementById('uploadVerifiedForm').reset();
        document.getElementById('uploadVerifiedModal').classList.remove('hidden');
        document.getElementById('uploadVerifiedModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeUploadVerifiedModal() {
        document.getElementById('uploadVerifiedModal').classList.add('hidden');
        document.getElementById('uploadVerifiedModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function openAddVerifiedModal() {
        document.getElementById('addVerifiedForm').reset();
        document.getElementById('addVerifiedModal').classList.remove('hidden');
        document.getElementById('addVerifiedModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeAddVerifiedModal() {
        document.getElementById('addVerifiedModal').classList.add('hidden');
        document.getElementById('addVerifiedModal').classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    function removeVerified(id) {
        Swal.fire({
            title: 'Remove from Verified List?',
            text: 'This student will no longer be automatically approved for clearance.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '/officer/verified/' + id;
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                let method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
    
    document.getElementById('uploadVerifiedModal')?.addEventListener('click', function(e) { 
        if (e.target === this) closeUploadVerifiedModal(); 
    });
    document.getElementById('addVerifiedModal')?.addEventListener('click', function(e) { 
        if (e.target === this) closeAddVerifiedModal(); 
    });
    document.addEventListener('keydown', function(e) { 
        if (e.key === 'Escape') { 
            closeUploadVerifiedModal(); 
            closeAddVerifiedModal(); 
        } 
    });
</script>
@endsection