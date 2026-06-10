<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Allow Admin, Super Admin, and Support to always access
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin', 'support'])) {
            return $next($request);
        }
        
        // Check if maintenance mode is active (Full Shutdown)
        if (Cache::get('maintenance_mode') && !$this->isExceptedRoute($request)) {
            return response()->view('maintenance', [
                'message' => Cache::get('maintenance_message', 'System is under maintenance. Please check back later.'),
                'end_time' => Cache::get('maintenance_end_time'),
                'mode' => 'full'
            ], 503);
        }
        
        // Check read-only mode (Soft Shutdown)
        if (Cache::get('read_only_mode') && $this->isWriteOperation($request)) {
            // Allow admin/super_admin/support to do write operations even in read-only mode
            if (auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin', 'support'])) {
                return $next($request);
            }
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => 'System is in read-only mode for maintenance',
                    'message' => Cache::get('read_only_message', 'System is under maintenance. New submissions are temporarily disabled.')
                ], 503);
            }
            
            return back()->with('error', '⚠️ System is in read-only mode. You cannot submit data at this time.');
        }
        
        return $next($request);
    }
    
    private function isExceptedRoute($request)
    {
        $exceptRoutes = [
            'maintenance.status',
            'support.maintenance.password',
            'support.maintenance',
            'support.maintenance.soft',
            'support.maintenance.full',
            'support.maintenance.disable',
            'support.maintenance.logout',
            'login',
            'logout',
            'clear-cache',
            'visitor-tracking-data',
            'home',
            'login.post',
        ];
        return $request->routeIs(...$exceptRoutes);
    }
    
    private function isWriteOperation($request)
    {
        $writeMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
        return in_array($request->method(), $writeMethods);
    }
}