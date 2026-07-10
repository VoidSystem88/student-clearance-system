<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'announcements' => $announcements->map(function($ann) {
                return [
                    'id' => $ann->id,
                    'title' => $ann->title,
                    'content' => $ann->content,
                    'type' => $ann->type,
                    'created_at' => $ann->created_at,
                ];
            })
        ]);
    }
}