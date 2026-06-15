<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClearanceRequest;
use App\Models\Department;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentDashboardController extends Controller
{
    /**
     * Check if user is student, if not redirect to appropriate dashboard
     */
    private function checkStudentRole()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect('/');
        }
        
        if ($user->role !== 'student') {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'staff') {
                return redirect()->route('staff.dashboard');
            } elseif ($user->role === 'support') {
                return redirect()->route('support.dashboard');
            }
            return redirect('/');
        }
        
        return null;
    }
    
    /**
     * Convert year level string to number
     */
    private function getYearNumber($yearLevel)
    {
        return match($yearLevel) {
            '1st Year' => 1,
            '2nd Year' => 2,
            '3rd Year' => 3,
            '4th Year' => 4,
            default => 4,
        };
    }
    
    // DASHBOARD PAGE
    public function index()
    {
        $redirect = $this->checkStudentRole();
        if ($redirect) return $redirect;
        
        $student = Auth::user();
        
        // Ensure course_year is set
        if (!$student->course_year && $student->course && $student->year_level) {
            $student->course_year = $student->course . ' - ' . $student->year_level;
            $student->save();
        }
        
        // ✅ GAMITIN ANG BAGONG isRequiredForYear() METHOD
        $allDepartments = Department::where('is_active', true)->get();
        $departments = $allDepartments->filter(function($dept) use ($student) {
            return $dept->isRequiredForYear($student->year_level);
        });
        
        $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
            ->get()
            ->keyBy('department_id');
        
        $totalDepartments = $departments->count();
        $approvedCount = 0;
        
        foreach ($departments as $dept) {
            $request = $clearanceRequests->get($dept->id);
            if ($request && $request->status === 'approved') {
                $approvedCount++;
            }
        }
        
        $isFullyCleared = ($totalDepartments > 0 && $approvedCount == $totalDepartments);
        
        return view('student.dashboard', compact('student', 
            'isFullyCleared', 'approvedCount', 'totalDepartments'));
    }
    
    // CLEARANCE PAGE
    public function clearance()
    {
        $redirect = $this->checkStudentRole();
        if ($redirect) return $redirect;
        
        $student = Auth::user();
        
        // Ensure course_year is set
        if (!$student->course_year && $student->course && $student->year_level) {
            $student->course_year = $student->course . ' - ' . $student->year_level;
            $student->save();
        }
        
        // ✅ GAMITIN ANG BAGONG isRequiredForYear() METHOD
        $allDepartments = Department::where('is_active', true)->get();
        $departments = $allDepartments->filter(function($dept) use ($student) {
            return $dept->isRequiredForYear($student->year_level);
        });
        
        $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
            ->get()
            ->keyBy('department_id');
        
        $totalDepartments = $departments->count();
        $approvedCount = 0;
        $pendingCount = 0;
        $rejectedCount = 0;
        
        foreach ($departments as $dept) {
            $request = $clearanceRequests->get($dept->id);
            if ($request) {
                if ($request->status == 'approved') $approvedCount++;
                elseif ($request->status == 'pending') $pendingCount++;
                elseif ($request->status == 'rejected') $rejectedCount++;
            }
        }
        
        $notSubmittedCount = $totalDepartments - ($approvedCount + $pendingCount + $rejectedCount);
        $isFullyCleared = ($totalDepartments > 0 && $approvedCount == $totalDepartments);
        
        return view('student.clearance', compact('student', 'departments', 'clearanceRequests',
            'approvedCount', 'pendingCount', 'rejectedCount', 'notSubmittedCount', 'totalDepartments', 'isFullyCleared'));
    }
    
    // REMINDERS PAGE
    public function reminders()
    {
        $redirect = $this->checkStudentRole();
        if ($redirect) return $redirect;
        
        $student = Auth::user();
        
        $announcements = Announcement::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.reminders', compact('student', 'announcements'));
    }
    
    // PROFILE PAGE
    public function profile()
    {
        $redirect = $this->checkStudentRole();
        if ($redirect) return $redirect;
        
        $student = Auth::user();
        return view('student.profile', compact('student'));
    }
    
    // UPDATE PROFILE - WITH PASSWORD CHANGE
    public function updateProfile(Request $request)
    {   
        $redirect = $this->checkStudentRole();
        if ($redirect) return $redirect;
        
        $student = Auth::user();
        
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'course' => 'required|string',
            'year_level' => 'required|string',
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        // Update basic info
        $student->first_name = $request->first_name;
        $student->last_name = $request->last_name;
        $student->name = $request->first_name . ' ' . $request->last_name;
        $student->email = $request->email;
        $student->course = $request->course;
        $student->year_level = $request->year_level;
        $student->course_year = $request->course . ' - ' . $request->year_level;
        $student->save();
        
        // Update password if provided
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
            $student->save();
        }
        
        return redirect()->route('student.profile')->with('success', 'Profile updated successfully!');
    }
}