<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Reset Password - Clearance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .hero-bg {
            background-image: url('{{ asset('images/background.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.5) 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="hero-bg min-h-screen relative">
    <div class="relative z-10 min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white/10 backdrop-blur-md border-b border-white/20">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-xl text-white">Clearance System</h1>
                        <p class="text-xs text-white/70">TCC - Student Portal</p>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </nav>

        <!-- Reset Password Form -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-8 w-full max-w-md card-hover border border-white/20">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lock-open text-white text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Set New Password</h2>
                    <p class="text-white/70 text-sm mt-1">Create a strong new password for your account</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-3 rounded-lg mb-4 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-3 rounded-lg mb-4 text-sm">
                        @foreach($errors->all() as $error)
                            <p><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.reset') }}">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-white/80 text-sm font-medium mb-2">
                            <i class="fas fa-lock mr-2"></i> New Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-key absolute left-3 top-3 text-white/50"></i>
                            <input type="password" name="password" 
                                   class="w-full pl-10 pr-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Enter new password" required autofocus>
                        </div>
                        <p class="text-white/50 text-xs mt-1">Minimum 6 characters</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-white/80 text-sm font-medium mb-2">
                            <i class="fas fa-check-circle mr-2"></i> Confirm Password
                        </label>
                        <div class="relative">
                            <i class="fas fa-check absolute left-3 top-3 text-white/50"></i>
                            <input type="password" name="password_confirmation" 
                                   class="w-full pl-10 pr-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="Confirm your new password" required>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-white/70 hover:text-white text-sm transition">
                        <i class="fas fa-arrow-left mr-1"></i> Remember your password? Login here
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-black/30 backdrop-blur-md text-center py-3 text-white/60 text-sm">
            <p>© {{ date('Y') }} Student Clearance System - Tagoloan Community College. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>