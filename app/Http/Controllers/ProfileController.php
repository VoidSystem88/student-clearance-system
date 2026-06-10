<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
   public function uploadPhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
    ]);

    $user = Auth::user();
    
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'User not authenticated'
        ], 401);
    }
    
    try {
        $file = $request->file('photo');
        $imageData = file_get_contents($file->getRealPath());
        
        // I-OPTION: I-save as base64 imbes na BLOB (mas safe sa JSON)
        $base64Image = 'data:' . $file->getMimeType() . ';base64,' . base64_encode($imageData);
        
        // I-save ang base64 string sa database (kung gusto mong i-change ang column type)
        // O kaya i-save pa rin bilang BLOB pero i-base64 muna bago i-return
        $user->profile_photo = $imageData; // BLOB parin
        $user->save();
        
        return response()->json([
            'success' => true,
            'photo_url' => $base64Image,
            'message' => 'Profile picture saved successfully!'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Profile photo upload error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
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
            // Download image from Google
            $imageData = file_get_contents($request->photo_url);
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image from Gmail'
                ], 400);
            }
            
            // Save BLOB to database
            $user->profile_photo = $imageData;
            $user->save();
            
            // Generate base64 for display
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