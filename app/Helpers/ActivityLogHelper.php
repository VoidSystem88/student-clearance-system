<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogHelper
{
    public static function log($action, $module, $description, $oldData = null, $newData = null)
    {
        $user = auth()->user();
        
        ActivityLog::create([
            'user_id' => $user?->id,
            'user_email' => $user?->email ?? 'system',
            'user_role' => $user?->role ?? 'system',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}