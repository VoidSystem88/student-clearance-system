@extends('layouts.app')

@section('title', 'Clearance Status')
@section('header', 'Clearance Status')

@section('content')
<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-600 text-sm">Approved</p>
                <p class="text-2xl font-bold text-green-700">{{ $approvedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-600 text-sm">Pending</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $pendingCount ?? 0 }}</p>
            </div>
            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-600 text-sm">Rejected</p>
                <p class="text-2xl font-bold text-red-700">{{ $rejectedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-times-circle text-red-500 text-2xl"></i>
        </div>
    </div>
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Not Submitted</p>
                <p class="text-2xl font-bold text-gray-700">{{ $notSubmittedCount ?? 0 }}</p>
            </div>
            <i class="fas fa-hourglass-start text-gray-500 text-2xl"></i>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 mb-6">
    <div class="flex justify-between items-center mb-2">
        <span class="font-semibold text-gray-700">Overall Progress</span>
        <span class="text-sm text-gray-500">{{ $approvedCount ?? 0 }}/{{ $totalDepartments ?? 0 }} cleared</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full transition-all" style="width: {{ isset($totalDepartments) && $totalDepartments > 0 ? ($approvedCount ?? 0) / $totalDepartments * 100 : 0 }}%"></div>
    </div>
    @if(isset($isFullyCleared) && $isFullyCleared)
        <div class="mt-4 text-center">
            <a href="{{ route('student.clearance.print') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">
                <i class="fas fa-download"></i> Download Clearance Slip
            </a>
        </div>
    @endif
</div>

<!-- Departments Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-800">Department Clearance Details</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Department</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Processed Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($departments ?? [] as $department)
                    @php
                        $request = $clearanceRequests->get($department->id) ?? null;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $department->name }}</td>
                        <td class="px-5 py-3">
                            @if($request && $request->status == 'approved')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <i class="fas fa-check-circle"></i> Approved
                                </span>
                            @elseif($request && $request->status == 'rejected')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-times-circle"></i> Rejected
                                </span>
                            @elseif($request && $request->status == 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <i class="fas fa-clock"></i> Pending
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-hourglass-start"></i> Not Submitted
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request ? $request->submitted_at->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request && $request->processed_at ? $request->processed_at->format('M d, Y') : '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $request ? $request->remarks ?? '—' : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">No departments available</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection