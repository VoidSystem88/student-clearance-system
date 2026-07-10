<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $logs = ActivityLog::orderBy('created_at', 'desc')
            ->when(request('user'), function($q) {
                $q->where('user_email', 'like', '%' . request('user') . '%');
            })
            ->when(request('action'), function($q) {
                $q->where('action', request('action'));
            })
            ->when(request('module'), function($q) {
                $q->where('module', request('module'));
            })
            ->when(request('date'), function($q) {
                $q->whereDate('created_at', request('date'));
            })
            ->paginate(50);
        
        return view('admin.activity-logs', compact('logs'));
    }
}