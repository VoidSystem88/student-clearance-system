<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Clearance System | TCC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body, html { min-height: 100vh; width: 100%; }

    @media (max-width: 768px) {
        body { background-image: url('/images/logindesk.png') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important; background-attachment: fixed !important; }
    }
    @media (min-width: 769px) {
        body { background-image: url('/images/login.png') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important; background-attachment: fixed !important; }
    }

    .hero-bg { position: relative; min-height: 100vh; }
    .hero-bg::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%); pointer-events: none; }

    /* ============ SIDE PANELS ============ */
    .side-panel {
        position: fixed;
        top: 0;
        right: -500px;
        width: 500px;
        height: 100%;
        background-color: rgba(10, 15, 26, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: -5px 0 30px rgba(0, 0, 0, 0.5);
        z-index: 1000;
        transition: right 0.3s ease-in-out;
        overflow-y: auto;
        border-left: 1px solid rgba(59, 130, 246, 0.2);
    }
    @media (max-width: 768px) {
        .side-panel { backdrop-filter: none !important; background-color: rgba(0, 0, 0, 0.9) !important; width: 100%; right: -100%; }
        .side-panel .p-6 { backdrop-filter: none !important; background-color: rgba(0, 0, 0, 0.85) !important; margin: 8px !important; border-radius: 16px !important; }
    }
    .side-panel.open { right: 0; }

    #loginPanel .p-6, #staffLoginPanel .p-6, #registerPanel .p-6,
    #forgotPanel .p-6, #otpPanel .p-6, #resetPanel .p-6,
    #bugReportPanel .p-6, #emailVerifyPanel .p-6, #newEmailVerifyPanel .p-6 {
        background-color: rgba(10, 15, 26, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        margin: 16px;
        height: calc(100% - 32px);
        overflow-y: auto;
    }
    #loginPanel { background-color: #080818 !important; backdrop-filter: none !important; }
    #staffLoginPanel { background-color: #080818 !important; backdrop-filter: none !important; }
    #registerPanel, #forgotPanel, #otpPanel, #resetPanel, #bugReportPanel,
    #emailVerifyPanel, #newEmailVerifyPanel {
        background-color: rgba(10, 15, 26, 0.95) !important;
        backdrop-filter: blur(10px);
    }
    @media (max-width: 768px) {
        #registerPanel, #forgotPanel, #otpPanel, #resetPanel, #bugReportPanel,
        #emailVerifyPanel, #newEmailVerifyPanel {
            backdrop-filter: none !important;
        }
    }

    /* ============ FORM STYLES ============ */
    .panel-input {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 10px;
        font-size: 14px;
        color: #e2e8f0;
        transition: all 0.3s;
    }
    @media (max-width: 768px) {
        .panel-input { padding: 10px 12px !important; font-size: 13px !important; }
        .btn-primary, .btn-secondary { padding: 10px 16px !important; font-size: 14px !important; }
    }
    .panel-input:focus { outline: none; border-color: #3b82f6; background: rgba(255, 255, 255, 0.12); }
    .panel-input::placeholder { color: rgba(226, 232, 240, 0.4); }

    #loginPanel .panel-input, #staffLoginPanel .panel-input,
    #loginPanel .panel-select, #staffLoginPanel .panel-select {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(4px);
        border-color: rgba(59, 130, 246, 0.4);
    }

    .panel-select {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 10px;
        font-size: 14px;
        color: #e2e8f0;
    }
    .panel-select option { background: #0f2a3f; color: #e2e8f0; }

    .panel-label { display: block; color: #94a3b8; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem; }
    .panel-subtitle { color: #94a3b8; }
    .panel-text { color: #cbd5e1; }
    .panel-link { color: #60a5fa; }
    .panel-link:hover { color: #93c5fd; }
    .panel-close { color: #94a3b8; transition: color 0.2s; cursor: pointer; font-size: 28px; line-height: 1; }
    .panel-close:hover { color: white; }
    .panel-divider { border-top-color: rgba(59, 130, 246, 0.2); }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        transition: all 0.3s ease;
    }
    .btn-primary:hover { transform: translateY(-2px); }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* ============ STAFF LOGIN BUTTON ============ */
    .btn-staff {
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        transition: all 0.3s ease;
    }
    .btn-staff:hover { transform: translateY(-2px); }

    /* ============ OTHER STYLES ============ */
    .report-btn {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid rgba(239, 68, 68, 0.4);
        transition: all 0.3s ease;
    }
    .report-btn:hover { background: rgba(239, 68, 68, 0.4); transform: translateY(-2px); }

    .step-circle {
        width: 56px; height: 56px;
        background: rgba(59, 130, 246, 0.25);
        border: 2px solid #60a5fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem auto;
    }
    .step-circle span { font-size: 1.5rem; font-weight: bold; color: #60a5fa; }

    .fab {
        position: fixed;
        bottom: 158px;
        right: 20px;
        width: 130px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
        transition: all 0.3s ease;
        z-index: 10;
        animation: gentlePulse 2s infinite;
        opacity: 1;
        visibility: visible;
    }
    .fab.fab-hidden { opacity: 0; visibility: hidden; transform: translateY(20px); }
    .fab-content { display: flex; align-items: center; gap: 8px; color: white; font-weight: 600; font-size: 0.8rem; }
    @keyframes gentlePulse { 0%, 100% { box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); } 50% { box-shadow: 0 4px 25px rgba(59, 130, 246, 0.7); } }
    @media (min-width: 768px) { .fab { display: none !important; } }

    .verify-badge {
        background: rgba(245, 158, 11, 0.2);
        border: 1px solid rgba(245, 158, 11, 0.5);
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 11px;
        color: #fbbf24;
    }

    .language-footer {
        position: fixed;
        bottom: 20px;
        left: 0;
        right: 0;
        text-align: center;
        z-index: 10;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }
    .language-footer.visible { opacity: 1; visibility: visible; }
    .lang-selector-footer {
        display: inline-flex;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 6px 12px;
        border: 1px solid rgba(255,255,255,0.2);
        gap: 8px;
    }
    .lang-btn-footer {
        background: transparent;
        border: none;
        padding: 6px 12px;
        border-radius: 30px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
        color: white;
        font-size: 12px;
    }
    .lang-btn-footer.active { background: #3b82f6; color: white; }
    .lang-btn-footer:hover:not(.active) { background: rgba(59, 130, 246, 0.5); }
    @media (min-width: 768px) { .language-footer { opacity: 1 !important; visibility: visible !important; } }
    @media (max-width: 768px) { .language-footer { bottom: 10px; } .lang-btn-footer { padding: 4px 10px; font-size: 11px; } }

    body.has-panel-open .fab,
    body.has-panel-open .language-footer {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }

    .panel-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 10;
        display: none;
    }
    .panel-overlay.show { display: block; }

    #loginPanel .panel-label, #loginPanel .panel-subtitle, #loginPanel .panel-text,
    #loginPanel .panel-link, #loginPanel .login-welcome, #loginPanel h2,
    #loginPanel .remember-text, #loginPanel .forgot-password-text,
    #loginPanel .no-account-text, #loginPanel .register-here-text,
    #loginPanel .text-white, #loginPanel .text-gray-400, #loginPanel .text-blue-400,
    #loginPanel .text-sm {
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    }

    #staffLoginPanel .panel-label, #staffLoginPanel .panel-subtitle,
    #staffLoginPanel .panel-text, #staffLoginPanel .panel-link,
    #staffLoginPanel .login-welcome, #staffLoginPanel h2,
    #staffLoginPanel .text-white, #staffLoginPanel .text-gray-400,
    #staffLoginPanel .text-blue-400, #staffLoginPanel .text-sm {
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    }

    #loginPanel .w-16.h-16.rounded-full,
    #staffLoginPanel .w-16.h-16.rounded-full {
        background: rgba(0, 0, 0, 0.5) !important;
        border-color: rgba(59, 130, 246, 0.6);
    }

    /* OTP */
    .otp-container {
        display: flex !important;
        justify-content: center !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
        padding: 4px 2px !important;
    }
    .otp-digit, .new-otp-digit {
        flex-shrink: 0 !important;
        text-align: center !important;
        font-weight: bold !important;
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(168, 85, 247, 0.4) !important;
        border-radius: 12px !important;
        color: white !important;
        transition: all 0.2s ease;
    }
    .otp-digit:focus, .new-otp-digit:focus {
        outline: none !important;
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 2px rgba(168, 85, 247, 0.3) !important;
    }
    @media (max-width: 360px) { .otp-digit, .new-otp-digit { width: 42px !important; height: 42px !important; font-size: 16px !important; } .otp-container { gap: 5px !important; } }
    @media (min-width: 361px) and (max-width: 480px) { .otp-digit, .new-otp-digit { width: 48px !important; height: 48px !important; font-size: 18px !important; } .otp-container { gap: 6px !important; } }
    @media (min-width: 481px) and (max-width: 640px) { .otp-digit, .new-otp-digit { width: 52px !important; height: 52px !important; font-size: 20px !important; } .otp-container { gap: 7px !important; } }
    @media (min-width: 641px) and (max-width: 768px) { .otp-digit, .new-otp-digit { width: 56px !important; height: 56px !important; font-size: 22px !important; } .otp-container { gap: 8px !important; } }
    @media (min-width: 769px) { .otp-digit, .new-otp-digit { width: 60px !important; height: 60px !important; font-size: 24px !important; } .otp-container { gap: 10px !important; } }

    .sliding-indicator {
        position: absolute;
        bottom: -2px;
        left: 0;
        height: 2px;
        background: linear-gradient(90deg, #a855f7, #d8b4fe);
        transition: all 0.3s;
        border-radius: 2px;
    }
    .forgot-tabs-container { position: relative; }
    .tab-button { transition: all 0.3s ease; cursor: pointer; background: transparent; }
    .hidden { display: none !important; }

    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        animation: slideInRight 0.3s ease-out;
        backdrop-filter: blur(10px);
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
    }
    @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    .toast-success { background: linear-gradient(135deg, rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.95)); border-left: 4px solid #10b981; color: white; }
    .toast-error { background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95)); border-left: 4px solid #ef4444; color: white; }
    .toast-info { background: linear-gradient(135deg, rgba(59, 130, 246, 0.95), rgba(37, 99, 235, 0.95)); border-left: 4px solid #3b82f6; color: white; }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        z-index: 10001;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .loading-spinner {
        background: linear-gradient(135deg, #0a0f1a, #0f2a3f);
        border-radius: 20px;
        padding: 30px 40px;
        text-align: center;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .loading-spinner .spinner {
        width: 50px;
        height: 50px;
        border: 3px solid rgba(59, 130, 246, 0.3);
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto 15px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Staff login panel text shadow */
    #staffLoginPanel .panel-label,
    #staffLoginPanel .panel-subtitle,
    #staffLoginPanel .panel-text,
    #staffLoginPanel .panel-link,
    #staffLoginPanel .login-welcome,
    #staffLoginPanel h2,
    #staffLoginPanel .text-white,
    #staffLoginPanel .text-gray-400,
    #staffLoginPanel .text-blue-400,
    #staffLoginPanel .text-sm {
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    }
</style>
</head>
<body class="hero-bg min-h-screen relative">

    <div id="panelOverlay" class="panel-overlay"></div>

    <div class="relative z-10 min-h-screen flex flex-col pb-20 md:pb-0">
        <nav class="bg-transparent py-4">
            <div class="container mx-auto px-4 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="font-bold text-white text-lg">Clearance<span class="text-blue-400">System</span></span>
                        <p class="text-xs text-white/50">TCC</p>
                    </div>
                </div>
                <button onclick="openBugReportPanel()" class="report-btn w-10 h-10 rounded-full flex items-center justify-center text-red-400 hover:text-red-300 transition" title="Report Issue">
                    <i class="fas fa-bug text-xl"></i>
                </button>
            </div>
        </nav>

        <div class="flex-1 flex items-center justify-center px-4 py-8 md:py-12">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-block px-4 py-1 bg-blue-500/20 backdrop-blur rounded-full text-blue-200 text-sm mb-4">Portal</div>
                <div class="relative inline-block mx-auto mb-4">
                    <div class="absolute -top-3 -right-1 md:-top-4 md:-right-3 z-10">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-[10px] md:text-xs font-bold px-2 py-0.5 md:px-3 md:py-1 rounded-md shadow-lg flex items-center gap-1">
                            <i class="fas fa-flask text-[8px] md:text-[10px]"></i>
                            <span>PUBLIC BETA 2.0</span>
                        </div>
                    </div>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold bg-gradient-to-r from-white via-blue-100 to-blue-300 bg-clip-text text-transparent">Clearance System</h1>
                </div>
                <p class="text-lg md:text-xl text-white/80 mb-8 max-w-2xl mx-auto">Track, Submit, and Get Cleared — All Online, All in One Place</p>

                <!-- ===== THREE LOGIN BUTTONS ===== -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
    <!-- STUDENT LOGIN -->
    <button onclick="openLoginPanel()" class="btn-primary px-8 py-3 rounded-xl font-semibold text-white flex items-center justify-center gap-2 min-w-[180px] w-full sm:w-auto">
        <i class="fas fa-user-graduate"></i> Student Login
    </button>

    <!-- REGISTER -->
    <button onclick="openRegisterPanel()" class="btn-primary px-8 py-3 rounded-xl font-semibold text-white flex items-center justify-center gap-2 min-w-[180px] w-full sm:w-auto">
        <i class="fas fa-user-plus"></i> Register
    </button>
</div>

<!-- STAFF LOGIN - text link -->
<div class="mt-4 text-center">
    <button onclick="openStaffLoginPanel()" class="text-white/70 hover:text-white text-sm font-medium transition">
        <i class="fas fa-users-cog mr-1"></i> Staff / Admin Login
    </button>
</div>
            </div>
        </div>

        <!-- FEATURES -->
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="rounded-2xl p-6 text-center bg-white/5 backdrop-blur">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-clock text-2xl text-blue-400"></i></div>
                    <h3 class="text-lg font-semibold text-white mb-2">Real-time Tracking</h3>
                    <p class="text-white/60 text-sm">Monitor your clearance status instantly</p>
                </div>
                <div class="rounded-2xl p-6 text-center bg-white/5 backdrop-blur">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-file-pdf text-2xl text-blue-400"></i></div>
                    <h3 class="text-lg font-semibold text-white mb-2">Digital Clearance</h3>
                    <p class="text-white/60 text-sm">Generate and print clearance slip</p>
                </div>
                <div class="rounded-2xl p-6 text-center bg-white/5 backdrop-blur">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-shield-alt text-2xl text-blue-400"></i></div>
                    <h3 class="text-lg font-semibold text-white mb-2">Secure & Reliable</h3>
                    <p class="text-white/60 text-sm">Secure OTP verification</p>
                </div>
            </div>
        </div>

        <!-- HOW IT WORKS -->
        <div id="howItWorksSection" class="container mx-auto px-4 py-8">
            <h2 class="text-2xl md:text-3xl font-bold text-center text-white mb-8">How It Works</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="text-center"><div class="step-circle mx-auto"><span>1</span></div><h4 class="font-semibold text-white mb-1">Register Account</h4><p class="text-white/50 text-sm">Create your student account</p></div>
                <div class="text-center"><div class="step-circle mx-auto"><span>2</span></div><h4 class="font-semibold text-white mb-1">Submit Requirements</h4><p class="text-white/50 text-sm">Upload your documents online</p></div>
                <div class="text-center"><div class="step-circle mx-auto"><span>3</span></div><h4 class="font-semibold text-white mb-1">Get Cleared</h4><p class="text-white/50 text-sm">Receive your digital clearance</p></div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
<!-- ==================== STUDENT LOGIN PANEL ==================== -->
<!-- ============================================================ -->
<div id="loginPanel" class="side-panel">
    <div class="p-6">
        <div class="flex justify-end mb-4">
            <button onclick="closeLoginPanel()" class="panel-close">&times;</button>
        </div>
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-blue-500/30">
                <i class="fas fa-user-graduate text-blue-400 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Student Login</h2>
            <p class="panel-subtitle text-sm">Enter your Student ID and password</p>
        </div>

        <div id="unverifiedWarning" class="hidden mb-4 p-3 bg-yellow-500/20 border border-yellow-500/30 rounded-lg text-yellow-400 text-sm">
            <i class="fas fa-exclamation-triangle mr-2"></i> Your email is not verified. Please verify before logging in.
        </div>

        <form id="loginForm">
            @csrf
            <div class="mb-4">
                <label class="panel-label">
                    <i class="fas fa-id-card mr-1"></i> Student ID <span class="text-red-400">*</span>
                </label>
                <input type="text" 
                       name="username" 
                       id="login_username" 
                       class="panel-input" 
                       placeholder="2023-00123" 
                       required 
                       maxlength="10"
                       autocomplete="off">
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Format: <strong>YYYY-XXXXX</strong> (e.g., 2023-00123)
                </p>
                <p id="studentIdError" class="text-xs text-red-400 mt-1 hidden">
                    <i class="fas fa-exclamation-circle"></i> Invalid format. Use: YYYY-XXXXX
                </p>
            </div>

            <div class="mb-4">
                <label class="panel-label">
                    <i class="fas fa-lock mr-1"></i> Password <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="login_password" 
                           class="panel-input pr-10" 
                           placeholder="Enter your password" 
                           required>
                    <button type="button" 
                            onclick="togglePasswordVisibility('login_password', 'login_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition">
                        <i id="login_eye_icon" class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4 flex items-center justify-between">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="remember" 
                           id="remember_me" 
                           class="w-4 h-4 text-blue-600 bg-white/10 border border-white/30 rounded focus:ring-blue-500 focus:ring-offset-0">
                    <span class="ml-2 text-sm text-white/70">Remember Me</span>
                </label>
                <button type="button" 
                        onclick="openForgotPanel()" 
                        class="text-sm text-blue-400 hover:text-blue-300 transition">
                    Forgot Password?
                </button>
            </div>

            <button type="submit" 
                    id="loginSubmitBtn" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="mt-6 pt-4 panel-divider border-t text-center">
            <p class="panel-text text-sm">Don't have an account?</p>
            <button onclick="openRegisterPanel()" class="panel-link text-sm font-medium hover:underline">
                Register here
            </button>
        </div>
    </div>
</div>

    <!-- ==================== STAFF LOGIN PANEL ==================== -->
<div id="staffLoginPanel" class="side-panel">
    <div class="p-6">
        <div class="flex justify-end mb-4">
            <button onclick="closeStaffLoginPanel()" class="panel-close">&times;</button>
        </div>
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-purple-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-purple-500/30">
                <i class="fas fa-users-cog text-purple-400 text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Staff / Admin Login</h2>
            <p class="panel-subtitle text-sm">Login to your staff, admin, or support account</p>
        </div>

        <!-- ✅ ITO ANG TAMA - WALANG action at method -->
        <form id="staffLoginForm">
            @csrf
            <div class="mb-4">
                <label class="panel-label">Email Address</label>
                <input type="email" 
                       name="email"      
                       id="staff_login_email" 
                       class="panel-input" 
                       placeholder="Enter your email address" 
                       required>
            </div>
            <div class="mb-4">
                <label class="panel-label">Password</label>
                <div class="relative">
                    <input type="password" 
                           name="password" 
                           id="staff_login_password" 
                           class="panel-input pr-10" 
                           placeholder="Enter your password" 
                           required>
                    <button type="button" 
                            onclick="togglePasswordVisibility('staff_login_password', 'staff_login_eye_icon')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white transition">
                        <i id="staff_login_eye_icon" class="fas fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            <button type="submit" id="staffLoginSubmitBtn" class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">Login</button>
        </form>
        <div class="mt-6 pt-4 panel-divider border-t text-center">
            <button onclick="closeStaffLoginPanel()" class="panel-link text-sm hover:underline">← Back</button>
        </div>
    </div>
</div>

    <!-- ============================================================ -->
    <!-- ==================== REGISTER PANEL ==================== -->
    <!-- ============================================================ -->
    <div id="registerPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeRegisterPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-500/30">
                    <i class="fas fa-user-plus text-green-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Student Registration</h2>
                <p class="panel-subtitle text-sm">Create your account</p>
                <div class="mt-2 inline-flex items-center gap-1 verify-badge">
                    <i class="fas fa-envelope text-xs"></i> Email Verification Required
                </div>
            </div>

            <form id="registerForm">
                @csrf
                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-id-card mr-1"></i> Student ID <span class="text-red-400">*</span></label>
                    <input type="text" name="student_id" id="reg_student_id" class="panel-input" placeholder="Example: 2023-00123" required maxlength="10">
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> Format: YYYY-XXXXX (e.g., 2023-00123)</p>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="panel-label"><i class="fas fa-user mr-1"></i> First Name <span class="text-red-400">*</span></label>
                        <input type="text" name="first_name" id="reg_first_name" class="panel-input" placeholder="Juan" required>
                    </div>
                    <div>
                        <label class="panel-label"><i class="fas fa-user mr-1"></i> Last Name <span class="text-red-400">*</span></label>
                        <input type="text" name="last_name" id="reg_last_name" class="panel-input" placeholder="Dela Cruz" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-envelope mr-1"></i> Email Address <span class="text-red-400">*</span></label>
                    <input type="email" name="email" id="reg_email" class="panel-input" placeholder="student@tcc.edu.ph" required>
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> A verification code will be sent to this email</p>
                </div>

                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-calendar-alt mr-1"></i> Birthdate <span class="text-red-400">*</span></label>
                    <input type="date" name="birthdate" id="reg_birthdate" class="panel-input" required>
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> Must be 16 years old or above</p>
                </div>

                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-graduation-cap mr-1"></i> Course <span class="text-red-400">*</span></label>
                    <select name="course" id="reg_course" class="panel-select" required>
                        <option value="">-- Select Course --</option>
                        @php
                            $activeCourses = App\Models\Course::where('is_active', true)->orderBy('code')->get();
                        @endphp
                        @foreach($activeCourses as $course)
                            <option value="{{ $course->code }}">{{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-layer-group mr-1"></i> Year Level <span class="text-red-400">*</span></label>
                    <select name="year_level" id="reg_year_level" class="panel-select" required>
                        <option value="">-- Select Year --</option>
                        <option value="1st Year">1st Year</option>
                        <option value="2nd Year">2nd Year</option>
                        <option value="3rd Year">3rd Year</option>
                        <option value="4th Year">4th Year</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="panel-label"><i class="fas fa-lock mr-1"></i> Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="reg_password" class="panel-input w-full pr-10" placeholder="Minimum 8 characters" required>
                        <button type="button" onclick="togglePassword('reg_password', 'toggleIcon1')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i id="toggleIcon1" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="panel-label"><i class="fas fa-lock mr-1"></i> Confirm Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="reg_confirm_password" class="panel-input w-full pr-10" placeholder="Repeat your password" required>
                        <button type="button" onclick="togglePassword('reg_confirm_password', 'toggleIcon2')" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <i id="toggleIcon2" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="registerSubmitBtn" class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-2.5 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transition">Register</button>
            </form>

            <div class="mt-4 pt-4 panel-divider border-t text-center">
                <p class="panel-text text-sm">Already have an account?</p>
                <button onclick="openLoginPanel()" class="panel-link text-sm font-medium hover:underline">Login here</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== FORGOT PASSWORD PANEL ==================== -->
    <!-- ============================================================ -->
    <div id="forgotPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeForgotPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-yellow-500/30">
                    <i class="fas fa-key text-yellow-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Forgot Password?</h2>
                <p class="panel-subtitle text-sm">Enter your credentials to reset password</p>
            </div>
            <div class="forgot-tabs-container relative border-b border-gray-700 mb-6">
                <div class="flex">
                    <button type="button" id="tabAccId" class="tab-button flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2" style="color: #a855f7;"><i class="fas fa-id-card"></i> Account ID</button>
                    <button type="button" id="tabEmail" class="tab-button flex-1 py-2.5 text-sm font-medium flex items-center justify-center gap-2" style="color: #6b7280;"><i class="fas fa-envelope"></i> Email Address</button>
                </div>
                <div id="slidingIndicator" class="sliding-indicator" style="width: 50%;"></div>
            </div>
            <div id="formAccId">
                <form id="forgotForm" onsubmit="return false;">
                    @csrf
                    <div class="mb-4">
                        <label class="panel-label">Student ID</label>
                        <input type="text" name="student_id" id="forgot_student_id" class="panel-input" placeholder="2023-00123" required>
                    </div>
                    <div class="mb-4">
                        <label class="panel-label">Account ID</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <span class="text-gray-400 text-sm font-mono bg-white/10 px-2 py-1 rounded-l-lg">CLR-</span>
                            </div>
                            <input type="text" name="account_id" id="forgot_account_id" class="panel-input pl-24" placeholder="2026-00001" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Enter numbers only (e.g., 2026-00001)</p>
                    </div>
                    <button type="button" id="forgotSubmitBtnAcc" class="w-full bg-yellow-600 text-white py-2.5 rounded-lg font-semibold hover:bg-yellow-700 transition">Send OTP</button>
                </form>
            </div>
            <div id="formEmail" class="hidden">
                <form id="forgotEmailForm" onsubmit="return false;">
                    @csrf
                    <div class="mb-4">
                        <label class="panel-label">Student ID</label>
                        <input type="text" name="student_id" id="forgot_email_student_id" class="panel-input" placeholder="2023-00123" required>
                    </div>
                    <div class="mb-4">
                        <label class="panel-label">Email Address</label>
                        <input type="email" name="email" id="forgot_email" class="panel-input" placeholder="student@tcc.edu.ph" required>
                    </div>
                    <button type="button" id="forgotSubmitBtnEmail" class="w-full bg-yellow-600 text-white py-2.5 rounded-lg font-semibold hover:bg-yellow-700 transition">Send OTP</button>
                </form>
            </div>
            <div class="mt-4 text-center"><button onclick="openLoginPanel()" class="panel-link text-sm hover:underline">← Back to Login</button></div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== OTP PANEL ==================== -->
    <!-- ============================================================ -->
    <div id="otpPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeOtpPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-purple-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-purple-500/30">
                    <i class="fas fa-envelope text-purple-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Verify OTP</h2>
                <p class="panel-subtitle text-sm">Enter the 6-digit code sent to your email</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 text-sm mb-2 text-center">Enter Verification Code</label>
                <div class="otp-container flex justify-center gap-2 md:gap-3">
                    <input type="text" maxlength="1" class="otp-digit" data-index="0">
                    <input type="text" maxlength="1" class="otp-digit" data-index="1">
                    <input type="text" maxlength="1" class="otp-digit" data-index="2">
                    <input type="text" maxlength="1" class="otp-digit" data-index="3">
                    <input type="text" maxlength="1" class="otp-digit" data-index="4">
                    <input type="text" maxlength="1" class="otp-digit" data-index="5">
                </div>
                <input type="hidden" name="otp" id="otp_hidden">
            </div>

            <button type="button" id="otpVerifyBtn" class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">Verify OTP</button>

            <div class="mt-4 text-center">
                <button onclick="openForgotPanel()" class="panel-link text-sm hover:underline">← Back</button>
            </div>
            <div class="mt-3 text-center">
                <button id="resendOtpBtn" class="text-gray-400 text-xs hover:text-purple-400 transition">Didn't receive code? Resend</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== RESET PANEL ==================== -->
    <!-- ============================================================ -->
    <div id="resetPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeResetPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-green-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-500/30">
                    <i class="fas fa-lock text-green-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Reset Password</h2>
                <p class="panel-subtitle text-sm">Create a new password</p>
            </div>
            <form id="resetForm">
                @csrf
                <div class="mb-3"><label class="panel-label">New Password</label><input type="password" name="password" id="reset_password" class="panel-input" placeholder="Minimum 8 characters" required></div>
                <div class="mb-4"><label class="panel-label">Confirm Password</label><input type="password" name="password_confirmation" id="reset_confirm_password" class="panel-input" placeholder="Repeat new password" required></div>
                <button type="submit" class="w-full bg-green-600 text-white py-2.5 rounded-lg font-semibold hover:bg-green-700 transition">Reset Password</button>
            </form>
            <div class="mt-4 text-center"><button onclick="openLoginPanel()" class="panel-link text-sm hover:underline">← Back to Login</button></div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== BUG REPORT PANEL ==================== -->
    <!-- ============================================================ -->
    <div id="bugReportPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeBugReportPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-red-500/30">
                    <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Report an Issue</h2>
                <p class="text-gray-400 text-sm">Having trouble? Let us know and we'll help you ASAP.</p>
            </div>

            <form id="bugReportFormPanel">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1"><i class="fas fa-user-circle mr-1"></i> Your Name <span class="text-gray-500 text-xs">(Optional)</span></label>
                        <input type="text" name="name" id="panel_bug_name" class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:outline-none" placeholder="Juan Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1"><i class="fas fa-envelope mr-1"></i> Email Address <span class="text-red-400">*</span></label>
                        <input type="email" name="email" id="panel_bug_email" class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:outline-none" placeholder="student@email.com" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1"><i class="fas fa-id-card mr-1"></i> Student ID <span class="text-gray-500 text-xs">(Optional)</span></label>
                        <input type="text" name="student_id" id="panel_bug_student_id" class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:outline-none" placeholder="2023-00123">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-1"><i class="fas fa-tags mr-1"></i> Issue Type <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <button type="button" id="issueTypeButton" class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-white text-left flex items-center justify-between focus:border-red-500 focus:outline-none">
                                <span id="selectedIssueType" class="flex items-center gap-2">
                                    <i class="fas fa-bug text-red-400"></i>
                                    <span>Select Issue Type</span>
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                            </button>
                            <div id="issueTypeDropdown" class="hidden absolute z-20 w-full mt-1 bg-gray-800 border border-gray-600 rounded-lg shadow-lg overflow-hidden">
                                <div class="issue-option px-4 py-2 hover:bg-red-600/20 cursor-pointer flex items-center gap-2 transition" data-value="login_issue"><i class="fas fa-sign-in-alt text-blue-400 w-5"></i><span class="text-white">Login Issue</span></div>
                                <div class="issue-option px-4 py-2 hover:bg-red-600/20 cursor-pointer flex items-center gap-2 transition" data-value="registration_issue"><i class="fas fa-user-plus text-green-400 w-5"></i><span class="text-white">Registration Issue</span></div>
                                <div class="issue-option px-4 py-2 hover:bg-red-600/20 cursor-pointer flex items-center gap-2 transition" data-value="bug"><i class="fas fa-bug text-red-400 w-5"></i><span class="text-white">Bug / Error</span></div>
                                <div class="issue-option px-4 py-2 hover:bg-red-600/20 cursor-pointer flex items-center gap-2 transition" data-value="otp_issue"><i class="fas fa-envelope text-yellow-400 w-5"></i><span class="text-white">OTP Issue</span></div>
                                <div class="issue-option px-4 py-2 hover:bg-red-600/20 cursor-pointer flex items-center gap-2 transition" data-value="other"><i class="fas fa-question-circle text-gray-400 w-5"></i><span class="text-white">Other</span></div>
                            </div>
                        </div>
                        <input type="hidden" name="type" id="panel_bug_type" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-400 text-sm mb-1"><i class="fas fa-comment-dots mr-1"></i> Describe Your Issue <span class="text-red-400">*</span></label>
                    <textarea name="message" id="panel_bug_message" rows="5" class="w-full px-4 py-2 bg-white/10 border border-gray-600 rounded-lg text-white focus:border-red-500 focus:outline-none" placeholder="Please describe the issue in detail..."></textarea>
                </div>

                <div id="panelBugMessage" class="hidden mb-4 p-3 rounded-lg text-sm"></div>

                <button type="button" id="panelBugSubmitBtn" class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-lg">
                    <i class="fas fa-paper-plane"></i> Submit Report
                </button>
            </form>

            <div class="mt-4 text-center">
                <button onclick="closeBugReportPanel()" class="text-blue-400 text-sm hover:text-blue-300 transition flex items-center justify-center gap-1 mx-auto">
                    <i class="fas fa-arrow-left text-xs"></i> Back
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== EMAIL VERIFICATION PANELS ==================== -->
    <!-- ============================================================ -->

    <!-- NEW EMAIL VERIFICATION -->
    <div id="newEmailVerifyPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeNewEmailVerifyPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-blue-500/30">
                    <i class="fas fa-envelope-open-text text-blue-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Verify Your Email</h2>
                <p class="text-gray-400 text-sm">A 6-digit verification code has been sent to your email</p>
                <p class="text-blue-400 text-xs mt-2" id="newVerifyEmailDisplay"></p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 text-sm mb-2 text-center">Enter Verification Code</label>
                <div class="otp-container flex justify-center gap-2 md:gap-3">
                    <input type="text" maxlength="1" class="new-otp-digit">
                    <input type="text" maxlength="1" class="new-otp-digit">
                    <input type="text" maxlength="1" class="new-otp-digit">
                    <input type="text" maxlength="1" class="new-otp-digit">
                    <input type="text" maxlength="1" class="new-otp-digit">
                    <input type="text" maxlength="1" class="new-otp-digit">
                </div>
            </div>

            <button id="newEmailVerifyBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold transition">Verify Email</button>

            <div class="text-center mt-4">
                <button id="newResendOtpBtn" class="text-blue-400 text-xs hover:text-blue-300 transition">Didn't receive code? Resend</button>
            </div>
            <div class="text-center mt-4">
                <button onclick="closeNewEmailVerifyPanel(); openRegisterPanel();" class="text-gray-400 text-xs hover:text-gray-300 transition">← Back to Registration</button>
            </div>
        </div>
    </div>

    <!-- EMAIL VERIFICATION OTP PANEL -->
    <div id="emailVerifyPanel" class="side-panel">
        <div class="p-6">
            <div class="flex justify-end mb-4"><button onclick="closeEmailVerifyPanel()" class="panel-close">&times;</button></div>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-3 border border-blue-500/30">
                    <i class="fas fa-envelope-open-text text-blue-400 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-white">Verify Your Email</h2>
                <p class="panel-subtitle text-sm" id="verifyEmailAddress">A verification code has been sent to your email</p>
            </div>

            <div class="mb-6">
                <div class="otp-container flex justify-center gap-2 md:gap-3">
                    <input type="text" maxlength="1" class="otp-digit" data-index="0" data-verify="true">
                    <input type="text" maxlength="1" class="otp-digit" data-index="1" data-verify="true">
                    <input type="text" maxlength="1" class="otp-digit" data-index="2" data-verify="true">
                    <input type="text" maxlength="1" class="otp-digit" data-index="3" data-verify="true">
                    <input type="text" maxlength="1" class="otp-digit" data-index="4" data-verify="true">
                    <input type="text" maxlength="1" class="otp-digit" data-index="5" data-verify="true">
                </div>
                <input type="hidden" name="verify_otp" id="verify_otp_hidden">
                <input type="hidden" id="temp_email" value="">
                <input type="hidden" id="temp_student_id" value="">
            </div>

            <button type="button" id="emailVerifyBtn" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Verify Email</button>

            <div class="mt-4 text-center">
                <button id="resendVerifyOtpBtn" class="text-gray-400 text-xs hover:text-blue-400 transition">Didn't receive code? Resend</button>
            </div>
            <div class="mt-4 text-center">
                <button onclick="closeEmailVerifyPanel(); openRegisterPanel();" class="panel-link text-sm hover:underline">← Back to Registration</button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ==================== FAB & LANGUAGE ==================== -->
    <!-- ============================================================ -->

    <div class="fab" onclick="scrollToHowItWorks()">
        <div class="fab-content">
            <i class="fas fa-arrow-down"></i>
            <span>How It Works</span>
        </div>
    </div>

    <div class="language-footer">
        <div class="lang-selector-footer">
            <button class="lang-btn-footer active" data-lang="en">🇺🇸 English</button>
            <button class="lang-btn-footer" data-lang="tl">🇵🇭 Tagalog</button>
        </div>
    </div>

    <script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    let tempRegistrationData = null;
    let currentLang = localStorage.getItem('language') || 'en';

    // ============ PANEL FUNCTIONS ============
    function closeAllPanels() {
        document.getElementById('loginPanel')?.classList.remove('open');
        document.getElementById('staffLoginPanel')?.classList.remove('open');
        document.getElementById('registerPanel')?.classList.remove('open');
        document.getElementById('forgotPanel')?.classList.remove('open');
        document.getElementById('otpPanel')?.classList.remove('open');
        document.getElementById('resetPanel')?.classList.remove('open');
        document.getElementById('bugReportPanel')?.classList.remove('open');
        document.getElementById('emailVerifyPanel')?.classList.remove('open');
        document.getElementById('newEmailVerifyPanel')?.classList.remove('open');
        document.getElementById('panelOverlay')?.classList.remove('show');
        document.body.style.overflow = '';
        document.body.classList.remove('has-panel-open');
        setTimeout(() => { checkFabVisibility(); checkLanguageVisibility(); }, 100);
    }

    window.openLoginPanel = function() {
        closeAllPanels();
        document.getElementById('loginPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeLoginPanel = closeAllPanels;

    window.openStaffLoginPanel = function() {
        closeAllPanels();
        document.getElementById('staffLoginPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeStaffLoginPanel = closeAllPanels;

    window.openRegisterPanel = function() {
        closeAllPanels();
        document.getElementById('registerPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeRegisterPanel = closeAllPanels;

    window.openBugReportPanel = function() {
        closeAllPanels();
        document.getElementById('bugReportPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeBugReportPanel = closeAllPanels;

    window.openForgotPanel = function() {
        closeAllPanels();
        document.getElementById('forgotPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
        setTimeout(() => activateAccIdTab(), 100);
    };
    window.closeForgotPanel = closeAllPanels;

    window.openOtpPanel = function() {
        closeAllPanels();
        document.getElementById('otpPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeOtpPanel = closeAllPanels;

    window.openResetPanel = function() {
        closeAllPanels();
        document.getElementById('resetPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeResetPanel = closeAllPanels;

    window.openNewEmailVerifyPanel = function() {
        closeAllPanels();
        document.getElementById('newEmailVerifyPanel').classList.add('open');
        document.getElementById('panelOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
        document.body.classList.add('has-panel-open');
    };
    window.closeNewEmailVerifyPanel = function() {
        document.getElementById('newEmailVerifyPanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('show');
        document.body.style.overflow = '';
        document.body.classList.remove('has-panel-open');
    };
    window.closeEmailVerifyPanel = closeAllPanels;

    window.scrollToHowItWorks = function() {
        document.getElementById('howItWorksSection')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        setTimeout(() => { checkFabVisibility(); checkLanguageVisibility(); }, 500);
    };

    window.togglePasswordVisibility = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    };

    window.togglePassword = function(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    };

    // ============ AUTO-FORMAT FUNCTIONS ============
    function formatStudentId(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 4) {
            value = value.substring(0, 4) + '-' + value.substring(4, 9);
        }
        if (value.length > 10) {
            value = value.substring(0, 10);
        }
        input.value = value;
    }

    function formatAccountId(input) {
        let rawValue = input.value.replace(/[^0-9]/g, '');
        let year = rawValue.substring(0, 4);
        let seq = rawValue.substring(4, 9);
        let formattedValue = '';
        if (year.length > 0) {
            formattedValue = year;
            if (seq.length > 0) {
                formattedValue = year + '-' + seq;
            }
        }
        input.value = formattedValue;
    }

    document.getElementById('forgot_student_id')?.addEventListener('input', function() { formatStudentId(this); });
    document.getElementById('forgot_account_id')?.addEventListener('input', function() { formatAccountId(this); });
    document.getElementById('forgot_email_student_id')?.addEventListener('input', function() { formatStudentId(this); });

    function validateStudentId(input) {
        const pattern = /^\d{4}-\d{5}$/;
        if (input.value && !pattern.test(input.value)) {
            input.style.borderColor = '#ef4444';
            return false;
        } else {
            input.style.borderColor = 'rgba(59, 130, 246, 0.3)';
            return true;
        }
    }

    // ============ FORGOT PASSWORD TABS ============
    const tabAccId = document.getElementById('tabAccId');
    const tabEmail = document.getElementById('tabEmail');
    const formAccId = document.getElementById('formAccId');
    const formEmail = document.getElementById('formEmail');
    const slidingIndicator = document.getElementById('slidingIndicator');

    function updateSlidingIndicator(activeTab) {
        if (!slidingIndicator) return;
        const activeButton = activeTab === 'accid' ? tabAccId : tabEmail;
        if (activeButton) {
            slidingIndicator.style.width = activeButton.offsetWidth + 'px';
            slidingIndicator.style.transform = `translateX(${activeButton.offsetLeft}px)`;
        }
    }

    function activateAccIdTab() {
        if (tabAccId) tabAccId.style.color = '#a855f7';
        if (tabEmail) tabEmail.style.color = '#6b7280';
        formAccId?.classList.remove('hidden');
        formEmail?.classList.add('hidden');
        updateSlidingIndicator('accid');
    }

    function activateEmailTab() {
        if (tabEmail) tabEmail.style.color = '#a855f7';
        if (tabAccId) tabAccId.style.color = '#6b7280';
        formEmail?.classList.remove('hidden');
        formAccId?.classList.add('hidden');
        updateSlidingIndicator('email');
    }

    if (tabAccId && tabEmail) {
        tabAccId.addEventListener('click', function(e) { e.preventDefault(); activateAccIdTab(); });
        tabEmail.addEventListener('click', function(e) { e.preventDefault(); activateEmailTab(); });
        activateAccIdTab();
    }

    // ============ FAB & LANGUAGE VISIBILITY ============
    const fabButton = document.querySelector('.fab');
    const howItWorksSection = document.getElementById('howItWorksSection');
    const languageFooter = document.querySelector('.language-footer');

    function checkFabVisibility() {
        if (!fabButton || !howItWorksSection) return;
        if (document.body.classList.contains('has-panel-open')) {
            fabButton.classList.add('fab-hidden');
            return;
        }
        const rect = howItWorksSection.getBoundingClientRect();
        if (rect.top <= window.innerHeight - 100) {
            fabButton.classList.add('fab-hidden');
        } else {
            fabButton.classList.remove('fab-hidden');
        }
    }

    function checkLanguageVisibility() {
        if (document.body.classList.contains('has-panel-open')) {
            if (languageFooter) languageFooter.classList.remove('visible');
            return;
        }
        if (window.innerWidth >= 768) {
            if (languageFooter) languageFooter.classList.add('visible');
            return;
        }
        if (howItWorksSection && languageFooter) {
            const rect = howItWorksSection.getBoundingClientRect();
            const windowHeight = window.innerHeight;
            if (rect.top <= windowHeight - 100) {
                languageFooter.classList.add('visible');
            } else {
                languageFooter.classList.remove('visible');
            }
        }
    }

    // ============ TOAST & LOADING ============
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.innerHTML = `<div class="flex items-center p-4 gap-3"><i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} text-xl"></i><div class="flex-1 text-sm font-medium">${message}</div><button onclick="this.closest('.toast-notification').remove()" class="opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button></div>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 5000);
    }

    let loadingOverlay = null;
    function showLoading(msg) {
        if (loadingOverlay) hideLoading();
        loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `<div class="loading-spinner"><div class="spinner"></div><p>${msg}</p></div>`;
        document.body.appendChild(loadingOverlay);
    }
    function hideLoading() {
        if (loadingOverlay) {
            loadingOverlay.remove();
            loadingOverlay = null;
        }
    }

    // ============ OTP DIGITS SETUP ============
    function setupOtpDigits(containerSelector) {
        const digits = document.querySelectorAll(`${containerSelector} .otp-digit`);
        digits.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && index < 5) {
                    digits[index + 1].focus();
                }
                let otp = '';
                digits.forEach(d => otp += d.value);
                if (containerSelector === '#emailVerifyPanel') {
                    document.getElementById('verify_otp_hidden').value = otp;
                } else {
                    document.getElementById('otp_hidden').value = otp;
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    digits[index - 1].focus();
                }
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const pastedDigits = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
                const digitsArray = pastedDigits.split('');
                for (let i = 0; i < digitsArray.length && i < digits.length; i++) {
                    digits[i].value = digitsArray[i];
                }
                let otp = '';
                digits.forEach(d => otp += d.value);
                if (containerSelector === '#emailVerifyPanel') {
                    document.getElementById('verify_otp_hidden').value = otp;
                } else {
                    document.getElementById('otp_hidden').value = otp;
                }
                const lastFilledIndex = Math.min(digitsArray.length, digits.length) - 1;
                if (lastFilledIndex < digits.length - 1) {
                    digits[lastFilledIndex + 1].focus();
                } else {
                    digits[digits.length - 1].focus();
                }
            });
        });
    }

    function setupNewOtpDigits() {
        const digits = document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit');
        digits.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1 && index < 5) {
                    digits[index + 1].focus();
                }
            });
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    digits[index - 1].focus();
                }
            });
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const pastedDigits = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
                const digitsArray = pastedDigits.split('');
                for (let i = 0; i < digitsArray.length && i < digits.length; i++) {
                    digits[i].value = digitsArray[i];
                }
                const lastFilledIndex = Math.min(digitsArray.length, digits.length) - 1;
                if (lastFilledIndex < digits.length - 1) {
                    digits[lastFilledIndex + 1].focus();
                } else {
                    digits[digits.length - 1].focus();
                }
            });
        });
    }

    // ============ STUDENT ID AUTO-FORMAT ============
    const loginUsernameInput = document.getElementById('login_username');
    if (loginUsernameInput) {
        loginUsernameInput.addEventListener('input', function() {
            const errorEl = document.getElementById('studentIdError');
            const pattern = /^\d{4}-\d{5}$/;
            
            const cursorPos = this.selectionStart;
            const oldValue = this.value;
            
            let digits = this.value.replace(/\D/g, '');
            if (digits.length > 9) digits = digits.substring(0, 9);
            
            let formatted = '';
            if (digits.length > 0) {
                formatted = digits.substring(0, 4);
                if (digits.length > 4) {
                    formatted += '-' + digits.substring(4, 9);
                }
            }
            
            this.value = formatted;
            
            if (this.value.length > oldValue.length && cursorPos === 4) {
                this.setSelectionRange(cursorPos + 1, cursorPos + 1);
            }
            
            if (this.value.length === 10) {
                if (pattern.test(this.value)) {
                    this.style.borderColor = 'rgba(59, 130, 246, 0.3)';
                    if (errorEl) errorEl.classList.add('hidden');
                } else {
                    this.style.borderColor = '#ef4444';
                    if (errorEl) errorEl.classList.remove('hidden');
                }
            } else {
                this.style.borderColor = '';
                if (errorEl) errorEl.classList.add('hidden');
            }
        });

        loginUsernameInput.addEventListener('blur', function() {
            const pattern = /^\d{4}-\d{5}$/;
            const errorEl = document.getElementById('studentIdError');
            if (this.value.length > 0 && !pattern.test(this.value)) {
                this.style.borderColor = '#ef4444';
                if (errorEl) errorEl.classList.remove('hidden');
            }
        });

        loginUsernameInput.addEventListener('keydown', function(e) {
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 
                                 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 
                                 'Home', 'End', 'Control', 'Meta', 'Shift'];
            if (allowedKeys.includes(e.key)) return;
            if (!/^[\d-]$/.test(e.key)) e.preventDefault();
        });
    }

    // ============ REGISTER FORM ============
    const studentIdInput = document.getElementById('reg_student_id');
    if (studentIdInput) {
        studentIdInput.addEventListener('input', function() { formatStudentId(this); });
        studentIdInput.addEventListener('blur', function() { validateStudentId(this); });
    }

    document.getElementById('registerForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const password = document.getElementById('reg_password').value;
        const confirm = document.getElementById('reg_confirm_password').value;
        const birthdate = document.getElementById('reg_birthdate').value;
        const studentId = document.getElementById('reg_student_id').value;

        if (!/^\d{4}-\d{5}$/.test(studentId)) {
            showToast('Student ID must be in format: YYYY-XXXXX', 'error');
            return;
        }
        if (password !== confirm) { showToast('Passwords do not match!', 'error'); return; }
        if (password.length < 8) { showToast('Password must be at least 8 characters!', 'error'); return; }
        if (birthdate) {
            const age = new Date().getFullYear() - new Date(birthdate).getFullYear();
            if (age < 16) { showToast('You must be at least 16 years old!', 'error'); return; }
        }

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('student_id', studentId);
        formData.append('first_name', document.getElementById('reg_first_name').value);
        formData.append('last_name', document.getElementById('reg_last_name').value);
        formData.append('email', document.getElementById('reg_email').value);
        formData.append('birthdate', birthdate);
        formData.append('course', document.getElementById('reg_course').value);
        formData.append('year_level', document.getElementById('reg_year_level').value);
        formData.append('password', password);
        formData.append('password_confirmation', confirm);

        showLoading('Creating your account...');
        try {
            const response = await fetch('/register', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) {
                showToast(data.message, 'success');
                tempRegistrationData = { email: document.getElementById('reg_email').value, student_id: studentId };
                document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit').forEach(d => d.value = '');
                closeAllPanels();
                openNewEmailVerifyPanel();
                document.getElementById('registerForm').reset();
            } else {
                showToast(data.message || 'Registration failed', 'error');
            }
        } catch (error) {
            hideLoading();
            showToast('Network error. Please try again.', 'error');
        }
    });

    // ============ ✅ STUDENT LOGIN (SINGLE HANDLER - JSON) ============
    document.getElementById('loginForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('login_username').value.trim();
        const password = document.getElementById('login_password').value;
        const remember = document.getElementById('remember_me')?.checked || false;
        const errorEl = document.getElementById('studentIdError');
        const submitBtn = document.getElementById('loginSubmitBtn');
        
        const studentIdPattern = /^\d{4}-\d{5}$/;
        
        if (errorEl) errorEl.classList.add('hidden');
        document.getElementById('login_username').style.borderColor = '';
        
        if (!studentIdPattern.test(username)) {
            if (errorEl) {
                errorEl.classList.remove('hidden');
                document.getElementById('login_username').style.borderColor = '#ef4444';
            }
            showToast('Invalid Student ID format. Use: YYYY-XXXXX', 'error');
            return;
        }
        
        if (!password) {
            showToast('Please enter your password', 'error');
            return;
        }
        
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        }
        showLoading('Logging in...');
        
        try {
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    remember: remember
                })
            });
            
            const data = await response.json();
            hideLoading();
            
            console.log('Student login response:', data);
            
            if (data.success) {
                if (remember) {
                    saveLoginCredentials(username, password, true);
                } else {
                    localStorage.removeItem('auto_login_user');
                }
                
                if (data.requires_2fa) {
                    showToast('2FA code sent to your email', 'info');
                    setTimeout(() => { window.location.href = data.redirect; }, 800);
                } else {
                    showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => { window.location.href = data.redirect || '/student/dashboard'; }, 500);
                }
            } else if (data.needs_verification) {
                showToast(data.message, 'info');
                setTimeout(() => openNewEmailVerifyPanel(), 1500);
            } else {
                showToast(data.message || 'Invalid credentials', 'error');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
                }
            }
        } catch (error) {
            hideLoading();
            console.error('Student login error:', error);
            showToast('Connection error. Please try again.', 'error');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
            }
        }
    });

    // ============ ✅ STAFF LOGIN (SINGLE HANDLER - JSON) ============
    document.getElementById('staffLoginForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const email = document.getElementById('staff_login_email').value;
    const password = document.getElementById('staff_login_password').value;
    const submitBtn = document.getElementById('staffLoginSubmitBtn');
    
    if (!email || !password) { 
        showToast('Please enter both email and password', 'error'); 
        return; 
    }

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
    showLoading('Logging in...');
    
    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                username: email,
                password: password
            })
        });
        
        // Check content type
        const contentType = response.headers.get('content-type');
        console.log('Response content-type:', contentType);
        console.log('Response status:', response.status);
        
        if (contentType && contentType.includes('application/json')) {
            // JSON response - process normally
            const data = await response.json();
            hideLoading();
            console.log('Staff login response:', data);
            
            if (data.success) {
                if (data.requires_2fa) {
                    showToast('2FA code sent to your email', 'info');
                    setTimeout(() => { window.location.href = data.redirect; }, 800);
                } else {
                    showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => { window.location.href = data.redirect || '/staff/dashboard'; }, 800);
                }
            } else {
                showToast(data.message || 'Invalid credentials', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Login';
            }
        } else {
            // HTML response - try to get the text and see what happened
            const htmlText = await response.text();
            hideLoading();
            console.error('Server returned HTML:', htmlText.substring(0, 500));
            
            // Check if it contains a redirect
            if (htmlText.includes('Redirecting') || htmlText.includes('window.location')) {
                // Try to extract redirect URL
                const match = htmlText.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                if (match) {
                    window.location.href = match[1];
                    return;
                }
            }
            
            // If we got HTML with 200 status, try direct redirect to dashboard
            if (response.status === 200) {
                showToast('Login appears successful! Redirecting...', 'success');
                setTimeout(() => { window.location.href = '/staff/dashboard'; }, 800);
            } else if (response.status === 422) {
                showToast('Validation error. Please check your inputs.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Login';
            } else {
                showToast('Server error. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Login';
            }
        }
    } catch (error) {
        hideLoading();
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Login';
        console.error('Staff login error:', error);
        
        // Last resort: try direct form submission
        if (confirm('AJAX login failed. Try direct form submission?')) {
            const form = document.getElementById('staffLoginForm');
            const tempInput = document.createElement('input');
            tempInput.type = 'hidden';
            tempInput.name = 'username';
            tempInput.value = email;
            form.appendChild(tempInput);
            form.method = 'POST';
            form.action = '/login';
            form.submit();
        } else {
            showToast('Login failed. Please try again.', 'error');
        }
    }
});

    // ============ BUG REPORT ============
    document.getElementById('panelBugSubmitBtn')?.addEventListener('click', async function() {
        const email = document.getElementById('panel_bug_email').value;
        const type = document.getElementById('panel_bug_type').value;
        const message = document.getElementById('panel_bug_message').value;
        if (!email || !type || !message) { showToast('Please fill in all required fields', 'error'); return; }
        showLoading('Submitting report...');
        const formData = new FormData(document.getElementById('bugReportFormPanel'));
        formData.append('_token', csrfToken);
        try {
            const response = await fetch('/bug-report', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) {
                showToast('Report submitted successfully!', 'success');
                setTimeout(() => {
                    closeBugReportPanel();
                    document.getElementById('bugReportFormPanel').reset();
                    document.getElementById('panel_bug_type').value = '';
                    document.getElementById('selectedIssueType').innerHTML = '<i class="fas fa-bug text-red-400"></i> <span>Select Issue Type</span>';
                }, 1500);
            } else {
                showToast(data.message || 'Failed to submit report', 'error');
            }
        } catch (error) {
            hideLoading();
            showToast('Network error. Please try again.', 'error');
        }
    });

    // ============ FORGOT FORMS ============
    document.getElementById('forgotSubmitBtnAcc')?.addEventListener('click', async function(e) {
        e.preventDefault();
        showLoading('Sending OTP...');
        const studentId = document.getElementById('forgot_student_id').value;
        let accountId = document.getElementById('forgot_account_id').value;
        if (accountId && !accountId.startsWith('CLR-')) accountId = 'CLR-' + accountId;
        if (!studentId || !accountId) { hideLoading(); showToast('Please enter both Student ID and Account ID', 'error'); return; }
        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('account_id', accountId);
        formData.append('_token', csrfToken);
        try {
            const response = await fetch('/account/send-code', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast('OTP sent to your email!', 'success'); openOtpPanel(); }
            else { showToast(data.message, 'error'); }
        } catch(err) { hideLoading(); showToast('Network error. Please try again.', 'error'); }
    });

    document.getElementById('forgotSubmitBtnEmail')?.addEventListener('click', async function(e) {
        e.preventDefault();
        showLoading('Sending OTP...');
        const studentId = document.getElementById('forgot_email_student_id').value;
        const email = document.getElementById('forgot_email').value;
        if (!studentId || !email) { hideLoading(); showToast('Please enter both Student ID and Email', 'error'); return; }
        const formData = new FormData();
        formData.append('student_id', studentId);
        formData.append('email', email);
        formData.append('_token', csrfToken);
        try {
            const response = await fetch('/account/send-code-email', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast('OTP sent to your email!', 'success'); openOtpPanel(); }
            else { showToast(data.message, 'error'); }
        } catch(err) { hideLoading(); showToast('Network error. Please try again.', 'error'); }
    });

    // ============ OTP VERIFY ============
    document.getElementById('otpVerifyBtn')?.addEventListener('click', async function() {
        let otp = '';
        document.querySelectorAll('#otpPanel .otp-digit').forEach(digit => otp += digit.value);
        if (otp.length !== 6) { showToast('Please enter the complete 6-digit OTP', 'error'); return; }
        showLoading('Verifying OTP...');
        const formData = new FormData();
        formData.append('otp', otp);
        formData.append('_token', csrfToken);
        try {
            const response = await fetch('/account/check-code', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast('OTP verified!', 'success'); setTimeout(() => openResetPanel(), 1500); }
            else { showToast(data.message || 'Invalid OTP', 'error'); document.querySelectorAll('#otpPanel .otp-digit').forEach(d => d.value = ''); }
        } catch(err) { hideLoading(); showToast('Network error. Please try again.', 'error'); }
    });

    // ============ RESET FORM ============
    document.getElementById('resetForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const password = document.getElementById('reset_password').value;
        const confirm = document.getElementById('reset_confirm_password').value;
        if (password !== confirm) { showToast('Passwords do not match!', 'error'); return; }
        if (password.length < 8) { showToast('Password must be at least 8 characters!', 'error'); return; }
        showLoading('Resetting password...');
        const formData = new FormData(this);
        formData.append('_token', csrfToken);
        try {
            const response = await fetch('/account/update-pass', {
                method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast('Password reset successful!', 'success'); setTimeout(() => { closeAllPanels(); openLoginPanel(); }, 1500); }
            else { showToast(data.message || 'Reset failed', 'error'); }
        } catch { hideLoading(); showToast('Network error.', 'error'); }
    });

    // ============ EMAIL VERIFY BUTTONS ============
    document.getElementById('newEmailVerifyBtn')?.addEventListener('click', async function() {
        let otp = '';
        document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit').forEach(d => otp += d.value);
        if (otp.length !== 6) { showToast('Please enter the complete 6-digit code', 'error'); return; }
        showLoading('Verifying email...');
        try {
            const response = await fetch('/email/verify-otp', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ otp })
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast(data.message, 'success'); setTimeout(() => { closeNewEmailVerifyPanel(); openLoginPanel(); tempRegistrationData = null; }, 1500); }
            else { showToast(data.message, 'error'); document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit').forEach(d => d.value = ''); document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit')[0]?.focus(); }
        } catch { hideLoading(); showToast('Network error.', 'error'); }
    });

    document.getElementById('newResendOtpBtn')?.addEventListener('click', async function() {
        this.disabled = true; this.innerHTML = 'Sending...';
        showLoading('Resending code...');
        try {
            const response = await fetch('/email/verify-resend', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();
            hideLoading();
            if (data.success) { showToast('New verification code sent!', 'success'); document.querySelectorAll('#newEmailVerifyPanel .new-otp-digit').forEach(d => d.value = ''); }
            else { showToast(data.message || 'Failed to resend', 'error'); }
        } catch { hideLoading(); showToast('Network error.', 'error'); }
        finally { this.disabled = false; this.innerHTML = "Didn't receive code? Resend"; }
    });

    // ============ ISSUE TYPE DROPDOWN ============
    const issueTypeButton = document.getElementById('issueTypeButton');
    const issueTypeDropdown = document.getElementById('issueTypeDropdown');
    const selectedIssueTypeSpan = document.getElementById('selectedIssueType');
    const issueTypeInput = document.getElementById('panel_bug_type');
    issueTypeButton?.addEventListener('click', function(e) { e.stopPropagation(); issueTypeDropdown.classList.toggle('hidden'); });
    document.querySelectorAll('.issue-option').forEach(option => {
        option.addEventListener('click', function() {
            const icon = this.querySelector('i').cloneNode(true);
            const text = this.querySelector('span').textContent;
            selectedIssueTypeSpan.innerHTML = '';
            selectedIssueTypeSpan.appendChild(icon);
            selectedIssueTypeSpan.appendChild(document.createTextNode(' ' + text));
            issueTypeInput.value = this.getAttribute('data-value');
            issueTypeDropdown.classList.add('hidden');
        });
    });
    document.addEventListener('click', function(e) {
        if (issueTypeButton && issueTypeDropdown && !issueTypeButton.contains(e.target) && !issueTypeDropdown.contains(e.target)) {
            issueTypeDropdown.classList.add('hidden');
        }
    });

    // ============ OVERLAY AND ESCAPE ============
    document.getElementById('panelOverlay')?.addEventListener('click', function(e) { if (e.target === this) closeAllPanels(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllPanels(); });

    // ============ LANGUAGE ============
    document.querySelectorAll('.lang-btn-footer').forEach(btn => {
        btn.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            document.querySelectorAll('.lang-btn-footer').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            applyLanguage(lang);
        });
    });

    const translations = {
        en: { studentPortal: "Student Portal", publicBeta: "PUBLIC BETA 2.0", mainTitle: "Clearance System", mainSubtitle: "Track, Submit, and Get Cleared — All Online, All in One Place", studentLogin: "Student Login", staffLogin: "Staff Login", register: "Register" },
        tl: { studentPortal: "Student Portal", publicBeta: "PUBLIC BETA", mainTitle: "Clearance System", mainSubtitle: "Subaybayan, I-submit, at Makakuha ng Clearance — Lahat Online, Isang Platform Lang", studentLogin: "Mag-login", staffLogin: "Staff Login", register: "Magrehistro" }
    };

    function applyLanguage(lang) {
        const t = translations[lang];
        if (!t) return;
        document.querySelectorAll('.student-portal-text').forEach(el => el.textContent = t.studentPortal);
        document.querySelectorAll('.public-beta-text').forEach(el => el.textContent = t.publicBeta);
        document.querySelectorAll('.main-title').forEach(el => el.textContent = t.mainTitle);
        document.querySelectorAll('.main-subtitle').forEach(el => el.textContent = t.mainSubtitle);
        document.querySelectorAll('.student-login-text').forEach(el => el.textContent = t.studentLogin);
        document.querySelectorAll('.staff-login-text').forEach(el => el.textContent = t.staffLogin);
        document.querySelectorAll('.register-text').forEach(el => el.textContent = t.register);
        localStorage.setItem('language', lang);
        currentLang = lang;
    }

    function initLanguage() {
        setupNewOtpDigits();
        applyLanguage(currentLang);
        document.querySelectorAll('.lang-btn-footer').forEach(btn => {
            btn.classList.toggle('active', btn.getAttribute('data-lang') === currentLang);
        });
    }

    function saveLoginCredentials(username, password, remember) {
        if (remember) {
            localStorage.setItem('auto_login_user', JSON.stringify({
                username: username, password: password, expires: Date.now() + (7 * 24 * 60 * 60 * 1000)
            }));
        } else {
            localStorage.removeItem('auto_login_user');
        }
    }

    // ============ INITIALIZE ============
    setupOtpDigits('#emailVerifyPanel');
    setupOtpDigits('#otpPanel');
    initLanguage();

    window.addEventListener('scroll', () => { checkFabVisibility(); checkLanguageVisibility(); });
    window.addEventListener('resize', () => { checkFabVisibility(); checkLanguageVisibility(); });
    setTimeout(() => { checkFabVisibility(); checkLanguageVisibility(); }, 100);

    // ============ AUTO-LOGIN (REMEMBER ME) ============
    (function autoLoginIfRemembered() {
        const rememberedUser = localStorage.getItem('auto_login_user');
        if (rememberedUser && !window.location.hash.includes('register')) {
            try {
                const userData = JSON.parse(rememberedUser);
                if (userData.expires && Date.now() < userData.expires) {
                    const usernameField = document.getElementById('login_username');
                    const passwordField = document.getElementById('login_password');
                    const rememberCheckbox = document.getElementById('remember_me');
                    const submitBtn = document.getElementById('loginSubmitBtn');
                    if (usernameField && passwordField && submitBtn) {
                        usernameField.value = userData.username;
                        passwordField.value = userData.password;
                        if (rememberCheckbox) rememberCheckbox.checked = true;
                        setTimeout(() => { submitBtn.click(); }, 500);
                    }
                } else if (userData.expires && Date.now() >= userData.expires) {
                    localStorage.removeItem('auto_login_user');
                }
            } catch(e) { localStorage.removeItem('auto_login_user'); }
        }
    })();

    console.log('All systems ready!');
</script>
</body>
</html>