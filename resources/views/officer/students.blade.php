@extends('layouts.officer')

@section('title', 'Pending Students')
@section('header', 'Pending Students')

@push('styles')
<style>
    /* Force dark mode */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .border-gray-200 {
        border-color: #374151 !important;
    }
    body.dark-mode .border-gray-100 {
        border-color: #374151 !important;
    }
    body.dark-mode .text-gray-800 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-600 {
        color: #9ca3af !important;
    }
    body.dark-mode .text-gray-500 {
        color: #9ca3af !important;
    }
    body.dark-mode .bg-gray-50 {
        background-color: #1f2937 !important;
    }
    body.dark-mode .divide-gray-100 {
        border-color: #374151 !important;
    }
    body.dark-mode .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
    }
    
    body.dark-mode .badge-pending {
        background: #78350f !important;
        color: #fcd34d !important;
    }
    
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .btn-verify {
        background: #2563eb;
        color: white;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-verify:hover:not(:disabled) {
        background: #1d4ed8;
        transform: scale(1.02);
    }
    .btn-verify:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    body.dark-mode .btn-verify {
        background: #3b82f6 !important;
    }
    body.dark-mode .btn-verify:hover:not(:disabled) {
        background: #2563eb !important;
    }
    
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .table-row:hover {
        background-color: #f8fafc;
    }
    body.dark-mode .table-row:hover {
        background-color: #1e293b !important;
    }
</style>
@endpush

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-clock text-yellow-600 mr-2"></i> Pending Students
            <span class="text-sm font-normal text-gray-500 ml-2">({{ $students->count() }})</span>
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($students as $student)
                <tr class="table-row transition">
                    <td class="px-4 py-3 text-gray-500">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-mono font-medium text-gray-800">{{ $student->student_id }}</td>
                    <td class="px-4 py-3 text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $student->course ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="verifyStudent('{{ $student->student_id }}', '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', this)" 
                                class="btn-verify">
                            <i class="fas fa-check"></i> Verify
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-3 text-green-500"></i>
                        <p>All students are verified!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
async function verifyStudent(studentId, studentName, button) {
    const result = await Swal.fire({
        title: 'Verify Student?',
        html: `Verify <strong>${studentName}</strong> (${studentId})?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Verify!',
        cancelButtonText: 'Cancel'
    });
    if (!result.isConfirmed) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const originalHtml = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner"></span>';
    
    try {
        const response = await fetch('{{ route("officer.verify.student") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ student_id: studentId, student_name: studentName })
        });
        const data = await response.json();
        if (data.success) {
            await Swal.fire('Success!', data.message, 'success');
            location.reload();
        } else {
            await Swal.fire('Error!', data.message, 'error');
            button.disabled = false;
            button.innerHTML = originalHtml;
        }
    } catch (error) {
        await Swal.fire('Error!', 'Network error. Please try again.', 'error');
        button.disabled = false;
        button.innerHTML = originalHtml;
    }
}
</script>
@endsection