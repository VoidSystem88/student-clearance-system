<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AssistanceController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $requests = SupportRequest::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('student.assistance', compact('student', 'requests'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'request_type' => 'required|string',
            'description' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:5120' // 5MB max
        ]);

        $student = Auth::user();
        
        // Handle attachment
        $attachmentPath = null;
        $attachmentOriginalName = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentOriginalName = $file->getClientOriginalName();
            
            // Check if it's an image for compression
            $isImage = in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']);
            
            if ($isImage) {
                // Compress and save image
                $filename = time() . '_' . uniqid() . '.jpg';
                $destinationPath = public_path('attachments/assistance');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                $this->compressAndSaveImage($file, $destinationPath . '/' . $filename);
                $attachmentPath = 'attachments/assistance/' . $filename;
            } else {
                // For PDF files - no compression needed
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('attachments/assistance');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                $file->move($destinationPath, $filename);
                $attachmentPath = 'attachments/assistance/' . $filename;
            }
        }
        
        // Create support request
        $supportRequest = SupportRequest::create([
            'student_id' => Auth::id(),
            'request_type' => $request->request_type,
            'description' => $request->description,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        
        // Optional: Send email notification to admin/support
        try {
            Mail::send('emails.assistance_notification', [
                'request' => $supportRequest,
                'student' => $student
            ], function($message) {
                $message->to('admin@tcc.edu.ph') // Palitan ng actual admin email
                        ->subject('New Assistance Request - TCC Clearance System');
            });
        } catch (\Exception $e) {
            \Log::error('Assistance email error: ' . $e->getMessage());
        }
        
        return redirect()->route('student.assistance')
            ->with('success', 'Your assistance request has been submitted! Reference #: ' . $supportRequest->id);
    }
    
    /**
     * Compress and save image to reduce file size
     */
    private function compressAndSaveImage($file, $path)
    {
        $image = null;
        $sourcePath = $file->getPathname();
        
        // Create image resource based on mime type
        $mime = $file->getMimeType();
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                // Preserve transparency for PNG
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                // Fallback: just move the file
                $file->move(dirname($path), basename($path));
                return;
        }
        
        if (!$image) {
            $file->move(dirname($path), basename($path));
            return;
        }
        
        // Get original dimensions
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Resize if too large (max 1200px)
        $maxWidth = 1200;
        $maxHeight = 1200;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($mime == 'image/png') {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }
            
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $newImage;
        }
        
        // Save compressed image (85% quality for JPEG, 9 for PNG)
        if ($mime == 'image/png') {
            imagepng($image, $path, 8); // 0-9, 9 is highest compression
        } elseif ($mime == 'image/gif') {
            imagegif($image, $path);
        } else {
            imagejpeg($image, $path, 85); // 85% quality
        }
        
        imagedestroy($image);
    }
    
    /**
     * View attachment
     */
    public function viewAttachment($id)
    {
        $request = SupportRequest::where('id', $id)
            ->where('student_id', Auth::id())
            ->firstOrFail();
            
        if (!$request->attachment_path) {
            abort(404, 'No attachment found');
        }
        
        $path = public_path($request->attachment_path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        return response()->file($path);
    }
}