@extends('layouts.admin')

@section('title', 'Database Backup')
@section('header', 'Database Backup')

@section('content')
<div class="container mx-auto">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
            <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
        </div>
    @endif

    <!-- Create Backup Button -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold mb-2">Create New Backup</h2>
                <p class="text-gray-600">Download a complete backup of your database including all tables and data.</p>
            </div>
            <form method="POST" action="{{ route('admin.backup.create') }}">
                @csrf
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <i class="fas fa-database"></i> Create Backup Now
                </button>
            </form>
        </div>
    </div>

    <!-- ============ IMPORT DATABASE SECTION ============ -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold mb-2">Import Database</h2>
                <p class="text-gray-600">Restore your database from a SQL backup file.</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('admin.backup.import') }}" enctype="multipart/form-data" class="mt-4" onsubmit="return confirm('WARNING: Importing will overwrite existing data. Continue?')">
            @csrf
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-gray-700 mb-2">SQL File</label>
                    <input type="file" name="sql_file" accept=".sql,.txt" class="w-full border rounded-lg p-2" required>
                    <p class="text-xs text-gray-500 mt-1">Max size: 20MB. Allowed formats: .sql, .txt</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                        <i class="fas fa-upload"></i> Import Database
                    </button>
                </div>
            </div>
        </form>
        
        <div class="mt-3 p-3 bg-yellow-50 rounded-lg text-sm text-yellow-800">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>Warning:</strong> Importing will replace your current database. Always create a backup first before importing.
        </div>
    </div>

    <!-- Backup Files List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <h3 class="text-lg font-bold p-4 bg-gray-50 border-b">
            <i class="fas fa-archive mr-2"></i> Available Backups
        </h3>
        
        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-3 text-left">Filename</th>
                            <th class="p-3 text-left">Size</th>
                            <th class="p-3 text-left">Date Created</th>
                            <th class="p-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $backup)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">
                                <i class="fas fa-file-archive text-gray-500 mr-2"></i>
                                {{ $backup['name'] }}
                            </td>
                            <td class="p-3">{{ $backup['size'] ?? 'Unknown' }}</td>
                            <td class="p-3">
                                @php
                                    $date = is_numeric($backup['modified']) ? date('Y-m-d H:i:s', $backup['modified']) : ($backup['modified'] ?? 'N/A');
                                @endphp
                                {{ $date }}
                            </td>
                            <td class="p-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.backup.download', ['filename' => $backup['name']]) }}" 
                                       class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition flex items-center gap-1">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <form method="POST" action="{{ route('admin.backup.delete', ['filename' => $backup['name']]) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this backup file?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition flex items-center gap-1">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-database text-5xl mb-3 text-gray-300"></i>
                <p>No backup files found. Click "Create Backup Now" to generate your first backup.</p>
            </div>
        @endif
    </div>
    
    <!-- Storage Info -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm text-yellow-800">
        <i class="fas fa-info-circle mr-2"></i>
        Backups are stored in <strong>storage/app/backups/</strong> directory.
        <button onclick="location.reload()" class="ml-4 text-blue-600 hover:underline">Refresh</button>
    </div>
</div>

<script>
    // Auto-refresh page after backup creation
    @if(session('success'))
        setTimeout(function() {
            location.reload();
        }, 2000);
    @endif
</script>
@endsection