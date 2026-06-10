<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\ClearanceRequest;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $student = $request->user();
        
        $departments = Department::where('is_active', true)->get();
        $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
            ->get()
            ->keyBy('department_id');
        
        $totalDepartments = $departments->count();
        $approvedCount = $clearanceRequests->where('status', 'approved')->count();
        $pendingCount = $clearanceRequests->where('status', 'pending')->count();
        $rejectedCount = $clearanceRequests->where('status', 'rejected')->count();
        $notSubmittedCount = $totalDepartments - ($approvedCount + $pendingCount + $rejectedCount);
        
        $departmentsList = [];
        foreach($departments as $dept) {
            $request = $clearanceRequests->get($dept->id);
            $departmentsList[] = [
                'id' => $dept->id,
                'name' => $dept->name,
                'status' => $request ? $request->status : 'not_submitted',
                'submitted_at' => $request ? $request->submitted_at : null,
                'remarks' => $request ? $request->remarks : null,
            ];
        }
        
        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'account_id' => $student->account_id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'email' => $student->email,
                'course' => $student->course,
                'is_cleared' => $student->is_cleared,
            ],
            'progress' => [
                'total_departments' => $totalDepartments,
                'approved_count' => $approvedCount,
                'pending_count' => $pendingCount,
                'rejected_count' => $rejectedCount,
                'not_submitted_count' => $notSubmittedCount,
                'percentage' => $totalDepartments > 0 ? round(($approvedCount / $totalDepartments) * 100) : 0,
            ],
            'departments' => $departmentsList,
        ]);
    }
    
    public function departments()
    {
        $departments = Department::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'departments' => $departments->map(function($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name,
                    'description' => $dept->description,
                ];
            })
        ]);
    }
    
    public function clearanceStatus(Request $request)
    {
        $student = $request->user();
        
        $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
            ->with('department')
            ->get();
        
        return response()->json([
            'success' => true,
            'is_fully_cleared' => $student->is_cleared,
            'cleared_at' => $student->cleared_at,
            'requests' => $clearanceRequests->map(function($req) {
                return [
                    'department' => $req->department->name,
                    'status' => $req->status,
                    'submitted_at' => $req->submitted_at,
                    'processed_at' => $req->processed_at,
                    'remarks' => $req->remarks,
                ];
            })
        ]);
    }
}