<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin2FAController;
use App\Http\Controllers\DepartmentStaffController;
use App\Http\Controllers\ClearanceRequestController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\StudentClearanceRequestController;
use App\Http\Controllers\Api\AnnouncementController as ApiAnnouncementController;
use App\Http\Controllers\Api\ClearanceController as ApiClearanceController;
use App\Http\Controllers\Api\StudentController as ApiStudentController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\AssistanceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicAssistanceController;
use App\Http\Controllers\SyncTrackingLogsController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VisitorTracking;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\AIController;


// Public clearance verification
Route::get('/verify/{token}', [App\Http\Controllers\VerifyController::class, 'show'])->name('clearance.verify');
Route::post('/upload-profile-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])
    ->name('upload.profile.photo')
    ->middleware('auth');
// ==================== AI ROUTES (NASA ITAAS LAHAT) ====================
Route::get('/ai-test', function () {
    return response()->json(['message' => 'AI route is working!']);
});

// Change POST to GET for AI
Route::middleware('auth')->group(function () {
    Route::get('/ai-ask', [AIController::class, 'ask']);
});

// ==================== ADMIN 2FA ROUTES ====================
Route::post('/admin/2fa/toggle', [AdminController::class, 'toggle2FA'])->name('admin.2fa.toggle')->middleware('auth');
Route::post('/admin/2fa/enable', [Admin2FAController::class, 'enable'])->middleware('auth')->name('admin.2fa.enable');
Route::post('/admin/2fa/disable', [Admin2FAController::class, 'disable'])->middleware('auth')->name('admin.2fa.disable');
Route::get('/admin/2fa/verify', [Admin2FAController::class, 'showVerifyForm'])->name('admin.2fa.verify');
Route::post('/admin/2fa/verify', [Admin2FAController::class, 'verify'])->name('admin.2fa.verify');
Route::post('/admin/2fa/resend', [Admin2FAController::class, 'resendCode'])->name('admin.2fa.resend');

// ==================== ANNOUNCEMENT DATA ROUTE ====================
Route::get('/announcements/{id}/data', [AnnouncementController::class, 'getData'])->name('announcement.data');

// ==================== DIRECT TEST ROUTES ====================
Route::post('/enable-2fa-direct', [AdminController::class, 'enable2FA']);
Route::post('/disable-2fa-direct', [AdminController::class, 'disable2FA']);
Route::get('/test-route-200', function() {
    return response()->json(['message' => 'Working!']);
});

// ==================== SUPPORT 2FA ROUTES ====================
Route::get('/support/2fa/verify', [App\Http\Controllers\SupportController::class, 'show2FAVerifyForm'])->name('support.2fa.verify');
Route::post('/support/2fa/verify', [App\Http\Controllers\SupportController::class, 'verify2FA'])->name('support.2fa.verify');
Route::post('/support/2fa/resend', [App\Http\Controllers\SupportController::class, 'resend2FACode'])->name('support.2fa.resend');

// ==================== RESUME VERIFICATION ROUTE ====================
Route::get('/verify/resume/{token}', [StudentAuthController::class, 'resumeVerification'])->name('verification.resume');

// ==================== TEST DATABASE SAVE ====================
Route::get('/test-db-save', function() {
    try {
        $user = new User();
        $user->student_id = 'TEST' . rand(10000, 99999);
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->email = 'test_' . rand(10000, 99999) . '@test.com';
        $user->password = bcrypt('password123');
        $user->course = 'BSIT';
        $user->year_level = '1st Year';
        $user->birthdate = '2000-01-01';
        $user->role = 'student';
        $user->is_active = 1;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'User saved successfully!',
            'user_id' => $user->id,
            'student_id' => $user->student_id
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
    }
});

// ==================== REGISTER ROUTE ====================
Route::post('/register', [StudentAuthController::class, 'register']);

// ==================== STUDENT EMAIL VERIFICATION OTP ROUTES ====================
Route::get('/email/verify', [StudentAuthController::class, 'showVerificationNotice'])
    ->middleware('guest')
    ->name('verification.notice');

Route::post('/email/verify-otp', [StudentAuthController::class, 'verifyOtp'])
    ->name('verification.verify');

Route::post('/email/verify-resend', [StudentAuthController::class, 'resendVerificationOtp'])
    ->name('verification.resend');

// ==================== TEST EMAIL ROUTES ====================
Route::get('/test-welcome-email', function() {
    $student = User::where('role', 'student')->first();
    if (!$student) {
        return "No student found in database!";
    }
    try {
        Mail::to('voidsystem88@gmail.com')->send(new WelcomeMail($student));
        return "Welcome email sent successfully to " . $student->email;
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/test-email', function () {
    $user = auth()->user();
    if ($user) {
        Mail::to($user->email)->send(new WelcomeMail($user));
        return 'Welcome email sent to ' . $user->email;
    }
    return 'No user found';
});

// ==================== STUDENT EDIT DATA ROUTE ====================
Route::get('/students/{id}/edit-data', [AdminController::class, 'getStudentData'])->name('student.edit-data');

// ==================== SYNC TRACKING LOGS ====================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/sync-tracking-logs', [SyncTrackingLogsController::class, 'index'])->name('admin.sync-logs');
    Route::get('/sync-tracking-logs-data', [SyncTrackingLogsController::class, 'getData'])->name('admin.sync-logs.data');
    Route::post('/sync-tracking-logs/run', [SyncTrackingLogsController::class, 'runSync'])->name('admin.sync-logs.run');
    Route::post('/sync-tracking-logs/full', [SyncTrackingLogsController::class, 'runFullSync'])->name('admin.sync-logs.full');
    Route::post('/clear-sync-logs', [SyncTrackingLogsController::class, 'clearLogs'])->name('admin.sync-logs.clear');
    Route::get('/sync-logs/export', [SyncTrackingLogsController::class, 'export'])->name('admin.sync-logs.export');
});

// ==================== IP LOGGER & TRACKING ROUTES ====================
Route::get('/my-ip', function() {
    $ip = request()->ip();
    $userAgent = request()->userAgent();
    $dateTime = now()->format('Y-m-d H:i:s');
    $page = url()->previous() ?? request()->fullUrl();
    
    $deviceType = 'desktop';
    if (preg_match('/(mobile|android|iphone|ipod|blackberry|windows phone)/i', $userAgent)) {
        $deviceType = 'mobile';
    } elseif (preg_match('/(tablet|ipad|kindle)/i', $userAgent)) {
        $deviceType = 'tablet';
    }
    
    $browser = 'Unknown';
    if (preg_match('/Edg/i', $userAgent)) $browser = 'Edge';
    elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
    elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
    elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
    
    $os = 'Unknown';
    if (preg_match('/Windows NT 10/i', $userAgent)) $os = 'Windows 10';
    elseif (preg_match('/Windows NT 11/i', $userAgent)) $os = 'Windows 11';
    elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'macOS';
    elseif (preg_match('/iPhone OS/i', $userAgent)) $os = 'iOS';
    elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
    
    $locationData = [];
    try {
        $locationJson = file_get_contents("http://ip-api.com/json/{$ip}");
        $location = json_decode($locationJson, true);
        if ($location && $location['status'] === 'success') {
            $locationData = $location;
            $locationStr = ($location['city'] ?? '') . ', ' . ($location['country'] ?? 'Unknown');
        } else {
            $locationStr = 'Unknown';
        }
    } catch (\Exception $e) {
        $locationStr = 'Unknown';
        $locationData = [];
    }
    
    $visitorLog = "[{$dateTime}] IP: {$ip} | Location: {$locationStr} | Device: {$deviceType} | Browser: {$browser} | OS: {$os} | Page: {$page}\n";
    file_put_contents(storage_path('logs/visitors.log'), $visitorLog, FILE_APPEND);
    
    try {
        VisitorTracking::create([
            'ip_address' => $ip,
            'country' => $locationData['country'] ?? null,
            'city' => $locationData['city'] ?? null,
            'latitude' => $locationData['lat'] ?? null,
            'longitude' => $locationData['lon'] ?? null,
            'isp' => $locationData['isp'] ?? null,
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
            'page_visited' => $page,
            'created_at' => now(),
        ]);
    } catch (\Exception $e) {}
    
    return view('location-tracker', compact('ip', 'locationData', 'deviceType', 'browser', 'os'));
})->name('location.tracker');

Route::get('/track.gif', function() {
    $ip = request()->ip();
    $userAgent = request()->userAgent();
    $referer = request()->header('referer') ?? request()->fullUrl();
    $dateTime = now()->format('Y-m-d H:i:s');
    
    $deviceType = 'desktop';
    if (preg_match('/(mobile|android|iphone|ipod|blackberry|windows phone)/i', $userAgent)) {
        $deviceType = 'mobile';
    } elseif (preg_match('/(tablet|ipad|kindle)/i', $userAgent)) {
        $deviceType = 'tablet';
    }
    
    $browser = 'Unknown';
    if (preg_match('/Edg/i', $userAgent)) $browser = 'Edge';
    elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
    elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
    elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
    
    $os = 'Unknown';
    if (preg_match('/Windows NT 10/i', $userAgent)) $os = 'Windows 10';
    elseif (preg_match('/Windows NT 11/i', $userAgent)) $os = 'Windows 11';
    elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'macOS';
    elseif (preg_match('/iPhone OS/i', $userAgent)) $os = 'iOS';
    elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
    
    $locationStr = 'Unknown';
    $country = null;
    $city = null;
    $lat = null;
    $lon = null;
    $isp = null;
    
    try {
        $locationJson = file_get_contents("http://ip-api.com/json/{$ip}");
        $location = json_decode($locationJson, true);
        if ($location && $location['status'] === 'success') {
            $country = $location['country'] ?? null;
            $city = $location['city'] ?? null;
            $lat = $location['lat'] ?? null;
            $lon = $location['lon'] ?? null;
            $isp = $location['isp'] ?? null;
            $locationStr = ($city ? $city . ', ' : '') . ($country ? $country : 'Unknown');
        }
    } catch (\Exception $e) {}
    
    $visitorLog = "[{$dateTime}] IP: {$ip} | Location: {$locationStr} | Device: {$deviceType} | Browser: {$browser} | OS: {$os} | Page: {$referer}\n";
    file_put_contents(storage_path('logs/visitors.log'), $visitorLog, FILE_APPEND);
    
    $trackLog = "[{$dateTime}] IP: {$ip} | Referer: {$referer} | UA: {$userAgent}\n";
    file_put_contents(storage_path('logs/tracking.log'), $trackLog, FILE_APPEND);
    
    try {
        VisitorTracking::create([
            'ip_address' => $ip,
            'country' => $country,
            'city' => $city,
            'latitude' => $lat,
            'longitude' => $lon,
            'isp' => $isp,
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
            'user_agent' => $userAgent,
            'page_visited' => $referer,
            'created_at' => now(),
        ]);
    } catch (\Exception $e) {}
    
    $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    return response($pixel)
        ->header('Content-Type', 'image/gif')
        ->header('Content-Length', strlen($pixel))
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
});

// ==================== BUG REPORT ROUTE ====================
Route::post('/bug-report', [PublicAssistanceController::class, 'store'])->name('bug-report.store');

// ==================== TEST ROUTES ====================
Route::get('/test-route', function() {
    return response()->json(['message' => 'Route is working!']);
});

// ==================== MAIN PAGE ====================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ==================== LOGIN PAGE REDIRECT ====================
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// ==================== LOGIN ROUTES ====================
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
});

// ==================== PASSWORD RESET ROUTES ====================
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.otp.send');
Route::post('/forgot-password-email', [ForgotPasswordController::class, 'sendOtpViaEmail'])->name('password.otp.send.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// ==================== PUBLIC ROUTES ====================
Route::post('/public/assistance', [PublicAssistanceController::class, 'store'])->name('public.assistance.store');

// ==================== CACHE CLEAR ROUTE ====================
Route::get('/clear-cache', function() {
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return "Cache cleared successfully!";
    } catch (\Exception $e) {
        return "Error clearing cache: " . $e->getMessage();
    }
});

Route::get('/check-session', function() {
    return response()->json([
        'session_id' => session()->getId(),
        'auth_check' => Auth::check(),
        'user' => Auth::user() ? Auth::user()->id : null,
    ]);
});

// ==================== STUDENT AUTH ROUTES ====================
Route::get('/student/register', function () {
    return redirect('/');
});

Route::get('/student/login', function () {
    return redirect('/');
})->name('student.login');

Route::post('/student/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
Route::post('/student/login', [LoginController::class, 'login'])->name('student.login.submit');

// ==================== CHECK UNIQUENESS ROUTES ====================
Route::post('/check-student-id', function (Request $request) {
    $exists = User::where('student_id', $request->student_id)->exists();
    return response()->json(['exists' => $exists]);
});

Route::post('/check-email', function (Request $request) {
    $exists = User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
});

// ==================== STUDENT ROUTES ====================
Route::prefix('student')->name('student.')->middleware(['auth', 'check.maintenance'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            if ($user && $user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user && $user->role === 'staff') return redirect()->route('staff.dashboard');
            if ($user && $user->role === 'support') return redirect()->route('support.dashboard');
            return redirect('/');
        }
        return app(StudentDashboardController::class)->index();
    })->name('dashboard');
    
    Route::get('/clearance', function () {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            if ($user && $user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user && $user->role === 'staff') return redirect()->route('staff.dashboard');
            if ($user && $user->role === 'support') return redirect()->route('support.dashboard');
            return redirect('/');
        }
        return app(StudentDashboardController::class)->clearance();
    })->name('clearance');
    
    Route::get('/reminders', function () {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            if ($user && $user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user && $user->role === 'staff') return redirect()->route('staff.dashboard');
            if ($user && $user->role === 'support') return redirect()->route('support.dashboard');
            return redirect('/');
        }
        return app(StudentDashboardController::class)->reminders();
    })->name('reminders');
    
    Route::get('/profile', function () {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            if ($user && $user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user && $user->role === 'staff') return redirect()->route('staff.dashboard');
            if ($user && $user->role === 'support') return redirect()->route('support.dashboard');
            return redirect('/');
        }
        return app(StudentDashboardController::class)->profile();
    })->name('profile');
    
    Route::post('/profile', function (Request $request) {
        $user = Auth::user();
        if (!$user || $user->role !== 'student') {
            if ($user && $user->role === 'admin') return redirect()->route('admin.dashboard');
            if ($user && $user->role === 'staff') return redirect()->route('staff.dashboard');
            if ($user && $user->role === 'support') return redirect()->route('support.dashboard');
            return redirect('/');
        }
        return app(StudentDashboardController::class)->updateProfile($request);
    })->name('profile.update');
    
    Route::post('/clearance/submit', [ClearanceRequestController::class, 'store'])->name('clearance.submit');
    Route::get('/clearance/print', [ClearanceRequestController::class, 'printSlip'])->name('clearance.print');
    Route::get('/assistance', [AssistanceController::class, 'index'])->name('assistance');
    Route::post('/assistance', [AssistanceController::class, 'store'])->name('assistance.store');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
});

