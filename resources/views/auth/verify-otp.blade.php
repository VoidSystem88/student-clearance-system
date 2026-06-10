<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Verify OTP - Clearance System</title>
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

        <!-- Verify OTP Form -->
        <div class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-8 w-full max-w-md card-hover border border-white/20">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-white">Verify OTP</h2>
                    <p class="text-white/70 text-sm mt-1">Enter the 6-digit code sent to your email</p>
                </div>

                @if(session('error'))
                    <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-3 rounded-lg mb-4 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-green-500/20 border border-green-500/50 text-green-200 p-3 rounded-lg mb-4 text-sm">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Test OTP Display (for development only) -->
                @if(session('reset_otp'))
                    <div class="bg-yellow-500/20 border border-yellow-500/50 text-yellow-200 p-3 rounded-lg mb-4 text-sm text-center">
                        <i class="fas fa-code mr-2"></i> 
                        <span class="text-xs">Test Mode - Your OTP is:</span>
                        <div class="text-2xl font-bold tracking-widest mt-1">{{ session('reset_otp') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.otp.verify') }}">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-white/80 text-sm font-medium mb-2">
                            <i class="fas fa-key mr-2"></i> One-Time Password (OTP)
                        </label>
                        <div class="relative">
                            <i class="fas fa-code absolute left-3 top-3 text-white/50"></i>
                            <input type="text" name="otp" maxlength="6" 
                                   class="w-full pl-10 pr-3 py-2 bg-white/20 border border-white/30 rounded-lg text-white text-center text-2xl tracking-widest placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                   placeholder="000000" required autofocus>
                        </div>
                        <p class="text-white/50 text-xs mt-2">
                            <i class="fas fa-clock mr-1"></i> OTP expires in 10 minutes
                        </p>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Verify OTP
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-white/60 text-sm">
                        Didn't receive OTP? 
                        <a href="{{ route('password.forgot') }}" class="text-blue-400 hover:text-blue-300 transition">
                            Try again
                        </a>
                    </p>
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