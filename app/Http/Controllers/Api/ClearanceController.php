<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClearanceController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
        ]);
        
        $student = $request->user();
        
        $existing = ClearanceRequest::where('student_id', $student->id)
            ->where('department_id', $request->department_id)
            ->first();
        
        if ($existing && $existing->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending request for this department'
            ], 400);
        }
        
        if ($existing && $existing->status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'You are already cleared for this department'
            ], 400);
        }
        
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }
        
        $clearanceRequest = ClearanceRequest::updateOrCreate(
            ['student_id' => $student->id, 'department_id' => $request->department_id],
            [
                'status' => 'pending',
                'attachment_path' => $attachmentPath,
                'submitted_at' => now(),
                'processed_at' => null,
                'remarks' => null,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Clearance request submitted successfully',
            'request' => [
                'id' => $clearanceRequest->id,
                'department_id' => $clearanceRequest->department_id,
                'status' => $clearanceRequest->status,
                'submitted_at' => $clearanceRequest->submitted_at,
            ]
        ]);
    }
    
    public function history(Request $request)
    {
        $student = $request->user();
        
        $history = ClearanceRequest::where('student_id', $student->id)
            ->with('department')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'history' => $history->map(function($item) {
                return [
                    'id' => $item->id,
                    'department' => $item->department->name,
                    'status' => $item->status,
                    'submitted_at' => $item->submitted_at,
                    'processed_at' => $item->processed_at,
                    'remarks' => $item->remarks,
                ];
            })
        ]);
    }
    
    public function printSlip()
{
    $student = Auth::user();
    
    $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
        ->with('department')
        ->get();
    
    $allApproved = $clearanceRequests->every(function ($request) {
        return $request->status === 'approved';
    });
    
    if (!$allApproved || $clearanceRequests->count() === 0) {
        return back()->with('error', 'You are not fully cleared yet');
    }
    
    // Convert logo to base64 for PDF
    $logoPath = public_path('images/tcc-logo.png');
    $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    
    $pdf = Pdf::loadView('student.clearance-slip-pdf', compact('student', 'clearanceRequests', 'logoBase64'));
    
    return $pdf->download('clearance_slip_' . $student->student_id . '.pdf');
}
}