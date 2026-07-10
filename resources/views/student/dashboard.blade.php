@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<!-- ============ NOTIFICATIONS SECTION ============ -->
@php
    $unreadNotifications = Auth::user()->notifications()
        ->where('is_read', false)
        ->orderBy('created_at', 'desc')
        ->get();
@endphp

@if($unreadNotifications->count() > 0)
<div class="mb-6 space-y-3">
    @foreach($unreadNotifications as $notif)
    @php
        $notifData = json_decode($notif->data, true);
        $icon = $notifData['icon'] ?? 'fa-bell';
        $color = $notifData['color'] ?? 'blue';
        
        $colorClass = match($color) {
            'purple' => 'border-purple-500 bg-purple-50 dark:bg-purple-900/30',
            'yellow' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/30',
            'green' => 'border-green-500 bg-green-50 dark:bg-green-900/30',
            'red' => 'border-red-500 bg-red-50 dark:bg-red-900/30',
            default => 'border-blue-500 bg-blue-50 dark:bg-blue-900/30',
        };
        
        $iconColor = match($color) {
            'purple' => 'text-purple-600 dark:text-purple-400',
            'yellow' => 'text-yellow-600 dark:text-yellow-400',
            'green' => 'text-green-600 dark:text-green-400',
            'red' => 'text-red-600 dark:text-red-400',
            default => 'text-blue-600 dark:text-blue-400',
        };
    @endphp
    
    <div class="rounded-xl p-4 shadow-sm border-l-4 {{ $colorClass }} transition-all hover:shadow-md dark:border-opacity-50">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full {{ str_replace('border-l-4', '', $colorClass) }} flex items-center justify-center flex-shrink-0">
                <i class="fas {{ $icon }} {{ $iconColor }} text-lg"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ $notif->title }}</h4>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $notif->message }}</p>
            </div>
            <button onclick="markAsRead({{ $notif->id }})" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Course & Year Display -->
<div class="mb-3">
    <label class="block text-gray-700 dark:text-gray-300 text-sm font-medium mb-1">Course & Year</label>
    <input type="text" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200" value="{{ $student->course_year ?? ($student->course . ' - ' . $student->year_level) }}" readonly>
</div>

<!-- Welcome Card with Dynamic Message -->
<div class="rounded-xl p-6 mb-6 text-white bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-800 dark:to-blue-950">
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            @php
                $isNewUser = $student->created_at && $student->created_at->diffInDays(now()) < 1;
            @endphp
            
            @if($isNewUser)
                <h2 class="text-2xl font-bold mb-2">🎉 Welcome, {{ $student->first_name ?? $student->name ?? 'Student' }}!</h2>
                <p class="text-blue-100 text-sm">We're excited to have you here! Start your clearance journey today.</p>
            @else
                <h2 class="text-2xl font-bold mb-2">👋 Welcome back, {{ $student->first_name ?? $student->name ?? 'Student' }}!</h2>
                <p class="text-blue-100 text-sm">Track your clearance progress here.</p>
            @endif
        </div>
        <div class="bg-white/20 backdrop-blur rounded-lg px-4 py-2 text-center">
            <p class="text-xs opacity-80">Account ID</p>
            <p class="font-mono text-sm font-bold">{{ $student->account_id ?? 'N/A' }}</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Student ID Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Student ID</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white">{{ $student->student_id ?? 'N/A' }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-id-card text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Course & Year Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Course & Year</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white">{{ $student->course_year ?? ($student->course . ' - ' . $student->year_level) }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-graduation-cap text-green-600 dark:text-green-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Progress Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Progress</p>
                <p class="font-bold text-lg text-gray-800 dark:text-white">{{ $approvedCount ?? 0 }}/{{ $totalDepartments ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Status Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Status</p>
                <p class="font-bold text-lg">
                    @if(isset($isFullyCleared) && $isFullyCleared)
                        <span class="text-green-600 dark:text-green-400">✓ Cleared</span>
                    @else
                        <span class="text-yellow-600 dark:text-yellow-400">In Progress</span>
                    @endif
                </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                <i class="fas fa-flag-checkered text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
    </div>
</div>

<!-- Progress Bar -->
<div class="bg-white dark:bg-gray-800 rounded-xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="flex justify-between items-center mb-2 flex-wrap gap-2">
        <span class="font-semibold text-gray-700 dark:text-gray-300">Overall Clearance Progress</span>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $approvedCount ?? 0 }} out of {{ $totalDepartments ?? 0 }} departments cleared</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: {{ isset($totalDepartments) && $totalDepartments > 0 ? ($approvedCount ?? 0) / $totalDepartments * 100 : 0 }}%"></div>
    </div>
    @if(isset($isFullyCleared) && $isFullyCleared)
        <div class="mt-4 text-center">
            <a href="{{ route('student.clearance.print') }}" class="inline-flex items-center gap-2 bg-green-600 dark:bg-green-700 text-white px-5 py-2 rounded-lg hover:bg-green-700 dark:hover:bg-green-800 transition">
                <i class="fas fa-download"></i>
                <span>Download Clearance Slip</span>
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// I-embed ang user data para magamit ni Void AI
window.VoidUserData = {
    // Basic Info
    id: {{ Auth::id() }},
    name: "{{ $student->first_name ?? Auth::user()->first_name ?? 'Student' }}",
    fullName: "{{ trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')) }}",
    studentId: "{{ $student->student_id ?? 'N/A' }}",
    accountId: "{{ $student->account_id ?? 'N/A' }}",
    email: "{{ Auth::user()->email ?? '' }}",
    
    // Academic Info
    course: "{{ $student->course ?? 'N/A' }}",
    yearLevel: "{{ $student->year_level ?? 'N/A' }}",
    courseYear: "{{ $student->course_year ?? ($student->course . ' - ' . $student->year_level) }}",
    
    // Clearance Progress
    clearedCount: {{ $approvedCount ?? 0 }},
    totalDepartments: {{ $totalDepartments ?? 0 }},
    isFullyCleared: {{ isset($isFullyCleared) && $isFullyCleared ? 'true' : 'false' }},
    
    // Pending departments (kung meron)
    pendingDepartments: @json($pendingDepartments ?? []),
    
    // User Status
    isNewUser: {{ isset($isNewUser) && $isNewUser ? 'true' : 'false' }},
    createdAt: "{{ $student->created_at ?? Auth::user()->created_at ?? '' }}",
    
    // Role
    role: "{{ Auth::user()->role ?? 'student' }}"
};

console.log('✅ Dashboard: Void AI User Data Loaded');
console.log('👤 User:', window.VoidUserData.name);
console.log('📊 Progress:', window.VoidUserData.clearedCount + '/' + window.VoidUserData.totalDepartments);

// Function to mark notification as read
function markAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.success) {
              location.reload();
          }
      });
}
</script>
@endpush