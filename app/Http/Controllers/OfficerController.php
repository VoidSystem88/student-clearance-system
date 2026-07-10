<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\VerifiedStudent;
use App\Models\VerifiedExport;
use App\Models\Reminder;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OfficerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        
        if (!$department) {
            return redirect('/')->with('error', 'Your account is not linked to any department.');
        }
        
        $students = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        
        $verifiedStudentIds = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->pluck('student_id')->toArray();
        
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->get(['id', 'student_id', 'student_name', 'verified_at', 'verified_by_role']);
        
        $studentIds = $verifiedStudents->pluck('student_id')->toArray();
        $studentsData = User::whereIn('student_id', $studentIds)->where('role', 'student')
            ->get(['student_id', 'course', 'year_level']);
        
        foreach ($verifiedStudents as $verified) {
            $studentInfo = $studentsData->firstWhere('student_id', $verified->student_id);
            $verified->course = $studentInfo->course ?? 'N/A';
            $verified->year_level = $studentInfo->year_level ?? 'N/A';
        }
        
        $verifiedCount = $verifiedStudents->count();
        
        return view('officer.dashboard', compact('department', 'students', 'verifiedStudentIds', 'verifiedStudents', 'verifiedCount'));
    }
    
    public function students()
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return redirect('/')->with('error', 'Department not found.');
        
        $allStudents = User::where('role', 'student')->orderBy('created_at', 'desc')->get();
        $verifiedIds = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->pluck('student_id')->toArray();
        
        $students = $allStudents->filter(function($s) use ($verifiedIds) {
            return !in_array($s->student_id, $verifiedIds);
        })->values();
        
        $verifiedCount = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->count();
        
        return view('officer.students', compact('students', 'verifiedCount', 'verifiedIds'));
    }
    
    public function verified()
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return redirect('/')->with('error', 'Department not found.');
        
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->with('student')->orderBy('verified_at', 'desc')->get();
        $verifiedCount = $verifiedStudents->count();
        
        return view('officer.verified', compact('verifiedStudents', 'verifiedCount'));
    }
    
    public function sendReportPage()
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return redirect('/')->with('error', 'Department not found.');
        
        $verifiedCount = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->count();
        
        $reports = DB::table('sent_reports')->where('officer_id', $user->id)
            ->orderBy('created_at', 'desc')->get();
        
        foreach ($reports as $report) {
            $dept = Department::find($report->department_id);
            $report->department_name = $dept->name ?? 'N/A';
        }
        
        return view('officer.send-report', compact('verifiedCount', 'department', 'reports'));
    }
    
    public function sendReport(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|exists:departments,id',
                'report_title' => 'nullable|string|max:255',
                'event_name' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            ]);
            
            $user = Auth::user();
            $department = Department::find($request->department_id);
            if (!$department) return redirect()->back()->with('error', 'Department not found.');
            
            $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
                ->where('is_active', true)->with('student')->get();
            
            if ($verifiedStudents->isEmpty()) {
                return redirect()->back()->with('error', 'No verified students to send.');
            }
            
            $eventName = $request->event_name ?? 'General';
            $csvContent = "Student ID,Student Name,Course,Year Level,Email,Date Verified,Event Name\n";
            
            foreach ($verifiedStudents as $verified) {
                $student = $verified->student;
                $studentName = $student ? ($student->first_name . ' ' . $student->last_name) : ($verified->student_name ?? 'N/A');
                $csvContent .= "\"{$verified->student_id}\",\"{$studentName}\",\"" . ($student->course ?? 'N/A') . "\",\"" . ($student->year_level ?? 'N/A') . "\",\"" . ($student->email ?? 'N/A') . "\",\"" . now()->format('Y-m-d H:i:s') . "\",\"{$eventName}\"\n";
            }
            
            $attachmentPath = null;
            $attachmentOriginalName = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $attachmentOriginalName = $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('sent_reports/attachments', time() . '_' . $file->getClientOriginalName(), 'public');
            }
            
            $reportId = DB::table('sent_reports')->insertGetId([
                'officer_id' => $user->id,
                'department_id' => $request->department_id,
                'report_title' => $request->report_title ?? 'Verified Students Report',
                'event_name' => $eventName,
                'notes' => $request->notes,
                'csv_data' => $csvContent,
                'attachment_path' => $attachmentPath,
                'attachment_original_name' => $attachmentOriginalName,
                'total_students' => $verifiedStudents->count(),
                'status' => 'pending',
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $this->notifyStaff($department, $request, $verifiedStudents->count(), $reportId);
            
            VerifiedStudent::where('department_id', $department->id)
                ->where('is_active', true)->update(['is_active' => false, 'updated_at' => now()]);
            
            return redirect()->route('officer.send.report')
                ->with('success', 'Report sent! ' . $verifiedStudents->count() . ' students included.');
        } catch (\Exception $e) {
            Log::error('Send report error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    private function notifyStaff($department, $request, $totalStudents, $reportId)
    {
        try {
            $staffUsers = User::where('department_id', $department->id)->where('role', 'staff')->get();
            $officer = Auth::user();
            
            foreach ($staffUsers as $staff) {
                DB::table('notifications')->insert([
                    'user_id' => $staff->id,
                    'type' => 'report_received',
                    'title' => 'New Report Received',
                    'message' => 'Report from ' . ($officer->name ?? 'Officer') . ': ' . $totalStudents . ' students',
                    'link' => '/staff/reports/' . $reportId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Notify staff error: ' . $e->getMessage());
        }
    }
    
    public function verifyStudent(Request $request)
    {
        $request->validate(['student_id' => 'required|string', 'student_name' => 'required|string']);
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        
        $student = User::where('student_id', $request->student_id)->where('role', 'student')->first();
        if (!$student) return response()->json(['success' => false, 'message' => 'Student not found.'], 404);
        
        if (VerifiedStudent::where('department_id', $department->id)->where('student_id', $request->student_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already verified.'], 400);
        }
        
        VerifiedStudent::create([
            'department_id' => $department->id,
            'student_id' => $request->student_id,
            'student_name' => $request->student_name,
            'verified_by' => $user->id,
            'verified_by_role' => 'officer',
            'verified_at' => now(),
            'is_active' => true,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Student verified!']);
    }
    
    public function uploadVerifiedList(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return redirect()->back()->with('error', 'Department not found.');
        
        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        fgetcsv($handle);
        
        $added = 0; $errors = [];
        while (($row = fgetcsv($handle)) !== false) {
            $studentId = trim($row[0] ?? '');
            if (empty($studentId)) continue;
            
            $student = User::where('student_id', $studentId)->where('role', 'student')->first();
            if (!$student) { $errors[] = "ID '{$studentId}' not found"; continue; }
            if (VerifiedStudent::where('department_id', $department->id)->where('student_id', $studentId)->exists()) {
                $errors[] = "ID '{$studentId}' already verified"; continue;
            }
            
            VerifiedStudent::create([
                'department_id' => $department->id,
                'student_id' => $studentId,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'verified_by' => $user->id,
                'verified_by_role' => 'officer',
                'verified_at' => now(),
                'is_active' => true,
            ]);
            $added++;
        }
        fclose($handle);
        
        $msg = "{$added} verified!";
        if ($errors) $msg .= " Errors: " . implode(', ', array_slice($errors, 0, 3));
        return redirect()->route('officer.dashboard')->with('success', $msg);
    }
    
    public function addVerifiedStudent(Request $request)
    {
        $request->validate(['student_id' => 'required|string|exists:users,student_id']);
        $user = Auth::user();
        $department = $this->getDepartment($user);
        if (!$department) return response()->json(['success' => false, 'message' => 'Department not found.'], 404);
        
        if (VerifiedStudent::where('department_id', $department->id)->where('student_id', $request->student_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already verified.'], 400);
        }
        
        $student = User::where('student_id', $request->student_id)->first();
        VerifiedStudent::create([
            'department_id' => $department->id,
            'student_id' => $request->student_id,
            'student_name' => $student->first_name . ' ' . $student->last_name,
            'verified_by' => $user->id,
            'verified_by_role' => 'officer',
            'verified_at' => now(),
            'is_active' => true,
        ]);
        
        return response()->json(['success' => true, 'message' => 'Student added!']);
    }
    
    public function removeVerifiedStudent($id)
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        $verified = VerifiedStudent::where('id', $id)->where('department_id', $department->id)->first();
        if (!$verified) return response()->json(['success' => false, 'message' => 'Not found.'], 404);
        $verified->delete();
        return response()->json(['success' => true, 'message' => 'Removed.']);
    }
    
    public function exportVerifiedCSV()
    {
        $user = Auth::user();
        $department = $this->getDepartment($user);
        $verifiedStudents = VerifiedStudent::where('department_id', $department->id)
            ->where('is_active', true)->with('student')->get();
        
        $filename = 'verified_students_' . date('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="' . $filename . '"'];
        
        return response()->stream(function() use ($verifiedStudents) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Name', 'Course', 'Year', 'Email', 'Date Verified']);
            foreach ($verifiedStudents as $v) {
                fputcsv($file, [$v->student_id, $v->student_name, $v->student->course ?? '', $v->student->year_level ?? '', $v->student->email ?? '', $v->verified_at->format('Y-m-d')]);
            }
            fclose($file);
        }, 200, $headers);
    }
    
    public function downloadExport($id)
    {
        $export = VerifiedExport::findOrFail($id);
        return response()->download(storage_path('app/' . $export->file_path));
    }

    // ============ NOTIFICATIONS (FIXED) ============
    public function notifications()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $all = collect();
        
        // Reminders
        try {
            $reminders = Reminder::where('is_active', true)
                ->where('start_date', '<=', $today)
                ->where(function($q) use ($today) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                })
                ->where(function($q) {
                    $q->where('target_role', 'officer')->orWhere('target_role', 'both');
                })
                ->orderBy('created_at', 'desc')->get();
            
            foreach ($reminders as $r) {
                $all->push([
                    'id' => 'reminder_' . $r->id,
                    'title' => $r->title ?? 'Reminder',
                    'message' => $r->message ?? '',
                    'type' => 'reminder',
                    'read_at' => null,
                    'created_at' => $r->created_at ? $r->created_at->diffForHumans() : 'Unknown',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Reminder error: ' . $e->getMessage());
        }
        
        // Announcements
        try {
            $announcements = Announcement::where('is_active', true)
                ->where(function($q) use ($today) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
                })
                ->where(function($q) use ($today) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
                })
                ->orderBy('created_at', 'desc')->get();
            
            foreach ($announcements as $a) {
                $all->push([
                    'id' => 'ann_' . $a->id,
                    'title' => $a->title ?? 'Announcement',
                    'message' => Str::limit($a->content ?? '', 100),
                    'type' => 'announcement',
                    'read_at' => null,
                    'created_at' => $a->created_at ? $a->created_at->diffForHumans() : 'Unknown',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Announcement error: ' . $e->getMessage());
        }
        
        // DB notifications
        try {
            $dbNotifs = DB::table('notifications')->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')->take(5)->get();
            
            foreach ($dbNotifs as $n) {
                $all->push([
                    'id' => 'db_' . $n->id,
                    'title' => $n->title ?? 'Notification',
                    'message' => $n->message ?? '',
                    'type' => $n->type ?? 'general',
                    'read_at' => $n->is_read ? now()->subHour()->diffForHumans() : null,
                    'created_at' => isset($n->created_at) ? date('M d, Y', strtotime($n->created_at)) : 'Unknown',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('DB notif error: ' . $e->getMessage());
        }
        
        $sorted = $all->sortByDesc('created_at')->values()->take(10);
        
        return response()->json([
            'success' => true,
            'notifications' => $sorted,
            'unread_count' => $sorted->where('read_at', null)->count(),
        ]);
    }
    
    public function markNotificationRead(Request $request)
    {
        $id = $request->id ?? '';
        if (strpos($id, 'db_') === 0) {
            $dbId = substr($id, 3);
            DB::table('notifications')->where('id', $dbId)->update([
                'is_read' => true,
                'updated_at' => now()
            ]);
        }
        return response()->json(['success' => true]);
    }
    
    public function markAllNotificationsRead()
    {
        DB::table('notifications')->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);
        return response()->json(['success' => true]);
    }

    private function getDepartment($user)
    {
        if ($user->department_id && $dept = Department::find($user->department_id)) {
            return $dept;
        }
        return Department::where('name', $user->department_id)->first();
    }
}