@extends('layouts.support')

@section('title', 'Maintenance Access')

@section('header', 'Secure Access Required')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-red-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Maintenance Control</h2>
            <p class="text-gray-500 mt-2">This area is restricted. Please enter the access password.</p>
        </div>
        
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif
        
        <form method="GET" action="{{ route('support.maintenance') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-lock mr-1"></i> Access Password
                </label>
                <input type="password" name="mpass" 
                    class="w-full border border-gray-300 rounded-lg p-3 focus:ring-red-500 focus:border-red-500"
                    placeholder="Enter maintenance password" required autofocus>
            </div>
            
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-lg transition flex items-center justify-center gap-2">
                <i class="fas fa-unlock-alt"></i> Access Maintenance Panel
            </button>
        </form>
        
        <div class="mt-6 text-center text-xs text-gray-400">
            <i class="fas fa-info-circle"></i>
            Contact system administrator if you don't have access.
        </div>
    </div>
</div>
@endsection