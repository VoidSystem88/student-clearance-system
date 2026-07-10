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
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\StaffController;
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
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public clearance verification
Route::get('/verify/{token}', [App\Http\Controllers\VerifyController::class, 'show'])->name('clearance.verify');
Route::post('/upload-profile-photo', [App\Http\Controllers\ProfileController::class, 'uploadPhoto'])
    ->name('upload.profile.photo')
    ->middleware('auth');

// ==================== AI ROUTES ====================
Route::get('/ai-test', function () {
    return response()->json(['message' => 'AI route is working!']);
});

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
Route::prefix('admin')->middleware(['auth', 'admin.auth'])->group(function () {
    Route::get('/sync-tracking-logs', [SyncTrackingLogsController::class, 'index'])->name('admin.sync-logs');
    Route::get('/sync-tracking-logs-data', [SyncTrackingLogsController::class, 'getData'])->name('admin.sync-logs.data');
    Route::post('/sync-tracking-logs/run', [SyncTrackingLogsController::class, 'runSync'])->name('admin.sync-logs.run');
    Route::post('/sync-tracking-logs/full', [SyncTrackingLogsController::class, 'runFullSync'])->name('admin.sync-logs.full');
    Route::post('/clear-sync-logs', [SyncTrackingLogsController::class, 'clearLogs'])->name('admin.sync-logs.clear');
    Route::get('/sync-logs/export', [SyncTrackingLogsController::class, 'export'])->name('admin.sync-logs.export');
});

// ==================== BUG REPORT ROUTE ====================
Route::post('/bug-report', [PublicAssistanceController::class, 'store'])->name('bug-report.store');
Route::post('/report-issue', [PublicAssistanceController::class, 'store'])->name('bug-report.store');

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
Route::post('/clear-new-registration-flag', function() {
    session()->forget('is_new_registration');
    return response()->json(['success' => true]);
})->name('clear.registration.flag');

