<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';
        $user = User::where($field, $request->username)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            
            // ============ EMAIL VERIFICATION CHECK (STUDENT ONLY) ============
            if ($user->role === 'student' && !$user->hasVerifiedEmail()) {
                session(['pending_verification_student_id' => $user->id]);
                $user->sendEmailVerificationNotification();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify your email first. A new verification code has been sent.',
                        'needs_verification' => true,
                        'redirect' => route('verification.notice')
                    ], 401);
                }
                return back()->with('error', 'Please verify your email before logging in.');
            }
            
            // ============ ACCOUNT ACTIVE CHECK ============
            if ($user->role === 'student' && !$user->is_active) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your account is deactivated. Please contact support.'
                    ], 401);
                }
                return back()->with('error', 'Your account is deactivated. Please contact support.');
            }
            
            // ============ 2FA CHECK FOR ADMIN AND SUPPORT ============
            if (in_array($user->role, ['admin', 'super_admin', 'support']) && $user->admin_2fa_enabled) {
                $code = rand(100000, 999999);
                
                $user->admin_2fa_code = $code;
                $user->admin_2fa_expires_at = now()->addMinutes(5);
                $user->save();
                
                $roleName = $user->role === 'support' ? 'Support' : ($user->role === 'super_admin' ? 'Super Admin' : 'Admin');
                
                try {
                    Mail::send([], [], function($message) use ($user, $code, $roleName) {
                        $message->to($user->email)
                                ->subject("🔐 {$roleName} 2FA Verification Code")
                                ->html("
                                    <!DOCTYPE html>
                                    <html>
                                    <head><meta charset='UTF-8'></head>
                                    <body style='font-family: Arial, sans-serif;'>
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
                                    </body>
                                    </html>
                                ");
                    });
                    Log::info('2FA code sent to: ' . $user->email);
                } catch (\Exception $e) {
                    Log::error('2FA email failed: ' . $e->getMessage());
                }
                
                session(['2fa_user_id' => $user->id]);
                $request->session()->regenerate();
                
                $redirectRoute = $user->role === 'support' ? route('support.2fa.verify') : route('admin.2fa.verify');
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'requires_2fa' => true,
                        'redirect' => $redirectRoute,
                        'message' => '2FA verification required. Code sent to your email.'
                    ]);
                }
                
                return redirect($redirectRoute)->with('info', 'Verification code sent to your email.');
            }
            
            // ============ DIRECT LOGIN (NO 2FA) ============
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // Determine redirect URL based on role
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
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'role' => $user->role,
                    'redirect' => $redirectUrl,
                    'message' => 'Login successful!',
                    'auto_login' => $request->input('auto_login', false),
                ]);
            }
            
            return redirect($redirectUrl);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/student ID or password'
            ], 401);
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}