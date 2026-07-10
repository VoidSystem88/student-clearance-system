<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClearanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ClearanceRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'attachment' => 'nullable|file|max:5120',
            'request_message' => 'nullable|string|max:500',
        ]);

        $student = Auth::user();

        $existing = ClearanceRequest::where('student_id', $student->id)
            ->where('department_id', $request->department_id)
            ->first();

        if ($existing && $existing->status === 'pending') {
            return back()->with('error', 'You already have a pending request for this department');
        }

        if ($existing && $existing->status === 'approved') {
            return back()->with('error', 'You are already cleared for this department');
        }

        $attachmentPath = null;

        // ============ SERVER-SIDE IMAGE-ONLY VALIDATION ============
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $extension = strtolower($file->getClientOriginalExtension());
            $mimeType = $file->getMimeType();
            
            // Tanging ito lang ang pinapayagan
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png'];
            
            // Check extension
            if (!in_array($extension, $allowedExtensions)) {
                return back()->with('error', '❌ JPG, JPEG, o PNG image files lang ang pwede. Hindi tinatanggap ang PDF o ibang file types.');
            }
            
            // Check mime type
            if (!in_array($mimeType, $allowedMimes)) {
                return back()->with('error', '❌ Invalid image file. Please upload a valid JPG or PNG.');
            }
            
            // Double-check na tunay na image (GD library)
            $imageData = @file_get_contents($file->getPathname());
            $checkImage = @imagecreatefromstring($imageData);
            if ($checkImage === false) {
                return back()->with('error', '❌ The file is not a valid image. Please upload a real JPG or PNG.');
            }
            imagedestroy($checkImage);
            
            // I-save ang file
            $attachmentPath = $file->store('attachments', 'public');
        }

        // Check if student is in verified list
        $isVerified = \App\Models\VerifiedStudent::where('student_id', $student->student_id)
            ->where('department_id', $request->department_id)
            ->where('is_active', true)
            ->exists();

        $status = $isVerified ? 'approved' : 'pending';

        $clearanceRequest = ClearanceRequest::updateOrCreate(
            ['student_id' => $student->id, 'department_id' => $request->department_id],
            [
                'status' => $status,
                'attachment_path' => $attachmentPath,
                'request_message' => $request->request_message,
                'submitted_at' => now(),
                'processed_at' => $isVerified ? now() : null,
                'remarks' => $isVerified ? 'Auto-approved (verified student)' : null,
            ]
        );

        if ($isVerified) {
            \App\Models\ApprovalLog::create([
                'clearance_request_id' => $clearanceRequest->id,
                'student_id' => $student->id,
                'department_id' => $request->department_id,
                'action' => 'approved',
                'remarks' => 'Auto-approved (student is in verified list)',
                'staff_email' => 'system@auto-approve',
            ]);
            
            $this->checkFullClearance($student->id);
            
            return back()->with('success', '✅ Your clearance has been AUTO-APPROVED! You are pre-verified for this department.');
        }

        return back()->with('success', 'Clearance request submitted successfully. Waiting for staff approval.');
    }

    // ============ PRINT CLEARANCE SLIP ============
    public function printSlip()
    {
        try {
            $student = Auth::user();

            $allDepartments = Department::where('is_active', true)->count();
            $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
                ->with('department')
                ->get();
            $approvedCount = $clearanceRequests->where('status', 'approved')->count();
            $allApproved = $approvedCount > 0 && $approvedCount === $allDepartments;

            if (!$allApproved) {
                return back()->with('error', 'You are not fully cleared yet');
            }

            if (!$student->clearance_token) {
                $token = Str::random(40);
                while (User::where('clearance_token', $token)->exists()) {
                    $token = Str::random(40);
                }
                $student->clearance_token = $token;
                $student->save();
            }

            $verifyUrl = url('/verify/' . $student->clearance_token);

            // QR Code using QR Server API (free, no API key)
            $qrCodeBase64 = null;
            
            try {
                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);
                
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $qrCodeUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    $qrCodeData = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode === 200 && $qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
                if (!$qrCodeBase64) {
                    $qrCodeData = @file_get_contents($qrCodeUrl);
                    if ($qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
                if (!$qrCodeBase64) {
                    \Log::warning('QR Code generation failed for student: ' . $student->student_id);
                }
                
            } catch (\Exception $e) {
                \Log::error('QR Error: ' . $e->getMessage());
                $qrCodeBase64 = null;
            }

            $logoPath = public_path('images/tcc-logo.png');
            $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

            $pdf = Pdf::loadView('student.clearance-slip-pdf', compact('student', 'clearanceRequests', 'qrCodeBase64', 'logoBase64', 'verifyUrl'));

            return $pdf->download('clearance_slip_' . $student->student_id . '.pdf');
            
        } catch (\Exception $e) {
            \Log::error('Print Slip Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating clearance slip: ' . $e->getMessage());
        }
    }

    // ============ HELPER METHOD ============
    private function checkFullClearance($studentId)
    {
        $student = User::find($studentId);
        $allDepartments = Department::where('is_active', true)->count();
        $approvedRequests = ClearanceRequest::where('student_id', $studentId)
            ->where('status', 'approved')
            ->count();
        
        $allApproved = ($approvedRequests > 0 && $approvedRequests >= $allDepartments);
        
        if ($allApproved && $student) {
            $student->update([
                'is_cleared' => true,
                'cleared_at' => now(),
            ]);
        }
    }

    // ============ VIEW CLEARANCE SLIP (NO DOWNLOAD) ============
    public function viewSlip()
    {
        try {
            $student = Auth::user();

            $allDepartments = Department::where('is_active', true)->count();
            $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
                ->with('department')
                ->get();
            $approvedCount = $clearanceRequests->where('status', 'approved')->count();
            $allApproved = $approvedCount > 0 && $approvedCount === $allDepartments;

            if (!$allApproved) {
                return back()->with('error', 'You are not fully cleared yet');
            }

            if (!$student->clearance_token) {
                $token = Str::random(40);
                while (User::where('clearance_token', $token)->exists()) {
                    $token = Str::random(40);
                }
                $student->clearance_token = $token;
                $student->save();
            }

            $verifyUrl = url('/verify/' . $student->clearance_token);

            $qrCodeBase64 = null;
            
            try {
                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);
                
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $qrCodeUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    $qrCodeData = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode === 200 && $qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
                if (!$qrCodeBase64) {
                    $qrCodeData = @file_get_contents($qrCodeUrl);
                    if ($qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('QR Error: ' . $e->getMessage());
                $qrCodeBase64 = null;
            }

            $logoPath = public_path('images/tcc-logo.png');
            $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

            return view('student.clearance-slip-view', compact('student', 'clearanceRequests', 'qrCodeBase64', 'logoBase64', 'verifyUrl'));
            
        } catch (\Exception $e) {
            \Log::error('View Slip Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating clearance slip: ' . $e->getMessage());
        }
    }

    // ============ VIEW CLEARANCE SLIP PDF VERSION ============
    public function viewSlipPdf()
    {
        try {
            $student = Auth::user();

            $allDepartments = Department::where('is_active', true)->count();
            $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
                ->with('department')
                ->get();
            $approvedCount = $clearanceRequests->where('status', 'approved')->count();
            $allApproved = $approvedCount > 0 && $approvedCount === $allDepartments;

            if (!$allApproved) {
                return back()->with('error', 'You are not fully cleared yet');
            }

            if (!$student->clearance_token) {
                $token = Str::random(40);
                while (User::where('clearance_token', $token)->exists()) {
                    $token = Str::random(40);
                }
                $student->clearance_token = $token;
                $student->save();
            }

            $verifyUrl = url('/verify/' . $student->clearance_token);

            $qrCodeBase64 = null;
            
            try {
                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verifyUrl);
                
                if (function_exists('curl_init')) {
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $qrCodeUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                    $qrCodeData = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode === 200 && $qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
                if (!$qrCodeBase64) {
                    $qrCodeData = @file_get_contents($qrCodeUrl);
                    if ($qrCodeData) {
                        $qrCodeBase64 = base64_encode($qrCodeData);
                    }
                }
                
            } catch (\Exception $e) {
                \Log::error('QR Error: ' . $e->getMessage());
                $qrCodeBase64 = null;
            }

            $logoPath = public_path('images/tcc-logo.png');
            $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

            return view('student.clearance-slip-view-pdf', compact('student', 'clearanceRequests', 'qrCodeBase64', 'logoBase64', 'verifyUrl'));
            
        } catch (\Exception $e) {
            \Log::error('View Slip Error: ' . $e->getMessage());
            return back()->with('error', 'Error generating clearance slip: ' . $e->getMessage());
        }
    }

    // ============ CANCEL PENDING REQUEST ============
    public function cancel($id)
    {
        try {
            $student = Auth::user();
            
            $clearanceRequest = ClearanceRequest::where('id', $id)
                ->where('student_id', $student->id)
                ->where('status', 'pending')
                ->first();
            
            if (!$clearanceRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request not found or already processed.'
                ], 404);
            }
            
            $clearanceRequest->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Clearance request cancelled successfully.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}