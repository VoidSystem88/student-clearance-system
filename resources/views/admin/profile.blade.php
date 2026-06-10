@extends('layouts.admin')

@section('title', 'My Profile - Admin')
@section('header', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-xl shadow-lg overflow-hidden mb-6">
        <div class="px-6 py-8 flex items-center gap-4">
            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur">
                <i class="fas fa-user-shield text-white text-4xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h2>
                <p class="text-blue-100 flex items-center gap-2 mt-1">
                    <i class="fas fa-envelope"></i> {{ Auth::user()->email }}
                </p>
                <p class="text-blue-100 flex items-center gap-2 mt-1 text-sm">
                    <i class="fas fa-shield-alt"></i> Administrator Account
                </p>
            </div>
        </div>
    </div>
    
    <!-- Profile Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800">
                <i class="fas fa-user-edit text-blue-600 mr-2"></i> Edit Profile Information
            </h3>
            <p class="text-sm text-gray-500">Update your account details and password</p>
        </div>
        
        <div class="p-5">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4">
                    <ul class="text-sm list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.update') }}" id="profileUpdateForm">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- First Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-user text-gray-400 mr-1"></i> First Name
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                               required>
                        @error('first_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Last Name -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-user text-gray-400 mr-1"></i> Last Name
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                               required>
                        @error('last_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-envelope text-gray-400 mr-1"></i> Email Address
                        </label>
                        <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                               required>
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Role (readonly) -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-tag text-gray-400 mr-1"></i> Role
                        </label>
                        <input type="text" value="Administrator" 
                               class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-600" 
                               readonly disabled>
                    </div>
                    
                    <!-- New Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-key text-gray-400 mr-1"></i> New Password (optional)
                        </label>
                        <input type="password" name="password" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                               placeholder="Leave blank to keep current"
                               autocomplete="new-password">
                        <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-1">
                            <i class="fas fa-check-circle text-gray-400 mr-1"></i> Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                               placeholder="Confirm new password"
                               autocomplete="new-password">
                    </div>
                </div>
                
                <!-- 2FA Toggle Section with Animation -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-600"></i>
                        Two-Factor Authentication (2FA)
                    </h4>
                    
                    <div id="2faCard" class="bg-gray-50 rounded-lg p-4 transition-all duration-300">
                        <div class="flex justify-between items-center flex-wrap gap-3">
                            <div>
                                <p class="font-medium text-gray-800">2FA Status</p>
                                <p class="text-sm text-gray-500">
                                    When enabled, you will need to enter a 6-digit code sent to your email every time you login.
                                </p>
                            </div>
                            <div>
                                @if(Auth::user()->admin_2fa_enabled)
                                    <span id="2faStatusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 transition-all duration-300">
                                        <i class="fas fa-check-circle mr-1"></i> ENABLED
                                    </span>
                                    <button type="button" onclick="toggle2FA()" id="2faToggleBtn" class="ml-2 px-3 py-1 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-toggle-on mr-1"></i> Disable
                                    </button>
                                @else
                                    <span id="2faStatusBadge" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 transition-all duration-300">
                                        <i class="fas fa-circle mr-1"></i> DISABLED
                                    </span>
                                    <button type="button" onclick="toggle2FA()" id="2faToggleBtn" class="ml-2 px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-toggle-off mr-1"></i> Enable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div id="2faInfo" class="mt-3 transition-all duration-500 overflow-hidden" style="max-height: 0; opacity: 0;">
                        @if(Auth::user()->admin_2fa_enabled)
                        <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Your 2FA is currently <strong>ENABLED</strong>. You will receive a verification code via email when you login.
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
                    <button type="submit" id="updateProfileBtn" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    .loading-pulse {
        animation: pulse 0.5s ease-in-out infinite;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.3s ease-out;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggle2FA() {
        const badge = document.getElementById('2faStatusBadge');
        const toggleBtn = document.getElementById('2faToggleBtn');
        const card = document.getElementById('2faCard');
        const infoDiv = document.getElementById('2faInfo');
        const isCurrentlyEnabled = badge.innerText.includes('ENABLED');
        
        // Show loading animation on button
        const originalBtnText = toggleBtn.innerHTML;
        toggleBtn.innerHTML = '<div class="spinner-border animate-spin inline-block w-4 h-4 border-2 border-white rounded-full mr-1"></div> Loading...';
        toggleBtn.disabled = true;
        toggleBtn.classList.add('opacity-70', 'cursor-not-allowed');
        
        // Add pulse animation to card
        card.classList.add('loading-pulse');
        
        fetch('{{ route("admin.2fa.toggle") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Remove loading animation
            card.classList.remove('loading-pulse');
            toggleBtn.disabled = false;
            toggleBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            
            if (data.success) {
                if (data.enabled) {
                    // Update badge with animation
                    badge.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        badge.innerHTML = '<i class="fas fa-check-circle mr-1"></i> ENABLED';
                        badge.classList.remove('bg-gray-100', 'text-gray-800');
                        badge.classList.add('bg-green-100', 'text-green-800');
                        badge.style.transform = 'scale(1)';
                    }, 150);
                    
                    // Update button
                    toggleBtn.innerHTML = '<i class="fas fa-toggle-on mr-1"></i> Disable';
                    toggleBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    toggleBtn.classList.add('bg-red-600', 'hover:bg-red-700');
                    
                    // Show info message with slide down animation
                    infoDiv.innerHTML = `
                        <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500 fade-in">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Your 2FA is currently <strong>ENABLED</strong>. You will receive a verification code via email when you login.
                            </p>
                        </div>
                    `;
                    infoDiv.style.maxHeight = '200px';
                    infoDiv.style.opacity = '1';
                    
                } else {
                    // Update badge with animation
                    badge.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        badge.innerHTML = '<i class="fas fa-circle mr-1"></i> DISABLED';
                        badge.classList.remove('bg-green-100', 'text-green-800');
                        badge.classList.add('bg-gray-100', 'text-gray-800');
                        badge.style.transform = 'scale(1)';
                    }, 150);
                    
                    // Update button
                    toggleBtn.innerHTML = '<i class="fas fa-toggle-off mr-1"></i> Enable';
                    toggleBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
                    toggleBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    
                    // Hide info message with slide up animation
                    infoDiv.style.maxHeight = '0';
                    infoDiv.style.opacity = '0';
                    setTimeout(() => {
                        infoDiv.innerHTML = '';
                    }, 500);
                }
                
                // Show success toast
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2500,
                    showConfirmButton: false,
                    position: 'top-end',
                    toast: true,
                    background: '#10b981',
                    color: 'white',
                    iconColor: 'white'
                });
            } else {
                toggleBtn.innerHTML = originalBtnText;
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Something went wrong',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            card.classList.remove('loading-pulse');
            toggleBtn.disabled = false;
            toggleBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            toggleBtn.innerHTML = originalBtnText;
            
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Please check your connection and try again.',
                confirmButtonColor: '#ef4444'
            });
        });
    }
    
    // Initialize info div height on page load
    document.addEventListener('DOMContentLoaded', function() {
        const infoDiv = document.getElementById('2faInfo');
        if (infoDiv && infoDiv.innerHTML.trim() !== '') {
            infoDiv.style.maxHeight = '200px';
            infoDiv.style.opacity = '1';
        }
    });
    
    // Make function global
    window.toggle2FA = toggle2FA;
</script>
@endsection