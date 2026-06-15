<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Officer - Clearance System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Pigilan ang horizontal scroll */
        html, body {
            overflow-x: hidden !important;
            width: 100%;
            position: relative;
        }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        
        /* Sidebar */
        .sidebar {
            background: #0f172a;
            color: #e2e8f0;
        }
        
        .sidebar-transition { transition: all 0.3s ease; }
        
        .nav-link {
            transition: all 0.2s;
        }
        .nav-link:hover {
            background-color: #1e293b;
        }
        .nav-active {
            background-color: #1e3a5f;
            color: #60a5fa;
            border-left: 4px solid #3b82f6;
        }
        
        /* Dark Mode Toggle Switch - FLOATING BOTTOM RIGHT */
        .float-dark-mode {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
        }
        .theme-switch {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            border-radius: 50px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            gap: 12px;
            border: 1px solid #e2e8f0;
        }
        body.dark-mode .theme-switch {
            background: #1e293b;
            border-color: #475569;
        }
        .theme-switch:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        .switch-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
        }
        body.dark-mode .switch-label {
            color: #e2e8f0;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #fbbf24;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        /* Animation for dark mode transition */
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        
        /* Notification Dropdown Styles - FIXED FOR MOBILE */
/* Notification Dropdown Styles */
.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 380px;
    max-width: 90vw;
    max-height: 500px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
    z-index: 1000;
    overflow-y: auto;
    display: none;
}
.notification-dropdown.show {
    display: block;
}

/* Light mode text colors - FORCE VISIBLE */
.notification-dropdown .notification-item {
    background: #ffffff;
    border-bottom: 1px solid #e5e7eb;
}
.notification-dropdown .notification-item p {
    color: #1f2937 !important;
}
.notification-dropdown .notification-item .text-gray-600,
.notification-dropdown .notification-item .text-gray-500,
.notification-dropdown .notification-item .text-gray-400 {
    color: #4b5563 !important;
}
.notification-dropdown .notification-item .font-semibold {
    color: #111827 !important;
}
.notification-dropdown .bg-yellow-50 {
    background-color: #fefce8 !important;
}
.notification-dropdown .bg-yellow-50 h4 {
    color: #854d0e !important;
}
.notification-dropdown h3 {
    color: #111827 !important;
}

