<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return redirect('/');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (filter_var($request->username, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $request->username)->first();
            
            if (!$user) {
                $staff = Department::where('staff_email', $request->username)->first();
                
                if ($staff && Hash::check($request->password, $staff->staff_password)) {
                    $user = User::updateOrCreate(
                        ['email' => $staff->staff_email],
                        [
                            'name' => $staff->name ?? 'Staff',
                            'password' => $staff->staff_password,
                            'role' => 'staff',
                            'department_id' => $staff->id,
                            'is_active' => true,
                        ]
                    );
                    
                    session([
                        'staff_logged_in' => true,
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->name ?? 'Staff',
                        'staff_email' => $staff->staff_email,
                        'staff_department_id' => $staff->id,
                    ]);
                    
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();
                    
                    if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => true,
                            'role' => 'staff',
                            'redirect' => route('staff.dashboard'),
                            'message' => 'Login successful!'
                        ]);
                    }
                    
                    return redirect()->route('staff.dashboard');
                }
            } else {
                if ($user->role === 'staff' && !$user->department_id) {
                    $dept = Department::where('staff_email', $user->email)->first();
                    if ($dept) {
                        $user->update(['department_id' => $dept->id]);
                        session(['staff_department_id' => $dept->id]);
                    }
                }
            }
        } else {
            if (!preg_match('/^\d{4}-\d{5}$/', $request->username)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Student ID format. Use: YYYY-XXXXX'
                ], 422);
            }
            
            $user = User::where('student_id', $request->username)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID not found'
                ], 401);
            }
        }

        if ($user && Hash::check($request->password, $user->password)) {
            
            if ($user->role === 'student' && !$user->hasVerifiedEmail()) {
                session(['pending_verification_student_id' => $user->id]);
                $user->sendEmailVerificationNotification();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email first. A new verification code has been sent.',
                    'needs_verification' => true,
                    'redirect' => route('verification.notice')
                ], 401);
            }
            
            if ($user->role === 'student' && !$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your account is deactivated. Please contact support.'
                ], 401);
            }
            
            if (in_array($user->role, ['admin', 'support']) && $user->admin_2fa_enabled) {
                $code = rand(100000, 999999);
                $user->admin_2fa_code = $code;
                $user->admin_2fa_expires_at = now()->addMinutes(5);
                $user->save();
                
                $roleName = $user->role === 'support' ? 'Support' : 'Admin';
                
                try {
                    Mail::send([], [], function($message) use ($user, $code, $roleName) {
                        $message->to($user->email)
                                ->subject("🔐 {$roleName} 2FA Verification Code")
                                ->html("
                                    <div style='max-width: 500px; margin: 0 auto; padding: 20px;'>
                                        <h2 style='color: #4F46E5;'>Verification Code</h2>
                                        <p>Hello <strong>{$user->name}</strong>,</p>
                                        <p>Use the code below to complete your login:</p>
                                        <div style='background: #F3F4F6; padding: 15px; text-align: center; font-size: 32px; font-weight: bold; letter-spacing: 5px; border-radius: 8px; margin: 20px 0;'>
                                            {$code}
                                        </div>
                                        <p>This code will expire in <strong>5 minutes</strong>.</p>
                                        <hr>
                                        <small>Clearance System Support Team</small>
                                    </div>
                                ");
                    });
                } catch (\Exception $e) {
                    Log::error('2FA email failed: ' . $e->getMessage());
                }
                
                session(['2fa_user_id' => $user->id]);
                $request->session()->regenerate();
                
                $redirectRoute = $user->role === 'support' ? route('support.2fa.verify') : route('admin.2fa.verify');
                
                return response()->json([
                    'success' => true,
                    'requires_2fa' => true,
                    'redirect' => $redirectRoute,
                    'message' => '2FA verification required. Code sent to your email.'
                ]);
            }
            
            // ============ DIRECT LOGIN ============
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            if ($user->role === 'staff' && !$user->department_id) {
                $dept = Department::where('staff_email', $user->email)->first();
                if ($dept) {
                    $user->update(['department_id' => $dept->id]);
                    session(['staff_department_id' => $dept->id]);
                }
            }
            
            // ✅ AI POPUP LOGIC
            if ($user->role === 'student') {
                $loginCount = session('login_count', 0) + 1;
                session(['login_count' => $loginCount]);
                
                if (!session('ai_popup_shown')) {
                    session(['show_ai_popup' => true]);
                    session(['ai_popup_shown' => true]);
                } elseif ($loginCount % 5 === 0) {
                    session(['show_ai_popup' => true]);
                }
            }
            
            $redirectUrl = '/';
            if (in_array($user->role, ['admin', 'super_admin'])) {
                $redirectUrl = route('admin.dashboard');
            } elseif ($user->role === 'staff') {
                $redirectUrl = route('staff.dashboard');
            } elseif ($user->role === 'support') {
                $redirectUrl = route('support.dashboard');
            } elseif ($user->role === 'officer') {
                $redirectUrl = route('officer.dashboard');
            } else {
                $redirectUrl = route('student.dashboard');
            }
            
            return response()->json([
                'success' => true,
                'role' => $user->role,
                'redirect' => $redirectUrl,
                'message' => 'Login successful!',
                'auto_login' => $request->input('auto_login', false),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        session()->forget(['staff_logged_in', 'staff_id', 'staff_name', 'staff_email', 'staff_department_id']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}