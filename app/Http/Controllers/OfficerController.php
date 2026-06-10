<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\VerifiedStudent;
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
        
        // Verified students (limited view - walang email, walang student ID sa display)
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)
            ->get(['id', 'student_id', 'student_name', 'verified_at', 'verified_by_role']);
        
        // Get additional data for officer (course, year) - from users table
        $studentIds = $verifiedStudents->pluck('student_id')->toArray();
        
        $studentsData = User::whereIn('student_id', $studentIds)
            ->where('role', 'student')
            ->get(['student_id', 'course', 'year_level']);
        
        // Merge data para sa officer view
        foreach ($verifiedStudents as $verified) {
            $studentInfo = $studentsData->firstWhere('student_id', $verified->student_id);
            $verified->course = $studentInfo->course ?? 'N/A';
            $verified->year_level = $studentInfo->year_level ?? 'N/A';
        }
        
        $verifiedCount = $verifiedStudents->count();
        
        return view('officer.dashboard', compact('department', 'verifiedStudents', 'verifiedCount'));
    }
    
    public function uploadVerifiedList(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
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
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip headers if present
        $firstLine = fgetcsv($handle);
        if (strtolower($firstLine[0]) === 'student_id' || strtolower($firstLine[0]) === 'id') {
            // Headers present, skip
        } else {
            // No headers, rewind and process first line as data
            rewind($handle);
        }
        
        $count = 0;
        $errors = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            $studentId = trim($data[0]);
            $studentName = trim($data[1] ?? $studentId);
            
            if (empty($studentId)) {
                continue;
            }
            
            // I-verify kung valid ang student ID sa users table
            $student = User::where('student_id', $studentId)
                ->where('role', 'student')
                ->first();
            
            if (!$student) {
                $errors[] = "Student ID {$studentId} not found in database.";
                continue;
            }
            
            VerifiedStudent::updateOrCreate(
                [
                    'department_id' => $department->id,
                    'student_id' => $studentId,
                ],
                [
                    'student_name' => $studentName,
                    'verified_by' => $user->id,
                    'verified_by_role' => 'officer',
                    'verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $count++;
        }
        
        fclose($handle);
        
        $message = "$count students added to verified list!";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }
        
        return redirect()->route('officer.dashboard')->with('success', $message);
    }
    
    public function addVerifiedStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:50',
            'student_name' => 'required|string|max:255',
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
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        // I-verify kung valid ang student ID
        $student = User::where('student_id', $request->student_id)
            ->where('role', 'student')
            ->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student ID not found in database.');
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
        
        return redirect()->route('officer.dashboard')->with('success', 'Student added to verified list!');
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
            return redirect()->back()->with('error', 'Unauthorized');
        }
        
        $verified->delete();
        
        return redirect()->route('officer.dashboard')->with('success', 'Student removed from verified list.');
    }
}