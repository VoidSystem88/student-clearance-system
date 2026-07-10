<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function uploadPhoto(Request $request)
    {
        // Set JSON response header
        header('Content-Type: application/json');
        
        try {
            // Check authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $user = Auth::user();
            
            // Check if file exists
            if (!$request->hasFile('photo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 400);
            }
            
            $file = $request->file('photo');
            
            // Validate file type
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only JPG and PNG files are allowed'
                ], 400);
            }
            
            // Validate file size (5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File too large. Maximum 5MB allowed'
                ], 400);
            }
            
            // Read file content
            $imageData = file_get_contents($file->getRealPath());
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read file'
                ], 500);
            }
            
            // Save to database
            $user->profile_photo = $imageData;
            $user->save();
            
            // Return base64 for display
            $base64Image = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($imageData);
            
            return response()->json([
                'success' => true,
                'photo_url' => $base64Image,
                'message' => 'Profile picture saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Profile upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function saveGmailPhoto(Request $request)
    {
        $request->validate([
            'photo_url' => 'required|url'
        ]);
        
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        
        try {
            $imageData = file_get_contents($request->photo_url);
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image'
                ], 400);
            }
            
            $user->profile_photo = $imageData;
            $user->save();
            
            $base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);
            
            return response()->json([
                'success' => true,
                'photo_url' => $base64Image,
                'message' => 'Gmail photo saved successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}