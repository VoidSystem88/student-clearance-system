<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;

class StaffAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if already authenticated
        if (Auth::check()) {
            return $next($request);
        }
        
        // Check if staff session exists
        if (session('staff_logged_in') && session('staff_email')) {
            // Find or create the user
            $user = User::where('email', session('staff_email'))->first();
            
            if (!$user) {
                // Create user from department data
                $staff = Department::find(session('staff_department_id'));
                if ($staff) {
                    $user = User::firstOrCreate(
                        ['email' => $staff->staff_email],
                        [
                            'name' => $staff->name ?? 'Staff',
                            'password' => $staff->staff_password,
                            'role' => 'staff',
                            'is_active' => true,
                        ]
                    );
                }
            }
            
            if ($user) {
                Auth::login($user);
                $request->session()->regenerate();
                return $next($request);
            }
        }
        
        // Not authenticated - redirect to login
        return redirect('/')->with('error', 'Please login first.');
    }
}