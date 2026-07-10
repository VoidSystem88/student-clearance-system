<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'admin.auth' => \App\Http\Middleware\AdminAuth::class,
                'staff.auth' => \App\Http\Middleware\StaffAuth::class,  // ✅ ADD THIS
                'maintenance.auth' => \App\Http\Middleware\MaintenancePassword::class,
                'check.maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
                'backup.auth' => \App\Http\Middleware\BackupAuthMiddleware::class,
            ]);
        })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();