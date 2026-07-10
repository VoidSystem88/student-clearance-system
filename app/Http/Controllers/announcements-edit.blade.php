@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <div class="bg-white rounded shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Edit Announcement</h1>
            <a href="{{ route('admin.announcements') }}" class="text-blue-600">← Back to Announcements</a>
        </div>
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.announcement.update', $announcement->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" 
                       class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Type</label>
                <select name="type" class="w-full border rounded px-3 py-2">
                    <option value="info" {{ $announcement->type == 'info' ? 'selected' : '' }}>📘 Info</option>
                    <option value="warning" {{ $announcement->type == 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                    <option value="success" {{ $announcement->type == 'success' ? 'selected' : '' }}>✅ Success</option>
                    <option value="danger" {{ $announcement->type == 'danger' ? 'selected' : '' }}>🔴 Danger</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Content</label>
                <textarea name="content" rows="5" class="w-full border rounded px-3 py-2" required>{{ old('content', $announcement->content) }}</textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 mb-2">Start Date (Optional)</label>
                    <input type="date" name="start_date" value="{{ $announcement->start_date ? $announcement->start_date->format('Y-m-d') : '' }}" 
                           class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">End Date (Optional)</label>
                    <input type="date" name="end_date" value="{{ $announcement->end_date ? $announcement->end_date->format('Y-m-d') : '' }}" 
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $announcement->is_active ? 'checked' : '' }} class="mr-2">
                    <span>Active</span>
                </label>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Announcement</button>
                <a href="{{ route('admin.announcements') }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection