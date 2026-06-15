<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>@yield('title', 'Student Clearance System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <script>
    window.VoidUserData = {
        id: {{ Auth::id() }},
        name: "{{ Auth::user()->first_name ?? Auth::user()->name ?? 'Student' }}",
        fullName: "{{ trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->last_name ?? '')) }}",
        studentId: "{{ Auth::user()->student_id ?? 'N/A' }}",
        accountId: "{{ Auth::user()->account_id ?? 'N/A' }}",
        email: "{{ Auth::user()->email ?? '' }}",
        course: "{{ Auth::user()->course ?? 'N/A' }}",
        yearLevel: "{{ Auth::user()->year_level ?? 'N/A' }}",
        courseYear: "{{ Auth::user()->course_year ?? (Auth::user()->course . ' - ' . Auth::user()->year_level) }}",
        clearedCount: {{ $approvedCount ?? 0 }},
        totalDepartments: {{ $totalDepartments ?? 0 }},
        isFullyCleared: {{ isset($isFullyCleared) && $isFullyCleared ? 'true' : 'false' }},
        pendingDepartments: @json($pendingDepartments ?? []),
        isNewUser: {{ isset($isNewUser) && $isNewUser ? 'true' : 'false' }},
        createdAt: "{{ Auth::user()->created_at ?? '' }}",
        role: "{{ Auth::user()->role ?? 'student' }}"
    };
    </script>
    @endauth

    <style>
        /* ============ CSS VARIABLES FOR DARK MODE ============ */
        :root {
            --bg-body: #f3f4f6;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --bg-tertiary: #f3f4f6;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-tertiary: #9ca3af;
            --border-color: #e5e7eb;
            --sidebar-bg: #ffffff;
            --card-bg: #ffffff;
            --input-bg: #ffffff;
            --input-border: #e5e7eb;
        }

        body.dark {
            --bg-body: #111827;
            --bg-primary: #1f2937;
            --bg-secondary: #374151;
            --bg-tertiary: #111827;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --text-tertiary: #6b7280;
            --border-color: #374151;
            --sidebar-bg: #1f2937;
            --card-bg: #1f2937;
            --input-bg: #374151;
            --input-border: #4b5563;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-primary);
            transition: background-color 0.3s ease, color 0.3s ease;
            background-image: url('/images/vlight.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            position: relative;
        }

        body.dark { background-image: url('/images/vdark.png'); }

        .bg-white { background-color: var(--bg-primary) !important; }
        .bg-gray-50 { background-color: var(--bg-secondary) !important; }
        .bg-gray-100 { background-color: var(--bg-tertiary) !important; }

        .text-gray-700, .text-gray-800, .text-gray-900 { color: var(--text-primary) !important; }
        .text-gray-500, .text-gray-600 { color: var(--text-secondary) !important; }
        .text-gray-400 { color: var(--text-tertiary) !important; }

        .border-gray-100, .border-gray-200, .border-gray-300 { border-color: var(--border-color) !important; }

        .sidebar {
            background-color: var(--sidebar-bg) !important;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid var(--border-color);
        }

        .nav-link {
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s;
        }
        .nav-link:hover { background-color: rgba(59, 130, 246, 0.1); }
        .nav-active { background-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }

        .app-container { display: flex; min-height: 100vh; overflow-x: hidden; }
        .sidebar {
            width: 280px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0; left: 0;
            z-index: 40;
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
        }
        .sidebar.open { transform: translateX(0); }
        .main-wrapper { flex: 1; transition: transform 0.3s ease-in-out; width: 100%; }

        @media (min-width: 768px) {
            .sidebar { transform: translateX(0) !important; position: relative; }
        }
        @media (max-width: 767px) {
            .main-wrapper.menu-open { transform: translateX(280px); }
        }

        /* Dark Mode Toggle */
        .dark-mode-sidebar-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 12px 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        .dark-mode-sidebar-btn:hover { background: rgba(59, 130, 246, 0.1); }
        .dark-mode-content { display: flex; align-items: center; gap: 12px; }
        .dark-mode-icon {
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            transition: all 0.3s ease;
        }
        body.dark .dark-mode-icon { background: linear-gradient(135deg, #1e293b, #0f172a); }
        .dark-mode-icon i { font-size: 16px; color: white; transition: all 0.3s ease; }
        .dark-mode-text { font-size: 14px; font-weight: 500; color: var(--text-primary); }
        .dark-mode-toggle-switch {
            position: relative;
            width: 48px; height: 24px;
            background: #cbd5e1;
            border-radius: 30px;
            transition: all 0.3s ease;
        }
        body.dark .dark-mode-toggle-switch { background: #3b82f6; }
        .dark-mode-toggle-switch::after {
            content: '';
            position: absolute;
            width: 20px; height: 20px;
            background: white;
            border-radius: 50%;
            top: 2px; left: 3px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        body.dark .dark-mode-toggle-switch::after { left: 25px; }

        /* Toast */
        .toast {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            min-width: 200px; animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-success { background: #22c55e; }
        .toast-error { background: #ef4444; }
        .toast-info { background: #3b82f6; }

        input, select, textarea {
            background-color: var(--input-bg);
            color: var(--text-primary);
            border-color: var(--input-border);
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }

        .profile-img {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #3b82f6;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .profile-img:hover { transform: scale(1.05); }
        .profile-img.large { width: 100px; height: 100px; border-width: 3px; }

        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: var(--bg-primary);
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        }

        .fab-hidden { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
        .hide-button { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }

        .assistance-link {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
            border-left: 3px solid #3b82f6;
            margin: 0.5rem 0;
        }
        body.dark .assistance-link {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
        }

        .copyright {
            padding: 1rem;
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-secondary);
            border-top: 1px solid var(--border-color);
        }

        /* ============ NOTIFICATION BELL ============ */
        .notification-bell {
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .notification-bell:hover { transform: scale(1.05); }

        .notification-badge {
            position: absolute;
            top: -8px; right: -8px;
            background-color: #ef4444;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 20px;
            min-width: 18px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        @keyframes bounce {
            0% { transform: scale(0.8); opacity: 0; }
            80% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        /*
         * ============ NOTIFICATION DROPDOWN ============
         * FIX: Ginagamit ang JS para i-position ang dropdown dynamically
         * gamit ang getBoundingClientRect() ng bell icon.
         * Ito ang pinaka-reliable na paraan para gumana sa mobile at desktop
         * dahil hindi ito affected ng sticky/overflow/z-index stacking contexts.
         * Ang dropdown ay naka-teleport sa <body> sa DOMContentLoaded.
         */
        #notificationDropdown {
            position: fixed;
            z-index: 99999;
            background: var(--bg-primary);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25);
            border: 1px solid var(--border-color);
            overflow: hidden;
            display: none;
            /* width at left/top ay set ng JS */
        }

        .notification-header {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-secondary);
        }
        .notification-header h4 { font-size: 0.875rem; font-weight: 600; }

        .notification-list { max-height: 350px; overflow-y: auto; }

        .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            transition: background 0.2s ease;
            cursor: pointer;
            display: block;
        }
        .notification-item:hover { background: var(--bg-secondary); }
        .notification-item.unread { background: rgba(59, 130, 246, 0.08); }
        .notification-item.unread:hover { background: rgba(59, 130, 246, 0.14); }

        .notification-title {
            font-size: 0.8rem; font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
            word-break: break-word;
        }
        .notification-message {
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
            word-break: break-word;
        }
        .notification-time {
            font-size: 0.65rem;
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .notification-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            background: var(--bg-secondary);
        }
        .notification-footer a { font-size: 0.75rem; color: #3b82f6; text-decoration: none; }

        .empty-notification {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.8rem;
        }
        .empty-notification i { font-size: 2rem; margin-bottom: 8px; display: block; opacity: 0.5; }

        .gradient-banner { background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%); }
        body.dark .gradient-banner { background: linear-gradient(135deg, #374151 0%, #1f2937 100%); }
        .gradient-feedback { background: linear-gradient(135deg, #faf5ff 0%, #ffffff 100%); }
        body.dark .gradient-feedback { background: linear-gradient(135deg, #4c1d95 0%, #1f2937 100%); }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>
    <div id="toastContainer"></div>

    <!-- Profile Picture Modal -->
    <div id="profileModal" class="modal">
        <div class="modal-content">
            <div class="text-center mb-4">
                <i class="fas fa-camera text-4xl text-blue-500 mb-2"></i>
                <h3 class="text-xl font-bold">Palitan ang Profile Picture</h3>
                <p class="text-sm text-gray-500 mt-1">Mag-upload ng larawan para sa iyong profile</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Pumili ng larawan</label>
                <input type="file" id="profileUpload" accept="image/jpeg,image/png,image/jpg" class="w-full p-2 border rounded-lg">
                <p class="text-xs text-gray-400 mt-1">Max 5MB, JPG/PNG only</p>
            </div>
            <div class="flex gap-2 mt-4">
                <button id="closeModalBtn" class="flex-1 bg-gray-300 py-2 rounded-lg hover:bg-gray-400 transition">Kanselahin</button>
            </div>
        </div>
    </div>

    <!-- Main App Container -->
    <div class="app-container">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar">
            <div class="p-5 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-lg text-gray-800">Clearance System</h1>
                        <p class="text-xs text-gray-500">Void - Student Portal</p>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav">
                @auth
                    <a href="{{ route('student.dashboard') }}" class="nav-link">
                        <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
                    </a>
                    <a href="{{ route('student.clearance') }}" class="nav-link">
                        <i class="fas fa-clipboard-list w-5"></i><span>Clearance</span>
                    </a>
                    <a href="{{ route('student.reminders') }}" class="nav-link">
                        <i class="fas fa-bell w-5"></i><span>Reminders</span>
                    </a>
                    <a href="{{ route('student.profile') }}" class="nav-link">
                        <i class="fas fa-user-circle w-5"></i><span>My Profile</span>
                    </a>
                    <a href="{{ route('student.feedback') }}" class="nav-link">
                        <i class="fas fa-star w-5"></i><span>Feedback</span>
                    </a>
                    <div class="border-t my-3 mx-5 border-gray-200"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link text-red-600 hover:bg-red-50 w-full text-left">
                            <i class="fas fa-sign-out-alt w-5"></i><span>Logout</span>
                        </button>
                    </form>
                @endauth
            </div>

            <div class="sidebar-footer">
                <button id="darkModeSidebarBtn" class="dark-mode-sidebar-btn">
                    <div class="dark-mode-content">
                        <div class="dark-mode-icon">
                            <i id="sidebarDarkModeIcon" class="fas fa-moon"></i>
                        </div>
                        <span class="dark-mode-text">Dark Mode</span>
                    </div>
                    <div class="dark-mode-toggle-switch"></div>
                </button>
                <a href="{{ route('student.assistance') }}" class="nav-link assistance-link">
                    <i class="fas fa-headset w-5"></i><span>Request Assistance</span>
                </a>
                <div class="copyright">
                    <p>© {{ date('Y') }} Clearance System</p>
                    <p>v1.0 | Void</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div id="mainWrapper" class="main-wrapper">
            <div class="bg-white shadow-sm sticky top-0 z-20">
                <div class="px-4 py-3 flex justify-between items-center">
                    <button id="mobileMenuBtn" class="md:hidden bg-transparent p-1">
                        <i id="hamburgerIcon" class="fas fa-bars text-xl" style="color: var(--text-primary);"></i>
                    </button>
                    <div class="md:hidden w-10"></div>
                    <h2 class="text-lg font-semibold text-gray-800">@yield('header', 'Dashboard')</h2>
                    <div class="flex items-center gap-4">
                        <!-- NOTIFICATION BELL -->
                        <div class="notification-bell" id="notificationBell">
                            <i class="fas fa-bell text-xl text-gray-600 hover:text-blue-600 transition-colors cursor-pointer"></i>
                            <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                        </div>

                        <span class="text-sm hidden sm:block text-gray-600">
                            @auth {{ Auth::user()->first_name ?? '' }} @endauth
                        </span>
                        <img id="profilePicture" class="profile-img cursor-pointer"
                             src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode(Auth::user()->first_name ?? 'User') . '&size=64' }}"
                             alt="Profile"
                             data-user-id="{{ Auth::id() }}">
                    </div>
                </div>
            </div>

            <div class="p-4 md:p-6">
                @yield('content')
            </div>
        </div>
    </div>

    <!--
        NOTIFICATION DROPDOWN
        FIX: Inilipat sa labas ng lahat ng containers (sticky, overflow, z-index stacking).
        Ang JS ang bahala sa positioning gamit ang getBoundingClientRect().
    -->
    <div id="notificationDropdown">
        <div class="notification-header">
            <h4><i class="fas fa-bell mr-2"></i> Notifications</h4>
        </div>
        <div id="notificationList" class="notification-list">
            <div class="empty-notification">
                <i class="fas fa-bell-slash"></i>
                <p>Loading notifications...</p>
            </div>
        </div>
        <div class="notification-footer">
            <a href="{{ route('student.reminders') }}">View all announcements →</a>
        </div>
    </div>

    @include('components.ai-assistant')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const csrfTokenGlobal = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    window.showToast = function(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} text-white px-4 py-2 rounded-lg shadow-lg`;
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>${message}`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    };

    window.handleImageUpload = async function(file) {
        if (!file) return;
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) { window.showToast('JPG or PNG only', 'error'); return; }
        if (file.size > 5 * 1024 * 1024) { window.showToast('Max 5MB only', 'error'); return; }

        const formData = new FormData();
        formData.append('photo', file);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        try {
            window.showToast('Uploading...', 'info');
            const response = await fetch('/upload-profile-photo', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: formData
            });
            const data = await response.json();
            if (response.ok && data.success) {
                const profileImg = document.getElementById('profilePicture');
                if (profileImg) profileImg.src = data.photo_url + '?t=' + Date.now();
                window.showToast(data.message, 'success');
                window.closeModalFunc();
                setTimeout(() => location.reload(), 1500);
            } else {
                window.showToast(data.message || 'Upload failed', 'error');
            }
        } catch (error) {
            window.showToast('Upload failed. Please try again.', 'error');
        }
    };

    window.closeModalFunc = function() {
        const modal = document.getElementById('profileModal');
        const upload = document.getElementById('profileUpload');
        if (modal) modal.classList.remove('active');
        if (upload) upload.value = '';
    };

    window.openModalFunc = function() {
        const modal = document.getElementById('profileModal');
        if (modal) modal.classList.add('active');
    };

    // ============ DARK MODE ============
    function initDarkMode() {
        const sidebarBtn = document.getElementById('darkModeSidebarBtn');
        const sidebarIcon = document.getElementById('sidebarDarkModeIcon');
        const savedTheme = localStorage.getItem('theme');

        if (savedTheme === 'dark') {
            document.body.classList.add('dark');
            if (sidebarIcon) { sidebarIcon.classList.remove('fa-moon'); sidebarIcon.classList.add('fa-sun'); }
        } else {
            document.body.classList.remove('dark');
            if (sidebarIcon) { sidebarIcon.classList.remove('fa-sun'); sidebarIcon.classList.add('fa-moon'); }
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark');
            if (document.body.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                if (sidebarIcon) { sidebarIcon.classList.remove('fa-moon'); sidebarIcon.classList.add('fa-sun'); }
            } else {
                localStorage.setItem('theme', 'light');
                if (sidebarIcon) { sidebarIcon.classList.remove('fa-sun'); sidebarIcon.classList.add('fa-moon'); }
            }
            if (sidebarBtn) {
                sidebarBtn.style.transform = 'scale(0.98)';
                setTimeout(() => { sidebarBtn.style.transform = ''; }, 150);
            }
        }

        if (sidebarBtn) sidebarBtn.addEventListener('click', toggleDarkMode);
    }

    // ============ NOTIFICATIONS ============
    let notificationCheckInterval = null;
    let lastCheckTime = localStorage.getItem('lastNotificationCheck') || Date.now();

    async function checkForNewNotifications() {
        try {
            const response = await fetch('/student/notifications/check', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (data.new_count > 0) {
                        badge.textContent = data.new_count > 99 ? '99+' : data.new_count;
                        badge.style.display = 'inline-block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
                lastCheckTime = data.current_time;
                localStorage.setItem('lastNotificationCheck', lastCheckTime);
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    function updateNotificationList(notifications) {
        const container = document.getElementById('notificationList');
        if (!container) return;

        if (!notifications || notifications.length === 0) {
            container.innerHTML = `
                <div class="empty-notification">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications</p>
                    <p class="text-xs mt-1">Check your reminders page for updates</p>
                </div>`;
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const isUnread = notif.created_at > parseInt(lastCheckTime);
            html += `
                <div class="notification-item ${isUnread ? 'unread' : ''}" onclick="window.location.href='${notif.link}'">
                    <div class="notification-title"><i class="fas ${notif.icon} mr-1"></i> ${escapeHtml(notif.title)}</div>
                    <div class="notification-message">${escapeHtml(notif.message)}</div>
                    <div class="notification-time">${getTimeAgo(notif.created_at)}</div>
                </div>`;
        });
        container.innerHTML = html;
    }

    function loadNotifications() {
        const container = document.getElementById('notificationList');
        if (!container) return;

        container.innerHTML = `
            <div class="empty-notification">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Loading notifications...</p>
            </div>`;

        fetch('/student/notifications/check', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => { if (data.success) updateNotificationList(data.recent_notifications); })
        .catch(() => {
            const c = document.getElementById('notificationList');
            if (c) c.innerHTML = `<div class="empty-notification"><i class="fas fa-exclamation-triangle"></i><p>Failed to load</p></div>`;
        });
    }

    function getTimeAgo(timestamp) {
        const date = new Date(timestamp * 1000);
        const seconds = Math.floor((new Date() - date) / 1000);
        if (seconds < 60) return 'just now';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
        const days = Math.floor(hours / 24);
        if (days < 7) return days + ' day' + (days > 1 ? 's' : '') + ' ago';
        return date.toLocaleDateString();
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ============ NOTIFICATION BELL - SMART POSITIONING ============
    /*
     * FIX: Ang dropdown ay nasa labas ng lahat ng containers.
     * Ginagamit ang getBoundingClientRect() para malaman ang exact
     * position ng bell icon at doon nagpo-position ang dropdown.
     *
     * MOBILE: naka-center sa screen horizontally
     * DESKTOP: naka-align sa kanan ng bell, may boundary check
     */
    function positionAndShowDropdown() {
        const bell = document.getElementById('notificationBell');
        const dropdown = document.getElementById('notificationDropdown');
        if (!bell || !dropdown) return;

        const rect = bell.getBoundingClientRect();
        const MARGIN = 16;
        const DROPDOWN_WIDTH = Math.min(350, window.innerWidth - MARGIN * 2);

        dropdown.style.width = DROPDOWN_WIDTH + 'px';
        dropdown.style.top = (rect.bottom + 8) + 'px';

        let leftPos;

        if (window.innerWidth <= 640) {
            // MOBILE: i-center sa screen
            leftPos = (window.innerWidth - DROPDOWN_WIDTH) / 2;
        } else {
            // DESKTOP: right-align sa bell icon
            leftPos = rect.right - DROPDOWN_WIDTH;
            // Boundary check — huwag lumabas sa left o right edge
            if (leftPos < MARGIN) leftPos = MARGIN;
            if (leftPos + DROPDOWN_WIDTH > window.innerWidth - MARGIN) {
                leftPos = window.innerWidth - DROPDOWN_WIDTH - MARGIN;
            }
        }

        dropdown.style.left = leftPos + 'px';
        dropdown.style.display = 'block';
    }

    const bellBtn = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');

    if (bellBtn && dropdown) {
        bellBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const isOpen = dropdown.style.display === 'block';
            if (!isOpen) {
                positionAndShowDropdown();
                loadNotifications();
            } else {
                dropdown.style.display = 'none';
            }
        });
    }

    // I-reposition pag nag-resize ang window (e.g. orientation change)
    window.addEventListener('resize', function() {
        if (dropdown && dropdown.style.display === 'block') {
            positionAndShowDropdown();
        }
    });

    // Isara pag nag-click sa labas
    document.addEventListener('click', function(e) {
        if (!dropdown) return;
        if (bellBtn && !bellBtn.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });

    // ============ AI PANEL & SIDEBAR COORDINATION ============
    const aiFloatContainer = document.getElementById('aiFloatContainer');
    const aiChatPanel = document.getElementById('aiChatPanel');
    const sidebar = document.getElementById('sidebar');

    if (aiFloatContainer || aiChatPanel) {
        let wasAIPanelOpenBeforeSidebar = false;

        function hideAIBubble() {
            if (aiFloatContainer) { aiFloatContainer.classList.add('fab-hidden', 'hide-button'); }
        }
        function showAIBubble() {
            if (aiFloatContainer) { aiFloatContainer.classList.remove('fab-hidden', 'hide-button'); }
        }
        function hideAIPanel() {
            if (aiChatPanel && aiChatPanel.classList.contains('show')) {
                wasAIPanelOpenBeforeSidebar = true;
                aiChatPanel.classList.remove('show');
            } else {
                wasAIPanelOpenBeforeSidebar = false;
            }
        }
        function restoreAIPanel() {
            if (wasAIPanelOpenBeforeSidebar && aiChatPanel) {
                aiChatPanel.classList.add('show');
                hideAIBubble();
                wasAIPanelOpenBeforeSidebar = false;
            }
        }

        document.addEventListener('click', function(event) {
            if (!aiChatPanel || !aiChatPanel.classList.contains('show')) return;
            const isClickInsideAI = aiChatPanel.contains(event.target) ||
                                    (aiFloatContainer && aiFloatContainer.contains(event.target));
            if (!isClickInsideAI) {
                aiChatPanel.classList.remove('show');
                localStorage.setItem('aiPanelOpen', 'closed');
                showAIBubble();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && aiChatPanel && aiChatPanel.classList.contains('show')) {
                aiChatPanel.classList.remove('show');
                localStorage.setItem('aiPanelOpen', 'closed');
                showAIBubble();
            }
        });

        if (sidebar) {
            const aiObserver = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'class') {
                        if (sidebar.classList.contains('open')) {
                            hideAIBubble();
                            hideAIPanel();
                        } else {
                            restoreAIPanel();
                            if (!wasAIPanelOpenBeforeSidebar) showAIBubble();
                        }
                    }
                });
            });
            aiObserver.observe(sidebar, { attributes: true });
        }

        if (localStorage.getItem('aiPanelOpen') === 'open') {
            aiChatPanel.classList.add('show');
            hideAIBubble();
        } else {
            showAIBubble();
        }
    }

    // ============ SIDEBAR SWIPE GESTURE ============
    let touchStartX = 0, touchCurrentX = 0, isSwiping = false;

    const swipeOverlay = document.createElement('div');
    swipeOverlay.id = 'swipeOverlay';
    swipeOverlay.style.cssText = 'position:fixed;left:0;top:0;width:35px;height:100%;z-index:99999;background:transparent;pointer-events:auto;';
    document.body.appendChild(swipeOverlay);

    swipeOverlay.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        isSwiping = true;
        e.stopPropagation();
    }, { passive: false });

    swipeOverlay.addEventListener('touchmove', function(e) {
        if (!isSwiping) return;
        touchCurrentX = e.touches[0].clientX;
        if (touchCurrentX - touchStartX > 10) e.preventDefault();
        e.stopPropagation();
    }, { passive: false });

    swipeOverlay.addEventListener('touchend', function(e) {
        if (!isSwiping) { isSwiping = false; return; }
        const s = document.getElementById('sidebar');
        const m = document.getElementById('mainWrapper');
        const o = document.getElementById('overlay');
        if (touchCurrentX - touchStartX > 40 && s && !s.classList.contains('open')) {
            s.classList.add('open');
            if (m) m.classList.add('menu-open');
            if (o) o.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        isSwiping = false; touchStartX = 0; touchCurrentX = 0;
        e.stopPropagation();
    });

    document.addEventListener('touchstart', function(e) {
        const s = document.getElementById('sidebar');
        if (s && s.classList.contains('open') && !e.target.closest('.sidebar')) {
            touchStartX = e.touches[0].clientX;
            isSwiping = true;
        }
    });

    document.addEventListener('touchmove', function(e) {
        if (!isSwiping) return;
        const s = document.getElementById('sidebar');
        if (s && s.classList.contains('open')) {
            touchCurrentX = e.touches[0].clientX;
            if (touchStartX - touchCurrentX > 10) e.preventDefault();
        }
    }, { passive: false });

    document.addEventListener('touchend', function(e) {
        if (!isSwiping) { isSwiping = false; return; }
        const s = document.getElementById('sidebar');
        const m = document.getElementById('mainWrapper');
        const o = document.getElementById('overlay');
        if (s && s.classList.contains('open') && touchStartX - touchCurrentX > 40) {
            s.classList.remove('open');
            if (m) m.classList.remove('menu-open');
            if (o) o.classList.add('hidden');
            document.body.style.overflow = '';
        }
        isSwiping = false; touchStartX = 0; touchCurrentX = 0;
    });

    // ============ DOM CONTENT LOADED ============
    document.addEventListener('DOMContentLoaded', function() {
        initDarkMode();

        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebarEl = document.getElementById('sidebar');
        const mainWrapperEl = document.getElementById('mainWrapper');
        const overlayEl = document.getElementById('overlay');

        function toggleMobileMenu() {
            sidebarEl.classList.toggle('open');
            mainWrapperEl.classList.toggle('menu-open');
            if (overlayEl) {
                overlayEl.classList.toggle('hidden', !sidebarEl.classList.contains('open'));
            }
            document.body.style.overflow = sidebarEl.classList.contains('open') ? 'hidden' : '';
        }

        if (mobileBtn) mobileBtn.addEventListener('click', toggleMobileMenu);

        if (overlayEl) {
            overlayEl.addEventListener('click', function() {
                sidebarEl.classList.remove('open');
                mainWrapperEl.classList.remove('menu-open');
                overlayEl.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }

        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebarEl.classList.remove('open');
                    mainWrapperEl.classList.remove('menu-open');
                    if (overlayEl) overlayEl.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });

        const profileImg = document.getElementById('profilePicture');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const profileUpload = document.getElementById('profileUpload');
        const profileModal = document.getElementById('profileModal');

        if (profileImg) profileImg.addEventListener('click', window.openModalFunc);
        if (closeModalBtn) closeModalBtn.addEventListener('click', window.closeModalFunc);
        if (profileUpload) {
            profileUpload.addEventListener('change', (e) => {
                if (e.target.files && e.target.files[0]) window.handleImageUpload(e.target.files[0]);
            });
        }
        if (profileModal) {
            profileModal.addEventListener('click', (e) => {
                if (e.target === profileModal) window.closeModalFunc();
            });
        }

        checkForNewNotifications();
        notificationCheckInterval = setInterval(checkForNewNotifications, 60000);

        @if(session('success'))
            window.showToast('{{ session('success') }}', 'success');
        @endif
        @if(session('error'))
            window.showToast('{{ session('error') }}', 'error');
        @endif

        console.log('App initialized');
    });

    window.addEventListener('beforeunload', function() {
        if (notificationCheckInterval) clearInterval(notificationCheckInterval);
    });
    </script>
    @stack('scripts')
</body>
</html>