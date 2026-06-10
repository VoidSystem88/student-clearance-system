<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PublicAssistanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'student_id' => 'nullable|string',
            'type' => 'required|in:bug,login_issue,registration_issue,clearance_issue,other',
            'message' => 'required|string|min:10',
        ]);

        // Get browser info
        $browserInfo = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $currentUrl = url()->previous();

        $bugReport = BugReport::create([
            'name' => $request->name,
            'email' => $request->email,
            'student_id' => $request->student_id,
            'type' => $request->type,
            'message' => $request->message,
            'browser_info' => $browserInfo,
            'url' => $currentUrl,
            'status' => 'pending',
        ]);

        // Get all admin users
        $admins = User::where('role', 'admin')->get();
        
        // Send notification to admins
        foreach ($admins as $admin) {
            Notification::create([
                'type' => 'bug_report',
                'title' => 'New Bug/Issue Report',
                'message' => $request->name . ' (' . $request->email . ') reported: ' . ucfirst(str_replace('_', ' ', $request->type)),
                'data' => json_encode([
                    'report_id' => $bugReport->id,
                    'type' => $request->type,
                    'email' => $request->email,
                ]),
                'user_id' => $admin->id,
                'is_read' => false,
            ]);
        }

        // Send email to admin
        $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();
        
        foreach ($adminEmails as $adminEmail) {
            try {
                Mail::send('emails.bug-report', ['report' => $bugReport, 'type' => $request->type], function($message) use ($adminEmail) {
                    $message->to($adminEmail)
                            ->subject('New Bug/Issue Report - ' . date('Y-m-d H:i'));
                });
            } catch (\Exception $e) {
                Log::error('Failed to send bug report email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your report! Our support team will review it shortly.'
        ]);
    }

    public function index()
    {
        $reports = BugReport::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.bug-reports', compact('reports'));
    }

    public function update(Request $request, $id)
    {
        $report = BugReport::findOrFail($id);
        
        $report->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
        ]);

        return redirect()->back()->with('success', 'Report updated successfully');
    }
}