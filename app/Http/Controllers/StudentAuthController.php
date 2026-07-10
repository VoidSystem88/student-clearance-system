<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification;
use App\Mail\NewStudentMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('student.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if user has verified email
        $student = User::where('student_id', $request->student_id)
            ->where('role', 'student')
            ->first();

        if ($student && Hash::check($request->password, $student->password)) {
            // Check if email is verified
            if (!$student->hasVerifiedEmail()) {
                // Store student ID in session for verification
                session(['pending_verification_student_id' => $student->id]);
                
                // Resend OTP
                $student->sendEmailVerificationNotification();
                
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please verify your email first. A new verification code has been sent.',
                        'redirect' => route('verification.notice')
                    ]);
                }
                
                return back()->withErrors(['email' => 'Please verify your email before logging in.']);
            }
            
            if (!$student->is_active) {
                return back()->withErrors(['student_id' => 'Your account is deactivated. Please contact support.']);
            }
            
            Auth::login($student);
            $request->session()->regenerate();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('student.dashboard')
                ]);
            }
            
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['student_id' => 'Invalid credentials']);
    }

    public function showRegisterForm()
    {
        return view('student.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:users,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'birthdate' => 'required|date',
            'course' => 'required|string',
            'year_level' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // Age validation (at least 16 years old)
            $age = \Carbon\Carbon::parse($request->birthdate)->age;
            if ($age < 16) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You must be at least 16 years old to register!'
                    ]);
                }
                return back()->withErrors(['birthdate' => 'You must be at least 16 years old to register!']);
            }
            
            // Generate Account ID
            $lastStudent = User::where('role', 'student')->orderBy('id', 'desc')->first();
            $lastNumber = $lastStudent ? intval(substr($lastStudent->account_id, -5)) : 0;
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
            $accountId = 'CLR-' . date('Y') . '-' . $newNumber;

            // Generate unique verification token
            $verificationToken = bin2hex(random_bytes(32));

            // Create student - NOT VERIFIED YET
            $student = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'account_id' => $accountId,
                'student_id' => $request->student_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'birthdate' => $request->birthdate,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'course_year' => $request->course . ' - ' . $request->year_level,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => null, // NOT VERIFIED
                'verification_token' => $verificationToken,  // <-- IDAGDAG
                'verification_pending' => true,              // <-- IDAGDAG
            ]);

            // ============ SEND OTP FOR EMAIL VERIFICATION ============
            $student->sendEmailVerificationNotification();
            
            // Store student ID and token in session for OTP verification
            session([
                'pending_verification_student_id' => $student->id,
                'pending_verification_token' => $verificationToken
            ]);
            // ========================================================

            // Send welcome email (optional - separate from verification)
            try {
                Mail::to($student->email)->send(new NewStudentMail($student));
                Log::info('Welcome email sent to student: ' . $student->email);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: ' . $e->getMessage());
            }

            // Send notifications to admins
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'type' => 'new_student',
                    'title' => 'New Student Registered',
                    'message' => $student->first_name . ' ' . $student->last_name . ' (' . $student->student_id . ') has registered.',
                    'data' => json_encode([
                        'student_id' => $student->id,
                        'student_name' => $student->first_name . ' ' . $student->last_name,
                        'student_number' => $student->student_id,
                    ]),
                    'user_id' => $admin->id,
                    'is_read' => false,
                ]);
            }
            
            DB::commit();

            // ✅ SET SESSION FLAG NA BAGONG REGISTER ITO (para hindi mag-auto-login)
            session(['is_new_registration' => true]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Please verify your email.',
                    'is_new_registration' => true,  // ✅ IDAGDAG ITO
                    //'redirect' => route('verification.notice')
                ]);
            }
            
            return redirect()->route('verification.notice')->with('success', 'Registration successful! Please verify your email.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Registration failed: ' . $e->getMessage())->withInput();
        }
    }

    // ============ EMAIL VERIFICATION METHODS ============
    
    /**
     * Show email verification notice page
     */
    public function showVerificationNotice()
    {
        // Check if user is already verified
        $studentId = session('pending_verification_student_id');
        
        // Kung walang session, tignan sa database kung may pending verification
        if (!$studentId) {
            // Hanapin ang pending verification sa database (last 30 minutes)
            $pendingStudent = User::where('verification_pending', true)
                ->whereNull('email_verified_at')
                ->where('created_at', '>', now()->subMinutes(30))
                ->orderBy('id', 'desc')
                ->first();
            
            if ($pendingStudent) {
                session(['pending_verification_student_id' => $pendingStudent->id]);
                $studentId = $pendingStudent->id;
            }
        }
        
        if (!$studentId) {
            return redirect('/')->with('error', 'No pending verification found. Please register again.');
        }
        
        $student = User::find($studentId);
        
        if (!$student) {
            return redirect('/');
        }
        
        if ($student->hasVerifiedEmail()) {
            $student->update([
                'verification_pending' => false,
                'verification_token' => null
            ]);
            session()->forget('pending_verification_student_id');
            return redirect('/')->with('success', 'Your email is already verified. You can now login.');
        }
        
        return view('verify-email');
    }
    
    /**
     * Resume verification using token (para sa pag-refresh ng page)
     */
    public function resumeVerification($token)
    {
        $student = User::where('verification_token', $token)
            ->where('verification_pending', true)
            ->whereNull('email_verified_at')
            ->first();
        
        if (!$student) {
            return redirect('/')->with('error', 'Invalid or expired verification link.');
        }
        
        // I-restore ang session
        session([
            'pending_verification_student_id' => $student->id,
            'pending_verification_token' => $token
        ]);
        
        // Mag-resend ng OTP
        $student->sendEmailVerificationNotification();
        
        return redirect()->route('verification.notice')
            ->with('success', 'Please verify your email to complete registration.');
    }
    
    /**
     * Verify OTP code
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);
        
        $studentId = session('pending_verification_student_id');
        
        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'No pending registration found. Please register again.'
            ]);
        }
        
        $student = User::find($studentId);
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found. Please register again.'
            ]);
        }
        
        // Check if already verified
        if ($student->hasVerifiedEmail()) {
            $student->update([
                'verification_pending' => false,
                'verification_token' => null
            ]);
            session()->forget('pending_verification_student_id');
            return response()->json([
                'success' => true,
                'message' => 'Email already verified! You can now login.'
            ]);
        }
        
        if ($student->verifyOtp($request->otp)) {
            // I-clear ang pending status
            $student->update([
                'verification_pending' => false,
                'verification_token' => null
            ]);
            session()->forget('pending_verification_student_id');
            
            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! You can now login.'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP code. Please try again.'
        ]);
    }
    
    /**
     * Resend verification OTP
     */
    public function resendVerificationOtp(Request $request)
    {
        $studentId = session('pending_verification_student_id');
        
        if (!$studentId) {
            return response()->json([
                'success' => false,
                'message' => 'No pending registration found. Please register again.'
            ]);
        }
        
        $student = User::find($studentId);
        
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found. Please register again.'
            ]);
        }
        
        // Check if already verified
        if ($student->hasVerifiedEmail()) {
            $student->update([
                'verification_pending' => false,
                'verification_token' => null
            ]);
            session()->forget('pending_verification_student_id');
            return response()->json([
                'success' => true,
                'message' => 'Your email is already verified! You can now login.'
            ]);
        }
        
        // Send new OTP
        $student->sendEmailVerificationNotification();
        
        return response()->json([
            'success' => true,
            'message' => 'New verification code sent to your email.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}