@php
    $user = Auth::user();
    $reminders = \App\Models\Reminder::getActiveRemindersForUser($user);
    $announcements = \App\Models\Announcement::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    $totalNotifications = $reminders->count() + $announcements->count();
@endphp

<div class="relative">
    <!-- Notification Bell -->
    <button id="notificationBell" class="relative text-gray-600 hover:text-blue-600 transition-colors">
        <i class="fas fa-bell text-xl"></i>
        @if($totalNotifications > 0)
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
            {{ $totalNotifications }}
        </span>
        @endif
    </button>
    
    <!-- Notification Dropdown - FIXED POSITION -->
    <div id="notificationDropdown" style="display: none;" class="notification-dropdown-custom">
        <div class="sticky top-0 bg-gray-100 dark:bg-gray-700 px-4 py-2 border-b dark:border-gray-600">
            <h3 class="font-semibold text-gray-800 dark:text-white">Notifications</h3>
        </div>
        
        @if($reminders->count() > 0)
        <div class="border-b dark:border-gray-700">
            <div class="px-4 py-2 bg-yellow-50 dark:bg-yellow-900/30">
                <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">
                    <i class="fas fa-bell mr-1"></i> Reminders
                </h4>
            </div>
            @foreach($reminders as $reminder)
            <div class="px-4 py-3 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-start gap-2">
                    <div class="text-yellow-500 mt-1">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $reminder->title }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $reminder->message }}</p>
                        @if($reminder->end_date)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-calendar-alt"></i> Until: {{ $reminder->end_date->format('M d, Y') }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        
        @if($announcements->count() > 0)
        <div>
            <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/30">
                <h4 class="text-sm font-semibold text-blue-800 dark:text-blue-400">
                    <i class="fas fa-megaphone mr-1"></i> Announcements
                </h4>
            </div>
            @foreach($announcements as $announcement)
            <div class="px-4 py-3 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <div class="flex items-start gap-2">
                    <div class="text-blue-500 mt-1">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $announcement->title }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($announcement->content, 100) }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-calendar-alt"></i> {{ $announcement->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        
        @if($totalNotifications == 0)
        <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
            <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300 dark:text-gray-600"></i>
            <p class="text-sm">No new notifications</p>
        </div>
        @endif
    </div>
</div>

<style>
/* Notification Dropdown - Fixed Position */
.notification-dropdown-custom {
    position: fixed !important;
    top: 60px !important;
    left: 100px !important;
    right: auto !important;
    bottom: auto !important;
    transform: none !important;
    width: 350px !important;
    min-width: 280px !important;
    max-width: 90vw !important;
    background: var(--bg-primary, #ffffff);
    border-radius: 12px !important;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2) !important;
    border: 1px solid var(--border-color, #e5e7eb) !important;
    z-index: 9999 !important;
    margin-top: 0 !important;
    overflow: hidden;
    max-height: 80vh;
    overflow-y: auto;
}

/* Mobile: Center the notification dropdown */
@media (max-width: 640px) {
    .notification-dropdown-custom {
        position: fixed !important;
        top: 60px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        right: auto !important;
        bottom: auto !important;
        width: 90% !important;
        max-width: 320px !important;
        min-width: 260px !important;
        margin-top: 0 !important;
    }
}
</style>

<script>
    // Notification dropdown toggle
    const notificationBell = document.getElementById('notificationBell');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBell && notificationDropdown) {
        notificationBell.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (notificationDropdown.style.display === 'none' || notificationDropdown.style.display === '') {
                notificationDropdown.style.display = 'block';
            } else {
                notificationDropdown.style.display = 'none';
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationDropdown && notificationBell) {
                if (!notificationBell.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.style.display = 'none';
                }
            }
        });
    }
</script>