<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Clearance System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8a8a8; }
        
        .sidebar-transition { transition: all 0.3s ease; }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 50;
            }
            .sidebar.open { transform: translateX(0); }
        }
        
        .nav-active {
            background-color: #e0e7ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        
        /* Notification Badge Pulse Animation */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        .notification-pulse {
            animation: pulse 1s ease-in-out infinite;
        }
        
        /* Notification Dropdown */
        .notification-dropdown {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .notification-item {
            transition: background-color 0.2s ease;
        }
        
        .notification-item.unread {
            background-color: #eff6ff;
        }
        
        .notification-item:hover {
            background-color: #f3f4f6;
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
        <div class="p-5 border-b">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-gray-800">Admin Portal</h1>
                    <p class="text-xs text-gray-500">Clearance System</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.students') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-users w-5"></i>
                <span>Students</span>
            </a>
            <a href="{{ route('admin.departments') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-building w-5"></i>
                <span>Departments</span>
            </a>
            <a href="{{ route('admin.officers') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-user-tie w-5"></i>
                <span>Officers</span>
            </a>
            <a href="{{ route('admin.announcements') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-bullhorn w-5"></i>
                <span>Announcements</span>
            </a>
            <a href="{{ route('admin.profile') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-user-circle w-5"></i>
                <span>My Profile</span>
            </a>
            <a href="{{ route('admin.backup.password.form') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-database w-5"></i>
                <span>Backup</span>
            </a>
            
           
            
            <div class="border-t my-3 mx-5"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-red-50 transition w-full text-left">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </nav>

        <div class="p-4 border-t text-center text-xs text-gray-400">
            <p>© {{ date('Y') }} Clearance System</p>
            <p>Admin v1.0</p>
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
                    <h2 class="text-lg font-semibold text-gray-700">@yield('header', 'Admin Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <!-- ============ NOTIFICATION BELL ============ -->
                    <div class="relative">
                        <button id="notificationBell" class="relative text-gray-600 hover:text-blue-600 transition-colors">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notificationBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 hidden">0</span>
                        </button>
                        
                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50">
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="font-semibold text-gray-800">
                                    <i class="fas fa-bell text-blue-600 mr-2"></i>Notifications
                                </h3>
                                <button onclick="markAllNotificationsAsRead()" class="text-xs text-blue-600 hover:text-blue-800 transition">
                                    Mark all as read
                                </button>
                            </div>
                            <div id="notificationList" class="notification-dropdown max-h-96 overflow-y-auto">
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading...
                                </div>
                            </div>
                            <div class="p-2 border-t border-gray-200 text-center">
                                <a href="{{ url('/admin/notifications') }}" class="text-sm text-blue-600 hover:text-blue-800 transition">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <span class="text-sm text-gray-600 hidden sm:block">{{ session('admin_email', 'Admin') }}</span>
                    
                    <!-- Secret IP Logs Trigger - Hidden in profile icon -->
                    <div class="relative">
                        <div id="secretProfileIcon" class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-200 transition">
                            <i class="fas fa-user-shield text-blue-600 text-sm"></i>
                        </div>
                        <span id="secretClickCount" class="hidden">0</span>
                    </div>
                </div>
            </div>
        </div>

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
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ============ NOTIFICATION FUNCTIONS ============
    
    // Get base URL
    const baseUrl = window.location.origin;
    
    // Load unread count
    function loadUnreadCount() {
        fetch(baseUrl + '/admin/notifications/unread-count', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('hidden');
                    badge.classList.add('notification-pulse');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('notification-pulse');
                }
            }
        })
        .catch(error => {
            console.error('Error loading unread count:', error);
        });
    }
    
    // Load notifications for dropdown
    function loadNotifications() {
        fetch(baseUrl + '/admin/notifications/ajax', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('notificationList');
            if (!container) return;
            
            if (!data.notifications || data.notifications.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                        <p class="text-sm">No notifications yet</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            data.notifications.forEach(notification => {
                const isUnread = !notification.is_read;
                const iconClass = getNotificationIcon(notification.type);
                const timeAgo = getTimeAgo(notification.created_at);
                
                html += `
                    <div class="notification-item ${isUnread ? 'unread' : ''} p-3 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition" onclick="markNotificationAsRead(${notification.id})">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="${iconClass} text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800">${escapeHtml(notification.title)}</p>
                                <p class="text-xs text-gray-500 mt-1">${escapeHtml(notification.message)}</p>
                                <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                            </div>
                            ${isUnread ? '<div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>' : ''}
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const container = document.getElementById('notificationList');
            if (container) {
                container.innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-circle mr-1"></i> Failed to load notifications
                        <button onclick="loadNotifications()" class="ml-2 text-blue-600 underline">Retry</button>
                    </div>
                `;
            }
        });
    }
    
    // Mark single notification as read
    function markNotificationAsRead(id) {
        fetch(baseUrl + '/admin/notifications/' + id + '/mark-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(() => {
            loadUnreadCount();
            loadNotifications();
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Mark all notifications as read
    function markAllNotificationsAsRead() {
        fetch(baseUrl + '/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(() => {
            loadUnreadCount();
            loadNotifications();
            document.getElementById('notificationDropdown')?.classList.add('hidden');
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Get notification icon based on type
    function getNotificationIcon(type) {
        const icons = {
            'new_student': 'fas fa-user-graduate',
            'new_request': 'fas fa-ticket-alt',
            'new_feedback': 'fas fa-star',
            'clearance_approved': 'fas fa-check-circle',
            'clearance_rejected': 'fas fa-times-circle',
            'system': 'fas fa-server',
            'bug_report': 'fas fa-bug'
        };
        return icons[type] || 'fas fa-bell';
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
    
    // Toggle notification dropdown
    const bellBtn = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    
    if (bellBtn) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                if (!dropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            }
        });
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (bellBtn && dropdown) {
            if (!bellBtn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        }
    });
    
    // Load unread count on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadUnreadCount();
    });
    
    // Refresh unread count every 30 seconds
    setInterval(loadUnreadCount, 30000);
    
    // ============ SECRET IP LOGS TRIGGER ============
    let secretClickCount = 0;
    let secretClickTimer = null;
    let secretKonamiSequence = [];
    const secretKonamiCode = [1,2,7,9,4,4,1,0,0,0,5,4];
    
    // Method 1: Click 5 times on profile icon
    const profileIcon = document.getElementById('secretProfileIcon');
    if (profileIcon) {
        profileIcon.addEventListener('click', function() {
            secretClickCount++;
            
            // Visual feedback
            this.style.transform = 'scale(0.9)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
            
            if (secretClickTimer) clearTimeout(secretClickTimer);
            
            if (secretClickCount >= 5) {
                openSecretIpLogs();
                secretClickCount = 0;
            }
            
            secretClickTimer = setTimeout(() => {
                secretClickCount = 0;
            }, 2000);
        });
    }
    
    // Method 2: Konami Code on keyboard
    document.addEventListener('keydown', function(e) {
        secretKonamiSequence.push(e.key);
        if (secretKonamiSequence.length > secretKonamiCode.length) secretKonamiSequence.shift();
        
        if (JSON.stringify(secretKonamiSequence) === JSON.stringify(secretKonamiCode)) {
            openSecretIpLogs();
            secretKonamiSequence = [];
        }
    });
    
    function openSecretIpLogs() {
        // Create modal
        const modal = document.createElement('div');
        modal.id = 'secretIpModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-95 hidden items-center justify-center z-50 p-4';
        modal.innerHTML = `
            <div class="bg-gray-900 rounded-xl max-w-5xl w-full max-h-[85vh] overflow-hidden border border-gray-700 shadow-2xl">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700 bg-gray-800">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2">
                        <i class="fas fa-network-wired text-blue-400"></i>
                        Secret IP Logs
                        <span class="text-xs text-gray-400 bg-gray-700 px-2 py-1 rounded-full ml-2">Admin Only </span>
                    </h3>
                    <button onclick="closeSecretIpLogs()" class="text-gray-400 hover:text-white text-2xl transition">&times;</button>
                </div>
                <div class="p-4 overflow-auto" style="max-height: calc(85vh - 70px);">
                    <div id="secretIpContent" class="text-green-400 font-mono text-sm">
                        <div class="text-center py-12">
                            <i class="fas fa-spinner fa-spin text-3xl text-blue-400"></i>
                            <p class="mt-3 text-gray-400">Loading IP logs...</p>
                        </div>
                    </div>
                    <div class="mt-4 flex gap-3 justify-end border-t border-gray-700 pt-4">
                        <button onclick="refreshSecretIpLogs()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <a href="{{ url('/admin/download-ips') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                            <i class="fas fa-download"></i> Download CSV
                        </a>
                        <button onclick="closeSecretIpLogs()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition flex items-center gap-2">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        loadSecretIpContent();
    }
    
    function closeSecretIpLogs() {
        const modal = document.getElementById('secretIpModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            setTimeout(() => modal.remove(), 300);
        }
    }
    
    function loadSecretIpContent() {
        const content = document.getElementById('secretIpContent');
        if (!content) return;
        
        content.innerHTML = '<div class="text-center py-12"><i class="fas fa-spinner fa-spin text-3xl text-blue-400"></i><p class="mt-3 text-gray-400">Loading IP logs...</p></div>';
        
        fetch('{{ url("/admin/ip-logs") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const preElements = doc.querySelectorAll('pre');
            
            if (preElements.length > 0) {
                let logsHtml = '';
                preElements.forEach(pre => {
                    logsHtml += pre.outerHTML;
                });
                content.innerHTML = logsHtml;
            } else {
                content.innerHTML = '<div class="text-yellow-400 text-center py-12"><i class="fas fa-inbox text-4xl mb-3"></i><p>No IP logs yet.</p><p class="text-sm mt-2">Visit <code class="bg-gray-800 px-2 py-1 rounded">/my-ip</code> to generate logs.</p></div>';
            }
        })
        .catch(err => {
            content.innerHTML = '<div class="text-red-400 text-center py-12"><i class="fas fa-exclamation-triangle text-4xl mb-3"></i><p>Error loading logs: ' + err.message + '</p></div>';
        });
    }
    
    function refreshSecretIpLogs() {
        loadSecretIpContent();
    }
    
    // Make functions global
    window.closeSecretIpLogs = closeSecretIpLogs;
    window.refreshSecretIpLogs = refreshSecretIpLogs;
</script>
    @stack('scripts')
    <!-- Tracker -->
<img src="{{ url('/track.gif') }}" width="1" height="1" style="display: none;">
</body>
</html>