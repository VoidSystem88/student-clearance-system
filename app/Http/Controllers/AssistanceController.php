<?php

namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:5120'
        ]);

        $student = Auth::user();
        
        $attachmentPath = null;
        $attachmentOriginalName = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentOriginalName = $file->getClientOriginalName();
            
            $isImage = in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']);
            
            if ($isImage) {
                $filename = time() . '_' . uniqid() . '.jpg';
                $destinationPath = public_path('attachments/assistance');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                $this->compressAndSaveImage($file, $destinationPath . '/' . $filename);
                $attachmentPath = 'attachments/assistance/' . $filename;
            } else {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('attachments/assistance');
                
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }
                
                $file->move($destinationPath, $filename);
                $attachmentPath = 'attachments/assistance/' . $filename;
            }
        }
        
        $supportRequest = SupportRequest::create([
            'student_id' => Auth::id(),
            'request_type' => $request->request_type,
            'description' => $request->description,
            'attachment_path' => $attachmentPath,
            'attachment_original_name' => $attachmentOriginalName,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        
        try {
            Mail::send('emails.assistance_notification', [
                'request' => $supportRequest,
                'student' => $student
            ], function($message) {
                $message->to('admin@tcc.edu.ph')
                        ->subject('New Assistance Request - TCC Clearance System');
            });
        } catch (\Exception $e) {
            Log::error('Assistance email error: ' . $e->getMessage());
        }
        
        return redirect()->route('student.assistance')
            ->with('success', 'Your assistance request has been submitted! Reference #: ' . $supportRequest->id);
    }
    
    // ✅ GET SINGLE REQUEST FOR EDITING
    public function getRequest($id)
    {
        try {
            $request = SupportRequest::where('id', $id)
                ->where('student_id', Auth::id())
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'request' => [
                    'id' => $request->id,
                    'request_type' => $request->request_type,
                    'description' => $request->description,
                    'attachment_path' => $request->attachment_path,
                    'attachment_original_name' => $request->attachment_original_name,
                    'status' => $request->status,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        }
    }
    
    // ✅ UPDATE REQUEST
    public function update(Request $request, $id)
    {
        try {
            $supportRequest = SupportRequest::where('id', $id)
                ->where('student_id', Auth::id())
                ->firstOrFail();

            // Check if request is still pending
            if ($supportRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot edit a request that is already ' . $supportRequest->status
                ], 400);
            }

            $request->validate([
                'request_type' => 'required|string',
                'description' => 'required|string|min:10',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:5120'
            ]);

            $attachmentPath = $supportRequest->attachment_path;
            $attachmentOriginalName = $supportRequest->attachment_original_name;
            
            // Check if attachment should be removed
            if ($request->has('remove_attachment') && $request->remove_attachment == '1') {
                if ($attachmentPath && file_exists(public_path($attachmentPath))) {
                    unlink(public_path($attachmentPath));
                }
                $attachmentPath = null;
                $attachmentOriginalName = null;
            }
            
            // Check if new attachment is uploaded
            if ($request->hasFile('attachment')) {
                // Delete old attachment if exists
                if ($attachmentPath && file_exists(public_path($attachmentPath))) {
                    unlink(public_path($attachmentPath));
                }
                
                $file = $request->file('attachment');
                $attachmentOriginalName = $file->getClientOriginalName();
                
                $isImage = in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif']);
                
                if ($isImage) {
                    $filename = time() . '_' . uniqid() . '.jpg';
                    $destinationPath = public_path('attachments/assistance');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    
                    $this->compressAndSaveImage($file, $destinationPath . '/' . $filename);
                    $attachmentPath = 'attachments/assistance/' . $filename;
                } else {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $destinationPath = public_path('attachments/assistance');
                    
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    
                    $file->move($destinationPath, $filename);
                    $attachmentPath = 'attachments/assistance/' . $filename;
                }
            }

            $supportRequest->update([
                'request_type' => $request->request_type,
                'description' => $request->description,
                'attachment_path' => $attachmentPath,
                'attachment_original_name' => $attachmentOriginalName,
            ]);

            // Refresh the model to get updated data
            $supportRequest->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully!',
                'request' => $supportRequest
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error updating assistance request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating request: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ DELETE REQUEST
    public function destroy($id)
    {
        try {
            $supportRequest = SupportRequest::where('id', $id)
                ->where('student_id', Auth::id())
                ->firstOrFail();

            // Check if request is still pending
            if ($supportRequest->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete a request that is already ' . $supportRequest->status
                ], 400);
            }

            // Delete attachment if exists
            if ($supportRequest->attachment_path && file_exists(public_path($supportRequest->attachment_path))) {
                unlink(public_path($supportRequest->attachment_path));
            }

            $supportRequest->delete();

            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully!'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Request not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting assistance request: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting request'
            ], 500);
        }
    }
    
    /**
     * Compress and save image to reduce file size
     */
    private function compressAndSaveImage($file, $path)
    {
        $image = null;
        $sourcePath = $file->getPathname();
        
        $mime = $file->getMimeType();
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                $file->move(dirname($path), basename($path));
                return;
        }
        
        if (!$image) {
            $file->move(dirname($path), basename($path));
            return;
        }
        
        $width = imagesx($image);
        $height = imagesy($image);
        
        $maxWidth = 1200;
        $maxHeight = 1200;
        
        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = (int)($width * $ratio);
            $newHeight = (int)($height * $ratio);
            
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            
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
        
        if ($mime == 'image/png') {
            imagepng($image, $path, 8);
        } elseif ($mime == 'image/gif') {
            imagegif($image, $path);
        } else {
            imagejpeg($image, $path, 85);
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