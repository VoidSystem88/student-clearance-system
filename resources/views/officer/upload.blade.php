@extends('layouts.officer')

@section('title', 'Upload CSV')
@section('header', 'Upload CSV')

@push('styles')
<style>
    /* Force dark mode */
    body.dark-mode .bg-white {
        background-color: #1f2937 !important;
    }
    body.dark-mode .border-gray-200 {
        border-color: #374151 !important;
    }
    body.dark-mode .border-gray-300 {
        border-color: #4b5563 !important;
    }
    body.dark-mode .text-gray-800 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-700 {
        color: #e5e7eb !important;
    }
    body.dark-mode .text-gray-600 {
        color: #9ca3af !important;
    }
    body.dark-mode .text-gray-500 {
        color: #9ca3af !important;
    }
    body.dark-mode .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
    }
    
    body.dark-mode .bg-blue-100 {
        background-color: #1e3a5f !important;
    }
    body.dark-mode .text-blue-600 {
        color: #60a5fa !important;
    }
    body.dark-mode .bg-gray-200 {
        background-color: #374151 !important;
    }
    body.dark-mode .text-gray-700 {
        color: #e5e7eb !important;
    }
    
    .file-input {
        transition: all 0.2s ease;
    }
    .file-input:hover {
        border-color: #3b82f6;
    }
    body.dark-mode .file-input {
        border-color: #4b5563;
    }
    body.dark-mode .file-input:hover {
        border-color: #60a5fa;
    }
</style>
@endpush

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-upload text-2xl text-blue-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800">Bulk Upload Students</h3>
            <p class="text-sm text-gray-500">Upload a CSV file to verify multiple students at once</p>
        </div>
        
        <form method="POST" action="{{ route('officer.upload.csv') }}" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition file-input">
                <i class="fas fa-file-csv text-4xl text-gray-400 mb-3"></i>
                <p class="text-sm text-gray-600 mb-2">
                    <span class="font-medium text-blue-600">Click to choose</span> or drag and drop
                </p>
                <p class="text-xs text-gray-500">CSV files only • One Student ID per row (first column)</p>
                <input type="file" name="csv_file" accept=".csv,.txt" required
                       class="mt-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            
            <div class="mt-6 flex gap-3">
                <a href="{{ route('officer.dashboard') }}" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-center hover:bg-gray-300 transition font-medium">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg transition font-medium">
                    <i class="fas fa-upload mr-2"></i> Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection