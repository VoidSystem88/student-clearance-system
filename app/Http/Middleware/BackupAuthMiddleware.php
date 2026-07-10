<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BackupAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('backup_authenticated')) {
            return redirect()->route('admin.backup.password.form');
        }
        
        return $next($request);
    }
}