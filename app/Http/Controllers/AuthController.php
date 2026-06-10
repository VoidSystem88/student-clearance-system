<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Send OTP for email verification
    public function sendVerificationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'student_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'birthdate' => 'required',
            'course' => 'required',
            'year_level' => 'required',
            'password' => 'required|min:8'
        ]);

        // I-save ang buong registration data sa session
        session(['temp_registration_data' => $request->all()]);
        
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        
        // Store in session
        session([
            'reg_otp' => $otp,
            'reg_email' => $request->email,
            'reg_student_id' => $request->student_id,
            'reg_otp_expires' => Carbon::now()->addMinutes(10)
        ]);
        
        // Send email
        try {
            Mail::raw("Your verification code is: $otp\n\nThis code will expire in 10 minutes.\n\nIf you did not request this, please ignore this email.", function($message) use ($request) {
                $message->to($request->email)
                        ->subject('Email Verification - Student Clearance System');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Verification code sent to your email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
    }
    
    // Verify OTP and Register
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6'
        ]);
        
        $storedOtp = session('reg_otp');
        $storedEmail = session('reg_email');
        $expires = session('reg_otp_expires');
        
        if (!$storedOtp || !$storedEmail) {
            return response()->json([
                'success' => false,
                'message' => 'No verification code found. Please request a new one.'
            ]);
        }
        
        if (Carbon::now()->gt($expires)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.'
            ]);
        }
        
        if ($storedOtp != $request->otp || $storedEmail != $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code'
            ]);
        }
        
        // Kunin ang registration data mula sa session
        $studentData = session('temp_registration_data');
        
        if (!$studentData) {
            return response()->json([
                'success' => false,
                'message' => 'Registration data not found. Please try again.'
            ]);
        }
        
        // Gumawa ng bagong request para sa StudentAuthController
        $registerRequest = new Request($studentData);
        
        // Tawagin ang StudentAuthController para mag-register
        $studentAuthController = new StudentAuthController();
        $result = $studentAuthController->register($registerRequest);
        
        // I-clear ang session
        session()->forget(['reg_otp', 'reg_email', 'reg_student_id', 'reg_otp_expires', 'temp_registration_data']);
        
        return $result;
    }
}