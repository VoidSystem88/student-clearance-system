@extends('layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')

@push('styles')
<style>
    /* Profile Picture Container */
    .profile-pic-container {
        position: relative;
        display: inline-block;
        cursor: pointer;
    }
    
    .profile-pic-container .profile-pic {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #3b82f6;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .profile-pic-container:hover .profile-pic {
        transform: scale(1.02);
        box-shadow: 0 6px 25px rgba(59, 130, 246, 0.5);
    }
    
    .profile-pic-container .edit-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #3b82f6;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid var(--bg-primary);
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(59, 130, 246, 0.4);
    }
    
    .profile-pic-container:hover .edit-overlay {
        transform: scale(1.1);
        background: #2563eb;
    }
    
    .profile-pic-container .edit-overlay i {
        font-size: 14px;
    }
    
    /* Dark mode adjustments */
    body.dark .profile-pic-container .edit-overlay {
        border-color: #1f2937;
    }
    
    /* Student info header */
    .profile-header {
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
        border-radius: 16px;
        padding: 30px 20px;
        text-align: center;
        margin-bottom: 24px;
    }
    
    body.dark .profile-header {
        background: linear-gradient(135deg, #1e293b 0%, #1f2937 100%);
    }
    
    .profile-name {
        font-size: 24px;
        font-weight: 700;
        margin-top: 12px;
        margin-bottom: 4px;
    }
    
    .profile-role {
        font-size: 14px;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- ✅ PROFILE HEADER WITH CENTERED PICTURE -->
    <div class="profile-header">
        <!-- Profile Picture with Edit Overlay -->
        <div class="profile-pic-container" onclick="window.openModalFunc()" title="Click to change profile picture">
            <img id="profilePagePic" class="profile-pic"
                 src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode(Auth::user()->first_name ?? 'User') . '&size=120' }}"
                 alt="Profile Picture">
            <div class="edit-overlay">
                <i class="fas fa-camera"></i>
            </div>
        </div>
        
        <!-- Student Name & Info -->
        <h2 class="profile-name">{{ $student->first_name ?? '' }} {{ $student->last_name ?? '' }}</h2>
        <p class="profile-role">{{ $student->course ?? 'N/A' }} - {{ $student->year_level ?? 'N/A' }}</p>
        
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
            <i class="fas fa-id-card mr-1"></i> Student ID: {{ $student->student_id ?? 'N/A' }} &nbsp;|&nbsp; 
            <i class="fas fa-envelope mr-1"></i> {{ $student->email ?? 'N/A' }}
        </p>
        
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            <i class="fas fa-calendar-alt mr-1"></i> Member since {{ $student->created_at ? $student->created_at->format('F d, Y') : 'N/A' }}
        </p>
    </div>
    
    <!-- ✅ EDIT FORM -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-blue-50 dark:bg-blue-900/20">
            <h3 class="font-semibold text-gray-800 dark:text-white">
                <i class="fas fa-user-edit text-blue-600 dark:text-blue-400 mr-2"></i> Edit Personal Information
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Update your account details below</p>
        </div>
        
        <div class="p-5">
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900/30 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900/30 border-l-4 border-red-500 text-red-700 dark:text-red-400 p-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.profile.update') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Student ID (Readonly) -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Student ID</label>
                        <input type="text" value="{{ $student->student_id ?? 'N/A' }}" 
                               class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                               readonly disabled>
                    </div>
                    
                    <!-- Account ID (Readonly) -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Account ID</label>
                        <input type="text" value="{{ $student->account_id ?? 'N/A' }}" 
                               class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                               readonly disabled>
                    </div>
                    
                    <!-- First Name -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name ?? '') }}" 
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                        @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Last Name -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name ?? '') }}" 
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                        @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $student->email ?? '') }}" 
                               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500" required>
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Course - READONLY -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Course</label>
                        <input type="text" value="{{ $student->course ?? 'N/A' }}" 
                               class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                               readonly disabled>
                        <input type="hidden" name="course" value="{{ $student->course ?? '' }}">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> Course cannot be changed. Please contact assistance if you need to update your course.
                        </p>
                    </div>
                    
                    <!-- Year Level - READONLY -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Year Level</label>
                        <input type="text" value="{{ $student->year_level ?? 'N/A' }}" 
                               class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-500 dark:text-gray-400 cursor-not-allowed" 
                               readonly disabled>
                        <input type="hidden" name="year_level" value="{{ $student->year_level ?? '' }}">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-info-circle"></i> Year level cannot be changed. Please contact assistance if you need to update your year level.
                        </p>
                    </div>
                    
                    <!-- New Password -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">New Password (optional)</label>
                        <div class="relative">
                            <input type="password" name="password" id="password_field" 
                                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 pr-10 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('password_field', 'passIcon')" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <i id="passIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Confirm Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="confirm_password_field" 
                                   class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 pr-10 text-sm text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <button type="button" onclick="togglePassword('confirm_password_field', 'confirmIcon')" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                <i id="confirmIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex flex-wrap gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm transition flex items-center gap-2">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    <a href="{{ route('student.dashboard') }}" class="bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-5 py-2 rounded-lg text-sm hover:bg-gray-400 dark:hover:bg-gray-500 transition inline-block">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
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
    
    // ✅ Sync profile picture with top bar when updated
    document.addEventListener('DOMContentLoaded', function() {
        const profilePagePic = document.getElementById('profilePagePic');
        const topBarPic = document.getElementById('profilePicture');
        
        if (topBarPic) {
            const observer = new MutationObserver(function() {
                if (profilePagePic && topBarPic.src) {
                    profilePagePic.src = topBarPic.src;
                }
            });
            observer.observe(topBarPic, { attributes: true, attributeFilter: ['src'] });
        }
    });
</script>
@endsection