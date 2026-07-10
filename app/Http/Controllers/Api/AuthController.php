<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';
        $user = User::where($field, $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'student_id' => $user->student_id,
                'account_id' => $user->account_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'course' => $user->course,
                'is_cleared' => $user->is_cleared,
            ]
        ]);
    }

    // Register
    public function register(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:users,student_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'course' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $lastStudent = User::where('role', 'student')->orderBy('id', 'desc')->first();
        $lastNumber = $lastStudent ? intval(substr($lastStudent->account_id, -5)) : 0;
        $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        $accountId = 'CLR-' . date('Y') . '-' . $newNumber;

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'student_id' => $request->student_id,
            'account_id' => $accountId,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'course' => $request->course,
            'is_active' => true,
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'student_id' => $user->student_id,
                'account_id' => $user->account_id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'course' => $user->course,
            ]
        ], 201);
    }

    // Get authenticated user
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    // Forgot password - send OTP
    public function forgotPassword(Request $request)
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
            return response()->json([
                'success' => false,
                'message' => 'Invalid Student ID or Account ID combination'
            ], 404);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordReset::updateOrCreate(
            ['student_id' => $user->student_id],
            [
                'account_id' => $user->account_id,
                'otp' => $otp,
                'expires_at' => Carbon::now()->addMinutes(10),
                'used' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully',
            'otp' => $otp // For testing only - remove in production
        ]);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $reset = PasswordReset::where('student_id', $request->student_id)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        $reset->update(['used' => true]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified. You can now reset your password.'
        ]);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('student_id', $request->student_id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully'
        ]);
    }
}