<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use App\Models\ApprovalLog;
use App\Models\DepartmentRequirement;
use App\Models\VerifiedExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClearanceStatusMail;

class DepartmentStaffController extends Controller
{
    // ============ GET STAFF DEPARTMENT FROM SESSION ============
    private function getStaffDepartment()
    {
        return Department::find(session('staff_department_id'));
    }

    // ============ STAFF LOGIN & AUTHENTICATION ============

    public function showLoginForm()
    {
        if (session('staff_logged_in')) {
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
        session([
            'staff_logged_in' => true,
            'staff_id' => $staff->id,
            'staff_name' => $staff->name,
            'staff_email' => $staff->staff_email,
            'staff_department_id' => $staff->id,
        ]);
        
        // ✅ IDAGDAG ITO - I-save ang session bago mag-return ng JSON
        session()->save();  // <--- IDAGDAG ITO!
        
        // ✅ Kung AJAX request, mag-return ng JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect' => route('staff.dashboard')
            ]);
        }
        
        return redirect()->route('staff.dashboard');
    }

    // ✅ Kung AJAX request, mag-return ng JSON error
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

    // ============ DASHBOARD ============

    public function dashboard()
    {
        if (!session('staff_logged_in')) {
            return redirect()->route('staff.login');
        }
        
        $department = Department::find(session('staff_department_id'));
        
        if (!$department) {
            return redirect()->route('staff.login')->with('error', 'Department not found.');
        }
        
        $allRequests = ClearanceRequest::where('department_id', $department->id)
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->get();
        
        $filteredRequests = $allRequests->filter(function($request) use ($department) {
            $student = $request->student;
            if (!$student) return false;
            return $department->isRequiredForYear($student->year_level);
        });
        
        $pendingRequests = $filteredRequests->where('status', 'pending');
        $approvedRequests = $filteredRequests->where('status', 'approved');
        $rejectedRequests = $filteredRequests->where('status', 'rejected');
        
        $pendingCount = $pendingRequests->count();
        $approvedCount = $approvedRequests->count();
        $rejectedCount = $rejectedRequests->count();
        $totalStudents = User::where('role', 'student')->count();
        
        $exports = VerifiedExport::where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $exportsCount = $exports->count();
        
        $verifiedStudents = collect();
        $verifiedCount = 0;
        $currentReportName = null;
        $currentImportId = null;
        
        $currentImport = \DB::table('verified_imports')
            ->where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('imported_at', 'desc')
            ->first();
        
        if ($currentImport) {
            $currentReportName = $currentImport->report_name;
            $currentImportId = $currentImport->id;
            $studentsData = json_decode($currentImport->students_data, true);
            if ($studentsData) {
                $verifiedStudents = collect($studentsData);
                $verifiedCount = $verifiedStudents->count();
            }
        }
        
        return view('staff.dashboard', compact(
            'department', 
            'pendingRequests', 
            'approvedRequests', 
            'rejectedRequests',
            'pendingCount', 
            'approvedCount', 
            'rejectedCount', 
            'totalStudents',
            'exports', 
            'exportsCount',
            'verifiedStudents',
            'verifiedCount',
            'currentReportName',
            'currentImportId'
        ));
    }
    
    // ============ DIRECT UPLOAD CSV TO VERIFIED LIST ============
    
    public function uploadCSVToVerified(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);
        
        $department = $this->getStaffDepartment();
        
        if (!$department) {
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        
        $studentsData = [];
        $count = 0;
        $isHeader = true;
        
        while (($data = fgetcsv($handle)) !== false) {
            if ($isHeader && (strtolower($data[0]) == 'student_id' || $data[0] == 'Student ID' || strtolower($data[0]) == 'id')) {
                $isHeader = false;
                continue;
            }
            $isHeader = false;
            
            if (!empty($data[0])) {
                $studentsData[] = [
                    'student_id' => trim($data[0]),
                    'student_name' => trim($data[1] ?? $data[0]),
                    'course' => $data[2] ?? 'N/A',
                    'year_level' => $data[3] ?? 'N/A',
                ];
                $count++;
            }
        }
        fclose($handle);
        
        if ($count == 0) {
            return redirect()->back()->with('error', 'No valid data found in CSV file.');
        }
        
        $importId = \DB::table('verified_imports')->insertGetId([
            'department_id' => $department->id,
            'export_id' => 0,
            'report_name' => 'CSV Upload - ' . date('Y-m-d H:i:s'),
            'students_data' => json_encode($studentsData),
            'total_records' => $count,
            'imported_by' => session('staff_id'),
            'imported_at' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \DB::table('verified_imports')
            ->where('department_id', $department->id)
            ->where('id', '!=', $importId)
            ->update(['status' => 'archived']);
        
        return redirect()->route('staff.dashboard')->with('success', $count . ' students loaded to verified list!');
    }
    
    // ============ IMPORT REPORT TO VERIFIED LIST ============
    
    public function importReportToVerified(Request $request)
    {
        try {
            $request->validate([
                'export_id' => 'required|exists:verified_exports,id',
            ]);
            
            $department = $this->getStaffDepartment();
            
            if (!$department) {
                return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
            }
            
            $export = VerifiedExport::where('id', $request->export_id)
                ->where('department_id', $department->id)
                ->firstOrFail();
            
            $lines = explode("\n", $export->csv_data);
            array_shift($lines);
            
            $studentsData = [];
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                $data = str_getcsv($line);
                if (count($data) >= 2) {
                    $studentsData[] = [
                        'student_id' => trim($data[0]),
                        'student_name' => trim($data[1]),
                        'course' => $data[2] ?? 'N/A',
                        'year_level' => $data[3] ?? 'N/A',
                    ];
                }
            }
            
            $importId = \DB::table('verified_imports')->insertGetId([
                'department_id' => $department->id,
                'export_id' => $export->id,
                'report_name' => $export->event_name,
                'students_data' => json_encode($studentsData),
                'total_records' => count($studentsData),
                'imported_by' => session('staff_id'),
                'imported_at' => now(),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            \DB::table('verified_imports')
                ->where('department_id', $department->id)
                ->where('id', '!=', $importId)
                ->update(['status' => 'archived']);
            
            return response()->json([
                'success' => true,
                'message' => count($studentsData) . ' students imported to verified list!'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // ============ APPROVE CLEARANCE ============
    
    public function approve($id)
    {
        $department = $this->getStaffDepartment();
        
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
            'staff_email' => session('staff_email'),
        ]);
        
        $this->checkFullClearance($clearanceRequest->student_id);
        
        return redirect()->route('staff.dashboard')->with('success', 'Clearance approved successfully');
    }
    
    // ============ REJECT CLEARANCE ============
    
    public function reject(Request $request, $id)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);
        
        $department = $this->getStaffDepartment();
        
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
            'staff_email' => session('staff_email'),
        ]);
        
        return redirect()->route('staff.dashboard')->with('success', 'Clearance rejected');
    }
    
    // ============ REQUIREMENT MANAGEMENT ============
    
    public function storeRequirement(Request $request)
    {
        $request->validate([
            'requirement_name' => 'required|string|max:255',
        ]);

        $department = $this->getStaffDepartment();

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
        $department = $this->getStaffDepartment();

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
        $department = $this->getStaffDepartment();

        if (!$department || $department->id != $requirement->department_id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $requirement->delete();

        return redirect()->route('staff.dashboard')->with('success', 'Requirement deleted successfully!');
    }
    
    // ============ YEAR REQUIREMENTS MANAGEMENT ============
    
    public function storeYearRequirement(Request $request)
    {
        $request->validate([
            'year_level' => 'required|in:1st Year,2nd Year,3rd Year,4th Year',
            'requirement_name' => 'required|string|max:255',
        ]);

        $department = $this->getStaffDepartment();

        if (!$department) {
            return redirect()->back()->with('error', 'Department not found.');
        }

        $lastOrder = \App\Models\DepartmentYearRequirement::where('department_id', $department->id)
            ->where('year_level', $request->year_level)
            ->max('sort_order');

        \App\Models\DepartmentYearRequirement::create([
            'department_id' => $department->id,
            'year_level' => $request->year_level,
            'requirement_name' => $request->requirement_name,
            'is_required' => $request->has('is_required'),
            'sort_order' => $lastOrder + 1,
            'is_active' => true,
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Requirement added for ' . $request->year_level . '!');
    }
    
    public function updateYearRequirement(Request $request, $id)
    {
        $request->validate([
            'requirement_name' => 'required|string|max:255',
        ]);

        $requirement = \App\Models\DepartmentYearRequirement::findOrFail($id);
        $department = $this->getStaffDepartment();

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
    
    public function destroyYearRequirement($id)
    {
        $requirement = \App\Models\DepartmentYearRequirement::findOrFail($id);
        $department = $this->getStaffDepartment();

        if (!$department || $department->id != $requirement->department_id) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $requirement->delete();

        return redirect()->route('staff.dashboard')->with('success', 'Requirement deleted successfully!');
    }
    
    public function getYearRequirements($yearLevel = null)
    {
        $department = $this->getStaffDepartment();
        
        if (!$department) {
            return response()->json(['error' => 'Department not found'], 404);
        }
        
        if ($yearLevel) {
            $requirements = \App\Models\DepartmentYearRequirement::where('department_id', $department->id)
                ->where('year_level', $yearLevel)
                ->orderBy('sort_order')
                ->get();
        } else {
            $requirements = \App\Models\DepartmentYearRequirement::where('department_id', $department->id)
                ->orderBy('year_level')
                ->orderBy('sort_order')
                ->get();
        }
        
        return response()->json([
            'success' => true,
            'requirements' => $requirements
        ]);
    }
    
    // ============ CHECK FULL CLEARANCE ============
    
    private function checkFullClearance($studentId)
    {
        $student = User::where('id', $studentId)->where('role', 'student')->first();
        
        if (!$student) {
            return;
        }
        
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
        
        if ($isFullyCleared) {
            $student->update([
                'is_cleared' => true,
                'cleared_at' => now(),
            ]);
        } else {
            $student->update([
                'is_cleared' => false,
            ]);
        }
    }
}