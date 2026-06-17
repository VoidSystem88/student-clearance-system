<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Support - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        .sidebar-transition { transition: all 0.3s ease; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); position: fixed; z-index: 50; }
            .sidebar.open { transform: translateX(0); }
        }
        .nav-active { background-color: #e0e7ff; color: #1e40af; border-left: 4px solid #3b82f6; }
        .announcement-banner {
            background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
            border-left: 4px solid #f59e0b;
            transition: all 0.2s ease;
        }
        .shutdown-badge {
            background-color: #dc2626;
            color: white;
            font-size: 0.7rem;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 20px;
            display: inline-block;
            margin-left: 8px;
        }
        .maintenance-indicator {
            width: 8px;
            height: 8px;
            background-color: #ef4444;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        
        /* Notification Bubble Styles */
        .nav-badge {
            margin-left: auto;
            background-color: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 20px;
            text-align: center;
            display: inline-block;
            animation: bounce 0.3s ease;
        }
        .nav-badge.zero {
            background-color: #9ca3af;
        }
        @keyframes bounce {
            0% { transform: scale(0.8); opacity: 0; }
            80% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        /* Sidebar menu item relative positioning */
        .nav-item {
            position: relative;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s;
        }
        .nav-item .badge-wrapper {
            margin-left: auto;
        }

        /* Notification Bell Animation */
        @keyframes ring {
            0% { transform: rotate(0); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-15deg); }
            60% { transform: rotate(10deg); }
            80% { transform: rotate(-10deg); }
            100% { transform: rotate(0); }
        }
        .notification-ring {
            animation: ring 0.6s ease-in-out;
        }
        
        /* Notification Dropdown Styles */
        #supportNotificationDropdown {
            min-width: 380px;
            max-width: 90vw;
        }
        
        .notification-item {
            transition: all 0.2s ease;
        }
        .notification-item.unread {
            background-color: #eff6ff;
        }
        .notification-item:hover {
            background-color: #f3f4f6;
        }
        .notification-item .notification-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #3b82f6;
            flex-shrink: 0;
        }
        
        @media (max-width: 640px) {
            #supportNotificationDropdown {
                position: fixed;
                top: 60px;
                left: 10px;
                right: 10px;
                width: auto !important;
                min-width: unset;
                max-width: unset;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">

    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 bg-blue-600 text-white p-2 rounded-lg shadow-lg">
        <i class="fas fa-bars text-xl"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed top-0 left-0 h-full w-64 bg-white shadow-xl z-40 flex flex-col sidebar-transition">
        <div class="p-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-headset text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-gray-800">Support Portal</h1>
                    <p class="text-xs text-gray-500">Clearance System</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('support.dashboard') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.dashboard') ? 'nav-active' : '' }}">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>
            
            <!-- Requests Link with Badge -->
            <a href="{{ route('support.requests') }}" id="requestsLink" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.requests') ? 'nav-active' : '' }}">
                <i class="fas fa-ticket-alt w-5"></i>
                <span>Requests</span>
                <span id="requestsBadge" class="nav-badge zero ml-auto" style="display: none;">0</span>
            </a>
                                        
            <!-- Feedbacks Link with Badge -->
            <a href="{{ route('support.feedbacks') }}" id="feedbacksLink" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.feedbacks') ? 'nav-active' : '' }}">
                <i class="fas fa-star w-5"></i>
                <span>Feedbacks</span>
                <span id="feedbacksBadge" class="nav-badge zero ml-auto" style="display: none;">0</span>
            </a>
                                        
            <a href="{{ route('support.students') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.students') ? 'nav-active' : '' }}">
                <i class="fas fa-users w-5"></i>
                <span>Manage Students</span>
            </a>
                                        
            <a href="{{ route('support.profile') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.profile') ? 'nav-active' : '' }}">
                <i class="fas fa-user-circle w-5"></i>
                <span>My Profile</span>
                @if(auth()->user()->admin_2fa_enabled)
                    <span class="ml-auto text-xs bg-green-100 text-green-700 px-1.5 py-0.5 rounded-full">2FA ON</span>
                @endif
            </a>
            <a href="{{ route('support.announcements') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.announcements') ? 'nav-active' : '' }}">
                <i class="fas fa-bullhorn w-5"></i>
                <span>Announcements</span>
                <span class="ml-auto text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full">NEW</span>
            </a>
            <!-- REMINDERS MENU ITEM - SUPPORT -->
            <a href="{{ route('support.reminders') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.reminders') ? 'nav-active' : '' }}">
                <i class="fas fa-bell w-5"></i>
                <span>Reminders</span>
                @php
                    $activeRemindersCount = \App\Models\Reminder::where('is_active', true)
                        ->where(function($q) {
                            $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                        })
                        ->where(function($q) {
                            $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                        })
                        ->count();
                @endphp
                @if($activeRemindersCount > 0)
                    <span class="ml-auto text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded-full">{{ $activeRemindersCount }}</span>
                @endif
            </a>
            <!-- MAINTENANCE MENU ITEM -->
            <a href="{{ route('support.maintenance') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.maintenance') ? 'nav-active' : '' }}">
                <i class="fas fa-tools w-5"></i>
                <span>Maintenance</span>
                @if(Cache::get('maintenance_mode') || Cache::get('read_only_mode'))
                    <span class="ml-auto">
                        <span class="maintenance-indicator"></span>
                    </span>
                @endif
            </a>

            <div class="border-t my-3 mx-5 border-gray-200"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-red-50 transition w-full text-left">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>

        <div class="p-4 border-t border-gray-200 text-center text-xs text-gray-400">
            <p>© {{ date('Y') }} Clearance System</p>
            <p>Support v1.0</p>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- Main Content -->
    <main class="md:ml-64 min-h-screen">
        <div class="bg-white shadow-sm sticky top-0 z-20">
            <div class="px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="md:hidden w-10"></div>
                    <h2 class="text-lg font-semibold text-gray-700">@yield('header')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <!-- ============ NOTIFICATION BELL ============ -->
                    <div class="relative">
                        <button id="supportNotificationBell" class="relative text-gray-600 hover:text-blue-600 transition-colors focus:outline-none" title="Notifications">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="supportNotificationBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="supportNotificationDropdown" class="absolute right-0 mt-2 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50 overflow-hidden">
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                                <h3 class="font-semibold text-gray-800 text-sm">
                                    <i class="fas fa-bell text-blue-600 mr-2"></i>Notifications
                                </h3>
                                <button onclick="markAllSupportNotificationsAsRead()" class="text-xs text-blue-600 hover:text-blue-800 transition font-medium">
                                    <i class="fas fa-check-double mr-1"></i> Mark all read
                                </button>
                            </div>
                            <div id="supportNotificationList" class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                                <div class="p-6 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-200 text-center bg-gray-50">
                                <a href="{{ route('support.notifications') }}" class="text-sm text-blue-600 hover:text-blue-800 transition font-medium">
                                    View all notifications <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <span class="text-sm text-gray-600 hidden sm:block">{{ Auth::user()->email ?? 'support@clearance.com' }}</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- GLOBAL ANNOUNCEMENT BANNER -->
        @php
            $activeAnnouncement = session('active_announcement');
            $isShutdown = session('is_shutdown_mode');
        @endphp

        @if($activeAnnouncement)
        <div id="globalAnnouncementBanner" class="announcement-banner mx-4 mt-4 p-4 rounded-lg shadow-sm flex justify-between items-start">
            <div class="flex items-start gap-3">
                <i class="fas fa-bullhorn text-amber-500 text-xl mt-0.5"></i>
                <div>
                    <div class="font-semibold text-amber-800 flex items-center flex-wrap">
                        📢 ANNOUNCEMENT
                        @if($isShutdown)
                            <span class="shutdown-badge"><i class="fas fa-power-off mr-1"></i> TEMPORARY SHUTDOWN</span>
                        @endif
                    </div>
                    <p class="text-amber-700 text-sm mt-1">{{ $activeAnnouncement }}</p>
                    <p class="text-xs text-amber-500 mt-2">Posted by Support Team • {{ now()->format('F j, Y g:i A') }}</p>
                </div>
            </div>
            <button onclick="dismissAnnouncement()" class="text-amber-400 hover:text-amber-600 transition">
                <i class="fas fa-times"></i>
            </button>
        </div>
        @endif

        <div class="p-4 md:p-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4 flex justify-between items-center">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4 flex justify-between items-center">
                    <span><i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700">&times;</button>
                </div>
            @endif
            
            @yield('content')
        </div>
    </main>

    <script>
        // ============ MOBILE MENU ============
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('hidden');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }

        // ============ NOTIFICATION BELL FUNCTIONS ============
        
        // Fetch notification counts from server
        function loadSupportNotificationCounts() {
            fetch('{{ route("support.notification.counts") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('supportNotificationBadge');
                    if (badge) {
                        if (data.total > 0) {
                            badge.textContent = data.total > 99 ? '99+' : data.total;
                            badge.classList.remove('hidden');
                            // Add ring animation on new notification
                            const bellIcon = document.querySelector('#supportNotificationBell i');
                            if (bellIcon) {
                                bellIcon.classList.remove('notification-ring');
                                // Trigger reflow for animation restart
                                void bellIcon.offsetWidth;
                                bellIcon.classList.add('notification-ring');
                            }
                        } else {
                            badge.classList.add('hidden');
                            badge.classList.remove('notification-pulse');
                        }
                    }
                    
                    // Update sidebar badges
                    updateSupportSidebarBadges(data.bug_reports, data.support_requests, data.feedbacks);
                }
            })
            .catch(error => {
                console.error('Error loading notification counts:', error);
            });
        }

        // Update sidebar badges
        function updateSupportSidebarBadges(bugReports, supportRequests, feedbacks) {
            // Update requests badge
            const requestsBadge = document.getElementById('requestsBadge');
            if (requestsBadge) {
                if (supportRequests > 0) {
                    requestsBadge.textContent = supportRequests > 99 ? '99+' : supportRequests;
                    requestsBadge.classList.remove('zero');
                    requestsBadge.style.display = 'inline-block';
                } else {
                    requestsBadge.style.display = 'none';
                }
            }
            
            // Update feedbacks badge
            const feedbacksBadge = document.getElementById('feedbacksBadge');
            if (feedbacksBadge) {
                if (feedbacks > 0) {
                    feedbacksBadge.textContent = feedbacks > 99 ? '99+' : feedbacks;
                    feedbacksBadge.classList.remove('zero');
                    feedbacksBadge.style.display = 'inline-block';
                } else {
                    feedbacksBadge.style.display = 'none';
                }
            }
        }

        // Load notifications for dropdown
        function loadSupportNotifications() {
            const container = document.getElementById('supportNotificationList');
            if (!container) return;
            
            container.innerHTML = `
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                </div>
            `;
            
            fetch('{{ route("support.notification.counts") }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.recent_items) {
                    if (data.recent_items.length === 0) {
                        container.innerHTML = `
                            <div class="p-8 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">No new notifications</p>
                                <p class="text-xs text-gray-400 mt-1">All caught up! 🎉</p>
                            </div>
                        `;
                        return;
                    }
                    
                    let html = '';
                    data.recent_items.forEach(item => {
                        const colorClass = item.color === 'red' ? 'bg-red-100 text-red-600' :
                                          (item.color === 'purple' ? 'bg-purple-100 text-purple-600' :
                                          (item.color === 'yellow' ? 'bg-yellow-100 text-yellow-600' :
                                          'bg-blue-100 text-blue-600'));
                        
                        // Check if already viewed in session storage
                        const viewedKey = 'support_viewed_' + item.type + '_' + item.id;
                        const isViewed = sessionStorage.getItem(viewedKey) !== null;
                        const isUnread = !isViewed;
                        
                        // Get time ago
                        const timeAgo = getTimeAgo(item.created_at);
                        
                        // Get status badge
                        let statusBadge = '';
                        if (item.status && item.status === 'pending') {
                            statusBadge = `<span class="text-xs text-yellow-600 bg-yellow-50 px-1.5 py-0.5 rounded-full inline-flex items-center gap-1 ml-2">
                                <i class="fas fa-clock text-xs"></i> Pending
                            </span>`;
                        }
                        
                        html += `
                            <div class="notification-item ${isUnread ? 'unread' : ''} p-3 hover:bg-gray-50 transition cursor-pointer" 
                                 onclick="markSupportNotificationRead('${item.type}', '${item.id}'); window.location.href='${item.link}'">
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 rounded-full ${colorClass} flex items-center justify-center">
                                            <i class="fas ${item.icon} text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center flex-wrap gap-1">
                                            <p class="text-sm font-medium text-gray-800">${escapeHtml(item.title)}</p>
                                            ${statusBadge}
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1">${escapeHtml(item.message)}</p>
                                        <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                                    </div>
                                    ${isUnread ? '<div class="w-2 h-2 bg-blue-600 rounded-full mt-1 flex-shrink-0"></div>' : ''}
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                container.innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-circle mr-1"></i> Failed to load notifications
                        <button onclick="loadSupportNotifications()" class="ml-2 text-blue-600 underline text-sm">Retry</button>
                    </div>
                `;
            });
        }

        // Get time ago string
        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
            const days = Math.floor(hours / 24);
            if (days < 7) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
            return date.toLocaleDateString();
        }

        // Escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Mark single notification as read (stored in sessionStorage)
        function markSupportNotificationRead(type, id) {
            const viewedKey = 'support_viewed_' + type + '_' + id;
            sessionStorage.setItem(viewedKey, Date.now().toString());
            
            // Update badge count
            loadSupportNotificationCounts();
        }

        // Mark all notifications as read
        function markAllSupportNotificationsAsRead() {
            // Show loading on button
            const btn = event ? event.target : document.querySelector('#supportNotificationDropdown .border-b button');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Loading...';
                btn.disabled = true;
            }
            
            fetch('{{ route("support.notifications.mark-all-read") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
                
                if (data.success) {
                    // Clear all viewed items in sessionStorage
                    const keys = Object.keys(sessionStorage);
                    keys.forEach(key => {
                        if (key.startsWith('support_viewed_')) {
                            sessionStorage.removeItem(key);
                        }
                    });
                    
                    // Hide badge
                    const badge = document.getElementById('supportNotificationBadge');
                    if (badge) {
                        badge.classList.add('hidden');
                    }
                    
                    // Close dropdown
                    const dropdown = document.getElementById('supportNotificationDropdown');
                    if (dropdown) {
                        dropdown.classList.add('hidden');
                    }
                    
                    // Show success toast
                    Swal.fire({
                        icon: 'success',
                        title: 'All marked as read!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    
                    // Reload notifications
                    loadSupportNotifications();
                    loadSupportNotificationCounts();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (btn) {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to mark all as read',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }

        // ============ NOTIFICATION DROPDOWN TOGGLE ============
        const supportBellBtn = document.getElementById('supportNotificationBell');
        const supportDropdown = document.getElementById('supportNotificationDropdown');

        if (supportBellBtn && supportDropdown) {
            supportBellBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = !supportDropdown.classList.contains('hidden');
                
                if (isOpen) {
                    supportDropdown.classList.add('hidden');
                } else {
                    supportDropdown.classList.remove('hidden');
                    loadSupportNotifications();
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (supportBellBtn && supportDropdown) {
                if (!supportBellBtn.contains(e.target) && !supportDropdown.contains(e.target)) {
                    supportDropdown.classList.add('hidden');
                }
            }
        });

        // ============ GLOBAL FUNCTIONS ============
        
        // Dismiss announcement banner
        window.dismissAnnouncement = function() {
            const banner = document.getElementById('globalAnnouncementBanner');
            if(banner) {
                banner.style.display = 'none';
            }
            sessionStorage.removeItem('active_announcement');
            sessionStorage.removeItem('is_shutdown_mode');
            Swal.fire({
                icon: 'info',
                title: 'Announcement dismissed',
                text: 'You can see new announcements from the Announcements page.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }

        // ============ INITIALIZATION ============
        document.addEventListener('DOMContentLoaded', function() {
            // Load notification counts on page load
            loadSupportNotificationCounts();
            
            // Auto-refresh counts every 30 seconds
            setInterval(loadSupportNotificationCounts, 30000);
            
            // Refresh when page becomes visible again
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    loadSupportNotificationCounts();
                }
            });
            
            // Auto-refresh dropdown content when opened
            const dropdownObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        if (!supportDropdown.classList.contains('hidden')) {
                            loadSupportNotifications();
                        }
                    }
                });
            });
            
            if (supportDropdown) {
                dropdownObserver.observe(supportDropdown, { attributes: true });
            }
            
            console.log('✅ Support Notification System Loaded');
            console.log('📊 Notifications will auto-refresh every 30 seconds');
        });

        // Clean up interval on page unload
        window.addEventListener('beforeunload', function() {
            // Intervals will be cleared automatically
        });
    </script>
    @stack('scripts')
</body>
</html>