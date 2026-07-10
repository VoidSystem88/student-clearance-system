<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use App\Models\User;
use App\Models\ApprovalLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClearanceStatusMail;

class ClearanceManagementController extends Controller
{
    public function index()
    {
        $requests = ClearanceRequest::with('student', 'department')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.clearance-requests', compact('requests'));
    }

    public function updateStatus(Request $request, $id)
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

    public function destroy($id)
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
}