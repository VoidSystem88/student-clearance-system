<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\VerifiedStudent;
use App\Models\VerifiedExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Hanapin ang department ng officer
        $department = null;
        
        if ($user->department_id) {
            $department = Department::find($user->department_id);
        }
        
        if (!$department) {
            $department = Department::where('name', $user->department_id)->first();
        }
        
        if (!$department) {
            return redirect('/')->with('error', 'Your account is not linked to any department.');
        }
        
        // ============ GET ALL STUDENTS (kagaya ng admin/support) ============
        $students = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // ============ GET VERIFIED STUDENTS para malaman kung sino ang verified na ============
        $verifiedStudentIds = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)
            ->pluck('student_id')
            ->toArray();
        
        // ============ OLD VERIFIED LIST (pang display lang) ============
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)
            ->get(['id', 'student_id', 'student_name', 'verified_at', 'verified_by_role']);
        
        // Get additional data for officer (course, year)
        $studentIds = $verifiedStudents->pluck('student_id')->toArray();
        
        $studentsData = User::whereIn('student_id', $studentIds)
            ->where('role', 'student')
            ->get(['student_id', 'course', 'year_level']);
        
        foreach ($verifiedStudents as $verified) {
            $studentInfo = $studentsData->firstWhere('student_id', $verified->student_id);
            $verified->course = $studentInfo->course ?? 'N/A';
            $verified->year_level = $studentInfo->year_level ?? 'N/A';
        }
        
        $verifiedCount = $verifiedStudents->count();
        
        return view('officer.dashboard', compact(
            'department', 
            'students',
            'verifiedStudentIds',
            'verifiedStudents', 
            'verifiedCount'
        ));
    }
    
    // ============ VERIFY STUDENT DIRECTLY FROM STUDENT LIST ============
    public function verifyStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:users,student_id',
            'student_name' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        $department = null;
        if ($user->department_id) {
            $department = Department::find($user->department_id);
        }
        if (!$department) {
            $department = Department::where('name', $user->department_id)->first();
        }
        
        if (!$department) {
            return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        }
        
        // I-verify kung valid ang student ID sa users table
        $student = User::where('student_id', $request->student_id)
            ->where('role', 'student')
            ->first();
        
        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student ID not found in database.'], 404);
        }
        
        VerifiedStudent::updateOrCreate(
            [
                'department_id' => $department->id,
                'student_id' => $request->student_id,
            ],
            [
                'student_name' => $request->student_name,
                'verified_by' => $user->id,
                'verified_by_role' => 'officer',
                'verified_at' => now(),
                'is_active' => true,
            ]
        );
        
        return response()->json([
            'success' => true, 
            'message' => 'Student verified successfully!',
            'student_id' => $request->student_id
        ]);
    }
    
    // ============ EXPORT VERIFIED STUDENTS TO CSV (SAVE TO DATABASE) ============
    public function exportVerifiedCSV(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Hanapin ang department
            $department = null;
            if ($user->department_id) {
                $department = Department::find($user->department_id);
            }
            if (!$department) {
                $department = Department::where('name', $user->department_id)->first();
            }
            
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
            }
            
            $eventName = $request->event_name ?? 'General_Verification';
            
            // Kunin ang verified students
            $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
                ->where('is_active', true)
                ->get();
            
            if ($verifiedStudents->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No verified students to export.'], 400);
            }
            
            // I-generate ang CSV content
            $csvContent = "student_id,student_name,course,year_level,email,date_verified,event_name\n";
            
            foreach ($verifiedStudents as $verified) {
    $student = User::where('student_id', $verified->student_id)->first();
    
    // Gamitin ang isset para sa PHP 7.4
    $course = isset($student->course) ? $student->course : 'N/A';
    $year_level = isset($student->year_level) ? $student->year_level : 'N/A';
    $email = isset($student->email) ? $student->email : 'N/A';
    
    $csvContent .= "\"{$verified->student_id}\",";
    $csvContent .= "\"{$verified->student_name}\",";
    $csvContent .= "\"{$course}\",";
    $csvContent .= "\"{$year_level}\",";
    $csvContent .= "\"{$email}\",";
    $csvContent .= "\"" . now() . "\",";
    $csvContent .= "\"{$eventName}\"\n";
}
            
            $filename = 'verified_students_' . date('Y-m-d_H-i-s') . '.csv';
            
            // SUBUKING I-INSERT SA DATABASE
            $result = \DB::table('verified_exports')->insert([
                'department_id' => $department->id,
                'generated_by' => $user->id,
                'filename' => $filename,
                'csv_data' => $csvContent,
                'event_name' => $eventName,
                'total_records' => $verifiedStudents->count(),
                'export_date' => date('Y-m-d'),
                'status' => 'active',
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'CSV report generated and saved successfully! ' . $verifiedStudents->count() . ' records exported.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to insert into database.'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
    
    public function uploadVerifiedList(Request $request)
    {
        // ... existing code ...
    }
    
    public function addVerifiedStudent(Request $request)
    {
        // ... existing code ...
    }
    
    public function removeVerifiedStudent($id)
    {
        $verified = VerifiedStudent::findOrFail($id);
        $user = Auth::user();

        $department = null;
        if ($user->department_id) {
            $department = Department::find($user->department_id);
        }
        if (!$department) {
            $department = Department::where('name', $user->department_id)->first();
        }

        if (!$department || $department->id != $verified->department_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $verified->delete();

        return response()->json(['success' => true, 'message' => 'Student removed from verified list.']);
    }
}