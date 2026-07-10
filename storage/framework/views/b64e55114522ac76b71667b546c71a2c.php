<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo $__env->yieldContent('title', 'Student Clearance System'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php if(auth()->guard()->check()): ?>
    <script>
    window.VoidUserData = {
        id: <?php echo e(Auth::id()); ?>,
        name: "<?php echo e(Auth::user()->first_name ?? Auth::user()->name ?? 'Student'); ?>",
        fullName: "<?php echo e(trim((Auth::user()->first_name ?? '') . ' ' . (Auth::user()->last_name ?? ''))); ?>",
        studentId: "<?php echo e(Auth::user()->student_id ?? 'N/A'); ?>",
        accountId: "<?php echo e(Auth::user()->account_id ?? 'N/A'); ?>",
        email: "<?php echo e(Auth::user()->email ?? ''); ?>",
        course: "<?php echo e(Auth::user()->course ?? 'N/A'); ?>",
        yearLevel: "<?php echo e(Auth::user()->year_level ?? 'N/A'); ?>",
        courseYear: "<?php echo e(Auth::user()->course_year ?? (Auth::user()->course . ' - ' . Auth::user()->year_level)); ?>",
        clearedCount: <?php echo e($approvedCount ?? 0); ?>,
        totalDepartments: <?php echo e($totalDepartments ?? 0); ?>,
        isFullyCleared: <?php echo e(isset($isFullyCleared) && $isFullyCleared ? 'true' : 'false'); ?>,
        pendingDepartments: <?php echo json_encode($pendingDepartments ?? [], 15, 512) ?>,
        isNewUser: <?php echo e(isset($isNewUser) && $isNewUser ? 'true' : 'false'); ?>,
        createdAt: "<?php echo e(Auth::user()->created_at ?? ''); ?>",
        role: "<?php echo e(Auth::user()->role ?? 'student'); ?>"
    };
    </script>
    <?php endif; ?>

    <style>
        :root {
            --bg-body: #f3f4f6; --bg-primary: #ffffff; --bg-secondary: #f9fafb; --bg-tertiary: #f3f4f6;
            --text-primary: #1f2937; --text-secondary: #6b7280; --text-tertiary: #9ca3af;
            --border-color: #e5e7eb; --sidebar-bg: #ffffff; --card-bg: #ffffff;
            --input-bg: #ffffff; --input-border: #e5e7eb;
        }
        body.dark {
            --bg-body: #111827; --bg-primary: #1f2937; --bg-secondary: #374151; --bg-tertiary: #111827;
            --text-primary: #f9fafb; --text-secondary: #9ca3af; --text-tertiary: #6b7280;
            --border-color: #374151; --sidebar-bg: #1f2937; --card-bg: #1f2937;
            --input-bg: #374151; --input-border: #4b5563;
        }
        body { background-color: var(--bg-body); color: var(--text-primary); transition: background-color 0.3s ease, color 0.3s ease; background-image: url('/images/vlight.png'); background-size: cover; background-position: center; background-attachment: fixed; background-repeat: no-repeat; position: relative; }
        body.dark { background-image: url('/images/vdark.png'); }
        .bg-white { background-color: var(--bg-primary) !important; }
        .bg-gray-50 { background-color: var(--bg-secondary) !important; }
        .bg-gray-100 { background-color: var(--bg-tertiary) !important; }
        .text-gray-700, .text-gray-800, .text-gray-900 { color: var(--text-primary) !important; }
        .text-gray-500, .text-gray-600 { color: var(--text-secondary) !important; }
        .text-gray-400 { color: var(--text-tertiary) !important; }
        .border-gray-100, .border-gray-200, .border-gray-300 { border-color: var(--border-color) !important; }
        .sidebar { background-color: var(--sidebar-bg) !important; display: flex; flex-direction: column; height: 100vh; }
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 1rem 0; }
        .sidebar-footer { margin-top: auto; border-top: 1px solid var(--border-color); }
        .nav-link { color: var(--text-primary); display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.25rem; transition: all 0.2s; }
        .nav-link:hover { background-color: rgba(59, 130, 246, 0.1); }
        .nav-active { background-color: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        .app-container { display: flex; min-height: 100vh; overflow-x: hidden; }
        .sidebar { width: 280px; box-shadow: 2px 0 10px rgba(0,0,0,0.1); position: fixed; top: 0; left: 0; z-index: 40; transform: translateX(-100%); transition: transform 0.3s ease-in-out; }
        .sidebar.open { transform: translateX(0); }
        .main-wrapper { flex: 1; transition: transform 0.3s ease-in-out; width: 100%; }
        @media (min-width: 768px) { .sidebar { transform: translateX(0) !important; position: relative; } }
        @media (max-width: 767px) { .main-wrapper.menu-open { transform: translateX(280px); } }
        .dark-mode-sidebar-btn { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 12px 20px; background: transparent; border: none; cursor: pointer; transition: all 0.3s ease; border-radius: 12px; }
        .dark-mode-sidebar-btn:hover { background: rgba(59, 130, 246, 0.1); }
        .dark-mode-content { display: flex; align-items: center; gap: 12px; }
        .dark-mode-icon { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); transition: all 0.3s ease; }
        body.dark .dark-mode-icon { background: linear-gradient(135deg, #1e293b, #0f172a); }
        .dark-mode-icon i { font-size: 16px; color: white; transition: all 0.3s ease; }
        .dark-mode-text { font-size: 14px; font-weight: 500; color: var(--text-primary); }
        .dark-mode-toggle-switch { position: relative; width: 48px; height: 24px; background: #cbd5e1; border-radius: 30px; transition: all 0.3s ease; }
        body.dark .dark-mode-toggle-switch { background: #3b82f6; }
        .dark-mode-toggle-switch::after { content: ''; position: absolute; width: 20px; height: 20px; background: white; border-radius: 50%; top: 2px; left: 3px; transition: all 0.3s ease; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        body.dark .dark-mode-toggle-switch::after { left: 25px; }
        .toast { position: fixed; bottom: 20px; right: 20px; z-index: 9999; min-width: 200px; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        .toast-success { background: #22c55e; } .toast-error { background: #ef4444; } .toast-info { background: #3b82f6; }
        input, select, textarea { background-color: var(--input-bg); color: var(--text-primary); border-color: var(--input-border); }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }
        .profile-img { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid #3b82f6; cursor: pointer; transition: transform 0.2s; }
        .profile-img:hover { transform: scale(1.05); }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center; }
        .modal.active { display: flex; }
        .modal-content { background: var(--bg-primary); border-radius: 16px; padding: 24px; max-width: 500px; width: 95%; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); }
        .fab-hidden, .hide-button { opacity: 0 !important; visibility: hidden !important; pointer-events: none !important; }
        .assistance-link { background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1)); border-left: 3px solid #3b82f6; margin: 0.5rem 0; }
        body.dark .assistance-link { background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2)); }
        .copyright { padding: 1rem; text-align: center; font-size: 0.75rem; color: var(--text-secondary); border-top: 1px solid var(--border-color); }
        .notification-bell { position: relative; cursor: pointer; transition: all 0.2s ease; }
        .notification-bell:hover { transform: scale(1.05); }
        .notification-badge { position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; font-size: 0.65rem; font-weight: bold; padding: 2px 6px; border-radius: 20px; min-width: 18px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
        #notificationDropdown { position: fixed; z-index: 99999; background: var(--bg-primary); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25); border: 1px solid var(--border-color); overflow: hidden; display: none; }
        .notification-header { padding: 12px 16px; border-bottom: 1px solid var(--border-color); background: var(--bg-secondary); }
        .notification-header h4 { font-size: 0.875rem; font-weight: 600; }
        .notification-list { max-height: 350px; overflow-y: auto; }
        .notification-item { padding: 12px 16px; border-bottom: 1px solid var(--border-color); transition: background 0.2s ease; cursor: pointer; display: block; }
        .notification-item:hover { background: var(--bg-secondary); }
        .notification-item.unread { background: rgba(59, 130, 246, 0.08); }
        .notification-title { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; word-break: break-word; }
        .notification-message { font-size: 0.7rem; color: var(--text-secondary); margin-bottom: 4px; word-break: break-word; }
        .notification-time { font-size: 0.65rem; color: var(--text-secondary); opacity: 0.7; }
        .notification-footer { padding: 10px 16px; border-top: 1px solid var(--border-color); text-align: center; background: var(--bg-secondary); }
        .notification-footer a { font-size: 0.75rem; color: #3b82f6; text-decoration: none; }
        .empty-notification { padding: 40px 20px; text-align: center; color: var(--text-secondary); font-size: 0.8rem; }
        .empty-notification i { font-size: 2rem; margin-bottom: 8px; display: block; opacity: 0.5; }
        #mobileMenuBtn { cursor: pointer; -webkit-tap-highlight-color: transparent; touch-action: manipulation; user-select: none; -webkit-user-select: none; position: relative; z-index: 50; }
        #mobileMenuBtn i { pointer-events: none; }
        .cropper-view-box, .cropper-face { border-radius: 50% !important; }
        .cropper-view-box { box-shadow: 0 0 0 1px #3b82f6 !important; outline: 0 !important; }
        .cropper-face { background-color: transparent !important; }
        .cropper-line, .cropper-point { display: none !important; }
        #offlineBar { position: fixed; top: 0; left: 0; right: 0; z-index: 99999; background: #ef4444; color: white; text-align: center; padding: 6px 16px; font-size: 13px; font-weight: 500; transform: translateY(-100%); transition: transform 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; }
        #offlineBar.show { transform: translateY(0); }
        body.offline .app-container { padding-top: 36px; }
        body.offline .needs-connection { opacity: 0.5; pointer-events: none; cursor: not-allowed; }
        .online-toast { position: fixed; top: 50px; left: 50%; transform: translateX(-50%); z-index: 99999; background: #22c55e; color: white; padding: 8px 20px; border-radius: 20px; font-size: 13px; font-weight: 500; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { transform: translateX(-50%) translateY(-20px); opacity: 0; } to { transform: translateX(-50%) translateY(0); opacity: 1; } }
        #connectionIndicator { white-space: nowrap; }
        @media (max-width: 480px) { #connectionIndicator span { display: none; } }
            @keyframes popIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes popOut {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(20px); opacity: 0; }
}
            @media (max-width: 640px) {
    #aiPopup {
        right: 10px !important;
        left: 10px !important;
        max-width: 100% !important;
        bottom: 90px !important;
    }
}
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div id="offlineBar"><i class="fas fa-wifi-slash"></i> No internet connection. Some features unavailable.</div>
<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>
<div id="toastContainer"></div>

<div id="profileModal" class="modal">
    <div class="modal-content" style="max-width: 500px; width: 95%;">
        <div class="text-center mb-4"><i class="fas fa-camera text-4xl text-blue-500 mb-2"></i><h3 class="text-xl font-bold">Palitan ang Profile Picture</h3><p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pumili ng larawan at i-crop</p></div>
        <div id="uploadStep"><div class="mb-4"><label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Pumili ng larawan</label><input type="file" id="profileUpload" accept="image/jpeg,image/png,image/jpg" class="w-full p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white"><p class="text-xs text-gray-400 mt-1">Max 5MB, JPG/PNG only</p></div></div>
        <div id="cropStep" class="hidden"><div class="mb-4"><div class="relative bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden" style="min-height: 300px;"><img id="cropImage" src="" alt="Crop Image" style="max-width: 100%; display: block;"></div><div class="flex flex-wrap justify-between items-center gap-2 mt-2"><span class="text-xs text-gray-500 dark:text-gray-400">I-drag ang crop box para i-adjust</span><div class="flex gap-2"><button type="button" id="cropRotateLeftBtn" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-lg text-xs hover:bg-gray-300 dark:hover:bg-gray-500 transition"><i class="fas fa-undo"></i></button><button type="button" id="cropRotateRightBtn" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-lg text-xs hover:bg-gray-300 dark:hover:bg-gray-500 transition"><i class="fas fa-redo"></i></button><button type="button" id="cropResetBtn" class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-lg text-xs hover:bg-gray-300 dark:hover:bg-gray-500 transition"><i class="fas fa-undo-alt"></i> Reset</button></div></div></div></div>
        <div class="flex gap-2 mt-4"><button id="closeModalBtn" class="flex-1 bg-gray-300 dark:bg-gray-600 py-2 rounded-lg hover:bg-gray-400 dark:hover:bg-gray-500 transition text-gray-800 dark:text-white">Kanselahin</button><button id="cropConfirmBtn" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition hidden"><i class="fas fa-crop-alt mr-2"></i> I-crop at I-upload</button></div>
    </div>
</div>

<div class="app-container">
    <aside id="sidebar" class="sidebar">
        <div class="p-5 border-b border-gray-200 dark:border-gray-700"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center"><i class="fas fa-graduation-cap text-white text-xl"></i></div><div><h1 class="font-bold text-lg text-gray-800 dark:text-white">Clearance System</h1><p class="text-xs text-gray-500 dark:text-gray-400">Void - Student Portal</p></div></div></div>
        <div class="sidebar-nav">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('student.dashboard')); ?>" class="nav-link"><i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span></a>
                <a href="<?php echo e(route('student.clearance')); ?>" class="nav-link"><i class="fas fa-clipboard-list w-5"></i><span>Clearance</span></a>
                <a href="<?php echo e(route('student.reminders')); ?>" class="nav-link"><i class="fas fa-bell w-5"></i><span>Reminders</span></a>
                <a href="<?php echo e(route('student.profile')); ?>" class="nav-link"><i class="fas fa-user-circle w-5"></i><span>My Profile</span></a>
                <a href="<?php echo e(route('student.feedback')); ?>" class="nav-link"><i class="fas fa-star w-5"></i><span>Feedback</span></a>
                <div class="border-t my-3 mx-5 border-gray-200 dark:border-gray-700"></div>
                <form method="POST" action="<?php echo e(route('logout')); ?>"><?php echo csrf_field(); ?><button type="submit" class="nav-link text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 w-full text-left"><i class="fas fa-sign-out-alt w-5"></i><span>Logout</span></button></form>
            <?php endif; ?>
        </div>
        <div class="sidebar-footer">
            <button id="darkModeSidebarBtn" class="dark-mode-sidebar-btn"><div class="dark-mode-content"><div class="dark-mode-icon"><i id="sidebarDarkModeIcon" class="fas fa-moon"></i></div><span class="dark-mode-text">Dark Mode</span></div><div class="dark-mode-toggle-switch"></div></button>
            <a href="<?php echo e(route('student.assistance')); ?>" class="nav-link assistance-link"><i class="fas fa-headset w-5"></i><span>Request Assistance</span></a>
            <div class="copyright"><p>© <?php echo e(date('Y')); ?> Clearance System</p><p>v1.0 | Void</p></div>
        </div>
    </aside>

    <div id="mainWrapper" class="main-wrapper">
        <div class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-20">
            <div class="px-4 py-3 flex justify-between items-center">
    <!-- Left: Hamburger + Title -->
    <div class="flex items-center gap-1">
        <button id="mobileMenuBtn" class="md:hidden bg-transparent p-3 -ml-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition min-w-[48px] min-h-[48px] flex items-center justify-center" style="z-index:50;">
            <i id="hamburgerIcon" class="fas fa-bars text-2xl" style="color: var(--text-primary); pointer-events:none;"></i>
        </button>
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white truncate"><?php echo $__env->yieldContent('header', 'Dashboard'); ?></h2>
    </div>
    
    <!-- Right: Notification bell + Profile -->
    <div class="flex items-center gap-2 sm:gap-4 flex-shrink-0">
        <div class="notification-bell" id="notificationBell">
            <i class="fas fa-bell text-xl text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors cursor-pointer"></i>
            <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
        </div>
        <span class="text-sm hidden sm:block text-gray-600 dark:text-gray-400"><?php if(auth()->guard()->check()): ?> <?php echo e(Auth::user()->first_name ?? ''); ?> <?php endif; ?></span>
        <div style="position: relative; display: inline-block; flex-shrink: 0;">
            <img id="profilePicture" class="profile-img cursor-pointer" src="<?php echo e(Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode(Auth::user()->first_name ?? 'User') . '&size=64'); ?>" alt="Profile" data-user-id="<?php echo e(Auth::id()); ?>">
            <span id="profileOnlineDot" style="position: absolute; bottom: 0; right: 0; width: 12px; height: 12px; background: #22c55e; border-radius: 50%; border: 2px solid var(--bg-primary, #ffffff); transition: background 0.3s ease;"></span>
        </div>
    </div>
</div>
        </div>
        <div class="p-4 md:p-6"><?php echo $__env->yieldContent('content'); ?></div>
    </div>
</div>

<div id="notificationDropdown"><div class="notification-header"><h4><i class="fas fa-bell mr-2"></i> Notifications</h4></div><div id="notificationList" class="notification-list"><div class="empty-notification"><i class="fas fa-bell-slash"></i><p>Loading notifications...</p></div></div><div class="notification-footer"><a href="<?php echo e(route('student.reminders')); ?>">View all announcements →</a></div></div>

<?php echo $__env->make('components.ai-assistant', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const csrfTokenGlobal = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

window.showToast = function(m, t = 'success') {
    const c = document.getElementById('toastContainer'); if (!c) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${t} text-white px-4 py-2 rounded-lg shadow-lg`;
    toast.innerHTML = `<i class="fas ${t === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>${m}`;
    c.appendChild(toast); setTimeout(() => toast.remove(), 3000);
};

let cropper = null, selectedFile = null;
const profileUpload = document.getElementById('profileUpload');
const cropConfirmBtn = document.getElementById('cropConfirmBtn');

window.openModalFunc = function() {
    const modal = document.getElementById('profileModal');
    document.getElementById('uploadStep').classList.remove('hidden');
    document.getElementById('cropStep').classList.add('hidden');
    cropConfirmBtn.classList.add('hidden');
    if (profileUpload) profileUpload.value = '';
    if (cropper) { cropper.destroy(); cropper = null; }
    if (modal) modal.classList.add('active');
};

window.closeModalFunc = function() {
    document.getElementById('uploadStep').classList.remove('hidden');
    document.getElementById('cropStep').classList.add('hidden');
    if (cropper) { cropper.destroy(); cropper = null; }
    if (profileUpload) profileUpload.value = '';
    document.getElementById('profileModal').classList.remove('active');
};

if (profileUpload) {
    profileUpload.addEventListener('change', function(e) {
        const file = e.target.files[0]; if (!file) return;
        if (!['image/jpeg','image/png','image/jpg'].includes(file.type)) { window.showToast('JPG or PNG only','error'); this.value=''; return; }
        if (file.size > 5*1024*1024) { window.showToast('Max 5MB only','error'); this.value=''; return; }
        selectedFile = file;
        document.getElementById('uploadStep').classList.add('hidden');
        document.getElementById('cropStep').classList.remove('hidden');
        cropConfirmBtn.classList.remove('hidden');
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('cropImage').src = ev.target.result;
            document.getElementById('cropImage').onload = function() {
                if (cropper) cropper.destroy();
                cropper = new Cropper(document.getElementById('cropImage'), { aspectRatio: 1, viewMode: 1, dragMode: 'move', autoCropArea: 0.8, restore: false, guides: true, center: true, highlight: false, cropBoxMovable: true, cropBoxResizable: true, toggleDragModeOnDblclick: false, responsive: true });
            };
        };
        reader.readAsDataURL(file);
    });
}

if (cropConfirmBtn) {
    cropConfirmBtn.addEventListener('click', function() {
        if (!cropper || !selectedFile) return;
        const canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
        canvas.toBlob(function(blob) {
            if (!blob) { window.showToast('Error cropping image','error'); return; }
            const fd = new FormData(); fd.append('photo', blob, selectedFile.name);
            uploadCroppedImage(fd);
        }, 'image/jpeg', 0.9);
    });
}

document.getElementById('cropRotateLeftBtn')?.addEventListener('click', () => { if (cropper) cropper.rotate(-90); });
document.getElementById('cropRotateRightBtn')?.addEventListener('click', () => { if (cropper) cropper.rotate(90); });
document.getElementById('cropResetBtn')?.addEventListener('click', () => { if (cropper) cropper.reset(); });

async function uploadCroppedImage(fd) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    try {
        window.showToast('Uploading...','info');
        cropConfirmBtn.disabled = true;
        cropConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';
        const res = await fetch('/upload-profile-photo', { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin', body: fd });
        const data = await res.json();
        if (res.ok && data.success) {
            const img = document.getElementById('profilePicture');
            if (img) img.src = data.photo_url + '?t=' + Date.now();
            window.showToast(data.message || 'Updated!','success');
            window.closeModalFunc();
            if (navigator.onLine) setTimeout(() => location.reload(), 1500);
        } else { window.showToast(data.message || 'Upload failed','error'); }
    } catch (e) { window.showToast('Upload failed.','error'); }
    finally { cropConfirmBtn.disabled = false; cropConfirmBtn.innerHTML = '<i class="fas fa-crop-alt mr-2"></i> I-crop at I-upload'; }
}

function initDarkMode() {
    const btn = document.getElementById('darkModeSidebarBtn'), icon = document.getElementById('sidebarDarkModeIcon');
    const saved = localStorage.getItem('theme');
    if (saved === 'dark') { document.body.classList.add('dark'); if (icon) { icon.classList.remove('fa-moon'); icon.classList.add('fa-sun'); } }
    else { document.body.classList.remove('dark'); if (icon) { icon.classList.remove('fa-sun'); icon.classList.add('fa-moon'); } }
    if (btn) btn.addEventListener('click', () => { document.body.classList.toggle('dark'); const isDark = document.body.classList.contains('dark'); localStorage.setItem('theme', isDark ? 'dark' : 'light'); if (icon) { icon.classList.toggle('fa-moon', !isDark); icon.classList.toggle('fa-sun', isDark); } });
}

let notifInterval = null, lastCheck = localStorage.getItem('lastNotificationCheck') || Date.now();
async function checkNotifs() {
    try {
        const res = await fetch('/student/notifications/check', { method: 'GET', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content } });
        const d = await res.json();
        if (d.success) {
            const badge = document.getElementById('notificationBadge');
            if (badge) { if (d.new_count > 0) { badge.textContent = d.new_count > 99 ? '99+' : d.new_count; badge.style.display = 'inline-block'; } else badge.style.display = 'none'; }
            lastCheck = d.current_time; localStorage.setItem('lastNotificationCheck', lastCheck);
        }
    } catch (e) {}
}
function updateNotifList(notifs) {
    const c = document.getElementById('notificationList'); if (!c) return;
    if (!notifs || !notifs.length) { c.innerHTML = '<div class="empty-notification"><i class="fas fa-bell-slash"></i><p>No new notifications</p></div>'; return; }
    c.innerHTML = notifs.map(n => `<div class="notification-item ${n.created_at > parseInt(lastCheck) ? 'unread' : ''}" onclick="window.location.href='${n.link}'"><div class="notification-title"><i class="fas ${n.icon} mr-1"></i> ${escapeHtml(n.title)}</div><div class="notification-message">${escapeHtml(n.message)}</div><div class="notification-time">${getTimeAgo(n.created_at)}</div></div>`).join('');
}
function loadNotifs() {
    const c = document.getElementById('notificationList'); if (!c) return;
    c.innerHTML = '<div class="empty-notification"><i class="fas fa-spinner fa-spin"></i><p>Loading...</p></div>';
    fetch('/student/notifications/check', { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content } }).then(r => r.json()).then(d => { if (d.success) updateNotifList(d.recent_notifications); });
}
function getTimeAgo(ts) { const d = new Date(ts*1000), s = Math.floor((new Date()-d)/1000); if (s<60) return 'just now'; const m = Math.floor(s/60); if (m<60) return m+' min ago'; const h = Math.floor(m/60); if (h<24) return h+' hr ago'; return d.toLocaleDateString(); }
function escapeHtml(t) { if (!t) return ''; const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }

function positionDropdown() {
    const bell = document.getElementById('notificationBell'), dd = document.getElementById('notificationDropdown');
    if (!bell || !dd) return;
    const r = bell.getBoundingClientRect(), M = 16, W = Math.min(350, window.innerWidth - M*2);
    dd.style.width = W+'px'; dd.style.top = (r.bottom+8)+'px';
    let L; if (window.innerWidth<=640) L = (window.innerWidth-W)/2; else { L = r.right-W; if (L<M) L=M; if (L+W>window.innerWidth-M) L=window.innerWidth-W-M; }
    dd.style.left = L+'px'; dd.style.display = 'block';
}

const bellBtn = document.getElementById('notificationBell'), dropdown = document.getElementById('notificationDropdown');
if (bellBtn && dropdown) {
    bellBtn.addEventListener('click', function(e) { e.stopPropagation(); if (dropdown.style.display === 'block') dropdown.style.display = 'none'; else { positionDropdown(); loadNotifs(); } });
}
window.addEventListener('resize', () => { if (dropdown && dropdown.style.display === 'block') positionDropdown(); });
document.addEventListener('click', function(e) { if (dropdown && bellBtn && !bellBtn.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none'; });

const aiFloat = document.getElementById('aiFloatContainer'), aiPanel = document.getElementById('aiChatPanel');
if (aiFloat || aiPanel) {
    function hideAI() { if (aiFloat) aiFloat.classList.add('fab-hidden','hide-button'); }
    function showAI() { if (aiFloat) aiFloat.classList.remove('fab-hidden','hide-button'); }
    document.addEventListener('click', function(e) { if (aiPanel?.classList.contains('show') && !aiPanel.contains(e.target) && !aiFloat?.contains(e.target)) { aiPanel.classList.remove('show'); localStorage.setItem('aiPanelOpen','closed'); showAI(); } });
    document.addEventListener('keydown', function(e) { if (e.key==='Escape' && aiPanel?.classList.contains('show')) { aiPanel.classList.remove('show'); localStorage.setItem('aiPanelOpen','closed'); showAI(); } });
    if (localStorage.getItem('aiPanelOpen')==='open') { aiPanel?.classList.add('show'); hideAI(); } else showAI();
}

// ============ SIMPLE SWIPE TO OPEN SIDEBAR ============
let swipeStartX = 0;
let swipeStartY = 0;
let swipeActive = false;

document.addEventListener('touchstart', function(e) {
    const touch = e.touches[0];
    swipeStartX = touch.clientX;
    swipeStartY = touch.clientY;
    // Only activate if touch starts from left edge (first 40px)
    if (swipeStartX < 40) {
        swipeActive = true;
    } else {
        swipeActive = false;
    }
}, { passive: true });

document.addEventListener('touchend', function(e) {
    if (!swipeActive) return;
    
    const touch = e.changedTouches[0];
    const endX = touch.clientX;
    const endY = touch.clientY;
    
    const diffX = endX - swipeStartX;
    const diffY = Math.abs(endY - swipeStartY);
    
    // Swipe right at least 60px and not too vertical
    if (diffX > 60 && diffY < diffX) {
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('mainWrapper');
        const overlay = document.getElementById('overlay');
        
        if (sidebar && !sidebar.classList.contains('open')) {
            sidebar.classList.add('open');
            if (mainWrapper) mainWrapper.classList.add('menu-open');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    swipeActive = false;
});
// ============ ONLINE/OFFLINE DETECTION ============
const offlineBar = document.getElementById('offlineBar');
const connectionIndicator = document.getElementById('connectionIndicator');
let wasOffline = false;

function updateConnectionUI(online) {
    // Update profile dot
    const dot = document.getElementById('profileOnlineDot');
    if (dot) {
        dot.style.background = online ? '#22c55e' : '#ef4444';
    }
    
    // Update offline bar
    if (offlineBar) offlineBar.classList.toggle('show', !online);
    document.body.classList.toggle('offline', !online);
    
    // Disable/enable submit buttons
    document.querySelectorAll('button[type="submit"]').forEach(btn => {
        if (!online) { btn.setAttribute('data-was-disabled', btn.disabled); btn.disabled = true; }
        else { if (btn.getAttribute('data-was-disabled') === 'false') btn.disabled = false; btn.removeAttribute('data-was-disabled'); }
    });
}

function goOffline() { updateConnectionUI(false); wasOffline = true; }
function goOnline() {
    updateConnectionUI(true);
    if (wasOffline) {
        const toast = document.createElement('div'); toast.className = 'online-toast';
        toast.innerHTML = '<i class="fas fa-check-circle mr-2"></i> Back online!';
        document.body.appendChild(toast); setTimeout(() => toast.remove(), 3000);
        wasOffline = false;
    }
}

updateConnectionUI(navigator.onLine);
if (!navigator.onLine) wasOffline = true;
window.addEventListener('online', goOnline);
window.addEventListener('offline', goOffline);
setInterval(() => {
    const online = navigator.onLine;
    const indicator = document.getElementById('connectionIndicator');
    if (indicator) {
        const showingOnline = indicator.innerHTML.includes('22c55e');
        if (online && !showingOnline) goOnline();
        else if (!online && showingOnline) goOffline();
    }
}, 5000);

window.addEventListener('beforeunload', function(e) {
    if (!navigator.onLine) { e.preventDefault(); e.returnValue = ''; return ''; }
});
document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    if (link && !navigator.onLine) {
        const href = link.getAttribute('href');
        if (href && href !== '#' && !href.startsWith('#') && !href.startsWith('javascript:')) {
            e.preventDefault(); window.showToast('No internet connection', 'error');
        }
    }
}, true);
document.addEventListener('submit', function(e) {
    if (!navigator.onLine) { e.preventDefault(); window.showToast('No internet connection', 'error'); }
}, true);

const originalFetch = window.fetch;
window.fetch = function(...args) {
    if (!navigator.onLine) { window.showToast('No internet connection', 'error'); return Promise.reject(new Error('No internet connection')); }
    return originalFetch.apply(this, args);
};

// ============ DOM READY ============
document.addEventListener('DOMContentLoaded', function() {
    initDarkMode();
    const mobileBtn = document.getElementById('mobileMenuBtn'), sidebarEl = document.getElementById('sidebar'), mainWrapperEl = document.getElementById('mainWrapper'), overlayEl = document.getElementById('overlay');
    function toggleMenu() { sidebarEl.classList.toggle('open'); mainWrapperEl.classList.toggle('menu-open'); if (overlayEl) overlayEl.classList.toggle('hidden', !sidebarEl.classList.contains('open')); document.body.style.overflow = sidebarEl.classList.contains('open') ? 'hidden' : ''; }
    if (mobileBtn) { mobileBtn.addEventListener('click', function(e) { e.preventDefault(); e.stopPropagation(); toggleMenu(); }); }
    if (overlayEl) { overlayEl.addEventListener('click', function() { sidebarEl.classList.remove('open'); mainWrapperEl.classList.remove('menu-open'); overlayEl.classList.add('hidden'); document.body.style.overflow = ''; }); }
    document.querySelectorAll('.nav-link').forEach(link => { link.addEventListener('click', function() { if (window.innerWidth <= 768) { sidebarEl.classList.remove('open'); mainWrapperEl.classList.remove('menu-open'); if (overlayEl) overlayEl.classList.add('hidden'); document.body.style.overflow = ''; } }); });

    const profileImg = document.getElementById('profilePicture');
    if (profileImg) { profileImg.addEventListener('click', () => { window.location.href = '<?php echo e(route("student.profile")); ?>'; }); }
    document.getElementById('closeModalBtn')?.addEventListener('click', window.closeModalFunc);
    document.getElementById('profileModal')?.addEventListener('click', (e) => { if (e.target === document.getElementById('profileModal')) window.closeModalFunc(); });

    checkNotifs(); notifInterval = setInterval(checkNotifs, 60000);
    <?php if(session('success')): ?> window.showToast('<?php echo e(session('success')); ?>', 'success'); <?php endif; ?>
    <?php if(session('error')): ?> window.showToast('<?php echo e(session('error')); ?>', 'error'); <?php endif; ?>
    updateConnectionUI(navigator.onLine);
});

window.addEventListener('beforeunload', () => { if (notifInterval) clearInterval(notifInterval); });

// Register Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js', { scope: '/' })
            .then((reg) => console.log('SW registered:', reg.scope))
            .catch((err) => console.log('SW failed:', err));
    });
}
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
    <?php if(auth()->guard()->check()): ?>
    <?php if(session('show_ai_popup')): ?>
        <?php session()->forget('show_ai_popup'); ?>
        
        <div id="aiPopup" style="position:fixed; bottom:110px; right:20px; z-index:10000; max-width:280px; animation: aiPopIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
            <div style="background: var(--bg-primary, #ffffff); border-radius: 16px; padding: 0; box-shadow: 0 20px 60px rgba(0,0,0,0.2); border: 1px solid var(--border-color, #e5e7eb); overflow: hidden;">
                
                <!-- Header -->
                <div style="background: #0f172a; padding: 20px; text-align: center;">
                    <img src="/images/void.png" alt="Void AI" 
                         style="width: 56px; height: 56px; border-radius: 50%; border: 2px solid rgba(59,130,246,0.5); margin: 0 auto; display: block; object-fit: cover;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="display:none; width:56px; height:56px; border-radius:50%; background:rgba(59,130,246,0.2); margin:0 auto; align-items:center; justify-content:center; font-size:24px;">
                        <i class="fas fa-robot" style="color:#60a5fa;"></i>
                    </div>
                    <h4 style="color: white; margin: 10px 0 2px; font-size: 15px; font-weight: 700;">Void AI Assistant</h4>
                    <p style="color: rgba(255,255,255,0.6); font-size: 11px; margin: 0;">Ask anything, get instant help!</p>
                </div>
                
                <!-- Body -->
                <div style="padding: 14px 18px; background: var(--bg-primary, #ffffff);">
                    <p style="color: var(--text-secondary, #6b7280); font-size: 12px; line-height: 1.5; margin: 0 0 14px;">
                        Need help with clearance, requirements, or just have questions? Tap the floating AI icon anytime!
                    </p>
                    
                    <button onclick="dismissAIPopup()" 
                            style="width:100%; padding: 10px; border: none; background: #0f172a; color: white; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                            onmouseover="this.style.background='#1e293b'" 
                            onmouseout="this.style.background='#0f172a'">
                        Got it!
                    </button>
                </div>
                
                <!-- Arrow -->
                <div style="position:absolute; bottom:-8px; right:32px; width:16px; height:16px; background: var(--bg-primary, #ffffff); transform:rotate(45deg); border-right:1px solid var(--border-color, #e5e7eb); border-bottom:1px solid var(--border-color, #e5e7eb);"></div>
            </div>
        </div>

        <style>
            @keyframes aiPopIn {
                from { transform: translateY(30px) scale(0.9); opacity: 0; }
                to { transform: translateY(0) scale(1); opacity: 1; }
            }
            @keyframes aiPopOut {
                from { transform: translateY(0) scale(1); opacity: 1; }
                to { transform: translateY(30px) scale(0.9); opacity: 0; }
            }
            @media (max-width: 640px) {
                #aiPopup {
                    right: 10px !important;
                    left: 10px !important;
                    max-width: 100% !important;
                    bottom: 90px !important;
                }
            }
        </style>

        <script>
            function dismissAIPopup() {
                const popup = document.getElementById('aiPopup');
                if (popup) {
                    popup.style.animation = 'aiPopOut 0.2s ease forwards';
                    setTimeout(() => popup.remove(), 200);
                }
            }
        </script>
    <?php endif; ?>
<?php endif; ?>
</body>
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/layouts/app.blade.php ENDPATH**/ ?>