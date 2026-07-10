<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Admin2FAController extends Controller
{
    public function showVerifyForm()
    {
        // Check if user is already authenticated OR has pending 2FA session
        $user = Auth::user();
        $pendingUserId = session('2fa_user_id');
        
        if (!$user && !$pendingUserId) {
            return redirect('/')->with('error', 'Session expired. Please login again.');
        }
        
        // If pending user exists, we don't need to check Auth::user()
        if ($pendingUserId) {
            $user = User::find($pendingUserId);
        }
        
        if (!$user) {
            return redirect('/')->with('error', 'User not found. Please login again.');
        }
        
        // Allow admin, super_admin, and support
        if (!in_array($user->role, ['admin', 'super_admin', 'support'])) {
            Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Unauthorized access');
        }
        
        // If already verified, redirect to appropriate dashboard
        if (session('2fa_verified')) {
            if ($user->role === 'support') {
                return redirect()->route('support.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }
        
        return view('admin.2fa-verify');
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);
        
        // Get user from session (pending 2FA) or from auth
        $pendingUserId = session('2fa_user_id');
        $user = null;
        
        if ($pendingUserId) {
            $user = User::find($pendingUserId);
        }
        
        if (!$user && Auth::check()) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return redirect('/')->with('error', 'Session expired. Please login again.');
        }
        
        // Allow admin, super_admin, and support
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'support'])) {
            abort(403, 'Unauthorized access.');
        }
        
        // Check if code matches and not expired
        if ($user->admin_2fa_code == $request->code && $user->admin_2fa_expires_at > now()) {
            // Clear the code
            $user->admin_2fa_code = null;
            $user->admin_2fa_expires_at = null;
            $user->save();
            
            // Now login the user (if not already logged in)
            if (!Auth::check()) {
                Auth::login($user);
            }
            
            // Set session that 2FA is verified
            session(['2fa_verified' => true]);
            session()->forget('2fa_user_id');
            session()->forget('2fa_failed_attempts');
            session()->regenerate();
            
            // Redirect based on role
            if ($user->role === 'support') {
                return redirect()->route('support.dashboard')->with('success', '2FA verified successfully!');
            }
            
            return redirect()->route('admin.dashboard')->with('success', '2FA verified successfully!');
        }
        
        // Increment failed attempts
        $attempts = session('2fa_failed_attempts', 0) + 1;
        session(['2fa_failed_attempts' => $attempts]);
        
        if ($attempts >= 3) {
            Auth::logout();
            session()->flush();
            return redirect('/')->with('error', 'Too many failed attempts. Please login again.');
        }
        
        return back()->with('error', 'Invalid or expired verification code. Attempt ' . $attempts . '/3');
    }
    
    public function resendCode(Request $request)
    {
        // Get user from session or auth
        $pendingUserId = session('2fa_user_id');
        $user = null;
        
        if ($pendingUserId) {
            $user = User::find($pendingUserId);
        }
        
        if (!$user && Auth::check()) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 404);
        }
        
        // Allow admin, super_admin, and support
        if (!in_array($user->role, ['admin', 'super_admin', 'support'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $code = rand(100000, 999999);
        $user->admin_2fa_code = $code;
        $user->admin_2fa_expires_at = now()->addMinutes(5);
        $user->save();
        
        $roleName = $user->role === 'support' ? 'Support' : ($user->role === 'super_admin' ? 'Super Admin' : 'Admin');
        
        try {
            Mail::send([], [], function($message) use ($user, $code, $roleName) {
                $message->to($user->email)
                        ->subject("🔐 {$roleName} New Verification Code")
                        ->html("
                            <!DOCTYPE html>
                            <html>
                            <head><meta charset='UTF-8'></head>
                            <body style='font-family: Arial, sans-serif;'>
                                <div style='max-width: 500px; margin: 0 auto; padding: 20px;'>
                                    <h2 style='color: #4F46E5;'>New Verification Code</h2>
                                    <p>Hello <strong>{$user->name}</strong>,</p>
                                    <p>Your new verification code is:</p>
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
            
            // Reset failed attempts
            session(['2fa_failed_attempts' => 0]);
            
            return response()->json(['success' => true, 'message' => 'New verification code sent to your email']);
        } catch (\Exception $e) {
            Log::error('2FA email failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send email'], 500);
        }
    }
    
    public function enable()
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'support'])) {
            return redirect('/')->with('error', 'Unauthorized');
        }
        
        $user->admin_2fa_enabled = true;
        $user->save();
        
        // Redirect based on role
        if ($user->role === 'support') {
            return redirect()->route('support.dashboard')->with('success', '2FA enabled successfully!');
        }
        
        return redirect()->route('admin.dashboard')->with('success', '2FA enabled successfully!');
    }
    
    public function disable()
    {
        $user = Auth::user();
        
        if (!$user || !in_array($user->role, ['admin', 'super_admin', 'support'])) {
            return redirect('/')->with('error', 'Unauthorized');
        }
        
        $user->admin_2fa_enabled = false;
        $user->admin_2fa_code = null;
        $user->admin_2fa_expires_at = null;
        $user->save();
        
        session()->forget('2fa_verified');
        
        // Redirect based on role
        if ($user->role === 'support') {
            return redirect()->route('support.dashboard')->with('success', '2FA disabled successfully!');
        }
        
        return redirect()->route('admin.dashboard')->with('success', '2FA disabled successfully!');
    }
}