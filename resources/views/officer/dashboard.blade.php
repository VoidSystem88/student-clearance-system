@extends('layouts.officer')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@push('styles')
<style>
    /* Force dark mode */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .border-gray-200 {
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
    
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.15);
    }
    body.dark-mode .stat-card:hover {
        box-shadow: 0 12px 30px -8px rgba(0,0,0,0.4);
    }
    
    .btn-primary {
        background: #2563eb;
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-primary:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37,99,235,0.4);
    }
    .btn-success {
        background: #16a34a;
        color: white;
        padding: 8px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    .btn-success:hover {
        background: #15803d;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22,163,74,0.4);
    }
    
    body.dark-mode .btn-primary {
        background: #3b82f6 !important;
    }
    body.dark-mode .btn-primary:hover {
        background: #2563eb !important;
    }
    body.dark-mode .btn-success {
        background: #22c55e !important;
    }
    body.dark-mode .btn-success:hover {
        background: #16a34a !important;
    }
</style>
@endpush

@section('content')

{{-- STATISTICS CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-blue-600">{{ $students->count() ?? 0 }}</p>
        <p class="text-sm text-gray-500">Total Students</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-green-600">{{ $verifiedCount ?? 0 }}</p>
        <p class="text-sm text-gray-500">Verified</p>
    </div>
    <div class="stat-card bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
        <p class="text-3xl font-bold text-yellow-600">{{ $students->count() - ($verifiedCount ?? 0) }}</p>
        <p class="text-sm text-gray-500">Pending</p>
    </div>
</div>

{{-- QUICK ACTIONS --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
    <div class="flex items-center justify-center gap-2 mb-3">
        <i class="fas fa-arrow-up text-blue-500 text-2xl"></i>
        <span class="text-gray-600 font-medium">Quick Navigation</span>
    </div>
    <p class="text-sm text-gray-500 mb-4">Select a menu below to manage students</p>
    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('officer.students') }}" class="btn-primary">
            <i class="fas fa-users"></i> View Pending
        </a>
        <a href="{{ route('officer.verified') }}" class="btn-success">
            <i class="fas fa-check-circle"></i> View Verified
        </a>
    </div>
</div>

{{-- RECENT ACTIVITY --}}
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
        <h3 class="font-semibold text-gray-800">
            <i class="fas fa-clock text-gray-500 mr-2"></i> Recent Activity
        </h3>
    </div>
    <div class="p-5 text-center text-gray-500">
        <i class="fas fa-inbox text-3xl mb-2 text-gray-300"></i>
        <p class="text-sm">No recent activity</p>
    </div>
</div>

@endsection