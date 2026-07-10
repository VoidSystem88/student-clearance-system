<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff - Clearance System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        
        .sidebar-transition { transition: all 0.3s ease; }
        
        /* ============================================================ */
        /* DARK MODE TOGGLE SWITCH */
        /* ============================================================ */
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
        body.dark .theme-switch {
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
        body.dark .switch-label {
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
        
        /* ============================================================ */
        /* DARK MODE STYLES - GAMIT ANG 'dark' CLASS */
        /* ============================================================ */
        body.dark {
            background-color: #111827 !important;
        }
        
        body.dark .bg-white {
            background-color: #1f2937 !important;
        }
        body.dark .bg-gray-50,
        body.dark .bg-gray-100 {
            background-color: #1f2937 !important;
        }
        body.dark .text-gray-500,
        body.dark .text-gray-600,
        body.dark .text-gray-700,
        body.dark .text-gray-800,
        body.dark .text-gray-900 {
            color: #e5e7eb !important;
        }
        body.dark .text-gray-400 {
            color: #9ca3af !important;
        }
        body.dark .border-gray-100,
        body.dark .border-gray-200,
        body.dark .border-gray-300 {
            border-color: #374151 !important;
        }
        body.dark .bg-yellow-50 {
            background-color: #713f12 !important;
        }
        body.dark .bg-yellow-50 .text-yellow-800,
        body.dark .bg-yellow-50 .text-yellow-700,
        body.dark .bg-yellow-50 .text-yellow-600 {
            color: #fde047 !important;
        }
        body.dark .bg-blue-50 {
            background-color: #1e3a5f !important;
        }
        body.dark .bg-green-50 {
            background-color: #064e3b !important;
        }
        body.dark .bg-red-50 {
            background-color: #7f1d1d !important;
        }
        body.dark .bg-purple-50 {
            background-color: #4c1d95 !important;
        }
        body.dark table thead.bg-gray-50 th {
            background-color: #374151 !important;
            color: #e5e7eb !important;
        }
        body.dark table tbody tr {
            border-bottom-color: #374151 !important;
        }
        body.dark table tbody tr:hover {
            background-color: #374151 !important;
        }
        body.dark .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
        }
        body.dark .border-b {
            border-bottom-color: #374151 !important;
        }
        body.dark .text-green-600 {
            color: #4ade80 !important;
        }
        body.dark .text-red-600 {
            color: #f87171 !important;
        }
        body.dark .text-yellow-600 {
            color: #fbbf24 !important;
        }
        body.dark .text-blue-600 {
            color: #60a5fa !important;
        }
        body.dark .text-purple-600 {
            color: #a78bfa !important;
        }
        body.dark main {
            background-color: #111827 !important;
        }
        body.dark .min-h-screen {
            background-color: #111827 !important;
        }
        body.dark table tbody td {
            color: #e5e7eb !important;
        }
        body.dark table thead th {
            color: #9ca3af !important;
            background-color: #1f2937 !important;
        }
        body.dark .font-semibold {
            color: #e5e7eb !important;
        }
        body.dark .font-mono {
            color: #e5e7eb !important;
        }
        body.dark .divide-y > div,
        body.dark .divide-y > tr {
            border-bottom-color: #374151 !important;
        }
        
        /* Sidebar */
        body.dark .sidebar {
            background-color: #1f2937 !important;
        }
        body.dark .sidebar .border-b,
        body.dark .sidebar .border-t {
            border-color: #374151 !important;
        }
        body.dark .sidebar .text-gray-800,
        body.dark .sidebar .text-gray-700,
        body.dark .sidebar .text-gray-500 {
            color: #e5e7eb !important;
        }
        body.dark .nav-link {
            color: #e5e7eb !important;
        }
        body.dark .nav-link:hover {
            background-color: #374151 !important;
        }
        body.dark .nav-active {
            background-color: #1e3a5f !important;
            color: #60a5fa !important;
        }
        
        /* Top bar */
        body.dark .bg-white.shadow-sm {
            background-color: #1f2937 !important;
        }
        body.dark .text-gray-600,
        body.dark .text-gray-700 {
            color: #e5e7eb !important;
        }
        
        /* Notification dropdown */
        body.dark .notification-dropdown {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }
        body.dark .notification-dropdown .bg-gray-100 {
            background-color: #1f2937 !important;
        }
        body.dark .notification-dropdown .text-gray-800 {
            color: #e5e7eb !important;
        }
        body.dark .notification-dropdown .text-gray-600 {
            color: #9ca3af !important;
        }
        body.dark .notification-dropdown .border-gray-100,
        body.dark .notification-dropdown .border-gray-200 {
            border-color: #374151 !important;
        }
        body.dark .notification-dropdown .hover\:bg-gray-50:hover {
            background-color: #374151 !important;
        }
        
        /* Animation */
        body {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        
        /* Notification Dropdown */
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 380px;
            max-height: 500px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2);
            z-index: 1000;
            overflow-y: auto;
            display: none;
        }
        .notification-dropdown.show {
            display: block;
        }
        
        @media (max-width: 768px) {
            .sidebar { 
                transform: translateX(-100%); 
                position: fixed; 
                z-index: 50; 
            }
            .sidebar.open { 
                transform: translateX(0); 
            }
            .notification-dropdown {
                width: calc(100vw - 40px);
                right: 20px;
                left: auto;
            }
            .float-dark-mode .theme-switch {
                padding: 8px 16px;
            }
            .float-dark-mode .switch-label span {
                display: none;
            }
        }
        
        .nav-active { 
            background-color: #e0e7ff; 
            color: #1e40af; 
            border-left: 4px solid #3b82f6; 
        }
        body.dark .nav-active {
            background-color: #1e3a5f !important;
            color: #60a5fa !important;
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
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-lg text-gray-800">Staff Portal</h1>
                    <p class="text-xs text-gray-500">{{ $department->name ?? 'Department' }}</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-4 overflow-y-auto">
            <a href="{{ route('staff.dashboard') }}" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
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
            <p>Staff Portal</p>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

    <!-- Main Content -->
    <main class="md:ml-64 min-h-screen">
        <!-- Top Bar -->
        <div class="bg-white shadow-sm sticky top-0 z-20">
            <div class="px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="md:hidden w-10"></div>
                    <h2 class="text-lg font-semibold text-gray-700">@yield('header', 'Staff Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    <!-- NOTIFICATION BELL -->
                    @php
                        $user = Auth::user();
                        $department = \App\Models\Department::find(session('staff_department_id'));
                        
                        $today = date('Y-m-d');
                        $reminders = collect();
                        $announcements = collect();
                        $totalNotifications = 0;
                        
                        if ($user) {
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
                                
                            $announcements = \App\Models\Announcement::where('is_active', true)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
                                
                            $totalNotifications = $reminders->count() + $announcements->count();
                        }
                    @endphp
                    
                    <div class="relative notification-bell">
                        <i class="fas fa-bell text-gray-600 text-xl hover:text-blue-600 transition" id="notificationBell"></i>
                        @if($totalNotifications > 0)
                        <span class="notification-badge">{{ $totalNotifications }}</span>
                        @endif
                        
                        <!-- Notification Dropdown -->
                        <div id="notificationDropdown" class="notification-dropdown">
                            <div class="sticky top-0 bg-gray-100 px-4 py-2 border-b border-gray-200 rounded-t-lg">
                                <h3 class="font-semibold text-gray-800">Notifications</h3>
                            </div>
                            
                            @if($reminders->count() > 0)
                            <div class="border-b border-gray-200">
                                <div class="px-4 py-2 bg-yellow-50">
                                    <h4 class="text-sm font-semibold text-yellow-800">
                                        <i class="fas fa-bell mr-1"></i> Reminders
                                    </h4>
                                </div>
                                @foreach($reminders as $reminder)
                                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="flex items-start gap-2">
                                        <div class="text-yellow-500 mt-1">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ $reminder->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ $reminder->message }}</p>
                                            @if($reminder->end_date)
                                            <p class="text-xs text-gray-400 mt-1">
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
                                <div class="px-4 py-2 bg-blue-50">
                                    <h4 class="text-sm font-semibold text-blue-800">
                                        <i class="fas fa-megaphone mr-1"></i> Announcements
                                    </h4>
                                </div>
                                @foreach($announcements as $announcement)
                                <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition">
                                    <div class="flex items-start gap-2">
                                        <div class="text-blue-500 mt-1">
                                            <i class="fas fa-bullhorn"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-800">{{ $announcement->title }}</p>
                                            <p class="text-xs text-gray-600 mt-1">{{ Str::limit($announcement->content, 100) }}</p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-calendar-alt"></i> {{ $announcement->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            
                            @if($totalNotifications == 0)
                            <div class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-bell-slash text-3xl mb-2 text-gray-300"></i>
                                <p class="text-sm">No new notifications</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <span class="text-sm text-gray-600 hidden sm:block">{{ Auth::user()->email ?? 'Staff' }}</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 text-sm"></i>
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

    <!-- FLOATING DARK MODE TOGGLE - BOTTOM RIGHT -->
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
        // ============ SIDEBAR FUNCTIONS ============
        const mobileBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        if(mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('hidden');
                document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
            });
        }
        if(overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            });
        }
        
        document.querySelectorAll('.nav-link').forEach(link => {
            if(link.href === window.location.href) link.classList.add('nav-active');
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
            
            // ✅ Toggle 'dark' class (walang '-mode')
            body.classList.toggle('dark');
            
            if (body.classList.contains('dark')) {
                localStorage.setItem('staff_dark_mode', 'enabled');
                if (darkModeToggle) darkModeToggle.checked = true;
            } else {
                localStorage.setItem('staff_dark_mode', 'disabled');
                if (darkModeToggle) darkModeToggle.checked = false;
            }
            
            console.log('Dark mode toggled:', body.classList.contains('dark'));
        }

        // Kapag clinick ang theme-switch div
        document.querySelector('.theme-switch')?.addEventListener('click', (e) => {
            if (e.target.closest('.switch')) return;
            toggleDarkMode();
        });

        // Kapag clinick ang checkbox mismo
        if (darkModeToggle) {
            darkModeToggle.addEventListener('change', (e) => {
                e.stopPropagation();
                toggleDarkMode();
            });
        }

        // ✅ Load dark mode preference
        const darkModePref = localStorage.getItem('staff_dark_mode');
        console.log('Dark mode preference from localStorage:', darkModePref);
        
        if (darkModePref === 'enabled') {
            document.body.classList.add('dark');
            if (darkModeToggle) darkModeToggle.checked = true;
            console.log('Dark mode enabled on load');
        } else {
            document.body.classList.remove('dark');
            if (darkModeToggle) darkModeToggle.checked = false;
            console.log('Dark mode disabled on load');
        }
        
        console.log('Body classes on load:', document.body.className);
    </script>
    @stack('scripts')
</body>
</html>