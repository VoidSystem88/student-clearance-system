@extends('layouts.support')

@section('title', 'My Profile')

@section('header', 'My Profile')

@section('content')
<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center gap-4 mb-6 pb-4 border-b">
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-user-shield text-blue-600 text-2xl"></i>
        </div>
        <div>
            <h3 class="text-xl font-bold text-gray-800">{{ Auth::user()->name }}</h3>
            <p class="text-gray-500">{{ Auth::user()->email }}</p>
            <span class="inline-block px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 mt-1">
                <i class="fas fa-crown mr-1"></i> {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Profile Information -->
        <div class="border rounded-xl p-5">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-600"></i>
                Profile Information
            </h4>
            <form method="POST" action="{{ route('support.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" 
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ Auth::user()->email }}" 
                           class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50" readonly>
                    <p class="text-xs text-gray-400 mt-1">Email cannot be changed for security reasons</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <input type="text" value="{{ ucfirst(Auth::user()->role) }}" 
                           class="w-full border border-gray-300 rounded-lg p-2 bg-gray-50" readonly>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-save mr-1"></i> Update Profile
                </button>
            </form>
        </div>

        <!-- Password Change -->
        <div class="border rounded-xl p-5">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-key text-yellow-600"></i>
                Change Password
            </h4>
            <form method="POST" action="{{ route('support.password.change') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" required 
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" required 
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" required 
                           class="w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm transition">
                    <i class="fas fa-key mr-1"></i> Change Password
                </button>
            </form>
        </div>

        <!-- 2FA Settings -->
        <div class="border rounded-xl p-5 md:col-span-2">
            <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-shield-alt text-green-600"></i>
                Two-Factor Authentication (2FA)
            </h4>
            
            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <p class="font-medium text-gray-800">2FA Status</p>
                        <p class="text-sm text-gray-600">
                            When enabled, you will need to enter a 6-digit code sent to your email every time you login.
                        </p>
                    </div>
                    <div>
                        @if(Auth::user()->admin_2fa_enabled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> ENABLED
                            </span>
                            <form method="POST" action="{{ route('support.2fa.disable') }}" class="inline ml-2">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fas fa-toggle-on mr-1"></i> Disable
                                </button>
                            </form>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-circle mr-1"></i> DISABLED
                            </span>
                            <form method="POST" action="{{ route('support.2fa.enable') }}" class="inline ml-2">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                    <i class="fas fa-toggle-off mr-1"></i> Enable
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(Auth::user()->admin_2fa_enabled)
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-1"></i>
                    Your 2FA is currently <strong>ENABLED</strong>. You will receive a verification code via email when you login.
                    <br><small>Last code generated: {{ Auth::user()->admin_2fa_expires_at ? Auth::user()->admin_2fa_expires_at->diffForHumans() : 'Never' }}</small>
                </p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection