@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')

@section('content')
<div class="bg-white dark:bg-blue-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
    <div class="px-5 py-4 border-b border-blue-100 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-blue dark:from-blue-900/20 dark:to-gray-800">
        <h3 class="font-semibold text-black-800 dark:text-white-200">
            <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 mr-2"></i> Personal Information
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">View and update your account information</p>
    </div>
    
    <div class="p-5">
        @if(session('success'))
            <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Student ID</label>
                    <input type="text" value="{{ $student->student_id }}" class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-gray-200" readonly disabled>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Account ID</label>
                    <input type="text" value="{{ $student->account_id }}" class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-gray-200" readonly disabled>
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                    @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Course</label>
                    <select name="course" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                        <option value="BSIT" {{ old('course', $student->course) == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BSCS" {{ old('course', $student->course) == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                        <option value="BSIS" {{ old('course', $student->course) == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                        <option value="BSBA-FM" {{ old('course', $student->course) == 'BSBA-FM' ? 'selected' : '' }}>BSBA Financial Management</option>
                        <option value="BSHM" {{ old('course', $student->course) == 'BSHM' ? 'selected' : '' }}>BS Hospitality Management</option>
                        <option value="BEEd" {{ old('course', $student->course) == 'BEEd' ? 'selected' : '' }}>BEEd</option>
                        <option value="BSEd-English" {{ old('course', $student->course) == 'BSEd-English' ? 'selected' : '' }}>BSEd English</option>
                        <option value="BSCrim" {{ old('course', $student->course) == 'BSCrim' ? 'selected' : '' }}>BS Criminology</option>
                    </select>
                    @error('course') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Year Level</label>
                    <select name="year_level" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" required>
                        <option value="1st Year" {{ old('year_level', $student->year_level) == '1st Year' ? 'selected' : '' }}>1st Year</option>
                        <option value="2nd Year" {{ old('year_level', $student->year_level) == '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3rd Year" {{ old('year_level', $student->year_level) == '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4th Year" {{ old('year_level', $student->year_level) == '4th Year' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year_level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">New Password (optional)</label>
    <div class="relative">
        <input type="password" name="password" id="password_field" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="togglePassword('password_field', 'passIcon')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i id="passIcon" class="fas fa-eye"></i>
        </button>
    </div>
</div>
<div>
    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Confirm Password</label>
    <div class="relative">
        <input type="password" name="password_confirmation" id="confirm_password_field" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 rounded-lg px-3 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500">
        <button type="button" onclick="togglePassword('confirm_password_field', 'confirmIcon')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
            <i id="confirmIcon" class="fas fa-eye"></i>
        </button>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i> Update Profile
                </button>
                <a href="{{ route('student.dashboard') }}" class="ml-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-5 py-2 rounded-lg text-sm hover:bg-gray-400 dark:hover:bg-gray-500 transition inline-block">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection