<?php

namespace App\Http\Controllers;

use App\Models\VerifiedExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function reports()
    {
        $user = Auth::user();
        $department = $user->department;
        
        if (!$department) {
            return redirect()->back()->with('error', 'Department not found.');
        }
        
        $exports = VerifiedExport::where('department_id', $department->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('staff.reports', compact('exports'));
    }
    
    public function downloadExport($id)
    {
        $export = VerifiedExport::findOrFail($id);
        $user = Auth::user();
        
        if ($export->department_id != $user->department_id) {
            abort(403, 'Unauthorized');
        }
        
        return response()->streamDownload(function() use ($export) {
            echo $export->csv_data;
        }, $export->filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $export->filename . '"'
        ]);
    }
}