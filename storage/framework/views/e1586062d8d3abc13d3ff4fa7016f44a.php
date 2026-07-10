<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Officer - Clearance System'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <!-- ✅ SWEETALERT2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* ============ BASE RESET ============ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            overflow-x: hidden !important;
            width: 100%;
        }
        
        /* ============ SCROLLBAR ============ */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        body.dark-mode ::-webkit-scrollbar-track { background: #1f2937; }
        body.dark-mode ::-webkit-scrollbar-thumb { background: #4b5563; }
        
        /* ============ BODY BACKGROUND ============ */
        body {
            background-color: #f3f4f6;
            transition: background-color 0.3s ease;
        }
        body.dark-mode {
            background-color: #111827 !important;
        }
        
        /* ============ APP WRAPPER ============ */
        .app-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-bottom: 80px;
        }
        
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px 20px 20px;
        }
        
        /* ============ TOP HEADER ============ */
        .top-header {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 20px;
            position: sticky;
            top: 0;
            z-index: 50;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s ease, border-color 0.3s ease;
        }
        body.dark-mode .top-header {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }
        .top-header .page-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            transition: color 0.3s ease;
        }
        body.dark-mode .top-header .page-title {
            color: #f9fafb !important;
        }
        
        /* ============ NOTIFICATION BELL ============ */
        .notif-btn {
            position: relative;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 20px;
            color: #6b7280;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .notif-btn:hover {
            background: #f3f4f6;
        }
        body.dark-mode .notif-btn {
            color: #e5e7eb !important;
        }
        body.dark-mode .notif-btn:hover {
            background: #374151 !important;
        }
        
        .notif-badge {
            position: absolute;
            top: 2px;
            right: 2px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid #ffffff;
            transition: transform 0.2s ease;
        }
        body.dark-mode .notif-badge {
            border-color: #1f2937 !important;
        }
        .notif-badge.pulse {
            animation: pulse-badge 1.5s infinite;
        }
        
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.15); }
        }
        
        /* ============ NOTIFICATION DROPDOWN ============ */
        .notif-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 340px;
            max-width: 90vw;
            max-height: 400px;
            overflow-y: auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid #e5e7eb;
            display: none;
            z-index: 1000;
            padding: 8px 0;
        }
        .notif-dropdown.open {
            display: block;
        }
        body.dark-mode .notif-dropdown {
            background: #1f2937 !important;
            border-color: #374151 !important;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4) !important;
        }
        
        .notif-dropdown .notif-header {
            padding: 12px 16px 8px;
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        body.dark-mode .notif-dropdown .notif-header {
            color: #f9fafb !important;
            border-color: #374151 !important;
        }
        .notif-dropdown .notif-header .mark-read {
            font-size: 12px;
            font-weight: 500;
            color: #2563eb;
            cursor: pointer;
            background: none;
            border: none;
        }
        body.dark-mode .notif-dropdown .notif-header .mark-read {
            color: #60a5fa !important;
        }
        .notif-dropdown .notif-header .mark-read:hover {
            text-decoration: underline;
        }
        
        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f9fafb;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .notif-item:hover {
            background: #f9fafb;
        }
        .notif-item.unread {
            background: #eff6ff;
            border-left: 3px solid #2563eb;
        }
        .notif-item.unread:hover {
            background: #dbeafe;
        }
        body.dark-mode .notif-item {
            border-color: #374151 !important;
        }
        body.dark-mode .notif-item:hover {
            background: #374151 !important;
        }
        body.dark-mode .notif-item.unread {
            background: #1e3a5f !important;
            border-left-color: #60a5fa !important;
        }
        body.dark-mode .notif-item.unread:hover {
            background: #2a4a7f !important;
        }
        
        .notif-item .notif-title {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }
        body.dark-mode .notif-item .notif-title {
            color: #f9fafb !important;
        }
        .notif-item .notif-message {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        body.dark-mode .notif-item .notif-message {
            color: #9ca3af !important;
        }
        .notif-item .notif-time {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 4px;
        }
        body.dark-mode .notif-item .notif-time {
            color: #6b7280 !important;
        }
        
        .notif-empty {
            padding: 30px 16px;
            text-align: center;
            color: #9ca3af;
            font-size: 14px;
        }
        body.dark-mode .notif-empty {
            color: #6b7280 !important;
        }
        .notif-empty i {
            font-size: 32px;
            display: block;
            margin-bottom: 8px;
            opacity: 0.5;
        }
        
        /* ============ DARK MODE TOGGLE BUTTON ============ */
        .dark-toggle {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            color: #6b7280;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .dark-toggle:hover {
            background: #f3f4f6;
        }
        body.dark-mode .dark-toggle {
            color: #e5e7eb !important;
        }
        body.dark-mode .dark-toggle:hover {
            background: #374151 !important;
        }
        
        /* ============ HEADER RIGHT GROUP ============ */
        .header-actions {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* ============ BOTTOM NAVIGATION ============ */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 8px 0 env(safe-area-inset-bottom, 8px);
            box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
            height: 70px;
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        body.dark-mode .bottom-nav {
            background: #1f2937 !important;
            border-color: #374151 !important;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.3) !important;
        }
        
        .bottom-nav .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            padding: 4px 16px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #9ca3af;
            text-decoration: none;
            position: relative;
            min-width: 56px;
            border-radius: 12px;
        }
        .bottom-nav .nav-item i {
            font-size: 20px;
            transition: all 0.3s ease;
        }
        .bottom-nav .nav-item span {
            font-size: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .bottom-nav .nav-item:hover {
            color: #6b7280;
        }
        .bottom-nav .nav-item.active {
            color: #2563eb;
        }
        .bottom-nav .nav-item.active i {
            transform: translateY(-2px);
        }
        .bottom-nav .nav-item.active::after {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 24px;
            height: 3px;
            background: #2563eb;
            border-radius: 0 0 4px 4px;
        }
        body.dark-mode .bottom-nav .nav-item {
            color: #6b7280 !important;
        }
        body.dark-mode .bottom-nav .nav-item.active {
            color: #60a5fa !important;
        }
        body.dark-mode .bottom-nav .nav-item.active::after {
            background: #60a5fa !important;
        }
        
        .bottom-nav .nav-item .badge {
            position: absolute;
            top: 0;
            right: 6px;
            background: #ef4444;
            color: white;
            font-size: 9px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        body.dark-mode .bottom-nav .nav-item .badge {
            background: #dc2626 !important;
        }
        
        /* ============ FORCE DARK MODE OVERRIDES ============ */
        body.dark-mode .bg-white {
            background-color: #1f2937 !important;
        }
        body.dark-mode .bg-gray-50 {
            background-color: #1f2937 !important;
        }
        body.dark-mode .bg-gray-100 {
            background-color: #374151 !important;
        }
        body.dark-mode .text-gray-800 {
            color: #e5e7eb !important;
        }
        body.dark-mode .text-gray-700 {
            color: #e5e7eb !important;
        }
        body.dark-mode .text-gray-600 {
            color: #9ca3af !important;
        }
        body.dark-mode .text-gray-500 {
            color: #9ca3af !important;
        }
        body.dark-mode .text-gray-400 {
            color: #6b7280 !important;
        }
        body.dark-mode .border-gray-200 {
            border-color: #374151 !important;
        }
        body.dark-mode .border-gray-300 {
            border-color: #4b5563 !important;
        }
        body.dark-mode .border-gray-100 {
            border-color: #374151 !important;
        }
        body.dark-mode .divide-gray-100 {
            border-color: #374151 !important;
        }
        body.dark-mode .divide-gray-200 {
            border-color: #374151 !important;
        }
        body.dark-mode .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important;
        }
        body.dark-mode .shadow {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.3) !important;
        }
        body.dark-mode .shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.4) !important;
        }
        body.dark-mode .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.4) !important;
        }
        body.dark-mode .shadow-xl {
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5) !important;
        }
        
        /* ============ MOBILE RESPONSIVE ============ */
        @media (max-width: 640px) {
            .bottom-nav .nav-item {
                padding: 4px 10px;
                min-width: 44px;
            }
            .bottom-nav .nav-item i {
                font-size: 18px;
            }
            .bottom-nav .nav-item span {
                font-size: 9px;
            }
            .top-header .page-title {
                font-size: 15px;
            }
            .main-content {
                padding: 12px 12px 20px;
            }
            .notif-dropdown {
                width: 300px;
                right: -60px;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>

<div class="app-wrapper">
    
    <!-- ============ TOP HEADER ============ -->
    <header class="top-header">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-tie text-white text-sm"></i>
            </div>
            <span class="page-title"><?php echo $__env->yieldContent('header', 'Officer Dashboard'); ?></span>
        </div>
        <div class="header-actions">
            <!-- ✅ NOTIFICATION BELL -->
            <div style="position: relative;">
                <button class="notif-btn" id="notifBell" onclick="toggleNotifDropdown()">
                    <i class="fas fa-bell"></i>
                    <span class="notif-badge pulse" id="notifBadge">0</span>
                </button>
                
                <!-- ✅ NOTIFICATION DROPDOWN -->
                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-header">
                        <span>Notifications</span>
                        <button class="mark-read" onclick="markAllAsRead()">Mark all as read</button>
                    </div>
                    <div id="notifList">
                        <!-- Dynamically loaded via JS or Blade -->
                        <div class="notif-empty">
                            <i class="fas fa-bell-slash"></i>
                            No notifications
                        </div>
                    </div>
                </div>
            </div>
            
            <button onclick="toggleDarkMode()" class="dark-toggle" id="darkModeToggleBtn">
                <i id="darkModeIcon" class="fas fa-moon"></i>
            </button>
        </div>
    </header>

    <!-- ============ MAIN CONTENT ============ -->
    <main class="main-content">
        <?php if(session('success')): ?>
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-3 rounded mb-4">
                <i class="fas fa-check-circle mr-2"></i><?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i><?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- ============ BOTTOM NAVIGATION ============ -->
    <nav class="bottom-nav" id="bottomNav">
        <a href="<?php echo e(route('officer.dashboard')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('officer.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>

        <a href="<?php echo e(route('officer.students')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('officer.students') ? 'active' : ''); ?>">
            <i class="fas fa-users"></i>
            <span>Students</span>
            <?php
                $pendingCount = isset($students) ? $students->count() - ($verifiedCount ?? 0) : 0;
            ?>
            <?php if($pendingCount > 0): ?>
                <span class="badge"><?php echo e($pendingCount); ?></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo e(route('officer.verified')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('officer.verified') ? 'active' : ''); ?>">
            <i class="fas fa-check-circle"></i>
            <span>Verified</span>
            <?php if(isset($verifiedCount) && $verifiedCount > 0): ?>
                <span class="badge" style="background: #22c55e;"><?php echo e($verifiedCount); ?></span>
            <?php endif; ?>
        </a>

        <a href="<?php echo e(route('officer.send.report')); ?>" 
           class="nav-item <?php echo e(request()->routeIs('officer.send.report') ? 'active' : ''); ?>">
            <i class="fas fa-paper-plane"></i>
            <span>Send</span>
        </a>
    </nav>
</div>

<script>
    // ============ DARK MODE ============
    function toggleDarkMode() {
        const body = document.body;
        const icon = document.getElementById('darkModeIcon');
        
        body.classList.toggle('dark-mode');
        
        if (body.classList.contains('dark-mode')) {
            localStorage.setItem('officer_dark_mode', 'enabled');
            if (icon) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            }
        } else {
            localStorage.setItem('officer_dark_mode', 'disabled');
            if (icon) {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        }
    }
    
    // Load saved dark mode preference
    const darkModePref = localStorage.getItem('officer_dark_mode');
    if (darkModePref === 'enabled') {
        document.body.classList.add('dark-mode');
        const icon = document.getElementById('darkModeIcon');
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    }

    // ============ NOTIFICATION BELL TOGGLE ============
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notifDropdown');
        dropdown.classList.toggle('open');
        
        // Close when clicking outside
        if (dropdown.classList.contains('open')) {
            document.addEventListener('click', closeNotifOutside);
        } else {
            document.removeEventListener('click', closeNotifOutside);
        }
    }

    function closeNotifOutside(e) {
        const container = document.querySelector('.header-actions');
        if (!container.contains(e.target)) {
            document.getElementById('notifDropdown').classList.remove('open');
            document.removeEventListener('click', closeNotifOutside);
        }
    }

    // ============ FETCH NOTIFICATIONS ============
    function fetchNotifications() {
        fetch('<?php echo e(route("officer.notifications")); ?>')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('notifList');
                const badge = document.getElementById('notifBadge');
                
                // Update badge
                const unreadCount = data.unread_count || 0;
                badge.textContent = unreadCount;
                if (unreadCount > 0) {
                    badge.style.display = 'flex';
                    badge.classList.add('pulse');
                } else {
                    badge.style.display = 'none';
                    badge.classList.remove('pulse');
                }
                
                // Update list
                if (data.notifications && data.notifications.length > 0) {
                    list.innerHTML = data.notifications.map(n => `
                        <div class="notif-item ${n.read_at ? '' : 'unread'}" onclick="markAsRead('${n.id}')">
                            <div class="notif-title">${n.title || 'Announcement'}</div>
                            <div class="notif-message">${n.message || ''}</div>
                            <div class="notif-time">${n.created_at || ''}</div>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = `
                        <div class="notif-empty">
                            <i class="fas fa-bell-slash"></i>
                            No notifications
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error('Error fetching notifications:', err);
            });
    }

    // ============ MARK SINGLE AS READ ============
    function markAsRead(id) {
        fetch('<?php echo e(route("officer.notification.mark-read")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        })
        .catch(err => console.error('Error:', err));
    }

    // ============ MARK ALL AS READ ============
    function markAllAsRead() {
        fetch('<?php echo e(route("officer.notification.mark-all-read")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        })
        .catch(err => console.error('Error:', err));
    }

    // ============ AUTO-FETCH ON PAGE LOAD ============
    document.addEventListener('DOMContentLoaded', function() {
        fetchNotifications();
        
        // Auto-refresh every 30 seconds
        setInterval(fetchNotifications, 30000);
    });
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/layouts/officer.blade.php ENDPATH**/ ?>