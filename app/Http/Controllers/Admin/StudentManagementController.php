<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        return view('admin.students', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|unique:users,student_id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'course' => 'required|string',
            'year_level' => 'required|string',
            'password' => 'required|string|min:8',
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
            'year_level' => $request->year_level,
            'course_year' => $request->course . ' - ' . $request->year_level,
            'is_active' => true,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Student',
            'description' => 'Added new student: ' . $user->student_id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.students')->with('success', 'Student added successfully');
    }

    public function getData($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        return response()->json([
            'id' => $student->id,
            'student_id' => $student->student_id,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'course' => $student->course,
            'year_level' => $student->year_level,
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'course' => 'required|string',
            'year_level' => 'required|string',
        ]);
        
        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'course' => $request->course,
            'year_level' => $request->year_level,
            'course_year' => $request->course . ' - ' . $request->year_level,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $student->update(['password' => Hash::make($request->password)]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Student',
            'description' => 'Updated student: ' . $student->student_id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.students')->with('success', 'Student updated successfully');
    }

    public function destroy($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        $studentId = $student->student_id;
        $student->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Student',
            'description' => 'Deleted student: ' . $studentId,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.students')->with('success', 'Student deleted successfully');
    }

    public function toggleStatus($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        $student->update(['is_active' => !$student->is_active]);
        
        $status = $student->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Student',
            'description' => $status . ' student: ' . $student->student_id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.students')->with('success', "Student {$status} successfully");
    }
}