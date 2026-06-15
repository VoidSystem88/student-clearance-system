<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminProfileController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('web')->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return redirect()->route('home');
        }
        
        return view('admin.profile', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('web')->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return redirect()->route('home');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        $admin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);
        
        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
        }
        
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    public function toggle2FA(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($admin->admin_2fa_enabled) {
            $admin->admin_2fa_enabled = false;
            $admin->admin_2fa_code = null;
            $admin->admin_2fa_expires_at = null;
            $admin->save();
            
            return response()->json(['success' => true, 'enabled' => false, 'message' => '2FA disabled successfully!']);
        } else {
            $code = rand(100000, 999999);
            
            $admin->admin_2fa_enabled = true;
            $admin->admin_2fa_code = $code;
            $admin->admin_2fa_expires_at = now()->addMinutes(10);
            $admin->save();
            
            try {
                Mail::send([], [], function($message) use ($admin, $code) {
                    $message->to($admin->email)
                            ->subject('2FA Enabled - Test Code')
                            ->html("
                                <div style='font-family: Arial, sans-serif;'>
                                    <h2>2FA has been enabled for your account</h2>
                                    <p>Your test code: <strong style='font-size: 24px;'>{$code}</strong></p>
                                    <p>This code will expire in 10 minutes.</p>
                                    <p>You will receive a new code each time you login.</p>
                                </div>
                            ");
                });
            } catch (\Exception $e) {
                \Log::error('2FA email failed: ' . $e->getMessage());
            }
            
            return response()->json(['success' => true, 'enabled' => true, 'message' => '2FA enabled successfully! Test code sent to your email.']);
        }
    }
}