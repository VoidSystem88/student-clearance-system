<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\ClearanceRequest;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ClearanceRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'attachment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
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
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        ClearanceRequest::updateOrCreate(
            ['student_id' => $student->id, 'department_id' => $request->department_id],
            [
                'status' => 'pending',
                'attachment_path' => $attachmentPath,
                'request_message' => $request->request_message,
                'submitted_at' => now(),
                'processed_at' => null,
                'remarks' => null,
            ]
        );

        return back()->with('success', 'Clearance request submitted successfully');
    }

    public function printSlip()
    {
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

        // Generate or reuse clearance token
        if (!$student->clearance_token) {
            $token = Str::random(40);
            // Ensure unique
            while (User::where('clearance_token', $token)->exists()) {
                $token = Str::random(40);
            }
            $student->clearance_token = $token;
            $student->save();
        }

        // QR Code points to public verification URL
        $verifyUrl = url('/verify/' . $student->clearance_token);

        try {
            $options = new QROptions([
                'version'     => 10,
                'output'      => 'png',
                'scale'       => 8,
                'imageBase64' => true,
            ]);

            $qrcode = new QRCode($options);
            $qrCodeBase64 = $qrcode->render($verifyUrl);

        } catch (\Exception $e) {
            $qrCodeBase64 = null;
            \Log::error('QR Code Error: ' . $e->getMessage());
        }

        // Logo watermark
        $logoPath = public_path('images/tcc-logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        $pdf = Pdf::loadView('student.clearance-slip-pdf', compact('student', 'clearanceRequests', 'qrCodeBase64', 'logoBase64'));

        return $pdf->download('clearance_slip_' . $student->student_id . '.pdf');
    }
}