<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use App\Models\ApprovalLog;
use App\Models\VerifiedStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepartmentStaffController extends Controller
{
    public function showLoginForm()
    {
        if (session('staff_logged_in') && Auth::check()) {
            return redirect()->route('staff.dashboard');
        }
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $staff = Department::where('staff_email', $request->email)->first();

        if ($staff && Hash::check($request->password, $staff->staff_password)) {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $user = new User();
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->role = 'staff';
                $user->department_id = $staff->id;
                $user->is_active = 1;
                $user->name = $staff->name ?? $staff->staff_email;
                $user->save();
                Log::info('New staff user created', ['user_id' => $user->id]);
            } else {
                $user->department_id = $staff->id;
                $user->role = 'staff';
                $user->is_active = 1;
                $user->name = $staff->name ?? $user->name;
                $user->save();
                Log::info('Existing staff user updated', ['user_id' => $user->id]);
            }

            $verifyUser = User::find($user->id);
            if (!$verifyUser) {
                Log::error('User not found after creation', ['user_id' => $user->id]);
                return back()->withErrors(['email' => 'System error. Please try again.']);
            }

            session([
                'staff_logged_in' => true,
                'staff_id' => $staff->id,
                'staff_name' => $staff->name,
                'staff_email' => $staff->staff_email,
                'staff_department_id' => $staff->id,
            ]);
            session()->save();

            Auth::login($verifyUser);
            $request->session()->regenerate();

            Log::info('Staff login successful', [
                'email' => $staff->staff_email,
                'user_id' => $verifyUser->id,
                'session_id' => session()->getId(),
            ]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect' => route('staff.dashboard')
                ]);
            }

            return redirect()->intended(route('staff.dashboard'));
        }

        // INVALID CREDENTIALS
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // ============ STAFF DASHBOARD ============
    public function dashboard()
    {
        $user = Auth::user();
        
        // ✅ Get department_id from user or session
        $departmentId = $user->department_id ?? session('staff_department_id');
        
        if (!$departmentId) {
            Log::error('Staff dashboard: No department ID found', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
            return redirect('/')->with('error', 'Your account is not linked to any department.');
        }

        // Get clearance requests for this department
        $requests = ClearanceRequest::where('department_id', $departmentId)
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->get();

        // Count by status
        $pendingCount = $requests->where('status', 'pending')->count();
        $approvedCount = $requests->where('status', 'approved')->count();
        $rejectedCount = $requests->where('status', 'rejected')->count();

        // Filter requests by status for tabs
        $pendingRequests = $requests->where('status', 'pending');
        $approvedRequests = $requests->where('status', 'approved');
        $rejectedRequests = $requests->where('status', 'rejected');

        // ✅ GET VERIFIED STUDENTS FROM verified_students TABLE
        $verifiedStudents = collect();
        try {
            $verifiedRecords = VerifiedStudent::where('department_id', $departmentId)
                ->where('is_active', true)
                ->with('student')
                ->orderBy('verified_at', 'desc')
                ->get();
            
            foreach ($verifiedRecords as $record) {
                $student = $record->student;
                $verifiedStudents->push((object) [
                    'student_id' => $record->student_id,
                    'student_name' => $record->student_name,
                    'first_name' => $student->first_name ?? '',
                    'last_name' => $student->last_name ?? '',
                    'course' => $student->course ?? 'N/A',
                    'year_level' => $student->year_level ?? 'N/A',
                    'email' => $student->email ?? '',
                    'verified_at' => $record->verified_at,
                    'verified_by_role' => $record->verified_by_role ?? 'Officer',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error getting verified students: ' . $e->getMessage());
        }

        // ✅ GET REPORTS
        $allReports = collect();
        $pendingReports = collect();
        $approvedReports = collect();
        $rejectedReports = collect();
        
        try {
            $allReports = DB::table('sent_reports')
                ->where('department_id', $departmentId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $pendingReports = $allReports->where('status', 'pending');
            $approvedReports = $allReports->where('status', 'approved');
            $rejectedReports = $allReports->where('status', 'rejected');
            
            Log::info('Staff dashboard reports loaded', [
                'department_id' => $departmentId,
                'total' => $allReports->count(),
                'pending' => $pendingReports->count(),
                'approved' => $approvedReports->count(),
                'rejected' => $rejectedReports->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting reports: ' . $e->getMessage());
        }

        return view('staff.dashboard', compact(
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingRequests',
            'approvedRequests',
            'rejectedRequests',
            'verifiedStudents',
            'allReports',
            'pendingReports',
            'approvedReports',
            'rejectedReports'
        ));
    }

    public function approve($id)
    {
        $department = Department::find(session('staff_department_id'));
        
        if (!$department) {
            return redirect()->route('staff.dashboard')->with('error', 'Department not found');
        }

        $clearanceRequest = ClearanceRequest::where('id', $id)
            ->where('department_id', $department->id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
            $clearanceRequest->update([
                'status' => 'approved',
                'processed_at' => now(),
            ]);

            ApprovalLog::create([
                'clearance_request_id' => $clearanceRequest->id,
                'student_id' => $clearanceRequest->student_id,
                'department_id' => $department->id,
                'action' => 'approved',
                'staff_email' => session('staff_email'),
            ]);

            $this->checkFullClearance($clearanceRequest->student_id);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approve error: ' . $e->getMessage());
            return redirect()->route('staff.dashboard')->with('error', 'Failed to approve: ' . $e->getMessage());
        }

        return redirect()->route('staff.dashboard')->with('success', 'Clearance approved successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $department = Department::find(session('staff_department_id'));
        
        if (!$department) {
            return redirect()->route('staff.dashboard')->with('error', 'Department not found');
        }

        $clearanceRequest = ClearanceRequest::where('id', $id)
            ->where('department_id', $department->id)
            ->firstOrFail();

        DB::beginTransaction();
        try {
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
                'staff_email' => session('staff_email'),
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reject error: ' . $e->getMessage());
            return redirect()->route('staff.dashboard')->with('error', 'Failed to reject: ' . $e->getMessage());
        }

        return redirect()->route('staff.dashboard')->with('success', 'Clearance rejected');
    }

    public function logout(Request $request)
    {
        session()->forget(['staff_logged_in', 'staff_id', 'staff_name', 'staff_email', 'staff_department_id']);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    private function checkFullClearance($studentId)
    {
        $student = User::where('id', $studentId)->where('role', 'student')->first();
        if (!$student) return;

        $allDepartments = Department::where('is_active', true)->get();
        $requiredDepartments = $allDepartments->filter(function($dept) use ($student) {
            return $dept->isRequiredForYear($student->year_level);
        });

        $requiredDeptIds = $requiredDepartments->pluck('id')->toArray();
        $allRequests = ClearanceRequest::where('student_id', $studentId)
            ->whereIn('department_id', $requiredDeptIds)
            ->get();

        $allApproved = $allRequests->every(function ($request) {
            return $request->status === 'approved';
        });

        $isFullyCleared = $allApproved && $allRequests->count() === count($requiredDeptIds);

        $student->update(['is_cleared' => $isFullyCleared]);
        if ($isFullyCleared) {
            $student->update(['cleared_at' => now()]);
        }
    }
    
    // ============ STAFF REPORTS ============
    public function reports()
    {
        $user = Auth::user();
        $departmentId = $user->department_id ?? session('staff_department_id');
        
        if (!$departmentId) {
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        $allReports = DB::table('sent_reports')
            ->where('department_id', $departmentId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $pendingReports = $allReports->where('status', 'pending');
        $approvedReports = $allReports->where('status', 'approved');
        $rejectedReports = $allReports->where('status', 'rejected');
        
        return view('staff.dashboard', compact(
            'allReports',
            'pendingReports',
            'approvedReports',
            'rejectedReports'
        ));
    }

    public function viewReport($id)
    {
        $user = Auth::user();
        $departmentId = $user->department_id ?? session('staff_department_id');
        
        $report = DB::table('sent_reports')
            ->where('id', $id)
            ->where('department_id', $departmentId)
            ->first();
        
        if (!$report) {
            return redirect()->route('staff.reports')->with('error', 'Report not found.');
        }
        
        return view('staff.report-view', compact('report'));
    }

    // ============ APPROVE REPORT ============
    public function approveReport($id)
    {
        try {
            $user = Auth::user();
            $departmentId = $user->department_id ?? session('staff_department_id');
            
            $report = DB::table('sent_reports')
                ->where('id', $id)
                ->where('department_id', $departmentId)
                ->first();
            
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }
            
            DB::table('sent_reports')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Report approved successfully! You can now extract the CSV.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Approve report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error approving report: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ REJECT REPORT ============
    public function rejectReport($id)
    {
        try {
            $user = Auth::user();
            $departmentId = $user->department_id ?? session('staff_department_id');
            
            $report = DB::table('sent_reports')
                ->where('id', $id)
                ->where('department_id', $departmentId)
                ->first();
            
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }
            
            DB::table('sent_reports')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now()
                ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Report rejected successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Reject report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting report: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ DELETE REPORT ============
    public function deleteReport($id)
    {
        try {
            $user = Auth::user();
            $departmentId = $user->department_id ?? session('staff_department_id');
            
            $report = DB::table('sent_reports')
                ->where('id', $id)
                ->where('department_id', $departmentId)
                ->first();
            
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }
            
            // Delete attachment if exists
            if ($report->attachment_path) {
                $path = storage_path('app/public/' . $report->attachment_path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
            
            DB::table('sent_reports')
                ->where('id', $id)
                ->where('department_id', $departmentId)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Report deleted successfully.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Delete report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ EXPORT REPORT AS CSV ============
    public function exportReport($id)
    {
        try {
            $user = Auth::user();
            $departmentId = $user->department_id ?? session('staff_department_id');
            
            $report = DB::table('sent_reports')
                ->where('id', $id)
                ->where('department_id', $departmentId)
                ->first();
            
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }
            
            // Get CSV data from report
            $csvContent = $report->csv_data;
            
            // Generate filename
            $filename = 'report_' . $report->id . '_' . date('Y-m-d') . '.csv';
            
            return response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Export report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting report: ' . $e->getMessage()
            ], 500);
        }
    }
}