<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Support Dashboard</title>
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
        
        /* Tab Styles */
        .tab-btn {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .tab-btn.active {
            background-color: #3b82f6;
            color: white;
        }
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block;
        }
        
        /* Search highlight */
        .search-highlight {
            background-color: rgba(59, 130, 246, 0.2);
            border-radius: 4px;
        }
    </style>
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
            <button onclick="switchTab('dashboard')" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition w-full text-left">
                <i class="fas fa-tachometer-alt w-5"></i>
                <span>Dashboard</span>
            </button>
            <button onclick="switchTab('requests')" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition w-full text-left">
                <i class="fas fa-ticket-alt w-5 text-blue-600"></i>
                <span>Requests</span>
            </button>
            <button onclick="switchTab('students')" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition w-full text-left">
                <i class="fas fa-users w-5 text-green-600"></i>
                <span>Manage Students</span>
            </button>
            <button onclick="switchTab('feedbacks')" class="nav-link flex items-center gap-3 px-5 py-3 text-gray-700 hover:bg-gray-100 transition w-full text-left">
                <i class="fas fa-star w-5 text-purple-600"></i>
                <span>Feedbacks</span>
            </button>
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
                    <h2 id="pageTitle" class="text-lg font-semibold text-gray-700">Support Dashboard</h2>
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

            <!-- ============ DASHBOARD TAB ============ -->
            <div id="dashboardPane" class="tab-pane active">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Pending Requests</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">In Progress</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $inProgressCount ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-spinner text-blue-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Resolved</p>
                                <p class="text-2xl font-bold text-green-600">{{ $resolvedCount ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Students</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $totalStudents ?? 0 }}</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-purple-600"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Quick Links -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded-xl p-6 text-center">
                        <i class="fas fa-ticket-alt text-blue-600 text-4xl mb-3"></i>
                        <h3 class="font-bold text-lg">Support Requests</h3>
                        <p class="text-gray-600 text-sm mb-4">View and manage student support tickets</p>
                        <button onclick="switchTab('requests')" class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Go to Requests <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="bg-green-50 rounded-xl p-6 text-center">
                        <i class="fas fa-users text-green-600 text-4xl mb-3"></i>
                        <h3 class="font-bold text-lg">Manage Students</h3>
                        <p class="text-gray-600 text-sm mb-4">Reset passwords, manage accounts</p>
                        <button onclick="switchTab('students')" class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                            Go to Students <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-6 text-center">
                        <i class="fas fa-star text-purple-600 text-4xl mb-3"></i>
                        <h3 class="font-bold text-lg">Feedbacks</h3>
                        <p class="text-gray-600 text-sm mb-4">View and respond to student feedback</p>
                        <button onclick="switchTab('feedbacks')" class="inline-flex items-center gap-2 bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                            Go to Feedbacks <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ============ REQUESTS TAB ============ -->
            <div id="requestsPane" class="tab-pane">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                <i class="fas fa-ticket-alt text-blue-600 mr-2"></i> Student Support Requests
                            </h3>
                            <p class="text-sm text-gray-500">View and manage student assistance requests</p>
                        </div>
                        <!-- Search Box for Requests -->
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="searchRequests" placeholder="Search by student name, ID, or request type..." 
                                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="requestsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Issue Type</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Description</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Submitted</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="requestsTableBody">
                                @forelse($supportRequests ?? [] as $request)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm font-mono text-gray-600">#{{ $request->id }}</td>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-800">{{ $request->student->first_name ?? '' }} {{ $request->student->last_name ?? '' }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->student->student_id ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                            @if($request->request_type == 'password_reset') bg-blue-100 text-blue-700
                                            @elseif($request->request_type == 'account_id_reset') bg-purple-100 text-purple-700
                                            @elseif($request->request_type == 'login_issue') bg-red-100 text-red-700
                                            @elseif($request->request_type == 'otp_issue') bg-yellow-100 text-yellow-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ str_replace('_', ' ', ucfirst($request->request_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600 max-w-xs">{{ Str::limit($request->description, 50) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                            @if($request->status == 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($request->status == 'in_progress') bg-blue-100 text-blue-700
                                            @elseif($request->status == 'resolved') bg-green-100 text-green-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-500">{{ $request->created_at->format('M d, Y') }}</td>
                                    <td class="px-5 py-3">
                                        <button onclick="openUpdateModal({{ $request->id }})" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                            <i class="fas fa-edit"></i> Update
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No support requests yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============ STUDENTS TAB ============ -->
            <div id="studentsPane" class="tab-pane">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                <i class="fas fa-users text-blue-600 mr-2"></i> Manage Students
                            </h3>
                            <p class="text-sm text-gray-500">Reset passwords, reset Account IDs, or manage account status</p>
                        </div>
                        <!-- Search Box for Students -->
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="searchStudents" placeholder="Search by name, student ID, or email..." 
                                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="studentsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Account ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Course & Year</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="studentsTableBody">
                                @foreach($students ?? [] as $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ $student->student_id }}</td>
                                    <td class="px-5 py-3 font-mono text-sm text-gray-600">{{ $student->account_id }}</td>
                                    <td class="px-5 py-3 text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $student->course_year ?? $student->course . ' - ' . $student->year_level }}</td>
                                    <td class="px-5 py-3">
                                        @if($student->is_active)
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-circle text-xs"></i> Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <i class="fas fa-circle text-xs"></i> Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            <button onclick="openEditModal({{ $student->id }})" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button onclick="resetPassword({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}')" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                                <i class="fas fa-key"></i> Reset PW
                                            </button>
                                            <button onclick="resetAccountId({{ $student->id }})" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700">
                                                <i class="fas fa-id-card"></i> Reset ID
                                            </button>
                                            <button onclick="toggleActive({{ $student->id }}, '{{ addslashes($student->first_name . ' ' . $student->last_name) }}', '{{ $student->is_active ? 'active' : 'inactive' }}')" 
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 {{ $student->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white text-sm rounded-lg">
                                                <i class="fas {{ $student->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                {{ $student->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ============ FEEDBACKS TAB ============ -->
            <div id="feedbacksPane" class="tab-pane">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex flex-wrap gap-3 justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-800">
                                <i class="fas fa-star text-purple-600 mr-2"></i> Student Feedbacks
                            </h3>
                            <p class="text-sm text-gray-500">View and respond to student feedback</p>
                        </div>
                        <!-- Search Box for Feedbacks -->
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="searchFeedbacks" placeholder="Search by student name, message, or category..." 
                                   class="pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm w-64 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="feedbacksTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Student</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Rating</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Category</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Message</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="feedbacksTableBody">
                                @forelse($feedbacks ?? [] as $fb)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3 text-sm font-mono text-gray-600">#{{ $fb->id }}</td>
                                    <td class="px-5 py-3">
                                        <div class="font-medium text-gray-800">{{ $fb->user->first_name ?? '' }} {{ $fb->user->last_name ?? '' }}</div>
                                        <div class="text-xs text-gray-500">{{ $fb->user->student_id ?? '' }}</div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $fb->rating)
                                                    <i class="fas fa-star text-yellow-500 text-sm"></i>
                                                @else
                                                    <i class="far fa-star text-gray-300 text-sm"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                            @if($fb->category == 'bug') bg-red-100 text-red-700
                                            @elseif($fb->category == 'feature') bg-purple-100 text-purple-700
                                            @elseif($fb->category == 'improvement') bg-blue-100 text-blue-700
                                            @elseif($fb->category == 'experience') bg-green-100 text-green-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ ucfirst($fb->category) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-sm text-gray-600 max-w-xs">{{ Str::limit($fb->message, 50) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium
                                            @if($fb->status == 'pending') bg-yellow-100 text-yellow-700
                                            @elseif($fb->status == 'reviewed') bg-blue-100 text-blue-700
                                            @else bg-green-100 text-green-700 @endif">
                                            <i class="fas 
                                                @if($fb->status == 'pending') fa-clock
                                                @elseif($fb->status == 'reviewed') fa-eye
                                                @else fa-check @endif text-xs"></i>
                                            {{ ucfirst($fb->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <button onclick="viewFeedback({{ $fb->id }}, '{{ addslashes($fb->message) }}', '{{ addslashes($fb->user->first_name . ' ' . $fb->user->last_name) }}', {{ $fb->rating }}, '{{ $fb->category }}', '{{ $fb->status }}', '{{ addslashes($fb->admin_response) }}')" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        @if($fb->status == 'pending')
                                        <button onclick="respondFeedback({{ $fb->id }})" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 ml-2">
                                            <i class="fas fa-reply"></i> Respond
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                                        <p>No feedback yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Update Status Modal -->
    <div id="updateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Update Support Request</h3>
                <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="updateForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 text-sm font-medium">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" required>
                        <option value="pending">⏳ Pending</option>
                        <option value="in_progress">🔄 In Progress</option>
                        <option value="resolved">✅ Resolved</option>
                        <option value="cancelled">❌ Cancelled</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 text-sm font-medium">Admin Notes</label>
                    <textarea name="admin_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Add notes about resolution..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeUpdateModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Student Information</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1 text-sm font-medium">First Name</label>
                    <input type="text" name="first_name" id="edit_first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1 text-sm font-medium">Last Name</label>
                    <input type="text" name="last_name" id="edit_last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1 text-sm font-medium">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1 text-sm font-medium">Course & Year</label>
                    <select name="course_year" id="edit_course_year" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                        <option value="">-- Select Course & Year --</option>
                        <optgroup label="College of Computer Studies">
                            <option value="BSIT-1st Year">BSIT - 1st Year</option>
                            <option value="BSIT-2nd Year">BSIT - 2nd Year</option>
                            <option value="BSIT-3rd Year">BSIT - 3rd Year</option>
                            <option value="BSIT-4th Year">BSIT - 4th Year</option>
                            <option value="BSCS-1st Year">BSCS - 1st Year</option>
                            <option value="BSCS-2nd Year">BSCS - 2nd Year</option>
                            <option value="BSCS-3rd Year">BSCS - 3rd Year</option>
                            <option value="BSCS-4th Year">BSCS - 4th Year</option>
                            <option value="BSIS-1st Year">BSIS - 1st Year</option>
                            <option value="BSIS-2nd Year">BSIS - 2nd Year</option>
                            <option value="BSIS-3rd Year">BSIS - 3rd Year</option>
                            <option value="BSIS-4th Year">BSIS - 4th Year</option>
                        </optgroup>
                        <optgroup label="College of Business and Accountancy">
                            <option value="BSBA-FM-1st Year">BSBA Financial Management - 1st Year</option>
                            <option value="BSBA-FM-2nd Year">BSBA Financial Management - 2nd Year</option>
                            <option value="BSBA-FM-3rd Year">BSBA Financial Management - 3rd Year</option>
                            <option value="BSBA-FM-4th Year">BSBA Financial Management - 4th Year</option>
                            <option value="BSHM-1st Year">BS Hospitality Management - 1st Year</option>
                            <option value="BSHM-2nd Year">BS Hospitality Management - 2nd Year</option>
                            <option value="BSHM-3rd Year">BS Hospitality Management - 3rd Year</option>
                            <option value="BSHM-4th Year">BS Hospitality Management - 4th Year</option>
                        </optgroup>
                        <optgroup label="College of Education">
                            <option value="BEEd-1st Year">BEEd - 1st Year</option>
                            <option value="BEEd-2nd Year">BEEd - 2nd Year</option>
                            <option value="BEEd-3rd Year">BEEd - 3rd Year</option>
                            <option value="BEEd-4th Year">BEEd - 4th Year</option>
                            <option value="BSEd-English-1st Year">BSEd English - 1st Year</option>
                            <option value="BSEd-English-2nd Year">BSEd English - 2nd Year</option>
                            <option value="BSEd-English-3rd Year">BSEd English - 3rd Year</option>
                            <option value="BSEd-English-4th Year">BSEd English - 4th Year</option>
                        </optgroup>
                        <optgroup label="College of Criminology">
                            <option value="BSCrim-1st Year">BS Criminology - 1st Year</option>
                            <option value="BSCrim-2nd Year">BS Criminology - 2nd Year</option>
                            <option value="BSCrim-3rd Year">BS Criminology - 3rd Year</option>
                            <option value="BSCrim-4th Year">BS Criminology - 4th Year</option>
                        </optgroup>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-gray-700 mb-1 text-sm font-medium">New Password (optional)</label>
                    <input type="password" name="password" id="edit_password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Leave blank to keep current">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Feedback Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Feedback Details</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div id="viewContent"></div>
            <div class="flex justify-end mt-4">
                <button onclick="closeViewModal()" class="px-4 py-2 bg-gray-300 rounded-lg text-sm">Close</button>
            </div>
        </div>
    </div>

    <!-- Respond Feedback Modal -->
    <div id="respondModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-green-600">Respond to Feedback</h3>
                <button onclick="closeRespondModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <form id="respondForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2 text-sm font-medium">Admin Response</label>
                    <textarea name="admin_response" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2" required placeholder="Type your response here..."></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRespondModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Send Response</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ============ TAB SWITCHING ============
        function switchTab(tabName) {
            // Hide all panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Show selected pane
            document.getElementById(tabName + 'Pane').classList.add('active');
            
            // Update page title
            const titles = {
                dashboard: 'Dashboard',
                requests: 'Support Requests',
                students: 'Manage Students',
                feedbacks: 'Feedbacks'
            };
            document.getElementById('pageTitle').innerText = titles[tabName] || 'Support Dashboard';
        }
        
        // ============ SEARCH FUNCTIONALITY ============
        // Search for Requests
        document.getElementById('searchRequests')?.addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#requestsTableBody tr');
            let hasResults = false;
            
            rows.forEach(row => {
                if (row.cells) {
                    let text = '';
                    for (let i = 0; i < row.cells.length; i++) {
                        text += row.cells[i].textContent.toLowerCase() + ' ';
                    }
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        hasResults = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // Show/hide no results message
            let noResultRow = document.getElementById('requestsNoResult');
            if (!hasResults && rows.length > 0) {
                if (!noResultRow) {
                    let tbody = document.getElementById('requestsTableBody');
                    let tr = document.createElement('tr');
                    tr.id = 'requestsNoResult';
                    tr.innerHTML = `<td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                        <p>No requests found for "<span id="requestsSearchTerm"></span>"</p>
                                    </td>`;
                    tbody.appendChild(tr);
                }
                document.getElementById('requestsSearchTerm').innerText = searchTerm;
                noResultRow.style.display = '';
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        });
        
        // Search for Students
        document.getElementById('searchStudents')?.addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#studentsTableBody tr');
            let hasResults = false;
            
            rows.forEach(row => {
                if (row.cells) {
                    let text = '';
                    for (let i = 0; i < row.cells.length; i++) {
                        text += row.cells[i].textContent.toLowerCase() + ' ';
                    }
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        hasResults = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            let noResultRow = document.getElementById('studentsNoResult');
            if (!hasResults && rows.length > 0) {
                if (!noResultRow) {
                    let tbody = document.getElementById('studentsTableBody');
                    let tr = document.createElement('tr');
                    tr.id = 'studentsNoResult';
                    tr.innerHTML = `<td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                        <p>No students found for "<span id="studentsSearchTerm"></span>"</p>
                                    </td>`;
                    tbody.appendChild(tr);
                }
                document.getElementById('studentsSearchTerm').innerText = searchTerm;
                noResultRow.style.display = '';
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        });
        
        // Search for Feedbacks
        document.getElementById('searchFeedbacks')?.addEventListener('keyup', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('#feedbacksTableBody tr');
            let hasResults = false;
            
            rows.forEach(row => {
                if (row.cells) {
                    let text = '';
                    for (let i = 0; i < row.cells.length; i++) {
                        text += row.cells[i].textContent.toLowerCase() + ' ';
                    }
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                        hasResults = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            let noResultRow = document.getElementById('feedbacksNoResult');
            if (!hasResults && rows.length > 0) {
                if (!noResultRow) {
                    let tbody = document.getElementById('feedbacksTableBody');
                    let tr = document.createElement('tr');
                    tr.id = 'feedbacksNoResult';
                    tr.innerHTML = `<td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                        <i class="fas fa-search text-4xl mb-2 text-gray-300"></i>
                                        <p>No feedbacks found for "<span id="feedbacksSearchTerm"></span>"</p>
                                    </td>`;
                    tbody.appendChild(tr);
                }
                document.getElementById('feedbacksSearchTerm').innerText = searchTerm;
                noResultRow.style.display = '';
            } else if (noResultRow) {
                noResultRow.style.display = 'none';
            }
        });
        
        // ============ MODAL FUNCTIONS ============
        function openEditModal(id) {
            fetch('/support/students/' + id + '/edit')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_first_name').value = data.first_name;
                    document.getElementById('edit_last_name').value = data.last_name;
                    document.getElementById('edit_email').value = data.email;
                    document.getElementById('edit_course_year').value = data.course_year;
                    document.getElementById('editForm').action = '/support/students/' + id;
                    document.getElementById('editModal').classList.remove('hidden');
                    document.getElementById('editModal').classList.add('flex');
                });
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
        
        function openUpdateModal(id) {
            document.getElementById('updateForm').action = '/support/request/' + id + '/status';
            document.getElementById('updateModal').classList.remove('hidden');
            document.getElementById('updateModal').classList.add('flex');
        }
        
        function closeUpdateModal() {
            document.getElementById('updateModal').classList.add('hidden');
            document.getElementById('updateModal').classList.remove('flex');
        }
        
        function resetPassword(id, name) {
            Swal.fire({
                title: 'Reset Password',
                html: `<div style="text-align: left;"><p>Reset password for <strong>${name}</strong></p><input type="password" id="newPassword" class="swal2-input" placeholder="New Password" value="12345678"></div>`,
                confirmButtonText: 'Reset',
                cancelButtonText: 'Cancel',
                showCancelButton: true,
                preConfirm: () => {
                    const password = document.getElementById('newPassword').value;
                    if (!password) {
                        Swal.showValidationMessage('Please enter a password');
                        return false;
                    }
                    return { password: password };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("support.reset.password") }}';
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    let studentId = document.createElement('input');
                    studentId.type = 'hidden';
                    studentId.name = 'student_id';
                    studentId.value = id;
                    let newPassword = document.createElement('input');
                    newPassword.type = 'hidden';
                    newPassword.name = 'new_password';
                    newPassword.value = result.value.password;
                    form.appendChild(csrf);
                    form.appendChild(studentId);
                    form.appendChild(newPassword);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        function resetAccountId(id) {
            Swal.fire({
                title: 'Reset Account ID?',
                text: 'This will generate a new Account ID for the student. Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, reset it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("support.reset.accountid") }}';
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    let studentId = document.createElement('input');
                    studentId.type = 'hidden';
                    studentId.name = 'student_id';
                    studentId.value = id;
                    form.appendChild(csrf);
                    form.appendChild(studentId);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function toggleActive(id, name, currentStatus) {
            const action = currentStatus === 'active' ? 'deactivate' : 'activate';
            Swal.fire({
                title: `${action === 'deactivate' ? 'Deactivate' : 'Activate'} Student?`,
                text: `Are you sure you want to ${action} ${name}'s account?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'deactivate' ? '#d33' : '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Yes, ${action} it!`,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/support/toggle-active/' + id;
                    let csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function viewFeedback(id, message, student, rating, category, status, admin_response) {
            const stars = '★'.repeat(rating) + '☆'.repeat(5-rating);
            document.getElementById('viewContent').innerHTML = `
                <div class="space-y-3">
                    <div><strong>Student:</strong> ${student}</div>
                    <div><strong>Rating:</strong> <span class="text-yellow-500">${stars}</span></div>
                    <div><strong>Category:</strong> ${category}</div>
                    <div><strong>Status:</strong> ${status}</div>
                    <div><strong>Message:</strong><br><p class="text-gray-600 mt-1 p-2 bg-gray-50 rounded">${message}</p></div>
                    ${admin_response ? `<div><strong>Admin Response:</strong><br><p class="text-green-600 mt-1 p-2 bg-green-50 rounded">${admin_response}</p></div>` : ''}
                </div>
            `;
            document.getElementById('viewModal').classList.remove('hidden');
            document.getElementById('viewModal').classList.add('flex');
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
            document.getElementById('viewModal').classList.remove('flex');
        }
        
        function respondFeedback(id) {
            document.getElementById('respondForm').action = '/support/feedback/' + id + '/respond';
            document.getElementById('respondModal').classList.remove('hidden');
            document.getElementById('respondModal').classList.add('flex');
        }
        
        function closeRespondModal() {
            document.getElementById('respondModal').classList.add('hidden');
            document.getElementById('respondModal').classList.remove('flex');
        }

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
</body>
</html>