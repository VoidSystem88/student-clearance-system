<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        return view('student.feedback', compact('feedbacks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'category' => 'required|string',
            'message' => 'required|string|min:10',
        ]);

        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'category' => $request->category,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        // ============ SEND EMAIL NOTIFICATION TO ADMINS ============
        $student = Auth::user();
        $admins = User::where('role', 'admin')->get();
        
        $categoryLabels = [
            'bug' => '🐛 Bug Report',
            'feature' => '💡 Feature Request',
            'improvement' => '📈 Improvement Suggestion',
            'experience' => '😊 User Experience',
            'other' => '📝 Other'
        ];
        
        $categoryDisplay = $categoryLabels[$feedback->category] ?? ucfirst($feedback->category);
        
        foreach ($admins as $admin) {
            try {
                Mail::send([], [], function($message) use ($admin, $student, $feedback, $categoryDisplay) {
                    $message->to($admin->email)
                            ->subject('📝 New Feedback Submitted - Void Clearance System')
                            ->html("
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset='UTF-8'>
                                <title>New Feedback</title>
                                <style>
                                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
                                    .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                                    .header { background: linear-gradient(135deg, #8b5cf6, #7c3aed); padding: 20px; text-align: center; }
                                    .header h1 { color: white; margin: 0; font-size: 24px; }
                                    .content { padding: 25px; }
                                    .feedback-box { background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #8b5cf6; }
                                    .stars { color: #fbbf24; font-size: 18px; }
                                    .button { display: inline-block; background: #8b5cf6; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
                                    .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>📝 New Feedback Received</h1>
                                    </div>
                                    <div class='content'>
                                        <p>Dear <strong>{$admin->name}</strong>,</p>
                                        <p>A new feedback has been submitted by <strong>{$student->first_name} {$student->last_name}</strong>.</p>
                                        
                                        <div class='feedback-box'>
                                            <p><strong>📋 Student Details:</strong><br>
                                            Student ID: {$student->student_id}<br>
                                            Email: {$student->email}<br>
                                            Course: {$student->course} - {$student->year_level}</p>
                                            
                                            <p><strong>⭐ Rating:</strong> 
                                            <span class='stars'>" . str_repeat('★', $feedback->rating) . str_repeat('☆', 5 - $feedback->rating) . "</span>
                                            </p>
                                            
                                            <p><strong>📂 Category:</strong> {$categoryDisplay}</p>
                                            
                                            <p><strong>💬 Message:</strong><br>
                                            " . nl2br(htmlspecialchars($feedback->message)) . "</p>
                                        </div>
                                        
                                        <p><strong>Status:</strong> <span style='color: #f59e0b;'>⏳ Pending Review</span></p>
                                        
                                        <div style='text-align: center;'>
                                            <a href='" . url('/admin/feedbacks') . "' class='button'>View & Respond</a>
                                        </div>
                                        
                                        <p style='margin-top: 20px; font-size: 12px; color: #6b7280;'>Login to the admin panel to respond to this feedback.</p>
                                    </div>
                                    <div class='footer'>
                                        <p>Void Clearance System - Admin Notification</p>
                                        <p>© " . date('Y') . " All rights reserved.</p>
                                    </div>
                                </div>
                            </body>
                            </html>
                            ");
                });
                Log::info('New feedback notification sent to admin: ' . $admin->email);
            } catch (\Exception $e) {
                Log::error('Failed to send new feedback notification to admin: ' . $e->getMessage());
            }
        }
        // =========================================================

        // Return JSON response for AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback!',
                'feedback' => [
                    'id' => $feedback->id,
                    'rating' => $feedback->rating,
                    'category' => $feedback->category,
                    'message' => $feedback->message,
                    'created_at' => $feedback->created_at,
                ]
            ]);
        }

        return redirect()->route('student.feedback')->with('success', 'Thank you for your feedback!');
    }
}