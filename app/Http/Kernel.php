protected $routeMiddleware = [
    // ... existing middleware
    'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
];