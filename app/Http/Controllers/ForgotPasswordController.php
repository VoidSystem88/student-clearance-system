<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return redirect('/#forgot-panel');
    }

    // Email-based forgot password (NEW)
    public function sendOtpViaEmail(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'email' => 'required|email',
        ]);

        $user = User::where('student_id', $request->student_id)
            ->where('email', $request->email)
            ->where('role', 'student')
            ->first();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid Student ID or Email combination']);
            }
            return redirect('/#forgot-panel')->with('error', 'Invalid Student ID or Email combination');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old OTPs
        \DB::table('password_resets')->where('student_id', $user->student_id)->delete();

        // Save OTP
        PasswordReset::create([
            'student_id' => $user->student_id,
            'account_id' => $user->account_id,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false,
        ]);

        // Send email with OTP
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'name' => $user->first_name], function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset OTP - Clearance System');
            });
            
            session(['reset_student_id' => $user->student_id, 'reset_account_id' => $user->account_id]);
            
            Log::info('OTP sent via email to: ' . $user->email);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'OTP sent to your email']);
            }
            return redirect('/#otp-panel')->with('success', 'OTP sent to your email');
            
        } catch (\Exception $e) {
            Log::error('OTP Email Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again later.']);
            }
            return redirect('/#forgot-panel')->with('error', 'Failed to send OTP. Please try again later.');
        }
    }

    // Account ID-based forgot password (OLD)
    public function sendOtp(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'account_id' => 'required|string',
        ]);

        $user = User::where('student_id', $request->student_id)
            ->where('account_id', $request->account_id)
            ->where('role', 'student')
            ->first();

        if (!$user) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid Student ID or Account ID combination']);
            }
            return redirect('/#forgot-panel')->with('error', 'Invalid Student ID or Account ID combination');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old OTPs
        \DB::table('password_resets')->where('student_id', $user->student_id)->delete();

        // Save OTP
        PasswordReset::create([
            'student_id' => $user->student_id,
            'account_id' => $user->account_id,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'used' => false,
        ]);

        // Send email
        try {
            Mail::send('emails.otp', ['otp' => $otp, 'name' => $user->first_name], function($message) use ($user) {
                $message->to($user->email)
                        ->subject('Password Reset OTP - Clearance System');
            });
            
            session(['reset_student_id' => $user->student_id, 'reset_account_id' => $user->account_id]);
            
            Log::info('OTP sent via Account ID to: ' . $user->email);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'OTP sent to your email']);
            }
            return redirect('/#otp-panel')->with('success', 'OTP sent to your email');
            
        } catch (\Exception $e) {
            Log::error('OTP Email Error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
            }
            return redirect('/#forgot-panel')->with('error', 'Failed to send OTP. Please try again.');
        }
    }

    public function showVerifyForm()
    {
        if (!session('reset_student_id')) {
            return redirect('/#forgot-panel');
        }
        return redirect('/#otp-panel');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $reset = PasswordReset::where('student_id', session('reset_student_id'))
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired OTP']);
            }
            return redirect('/#otp-panel')->with('error', 'Invalid or expired OTP');
        }

        $reset->update(['used' => true]);
        
        Log::info('OTP verified for student_id: ' . session('reset_student_id'));

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'OTP verified successfully']);
        }
        return redirect('/#reset-panel')->with('success', 'OTP verified. Please set your new password.');
    }

    public function showResetForm()
    {
        if (!session('reset_student_id')) {
            return redirect('/#forgot-panel');
        }
        return redirect('/#reset-panel');
    }

    public function resetPassword(Request $request)
    {
        // Log for debugging
        Log::info('=== RESET PASSWORD REQUEST ===');
        Log::info('Session reset_student_id: ' . session('reset_student_id'));
        Log::info('Session reset_account_id: ' . session('reset_account_id'));
        Log::info('Request has password: ' . ($request->has('password') ? 'YES' : 'NO'));
        
        // Validate
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get session data
        $studentId = session('reset_student_id');
        $accountId = session('reset_account_id');
        
        // Check if session exists
        if (!$studentId || !$accountId) {
            Log::error('Session expired - missing student_id or account_id');
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Session expired. Please restart the password reset process.']);
            }
            return redirect('/#forgot-panel')->with('error', 'Session expired. Please restart the process.');
        }

        // Find user
        $user = User::where('student_id', $studentId)
            ->where('account_id', $accountId)
            ->where('role', 'student')
            ->first();

        if (!$user) {
            Log::error('User not found: student_id=' . $studentId . ', account_id=' . $accountId);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'User not found. Please restart the process.']);
            }
            return redirect('/#forgot-panel')->with('error', 'User not found.');
        }

        // Update password
        $hashedPassword = Hash::make($request->password);
        $user->password = $hashedPassword;
        $user->save();
        
        Log::info('Password updated for user: ' . $user->email);
        
        // Verify if password was saved correctly
        $verifyUser = User::find($user->id);
        $passwordMatches = Hash::check($request->password, $verifyUser->password);
        
        if ($passwordMatches) {
            Log::info('Password verification SUCCESS for user: ' . $user->email);
            
            // Clear session
            session()->forget(['reset_student_id', 'reset_account_id']);
            
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Password reset successfully! Please login with your new password.']);
            }
            return redirect('/#login-panel')->with('success', 'Password reset successfully! Please login with your new password.');
        } else {
            Log::error('Password verification FAILED for user: ' . $user->email);
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Failed to save password. Please try again.']);
            }
            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
}