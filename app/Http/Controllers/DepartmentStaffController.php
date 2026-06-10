<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use App\Models\ApprovalLog;
use App\Models\DepartmentRequirement;
use App\Models\VerifiedStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClearanceStatusMail;

class DepartmentStaffController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $department = null;
        if ($user->department_id) {
            $department = Department::find($user->department_id);
        }
        if (!$department && $user->email) {
            $department = Department::where('staff_email', $user->email)->first();
        }
        
        if (!$department) {
            return redirect('/')->with('error', 'Your account is not linked to any department. Please contact admin.');
        }
        
        $allRequests = ClearanceRequest::where('department_id', $department->id)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        $pendingRequests = $allRequests->where('status', 'pending');
        $approvedRequests = $allRequests->where('status', 'approved');
        $rejectedRequests = $allRequests->where('status', 'rejected');
        
        $pendingCount = $pendingRequests->count();
        $approvedCount = $approvedRequests->count();
        $rejectedCount = $rejectedRequests->count();
        $totalStudents = User::where('role', 'student')->count();
        
        // Verified students
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->with('verifier')
            ->get();
        $verifiedCount = $verifiedStudents->count();
        
        return view('staff.dashboard', compact(
            'department', 'pendingRequests', 'approvedRequests', 'rejectedRequests',
            'pendingCount', 'approvedCount', 'rejectedCount', 'totalStudents',
            'verifiedStudents', 'verifiedCount'
        ));
    }
    
    public function approve($id)
    {
        $user = Auth::user();
        
        $department = null;
        if ($user->department_id) {
            $department = Department::where('id', $user->department_id)->first();
            if (!$department) {
                $department = Department::where('name', $user->department_id)->first();
            }
        }
        if (!$department) {
            $department = Department::where('staff_email', $user->email)->first();
        }
        
        if (!$department) {
            return redirect()->route('staff.dashboard')->with('error', 'Department not found');
        }
        
        $clearanceRequest = ClearanceRequest::where('id', $id)
            ->where('department_id', $department->id)
            ->firstOrFail();
        
        $clearanceRequest->update([
            'status' => 'approved',
            'processed_at' => now(),
        ]);
        
        ApprovalLog::create([
            'clearance_request_id' => $clearanceRequest->id,
            'student_id' => $clearanceRequest->student_id,
            'department_id' => $department->id,
            'action' => 'approved',
            'staff_email' => $user->email,
        ]);
        
        try {
            Mail::to($clearanceRequest->student->email)->send(new ClearanceStatusMail(
                $clearanceRequest->student,
                $department->name,
                'approved',
                null
            ));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
        
        $this->checkFullClearance($clearanceRequest->student_id);
        
        return redirect()->route('staff.dashboard')->with('success', 'Clearance approved successfully');
    }
    
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);
        
        $user = Auth::user();
        
        $department = null;
        if ($user->department_id) {
            $department = Department::where('id', $user->department_id)->first();
            if (!$department) {
                $department = Department::where('name', $user->department_id)->first();
            }
        }
        if (!$department) {
            $department = Department::where('staff_email', $user->email)->first();
        }
        
        if (!$department) {
            return redirect()->route('staff.dashboard')->with('error', 'Department not found');
        }
        
        $clearanceRequest = ClearanceRequest::where('id', $id)
            ->where('department_id', $department->id)
            ->firstOrFail();
        
        $clearanceRequest->update([
            'status' => 'rejected',
            'remarks' => $request->remarks,
            'processed_at' => now(),
        ]);
        
        ApprovalLog::create([
            'clearance_request_id' => $clearanceRequest->id,
            'student_id' => $clearanceRequest->student_id,
            'department_id' => $department->id,
            'action' => 'rejected',
            'remarks' => $request->remarks,
            'staff_email' => $user->email,
        ]);
        
        try {
            Mail::to($clearanceRequest->student->email)->send(new ClearanceStatusMail(
                $clearanceRequest->student,
                $department->name,
                'rejected',
                $request->remarks
            ));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
        
        return redirect()->route('staff.dashboard')->with('success', 'Clearance rejected');
    }
    
    public function storeRequirement(Request $request)
    {
        $request->validate([
            'requirement_name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $department = $this->getStaffDepartment($user);

        if (!$department) {
            return redirect()->back()->with('error', 'Department not found.');
        }

        $lastOrder = DepartmentRequirement::where('department_id', $department->id)->max('sort_order');

        DepartmentRequirement::create([
            'department_id' => $department->id,
            'requirement_name' => $request->requirement_name,
            'is_required' => $request->has('is_required'),
            'sort_order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Requirement added successfully!');
    }

    public function updateRequirement(Request $request, $id)
    {
        $request->validate([
            'requirement_name' => 'required|string|max:255',
        ]);

        $requirement = DepartmentRequirement::findOrFail($id);
        $user = Auth::user();
        $department = $this->getStaffDepartment($user);

        if (!$department || $department->id != $requirement->department_id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $requirement->update([
            'requirement_name' => $request->requirement_name,
            'is_required' => $request->has('is_required'),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Requirement updated successfully!');
    }

    public function destroyRequirement($id)
    {
        $requirement = DepartmentRequirement::findOrFail($id);
        $user = Auth::user();
        $department = $this->getStaffDepartment($user);

        if (!$department || $department->id != $requirement->department_id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $requirement->delete();

        return redirect()->route('staff.dashboard')->with('success', 'Requirement deleted successfully!');
    }

    // ============ VERIFIED STUDENTS METHODS ============
    
    public function uploadVerifiedList(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);
        
        $user = Auth::user();
        $department = $this->getStaffDepartment($user);
        
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
        while (($data = fgetcsv($handle)) !== false) {
            $studentId = trim($data[0]);
            $studentName = trim($data[1] ?? $studentId);
            
            if (!empty($studentId) && !empty($studentName)) {
                VerifiedStudent::updateOrCreate(
                    [
                        'department_id' => $department->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'student_name' => $studentName,
                        'verified_by' => $user->id,
                        'verified_at' => now(),
                        'is_active' => true,
                    ]
                );
                $count++;
            }
        }
        
        fclose($handle);
        
        return redirect()->route('staff.dashboard')->with('success', "$count students added to verified list!");
    }

    public function addVerifiedStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|max:50',
            'student_name' => 'required|string|max:255',
        ]);
        
        $user = Auth::user();
        $department = $this->getStaffDepartment($user);
        
        if (!$department) {
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        VerifiedStudent::updateOrCreate(
            [
                'department_id' => $department->id,
                'student_id' => $request->student_id,
            ],
            [
                'student_name' => $request->student_name,
                'verified_by' => $user->id,
                'verified_at' => now(),
                'is_active' => true,
            ]
        );
        
        return redirect()->route('staff.dashboard')->with('success', 'Student added to verified list!');
    }

    public function removeVerifiedStudent($id)
    {
        $verified = VerifiedStudent::findOrFail($id);
        $user = Auth::user();
        $department = $this->getStaffDepartment($user);
        
        if (!$department || $department->id != $verified->department_id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }
        
        $verified->delete();
        
        return redirect()->route('staff.dashboard')->with('success', 'Student removed from verified list.');
    }

    // ============ HELPER METHODS ============
    
    private function getStaffDepartment($user)
    {
        $department = null;
        
        if ($user->department_id) {
            $department = Department::where('id', $user->department_id)->first();
            if (!$department) {
                $department = Department::where('name', $user->department_id)->first();
            }
        }
        
        if (!$department) {
            $department = Department::where('staff_email', $user->email)->first();
        }
        
        return $department;
    }
    
    private function checkFullClearance($studentId)
    {
        $student = User::where('id', $studentId)->where('role', 'student')->first();
        $allRequests = ClearanceRequest::where('student_id', $studentId)->get();
        
        $allApproved = $allRequests->every(function ($request) {
            return $request->status === 'approved';
        });
        
        if ($allApproved && $allRequests->count() > 0 && $student) {
            $student->update([
                'is_cleared' => true,
                'cleared_at' => now(),
            ]);
        }
    }
}