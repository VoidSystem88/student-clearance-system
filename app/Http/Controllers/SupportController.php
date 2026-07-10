<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SupportRequest;
use App\Models\Feedback;
use App\Models\BugReport;
use App\Models\MaintenanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    // DASHBOARD PAGE - Stats only with Bug Reports
    public function dashboard()
    {
        $pendingCount = SupportRequest::where('status', 'pending')->count();
        $inProgressCount = SupportRequest::where('status', 'in_progress')->count();
        $resolvedCount = SupportRequest::where('status', 'resolved')->count();
        $totalStudents = User::where('role', 'student')->count();
        
        $bugReports = BugReport::orderBy('created_at', 'desc')->limit(10)->get();
        $pendingBugReports = BugReport::where('status', 'pending')->count();
        
        return view('support.dashboard', compact(
            'pendingCount', 
            'inProgressCount', 
            'resolvedCount', 
            'totalStudents',
            'bugReports',
            'pendingBugReports'
        ));
    }
    
    // ============ 2FA VERIFICATION METHODS ============
    
    public function show2FAVerifyForm()
    {
        if (!session('2fa_user_id')) {
            return redirect('/login');
        }
        
        $user = User::find(session('2fa_user_id'));
        
        if (!$user || !$user->admin_2fa_enabled) {
            return redirect('/login');
        }
        
        return view('support.2fa-verify');
    }
    
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return redirect('/login')->with('error', 'Session expired. Please login again.');
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->admin_2fa_enabled) {
            return redirect('/login')->with('error', '2FA is not enabled for this account.');
        }
        
        if ($user->admin_2fa_code === $request->code && now()->lessThan($user->admin_2fa_expires_at)) {
            $user->admin_2fa_code = null;
            $user->admin_2fa_expires_at = null;
            $user->save();
            
            session()->forget('2fa_user_id');
            Auth::login($user);
            $request->session()->regenerate();
            
            return redirect()->route('support.dashboard')->with('success', '2FA verified successfully!');
        }
        
        return back()->with('error', 'Invalid or expired verification code. Please try again.')->withInput();
    }
    
    public function resend2FACode(Request $request)
    {
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.'
            ]);
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->admin_2fa_enabled) {
            return response()->json([
                'success' => false,
                'message' => '2FA is not enabled for this account.'
            ]);
        }
        
        $code = rand(100000, 999999);
        
        $user->admin_2fa_code = $code;
        $user->admin_2fa_expires_at = now()->addMinutes(5);
        $user->save();
        
        try {
            Mail::send([], [], function($message) use ($user, $code) {
                $message->to($user->email)
                        ->subject('🔐 Support 2FA Verification Code')
                        ->html("
                            <!DOCTYPE html>
                            <html>
                            <head><meta charset='UTF-8'></head>
                            <body style='font-family: Arial, sans-serif;'>
                                <div style='max-width: 500px; margin: 0 auto; padding: 20px;'>
                                    <h2 style='color: #4F46E5;'>2FA Verification Code</h2>
                                    <p>Hello <strong>{$user->name}</strong>,</p>
                                    <p>Use the code below to complete your login:</p>
                                    <div style='background: #F3F4F6; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; margin: 20px 0;'>
                                        {$code}
                                    </div>
                                    <p>This code will expire in <strong>5 minutes</strong>.</p>
                                    <hr>
                                    <small>Clearance System Support Team</small>
                                </div>
                            </body>
                            </html>
                        ");
            });
            Log::info('2FA code resent to: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('2FA email failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send code. Please try again.'
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'New verification code sent to your email.'
        ]);
    }
    
    // ============ PROFILE METHODS ============

    public function profile()
    {
        return view('support.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $user->update([
            'name' => $request->name,
        ]);
        
        return redirect()->route('support.profile')->with('success', 'Profile updated successfully!');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);
        
        return redirect()->route('support.profile')->with('success', 'Password changed successfully!');
    }

    public function enable2FA(Request $request)
    {
        $user = auth()->user();
        
        $code = rand(100000, 999999);
        
        $user->update([
            'admin_2fa_enabled' => true,
            'admin_2fa_code' => $code,
            'admin_2fa_expires_at' => now()->addMinutes(10),
        ]);
        
        try {
            Mail::send([], [], function($message) use ($user, $code) {
                $message->to($user->email)
                        ->subject('2FA Enabled - Test Code')
                        ->html("<h2>Your Test Code: {$code}</h2><p>2FA has been enabled for your account.</p>");
            });
        } catch (\Exception $e) {
            Log::error('2FA email failed: ' . $e->getMessage());
        }
        
        return redirect()->route('support.profile')->with('success', '2FA enabled! Test code sent to your email.');
    }

    public function disable2FA(Request $request)
    {
        $user = auth()->user();
        
        $user->update([
            'admin_2fa_enabled' => false,
            'admin_2fa_code' => null,
            'admin_2fa_expires_at' => null,
        ]);
        
        return redirect()->route('support.profile')->with('success', '2FA disabled successfully!');
    }
    
    public function requests()
    {
        $supportRequests = SupportRequest::with('student')->orderBy('created_at', 'desc')->get();
        return view('support.requests', compact('supportRequests'));
    }
    
    public function studentsPage()
    {
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        
        // Kunin ang lahat ng active courses para sa dropdown
        $courses = \App\Models\Course::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return view('support.students', compact('students', 'courses'));
    }
    
    public function feedbacksPage()
    {
        $feedbacks = Feedback::with('user')->orderBy('created_at', 'desc')->get();
        return view('support.feedbacks', compact('feedbacks'));
    }
    
    public function announcements()
    {
        return view('support.announcements');
    }
    
    public function sendAnnouncement(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|string|in:general_update,shutdown',
            'metadata' => 'nullable|array'
        ]);
        
        $message = $request->message;
        $type = $request->type;
        $metadata = $request->metadata ?? [];
        
        $users = User::whereIn('role', ['student', 'staff', 'admin', 'support'])->get();
        
        $sentCount = 0;
        $failedCount = 0;
        
        foreach ($users as $user) {
            try {
                Mail::send([], [], function ($mail) use ($user, $message, $type, $metadata) {
                    $mail->to($user->email)
                        ->subject($type === 'shutdown' ? '⚠️ SYSTEM SHUTDOWN NOTICE' : '📢 System Update Announcement')
                        ->html($this->buildEmailBody($message, $type, $user->name, $metadata));
                });
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                Log::error("Failed to send announcement to {$user->email}: " . $e->getMessage());
            }
        }
        
        session(['active_announcement' => $message]);
        if ($type === 'shutdown') {
            session(['is_shutdown_mode' => true]);
        } else {
            session(['is_shutdown_mode' => false]);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Announcement sent to {$sentCount} users" . ($failedCount > 0 ? " ({$failedCount} failed)" : ""),
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);
        }
        
        return redirect()->back()->with('success', "Announcement sent to {$sentCount} users" . ($failedCount > 0 ? " ({$failedCount} failed)" : ""));
    }
    
    private function buildEmailBody($message, $type, $userName, $metadata = [])
    {
        $headerColor = $type === 'shutdown' ? '#dc2626' : '#2563eb';
        $icon = $type === 'shutdown' ? '⚠️' : '📢';
        $title = $type === 'shutdown' ? 'SYSTEM SHUTDOWN NOTICE' : 'SYSTEM UPDATE';
        
        $scheduleHtml = '';
        if ($type === 'shutdown' && (!empty($metadata['start_time']) || !empty($metadata['end_time']))) {
            $scheduleHtml = '<div style="background: #fef2f2; padding: 12px; border-radius: 8px; margin: 15px 0;">
                <strong style="color: #dc2626;">📅 SCHEDULE:</strong><br>';
            if (!empty($metadata['start_time'])) {
                $scheduleHtml .= '⏰ Start: ' . date('F d, Y g:i A', strtotime($metadata['start_time'])) . '<br>';
            }
            if (!empty($metadata['end_time'])) {
                $scheduleHtml .= '⏰ End: ' . date('F d, Y g:i A', strtotime($metadata['end_time'])) . '<br>';
            }
            $scheduleHtml .= '</div>';
        }
        
        return "
        <!DOCTYPE html>
        <html>
        <head><meta charset='UTF-8'><title>{$title}</title></head>
        <body>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden;'>
                <div style='background: {$headerColor}; padding: 20px; text-align: center;'>
                    <h1 style='color: white;'>{$icon} {$title}</h1>
                </div>
                <div style='padding: 30px;'>
                    <p>Dear {$userName},</p>
                    <div style='background: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid {$headerColor};'>
                        <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    </div>
                    {$scheduleHtml}
                    <p style='color: #6b7280; font-size: 14px;'>This is an automated message from the Clearance System Support Team.</p>
                </div>
                <div style='text-align: center; padding: 20px; color: #9ca3af; font-size: 12px;'>
                    <p>© " . date('Y') . " Clearance System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    public function editStudent($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        // Kunin ang mga active courses para sa dropdown
        $courses = \App\Models\Course::where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return response()->json([
            'id' => $student->id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'course' => $student->course,
            'year_level' => $student->year_level,
            'course_year' => $student->course_year ?? ($student->course . ' - ' . $student->year_level),
            'courses' => $courses,
        ]);
    }
    
    public function updateStudent(Request $request, $id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'course_year' => 'required|string',
        ]);
        
        $courseYear = $request->course_year;
        $parts = explode('-', $courseYear);
        $course = trim($parts[0]);
        $year = isset($parts[1]) ? trim($parts[1]) : '';
        
        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'course' => $course,
            'year_level' => $year,
            'course_year' => $request->course_year,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $student->update(['password' => Hash::make($request->password)]);
        }
        
        return redirect()->route('support.students')->with('success', "Student updated successfully!");
    }
    
    // ============ UPDATE STATUS WITH EMAIL NOTIFICATION ============
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,cancelled',
            'admin_notes' => 'nullable|string',
        ]);
        
        $supportRequest = SupportRequest::findOrFail($id);
        $oldStatus = $supportRequest->status;
        
        $supportRequest->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'resolved_at' => $request->status == 'resolved' ? now() : null,
        ]);
        
        // ============ SEND EMAIL TO STUDENT ============
        if ($oldStatus != $request->status) {
            try {
                $viewExists = view()->exists('emails.assistance-response');
                
                if ($viewExists) {
                    Mail::send('emails.assistance-response', [
                        'student' => $supportRequest->student,
                        'request' => $supportRequest,
                        'admin_notes' => $request->admin_notes ?? 'No additional notes provided.'
                    ], function($message) use ($supportRequest) {
                        $subject = match($supportRequest->status) {
                            'resolved' => '✅ Your Assistance Request Has Been Resolved',
                            'in_progress' => '🔄 Your Assistance Request Is Now In Progress',
                            'cancelled' => '❌ Your Assistance Request Has Been Cancelled',
                            default => '📋 Your Assistance Request Has Been Updated'
                        };
                        $message->to($supportRequest->student->email)
                                ->subject($subject . ' - Void Clearance System');
                    });
                } else {
                    Mail::raw("Your assistance request (#{$supportRequest->id}) status has been updated to: {$request->status}\n\nAdmin notes: " . ($request->admin_notes ?? 'None'), function($message) use ($supportRequest) {
                        $message->to($supportRequest->student->email)
                                ->subject('Assistance Request Update - Void Clearance System');
                    });
                }
                
                Log::info('Assistance request status email sent to: ' . $supportRequest->student->email);
            } catch (\Exception $e) {
                Log::error('Failed to send assistance request email: ' . $e->getMessage());
            }
        }
        // ===============================================
        
        return redirect()->route('support.requests')->with('success', 'Request status updated');
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'new_password' => 'required|string|min:8',
        ]);
        
        $student = User::where('id', $request->student_id)->where('role', 'student')->firstOrFail();
        $student->password = Hash::make($request->new_password);
        $student->save();
        
        return redirect()->route('support.students')->with('success', "Password reset successfully!");
    }
    
    public function resetAccountId(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);
        
        $student = User::where('id', $request->student_id)->where('role', 'student')->firstOrFail();
        
        $lastStudent = User::where('role', 'student')->orderBy('id', 'desc')->first();
        $lastNumber = $lastStudent ? intval(substr($lastStudent->account_id, -5)) : 0;
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        $newAccountId = 'CLR-' . date('Y') . '-' . $newNumber;
        
        $student->account_id = $newAccountId;
        $student->save();
        
        return redirect()->route('support.students')->with('success', "Account ID reset successfully!");
    }
    
    public function toggleActive($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        $student->is_active = !$student->is_active;
        $student->save();
        
        $status = $student->is_active ? 'activated' : 'deactivated';
        return redirect()->route('support.students')->with('success', "Student account {$status} successfully!");
    }
    
    // ============ RESPOND FEEDBACK WITH EMAIL NOTIFICATION ============
    public function respondFeedback(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'admin_response' => $request->admin_response,
            'status' => 'reviewed',
        ]);
        
        // ============ SEND EMAIL TO STUDENT ============
        try {
            $viewExists = view()->exists('emails.feedback-response');
            
            if ($viewExists && $feedback->user) {
                Mail::send('emails.feedback-response', [
                    'student' => $feedback->user,
                    'feedback' => $feedback,
                    'admin_response' => $request->admin_response
                ], function($message) use ($feedback) {
                    $message->to($feedback->user->email)
                            ->subject('📝 Feedback Response - Void Clearance System');
                });
            } else {
                Mail::raw("Your feedback has been reviewed.\n\nAdmin Response: " . $request->admin_response . "\n\nThank you for helping us improve the system.", function($message) use ($feedback) {
                    $message->to($feedback->user->email)
                            ->subject('Feedback Response - Void Clearance System');
                });
            }
            
            Log::info('Feedback response email sent to: ' . ($feedback->user->email ?? 'unknown'));
        } catch (\Exception $e) {
            Log::error('Failed to send feedback response email: ' . $e->getMessage());
        }
        // ===============================================
        
        return redirect()->route('support.feedbacks')->with('success', 'Response sent successfully!');
    }
    
    public function storeRequest(Request $request)
    {
        $request->validate([
            'request_type' => 'required|string',
            'description' => 'required|string',
        ]);
        
        $supportRequest = SupportRequest::create([
            'student_id' => auth()->id(),
            'request_type' => $request->request_type,
            'description' => $request->description,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        
        return redirect()->route('support.requests')->with('success', 'Support request submitted. Reference #: ' . $supportRequest->id);
    }
    
    public function updateBugReport(Request $request, $id)
    {
        $report = BugReport::findOrFail($id);
        
        $report->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
        ]);
        
        return redirect()->back()->with('success', 'Bug report updated successfully!');
    }
    
    public function viewAttachment($id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        
        if (!$supportRequest->attachment_path) {
            abort(404, 'No attachment found for this request');
        }
        
        $filename = basename($supportRequest->attachment_path);
        return redirect(url('/file/' . $filename));
    }
    
    public function downloadAttachment($id)
    {
        $supportRequest = SupportRequest::findOrFail($id);
        
        if (!$supportRequest->attachment_path) {
            return redirect()->back()->with('error', 'No attachment found.');
        }
        
        $filename = basename($supportRequest->attachment_path);
        return redirect(url('/file/' . $filename . '?download=1'));
    }
    
    public function getRequest($id)
    {
        $supportRequest = SupportRequest::with('student')->findOrFail($id);
        
        $attachmentUrl = null;
        if ($supportRequest->attachment_path) {
            $filename = basename($supportRequest->attachment_path);
            $attachmentUrl = url('/file/' . $filename);
        }
        
        return response()->json([
            'id' => $supportRequest->id,
            'student_name' => $supportRequest->student->first_name . ' ' . $supportRequest->student->last_name,
            'student_id' => $supportRequest->student->student_id,
            'request_type' => $supportRequest->request_type,
            'description' => $supportRequest->description,
            'status' => $supportRequest->status,
            'admin_notes' => $supportRequest->admin_notes,
            'attachment_path' => $attachmentUrl,
            'attachment_original_name' => $supportRequest->attachment_original_name,
            'created_at' => $supportRequest->created_at->format('F d, Y h:i A'),
            'resolved_at' => $supportRequest->resolved_at ? $supportRequest->resolved_at->format('F d, Y h:i A') : null,
        ]);
    }
    
    // ============ MAINTENANCE / SHUTDOWN METHODS ============
    
    public function maintenance()
    {
        if (auth()->user()->role !== 'support') {
            abort(403, 'Unauthorized access. Only Support can access maintenance page.');
        }
        
        $isMaintenanceMode = Cache::get('maintenance_mode', false);
        $isReadOnlyMode = Cache::get('read_only_mode', false);
        $maintenanceMessage = Cache::get('maintenance_message', '');
        $maintenanceEndTime = Cache::get('maintenance_end_time');
        $maintenanceModeType = Cache::get('maintenance_mode_type', 'soft');
        
        $recentMaintenance = collect();
        if (Schema::hasTable('maintenance_logs')) {
            try {
                $recentMaintenance = MaintenanceLog::with('initiatedBy')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                Log::warning('Could not fetch maintenance logs: ' . $e->getMessage());
            }
        }
        
        return view('support.maintenance', compact(
            'isMaintenanceMode', 'isReadOnlyMode', 'maintenanceMessage',
            'maintenanceEndTime', 'maintenanceModeType', 'recentMaintenance'
        ));
    }
    
    public function enableSoftShutdown(Request $request)
    {
        if (auth()->user()->role !== 'support') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'message' => 'required|string|max:500',
            'duration_hours' => 'required|integer|min:1|max:24',
        ]);
        
        $endTime = now()->addHours($request->duration_hours);
        
        Cache::put('read_only_mode', true, $endTime);
        Cache::put('read_only_message', $request->message, $endTime);
        Cache::put('maintenance_end_time', $endTime, $endTime);
        Cache::put('maintenance_mode_type', 'soft', $endTime);
        
        if (Schema::hasTable('maintenance_logs')) {
            try {
                MaintenanceLog::create([
                    'initiated_by' => auth()->id(),
                    'mode' => 'soft',
                    'message' => $request->message,
                    'start_time' => now(),
                    'end_time' => $endTime,
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                Log::warning('Could not save to maintenance_logs: ' . $e->getMessage());
            }
        }
        
        $this->sendMaintenanceAlert($request->message, $request->duration_hours, 'soft');
        
        return response()->json([
            'success' => true,
            'message' => 'Soft shutdown enabled. System is now read-only.',
            'end_time' => $endTime->toDateTimeString()
        ]);
    }
    
    public function enableFullShutdown(Request $request)
    {
        if (auth()->user()->role !== 'support') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'message' => 'required|string|max:500',
            'duration_hours' => 'required|integer|min:1|max:12',
        ]);
        
        $endTime = now()->addHours($request->duration_hours);
        
        Cache::put('maintenance_mode', true, $endTime);
        Cache::put('maintenance_message', $request->message, $endTime);
        Cache::put('maintenance_mode_type', 'full', $endTime);
        Cache::put('maintenance_end_time', $endTime, $endTime);
        
        if (Schema::hasTable('maintenance_logs')) {
            try {
                MaintenanceLog::create([
                    'initiated_by' => auth()->id(),
                    'mode' => 'full',
                    'message' => $request->message,
                    'start_time' => now(),
                    'end_time' => $endTime,
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                Log::warning('Could not save to maintenance_logs: ' . $e->getMessage());
            }
        }
        
        $this->sendMaintenanceAlert($request->message, $request->duration_hours, 'full');
        
        return response()->json([
            'success' => true,
            'message' => 'Full shutdown enabled. System is now completely offline.',
            'end_time' => $endTime->toDateTimeString()
        ]);
    }
    
    public function disableMaintenance(Request $request)
    {
        if (auth()->user()->role !== 'support') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        Cache::forget('maintenance_mode');
        Cache::forget('read_only_mode');
        Cache::forget('maintenance_message');
        Cache::forget('read_only_message');
        Cache::forget('maintenance_end_time');
        Cache::forget('maintenance_mode_type');
        
        if (Schema::hasTable('maintenance_logs')) {
            try {
                $latestLog = MaintenanceLog::where('is_active', true)->latest()->first();
                if ($latestLog) {
                    $latestLog->update(['is_active' => false]);
                }
            } catch (\Exception $e) {
                Log::warning('Could not update maintenance log: ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Maintenance mode disabled. System back to normal operation.'
        ]);
    }
    
    public function maintenanceStatus()
    {
        return response()->json([
            'maintenance_mode' => Cache::get('maintenance_mode', false),
            'read_only_mode' => Cache::get('read_only_mode', false),
            'message' => Cache::get('maintenance_message') ?? Cache::get('read_only_message'),
            'end_time' => Cache::get('maintenance_end_time'),
            'mode_type' => Cache::get('maintenance_mode_type', 'none')
        ]);
    }
    
    public function clearMaintenanceAuth(Request $request)
    {
        $request->session()->forget('maintenance_authenticated');
        return redirect()->route('support.maintenance.password')
            ->with('success', 'You have been logged out of maintenance mode.');
    }
    
    private function sendMaintenanceAlert($message, $durationHours, $mode)
    {
        $users = User::whereIn('role', ['student', 'staff', 'admin', 'support'])->get();
        $modeText = $mode === 'full' ? 'FULL SYSTEM SHUTDOWN' : 'READ-ONLY MAINTENANCE';
        $icon = $mode === 'full' ? '⚠️' : '🚧';
        
        foreach ($users as $user) {
            try {
                Mail::send([], [], function ($mail) use ($user, $message, $durationHours, $modeText, $icon) {
                    $mail->to($user->email)
                        ->subject("{$icon} {$modeText} - Clearance System")
                        ->html("
                        <html>
                        <body>
                            <h2>{$icon} {$modeText}</h2>
                            <p>Dear {$user->name},</p>
                            <p><strong>Maintenance Notice:</strong> " . nl2br(htmlspecialchars($message)) . "</p>
                            <p><strong>Duration:</strong> Approximately {$durationHours} hour(s)</p>
                            <p><strong>Started:</strong> " . now()->format('F d, Y g:i A') . "</p>
                            <p>We apologize for the inconvenience.</p>
                            <hr>
                            <small>Clearance System Support Team</small>
                        </body>
                        </html>
                        ");
                });
            } catch (\Exception $e) {
                Log::error("Failed to send maintenance alert to {$user->email}: " . $e->getMessage());
            }
        }
    }
    
    public function bulkYearUpdate(Request $request)
    {
        $request->validate([
            'selection_type' => 'required|in:all,by_course,selected',
            'from_year' => 'required|string',
            'to_year' => 'required|string',
            'course' => 'nullable|string',
            'student_ids' => 'nullable|array',
        ]);
        
        $query = User::where('role', 'student')
            ->where('year_level', $request->from_year);
        
        if ($request->selection_type === 'by_course' && $request->course) {
            $query->where('course', $request->course);
        }
        
        if ($request->selection_type === 'selected' && $request->student_ids) {
            $query->whereIn('id', $request->student_ids);
        }
        
        $students = $query->get();
        $count = $students->count();
        
        if ($count === 0) {
            return redirect()->back()->with('error', 'No students found to update.');
        }
        
        foreach ($students as $student) {
            $student->update([
                'year_level' => $request->to_year,
                'course_year' => $student->course . ' - ' . $request->to_year,
            ]);
            
            // Kung nag-graduate na, i-reset ang clearance status para fresh start
            if ($request->to_year === 'Graduated') {
                $student->update(['is_cleared' => false]);
                \App\Models\ClearanceRequest::where('student_id', $student->id)->delete();
            }
        }
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'bulk_year_update',
            'module' => 'Student',
            'description' => "Updated {$count} students from {$request->from_year} to {$request->to_year}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('support.students')->with('success', "✅ {$count} students updated from {$request->from_year} to {$request->to_year}");
    }

    // ============ NOTIFICATION METHODS ============

    public function notifications()
    {
        $bugReports = BugReport::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $supportRequests = SupportRequest::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $feedbacks = Feedback::where('status', 'pending')->orderBy('created_at', 'desc')->get();
        
        return view('support.notifications', compact('bugReports', 'supportRequests', 'feedbacks'));
    }

    public function getNotificationCounts()
    {
        $bugReportsCount = BugReport::where('status', 'pending')->count();
        $supportRequestsCount = SupportRequest::where('status', 'pending')->count();
        $feedbacksCount = Feedback::where('status', 'pending')->count();
        
        $total = $bugReportsCount + $supportRequestsCount + $feedbacksCount;
        
        // Get recent items for dropdown
        $recentBugReports = BugReport::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'bug_report',
                    'title' => '🐛 Bug Report',
                    'message' => ($item->name ?? 'Anonymous') . ' reported: ' . ucfirst(str_replace('_', ' ', $item->type)),
                    'created_at' => $item->created_at,
                    'link' => route('support.dashboard') . '#bug-reports',
                    'icon' => 'fa-bug',
                    'color' => 'red',
                    'status' => $item->status
                ];
            });
        
        $recentRequests = SupportRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'support_request',
                    'title' => '🎫 Support Request',
                    'message' => ($item->student->first_name ?? 'Student') . ' needs assistance: ' . ucfirst(str_replace('_', ' ', $item->request_type)),
                    'created_at' => $item->created_at,
                    'link' => route('support.requests'),
                    'icon' => 'fa-ticket-alt',
                    'color' => 'blue',
                    'status' => $item->status
                ];
            });
        
        $recentFeedbacks = Feedback::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'feedback',
                    'title' => '⭐ New Feedback',
                    'message' => ($item->user->first_name ?? 'Student') . ' gave ' . $item->rating . '⭐ feedback',
                    'created_at' => $item->created_at,
                    'link' => route('support.feedbacks'),
                    'icon' => 'fa-star',
                    'color' => 'purple',
                    'status' => $item->status
                ];
            });
        
        // Combine and sort by date
        $recentItems = collect()
            ->concat($recentBugReports)
            ->concat($recentRequests)
            ->concat($recentFeedbacks)
            ->sortByDesc('created_at')
            ->values()
            ->take(10);
        
        return response()->json([
            'success' => true,
            'total' => $total,
            'bug_reports' => $bugReportsCount,
            'support_requests' => $supportRequestsCount,
            'feedbacks' => $feedbacksCount,
            'recent_items' => $recentItems
        ]);
    }

    public function markAllNotificationsAsRead()
    {
        // Store timestamp when all notifications were viewed
        session(['support_all_notifications_viewed_at' => now()]);
        
        return response()->json(['success' => true]);
    }
        // ============ REMINDERS METHODS ============

    public function reminders()
    {
        $reminders = \App\Models\Reminder::with('creator', 'department')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $departments = \App\Models\Department::where('is_active', true)->get();
        
        return view('support.reminders', compact('reminders', 'departments'));
    }

    public function storeReminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:staff,officer,both',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        $reminder = \App\Models\Reminder::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
            'department_id' => $request->department_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
            'created_by' => auth()->id(),
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'created',
            'module' => 'Reminder',
            'description' => "Created reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('support.reminders')->with('success', 'Reminder created successfully!');
    }

    public function editReminder($id)
    {
        $reminder = \App\Models\Reminder::findOrFail($id);
        return response()->json($reminder);
    }

    public function updateReminder(Request $request, $id)
    {
        $reminder = \App\Models\Reminder::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:staff,officer,both',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);

        $reminder->update([
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
            'department_id' => $request->department_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? $request->is_active : $reminder->is_active,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Reminder',
            'description' => "Updated reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('support.reminders')->with('success', 'Reminder updated successfully!');
    }

    public function destroyReminder($id)
    {
        $reminder = \App\Models\Reminder::findOrFail($id);
        $title = $reminder->title;
        $reminder->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'deleted',
            'module' => 'Reminder',
            'description' => "Deleted reminder: {$title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('support.reminders')->with('success', 'Reminder deleted successfully!');
    }

    public function toggleReminder($id)
    {
        $reminder = \App\Models\Reminder::findOrFail($id);
        $reminder->update(['is_active' => !$reminder->is_active]);
        
        $status = $reminder->is_active ? 'activated' : 'deactivated';
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Reminder',
            'description' => "{$status} reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('support.reminders')->with('success', "Reminder {$status} successfully!");
    }
}