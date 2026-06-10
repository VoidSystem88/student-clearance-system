<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use App\Models\ApprovalLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\ClearanceStatusMail;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics and charts
     */
    public function dashboard()
    {
        
        // 2FA Check - redirect if 2FA is enabled but not yet verified this session
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'super_admin']) && $user->admin_2fa_enabled && !session('2fa_verified')) {
            return redirect()->route('admin.2fa.verify');
        }
        
        $totalStudents = User::where('role', 'student')->count();
        $totalStaff = User::where('role', 'staff')->count();
        $totalDepartments = Department::count();
        $totalRequests = ClearanceRequest::count();
        $clearedStudents = User::where('role', 'student')->where('is_cleared', true)->count();
        
        $pendingRequests = ClearanceRequest::where('status', 'pending')->count();
        $approvedRequests = ClearanceRequest::where('status', 'approved')->count();
        $rejectedRequests = ClearanceRequest::where('status', 'rejected')->count();
        
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        $staffs = User::where('role', 'staff')->orderBy('created_at', 'desc')->get();
        $departments = Department::all();
        $clearanceRequests = ClearanceRequest::with('student', 'department')->orderBy('created_at', 'desc')->get();
        
        // Chart Data: Requests per department
        $departmentsList = Department::pluck('name');
        $pendingPerDept = [];
        $approvedPerDept = [];
        $rejectedPerDept = [];
        
        foreach(Department::all() as $dept) {
            $pendingPerDept[] = ClearanceRequest::where('department_id', $dept->id)->where('status', 'pending')->count();
            $approvedPerDept[] = ClearanceRequest::where('department_id', $dept->id)->where('status', 'approved')->count();
            $rejectedPerDept[] = ClearanceRequest::where('department_id', $dept->id)->where('status', 'rejected')->count();
        }
        
        // Chart Data: Monthly submissions
        $months = [];
        $monthlySubmissions = [];
        for($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('M Y');
            $monthlySubmissions[] = ClearanceRequest::whereYear('created_at', now()->subMonths($i)->year)
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->count();
        }
        
        // Chart Data: Clearance status distribution
        $statusData = [
            'pending' => $pendingRequests,
            'approved' => $approvedRequests,
            'rejected' => $rejectedRequests,
        ];
        
        return view('admin.dashboard', compact(
            'totalStudents', 'totalStaff', 'totalDepartments', 'totalRequests', 'clearedStudents',
            'pendingRequests', 'approvedRequests', 'rejectedRequests',
            'students', 'staffs', 'departments', 'clearanceRequests',
            'departmentsList', 'pendingPerDept', 'approvedPerDept', 'rejectedPerDept',
            'months', 'monthlySubmissions', 'statusData'
        ));
    }
    
    /**
     * Display admin login form
     */
    public function showLoginForm()
    {
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'super_admin'])) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }
    
    
    public function getStudentData($id)
{
    $student = User::where('role', 'student')->findOrFail($id);
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
    /**
     * Process admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            if (!in_array($user->role, ['admin', 'super_admin'])) {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Admin accounts only.']);
            }

            $request->session()->regenerate();

            // Check if 2FA is enabled
            if ($user->admin_2fa_enabled) {
                session(['2fa_user_id' => $user->id]);
                return redirect()->route('admin.2fa.verify');
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput($request->only('email'));
    }
    
    /**
     * Admin logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
    
    // ==================== MANAGE STUDENTS ====================
    
    public function students()
    {
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        return view('admin.students', compact('students'));
    }
    
    public function storeStudent(Request $request)
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
    
    public function editStudent($id)
    {
        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        return response()->json($student);
    }
    
    public function updateStudent(Request $request, $id)
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
    
    public function destroyStudent($id)
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
    
    // ==================== MANAGE STAFF ====================
    
    public function staffs()
    {
        $staffs = User::where('role', 'staff')->orderBy('created_at', 'desc')->get();
        $departments = Department::all();
        return view('admin.staffs', compact('staffs', 'departments'));
    }
    
    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'department_id' => 'nullable|exists:departments,id',
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'department_id' => $request->department_id,
            'is_active' => true,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Staff',
            'description' => 'Added new staff: ' . $user->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff added successfully');
    }
    
    public function editStaff($id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        return response()->json($staff);
    }
    
    public function updateStaff(Request $request, $id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'department_id' => 'nullable|exists:departments,id',
        ]);
        
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $staff->update(['password' => Hash::make($request->password)]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Staff',
            'description' => 'Updated staff: ' . $staff->email,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff updated successfully');
    }
    
    public function destroyStaff($id)
    {
        $staff = User::where('id', $id)->where('role', 'staff')->firstOrFail();
        $staffEmail = $staff->email;
        $staff->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Staff',
            'description' => 'Deleted staff: ' . $staffEmail,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.staffs')->with('success', 'Staff deleted successfully');
    }
    
    // ==================== MANAGE DEPARTMENTS ====================
    
    public function departments()
    {
    $departments = Department::orderBy('name', 'asc')->paginate(10);
    return view('admin.departments', compact('departments'));
}
    
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:departments',
            'description' => 'nullable|string',
            'staff_email' => 'required|email|unique:departments',
            'password' => 'required|string|min:8',
        ]);
        
        $department = Department::create([
            'name' => $request->name,
            'description' => $request->description,
            'staff_email' => $request->staff_email,
            'staff_password' => Hash::make($request->password),
            'is_active' => true,
        ]);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'created',
            'module' => 'Department',
            'description' => 'Added new department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department added successfully');
    }
    
    public function editDepartment($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }

    public function getDepartmentData($id)
    {
        $department = Department::findOrFail($id);
        return response()->json($department);
    }
    
    public function updateDepartment(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:departments,name,' . $id,
            'description' => 'nullable|string',
            'staff_email' => 'required|email|unique:departments,staff_email,' . $id,
        ]);
        
        $department->update([
            'name' => $request->name,
            'description' => $request->description,
            'staff_email' => $request->staff_email,
        ]);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8']);
            $department->update(['staff_password' => Hash::make($request->password)]);
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Department',
            'description' => 'Updated department: ' . $department->name,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department updated successfully');
    }
    
    public function destroyDepartment($id)
    {
        $department = Department::findOrFail($id);
        $departmentName = $department->name;
        $department->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Department',
            'description' => 'Deleted department: ' . $departmentName,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.departments')->with('success', 'Department deleted successfully');
    }
    
    // ==================== MANAGE CLEARANCE REQUESTS ====================
    
    public function clearanceRequests()
    {
        $requests = ClearanceRequest::with('student', 'department')->orderBy('created_at', 'desc')->get();
        return view('admin.clearance-requests', compact('requests'));
    }
    
    public function updateClearanceStatus(Request $request, $id)
    {
        $clearanceRequest = ClearanceRequest::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ]);
        
        $oldStatus = $clearanceRequest->status;
        $clearanceRequest->update([
            'status' => $request->status,
            'remarks' => $request->remarks,
            'processed_at' => now(),
        ]);
        
        ApprovalLog::create([
            'clearance_request_id' => $clearanceRequest->id,
            'student_id' => $clearanceRequest->student_id,
            'department_id' => $clearanceRequest->department_id,
            'action' => $request->status,
            'remarks' => $request->remarks,
            'staff_email' => auth()->user()->email ?? 'admin@tcc.com',
        ]);
        
        if ($request->status === 'approved') {
            $pendingRequests = ClearanceRequest::where('student_id', $clearanceRequest->student_id)
                ->where('status', 'pending')
                ->count();
            
            if ($pendingRequests == 0) {
                User::where('id', $clearanceRequest->student_id)->update(['is_cleared' => true]);
            }
        } elseif ($request->status === 'rejected') {
            User::where('id', $clearanceRequest->student_id)->update(['is_cleared' => false]);
        }
        
        try {
            Mail::to($clearanceRequest->student->email)->send(new ClearanceStatusMail(
                $clearanceRequest->student,
                $clearanceRequest->department->name,
                $request->status,
                $request->remarks
            ));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'updated',
            'module' => 'Clearance',
            'description' => 'Changed clearance status from ' . $oldStatus . ' to ' . $request->status . ' for student ID: ' . $clearanceRequest->student_id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.clearance-requests')->with('success', 'Clearance status updated successfully');
    }
    
    public function destroyClearanceRequest($id)
    {
        $clearanceRequest = ClearanceRequest::findOrFail($id);
        $clearanceRequest->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'admin',
            'action' => 'deleted',
            'module' => 'Clearance',
            'description' => 'Deleted clearance request for student ID: ' . $clearanceRequest->student_id,
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.clearance-requests')->with('success', 'Clearance request deleted');
    }
    
    // ==================== ACTIVITY LOGS ====================
    
    public function activityLogs()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')
            ->when(request('user'), function($q) {
                $q->where('user_email', 'like', '%' . request('user') . '%');
            })
            ->when(request('action'), function($q) {
                $q->where('action', request('action'));
            })
            ->when(request('module'), function($q) {
                $q->where('module', request('module'));
            })
            ->when(request('date'), function($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->paginate(50);
        
        return view('admin.activity-logs', compact('logs'));
    }
    // ==================== OFFICER MANAGEMENT ====================

public function officers()
{
    $officers = User::where('role', 'officer')
        ->with('department')
        ->orderBy('created_at', 'desc')
        ->paginate(15);
    
    $departments = Department::where('is_active', true)->get();
    
    return view('admin.officers', compact('officers', 'departments'));
}

public function storeOfficer(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'department_id' => 'required|exists:departments,id',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'officer',
        'department_id' => $request->department_id,
        'is_active' => true,
    ]);

    ActivityLog::create([
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email ?? 'admin',
        'action' => 'created',
        'module' => 'Officer',
        'description' => 'Added new officer: ' . $user->email,
        'ip_address' => request()->ip(),
    ]);

    return redirect()->route('admin.officers')->with('success', 'Officer added successfully! Password: ' . $request->password);
}

public function editOfficer($id)
{
    $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
    return response()->json($officer);
}

public function updateOfficer(Request $request, $id)
{
    $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'department_id' => 'required|exists:departments,id',
    ]);

    $officer->update([
        'name' => $request->name,
        'email' => $request->email,
        'department_id' => $request->department_id,
    ]);

    if ($request->filled('password')) {
        $request->validate(['password' => 'min:8']);
        $officer->update(['password' => Hash::make($request->password)]);
    }

    ActivityLog::create([
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email ?? 'admin',
        'action' => 'updated',
        'module' => 'Officer',
        'description' => 'Updated officer: ' . $officer->email,
        'ip_address' => request()->ip(),
    ]);

    return redirect()->route('admin.officers')->with('success', 'Officer updated successfully!');
}

public function destroyOfficer($id)
{
    $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
    
    if ($officer->id === auth()->id()) {
        return back()->with('error', 'You cannot delete your own account.');
    }
    
    $officerEmail = $officer->email;
    $officer->delete();

    ActivityLog::create([
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email ?? 'admin',
        'action' => 'deleted',
        'module' => 'Officer',
        'description' => 'Deleted officer: ' . $officerEmail,
        'ip_address' => request()->ip(),
    ]);

    return redirect()->route('admin.officers')->with('success', 'Officer deleted successfully!');
}

public function toggleOfficerStatus($id)
{
    $officer = User::where('id', $id)->where('role', 'officer')->firstOrFail();
    $officer->update(['is_active' => !$officer->is_active]);
    
    $status = $officer->is_active ? 'activated' : 'deactivated';
    
    ActivityLog::create([
        'user_id' => auth()->id(),
        'user_email' => auth()->user()->email ?? 'admin',
        'action' => 'updated',
        'module' => 'Officer',
        'description' => $status . ' officer: ' . $officer->email,
        'ip_address' => request()->ip(),
    ]);
    
    return redirect()->route('admin.officers')->with('success', "Officer {$status} successfully!");
}
    // ==================== ADMIN PROFILE ====================
    
    public function profile()
    {
        $admin = Auth::guard('web')->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return redirect()->route('home');
        }
        
        return view('admin.profile', compact('admin'));
    }
    
    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('web')->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return redirect()->route('home');
        }
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'password' => 'nullable|min:8|confirmed',
        ]);
        
        $admin->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
        ]);
        
        if ($request->filled('password')) {
            $admin->update([
                'password' => Hash::make($request->password)
            ]);
        }
        
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }
    
    // ==================== 2FA METHODS ====================
    
    /**
     * Enable 2FA for admin
     */
    public function enable2FA(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin || $admin->role !== 'admin') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->route('home');
        }
        
        $code = rand(100000, 999999);
        
        $admin->admin_2fa_enabled = true;
        $admin->admin_2fa_code = $code;
        $admin->admin_2fa_expires_at = now()->addMinutes(10);
        $admin->save();
        
        try {
            Mail::send([], [], function($message) use ($admin, $code) {
                $message->to($admin->email)
                        ->subject('2FA Enabled - Test Code')
                        ->html("
                            <div style='font-family: Arial, sans-serif;'>
                                <h2>2FA has been enabled for your account</h2>
                                <p>Test code: <strong style='font-size: 24px;'>{$code}</strong></p>
                                <p>This code will expire in 10 minutes.</p>
                            </div>
                        ");
            });
        } catch (\Exception $e) {
            \Log::error('2FA email failed: ' . $e->getMessage());
        }
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => '2FA enabled successfully!']);
        }
        
        return redirect()->route('admin.profile')->with('success', '2FA enabled successfully! Test code sent to your email.');
    }
    
    /**
     * Disable 2FA for admin
     */
    public function disable2FA(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin || $admin->role !== 'admin') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }
            return redirect()->route('home');
        }
        
        $admin->admin_2fa_enabled = false;
        $admin->admin_2fa_code = null;
        $admin->admin_2fa_expires_at = null;
        $admin->save();
        
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => '2FA disabled successfully!']);
        }
        
        return redirect()->route('admin.profile')->with('success', '2FA disabled successfully!');
    }
    
    /**
     * Show 2FA verification form
     */
    public function show2FAVerifyForm()
    {
        // Check if user is already logged in and 2FA is verified
        if (Auth::check() && session('2fa_verified')) {
            return redirect()->route('admin.dashboard');
        }
        
        // Check if there's a pending 2FA session
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            // Kung walang 2FA session, redirect sa login
            return redirect()->route('login')->with('error', 'Please login first.');
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->admin_2fa_enabled) {
            return redirect()->route('login')->with('error', '2FA is not enabled for this account.');
        }
        
        return view('admin.2fa-verify');
    }
    
    /**
     * Verify 2FA code
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->admin_2fa_enabled) {
            return redirect()->route('login')->with('error', '2FA is not enabled for this account.');
        }
        
        if ($user->admin_2fa_code === $request->code && now()->lessThan($user->admin_2fa_expires_at)) {
            $user->admin_2fa_code = null;
            $user->admin_2fa_expires_at = null;
            $user->save();
            
            session(['2fa_verified' => true]);
            session()->forget('2fa_user_id');
            Auth::login($user);
            
            return redirect()->route('admin.dashboard')->with('success', '2FA verified successfully!');
        }
        
        return back()->with('error', 'Invalid or expired verification code.')->withInput();
    }
    
    /**
     * Resend 2FA code
     */
    public function resend2FACode(Request $request)
    {
        $userId = session('2fa_user_id');
        
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Session expired.']);
        }
        
        $user = User::find($userId);
        
        if (!$user || !$user->admin_2fa_enabled) {
            return response()->json(['success' => false, 'message' => '2FA is not enabled.']);
        }
        
        $code = rand(100000, 999999);
        
        $user->admin_2fa_code = $code;
        $user->admin_2fa_expires_at = now()->addMinutes(5);
        $user->save();
        
        try {
            Mail::send([], [], function($message) use ($user, $code) {
                $message->to($user->email)
                        ->subject('2FA Verification Code (Resend)')
                        ->html("<h2>Your 2FA code: <strong>{$code}</strong></h2><p>This code expires in 5 minutes.</p>");
            });
        } catch (\Exception $e) {
            \Log::error('2FA email failed: ' . $e->getMessage());
        }
        
        return response()->json(['success' => true, 'message' => 'New code sent to your email.']);
    }
    
    // ==================== 2FA TOGGLE METHOD ====================
    
    /**
     * Toggle 2FA on/off
     */
    public function toggle2FA(Request $request)
    {
        $admin = auth()->user();
        
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($admin->admin_2fa_enabled) {
            // Disable 2FA
            $admin->admin_2fa_enabled = false;
            $admin->admin_2fa_code = null;
            $admin->admin_2fa_expires_at = null;
            $admin->save();
            
            return response()->json(['success' => true, 'enabled' => false, 'message' => '2FA disabled successfully!']);
        } else {
            // Enable 2FA
            $code = rand(100000, 999999);
            
            $admin->admin_2fa_enabled = true;
            $admin->admin_2fa_code = $code;
            $admin->admin_2fa_expires_at = now()->addMinutes(10);
            $admin->save();
            
            // Send test email
            try {
                Mail::send([], [], function($message) use ($admin, $code) {
                    $message->to($admin->email)
                            ->subject('2FA Enabled - Test Code')
                            ->html("
                                <div style='font-family: Arial, sans-serif;'>
                                    <h2>2FA has been enabled for your account</h2>
                                    <p>Your test code: <strong style='font-size: 24px;'>{$code}</strong></p>
                                    <p>This code will expire in 10 minutes.</p>
                                    <p>You will receive a new code each time you login.</p>
                                </div>
                            ");
                });
            } catch (\Exception $e) {
                \Log::error('2FA email failed: ' . $e->getMessage());
            }
            
            return response()->json(['success' => true, 'enabled' => true, 'message' => '2FA enabled successfully! Test code sent to your email.']);
        }
    }
}