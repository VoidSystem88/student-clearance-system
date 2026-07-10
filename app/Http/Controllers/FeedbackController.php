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

        // Email notification code...
        
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

    public function update(Request $request, $id)
    {
        $feedback = Feedback::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found or unauthorized.'
            ], 404);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'category' => 'required|string',
            'message' => 'required|string|min:10',
        ]);

        $feedback->update([
            'rating' => $request->rating,
            'category' => $request->category,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback updated successfully!',
            'feedback' => $feedback
        ]);
    }

    public function destroy($id)
    {
        $feedback = Feedback::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found or unauthorized.'
            ], 404);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully!'
        ]);
    }
}