/* Dark mode styles */
body.dark-mode .notification-dropdown {
    background: #1f2937;
}
body.dark-mode .notification-dropdown .notification-item {
    background: #1f2937;
    border-bottom-color: #374151;
}
body.dark-mode .notification-dropdown .notification-item p {
    color: #e5e7eb !important;
}
body.dark-mode .notification-dropdown .notification-item .text-gray-600,
body.dark-mode .notification-dropdown .notification-item .text-gray-500,
body.dark-mode .notification-dropdown .notification-item .text-gray-400 {
    color: #9ca3af !important;
}
body.dark-mode .notification-dropdown .notification-item .font-semibold {
    color: #ffffff !important;
}
body.dark-mode .notification-dropdown .bg-yellow-50 {
    background-color: #422006 !important;
}
body.dark-mode .notification-dropdown .bg-yellow-50 h4 {
    color: #fde047 !important;
}
body.dark-mode .notification-dropdown h3 {
    color: #ffffff !important;
}
body.dark-mode .notification-dropdown {
    color: #e5e7eb;
}
        .notification-dropdown.show {
            display: block;
        }
        body.dark-mode .notification-dropdown {
            background: #1f2937;
            color: #e5e7eb;
        }
        
        /* Mobile sidebar */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1000;
                width: 280px;
                height: 100%;
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.6);
                z-index: 999;
                display: none;
            }
            .sidebar-overlay.show {
                display: block;
            }
            .main-content {
                width: 100%;
                overflow-x: hidden;
            }
            
            /* Hamburger icon sa mobile - itim, transparent background */
            #mobileMenuBtn {
                background: transparent !important;
                color: #000000 !important;
                box-shadow: none !important;
            }
            #mobileMenuBtn i {
                color: #000000;
            }
            
            /* Floating dark mode sa mobile - adjust size */
            .theme-switch {
                padding: 8px 16px;
            }
            .switch-label span {
                display: none;
            }
            
            /* Notification dropdown mobile fix - centered */
            .notification-dropdown {
                position: fixed;
                top: 60px;
                right: 10px;
                left: auto;
                width: calc(100% - 20px);
                max-width: calc(100% - 20px);
            }
        }
        
        /* Desktop */
        @media (min-width: 769px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                width: 260px;
                display: flex;
                flex-direction: column;
            }
            .main-content {
                margin-left: 260px;
                overflow-x: hidden;
            }
            .sidebar {
                display: flex;
                flex-direction: column;
            }
            .sidebar-nav {
                flex: 1;
            }
            .sidebar-bottom {
                margin-top: auto;
                padding-bottom: 20px;
            }
            
            .notification-dropdown {
                width: 380px;
                right: 0;
                left: auto;
            }
        }
        
        /* Hamburger icon style - itim, transparent background, walang box shadow */
        #mobileMenuBtn {
            background: transparent !important;
            color: #000000 !important;
            box-shadow: none !important;
            border: none;
        }
        #mobileMenuBtn i {
            color: #000000;
            font-size: 24px;
        }
        
        .main-content {
            overflow-x: hidden;
            width: 100%;
        }
        
        /* Dark mode styles */
        body.dark-mode {
            background-color: #111827;
        }
        body.dark-mode .bg-white {
            background-color: #1f2937 !important;
        }
        body.dark-mode .bg-gray-50,
        body.dark-mode .bg-gray-100 {
            background-color: #1f2937 !important;
        }
        body.dark-mode .text-gray-800,
        body.dark-mode .text-gray-700,
        body.dark-mode .text-gray-600 {
            color: #e5e7eb !important;
        }
        body.dark-mode .text-gray-500 {
            color: #9ca3af !important;
        }
        body.dark-mode .border-gray-100,
        body.dark-mode .border-gray-200,
        body.dark-mode .border-gray-300 {
            border-color: #374151 !important;
        }
        body.dark-mode .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
        }
        body.dark-mode .bg-blue-50 {
            background-color: #1e3a5f !important;
        }
        body.dark-mode .bg-green-50 {
            background-color: #064e3b !important;
        }
        body.dark-mode .bg-yellow-50 {
            background-color: #713f12 !important;
        }
        body.dark-mode .bg-red-50 {
            background-color: #7f1d1d !important;
        }
        body.dark-mode .bg-purple-50 {
            background-color: #4c1d95 !important;
        }
        
        /* Dark mode notification */
        body.dark-mode .notification-dropdown {
            background: #1f2937;
            border-color: #374151;
        }
        body.dark-mode .notification-item {
            border-bottom-color: #374151;
        }
        body.dark-mode .notification-item:hover {
            background-color: #374151;
        }
        
        /* Dark mode hamburger icon */
        body.dark-mode #mobileMenuBtn {
            color: #ffffff !important;
        }
        body.dark-mode #mobileMenuBtn i {
            color: #ffffff;
        }
        
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: bold;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        
        /* Fix for dropdown content */
        .notification-dropdown .notification-item {
            word-break: break-word;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Sidebar Overlay for mobile -->
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

    <!-- Mobile Menu Toggle -->
    <button id="mobileMenuBtn" class="md:hidden fixed top-4 left-4 z-50 p-2 rounded-lg">
        <i class="fas fa-bars text-2xl"></i>
    </button>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar flex flex-col shadow-xl sidebar-transition">
        <!-- Header -->
        <div class="p-5 border-b border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-tie text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-white">Officer Portal</h1>
                    <p class="text-xs text-slate-400">Verified Students Manager</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav py-4 overflow-y-auto">
            <a href="{{ route('officer.dashboard') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-slate-300 hover:bg-slate-800 transition">
                <i class="fas fa-check-double w-5"></i>
                <span>Verified Students</span>
            </a>
        </nav>

        <!-- Bottom Section: Logout -->
        <div class="sidebar-bottom mt-auto">
            <form method="POST" action="{{ route('logout') }}" class="px-4">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-red-900/30 transition w-full text-left rounded-lg">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content min-h-screen">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm sticky top-0 z-20 transition-colors">
            <div class="px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="md:hidden w-10"></div>
                    <h2 class="text-lg font-semibold text-gray-800">@yield('header', 'Officer Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <!-- NOTIFICATION BELL -->
@php
    $user = Auth::user();
    $today = date('Y-m-d');
    
    // Kunin ang mga reminders para sa officer
    $reminders = \App\Models\Reminder::where('is_active', true)
        ->where('start_date', '<=', $today)
        ->where(function($q) use ($today) {
            $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
        })
        ->where(function($q) use ($user) {
            $q->where('target_role', $user->role)
              ->orWhere('target_role', 'both');
        })
        ->where(function($q) use ($user) {
            $q->whereNull('department_id')
              ->orWhere('department_id', $user->department_id);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    
    $announcements = collect();
    $totalNotifications = $reminders->count() + $announcements->count();
@endphp

<div class="relative notification-bell">
    <i class="fas fa-bell text-gray-600 text-xl hover:text-blue-600 transition cursor-pointer" id="notificationBell"></i>
    @if($totalNotifications > 0)
    <span class="notification-badge">{{ $totalNotifications }}</span>
    @endif
    
    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="notification-dropdown">
        <div class="sticky top-0 bg-gray-100 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 rounded-t-lg">
            <h3 class="font-semibold text-gray-800 dark:text-white">Notifications</h3>
        </div>
        
        @if($reminders->count() > 0)
        <div class="border-b border-gray-200 dark:border-gray-700">
            <div class="px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20">
                <h4 class="text-sm font-semibold text-yellow-800 dark:text-yellow-400">
                    <i class="fas fa-bell mr-1"></i> Reminders
                </h4>
            </div>
            @foreach($reminders as $reminder)
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition notification-item">
                <div class="flex items-start gap-2">
                    <div class="text-yellow-500 mt-1">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $reminder->title }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $reminder->message }}</p>
                        @if($reminder->end_date)
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fas fa-calendar-alt"></i> Until: {{ \Carbon\Carbon::parse($reminder->end_date)->format('M d, Y') }}
                        </p>
                        @endif
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
                    
                    <span class="text-sm text-gray-600 hidden sm:block">{{ Auth::user()->name ?? 'Officer' }}</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-tie text-blue-600 text-sm"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
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

    <!-- FLOATING DARK MODE TOGGLE -->
    <div class="float-dark-mode">
        <div class="theme-switch" onclick="toggleDarkMode()">
            <div class="switch-label">
                <i class="fas fa-moon"></i>
                <span>Dark Mode</span>
            </div>
            <label class="switch">
                <input type="checkbox" id="darkModeToggle">
                <span class="slider"></span>
            </label>
        </div>
    </div>

    <script>
        // ============ SIDEBAR MOBILE FUNCTIONS ============
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        
        function openSidebar() {
            sidebar.classList.add('open');
            if (sidebarOverlay) sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
            document.body.style.top = `-${window.scrollY}px`;
            document.body.dataset.scrollY = window.scrollY;
        }
        
        function closeSidebar() {
            sidebar.classList.remove('open');
            if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            const scrollY = document.body.dataset.scrollY || 0;
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
            document.body.style.top = '';
            window.scrollTo(0, parseInt(scrollY));
            delete document.body.dataset.scrollY;
        }
        
        if (mobileBtn) {
            mobileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }
        
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }
        
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });
        
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768 && sidebar.classList.contains('open')) {
                closeSidebar();
            }
        });
        
        // ============ NOTIFICATION DROPDOWN ============
        const notificationBell = document.getElementById('notificationBell');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationBell) {
            notificationBell.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
            });
            
            document.addEventListener('click', function(e) {
                if (notificationBell && notificationDropdown && 
                    !notificationBell.contains(e.target) && 
                    !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.remove('show');
                }
            });
        }
        
        // ============ DARK MODE FUNCTION ============
        const darkModeToggle = document.getElementById('darkModeToggle');
        
        function toggleDarkMode() {
            const body = document.body;
            
            body.classList.toggle('dark-mode');
            
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('officer_dark_mode', 'enabled');
                if (darkModeToggle) darkModeToggle.checked = true;
            } else {
                localStorage.setItem('officer_dark_mode', 'disabled');
                if (darkModeToggle) darkModeToggle.checked = false;
            }
        }
        
        document.querySelector('.theme-switch')?.addEventListener('click', (e) => {
            if (e.target.closest('.switch')) return;
            toggleDarkMode();
        });
        
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', (e) => {
                e.stopPropagation();
                toggleDarkMode();
            });
        }
        
        const darkModePref = localStorage.getItem('officer_dark_mode');
        if (darkModePref === 'enabled') {
            document.body.classList.add('dark-mode');
            if (darkModeToggle) darkModeToggle.checked = true;
        }
        
        document.querySelectorAll('.nav-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('nav-active');
            }
        });
        
        window.addEventListener('load', function() {
            if (document.body.scrollWidth > window.innerWidth) {
                document.body.style.overflowX = 'hidden';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>