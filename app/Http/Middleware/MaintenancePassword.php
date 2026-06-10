<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MaintenancePassword
{
    public function handle(Request $request, Closure $next)
    {
        // Skip if already authenticated for maintenance
        if ($request->session()->get('maintenance_authenticated')) {
            return $next($request);
        }
        
        // Check if password is provided
        if ($request->has('mpass')) {
            $password = $request->input('mpass');
            $validPassword = env('MAINTENANCE_ACCESS_PASSWORD', 'admin123');
            
            if ($password === $validPassword) {
                $request->session()->put('maintenance_authenticated', true, 60); // 60 minutes
                return redirect()->route('support.maintenance');
            } else {
                return redirect()->route('support.maintenance.password')->with('error', 'Incorrect password!');
            }
        }
        
        // Redirect to password form
        return redirect()->route('support.maintenance.password');
    }
}