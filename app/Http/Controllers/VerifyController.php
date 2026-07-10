<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ClearanceRequest;
use Illuminate\Http\Request;

class VerifyController extends Controller
{
    public function show($token)
    {
        $student = User::where('clearance_token', $token)
            ->where('role', 'student')
            ->first();

        if (!$student) {
            return view('verify', [
                'valid'   => false,
                'student' => null,
                'requests'=> collect(),
            ]);
        }

        $clearanceRequests = ClearanceRequest::where('student_id', $student->id)
            ->with('department')
            ->where('status', 'approved')
            ->orderBy('processed_at', 'asc')
            ->get();

        return view('verify', [
            'valid'    => true,
            'student'  => $student,
            'requests' => $clearanceRequests,
        ]);
    }
}