// ==================== SUPPORT ROUTES ====================
Route::prefix('support')->name('support.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [SupportController::class, 'dashboard'])->name('dashboard');
    Route::get('/requests', [SupportController::class, 'requests'])->name('requests');
    Route::get('/students', [SupportController::class, 'studentsPage'])->name('students');
    Route::get('/feedbacks', [SupportController::class, 'feedbacksPage'])->name('feedbacks');
    Route::get('/announcements', [SupportController::class, 'announcements'])->name('announcements');
    Route::post('/send-announcement', [SupportController::class, 'sendAnnouncement'])->name('send-announcement');
    
    Route::get('/profile', [SupportController::class, 'profile'])->name('profile');
    Route::put('/profile/update', [SupportController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password/change', [SupportController::class, 'changePassword'])->name('password.change');
    Route::post('/2fa/enable', [SupportController::class, 'enable2FA'])->name('2fa.enable');
    Route::post('/2fa/disable', [SupportController::class, 'disable2FA'])->name('2fa.disable');
    
    Route::get('/maintenance/password', function() {
        return view('support.maintenance-password');
    })->name('maintenance.password');
    
    Route::middleware(['maintenance.auth'])->group(function () {
        Route::get('/maintenance', [SupportController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/soft', [SupportController::class, 'enableSoftShutdown'])->name('maintenance.soft');
        Route::post('/maintenance/full', [SupportController::class, 'enableFullShutdown'])->name('maintenance.full');
        Route::post('/maintenance/disable', [SupportController::class, 'disableMaintenance'])->name('maintenance.disable');
        Route::get('/maintenance/logout', [SupportController::class, 'clearMaintenanceAuth'])->name('maintenance.logout');
    });
    
    Route::get('/maintenance/status', [SupportController::class, 'maintenanceStatus'])->name('maintenance.status');
    Route::post('/reset-password', [SupportController::class, 'resetPassword'])->name('reset.password');
    Route::post('/reset-accountid', [SupportController::class, 'resetAccountId'])->name('reset.accountid');
    Route::post('/toggle-active/{id}', [SupportController::class, 'toggleActive'])->name('toggle.active');
    Route::get('/students/{id}/edit', [SupportController::class, 'editStudent'])->name('student.edit');
    Route::put('/students/{id}', [SupportController::class, 'updateStudent'])->name('student.update');
    Route::post('/request/{id}/status', [SupportController::class, 'updateStatus'])->name('request.status');
    Route::post('/request', [SupportController::class, 'storeRequest'])->name('request.store');
    Route::get('/request/{id}/view-attachment', [SupportController::class, 'viewAttachment'])->name('attachment.view');
    Route::get('/request/{id}/download-attachment', [SupportController::class, 'downloadAttachment'])->name('attachment.download');
    Route::get('/request/{id}/json', [SupportController::class, 'getRequest'])->name('request.json');
    Route::post('/feedback/{id}/respond', [SupportController::class, 'respondFeedback'])->name('feedback.respond');
    Route::post('/bug-reports/{id}/update', [SupportController::class, 'updateBugReport'])->name('bug-reports.update');
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'check.maintenance'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    // Officer Management
    Route::get('/officers', [AdminController::class, 'officers'])->name('officers');
    Route::post('/officers', [AdminController::class, 'storeOfficer'])->name('officer.store');
    Route::get('/officers/{id}/edit', [AdminController::class, 'editOfficer'])->name('officer.edit');
    Route::put('/officers/{id}', [AdminController::class, 'updateOfficer'])->name('officer.update');
    Route::delete('/officers/{id}', [AdminController::class, 'destroyOfficer'])->name('officer.destroy');
    Route::post('/officers/{id}/toggle-status', [AdminController::class, 'toggleOfficerStatus'])->name('officer.toggle');
    
    // Student Management
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('student.store');
    Route::get('/students/{id}/edit', [AdminController::class, 'editStudent'])->name('student.edit');
    Route::get('/students/{id}/edit-data', [AdminController::class, 'getStudentData'])->name('student.edit-data');
    Route::put('/students/{id}', [AdminController::class, 'updateStudent'])->name('student.update');
    Route::delete('/students/{id}', [AdminController::class, 'destroyStudent'])->name('student.destroy');
    
    // Staff Management
    Route::get('/staffs', [AdminController::class, 'staffs'])->name('staffs');
    Route::post('/staffs', [AdminController::class, 'storeStaff'])->name('staff.store');
    Route::get('/staffs/{id}/edit', [AdminController::class, 'editStaff'])->name('staff.edit');
    Route::put('/staffs/{id}', [AdminController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staffs/{id}', [AdminController::class, 'destroyStaff'])->name('staff.destroy');
    
    // Department Management
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'storeDepartment'])->name('department.store');
    Route::get('/departments/{id}/edit', [AdminController::class, 'editDepartment'])->name('department.edit');
    Route::get('/departments/{id}/edit-data', [AdminController::class, 'getDepartmentData'])->name('department.edit-data');
    Route::put('/departments/{id}', [AdminController::class, 'updateDepartment'])->name('department.update');
    Route::delete('/departments/{id}', [AdminController::class, 'destroyDepartment'])->name('department.destroy');
    
    // Clearance Requests
    Route::get('/clearance-requests', [AdminController::class, 'clearanceRequests'])->name('clearance-requests');
    Route::post('/clearance-requests/{id}/status', [AdminController::class, 'updateClearanceStatus'])->name('clearance.status');
    Route::delete('/clearance-requests/{id}', [AdminController::class, 'destroyClearanceRequest'])->name('clearance.destroy');
    
    // Activity Logs
    Route::get('/activity-logs', [AdminController::class, 'activityLogs'])->name('activity-logs');
    
    // Backup 
    Route::get('/backup', [BackupController::class, 'showPasswordForm'])->name('backup.password.form');
    Route::post('/backup/verify', [BackupController::class, 'verifyPassword'])->name('backup.verify');
    Route::get('/backup/dashboard', [BackupController::class, 'index'])->name('backup')->middleware('backup.auth');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create')->middleware('backup.auth');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download')->middleware('backup.auth');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete')->middleware('backup.auth');
    Route::post('/import-database', [BackupController::class, 'import'])->name('backup.import')->middleware('backup.auth');
    
    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'adminIndex'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::get('/announcements/{id}/data', [AnnouncementController::class, 'getData'])->name('announcement.data');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
    Route::post('/announcements/{id}/toggle', [AnnouncementController::class, 'toggleActive'])->name('announcement.toggle');
    
    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Bug Reports
    Route::get('/bug-reports', [PublicAssistanceController::class, 'index'])->name('bug-reports');
    Route::post('/bug-reports/{id}/update', [PublicAssistanceController::class, 'update'])->name('bug-reports.update');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread');
    Route::get('/notifications/ajax', [NotificationController::class, 'getAjaxNotifications'])->name('notifications.ajax');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    
    // IP Logs
    Route::get('/ip-logs', function() {
        $logFile = storage_path('logs/visitors.log');
        $logs = file_exists($logFile) ? file_get_contents($logFile) : 'No logs yet.';
        $trackFile = storage_path('logs/tracking.log');
        $trackLogs = file_exists($trackFile) ? file_get_contents($trackFile) : 'No tracking logs yet.';
        $ipCount = file_exists($logFile) ? substr_count(file_get_contents($logFile), 'IP Address:') : 0;
        $trackCount = file_exists($trackFile) ? substr_count(file_get_contents($trackFile), 'IP:') : 0;
        return view('admin.ip-logs', compact('logs', 'trackLogs', 'ipCount', 'trackCount'));
    })->name('ip-logs');
    
    Route::get('/download-ips', function() {
        $logFile = storage_path('logs/visitors.log');
        $content = file_exists($logFile) ? file_get_contents($logFile) : '';
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="ip_logs_' . date('Y-m-d') . '.csv"');
    })->name('download-ips');
    
    Route::get('/visitor-tracking', function() {
        $visitors = VisitorTracking::orderBy('created_at', 'desc')->paginate(20);
        
        foreach ($visitors as $visitor) {
            if ($visitor->ip_address && empty($visitor->country)) {
                try {
                    $locationJson = @file_get_contents("http://ip-api.com/json/{$visitor->ip_address}");
                    $location = json_decode($locationJson, true);
                    if ($location && $location['status'] === 'success') {
                        $visitor->country = $location['country'] ?? null;
                        $visitor->city = $location['city'] ?? null;
                        $visitor->latitude = $location['lat'] ?? null;
                        $visitor->longitude = $location['lon'] ?? null;
                        $visitor->isp = $location['isp'] ?? null;
                        $visitor->save();
                    }
                } catch (\Exception $e) {}
            }
        }
        
        $visitors = VisitorTracking::orderBy('created_at', 'desc')->paginate(20);
        $mobileCount = VisitorTracking::where('device_type', 'mobile')->count();
        $desktopCount = VisitorTracking::where('device_type', 'desktop')->count();
        $tabletCount = VisitorTracking::where('device_type', 'tablet')->count();
        $wifiCount = VisitorTracking::where('network_type', 'wifi')->count();
        $cellularCount = VisitorTracking::where('network_type', 'cellular')->count();
        $totalVisitors = VisitorTracking::count();
        
        $visitorsByCountry = VisitorTracking::whereNotNull('country')
            ->select('country', \DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('admin.visitor-tracking', compact(
            'visitors', 'mobileCount', 'desktopCount', 'tabletCount', 
            'wifiCount', 'cellularCount', 'totalVisitors', 'visitorsByCountry'
        ));
    })->name('visitor-tracking');
    
    Route::post('/clear-ip-logs', function() {
        $logFile = storage_path('logs/visitors.log');
        if (file_exists($logFile)) {
            file_put_contents($logFile, '');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('clear-ip-logs');
    
    Route::post('/clear-tracking-logs', function() {
        $trackFile = storage_path('logs/tracking.log');
        if (file_exists($trackFile)) {
            file_put_contents($trackFile, '');
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    })->name('clear-tracking-logs');
    
    Route::get('/sync-tracking-logs', function() {
        $logFile = storage_path('logs/tracking.log');
        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found'], 404);
        }
        
        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);
        $synced = 0;
        $errors = 0;
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            preg_match('/\[(.*?)\]\s+IP:\s+(\d+\.\d+\.\d+\.\d+)\s+\|\s+Referer:\s+(.*?)\s+\|\s+UA:\s+(.*)/', $line, $matches);
            
            if (count($matches) >= 5) {
                $ip = $matches[2];
                $referer = $matches[3];
                $userAgent = $matches[4];
                $createdAt = $matches[1];
                
                $exists = VisitorTracking::where('ip_address', $ip)
                    ->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($createdAt . ' -1 minute')))
                    ->exists();
                
                if (!$exists) {
                    try {
                        $locationJson = @file_get_contents("http://ip-api.com/json/{$ip}");
                        $location = json_decode($locationJson, true);
                        
                        $deviceType = 'desktop';
                        if (preg_match('/(mobile|iphone|android|blackberry)/i', $userAgent)) $deviceType = 'mobile';
                        elseif (preg_match('/(tablet|ipad)/i', $userAgent)) $deviceType = 'tablet';
                        
                        $os = 'Unknown';
                        if (preg_match('/Windows NT 10/i', $userAgent)) $os = 'Windows 10';
                        elseif (preg_match('/Windows NT 11/i', $userAgent)) $os = 'Windows 11';
                        elseif (preg_match('/Mac OS X/i', $userAgent)) $os = 'macOS';
                        elseif (preg_match('/iPhone OS/i', $userAgent)) $os = 'iOS';
                        elseif (preg_match('/Android/i', $userAgent)) $os = 'Android';
                        
                        $browser = 'Unknown';
                        if (preg_match('/Edg/i', $userAgent)) $browser = 'Edge';
                        elseif (preg_match('/Chrome/i', $userAgent)) $browser = 'Chrome';
                        elseif (preg_match('/Firefox/i', $userAgent)) $browser = 'Firefox';
                        elseif (preg_match('/Safari/i', $userAgent)) $browser = 'Safari';
                        elseif (preg_match('/FBAN|FBAV/i', $userAgent)) $browser = 'Facebook App';
                        
                        VisitorTracking::create([
                            'ip_address' => $ip,
                            'device_type' => $deviceType,
                            'os' => $os,
                            'browser' => $browser,
                            'user_agent' => $userAgent,
                            'referer' => $referer,
                            'page_visited' => $referer,
                            'country' => $location['country'] ?? null,
                            'city' => $location['city'] ?? null,
                            'latitude' => $location['lat'] ?? null,
                            'longitude' => $location['lon'] ?? null,
                            'isp' => $location['isp'] ?? null,
                            'created_at' => $createdAt,
                        ]);
                        $synced++;
                    } catch (\Exception $e) {
                        $errors++;
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'synced' => $synced,
            'errors' => $errors,
            'total_lines' => count($lines),
        ]);
    })->name('sync-tracking-logs');
});

// ==================== STAFF ROUTES ====================
Route::prefix('staff')->name('staff.')->middleware(['auth', 'check.maintenance'])->group(function () {
    Route::get('/dashboard', [DepartmentStaffController::class, 'dashboard'])->name('dashboard');
    Route::post('/approve/{id}', [DepartmentStaffController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [DepartmentStaffController::class, 'reject'])->name('reject');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Requirement management routes
    Route::post('/requirements', [DepartmentStaffController::class, 'storeRequirement'])->name('requirements.store');
    Route::put('/requirements/{id}', [DepartmentStaffController::class, 'updateRequirement'])->name('requirements.update');
    Route::delete('/requirements/{id}', [DepartmentStaffController::class, 'destroyRequirement'])->name('requirements.destroy');
    Route::post('/verified/upload', [DepartmentStaffController::class, 'uploadVerifiedList'])->name('verified.upload');
    Route::post('/verified/add', [DepartmentStaffController::class, 'addVerifiedStudent'])->name('verified.add');
    Route::delete('/verified/{id}', [DepartmentStaffController::class, 'removeVerifiedStudent'])->name('verified.remove');
});

// ==================== PUBLIC ANNOUNCEMENTS ====================
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');

// ==================== FILE ATTACHMENT ROUTES ====================
Route::get('/file/{filename}', function($filename) {
    $paths = [
        storage_path('app/public/attachments/' . $filename),
        public_path('attachments/assistance/' . $filename),
        public_path('attachments/' . $filename),
        storage_path('app/attachments/' . $filename),
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $mime = mime_content_type($path);
            if (request()->has('download')) {
                return response()->download($path, $filename, ['Content-Type' => $mime]);
            }
            return response()->file($path, ['Content-Type' => $mime]);
        }
    }
    abort(404, 'File not found: ' . $filename);
})->name('file.view');

Route::get('/debug-files', function() {
    $paths = [
        'storage/app/public/attachments/' => storage_path('app/public/attachments'),
        'public/attachments/assistance/' => public_path('attachments/assistance'),
        'public/attachments/' => public_path('attachments'),
    ];
    
    $results = [];
    foreach ($paths as $name => $path) {
        $results[$name] = [
            'path' => $path,
            'exists' => file_exists($path),
            'files' => file_exists($path) ? array_values(array_diff(scandir($path), ['.', '..'])) : []
        ];
    }
    
    $requests = \App\Models\SupportRequest::whereNotNull('attachment_path')->get();
    $attachments = [];
    foreach ($requests as $request) {
        $attachments[] = [
            'id' => $request->id,
            'stored_path' => $request->attachment_path,
            'filename' => basename($request->attachment_path),
            'view_url' => url('/file/' . basename($request->attachment_path)),
            'download_url' => url('/file/' . basename($request->attachment_path) . '?download=1')
        ];
    }
    
    return response()->json([
        'directories' => $results,
        'support_requests' => $attachments
    ]);
});

Route::get('/visitor-tracking-data', function(Request $request) {
    $query = VisitorTracking::query();
    
    if ($request->search) {
        $query->where('ip_address', 'LIKE', "%{$request->search}%")
              ->orWhere('country', 'LIKE', "%{$request->search}%")
              ->orWhere('city', 'LIKE', "%{$request->search}%");
    }
    
    if ($request->device && $request->device != 'all') {
        $query->where('device_type', $request->device);
    }
    
    if ($request->country && $request->country != 'all') {
        $query->where('country', $request->country);
    }
    
    if ($request->date_from) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->date_to) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }
    
    $visitors = $query->orderBy('created_at', 'desc')->paginate(20);
    
    return response()->json([
        'visitors' => $visitors,
        'total' => $visitors->total(),
        'current_page' => $visitors->currentPage(),
        'last_page' => $visitors->lastPage(),
    ]);
})->name('visitor-tracking-data');

// ==================== NOTIFICATION MARK READ ROUTE ====================
Route::post('/notifications/{id}/mark-read', function ($id) {
    $notification = Auth::user()->notifications()->findOrFail($id);
    $notification->update([
        'is_read' => true,
        'read_at' => now()
    ]);
    return response()->json(['success' => true]);
})->middleware('auth')->name('notifications.mark-read');

// ==================== GEMINI TEST ROUTE ====================
Route::get('/test-gemini-direct', function () {
    $apiKey = env('GEMINI_API_KEY');
    
    if (!$apiKey || $apiKey === '') {
        return response()->json([
            'success' => false,
            'error' => 'GEMINI_API_KEY is not set in .env file'
        ]);
    }
    
    try {
        $response = Illuminate\Support\Facades\Http::timeout(10)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey,
            [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Say "Hello! Gemini API is working!" in 5 words.']
                        ]
                    ]
                ]
            ]
        );
        
        if ($response->successful()) {
            $data = $response->json();
            $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
            return response()->json([
                'success' => true,
                'answer' => $answer,
                'api_key_works' => true
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'API returned status ' . $response->status(),
                'response' => $response->body()
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
// Public API for student to view department requirements
Route::get('/api/departments/{department}/requirements', function($departmentId) {
    $department = App\Models\Department::findOrFail($departmentId);
    $requirements = $department->getActiveRequirements();
    
    return response()->json([
        'success' => true,
        'requirements' => $requirements->map(function($req) {
            return [
                'id' => $req->id,
                'requirement_name' => $req->requirement_name,
                'is_required' => $req->is_required,
            ];
        })
    ]);
})->name('api.department.requirements');

// ==================== OFFICER ROUTES ====================
Route::prefix('officer')->name('officer.')->middleware(['auth', 'check.maintenance'])->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
    Route::post('/verified/upload', [OfficerController::class, 'uploadVerifiedList'])->name('verified.upload');
    Route::post('/verified/add', [OfficerController::class, 'addVerifiedStudent'])->name('verified.add');
    Route::delete('/verified/{id}', [OfficerController::class, 'removeVerifiedStudent'])->name('verified.remove');
});
// ==================== END OF ROUTES ====================
