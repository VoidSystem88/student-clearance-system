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
                :root {
                    --bg-primary: #f3f4f6;
                    --bg-secondary: #ffffff;
                    --text-primary: #1f2937;
                    --text-secondary: #6b7280;
                    --border-color: #e5e7eb;
                    --card-bg: #ffffff;
                    --sidebar-bg: #ffffff;
                    --topbar-bg: #ffffff;
                    --input-bg: #ffffff;
                    --input-text: #1f2937;
                    --input-border: #e5e7eb;
                }

                body.dark {
                    --bg-primary: #111827;
                    --bg-secondary: #1f2937;
                    --text-primary: #f9fafb;
                    --text-secondary: #9ca3af;
                    --border-color: #374151;
                    --card-bg: #1f2937;
                    --sidebar-bg: #111827;
                    --topbar-bg: #1f2937;
                    --input-bg: #374151;
                    --input-text: #f3f4f6;
                    --input-border: #4b5563;
                }

                body {
                    background-color: var(--bg-primary);
                    color: var(--text-primary);
                    transition: background-color 0.3s ease, color 0.3s ease;
                }

                .bg-white { background-color: var(--bg-secondary) !important; }
                .bg-gray-50, .bg-gray-100 { background-color: var(--bg-primary) !important; }
                .text-gray-700, .text-gray-800, .text-gray-900 { color: var(--text-primary) !important; }
                .text-gray-500, .text-gray-600 { color: var(--text-secondary) !important; }
                .border-gray-100, .border-gray-200, .border-gray-300 { border-color: var(--border-color) !important; }
                .sidebar { background-color: var(--sidebar-bg) !important; }
                .nav-link { color: var(--text-primary) !important; }
                .nav-link:hover { background-color: rgba(59, 130, 246, 0.1) !important; }
                .nav-active { background-color: rgba(59, 130, 246, 0.2) !important; color: #3b82f6 !important; }

                .app-container { display: flex; min-height: 100vh; overflow-x: hidden; }
                .sidebar {
                    width: 280px; background: white; box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                    height: 100vh; position: fixed; top: 0; left: 0; z-index: 40;
                    transform: translateX(-100%); transition: transform 0.3s ease-in-out;
                }
                .sidebar.open { transform: translateX(0); }
                .main-wrapper { flex: 1; transition: transform 0.3s ease-in-out; width: 100%; }

                @media (min-width: 768px) {
                    .sidebar { transform: translateX(0) !important; position: relative; }
                }

                @media (max-width: 767px) {
                    .main-wrapper.menu-open { transform: translateX(280px); }
                }

                .dark-mode-toggle {
                    position: fixed; bottom: 20px; right: 20px; z-index: 1000;
                    background: var(--card-bg); border: 1px solid var(--border-color);
                    border-radius: 50%; width: 50px; height: 50px;
                    display: flex; align-items: center; justify-content: center;
                    cursor: pointer; box-shadow: 0 2px 10px rgba(0,0,0,0.2);
                    transition: all 0.3s ease;
                }

                .toast {
                    position: fixed; bottom: 100px; right: 20px; z-index: 9999;
                    min-width: 200px; animation: slideIn 0.3s ease;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                .toast-success { background: #22c55e; }
                .toast-error { background: #ef4444; }
                .toast-info { background: #3b82f6; }

                .swipe-area { position: fixed; left: 0; top: 0; width: 50px; height: 100%; z-index: 9999; pointer-events: none; }
                .swipe-hint { position: fixed; left: 5px; top: 50%; transform: translateY(-50%); width: 4px; height: 50px; background: rgba(59, 130, 246, 0.5); border-radius: 4px; opacity: 0; transition: opacity 0.3s ease; z-index: 46; pointer-events: none; }
                .swipe-hint.visible { opacity: 1; }
                @media (min-width: 768px) { .swipe-area, .swipe-hint { display: none; } }
                #swipeOverlay { position: fixed; left: 0; top: 0; width: 35px; height: 100%; z-index: 99999; background: transparent; pointer-events: auto; }
                #overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 35; display: none; }
                #overlay:not(.hidden) { display: block; }

                input, select, textarea {
                    background-color: var(--input-bg) !important;
                    color: var(--input-text) !important;
                    border-color: var(--input-border) !important;
                }

                ::-webkit-scrollbar { width: 6px; height: 6px; }
                ::-webkit-scrollbar-track { background: var(--bg-primary); border-radius: 10px; }
                ::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 10px; }

                /* Profile Picture Styles */
                .profile-img {
                    width: 36px;
                    height: 36px;
                    border-radius: 50%;
                    object-fit: cover;
                    border: 2px solid #3b82f6;
                    background-color: #e2e8f0;
                    cursor: pointer;
                    transition: transform 0.2s;
                }
                .profile-img:hover {
                    transform: scale(1.05);
                }
                .profile-img.large {
                    width: 100px;
                    height: 100px;
                    border-width: 3px;
                }

                /* Modal for uploading profile picture */
                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    z-index: 10000;
                    justify-content: center;
                    align-items: center;
                }
                .modal.active {
                    display: flex;
                }
                .modal-content {
                    background: var(--bg-secondary);
                    border-radius: 16px;
                    padding: 24px;
                    max-width: 400px;
                    width: 90%;
                    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
                }
            </style>
            @stack('styles')
        </head>
        <body class="bg-gray-50 font-sans antialiased">
            <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden"></div>
            <div id="toastContainer"></div>

            <!-- Modal for uploading profile picture -->
            <div id="profileModal" class="modal">
                <div class="modal-content">
                    <div class="text-center mb-4">
                        <i class="fas fa-camera text-4xl text-blue-500 mb-2"></i>
                        <h3 class="text-xl font-bold">Palitan ang Profile Picture</h3>
                        <p class="text-sm text-gray-500 mt-1">Mag-upload ng larawan o gamitin ang Gmail</p>
                    </div>

                    <!-- Upload option -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Mag-upload ng larawan</label>
                        <input type="file" id="profileUpload" accept="image/jpeg,image/png,image/jpg" class="w-full p-2 border rounded-lg">
                    </div>

                    <!-- Gmail Option (with instructions) -->
                    <div class="border-t pt-4 mt-2">
                        <p class="text-sm text-gray-500 mb-2 text-center">— O kaya —</p>
                        <button id="gmailOptionBtn" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition flex items-center justify-center gap-2">
                            <i class="fab fa-google"></i> Kunin ang larawan mula sa Gmail
                        </button>
                        <p class="text-xs text-gray-400 text-center mt-2">
                            <i class="fas fa-info-circle"></i> Kailangan ng iyong sariling Google Client ID
                        </p>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button id="closeModalBtn" class="flex-1 bg-gray-300 py-2 rounded-lg">Kanselahin</button>
                    </div>
                </div>
            </div>

            <!-- Dark Mode Toggle Button -->
            <button id="darkModeToggle" class="dark-mode-toggle">
                <i id="darkModeIcon" class="fas fa-moon text-xl"></i>
            </button>

            <!-- Swipe detection area -->
            <div class="swipe-area" id="swipeArea"></div>
            <div class="swipe-hint" id="swipeHint">
                <div class="w-1 h-12 bg-blue-500 rounded-full"></div>
            </div>

            <!-- Main App Container -->
            <div class="app-container">
                <!-- Sidebar -->
                <aside id="sidebar" class="sidebar">
                    <div class="p-5 border-b">
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

                    <nav class="flex-1 py-4 overflow-y-auto">
                        @auth
                            <a href="{{ route('student.dashboard') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                <i class="fas fa-tachometer-alt w-5"></i><span>Dashboard</span>
                            </a>
                            <a href="{{ route('student.clearance') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                <i class="fas fa-clipboard-list w-5"></i><span>Clearance</span>
                            </a>
                            <a href="{{ route('student.reminders') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                <i class="fas fa-bell w-5"></i><span>Reminders</span>
                            </a>
                            <a href="{{ route('student.profile') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                <i class="fas fa-user-circle w-5"></i><span>My Profile</span>
                            </a>
                            <a href="{{ route('student.assistance') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                <i class="fas fa-headset w-5"></i><span>Request Assistance</span>
                            </a>
                            <div class="border-t my-3 mx-5"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link flex items-center gap-3 px-5 py-3 text-red-600 hover:bg-red-50 transition w-full text-left">
                                    <i class="fas fa-sign-out-alt w-5"></i><span>Logout</span>
                                </button>
                                <a href="{{ route('student.feedback') }}" class="nav-link flex items-center gap-3 px-5 py-3 transition">
                                    <i class="fas fa-star w-5"></i><span>Feedback</span>
                                </a>
                            </form>
                        @endauth
                    </nav>

                    <div class="p-4 border-t text-center text-xs">
                        <p>© {{ date('Y') }} Clearance System</p>
                        <p>v1.0 | Void</p>
                    </div>
                </aside>

                <!-- Main Content Wrapper -->
                <div id="mainWrapper" class="main-wrapper">
                    <button id="mobileMenuBtn" class="absolute top-2 left-4 z-50 text-gray-800 dark:text-white p-2">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="bg-white shadow-sm sticky top-0 z-20" style="background-color: var(--topbar-bg) !important;">
                        <div class="px-4 py-3 flex justify-between items-center">
                            <div class="md:hidden w-10"></div>
                            <h2 class="text-lg font-semibold">@yield('header', 'Dashboard')</h2>
                            <div class="flex items-center gap-4">
                                <span class="text-sm hidden sm:block" id="userNameDisplay">@auth {{ Auth::user()->first_name ?? '' }} @endauth</span>
                                <!-- PROFILE PICTURE -->
                                <img id="profilePicture" class="profile-img cursor-pointer" 
                                     src="{{ Auth::user()->profile_photo_url ?? 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode(Auth::user()->first_name ?? 'User') . '&size=64' }}" 
                                     alt="Profile" 
                                     title="I-click para palitan ang larawan">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 md:p-6">
                        @yield('content')
                    </div>
                </div>
            </div>

            <!-- ============ AI ASSISTANT COMPONENT ============ -->
            @include('components.ai-assistant')

            <script>
                // ==================== PROFILE PICTURE FUNCTIONS ====================

                // Get elements
                const profileImg = document.getElementById('profilePicture');
                const profileModal = document.getElementById('profileModal');
                const profileUpload = document.getElementById('profileUpload');
                const closeModalBtn = document.getElementById('closeModalBtn');
                const gmailOptionBtn = document.getElementById('gmailOptionBtn');
                const userNameDisplay = document.getElementById('userNameDisplay');

                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Show toast notification
                function showToast(message, type = 'success') {
                    const container = document.getElementById('toastContainer');
                    if (!container) return;
                    const toast = document.createElement('div');
                    toast.className = `toast toast-${type} text-white px-4 py-2 rounded-lg shadow-lg`;
                    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>${message}`;
                    container.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                }

                // Handle file upload to server
                async function handleImageUpload(file) {
                    if (!file) return;

                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!validTypes.includes(file.type)) {
                        showToast('JPG o PNG lang po ang pwede.', 'error');
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        showToast('Max 2MB lang po ang laki ng file.', 'error');
                        return;
                    }

                    // Create FormData
                    const formData = new FormData();
                    formData.append('photo', file);

                    try {
                        showToast('⏳ Nag-a-upload...', 'info');

                        const response = await fetch('/upload-profile-photo', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update the image source with the new URL from server
                            document.getElementById('profilePicture').src = data.photo_url + '?t=' + Date.now();
                            showToast(data.message, 'success');
                            closeModal();

                            // Optional: Reload page para makita ang changes sa buong system
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showToast(data.message || 'Error uploading photo', 'error');
                        }
                    } catch (error) {
                        console.error('Upload error:', error);
                        showToast('Network error. Pakisubukan ulit.', 'error');
                    }
                }

                // Save Gmail photo to server
                async function saveGmailPhotoToServer(photoUrl) {
                    try {
                        const response = await fetch('/save-gmail-photo', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ photo_url: photoUrl })
                        });

                        const data = await response.json();

                        if (data.success) {
                            document.getElementById('profilePicture').src = data.photo_url + '?t=' + Date.now();
                            showToast('✓ Nakuha ang larawan mula sa iyong Gmail!', 'success');
                            closeModal();
                            setTimeout(() => location.reload(), 1500);
                            return true;
                        } else {
                            showToast(data.message || 'Error saving Gmail photo', 'error');
                            return false;
                        }
                    } catch (error) {
                        console.error('Gmail save error:', error);
                        showToast('Network error. Pakisubukan ulit.', 'error');
                        return false;
                    }
                }

                // Open modal
                function openModal() {
                    profileModal.classList.add('active');
                }

                function closeModal() {
                    profileModal.classList.remove('active');
                    if (profileUpload) profileUpload.value = '';
                }

                // Gmail Option - shows instructions kung paano gumawa ng sariling Google Client ID
                function showGmailInstructions() {
                    const instructions = `⚠️ Para magamit ang Gmail photo, kailangan mong gumawa ng sarili mong Google OAuth Client ID.\n\n📋 Mga Hakbang:\n1. Pumunta sa https://console.cloud.google.com/\n2. Gumawa ng bagong project o pumili ng existing\n3. I-enable ang "People API"\n4. Pumunta sa "Credentials" > "Create Credentials" > "OAuth Client ID"\n5. Piliin ang "Web application"\n6. Ilagay ang iyong website URL (hal. http://localhost:8000)\n7. I-copy ang Client ID\n8. I-paste ito sa ibaba\n\nGusto mo bang i-setup ngayon?`;

                    const clientIdInput = prompt(instructions + "\n\nI-paste ang iyong Google Client ID:");
                    if (clientIdInput && clientIdInput.trim() !== '') {
                        localStorage.setItem('google_client_id', clientIdInput);
                        showToast('Client ID na-save! Subukan mong i-click muli ang Gmail option.', 'info');
                        initGmailAuth(clientIdInput);
                    } else {
                        showToast('Pwede ka namang mag-upload na lang ng larawan.', 'info');
                    }
                }

                // Initialize Gmail authentication
                function initGmailAuth(clientId) {
                    // Load Google API script dynamically if not loaded
                    if (typeof google === 'undefined' || !google.accounts) {
                        showToast('Loading Google SDK, pakisubukan ulit sa sandali.', 'info');

                        // Dynamically load Google API
                        const script = document.createElement('script');
                        script.src = 'https://accounts.google.com/gsi/client';
                        script.onload = () => {
                            initGmailAuth(clientId);
                        };
                        document.head.appendChild(script);
                        return;
                    }

                    const client = google.accounts.oauth2.initTokenClient({
                        client_id: clientId,
                        scope: 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
                        callback: async (tokenResponse) => {
                            if (tokenResponse.access_token) {
                                try {
                                    showToast('Kumukuha ng impormasyon mula sa Gmail...', 'info');

                                    // Fetch user info including photo
                                    const response = await fetch('https://www.googleapis.com/oauth2/v3/userinfo', {
                                        headers: { 'Authorization': `Bearer ${tokenResponse.access_token}` }
                                    });
                                    const userData = await response.json();

                                    if (userData.picture) {
                                        // Save to server
                                        await saveGmailPhotoToServer(userData.picture);
                                    } else {
                                        showToast('Walang nakitang profile picture sa Gmail mo. Mag-upload ka na lang.', 'info');
                                    }
                                } catch (err) {
                                    console.error('Gmail fetch error:', err);
                                    showToast('Hindi makuha ang larawan, pakisubukan ulit.', 'error');
                                }
                            } else {
                                showToast('Kailangan ng permission para makuha ang iyong larawan.', 'error');
                            }
                        },
                    });
                    client.requestAccessToken({ prompt: 'consent' });
                }

                // Click handlers
                if (profileImg) {
                    profileImg.addEventListener('click', openModal);
                }

                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', closeModal);
                }

                if (profileUpload) {
                    profileUpload.addEventListener('change', (e) => {
                        if (e.target.files && e.target.files[0]) {
                            handleImageUpload(e.target.files[0]);
                        }
                    });
                }

                if (gmailOptionBtn) {
                    gmailOptionBtn.addEventListener('click', () => {
                        const savedClientId = localStorage.getItem('google_client_id');
                        if (savedClientId) {
                            initGmailAuth(savedClientId);
                        } else {
                            showGmailInstructions();
                        }
                    });
                }

                // Close modal when clicking outside
                if (profileModal) {
                    profileModal.addEventListener('click', (e) => {
                        if (e.target === profileModal) closeModal();
                    });
                }

                // ==================== EXISTING SCRIPTS (Preserved) ====================

                // Dark Mode
                function initDarkMode() {
                    const toggle = document.getElementById('darkModeToggle');
                    const icon = document.getElementById('darkModeIcon');
                    const savedTheme = localStorage.getItem('theme');
                    if (savedTheme === 'dark') {
                        document.body.classList.add('dark');
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    }
                    if (toggle) {
                        toggle.addEventListener('click', () => {
                            document.body.classList.toggle('dark');
                            if (document.body.classList.contains('dark')) {
                                localStorage.setItem('theme', 'dark');
                                icon.classList.remove('fa-moon');
                                icon.classList.add('fa-sun');
                            } else {
                                localStorage.setItem('theme', 'light');
                                icon.classList.remove('fa-sun');
                                icon.classList.add('fa-moon');
                            }
                        });
                    }
                }

                // Mobile menu
                const mobileBtn = document.getElementById('mobileMenuBtn');
                const sidebar = document.getElementById('sidebar');
                const mainWrapper = document.getElementById('mainWrapper');
                const overlay = document.getElementById('overlay');

                function toggleMobileMenu() {
                    sidebar.classList.toggle('open');
                    mainWrapper.classList.toggle('menu-open');
                    if (overlay) {
                        if (sidebar.classList.contains('open')) overlay.classList.remove('hidden');
                        else overlay.classList.add('hidden');
                    }
                    document.body.style.overflow = sidebar.classList.contains('open') ? 'hidden' : '';
                }

                if (mobileBtn) mobileBtn.addEventListener('click', toggleMobileMenu);

                document.querySelectorAll('.nav-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            sidebar.classList.remove('open');
                            mainWrapper.classList.remove('menu-open');
                            if (overlay) overlay.classList.add('hidden');
                            document.body.style.overflow = '';
                        }
                    });
                });

                // Swipe Sidebar
                let touchStartX = 0, touchCurrentX = 0, isSwiping = false, swipeDirection = null;
                let swipeOverlay = document.getElementById('swipeOverlay');
                if (!swipeOverlay) {
                    swipeOverlay = document.createElement('div');
                    swipeOverlay.id = 'swipeOverlay';
                    swipeOverlay.style.cssText = 'position:fixed; left:0; top:0; width:35px; height:100%; z-index:99999; background:transparent;';
                    document.body.appendChild(swipeOverlay);
                }

                swipeOverlay.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].clientX;
                    isSwiping = true;
                    swipeDirection = null;
                    e.stopPropagation();
                }, { passive: false });

                swipeOverlay.addEventListener('touchmove', function(e) {
                    if (!isSwiping) return;
                    touchCurrentX = e.touches[0].clientX;
                    if (touchCurrentX - touchStartX > 10) {
                        swipeDirection = 'right';
                        e.preventDefault();
                    }
                    e.stopPropagation();
                }, { passive: false });

                swipeOverlay.addEventListener('touchend', function(e) {
                    if (!isSwiping) { isSwiping = false; return; }
                    if (touchCurrentX - touchStartX > 40 && swipeDirection === 'right') {
                        if (sidebar && !sidebar.classList.contains('open')) {
                            sidebar.classList.add('open');
                            if (mainWrapper) mainWrapper.classList.add('menu-open');
                            if (overlay) overlay.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }
                    }
                    isSwiping = false;
                    touchStartX = 0;
                    touchCurrentX = 0;
                    swipeDirection = null;
                    e.stopPropagation();
                });

                document.addEventListener('touchstart', function(e) {
                    if (sidebar && sidebar.classList.contains('open') && !e.target.closest('.sidebar')) {
                        touchStartX = e.touches[0].clientX;
                        isSwiping = true;
                    }
                });

                document.addEventListener('touchmove', function(e) {
                    if (!isSwiping) return;
                    if (sidebar && sidebar.classList.contains('open')) {
                        touchCurrentX = e.touches[0].clientX;
                        if (touchStartX - touchCurrentX > 10) e.preventDefault();
                    }
                }, { passive: false });

                document.addEventListener('touchend', function(e) {
                    if (!isSwiping) { isSwiping = false; return; }
                    if (sidebar && sidebar.classList.contains('open') && touchStartX - touchCurrentX > 40) {
                        sidebar.classList.remove('open');
                        if (mainWrapper) mainWrapper.classList.remove('menu-open');
                        if (overlay) overlay.classList.add('hidden');
                        document.body.style.overflow = '';
                    }
                    isSwiping = false;
                    touchStartX = 0;
                    touchCurrentX = 0;
                });

                @if(session('success'))
                    showToast('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                    showToast('{{ session('error') }}', 'error');
                @endif

                initDarkMode();
            </script>
            @stack('scripts')
        </body>
        </html>