<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        Feedback::create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'category' => $request->category,
            'message' => $request->message,
            'status' => 'pending',
        ]);

        return redirect()->route('student.feedback')->with('success', 'Thank you for your feedback!');
    }
}