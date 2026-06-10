@extends('layouts.admin')

@section('title', 'Backup Access')
@section('header', 'Backup Access')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-red-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Secure Access Required</h2>
            <p class="text-gray-600 mt-2">Please enter your password to access the database backup system.</p>
        </div>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('admin.backup.verify') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Your Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" 
                           class="w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required autofocus>
                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-500">
                        <i id="passwordToggleIcon" class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                <i class="fas fa-unlock-alt"></i> Verify & Access Backup
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('passwordToggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection
    