// ==================== PASSWORD RESET ROUTES ====================
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/account/send-code', [ForgotPasswordController::class, 'sendOtp'])->name('password.otp.send');
Route::post('/account/send-code-email', [ForgotPasswordController::class, 'sendOtpViaEmail'])->name('password.otp.send.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/account/check-code', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/account/update-pass', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

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
Route::prefix('student')->name('student.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/clearance', [StudentDashboardController::class, 'clearance'])->name('clearance');
    Route::post('/clearance/submit', [ClearanceRequestController::class, 'store'])->name('clearance.submit');
    Route::post('/clearance/{id}/cancel', [ClearanceRequestController::class, 'cancel'])->name('clearance.cancel');
    Route::get('/clearance/print', [ClearanceRequestController::class, 'printSlip'])->name('clearance.print');
    Route::get('/clearance/view-slip-pdf', [ClearanceRequestController::class, 'viewSlipPdf'])->name('clearance.view-pdf');
    Route::get('/clearance/view-slip', [ClearanceRequestController::class, 'viewSlip'])->name('clearance.view');
    Route::get('/reminders', [StudentDashboardController::class, 'reminders'])->name('reminders');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [StudentDashboardController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/assistance', [AssistanceController::class, 'index'])->name('assistance');
    Route::post('/assistance', [AssistanceController::class, 'store'])->name('assistance.store');
    Route::get('/assistance/{id}', [AssistanceController::class, 'getRequest'])->name('assistance.get');
    Route::put('/assistance/{id}', [AssistanceController::class, 'update'])->name('assistance.update');
    Route::delete('/assistance/{id}', [AssistanceController::class, 'destroy'])->name('assistance.destroy');
    Route::get('/assistance/attachment/{id}', [AssistanceController::class, 'viewAttachment'])->name('assistance.attachment');
    
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{id}', [FeedbackController::class, 'getFeedback'])->name('feedback.get');
    Route::put('/feedback/{id}', [FeedbackController::class, 'update'])->name('feedback.update');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
    
    Route::get('/notifications/check', function() {
        $user = Auth::user();
        $recentAnnouncements = App\Models\Announcement::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $notifications = [];
        foreach ($recentAnnouncements as $ann) {
            $notifications[] = [
                'id' => 'ann_' . $ann->id,
                'title' => $ann->title,
                'message' => Str::limit($ann->content, 80),
                'icon' => 'fa-bullhorn',
                'link' => route('student.reminders'),
                'created_at' => $ann->created_at->timestamp,
            ];
        }

        usort($notifications, function($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        $lastCheck = session('last_notification_check', now()->subDay()->timestamp);
        $newCount = 0;
        foreach ($notifications as $notif) {
            if ($notif['created_at'] > $lastCheck) {
                $newCount++;
            }
        }

        session(['last_notification_check' => now()->timestamp]);

        return response()->json([
            'success' => true,
            'new_count' => $newCount,
            'recent_notifications' => array_slice($notifications, 0, 10),
            'current_time' => now()->timestamp
        ]);
    })->name('notifications.check');
});

// ==================== SUPPORT ROUTES ====================
Route::prefix('support')->name('support.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [SupportController::class, 'dashboard'])->name('dashboard');
    Route::get('/requests', [SupportController::class, 'requests'])->name('requests');
    Route::get('/students', [SupportController::class, 'studentsPage'])->name('students');
    Route::get('/feedbacks', [SupportController::class, 'feedbacksPage'])->name('feedbacks');
    Route::get('/announcements', [SupportController::class, 'announcements'])->name('announcements');
    Route::post('/send-announcement', [SupportController::class, 'sendAnnouncement'])->name('send-announcement');
    Route::post('/bulk-year-update', [SupportController::class, 'bulkYearUpdate'])->name('bulk.year.update');

    Route::get('/reminders', [SupportController::class, 'reminders'])->name('reminders');
    Route::post('/reminders', [SupportController::class, 'storeReminder'])->name('reminder.store');
    Route::get('/reminders/{id}/edit', [SupportController::class, 'editReminder'])->name('reminder.edit');
    Route::put('/reminders/{id}', [SupportController::class, 'updateReminder'])->name('reminder.update');
    Route::delete('/reminders/{id}', [SupportController::class, 'destroyReminder'])->name('reminder.destroy');
    Route::post('/reminders/{id}/toggle', [SupportController::class, 'toggleReminder'])->name('reminder.toggle');

    Route::get('/notifications', [SupportController::class, 'notifications'])->name('notifications');
    Route::get('/notification-counts', [SupportController::class, 'getNotificationCounts'])->name('notification.counts');
    Route::post('/notifications/mark-all-read', [SupportController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');

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
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::any('/departments/{id}/assign-years', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'assignYearsToDepartment'])->name('department.assign-years');
    Route::get('/download-backup', [App\Http\Controllers\Admin\BackupController::class, 'downloadBackup'])->name('download.backup');
    Route::post('/students/{id}/archive', function($id) {
        return redirect()->route('admin.students')->with('error', 'Archive feature is disabled.');
    })->name('student.archive');
    
    Route::post('/students/{id}/restore', function($id) {
        return redirect()->route('admin.students')->with('error', 'Restore feature is disabled.');
    })->name('student.restore');
    
    Route::get('/reminders', [AdminController::class, 'reminders'])->name('reminders');
    Route::post('/reminders', [AdminController::class, 'storeReminder'])->name('reminder.store');
    Route::get('/reminders/{id}/edit', [AdminController::class, 'editReminder'])->name('reminder.edit');
    Route::put('/reminders/{id}', [AdminController::class, 'updateReminder'])->name('reminder.update');
    Route::delete('/reminders/{id}', [AdminController::class, 'destroyReminder'])->name('reminder.destroy');
    Route::post('/reminders/{id}/toggle', [AdminController::class, 'toggleReminder'])->name('reminder.toggle');

    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('course.store');
    Route::get('/courses/{id}/edit', [AdminController::class, 'editCourse'])->name('course.edit');
    Route::put('/courses/{id}', [AdminController::class, 'updateCourse'])->name('course.update');
    Route::delete('/courses/{id}', [AdminController::class, 'destroyCourse'])->name('course.destroy');
    Route::post('/courses/{id}/toggle', [AdminController::class, 'toggleCourseStatus'])->name('course.toggle');
    
    Route::get('/reports/students', [AdminController::class, 'exportStudentsReport'])->name('reports.students');
    Route::get('/reports/clearance', [AdminController::class, 'exportClearanceReport'])->name('reports.clearance');
    Route::get('/reports/activity', [AdminController::class, 'exportActivityReport'])->name('reports.activity');
    
    Route::get('/students', [App\Http\Controllers\Admin\StudentManagementController::class, 'index'])->name('students');
    Route::post('/students', [App\Http\Controllers\Admin\StudentManagementController::class, 'store'])->name('student.store');
    Route::get('/students/{id}/edit-data', [App\Http\Controllers\Admin\StudentManagementController::class, 'getData'])->name('student.edit-data');
    Route::put('/students/{id}', [App\Http\Controllers\Admin\StudentManagementController::class, 'update'])->name('student.update');
    Route::delete('/students/{id}', [App\Http\Controllers\Admin\StudentManagementController::class, 'destroy'])->name('student.destroy');
    Route::post('/students/{id}/toggle-status', [App\Http\Controllers\Admin\StudentManagementController::class, 'toggleStatus'])->name('student.toggle');

    Route::get('/staffs', [App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('staffs');
    Route::post('/staffs', [App\Http\Controllers\Admin\StaffManagementController::class, 'store'])->name('staff.store');
    Route::get('/staffs/{id}/edit-data', [App\Http\Controllers\Admin\StaffManagementController::class, 'getData'])->name('staff.edit-data');
    Route::put('/staffs/{id}', [App\Http\Controllers\Admin\StaffManagementController::class, 'update'])->name('staff.update');
    Route::delete('/staffs/{id}', [App\Http\Controllers\Admin\StaffManagementController::class, 'destroy'])->name('staff.destroy');

    Route::get('/departments', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'index'])->name('departments');
    Route::post('/departments', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'store'])->name('department.store');
    Route::get('/departments/{id}/edit-data', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'getData'])->name('department.edit-data');
    Route::put('/departments/{id}', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'update'])->name('department.update');
    Route::delete('/departments/{id}', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'destroy'])->name('department.destroy');
    Route::post('/departments/{id}/toggle-status', [App\Http\Controllers\Admin\DepartmentManagementController::class, 'toggleStatus'])->name('department.toggle');

    Route::get('/officers', [App\Http\Controllers\Admin\OfficerManagementController::class, 'index'])->name('officers');
    Route::post('/officers', [App\Http\Controllers\Admin\OfficerManagementController::class, 'store'])->name('officer.store');
    Route::get('/officers/{id}/edit-data', [App\Http\Controllers\Admin\OfficerManagementController::class, 'getData'])->name('officer.edit-data');
    Route::put('/officers/{id}', [App\Http\Controllers\Admin\OfficerManagementController::class, 'update'])->name('officer.update');
    Route::delete('/officers/{id}', [App\Http\Controllers\Admin\OfficerManagementController::class, 'destroy'])->name('officer.destroy');
    Route::post('/officers/{id}/toggle-status', [App\Http\Controllers\Admin\OfficerManagementController::class, 'toggleStatus'])->name('officer.toggle');

    Route::get('/clearance-requests', [App\Http\Controllers\Admin\ClearanceManagementController::class, 'index'])->name('clearance-requests');
    Route::post('/clearance-requests/{id}/status', [App\Http\Controllers\Admin\ClearanceManagementController::class, 'updateStatus'])->name('clearance.status');
    Route::delete('/clearance-requests/{id}', [App\Http\Controllers\Admin\ClearanceManagementController::class, 'destroy'])->name('clearance.destroy');

    Route::get('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\Admin\AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/2fa/toggle', [App\Http\Controllers\Admin\AdminProfileController::class, 'toggle2FA'])->name('profile.2fa.toggle');

    Route::get('/activity-logs', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs');
    
    Route::get('/announcements', [AnnouncementController::class, 'adminIndex'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcement.store');
    Route::get('/announcements/{id}/edit', [AnnouncementController::class, 'edit'])->name('announcement.edit');
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update'])->name('announcement.update');
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('announcement.destroy');
    Route::post('/announcements/{id}/toggle', [AnnouncementController::class, 'toggleActive'])->name('announcement.toggle');
    
    Route::get('/backup', [BackupController::class, 'showPasswordForm'])->name('backup.password.form');
    Route::post('/backup/verify', [BackupController::class, 'verifyPassword'])->name('backup.verify');
    Route::get('/backup/dashboard', [BackupController::class, 'index'])->name('backup.dashboard');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete');
    Route::post('/import-database', [BackupController::class, 'import'])->name('backup.import');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/bug-reports', [PublicAssistanceController::class, 'index'])->name('bug-reports');
    Route::post('/bug-reports/{id}/update', [PublicAssistanceController::class, 'update'])->name('bug-reports.update');
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread');
    Route::get('/notifications/ajax', [NotificationController::class, 'getAjaxNotifications'])->name('notifications.ajax');
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// ==================== STAFF LOGIN ROUTES ====================
Route::get('/staff/login', [DepartmentStaffController::class, 'showLoginForm'])->name('staff.login');
Route::post('/staff/login', [DepartmentStaffController::class, 'login'])->name('staff.login.submit');

// ==================== STAFF ROUTES ====================
Route::prefix('staff')->name('staff.')->middleware(['staff.auth'])->group(function () {
    Route::get('/dashboard', [DepartmentStaffController::class, 'dashboard'])->name('dashboard');
    Route::post('/approve/{id}', [DepartmentStaffController::class, 'approve'])->name('approve');
    Route::post('/reject/{id}', [DepartmentStaffController::class, 'reject'])->name('reject');
    Route::post('/logout', [DepartmentStaffController::class, 'logout'])->name('staff.logout');

    Route::post('/year-requirements', [DepartmentStaffController::class, 'storeYearRequirement'])->name('year-requirements.store');
    Route::put('/year-requirements/{id}', [DepartmentStaffController::class, 'updateYearRequirement'])->name('year-requirements.update');
    Route::delete('/year-requirements/{id}', [DepartmentStaffController::class, 'destroyYearRequirement'])->name('year-requirements.destroy');
    Route::get('/year-requirements/{yearLevel?}', [DepartmentStaffController::class, 'getYearRequirements'])->name('year-requirements.get');

    Route::delete('/requirements/{id}', [DepartmentStaffController::class, 'destroyRequirement'])->name('requirements.destroy');
    Route::post('/requirements', [DepartmentStaffController::class, 'storeRequirement'])->name('requirements.store');
    Route::put('/requirements/{id}', [DepartmentStaffController::class, 'updateRequirement'])->name('requirements.update');

    Route::get('/download-export/{id}', [DepartmentStaffController::class, 'downloadExport'])->name('download.export');
    Route::post('/import-report', [DepartmentStaffController::class, 'importReportToVerified'])->name('import.report');
    Route::post('/upload-csv-verified', [DepartmentStaffController::class, 'uploadCSVToVerified'])->name('verified.upload-csv');

    // ✅ REPORTS ROUTES (COMPLETE)
    Route::get('/reports', [DepartmentStaffController::class, 'reports'])->name('reports');
    Route::get('/reports/{id}', [DepartmentStaffController::class, 'viewReport'])->name('reports.view');
    Route::post('/reports/{id}/approve', [DepartmentStaffController::class, 'approveReport'])->name('reports.approve');
    Route::post('/reports/{id}/reject', [DepartmentStaffController::class, 'rejectReport'])->name('reports.reject');
    Route::delete('/reports/{id}/delete', [DepartmentStaffController::class, 'deleteReport'])->name('reports.delete');
    Route::get('/reports/{id}/export', [DepartmentStaffController::class, 'exportReport'])->name('reports.export');
});
// ==================== OFFICER ROUTES ====================
Route::prefix('officer')->name('officer.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [OfficerController::class, 'students'])->name('students');
    Route::get('/verified', [OfficerController::class, 'verified'])->name('verified');
    Route::get('/upload', [OfficerController::class, 'uploadPage'])->name('upload');
    Route::post('/upload-csv', [OfficerController::class, 'uploadVerifiedList'])->name('upload.csv');
    Route::get('/send-report', [OfficerController::class, 'sendReportPage'])->name('send.report');
    Route::post('/send-report', [OfficerController::class, 'sendReport'])->name('send.report');
    // ✅ ADD THESE NOTIFICATION ROUTES
    Route::get('/notifications', [OfficerController::class, 'notifications'])->name('notifications');
    Route::post('/notification/mark-read', [OfficerController::class, 'markNotificationRead'])->name('notification.mark-read');
    Route::post('/notification/mark-all-read', [OfficerController::class, 'markAllNotificationsRead'])->name('notification.mark-all-read');

    // Redirect old export routes to send-report
    Route::get('/export', function() {
        return redirect()->route('officer.send.report');
    });
    
    Route::get('/export-csv', function() {
        return redirect()->route('officer.send.report');
    });
    
    Route::post('/verify-student', [OfficerController::class, 'verifyStudent'])->name('verify.student');
    Route::post('/verified/add', [OfficerController::class, 'addVerifiedStudent'])->name('verified.add');
    Route::delete('/verified/{id}', [OfficerController::class, 'removeVerifiedStudent'])->name('verified.remove');
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

// ==================== REPORT ROUTES ====================
Route::get('/reports/students', [AdminController::class, 'exportStudentsReport'])->name('reports.students');
Route::get('/reports/clearance', [AdminController::class, 'exportClearanceReport'])->name('reports.clearance');
Route::get('/reports/activity', [AdminController::class, 'exportActivityReport'])->name('reports.activity');

// ==================== VISITOR TRACKING ====================
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

// ==================== PUBLIC API FOR DEPARTMENT REQUIREMENTS ====================
Route::get('/api/departments/{department}/requirements', function($departmentId, Request $request) {
    $department = App\Models\Department::findOrFail($departmentId);
    $yearLevel = $request->get('year_level', '1st Year');
    $requirements = $department->getRequirementsForYear($yearLevel);

    return response()->json([
        'success' => true,
        'year_level' => $yearLevel,
        'requirements' => $requirements->map(function($req) {
            return [
                'id' => $req->id,
                'requirement_name' => $req->requirement_name,
                'is_required' => $req->is_required,
            ];
        })
    ]);
})->name('api.department.requirements');

// ==================== DEBUG FILES ====================
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

Route::get('/test-error', function() {
    return response()->json(['message' => 'Route is working']);
});

// ==================== DIRECT STAFF DASHBOARD (FOR TESTING) ====================
Route::get('/staff-direct', function() {
    $staff = \App\Models\Department::where('staff_email', 'aljey@gmail.com')->first();
    
    if (!$staff) {
        return "Staff not found!";
    }
    
    session([
        'staff_logged_in' => true,
        'staff_id' => $staff->id,
        'staff_name' => $staff->name ?? 'Staff',
        'staff_email' => $staff->staff_email,
        'staff_department_id' => $staff->id,
    ]);
    
    $user = new \App\Models\User();
    $user->id = $staff->id;
    $user->name = $staff->name ?? 'Staff';
    $user->email = $staff->staff_email;
    $user->role = 'staff';
    $user->department_id = $staff->id;
    $user->is_active = true;
    
    Auth::login($user);
    session()->save();
    
    return redirect()->route('staff.dashboard');
});
// Officer Notification Routes
Route::prefix('officer')->name('officer.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [OfficerController::class, 'dashboard'])->name('dashboard');
    Route::get('/students', [OfficerController::class, 'students'])->name('students');
    Route::get('/verified', [OfficerController::class, 'verified'])->name('verified');
    Route::get('/send-report', [OfficerController::class, 'sendReportPage'])->name('send.report');
    Route::post('/send-report', [OfficerController::class, 'sendReport'])->name('send.report.submit');
    Route::post('/verify-student', [OfficerController::class, 'verifyStudent'])->name('verify.student');
    Route::post('/verified/upload', [OfficerController::class, 'uploadVerifiedList'])->name('verified.upload');
    Route::post('/verified/add', [OfficerController::class, 'addVerifiedStudent'])->name('verified.add');
    Route::delete('/verified/{id}', [OfficerController::class, 'removeVerifiedStudent'])->name('verified.remove');
    Route::post('/export-csv', [OfficerController::class, 'exportVerifiedCSV'])->name('export.csv');
    Route::get('/download-export/{id}', [OfficerController::class, 'downloadExport'])->name('download.export');
    
    // ✅ NOTIFICATION ROUTES
    Route::get('/notifications', [OfficerController::class, 'notifications'])->name('notifications');
    Route::post('/notification/mark-read', [OfficerController::class, 'markNotificationRead'])->name('notification.mark-read');
    Route::post('/notification/mark-all-read', [OfficerController::class, 'markAllNotificationsRead'])->name('notification.mark-all-read');
});
// ==================== END OF ROUTES ====================