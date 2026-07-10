protected $routeMiddleware = [
    // ... existing middleware
    'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
    'staff.auth' => \App\Http\Middleware\StaffAuthMiddleware::class,  // ← ADD THIS LINE
];