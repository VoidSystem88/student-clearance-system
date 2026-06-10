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
            <a href="{{ route('support.requests') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.requests') ? 'nav-active' : '' }}">
                <i class="fas fa-ticket-alt w-5"></i>
                <span>Requests</span>
            </a>
            <a href="{{ route('support.students') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.students') ? 'nav-active' : '' }}">
                <i class="fas fa-users w-5"></i>
                <span>Manage Students</span>
            </a>
            <a href="{{ route('support.feedbacks') }}" class="flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition {{ request()->routeIs('support.feedbacks') ? 'nav-active' : '' }}">
                <i class="fas fa-star w-5"></i>
                <span>Feedbacks</span>
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
                    <span class="text-sm text-gray-600 hidden sm:block">{{ Auth::user()->email ?? 'Support' }}</span>
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

    <script>
        // Mobile menu functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.add('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>