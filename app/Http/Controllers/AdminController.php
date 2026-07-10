<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\ClearanceRequest;
use App\Models\ActivityLog;
use App\Models\Reminder;
use App\Models\DepartmentYearAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

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
        
        // Activity Logs for recent activities
        $activityLogs = ActivityLog::orderBy('created_at', 'desc')->limit(10)->get();
        
        return view('admin.dashboard', compact(
            'totalStudents', 'totalStaff', 'totalDepartments', 'totalRequests', 'clearedStudents',
            'pendingRequests', 'approvedRequests', 'rejectedRequests',
            'students', 'staffs', 'departments', 'clearanceRequests',
            'departmentsList', 'pendingPerDept', 'approvedPerDept', 'rejectedPerDept',
            'months', 'monthlySubmissions', 'statusData', 'activityLogs'
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
    
    /**
     * Get student data for editing (used by some views)
     */
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
    
    // ==================== EXPORT REPORTS ====================

    public function exportStudentsReport()
    {
        $students = User::where('role', 'student')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'students_report_' . date('Y-m-d') . '.csv';
        
        return response()->stream(
            function() use ($students) {
                $handle = fopen('php://output', 'w');
                fputs($handle, "\xEF\xBB\xBF");
                
                fputcsv($handle, ['ID', 'Student ID', 'Account ID', 'First Name', 'Last Name', 'Email', 'Course', 'Year Level', 'Status', 'Registered Date']);
                
                foreach ($students as $student) {
                    fputcsv($handle, [
                        $student->id,
                        $student->student_id,
                        $student->account_id,
                        $student->first_name,
                        $student->last_name,
                        $student->email,
                        $student->course,
                        $student->year_level,
                        $student->is_active ? 'Active' : 'Inactive',
                        $student->created_at->format('Y-m-d H:i:s')
                    ]);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function exportClearanceReport()
    {
        $requests = ClearanceRequest::with('student', 'department')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'clearance_report_' . date('Y-m-d') . '.csv';
        
        return response()->stream(
            function() use ($requests) {
                $handle = fopen('php://output', 'w');
                fputs($handle, "\xEF\xBB\xBF");
                
                fputcsv($handle, ['ID', 'Student Name', 'Student ID', 'Email', 'Department', 'Status', 'Submitted Date', 'Processed Date', 'Remarks']);
                
                foreach ($requests as $req) {
                    fputcsv($handle, [
                        $req->id,
                        optional($req->student)->first_name . ' ' . optional($req->student)->last_name,
                        optional($req->student)->student_id,
                        optional($req->student)->email,
                        optional($req->department)->name,
                        $req->status,
                        $req->submitted_at ? $req->submitted_at->format('Y-m-d H:i:s') : '',
                        $req->processed_at ? $req->processed_at->format('Y-m-d H:i:s') : '',
                        $req->remarks
                    ]);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }

    public function exportActivityReport()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')
            ->limit(1000)
            ->get();
        
        $filename = 'activity_logs_' . date('Y-m-d') . '.csv';
        
        return response()->stream(
            function() use ($logs) {
                $handle = fopen('php://output', 'w');
                fputs($handle, "\xEF\xBB\xBF");
                
                fputcsv($handle, ['ID', 'User Email', 'User Role', 'Action', 'Module', 'Description', 'IP Address', 'Date/Time']);
                
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->user_email,
                        $log->user_role,
                        $log->action,
                        $log->module,
                        $log->description,
                        $log->ip_address,
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : ''
                    ]);
                }
                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
    
    // ==================== COURSE MANAGEMENT ====================

    public function courses()
    {
        // Auto-sync: Kunin ang LAHAT ng courses mula sa users table
        $allCoursesFromUsers = User::where('role', 'student')
            ->whereNotNull('course')
            ->where('course', '!=', '')
            ->distinct()
            ->pluck('course');
        
        // I-sync lahat sa courses table
        foreach ($allCoursesFromUsers as $courseCode) {
            Course::firstOrCreate(
                ['code' => $courseCode],
                [
                    'name' => $courseCode,
                    'department' => $this->getDepartmentForCourse($courseCode),
                    'duration' => '4 Years',
                    'is_active' => true,
                    'created_by' => auth()->id(),
                ]
            );
        }
        
        $courses = Course::with('creator')
            ->orderBy('code')
            ->paginate(15);
        
        return view('admin.courses', compact('courses'));
    }

    private function getDepartmentForCourse($courseCode)
    {
        $courseCode = strtoupper($courseCode);
        
        $mapping = [
            'BSIT' => 'College of Computer Studies',
            'BSCS' => 'College of Computer Studies',
            'BSIS' => 'College of Computer Studies',
            'BSBA' => 'College of Business and Accountancy',
            'BSBA-FM' => 'College of Business and Accountancy',
            'BSHM' => 'College of Hospitality Management',
            'BEEd' => 'College of Education',
            'BSEd' => 'College of Education',
            'BSEd-ENGLISH' => 'College of Education',
            'BSCRIM' => 'College of Criminology',
        ];
        
        if (isset($mapping[$courseCode])) {
            return $mapping[$courseCode];
        }
        
        if (str_contains($courseCode, 'BSIT')) return 'College of Computer Studies';
        if (str_contains($courseCode, 'BSCS')) return 'College of Computer Studies';
        if (str_contains($courseCode, 'BSIS')) return 'College of Computer Studies';
        if (str_contains($courseCode, 'BSBA')) return 'College of Business and Accountancy';
        if (str_contains($courseCode, 'BSHM')) return 'College of Hospitality Management';
        if (str_contains($courseCode, 'BEEd')) return 'College of Education';
        if (str_contains($courseCode, 'BSEd')) return 'College of Education';
        if (str_contains($courseCode, 'BSCRIM')) return 'College of Criminology';
        
        return 'General';
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $course = Course::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'department' => $request->department,
            'duration' => $request->duration,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'created',
            'module' => 'Course',
            'description' => "Added new course: {$course->code} - {$course->name}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.courses')->with('success', "Course {$course->code} added successfully!");
    }

    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,' . $id,
            'name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'duration' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $oldCode = $course->code;
        
        $course->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'description' => $request->description,
            'department' => $request->department,
            'duration' => $request->duration,
        ]);

        if ($oldCode !== $course->code) {
            User::where('course', $oldCode)->update(['course' => $course->code]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Course',
            'description' => "Updated course: {$oldCode} -> {$course->code}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.courses')->with('success', "Course updated successfully!");
    }

    public function destroyCourse($id)
    {
        $course = Course::findOrFail($id);
        
        $studentCount = User::where('course', $course->code)->where('role', 'student')->count();
        
        if ($studentCount > 0) {
            return redirect()->route('admin.courses')->with('error', 
                "Cannot delete {$course->code} because {$studentCount} student(s) are enrolled in this course.");
        }
        
        $courseCode = $course->code;
        $courseName = $course->name;
        $course->delete();
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'deleted',
            'module' => 'Course',
            'description' => "Deleted course: {$courseCode} - {$courseName}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.courses')->with('success', "Course deleted successfully!");
    }

    public function toggleCourseStatus($id)
    {
        $course = Course::findOrFail($id);
        $course->update(['is_active' => !$course->is_active]);
        
        $status = $course->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Course',
            'description' => "{$status} course: {$course->code} - {$course->name}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.courses')->with('success', "Course {$status} successfully!");
    }
    
    // ============ REMINDERS METHODS ============

    public function reminders()
    {
        $reminders = Reminder::with('creator', 'department')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $departments = Department::where('is_active', true)->get();
        
        return view('admin.reminders', compact('reminders', 'departments'));
    }

    public function storeReminder(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:staff,officer,both',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // ✅ FIX: If start_date is null, use current date
        $startDate = $request->start_date ?? now();
        
        $reminder = Reminder::create([
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
            'department_id' => $request->department_id,
            'start_date' => $startDate,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
            'created_by' => auth()->id(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'created',
            'module' => 'Reminder',
            'description' => "Created reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.reminders')->with('success', 'Reminder created successfully!');
    }

    public function editReminder($id)
    {
        $reminder = Reminder::findOrFail($id);
        return response()->json($reminder);
    }

    public function updateReminder(Request $request, $id)
    {
        $reminder = Reminder::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:staff,officer,both',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // ✅ FIX: If start_date is null, use current date
        $startDate = $request->start_date ?? now();

        $reminder->update([
            'title' => $request->title,
            'message' => $request->message,
            'target_role' => $request->target_role,
            'department_id' => $request->department_id,
            'start_date' => $startDate,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Reminder',
            'description' => "Updated reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.reminders')->with('success', 'Reminder updated successfully!');
    }

    public function destroyReminder($id)
    {
        $reminder = Reminder::findOrFail($id);
        $title = $reminder->title;
        $reminder->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'deleted',
            'module' => 'Reminder',
            'description' => "Deleted reminder: {$title}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('admin.reminders')->with('success', 'Reminder deleted successfully!');
    }

    public function toggleReminder($id)
    {
        $reminder = Reminder::findOrFail($id);
        $reminder->update(['is_active' => !$reminder->is_active]);
        
        $status = $reminder->is_active ? 'activated' : 'deactivated';
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email,
            'action' => 'updated',
            'module' => 'Reminder',
            'description' => "{$status} reminder: {$reminder->title}",
            'ip_address' => request()->ip(),
        ]);
        
        return redirect()->route('admin.reminders')->with('success', "Reminder {$status} successfully!");
    }

    public function assignYearsToDepartment($departmentId, Request $request)
    {
        // Logic para mag-assign ng year levels sa department
        $years = $request->input('years', []);
        
        // Delete existing assignments
        DepartmentYearAssignment::where('department_id', $departmentId)->delete();
        
        // Insert new assignments
        foreach ($years as $year) {
            DepartmentYearAssignment::create([
                'department_id' => $departmentId,
                'year_level' => $year
            ]);
        }
        
        return redirect()->back()->with('success', 'Year levels assigned successfully');
    }
}