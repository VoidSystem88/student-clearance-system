<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    // Display announcements for students/staff (API)
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
        
        return response()->json($announcements);
    }
    
    // Admin: Show all announcements with pagination
    public function adminIndex()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements', compact('announcements'));
    }
    
    // Admin: Show edit form
// Sa AnnouncementController.php, palitan ang edit method:
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));  // Note: may folder na "announcements"
    }
    
    // Admin: Store announcement
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'created_by' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => true,
        ]);
        
        return redirect()->route('admin.announcements')->with('success', 'Announcement created successfully');
    }
    
    // Admin: Update announcement
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:info,warning,success,danger',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'sometimes|boolean',
        ]);
        
        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        
        // Update is_active if present in request
        if ($request->has('is_active')) {
            $announcement->update(['is_active' => $request->is_active]);
        }
        
        return redirect()->route('admin.announcements')->with('success', 'Announcement updated successfully');
    }
    
    // Admin: Delete announcement
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();
        
        return redirect()->route('admin.announcements')->with('success', 'Announcement deleted successfully');
    }
    
    // Admin: Toggle active status
    public function toggleActive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
        
        $status = $announcement->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.announcements')->with('success', "Announcement {$status}");
    }
}