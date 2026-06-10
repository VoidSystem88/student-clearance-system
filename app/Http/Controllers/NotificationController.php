<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('admin.notifications', compact('notifications'));
    }
    
    // ============ ADD THIS METHOD ============
    public function getAjaxNotifications()
    {
        $notifications = Auth::user()->notifications()->limit(10)->get();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
    // =========================================
    
    public function getUnreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }
    
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
        
        return response()->json(['success' => true]);
    }
    
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        // For AJAX request
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        
        // For form submission
        return redirect()->back()->with('success', 'All notifications marked as read');
    }
}