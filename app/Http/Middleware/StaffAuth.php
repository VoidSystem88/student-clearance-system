<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('staff.login');
        }
        
        // Check if user has staff role
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'admin', 'super_admin'])) {
            return redirect()->route('staff.login')->with('error', 'Unauthorized access');
        }
        
        return $next($request);
    }
}